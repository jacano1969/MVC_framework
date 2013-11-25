<?php
/**
 * This file is part of MVC framework
 *
 * MVC framework is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * MVC framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MVC framework; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @author $Author$
 * @version $Rev$
 * @updated $Date$
 *

 */

namespace mvc;

use io\File;

/**
 * Implements an XML Config that loads a xml file with a specific format to feed an AppContext.
 */
class XMLAppConfig implements AppConfig {

	/**
	 * XML File
	 */
	protected $file = null;

	/**
	 * XML Data
	 */
	protected $xml = null;

	/**
	 * Views
	 */
	protected $views = null;

	/**
	 * Instantiates an XML Config for the supplied file, validating it.
	 *
	 * See MVC framework documentation for the XML Config format
	 *
	 * @param string $file
	 * @return XMLAppConfig
	 */
	public function __construct( $file ) {
		$this->file = new File( $file );
		if ( !$this->file->exists() || !$this->file->isReadable() ) {
			throw new AppException( sprintf( 'Cuould not load configuration file: "%s". File does not exist or is not readable', $this->file ) );
		}

		libxml_use_internal_errors(true);
		$this->xml = simplexml_load_file( $this->file->getName() );
		if ( !$this->xml ) {
			foreach( libxml_get_errors() as $error ) {
				$errors[] = sprintf( 'XML Error at line %d: %s', $error->line, $error->message );
			}
			throw new AppConfigException( sprintf( 'Error loading configuration file: "%s": %s', $this->file, join( "\n", $errors ) ) );
		}
	}

	/**
	 * Gets the xml file wrapped by this XMLAppConfig.
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
		return (string)$this->xml['id'];
	}

	/**
	 * Gets the App Name
	 *
	 * @return string
	 */
	public function getName() {
		return (string)$this->xml['name'];
	}

	/**
	 * Gets the config encoding
	 *
	 * @return string
	 */
	public function getEncoding() {
		if ( sizeof( $this->xml->encoding ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one encoding defined (%d)', sizeof( $this->xml->encoding ) ) );
		}
		return $this->xml->encoding ? (string)$this->xml->encoding : null;
	}

	/**
	 * Gets the default config timezone
	 *
	 * @return string
	 */
	public function getTimezone() {
		if ( sizeof( $this->xml->timezone ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one timezone defined(%d)', sizeof( $this->xml->timezone ) ) );
		}
		return $this->xml->timezone ? (string)$this->xml->timezone : null;
	}

	/**
	 * Gets the default config locale
	 *
	 * @return string
	 */
	public function getDefaultLocale() {
		if ( sizeof( $this->xml->{'default-locale'} ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one default locale defined (%d)', sizeof( $this->xml->{'default-locale'} ) ) );
		}
		return $this->xml->{'default-locale'} ? (string)$this->xml->{'default-locale'} : null;
	}

	/**
	 * Gets an array of supported locales, other than the default (no need to redefine it)
	 *
	 * @return array
	 */
	public function getSupportedLocales() {
		$ret = array();
		foreach( $this->xml->locale as $locale ) {
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
		foreach( $this->xml->logger as $logger ) {
			$name = (string)$logger['name'];
			$path = (string)$logger['path'];
			$level = (string)$logger['level'];
			if ( !$name || !$path || !$level ) {
				throw new AppConfigException( sprintf( 'Invalid logger definition (Name: "%s", Path: "%s", Level: "%s"). Loggers require name, path and level', $name, $path, $level ) ); 
			}
			$loggers[] = array( 'name' => $name, 'path' => $path, 'level' => $level );
		}
		return $loggers;
	}

	/**
	 * Gets an array of factory definitions
	 *
	 * Factory definitions have a name, and N namespaces for each one.
	 */
	public function getFactories() {
		$factories = array();
		foreach( $this->xml->factory as $factory ) {
			$name = (string)$factory['name'];
			if ( !$name ) {
				throw new AppConfigException( 'Wrong factory definition. Name not provided' );
			}
			$factories[$name] = array();
			foreach( $factory->namespace as $ns ) {
				$ns = (string)$ns;
				if ( $ns == '' ) {
					throw new AppConfigException( sprintf( 'Empty namespace definition for Factory: "%s"', $name ) );
					continue;
				}
				$factories[$name][] = $ns;
			}
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
		foreach( $this->xml->classpath as $path ) {
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
		foreach( $this->xml->{'connection-driver'} as $driver ) {
			$name = $driver['name'];
			$class = $driver['class'];
			if ( !$name || !$class ) {
				throw new AppConfigException( sprintf( 'Connection driver definitions require name and class. (Found name: "%s", Class: "%s")', $name, $class ) );
			}
			$rc = new \ReflectionClass( (string)$class );
			if ( !$rc->isSubclassOf( 'php\sql\Connection' ) ) {
				throw new AppConfigException( sprintf( 'Connection Driver class "%s" must extend php\sql\Connection', $class ) );
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
		foreach( $this->xml->connection as $conn ) {
			$name = $conn['name'] ? (string)$conn['name'] : 'DEFAULT';
			$url = (string)$conn['url'];
			$flags = (string)$conn['flags'];
			$binflags = 0;
			if ( $flags ) {
				if ( preg_match_all( '/Connection::([\w]*)/', $flags, $matches ) ) {
					$rc = new \ReflectionClass( 'php\sql\Connection' );
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
		if ( sizeof( $this->xml->session ) > 1 ) {
			throw new AppConfigException( sprintf( 'More than one session handler defined (%d)', sizeof( $this->xml->session ) ) );
		}
		if ( $this->xml->session ) {
			$class = (string)$this->xml->session['class'];
			$session = array( 'class' => $class );
			foreach( $this->xml->session->property as $prop ) {
				$name = (string)$prop['name'];
				$value = (string)$prop['value'];
				$session['props'][$name] = $value;
			}
			return $session;
		} else {
			return null;
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
			foreach( $this->xml->view as $view ) {
				$name = (string)$view['name'];
				$class = (string)$view['class'];
                $forceCompile = intval($view['force-compile']);

				$this->views[$name] = array();
				$this->views[$name]['class'] = $class;
                $this->views[$name]['forceCompile'] = $forceCompile;
				$this->views[$name]['loaders'] = array();
				$this->views[$name]['compilers'] = array();
				$this->views[$name]['properties'] = array();

				foreach( $view->loader as $loader ) {
					$class = (string)$loader['class'];
					$l = array();
					$l['class'] = $class;
					$l['props'] = array();
					foreach( $loader->property as $prop ) {
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
				foreach( $view->compiler as $compiler ) {
					$class = (string)$compiler['class'];
					$c = array();
					$c['class'] = $class;
					$c['props'] = array();
					foreach( $compiler->property as $prop ) {
						$key = (string)$prop['name'];
						$value = (string)$prop['value'];
						$c['props'][$key] = $value;
					}
					$this->views[$name]['compilers'][] = $c;
				}
				foreach( $view->property as $property ) {
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
		foreach( $this->xml->{'error-view'} as $v ) {
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
		return explode(',', $this->xml->{'debug-ip'});
	}

	/**
	 * Loads the supplied AppConfigHandler with this XMLAppConfig values.
	 *
	 * In XMLAppConfig, config handler sections are nodes under the root <app> node, and each config value
	 * is a tag (config name) with its value (config value)
	 *
	 * @param AppConfigHandler $handler
	 */
	public function loadHandler( AppConfigHandler $handler ) {
		$node = $handler->getSection();
		if ( !$this->xml->{$node} ) {
			throw new AppConfigException( sprintf( 'Cant load handler configuration for "%s". Section Node "%s" not found', get_class( $handler ), $node ) );
		}
		if ( $this->xml->{$node}['enabled'] == 'true' ) {
			foreach( $this->xml->{$node}->children() as $name => $value ) {
				$handler->setValue( (string)$name, (string)$value );
			}
		}
	}

	/**
	 * Returns a string representation of this XMLAppConfig, with the class name and file loaded.
	 *
	 * @return string
	 */
	public function __toString() {
		return sprintf( '%s: %s', get_class(), $this->file );
	}

}
