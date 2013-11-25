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
 * Factories acts as a static Factory Registry for multiple namespace class lookup
 */
class Factory extends Object {

	/**
	 * Hashtable of registered factories
	 */
	private static $factories = null;

	/**
	 * Factory name
	 */
	private $name = null;

	/**
	 * Namespaces Array for lookup
	 */
	private $namespaces = array();

	/**
	 * Array of Singleton Instances
	 */
	private $instances = array();

	/**
	 * Instantiates a new factory with the given name
	 *
	 * @param string $name
	 */
	public function __construct( $name ) {
		$this->name = $name;
	}

	/**
	 * Gets this factory name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Static initialization.
	 */
	public static function __static() {
		static::$factories = new Hashtable();
	}

	/**
	 * Registers a Factory with the supplied name
	 *
	 * @param string $name
	 */
	public static function register( Factory $factory ) {
		static::$factories->put( $factory->getName(), $factory );
		return $factory;
	}

	/**
	 * Clears all registered factories
	 */
	public static function clear() {
		static::$factories = new Hashtable();
	}

	/**
	 * Checks whether a Factory is registered
	 *
	 * @return boolean
	 */
	public static function isRegistered( $name ) {
		return static::$factories->has( $name );
	}

	/**
	 * Registers the supplied namespace to this factory
	 *
	 * @param string $namespace
	 */
	public function registerNamespace( $namespace ) {
		$this->namespaces[] = $namespace;
	}

	/**
	 * Get the namespaces for class lookup
	 *
	 * @return array
	 */
	public function getNamespaces() {
		return $this->namespaces;
	}

	/**
	 * Gets the namespaced-factorized class for the given class, looking up all available Namespaces.
	 *
	 * @param string $class
	 * @param string $ns Additional namespace suffix to append to base namespaces.
	 * @return string (Class)
	 * @throws CoreException
	 */
	public function getClass( $class, $ns=null ) {
		$nsClass = \get_ns_class( $this->namespaces, $class, $ns );
		if ( $nsClass ) {
			return $nsClass;
		} else {
			throw new CoreException( sprintf( 'Class not found "%s". ( Namespace: "%s" )', $class, join( '", "', $this->namespaces ) ) );
		}
	}

	/**
	 * Gets an Instance of the given Class, looking up all available Namespaces.
	 * optionally appending the given $ns.
	 *
	 * @param string $class
	 * @param string $ns Additional namespace suffix to append to base namespaces.
	 * @return Object
	 * @throws CoreException
	 */
	public function getInstance( $class, $ns=null, array $params=array() ) {
		if ( !isset( $this->instances[$class] ) || sizeof( $params ) > 0 ) {
			$nsClass = \get_ns_class( $this->namespaces, $class, $ns );
			if ( $nsClass ) {
				$instance = null;
				if ( sizeof( $params ) > 0 ) {
					$rClass = new \ReflectionClass( $nsClass );
					$instance = $rClass->newInstanceArgs( $params );
				} else {
					$instance = new $nsClass();
				}
				$this->instances[$class] = $instance;
			} else {
				throw new CoreException( sprintf( 'Class not found "%s". ( Namespace: "%s" )', $class, join( '", "', $this->namespaces ) ) );
			}
		}
		return $this->instances[$class];
	}

	/**
	 * Overloaded Getter. Calls getInstance.
	 *
	 * @param string $class
	 */
	public function __get( $class ) {
		return $this->getInstance( $class );
	}

	/**
	 * Overloaded Caller. Calls getInstance with the given $params as parameters to the constructor
	 *
	 * @param string $method
	 * @param array $params
	 */
	public function __call( $method, $params ) {
		return $this->getInstance( $method, null, $params );
	}

	/**
	 * Static Caller. This method works as gateway to the registered factories.
	 *
	 * The first param is the class to find, any subsequent params are passed to the class constructor
	 *
	 * @param string $method The factory registered
	 * @param array $params The desired factorized class, and any constructor parameters.
	 * @return Factory if no params are supplied, or an instance of the given class.
	 */
	public static function __callStatic( $method, $params ) {
		if ( !static::$factories->has( $method ) ) {
			throw new CoreException( sprintf( 'No factory with name "%s"', $method ) );
		}
		$f = static::$factories->get( $method );
		if ( !isset( $params[0] ) ) {
			return $f;
		}
		$args = array_splice( $params, 1 );
		$class = $params[0];
		if ( strstr( $class, '\\' ) ) {
			$pos = strrpos( $class, '\\' );
			$ns = substr( $class, 0, $pos );
			$class = substr( $class, $pos + 1 );
		} else {
			$ns = null;
		}
		return $f->getInstance( $class, $ns, $args );
	}

}
