<?php

namespace orm;

use core\Object;

use sql\ResultSet;

/**
 * This class is used as an ActiveRecord wrapper for iterating through ResultSet objects
 * and hydrating the target ActiveRecord on each pass.
 */
class ActiveRecordIterator extends Object implements \Iterator {

	/**
	 * The underlying ActiveRecord to be hydrated.
	 */
	private $ar;

	/**
	 * The underlying ResultSet to be iterated.
	 */
	private $rs;
	
	/**
	 * Instantiates a new ActiveRecordIterator for the given ActiveRecord and ResultSet
	 * 
	 * @param ActiveRecord $ar
	 * @param ResultSet $rs
	 */
	public function __construct( ActiveRecord $ar, ResultSet $rs ) {
		$this->ar = $ar;
		$this->rs = $rs;
	}
	
	
	/**
	 * Iterator::rewind() method. Called once at the beginning of a foreach loop
	 * 
	 * Rewinds the underlying ResultSet object and hydrates the ActiveRecord with the first record.
	 */
	public function rewind() {
		if ( $this->rs->rewind() ) {
			$this->ar->hydrate( $this->rs->current() );
		}
	}

	/**
	 * Iterator::current() method. Called on a foreach() loop to fetch the value (as in $key => $value)
	 *
	 * @return ActiveRecord The underlying Object, hydrated.
	 */
	public function current() {
		return $this->ar;
	}

	/**
	 * Iterator::key() method. Called on a foreach() loop to fetch the key (as in $key => $value)
	 * 
	 * @return int The ResultSet row number.
	 */
	public function key() {
		return $this->rs->rownum();
	}

	/**
	 * Iterator::valid() method. Called on every pass of a foreach() loop to check if the current iteration is valid.
	 * (If false, the loop is broken)
	 *
	 * @return boolean ResultSet::valid() result.
	 */
	public function valid() {
		return $this->rs->valid();
	}

	/**
	 * Iterator::next() method. Called on every pass of a foreach() loop, at the end, to advance the cursor.
	 * Calls ResultSet::next() loading the row in the ActiveRecords if successful
	 *
	 * @return boolean Wether we have a next row or not
	 */
	public function next() {
		if ( $this->rs->next() ) {
			$this->ar->hydrate( $this->rs->current() );
			return $this->ar;
		} else {
			return false;
		}
	}

	/**
	 * Seeks to a specific row in the ResultSet, hydrating the ActiveRecord and returning it.
	 *
	 * @param unknown_type $idx
	 * @return unknown
	 */
	public function row( $idx ) {
		$this->rs->seek( $idx );
		$this->ar->hydrate( $this->rs );
		return $this->ar;
	}

	/**
	 * Returns the number for selected rows.
	 *
	 * @return int ResultSet::selectedRows()
	 */
	public function length() {
		return $this->rs->selectedRows();
	}

	/**
	 * Gets the underlying ActiveRecord class
	 *
	 * @param string
	 */
	public function getRecordClass() {
		return get_class( $this->ar );
	}

	/**
	 * Built-in __toString method.
	 *
	 * @return string A String representation of this ActiveRecordIterator.
	 */
	public function __toString() {	
		$str = "ActiveRecordIterator\n";
		$str.= sprintf( " * Row Number: %s\n", $this->rs->rownum() );
		$str.= sprintf( " * Total Rows: %s\n", $this->rs->selectedRows() );
		$str.= sprintf( " -> ActiveRecord: %s\n", get_class( $this->ar ) );
		return $str;
	}

}
