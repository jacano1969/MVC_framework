<?php

namespace mvc\views;

use core\Object;
use mvc\Model;
use util\Hashtable;

/**
 * This class represents a ViewRenderer.
 * It must be extended to support specific view types (such as HTMLViewRenderer or XMLViewRenderer)
 */
abstract class ViewRenderer extends Object {

	/**
	 * Flag to force compilation of views each time
	 */
	protected $forceCompile = false;

	/**
	 * Override with true to require loaders
	 */
	protected $requireLoaders = false;

	/**
	 * Override with true to support compilers.
	 */
	protected $supportCompilers = false;

	/**
	 * Array of ViewLoader definitions for this View class
	 */
	protected $loaders = array();

	/**
	 * Array of ViewCompiler definitions for this View class
	 */
	protected $compilers = array();

	/**
	 * Array of View properties
	 */
	protected $properties = array();

	/**
	 * Gets the view data and optional data uri as a this renderer specific data format.
	 *
	 * @param string $data
	 * @param string $uri=null
	 */
	abstract public function getViewData( $data, $uri=null );

	/**
	 * Renders the provided $model Model with the supplied View $view
	 *
	 * @param Model $model
	 * @param View $view
	 */
	abstract public function render( Model $model, View $view );

	/**
	 * Renders the supplied exception and returns the result
	 *
	 * @param \Exception $exception
	 * @return str
	 */
	abstract public function Exception( \Exception $exception );

	/**
	 * Renders the array of supplied exceptions
	 *
	 * @param array $exceptions
	 */
	public function Exceptions( array $exceptions ) {
		$str = '';
		foreach( $exceptions as $e ) {
			$str.= $this->Exception( $e );
		}
		print( $str );
	}

	/**
	 * When true, forces compilation of views every time
	 *
	 * @param boolean $force
	 */
	public function setForceCompile( $force ) {
		$this->forceCompile = $force;
	}

	/**
	 * Gets the force compile property
	 *
	 * @return boolean
	 */
	public function getForceCompile() {
		return $this->forceCompile;
	}

	/**
	 * Returns whether this view requires Loaders.
	 *
	 * @return boolean
	 */
	public function requireLoaders() {
		return $this->requireLoaders;
	}

	/**
	 * Adds a ViewLoader to this View
	 *
	 * @param ViewLoader $loader ViewLoader
	 */
	public function addLoader( ViewLoader $loader ) {
		if ( !$this->requireLoaders() ) {
			throw new ViewException( sprintf( 'Cant add Loader "%s" to View "%s". This view does not support/require loaders', $loader, get_class() ) );
		}
		$this->loaders[] = $loader;
	}

	/**
	 * Gets the ViewLoaders for this View
	 *
	 * @return array(ViewLoader)
	 */
	public function getLoaders() {
		return $this->loaders;
	}

	/**
	 * Returns whether this View has any registered loaders
	 *
	 * @return boolean
	 */
	public function hasLoaders() {
		return sizeof( $this->loaders ) > 0;
	}

	/**
	 * Clears loaders for this view
	 */
	public function clearLoaders() {
		$this->loaders = array();
	}

	/**
	 * Returns whether this view support Compilers.
	 *
	 * @return boolean
	 */
	public function supportCompilers() {
		return $this->supportCompilers;
	}

	/**
	 * Returns whether this view has any registered compilers
	 *
	 * @return boolean
	 */
	public function hasCompilers() {
		return sizeof( $this->compilers ) > 0;
	}

	/**
	 * Clears compilers for this view
	 */
	public function clearCompilers() {
		$this->compilers = array();
	}

	/**
	 * Adds a View Compiler to this View
	 *
	 * @param ViewCompiler $compiler ViewCompiler
	 */
	public function addCompiler( ViewCompiler $compiler ) {
		if ( !$this->supportCompilers() ) {
			throw new ViewException( sprintf( 'Cant add Compiler "%s" to View "%s". This view does not support/require compilers', $compiler, get_called_class() ) );
		}
		$this->compilers[] = $compiler;
	}

	/**
	 * Gets the ViewCompilers for this View
	 *
	 * @return array(ViewCompiler)
	 */
	public function getCompilers() {
		return $this->compilers;
	}

	/**
	 * Sets a property for this ViewRenderer
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setProperty( $name, $value ) {
		$this->properties[$name] = $value;
	}

	/**
	 * Gets a property for this ViewRenderer
	 *
	 * @param string $name
	 * @return string value
	 */
	public function getProperty( $name ) {
		if ( !isset( $this->properties[$name] ) ) {
			return $this->properties[$name];
		} else {
			return null;
		}
	}

	/**
	 * Gets all set properties for this ViewRenderer
	 *
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * Compiles the supplied view, according to the registered Compilers.
	 *
	 * @param ViewLoader $loader
	 * @param View $view
	 */
	public function compile( ViewLoader $loader, View $view ) {
		foreach( $this->getCompilers() as $compiler ) {
			$compiler->compile( $this, $loader, $view );
		}
	}

	/**
	 * Loads the view for the supplied module and name, returning a new View object.
	 *
	 * @param string $module
	 * @param string $name
	 */
	public function load( $module, $name ) {
		$view = new View( $module, $name );
		if ( $this->hasLoaders() ) {
			$le = array();
			$ok = false;
			foreach( $this->getLoaders() as $loader ) {
				try {
					$ok = $loader->load( $this, $view );
					if ( $ok ) break;
				} catch ( ViewLoaderException $e ) {
					$le[] = $e->getMessage();
				}
			}
			if ( !$ok ) throw new ViewException( sprintf( 'Error loading view: %s', join( ', ', $le ) ) );
		}
		return $view;
	}

	protected function setHeadersFromModel( Model $model ) {
		$headers = array(
			"expires" => "Expires"
			, "lastModified" => "Last-Modified"
			, "cacheControl" => "Cache-Control"
			, "pragma" =>	"Pragma"
			, "etag" =>	"ETag"
		);
		foreach($headers as $key => $name) {
			if ($model->data->$key) {
				header( $name . ": " . $model->data->$key, true);
			}
		}
	}
	
}
