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

namespace util;

use core\Object;
use core\CoreException;

/**
 * Logger Factory. 
 */
class LoggerFactory extends Object {

	/**
	 * Loggers available
	 */
	private static $loggers = array(
				'file' => 'util\FileLogger'
			);

	/**
	 * Array of Logger Instances.
	 */
	private static $instances = array();

	/**
	 * Private Constructor to avoid instantiation.
	 */
	private function __construct() {
		return self::$instances['DEFAULT'];
	}

	/**
	 * Registers a new Logger Type (Logger subclass)
	 *
	 * @param string $name The Logger name (this is used as the protocol in the logger url)
	 * @param string $class The Logger subclass
	 */
	public static function registerLogger( $name, $class ) {
		self::$loggers[$name] = $class;
	}

	/**
	 * Deregisters a Logger Class
	 *
	 * @param string $name
	 */
	public static function deregisterLogger( $name ) {
		unset( self::$loggers[$name] );
	}

	/**
	 * Gets a new Logger Instance for the given name and url
	 *
	 * The URL is in the form
	 * <logger>://<parameters>
	 * 
	 * Each Logger type has its own parameters. The FileLogger, for instance, accepts the form:
	 * file:///path/to/file
	 * 
	 * @param string $name The Logger Name
	 * @param Logger $url The Logger URL
	 * @param int $level The Log Level
	 * @return Logger
	 * @throws CoreException
	 */
	public static function newLogger( $name,$type, $url, $level=Logger::LEVEL_EXCEPTION ) {
		$params = parse_url( $url );
		if ( !array_key_exists( $type, self::$loggers ) ) {
			throw new CoreException( sprintf( 'Unregistered Logger Class for "%s".', $params['scheme'] ) );
		}
		$class = self::$loggers[$type];
		return new $class( $name, $level, $params );
	}

	/**
	 * Adds a Logger to this logger factory, so it can be referenced statically
	 *
	 * @param Logger $logger
	 */
	public static function addLogger( Logger $logger ) {
		self::$instances[$logger->getName()] = $logger;
	}

	/**
	 * Gets a previously added logger by name.
	 *
	 * @param string $name
	 * @return Logger
	 * @throws CoreException
	 */
	public static function getLogger( $name ) {
		if ( isset( self::$instances[$name] ) ) {
			return self::$instances[$name];
		} else {
			throw new CoreException( sprintf( 'No Logger instance found for name: "%s"', $name ) );
		}
	}

	/**
	 * Sets the Default Logger.
	 * The default logger is saved as name DEFAULT
	 *
	 * @param Logger $logger
	 */
	public static function setDefault( Logger $logger ) {
		self::$instances['DEFAULT'] = $logger;
	}

	/**
	 * Returns the Default Logger
	 *
	 * @return Logger
	 * @throws CoreException
	 */
	public static function getDefault() {
		if ( !isset( self::$instances['DEFAULT'] ) ) {
			throw new CoreException( 'No Default Logger' );
		}
		return self::$instances['DEFAULT'];
	}

	/**
	 * Lists all available Loggers
	 *
	 * @return string
	 */
	public static function listLoggers() {
		echo "Available Loggers:\n";
		echo "-------\n";
		foreach( self::$loggers as $name => $class ) {
			printf( " - %10s %s\n", $name, $class );
		}
		echo "Instanced Loggers:\n";
		echo "-------\n";
		foreach( self::$instances as $name => $logger ) {
			printf( " - %10s %s\n", $name, $logger );
		}
	}

	/**
	 * Lists all active Logger Instances
	 *
	 * @return string
	 */
	public static function listInstances() {
		printf( "Listing %s active Logger(s)\n", sizeof( self::$instances ) );
		echo "-------\n";
		foreach( self::$instances as $alias => $loger ) {
			printf( " - Logger Alias: %s\n", $alias );
			echo $logger;
		}
	}

	/**
	 * Clears all added loggers
	 */
	public static function clearLoggers() {
		self::$instances = array();
	}

}
