<?php
namespace mvc;

use core\Object;
use core\Session;
use core\SessionException;
use core\session\FileSessionHandler;
use core\CoreException;
use i18n\Locale;
use sql\ConnectionFactory;
use sql\Connection;
use sql\SQLException;
use util\Date;
use util\Hashtable;
use util\LoggerFactory;
use util\Factory;
use mvc\views\ViewRenderer;
use mvc\views\ViewLoaderException;

/** 
 * Application Context Class.
 */ 
class AppContext extends Object {

	/**
	 * AppConfig instance used to load this context
	 */
	private $config = null;

	/**
	 * Application ID
	 */
	private $id = null;

	/**
	 * Application Name
	 */
	private $name = null;

	/**
	 * Default Character Encoding
	 */
	private $encoding = null;

	/**
	 * Default Timezone
	 */
	private $timezone = null;

	/**
	 * Default Locale
	 */
	private $defaultLocale = null;

	/**
	 * Array of available locales for this app
	 */
	private $locales = array();

	/**
	 * Array of registered loggers.
	 */
	private $loggers = array();

	/**
	 * Registered Factories
	 */
	private $factories = array();

	/**
	 * Registered Connections
	 */
	private $connections = array();

	/**
	 * Array of available view renderers
	 */
	private $availableViewRenderers = array();

	/**
	 * Array of defined view renderer
	 */ 
	private $viewRenderers = null;

	/**
	 * Default ViewRenderer for this context
	 */
	private $defaultViewRenderer = 'smarty';

	/**
	 * Error views
	 */
	private $errorViews = array();

	/**
	 * Array of exceptions caught when loading context.
	 */
	private $exceptions = array();

	/**
	 * Array of parameters debug when loading context.
	 */
	private $Debugs = array();

	/**
	 * Array of config smarty when loading context.
	 */
	private $SmartyConfig = array();


	/**
	 * Whether debug mode is enabled for this context
	 */
	private $debug = false;

	/**
	 * Instantiates a new Application Context with the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	public function __construct( AppConfig $config ) {
		$this->config = $config;
		$this->viewRenderers = new Hashtable();
		$this->loadApp( $config );
		$this->loadEncoding( $config );
		$this->loadTimezone( $config );
		$this->loadLoggers( $config );
		$this->loadLocales( $config );
		$this->loadFactories( $config );
		$this->loadConnections( $config );
		$this->loadSessionHandler( $config );
		$this->loadViewRenderers( $config );
		$this->loadErrorViews( $config );
		$this->LoadDebugs( $config );
		$this->LoadSmartyConfig( $config );

		if ( sizeof( $this->exceptions ) > 0 ) {
			$appException = new AppException( 'Errors loading context', $this->exceptions );
			throw $appException;
		}

		unset( $config );
	}

	/**
	 * Return the AppConfig instance used to load this context
	 *
	 * @return AppConfig
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * Encodes the provided string with this context encoding.
	 *
	 * @param string $str
	 * @return string
	 */
	public function encode( $str ) {
		$enc = $this->getEncoding();
		$str = html_entity_decode( $str );
		if ( $enc ) {
			return iconv( $enc, 'UTF8', $str );
		} else {
			return utf8_encode( $str );
		}
	}

	/**
	 * Decodes the provided string (inverse of previous method)
	 *
	 * @param string $str
	 * @return string
	 */
	public function decode( $str ) {
		$enc = $this->getEncoding();
		$str = stripslashes( $str );
		if ( $enc ) {
			return iconv( 'UTF8', $enc.'//TRANSLIT', $str );
		} else {
			return utf8_decode( $str );
		}
	}

	/**
	 * Gets the App ID
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Gets the App Name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns Default Locale
	 * 
	 * @return string
	 */
	public function getDefaultLocale() {
		return $this->defaultLocale;
	}

	/**
	 * Returns the array of available locales
	 *
	 * @return array
	 */
	public function getLocales() {
		return $this->locales;
	}

	/**
	 * Returns the Timezone for this Context
	 *
	 * @return string
	 */
	public function getTimezone() {
		return $this->timezone;
	}

	/**
	 * Returns the default character set encoding
	 *
	 * @return string
	 */
	public function getEncoding() {
		return $this->encoding;
	}

	/**
	 * Gets the defined factories
	 *
	 * @return array
	 */
	public function getFactories() {
		return $this->factories;
	}

	/**
	 * Gets the registered loggers for this context
	 *
	 * @return array
	 */
	public function getLoggers() {
		return $this->loggers;
	}

	/**
	 * Gets the registered connections for this context
	 *
	 * @return array
	 */
	public function getConnections() {
		return $this->connections;
	}

	/**
	 * Gets the Session Handler
	 *
	 * @return SessionHandler
	 */
	public function getSessionHandler() {
		return $this->handler;
	}

	/**
	 * Get debugs parameters for this context
	 *
	 * @return array Debug conf
	 */
	public function getDebugs() {
		return $this->debugs ;
	}

	/**
	 * Get Smarty configuration parameters for this context
	 *
	 * @return array Smarty conf
	 */
	public function getSmartyConfig() {
		return $this->SmartyConfig;
	}



	/**
	 * Sets debug for this context
	 *
	 * @param boolean $debug
	 */
	public function setDebug( $debug ) {
		$this->debug = $debug;
	}

	/**
	 * Gets whether debug is enabled for this context
	 * 
	 * @return boolean
	 */
	public function getDebug() {
		return $this->debug;
	}

	/**
	 * Gets a ViewRenderer by name
	 * If no name is provided, then the default view renderer is returned (the first defined)
	 *
	 * The view renderer class is instantiated when first requested
	 *
	 * @param string $name
	 * @return array 
	 */
	public function getViewRenderer( $name=null ) {
		if ( !$name ) $name = $this->defaultViewRenderer;
		if ( $this->viewRenderers->has( $name ) ) {
			return $this->viewRenderers->get( $name );
		}
		$view = $this->loadViewRenderer( $name );
		$this->viewRenderers->put( $name, $view );
		return $view;
	}

	/**
	 * Gets a module/view pair for an exception by class. If no view is defined for a specific exception, then the default MVC framework error view is used.
	 *
	 * @param Exception $e
	 * @return array( module, view )
	 */
	public function getErrorView( \Exception $e ) {
		if ( !isset( $this->errorViews[get_class($e)] ) ) {
			return array( 'errors', 'default' );
		} else {
			return $this->errorViews[get_class($e)];
		}
	}

	/**
	 * Registers a view renderer with this context
	 *
	 * @param string $name
	 * @param string $value
	 * @param array $loaders
	 * @param array $compilers
	 */
	public function addViewRenderer( $name, $class, $loaders=array(), $compilers=array() ) {
		$this->availableViewRenderers[$name] = $class;
	}

	/**
	 * Gets the array of available view renderers
	 *
	 * @return array
	 */
	public function getAvailableViewRenderers() {
		return $this->availableViewsRenderers;
	}

	/**
	 * Checks whether the supplied view is available for this context
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function isViewAvailable( $name ) {
		return array_key_exists( $name, $this->availableViewRenderers );
	}

	/**
	 * Sets the default view for this context.
	 * When no view is specified either by argument or controller/action, then this view is used.
	 *
	 * @param string $view
	 */
	public function setDefaultViewRenderer( $view ) {
		$this->defaultViewRenderer = $view;
	}

	/**
	 * Gets the default view for this context
	 *
	 * @return string
	 */
	public function getDefaultView() {
		return $this->defaultView;
	}

	/**
	 * Switches to this context, setting timezone, default locale, loggers, factories, connections and session handlers.
	 *
	 * Returns AppContext this context
	 */
	public function switchContext() {
		if ( $this->timezone ) {
			//Date::setDefaultTimezone( $this->timezone );
		}

		Locale::clearDefault();
		if ( $this->defaultLocale ) {
			Locale::setDefault( $this->defaultLocale );
		}

		LoggerFactory::clearLoggers();
		foreach( $this->loggers as $logger ) {
			if ( strtoupper( $logger->getName() ) == 'DEFAULT' ) {
				LoggerFactory::setDefault( $logger );
			} else {
				LoggerFactory::addLogger( $logger );
			}
		}
		Factory::clear();
		foreach( $this->factories as $f ) {
			Factory::register( $f );
		}

		ConnectionFactory::clearConnections();
		foreach( $this->connections as $conn ) {
			if ( $conn->getName() == 'DEFAULT' ) {
				ConnectionFactory::setDefault( $conn );
			} else {
				ConnectionFactory::addConnection( $conn );
			}
		}

		if ( $this->handler !== null ) {
			Session::setHandler( $this->handler );
		}

		$this->loadSessionValues();

		return $this;
	}

	/**
	 * Loads Base Application Info (ID and name)
	 *
	 * @param AppConfig $config
	 */
	protected function loadApp( AppConfig $config ) {
		$this->id = $config->getId();
		$this->name = $config->getName();
		if($config->getClassPathEntries())foreach ($config->getClassPathEntries() as $path) {
			add_classpath($path);
		}
	}

	/**
	 * Load the encoding from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadEncoding( AppConfig $config ) {
		try {
			$this->encoding = $config->getEncoding();
		} catch ( ConfigException $e ) {
			$this->addException( AppContextException::ERROR_ENCODING, $e->getMessage() );
		}
		if ( !$this->encoding ) {
			$this->encoding = 'ISO-8859-1';
		}
	}

	/**
	 * Load the timezone from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadTimezone( AppConfig $config ) {
		try {
			$this->timezone = $config->getTimezone();
		} catch ( ConfigException $e ) {
			$this->addException( AppContextException::ERROR_TIMEZONE, $e->getMessage() );
		}
		if ( !$this->timezone ) {
			$this->timezone = date_default_timezone_get();
		}
	}

	/**
	 * Load the locales from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadLocales( AppConfig $config ) {
		try {
			$this->defaultLocale = $config->getDefaultLocale();
			if ( $this->defaultLocale == 'accept' ) {
				$this->defaultLocale = $this->getLocaleFromHttp();
			}
			$this->locales[] = $this->defaultLocale;
			foreach( $config->getSupportedLocales() as $l ) {
				if ( !$l ) {
					$this->addException( AppContextException::ERROR_LOCALE, sprintf( 'Empty locale definition in config: %s', $config ) );
				} else {
					$this->locales[] = $l;
				}
			}
		} catch ( ConfigException $e ) {
			$this->addException( AppContextException::ERROR_LOCALE, $e->getMessage() );
		}
		if ( !$this->defaultLocale ) {
			$this->defaultLocale = $this->getLocaleFromHttp();
			$this->locales[] = $this->defaultLocale;
		}
	}

	/**
	 * Gets the locale from the HTTP_ACCEPT_LANGUAGE header
	 *
	 * @return string
	 */
	protected function getLocaleFromHttp() {
		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			list( $l, $c ) = explode( '-', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
			return sprintf( '%s_%s', $l, strtoupper( $c ) );
		} else {
			return null;
		}
	}

	/**
	 * Load the loggers from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadLoggers( AppConfig $config ) {
		foreach( $config->getLoggers() as $logger ) {
			try {
				$this->loggers[] = LoggerFactory::newLogger( $logger['name'],$logger['type'], $logger['path'], $logger['level'] );
			} catch ( ConfigException $e ) {
				$this->addException( AppContextException::ERROR_LOGGER, sprintf( '%s for config: %s', $e->getMessage(), $config ) );
			} catch ( CoreException $e ) {
				$this->addException( AppContextException::ERROR_LOGGER, $e->getMessage() );
			}
		}
	}

	/**
	 * Load the loggers from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadFactories( AppConfig $config ) {
		$ctrl = false;
		try {
			foreach( $config->getFactories() as $name => $namespaces ) {
				$factory = new Factory( $name );
				if($namespaces)foreach( $namespaces as $ns ) {
					$factory->registerNamespace( $ns );
				}
				$this->factories[] = $factory;
				if ( $name == 'controller' ) {
					$ctrl = true;
				}
			}
		} catch ( ConfigException $e ) {
			$this->addException( AppContextException::ERROR_FACTORY, sprintf( '%s for Config: %s', $e->getMessage(), $config ) );
		}
		if ( !$ctrl ) {
			if ( $this->getId() ) {
				$appns = sprintf( '%s\app', $this->getId() );
			} else {
				$appns = 'app';
			}
			// Set up default controller factory
			$factory = new Factory( 'controller' );
			$factory->registerNamespace( $appns );
			$factory->registerNamespace( 'app' );
			$this->factories[] = $factory;
		}
	}

	/**
	 * Load the connections from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadConnections( AppConfig $config ) {
		// First load up any registered drivers
		try {
			foreach( $config->getConnectionDrivers() as $name => $class ) {
				ConnectionFactory::registerDriver( $name, $class );
			}
		} catch ( ConfigException $e ) {
			$this->addException( AppContextException::ERROR_CONNECTION, sprintf( '%s for Config: %s', $e->getMessage(), $config ) );
		}
		try {
			foreach( $config->getConnections() as $name => $values ) {
				try {
					$conn = ConnectionFactory::newConnection( $name, $values['url'], $values['flags'] );
					$conn->setTimezone( $this->timezone );
					if ( $this->encoding ) {
						$conn->setEncoding( $this->encoding );
					}
					$this->connections[] = $conn;
				} catch ( SQLException $e ) {
					$this->addException( AppContextException::ERROR_CONNECTION, $e->getMessage() );
				}
			}
		} catch ( ConfigException $e ) {
			$this->addException( AppContextException::ERROR_CONNECTION, sprintf( '%s for Config: %s', $e->getMessage(), $config ) );
		}
	}

	/**
	 * Load the session handler from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadSessionHandler( AppConfig $config ) {
		try {
			$s = $config->getSessionHandler();
            $this->handler = null;
			if ( $s ) {
				if ( !isset( $s['class'] ) ) {
					$this->addException( AppContextException::ERROR_SESSIOn, sprintf( 'No class defined for Session Handler. Config: %s', $config ) );
					return;
				}
				$handler = new $s['class']();
				if ( isset( $s['props'] ) ) foreach( $s['props'] as $name => $value ) {
					if ( !$name || !is_string( $name ) || !$value ) {
						$this->addException( AppContextException::ERROR_SESSION, sprintf( 'Invalid property definition for Session Handler "%s". (Name: "%s") => (Value: "%s"). Config: %s', get_class($handler), $name, $value, $config ) );
						return;
					}
					$handler->setProperty( $name, $value );
				}
				$this->handler = $handler;
			} elseif(php_sapi_name()!='cli') {
				$this->handler = new FileSessionHandler();
			}
		} catch ( ConfigException $e ) {
			$this->addException( AppContextException::ERROR_SESSION, sprintf( '%s for Config: %s', $e->getMessage(), $config ) );
		}
	}

	/**
	 * Loads the supplied view, instantiating its class and setting up loaders and compilers are needed or set by config.
	 *
	 * If view isn't found in config, and isn't a default view, an exception is thrown.
	 *
	 * @param string $name
	 * @return ViewRenderer
	 */
	protected function loadViewRenderer( $name ) {
		if ( !isset( $this->availableViewRenderers[$name] ) ) {
			throw new AppContextException( AppContextException::ERROR_VIEW, sprintf( 'Unsupported view "%s" for context. Config: %s', $name, $this->config ) );
		}
		$class = $this->availableViewRenderers[$name];
		$view = new $class();
		if ( !$view instanceOf ViewRenderer ) {
			throw new AppContextException( AppContextException::ERROR_VIEW, sprintf( 'Invalid View Renderer "%s". Class "%s" does not extend ViewRenderer. Config: %s', $name, $class, $this->config ) );
		}

		// now get loaders/compilers/properties from config.
		$views = $this->config->getViews();
		if ( isset( $views[$name] ) ) {
			$def = $views[$name];

            //compiling
            if (isset($def['forceCompile']) && $def['forceCompile']==1) {
                $view->setForceCompile(true);
            }
			// Loaders
			if ( $view->requireLoaders() ) {
				try {
					if ( sizeof( $def['loaders'] ) > 0 ) {
						$view->clearLoaders();
						// If we have defined loaders, use those
						foreach( $def['loaders'] as $loader ) {
							$loaderClass = $loader['class'];
							$l = new $loaderClass();
							foreach( $loader['props'] as $key => $value ) {
								$l->setProperty( $key, $value );
							}
							$l->checkProperties();
							$view->addLoader( $l );
						}
					} else {
						// Otherwise use our default ones.
						foreach( $this->getDefaultViewLoaders( $name ) as $loader ) {
							$view->addLoader( $loader );
						}
					}

					if ( !$view->hasLoaders() ) {
						throw new AppContextException( AppContextException::ERROR_VIEW, sprintf( 'ViewRenderer "%s" requires loaders, and none were provided. Config: %s', get_class( $view ), $this->config ) );
					}
				} catch ( ViewLoaderException $e ) {
					throw new AppContextException( AppContextException::ERROR_VIEW, sprintf( 'Invalid View configuration for view %s: %s. Config: %s', get_class($view), $e->getMessage(), $this->config ) );
				}
			}

			// Compilers
			if ( $view->supportCompilers() ) {
				if ( sizeof( $def['compilers'] ) > 0 ) {
					try {
						foreach( $def['compilers'] as $compiler ) {
							$compilerClass = $compiler['class'];
							$c = new $compilerClass();
							foreach( $compiler['props'] as $key => $value ) {
								$c->setProperty( $key, $value );
							}
							$c->checkProperties();
							$view->addCompiler( $c );
						}
					}  catch ( ViewException $e ) {
						throw new AppContextException( AppContextException::ERROR_VIEW, sprintf( 'Invalid View configuration for view %s: %s', get_class( $view ), $e->getMessage() ) );
					}
				}
			}

			if ( sizeof( $def['properties'] ) > 0 ) {
				foreach( $def['properties'] as $key => $value ) {
					$view->setProperty( $key, $value );
				}
			}
		} else {
			foreach( $this->getDefaultViewLoaders( $name ) as $loader ) {
				$view->addLoader( $loader );
			}
			// Add default loaders
		}

		return $view;
	}

	/**
	 * Load the view errors from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadErrorViews( AppConfig $config ) {
		foreach( $config->getErrorViews() as $class => $view ) {
			if ( !isset( $view['name'] ) || !$view['name'] ) {
				$this->addException( AppContextException::ERROR_VIEW, sprintf( 'No name defined for error view "%s". Config: %s', $class, $config ) );
				continue;
			}
			$this->errorViews[$class] = array( $view['module'], $view['name'] );
		}
	}


	/**
	 * Load the Debugs parameters from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadDebugs( AppConfig $config ) {

		$this->Debugs=$config->getDebugs();

	}

	/**
	 * Load the Debugs parameters from the supplied AppConfig $config
	 *
	 * @param AppConfig $config
	 */
	protected function loadSmartyConfig( AppConfig $config ) {

		$this->SmartyConfig=$config->getSmartyConfig();

	}


	/**
	 * Loads default view renderers.
	 *
	 * @param string $name
	 * @return void
	 */
	protected function loadViewRenderers( $name ) {
		$this->addViewRenderer( 'xml', '\mvc\views\renderers\XMLViewRenderer' );
		$this->addViewRenderer( 'xsl', '\mvc\views\renderers\XSLViewRenderer' );
		$this->addViewRenderer( 'html', '\mvc\views\renderers\HTMLViewRenderer' );
		$this->addViewRenderer( 'json', '\mvc\views\renderers\JSONViewRenderer' );
		$this->addViewRenderer( 'data', '\mvc\views\renderers\DataViewRenderer' );
		$this->addViewRenderer( 'img', '\mvc\views\renderers\ImageViewRenderer' );
		$this->addViewRenderer( 'smarty', '\mvc\views\renderers\SmartyViewRenderer' );

		foreach( $this->config->getViews() as $name => $view ) {
			$class = (string)$view['class'];
			if ( !$this->isViewAvailable( $name ) ) {
				if ( !$class ) {
					$this->addException( AppContextException::ERROR_VIEW, sprintf( 'Invalid view renderer definition for "%s". No class supplied. Config: %s', $name, $this->config ) );
					break;
				} else {
					$this->addViewRenderer( $name, $class );
				}
			}
		}
	}

	/**
	 * Get the default view loaders. By default, a FileViewLoader is provided, with the following paths:
	 * - Lookup 1: APPROOT/views
	 * - Lookup 2: MVC framework_PATH/views
	 * - Compiled: APPROOT/views-compiled
	 *
	 * @return array
	 */
	protected function getDefaultViewLoaders( $name ) {
		switch( $name ) {
			case 'xsl':
			case 'html':
				$loader = new views\loaders\FileViewLoader();
				$loader->setExtension( 'xsl' );
				if ( is_dir( APPROOT . '/views' ) ) {
					$loader->addPath( APPROOT . '/views' );
				}
				$loader->addPath( APP_PATH . '/views' );
				return array( $loader );
			default:
				return array();
		}
	}

	protected function loadSessionValues() {
		if ( $locale = Session::get( 'LOCALE' ) ) {
			Locale::setDefault( $locale );
		}
	}

	/**
	 * Adds an AppContextException to this AppContext
	 *
	 * @param int $error An AppContextException::ERROR 
	 * @param string $message
	 */
	protected function addException( $error, $message ) {
		$this->exceptions[] = new AppContextException( $error, $message );
	}

}
