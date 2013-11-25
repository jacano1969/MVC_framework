<?php

namespace mvc\views\loaders;

use core\Object;
use io\File;
use util\Date;
use mvc\views\View;
use mvc\views\ViewLoader;
use mvc\views\ViewRenderer;
use mvc\views\ViewLoaderException;
use mvc\AppException;

/**
 * Implements a ViewLoader for loading file views from the file system
 */
class FileViewLoader extends Object implements ViewLoader {

	/**
	 * Filesystem paths for lookup
	 */
	protected $paths = array();

	/**
	 * Filesystem path for compiled views
	 */
	protected $compilePath = null;

	/**
	 * Extension for this View Loader
	 */
	protected $extension = null;

	/**
	 * Gets base file
	 *
	 * @parma View $view
	 */
	public function getBaseFile( View $view ) {
		$file = $this->lookup( $view );
		if ( !$file ) {
			throw new ViewLoaderException( $this, sprintf( 'View not found: "%s". Paths: %s', $view->getModule() ? $view->getModule() . $view->getName() : $view->getName(), join( ', ', $this->paths ) ) );
		}
		return $file;
	}

	/**
	 * Loads the base view (file)
	 * If the file parameter is not provided, then it's resolved from the $view
	 *
	 * @param ViewRenderer $renderer
	 * @param View $view
	 * @param File $file
	 */
	public function loadViewFromFile( ViewRenderer $renderer, View $view, File $file=null ) {
		if ( $file === null ) $file = $this->getBaseFile( $view );
		$data = $file->read();
		$view->setMTime( new Date( $file->getMTime() ) );
		$view->setRawData( $data );
		$view->setData( $renderer->getViewData( $data, $file->getName() ) );
	}

	/**
	 * Loads the supplied view with data from the file system, checking all registered paths.
	 * If a compile path is set, it tries to compile it using the supplied ViewRenderer.
	 *
	 * @param ViewRenderer $renderer
	 * @param View $view
	 */
	public function load( ViewRenderer $renderer, View $view ) {
		$bFile = $this->getBaseFile( $view );
		if ( $renderer->hasCompilers() ) {
			// Check compile data needed
			if ( !$this->compilePath ) {
				throw new ViewLoaderException( $this, sprintf( 'Cant compile view. No compile path registered.' ) );
			}
			$cpath = $this->compilePath;
			if ( strstr( $cpath, '{locale}' ) ) $cpath = str_replace( '{locale}', Locale::getDefault(), $cpath );
			$cFile = new File( sprintf( '%s/%s%s.%s', $cpath, $view->getModule() ? $view->getModule() . '/' : '', $view->getName(), $this->extension ) );
			if ( !$cFile->exists() || $cFile->isOlderThan( $bFile ) || $renderer->getForceCompile() ) {
				// Load base view, compile & save
				$this->loadViewFromFile( $renderer, $view, $bFile );
				$renderer->compile( $this, $view );
				$this->save( $view, array( $cFile ) );
			} else {
				$this->loadViewFromFile( $renderer, $view, $cFile );
			}

		} else {
			// No compilers registered, simply set view data
			$this->loadViewFromFile( $renderer, $view, $bFile );
		}
		return true;
	}

	/**
	 * Saves the compiled view into the given file
	 *
	 * @param File $file
	 */
	public function save( View $view, array $params=array() ) {
		$file = $params[0];
		$dir = dirname( $file );
		if ( substr( $dir, -2 ) != '..' && !is_dir( $dir ) ) {
			if ( @mkdir( $dir, 0777 ) === false ) {
				throw new AppException( sprintf( 'Error saving view file "%s". Directory "%s" could not be created!', $file, $dir ) );
			}
		}
		$view->getData()->documentURI = $file->getName();
		$view->getData()->save( $file->getName() );
		@chmod( $file, 0777 );
	}

	/**
	 * Searches the defined view paths for the supplied module/view
	 *
	 * @param View $view
	 * @return string The full view path
	 */
	protected function lookup( View $view ) {
		$module = $view->getModule();
		foreach( $this->paths as $path ) {
			if ( !$module ) $module = '';
			elseif ( !substr( $module, -1 ) != '/' ) $module.= '/';
			$file = sprintf( '%s/%s%s.%s', $path, $module, $view->getName(), $this->extension );
			if ( file_exists( $file ) ) return new File( $file );
		}
		return null;
	}

	/** 
	 * Sets a FileViewLoader property. A FileViewLoader supports the following properties:
	 *
	 * - path: Added to paths array. In order for lookup
	 * - compile-path: Path to compile views
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
			case 'compile-path':
				$this->setCompilePath( $value );
				break;
			case 'extension':
				$this->setExtension( $value );
				break;
			default:
				throw new ViewException( sprintf( 'Unsupported property for FileViewLoader: "%s"', $name ) );
		}
	}

	/**
	 * Clears all registered paths and the compile path for FileViewLoader
	 */
	public function clearProperties() {
		$this->paths = array();
		$this->compilePath = null;
	}

	/**
	 * Checks that at least one path is defined for this FileViewLoader
	 *
	 * @throws ViewException
	 */
	public function checkProperties() {
		if ( sizeof( $this->paths ) == 0 || $this->extension === null ) {
			throw new ViewLoaderException( $this, sprintf( '%s Configuration error: No view paths and/or extension defined', get_called_class() ) );
		}
	}

	/**
	 * Adds a filesystem path for lookup
	 *
	 * @param string $path
	 */
	public function addPath( $path ) {
		if ( !in_array( $path, $this->paths ) ) {
			if ( !is_dir( $path ) ) {
				throw new ViewLoaderException( $this, sprintf( 'Invalid path "%s" for FileViewLoader. Directory not found', $path ) );
			}
			$this->paths[] = $path;
		}
	}

	/**
	 * Returns the lookup paths of FileViewLoader
	 *
	 * @return array
	 */
	public function getPaths() {
		return $this->paths;
	}

	/**
	 * Sets the compile path.
	 *
	 * @param string $path
	 */
	public function setCompilePath( $path ) {
		if ( !is_dir( $path ) ) {
			throw new ViewLoaderException( $this, sprintf( 'Invalid compile path "%s" for FileViewLoader. Directory not found', $path ) );
		}
		$this->compilePath = $path;
	}

	/**
	 * Gets the compile path.
	 *
	 * @return string
	 */
	public function getCompilePath() {
		return $this->compilePath;
	}

	/**
	 * Sets the view extension for this loader.
	 * 
	 * @param string $ext
	 */
	public function setExtension( $ext ) {
		$this->extension = $ext;
	}

	/**
	 * Gets the view extension for this loader.
	 *
	 * @return string
	 */
	public function getExtension() {
		return $this->extension;
	}

}

