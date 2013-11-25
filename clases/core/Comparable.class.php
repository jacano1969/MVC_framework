<?php

namespace core;

/**
 * Defines a generic comparable interface
 */
interface Comparable {

	/**
	 * Compares this object to the provided one returning wether it's equal.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function equals( Object $obj );

	/**
	 * Compares this object to the provided one returning wether it's greater.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function greaterThan( Object $obj );

	/**
	 * Compares this object to the provided one returning wether it's lesser.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function lesserThan( Object $obj );

	/**
	 * Compares this object to the provided one returning wether it's greater or equal
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function greaterOrEqualThan( Object $obj );

	/**
	 * Compares this object to the provided one returning wether it's lesser or equal
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function lesserOrEqualThan( Object $obj );
}
