<?php

namespace mvc;

use io\File;

/**
 * Implements an JSON Config that loads a JSON file with a specific format to feed an AppContext.
 */
class JSONAppConfig implements AppConfig {

	/**
	 * JSON File
	 */
	protected $file = null;

	/**
	 * JSON Data
	 */
	protected $json = null;

	/**
	 * Views
	 */
	protected $views = null;

	/**
	 * Instantiates an JSON Config for the supplied file, validating it.
	 *
	 * See MVC framework documentation for the JSON Config format
	 *
	 * @param string $file
	 * @return JSONAppConfig
	 */
	public function __construct( $file ) {
		$this->file = new File( $file );

		if ( !$this->file->exists() || !$this->file->isReadable() ) {
			throw new AppException( sprintf( 'No se ha podido cargar el fichero de configuracion: "%s". fichero no existente o no accesible', $this->file ) );
		}
        try{
	        $this->json = get_object_vars($this->json_parser( $this->file->read() )->{'app'});
	        switch(json_last_error()) {
		        case JSON_ERROR_NONE:
			        break;
		        case JSON_ERROR_DEPTH:
			        throw new AppConfigException( 'Excedido tamaño máximo de la pila al cargar fichero config: ' );
			        break;
		        case JSON_ERROR_STATE_MISMATCH:
			        throw new AppConfigException( 'Desbordamiento de buffer o los modos no coinciden al cargar fichero config: ' );
			        break;
		        case JSON_ERROR_CTRL_CHAR:
			        throw new AppConfigException( 'Encontrado carácter de control no esperado al cargar fichero config: ' );
			        break;
		        case JSON_ERROR_SYNTAX:
			        throw new AppConfigException( 'Error de sintaxis, JSON mal formado al cargar fichero config: ' );
			        break;
		        case JSON_ERROR_UTF8:
			        throw new AppConfigException( 'Caracteres UTF-8 malformados, posiblemente están mal codificados al cargar fichero config: ' );
			        break;
		        default:
			        throw new AppConfigException( sprintf('Error desconocido id->%s al cargar fichero config: '),json_last_error() );
			        break;
	        }
            //pprint($this->json);
        }catch (Exception $error){
            throw new AppConfigException( sprintf( 'Error cargando el fichero de configuracion: "%s": %s', $this->file, join( "\n", $error ) ) );

        }
	}

	public function json_parser($json){

//  Removes multi-line comments and does not create
//  a blank line, also treats white spaces/tabs
		$json = preg_replace('!^[ \t]*/\*.*?\*/[ \t]*[\r\n]!s', '', $json);
		//$json = preg_replace('/(\s+)\/\*([^\/]*)\*\/(\s+)/s', "\n", $json);
//  Strip blank lines
		$json = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $json);
//  Removes single line '//' comments, treats blank characters
		$json = preg_replace('![ \t]*\t//.*[ \t]*[\r\n]!', '', $json);
		return json_decode($json);
	}

	/**
	 * Gets the JSON file wrapped by this JSONAppConfig.
	 *
	 * @return File
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * Gets the App ID
	 *
	 * @return string
	 */
	public function getId() {
		return (string)$this->json['id'];
	}

	/**
	 * Gets the App Name
	 *
	 * @return string
	 */
	public function getName() {
		return (string)$this->json['name'];
	}

	/**
	 * Gets the config encoding
	 *
	 * @return string
	 */
	public function getEncoding() {
		if ( sizeof( $this->json['encoding'] ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one encoding defined (%d)', sizeof( $this->json['encoding'] ) ) );
		}
		return $this->json['encoding'] ? (string)$this->json['encoding'] : null;
	}

	/**
	 * Gets the default config timezone
	 *
	 * @return string
	 */
	public function getTimezone() {
		if ( sizeof( $this->json['timezone'] ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one timezone defined(%d)', sizeof( $this->json['timezone'] ) ) );
		}
		return $this->json['timezone'] ? (string)$this->json['timezone'] : null;
	}

	/**
	 * Gets the default config locale
	 *
	 * @return string
	 */
	public function getDefaultLocale() {
		if ( sizeof( $this->json['default-locale'] ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one default locale defined (%d)', sizeof( $this->json['default-locale'] ) ) );
		}
		return $this->json['default-locale'] ? (string)$this->json['default-locale'] : null;
	}

	/**
	 * Gets an array of supported locales, other than the default (no need to redefine it)
	 *
	 * @return array
	 */
	public function getSupportedLocales() {
		$ret = array();
        if ($this->json['locale'])foreach( $this->json['locale'] as $locale ) {
			$ret[] = (string)$locale;
		}
		return $ret;
	}

	/**
	 * Gets an array of logger definitions
	 *
	 * Logger definitions can have several properties:
	 *  - name: Optional. The logger name. if no name, then it's assumed to be the default logger ('default')
	 *  - path: Required. The logger path. See LoggerFactory for supported paths.
	 *  - level: Required. The log level. See Logger for log levels.
	 */
	public function getLoggers() {
		$loggers = array();
        if ($this->json['logger'])foreach( $this->json['logger'] as $logger ) {
			$name = (string)$logger->name;
            $type = (string)$logger->type;
			$path = (string)$logger->path;
			$level = (string)$logger->level;
			if ( !$name || !$path || !$level ) {
				throw new AppConfigException( sprintf( 'Invalid logger definition (Name: "%s", Path: "%s", Level: "%s", Type: "%s"). Loggers require name, path and level', $name, $path, $level,$type ) );
			}
			$loggers[] = array( 'name' => $name,'type' => $type, 'path' => $path, 'level' => $level );
		}
		return $loggers;
	}
	/**
	 * Gets an array of debug data
	 *
	 * Logger definitions can have several properties:
	 *  - level: Required. The debug level. .
	 *  - domain_debug: Required.  list domains witch debug facility.
	 *  - ips_debug: Required. The list ips of station authorize for debug. .
	 */
	public function getDebugs(){
		$level = (string)$this->json['debug']->level;
		$domain = (array)$this->json['debug']->domain_debug;
		$ips = (array)$this->json['debug']->ips_debug;
		$debug = array( 'level' => $level,'domain' => $domain, 'ips' => $ips);
		return $debug;
	}
	/**
	 * Gets an array of configuracion del Smarty
	 *
	 * Logger definitions can have several properties:
	 * "caching"       :  Se activa/desactiva el cacheado
	 * "cache_lifetime":  Tiempo de vida del cache
	 * "force_compile" :  Se fuerza el compilado en cada ejecución
	 * "path"          :  Ruta al Smarty
	 * "template_path" :  Ruta de los templates.
	 * "compile_path"  :  Ruta de los compilados
	 * "cache_path"    :  Ruta del cache
	 */
	public function getSmartyConfig(){
		$caching        = (string)$this->json['smartyconf']->caching;
		$cache_lifetime = (string)$this->json['smartyconf']->cache_lifetime;
		$force_compile  = (string)$this->json['smartyconf']->force_compile;
		$path           = (string)$this->json['smartyconf']->path;
		$template_path  = (string)$this->json['smartyconf']->template_path;
		$compile_path   = (string)$this->json['smartyconf']->compile_path;
		$cache_path     = (string)$this->json['smartyconf']->cache_path;
		$debug_smart    = isset($this->json['smartyconf']->debug)?(string)$this->json['smartyconf']->debug:false;
		$smtyconf = array( 'caching' => $caching,'cache_lifetime' => $cache_lifetime, 'force_compile' => $force_compile, 'path' => $path, 'template_path' => $template_path, 'compile_path' => $compile_path, 'cache_path' => $cache_path, 'debug' => $debug_smart);
		return $smtyconf;
	}

	/**
	 * Gets an array of factory definitions
	 *
	 * Factory definitions have a name, and N namespaces for each one.
	 */
	public function getFactories() {
		$factories = array();
        if ($this->json['factory'])foreach( $this->json['factory'] as $factory ) {
			$name = (string)$factory->name;
			if ( !$name ) {
				throw new AppConfigException( 'Wrong factory definition. Name not provided' );
			}
			$factories[$name] = $factory->namespace;
		}
		return $factories;
	}

	/**
	 * Gets an array fo classpath entries
	 * 
	 * ClassPath entries are configured like this:
	 * <classpath>/var/folder/classes</classpath>
	 * <classpath>/var/plugins/classes</classpath>
	 */
	public function getClassPathEntries() {
		$entries = array();
        if ($this->json['classpath'])foreach( $this->json['classpath'] as $path ) {
			$entries[] = strval($path);
		}
		return $entries;
	}

	/**
	 * Gets an array of connection driver definitions
	 *
	 * Connection drivers have a name and a class that must extend php\sql\Connection, registering the driver.
	 */
	public function getConnectionDrivers() {
		$drivers = array();
        if (isset($this->json['connection-driver']))foreach( $this->json['connection-driver'] as $driver ) {
			$name = $driver['name'];
			$class = $driver['class'];
			if ( !$name || !$class ) {
				throw new AppConfigException( sprintf( 'Connection driver definitions require name and class. (Found name: "%s", Class: "%s")', $name, $class ) );
			}
			$rc = new \ReflectionClass( (string)$class );
			if ( !$rc->isSubclassOf( 'sql\Connection' ) ) {
				throw new AppConfigException( sprintf( 'Connection Driver class "%s" must extend sql\Connection', $class ) );
			}
			$drivers[(string)$name] = (string)$class;
		}
		return $drivers;
	}

	/**
	 * Gets an array of connection definitions
	 *
	 * Connection definitions have a name, url, and optional flags
	 */
	public function getConnections() {
		$conns = array();
        if ($this->json['connection'])foreach( $this->json['connection'] as $conn ) {
			$name = $conn->name ? (string)$conn->name : 'DEFAULT';
			$url = (string)$conn->url;
			$flags = (string)$conn->flags;
			$binflags = 0;
			if ( $flags ) {
				if ( preg_match_all( '/Connection::([\w]*)/', $flags, $matches ) ) {
					$rc = new \ReflectionClass( 'sql\Connection' );
					foreach( $matches[1] as $idx => $m ) {
						if ( $value = $rc->getConstant( $m ) ) {
							$binflags |= $value;
						}
					}
				}
			}
			if ( !$url ) {
				throw new AppConfigException( sprintf( 'No connection url defined for connection: "%s"', $name ) );
			}
			$conns[$name]['url'] = $url;
			$conns[$name]['flags'] = $flags;
		}
		return $conns;
	}

	/**
	 * Gets the session handler definition
	 *
	 * Session Handler definitions have a class and handler-specific properties
	 */
	public function getSessionHandler() {
        return null;
		if ( sizeof( $this->json['session'] ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one session handler defined (%d)', sizeof( $this->json['session']) ) );
		}
		if ( $this->json['session'] ) {
			$class = (string)$this->json['session']['class'];
			$session = array( 'class' => $class );
			foreach( $this->json['session']->property as $prop ) {
				$name = (string)$prop['name'];
				$value = (string)$prop['value'];
				$session['props'][$name] = $value;
			}
			return $session;
		} else {
		}
	}

	/**
	 * Gets the views definitions
	 * 
	 * View definitions have:
	 * - name: Required. The view name, registering the available extension.
	 * - class: Required. The view class.
	 * - loaders: Optional. Array of ViewLoaders. Each view loader has:
	 *   * class: Required. The ViewLoader class
	 *   * props: Array of name => value properties
	 * - compilers: Optional. Array of ViewCompilers. Each view compiler has:
	 *   * class: Required. The ViewCompiler class
	 *   * props: Array of name => value properties
	 *
	 * @return array
	 */
	public function getViews() {
		if ( $this->views === null ) {
			$this->views = array();
            if (isset($this->json['view']))foreach( $this->json['view'] as $view ) {
				$name = (string)$view['name'];
				$class = (string)$view['class'];
                $forceCompile = intval($view['force-compile']);

				$this->views[$name] = array();
				$this->views[$name]['class'] = $class;
                $this->views[$name]['forceCompile'] = $forceCompile;
				$this->views[$name]['loaders'] = array();
				$this->views[$name]['compilers'] = array();
				$this->views[$name]['properties'] = array();

                if ($view->loader)foreach( $view->loader as $loader ) {
					$class = (string)$loader['class'];
					$l = array();
					$l['class'] = $class;
					$l['props'] = array();
                    if ($loader->property)foreach( $loader->property as $prop ) {
						$key = (string)$prop['name'];
						$value = (string)$prop['value'];
						if ( isset( $l['props'][$key] ) ) {
							if ( !is_array( $l['props'][$key] ) ) {
								$l['props'][$key] = array( $l['props'][$key] );
							}
							$l['props'][$key][] = $value;
						} else {
							$l['props'][$key] = $value;
						}
					}
					$this->views[$name]['loaders'][] = $l;
				}
                if ($view->compiler)foreach( $view->compiler as $compiler ) {
					$class = (string)$compiler['class'];
					$c = array();
					$c['class'] = $class;
					$c['props'] = array();
                    if ($compiler->property)foreach( $compiler->property as $prop ) {
						$key = (string)$prop['name'];
						$value = (string)$prop['value'];
						$c['props'][$key] = $value;
					}
					$this->views[$name]['compilers'][] = $c;
				}
                if ($view->property)foreach( $view->property as $property ) {
					$key = (string)$property['name'];
					$value = (string)$property['value'];
					if ( isset( $this->views[$name]['properties'][$key] ) ) {
						if ( !is_array( $this->views[$name]['properties'][$key] ) ) {
							$first = $this->views[$name]['properties'][$key];
							$this->views[$name]['properties'][$key] = array( $first );
						}
						$this->views[$name]['properties'][$key][] = $value;
					} else {
						$this->views[$name]['properties'][$key] = $value;
					}
				}

			}
		}
		return $this->views;
	}

	/**
	 * Gets the error view definitions. 
	 * Error view definitions have:
	 * - class: Array key. Required. Exception class to render with this error view
	 * - name: Required. Error view name
	 * - module: Optional. Error view module
	 *
	 * @return array
	 */
	public function getErrorViews() {
		$errors = array();
        if (isset($this->json['error-view']))foreach( $this->json['error-view'] as $v ) {
			$class = (string)$v['class'];
			$name = (string)$v['name'];
			$module = $v['module'] ? (string)$v['module'] : null;

			$errors[$class] = array( 'module' => $module, 'name' => $name );
		}
		return $errors;
	}

	/**
	 * Gets the debug ips
	 */
	public function getDebugIps() {
		return explode(',', $this->json['debug']->ips_debug);
	}

	/**
	 * Loads the supplied AppConfigHandler with this JSONAppConfig values.
	 *
	 * In JSONAppConfig, config handler sections are nodes under the root <app> node, and each config value
	 * is a tag (config name) with its value (config value)
	 *
	 * @param AppConfigHandler $handler
	 */
	public function loadHandler( AppConfigHandler $handler ) {
		$node = $handler->getSection();
		if ( !$this->json[$node] ) {
			throw new AppConfigException( sprintf( 'Cant load handler configuration for "%s". Section Node "%s" not found', get_class( $handler ), $node ) );
		}
		$conf=((array)$this->json[$node]);
;		if ( $conf['enabled'] == "true" ) {
			foreach( $conf as $name => $value ) {
				$handler->setValue( (string)$name, (string)$value );
			}
		}
	}

	/**
	 * Returns a string representation of this JSONAppConfig, with the class name and file loaded.
	 *
	 * @return string
	 */
	public function __toString() {
		return sprintf( '%s: %s', get_class(), $this->file );
	}

}
