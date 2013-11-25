<?php

namespace core;

/**
 * Base Object class
 */
class Object {
	
	/**
	 * Returns the unique internal hash for this Object.
	 *
	 * @return string
	 */
	public function id() {
		return spl_object_hash( $this );
	}

	/**
	 * Returns a string representation of this Object.
	 *
	 * @return string
	 */
	public function __toString() {
		return sprintf( "[%s (ID #%s)]\n", get_class( $this ), $this->id() );
        return sprintf( "{%s}\n", get_object_vars($this));
	}

}
