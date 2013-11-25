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

/**
 * Basic Hashtable class, wrapping around a standard associative array.
 */
class Hashtable extends Object implements \Iterator{
	
	/**
	 * The underlying array.
	 */
	private $array = array();

	public function __construct( $values=null ) {
		if ( is_array( $values ) ) {
			$this->array = $values;
		}
	}

	/**
	 * Removes an element from the Hashtable
	 *
	 * @param string $key
	 */
	public function del( $key ) {
		unset( $this->array[$key] );
	}

	/**
	 * Puts a new value into the Hashtable
	 *
	 * @param string $key
	 * @param mixed $val
	 */
	public function put( $key, $val ) {
		$this->array[$key] = $val;
	}
	
	/**
	 * Checks wether a given key exists in the Hashtable.
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has( $key ) {
		return array_key_exists( $key, $this->array );
	}
	
	/**
	 * Gets a value from the Hashtable.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get( $key ) {
		return ( $this->has($key) ? $this->array[$key] : null );
	}

	/**
	 * Magic Getter. Wraps get
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Magic Setter. Wraps put
	 *
	 * @param string $key
	 * @param mixed $val
	 */ 
	public function __set( $key, $val ) {
		$this->put( $key, $val );
	}
	
	/**
	 * Returns the underlying associative array.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->array;
	}
	
	/**
	 * Clears the Hashtable, emptying the underlying array.
	 */
	public function clear() {
		$this->array = array();
	}

	/**
	 * Returns the size of this Hashtable (amount of elements)
	 * 
	 * @return int
	 */
	public function length() {
		return sizeof( $this->array );
	}

	/**
	 * Return the keys for this Hashtable
	 *
	 * @return array
	 */
	public function getKeys() {
		return array_keys( $this->array );
	}

	/**
	* Reset cursor position	
	*/
	public function rewind() {
		reset($this->array);
	}
	
	/**
	* Return count of objects 
	*/
	public function count() {
		return count($this->array);
	}
	
	/**
	* Return curren element 
	*/
	public function current() {
		return  current($this->array);
	}
	
	/**
	* return Next element 
	*/
	public function next() {
		return  next($this->array);
	}
	
	/**
	* Return current key 
	*/
	public function key() {
		return  key($this->array);
	}
	
	/**
	* Comprobate if is Valid iterator 
	*/
	public function valid() {
		return ($this->current() !== false);
	}

	/**
	* Return a new Instance of Hashtable with args
	*/
	public static function getFromArgs(){

		$args = func_get_args();

		if(!is_null($args) && is_array($args))
			return new Hashtable($args);

		return new Hashtable();
	}

	/**
	 * Returns a string representation of this Hashtable.
	 */
	public function __toString() {
		$str = sprintf( "[Hashtable (ID #%s)] (%d elements)\n", $this->id(), $this->length() );
		$str.= "Values:\n";
		foreach( $this->array as $field => $value ) {
			$str.= sprintf( "  -> %-25s = %s\n", $field, $value );
		}
		return $str."\n";
	}
	
}
