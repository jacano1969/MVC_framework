<?php

namespace mvc\views\loaders;

use io\File;
use util\Date;
use i18n\Locale;
use mvc\views\View;
use mvc\views\ViewRenderer;
use mvc\views\ViewException;
use mvc\views\ViewLoaderException;
use mvc\AppException;
use sql\ConnectionFactory;
use sql\SQLException;

/**
 * Implements a ViewLoader for loading file views from a database.
 * This Loader extends the FileViewLoader to load base view data from the filesystem, but save it on a database
 */
class DBViewLoader extends FileViewLoader {

	/**
	 * Connection
	 */
	protected $conn = null;

	/**
	 * Table where views are stored
	 *
	 * Default: views
	 */
	protected $table = 'views';

	/**
	 * View Module column.
	 *
	 * Default: module
	 */
	protected $colModule = 'module';

	/**
	 * View Name column.
	 *
	 * Default: name
	 */
	protected $colName = 'name';

	/**
	 * View Locale column.
	 *
	 * Default: locale
	 */
	protected $colLocale = 'locale';

	/**
	 * View Data column.
	 *
	 * Default: data
	 */
	protected $colData = 'data';

	/**
	 * View last compilation time column.
	 *
	 * Default: time
	 */
	protected $colTime = 'time';

	/**
	 * Loads the supplied view with data from the file system, checking all registered paths.
	 *
	 * @param View $view
	 */
	public function load( ViewRenderer $renderer, View $view ) {
		if ( $this->conn !== null ) {
			if ( !$this->table || !$this->colLocale || !$this->colModule || !$this->colName || !$this->colData || !$this->colTime ) {
				throw new ViewLoaderException( $this, sprintf( 'Cant load view data from database. Table and/or Columns (locale, module, name, data, time) definitions missing' ) );
			}
			// Fetch data from db
			$data = $this->lookupDB( $view );
			if ( $renderer->hasCompilers() ) {
				// Check whether we need to recompile
				$file = $this->getBaseFile( $view );
				$fTime = new Date( $file->getMTime() );
				if ( !$data || $fTime->greaterThan( Date::parse( $data->time ) ) || $renderer->getForceCompile() ) {
					// Compile and Save view
					$this->loadViewFromFile( $renderer, $view, $file );
					$renderer->compile( $this, $view );
					$this->save( $view );

				} else {
					// Set data from db and done.
					$view->setRawData( $data->data );
					$view->setData( $renderer->getViewData( $data->data ) );
				}

			} elseif ( $data ) {
				// Set data from db and done.
				$view->setRawData( $data->data );
				$view->setData( $renderer->getViewData( $data->data ) );
			} else {
				// No compilers, and no data, load from filesystem
				$this->loadViewFromFile( $renderer, $view );
			}

		} else {

			// Load data from filesystem
			$this->loadViewFromFile( $renderer, $view );
			if ( $renderer->hasCompilers() ) {
				// Compile on the fly
				$renderer->compile( $this, $view );
			}
		}
		return true;
	}

	/**
	 * Saves the compiled view into the given file
	 *
	 * @param File $file
	 */
	public function save( View $view, array $params=array() ) {
		try {
			$stmt = $this->conn->prepare( sprintf( 'select * from %s where %s=:module and %s=:name and %s=:locale', $this->table, $this->colModule, $this->colName, $this->colLocale ) );
			$stmt->bind( ':module', $view->getModule() );
			$stmt->bind( ':name', $view->getName() );
			$stmt->bind( ':locale', Locale::getDefault()->getLocale() );
			$rs = $stmt->execute();
			if ( $rs->next() ) {
				$stmt = $this->conn->prepare( sprintf( 'update %s set %s=:data, %s=now() where %s=:module and %s=:name and %s=:locale', $this->table, $this->colData, $this->colTime, $this->colModule, $this->colName, $this->colLocale ) );
			} else {
				$stmt = $this->conn->prepare( sprintf( 'insert into %s ( %s, %s, %s, %s, %s ) values ( :module, :name, :locale, :data, now() )', $this->table, $this->colModule, $this->colName, $this->colLocale, $this->colData, $this->colTime ) );
			}
			$stmt->bind( ':module', $view->getModule() );
			$stmt->bind( ':name', $view->getName() );
			$stmt->bind( ':locale', Locale::getDefault()->getLocale() );
			$stmt->bind( ':data', $view->getRawData() );
			$stmt->execute();
		} catch ( SQLException $e ) {
			throw new ViewLoaderException( $this, sprintf( '%s: Error saving compiled view data to database: %s', get_class(), $e->getMessage() ) );
		}
	}

	/**
	 * Tries to fetch the compiled view from database
	 *
	 * @param View $view
	 * @return string The full view path
	 */
	protected function lookupDB( View $view ) {
		try {
			$stmt = $this->conn->prepare( sprintf( 'select * from %s where %s=:module and %s=:name and %s=:locale', $this->table, $this->colModule, $this->colName, $this->colLocale ) );
			$stmt->bind( ':module', $view->getModule() );
			$stmt->bind( ':name', $view->getName() );
			$stmt->bind( ':locale', Locale::getDefault()->getLocale() );
			$rs = $stmt->execute();
			if ( $rs->next() ) {
				return $rs;
			} else {
				return null;
			}
		} catch ( SQLException $e ) {
			throw new ViewLoaderException( $this, sprintf( '%s: Error fetching view data from database: %s', get_class(), $e->getMessage() ) );
		}
	}

	/** 
	 * Sets a FileViewLoader property. A FileViewLoader supports the following properties:
	 *
	 * - connection
	 * - table
	 * - col-module
	 * - col-name
	 * - col-data
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setProperty( $name, $value ) {
		switch( $name ) {
			case 'path':
				if ( is_array( $value ) ) {
					foreach( $value as $path ) {
						$this->addPath( $path );
					}
				} else {
					$this->addPath( $value );
				}
				break;
			case 'extension':
				$this->setExtension( $value );
				break;
			case 'connection-id':
				$this->setConnection( $value );
				break;
			case 'connection-url':
				$this->setConnectionURL( $value );
				break;
			case 'table':
				$this->setTable( $value );
				break;
			case 'col-module':
				$this->setModuleColumn( $value );
				break;
			case 'col-name':
				$this->setNameColumn( $value );
				break;
			case 'col-data':
				$this->setDataColumn( $value );
				break;
			default:
				throw new ViewException( sprintf( 'Unsupported property for FileViewLoader: "%s"', $name ) );
		}
	}

	/**
	 * Clears all registered paths and connection/db properties for this DBViewLoader
	 */
	public function clearProperties() {
		$this->paths = array();
		$this->conn = null;
		$this->table = null;
		$this->colModule = null;
		$this->colName = null;
		$this->colLocale = null;
		$this->colData = null;
		$this->colTime = null;
	}

	/**
	 * Checks that at least one path is defined for this FileViewLoader
	 *
	 * @throws ViewException
	 */
	public function checkProperties() {
		if ( sizeof( $this->paths ) == 0 ) {
			throw new ViewException( sprintf( '%s Configuration error: No view paths defined', get_called_class() ) );
		}
	}

	/**
	 * Sets the connection id for view lookup, trying to get the connection object.
	 *
	 * @param string $id
	 */
	public function setConnection( $id ) {
		try {
			$this->conn = ConnectionFactory::getConnection( $id );
		} catch ( SQLException $e ) {
			throw new ViewException( sprintf( '%s: Error getting connection for view lookup: %s', get_class(), $e->getMessage() ) );
		}
	}

	/**
	 * Sets the connection url for view lookup, trying to get the connection object.
	 *
	 * @param string $url
	 */
	public function setConnectionURL( $url ) {
		try {
			$this->conn = ConnectionFactory::newConnection( 'VIEWS', $url );
		} catch ( SQLException $e ) {
			throw new ViewException( sprintf( '%s: Error opening connection for view lookup: %s', get_class(), $e->getMessage() ) );
		}
	}

	/**
	 * Sets the table for view lookup
	 *
	 * @param string $table
	 */
	public function setTable( $table ) {
		$this->table = $table;
		return $this;
	}

	/**
	 * Sets the module column
	 *
	 * @param string $module
	 */
	public function setModuleColumn( $module ) {
		$this->moduleColumn = $module;
		return $this;
	}

	/**
	 * Sets the name column
	 *
	 * @param string $name
	 */
	public function setNameColumn( $name ) {
		$this->nameColumn = $name;
		return $this;
	}

	/**
	 * Sets the data column
	 *
	 * @param string $data
	 */
	public function setDataColumn( $data ) {
		$this->dataColumn = $data;
		return $this;
	}

}
