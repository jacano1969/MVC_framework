<?php

namespace sql;

use core\Object;

/**
 * Class ResultSet 
 * 
 * This class is meant to be extended by database-specific subclasses (MySQLResultSet, OCIResultSet, etc)
 *
 * It's an iterable class (implements Iterator) to be used on foreach() loops
 */
abstract class ResultSet extends Object implements \Iterator {

	/**
	 * The PreparedStatement this ResultSet originated from
	 */
	protected $stmt = null;

	/**
	 * The underlying PHP Resource Identifier
	 */
	protected $resource = null;
	
	/**
	 * Fetch Mode (One of the Connection::MODE_* Constants).
	 * By default is inherited by the Connection Object that originated this ResultSet.
	 */
	protected $mode = null;

	/**
	 * The fully-qualified list fields retrieved (table, column)
	 */
	protected $fields = array();

	/**
	 * The tables in this ResultSet.
	 */
	protected $tables = array();
	
	/**
	 * The number of tables (essentially, sizeof( $this->tables )). Stored separately to save on sizeof calls.
	 */
	protected $tabnum = 0;

	/**
	 * The number of selected rows
	 */
	protected $selectedRows = 0;

	/**
	 * The current row number in the iteration
	 */
	protected $rownum = null;

	/**
	 * The underlying ResultSetRow populated in turn for each row retrieved.
	 */
	protected $row = null;

	/**
	 * Instantiates a new ResultSet object with the given Connection, PHP Resource Identifier, and fetch mode
	 *
	 * @param PreparedStatement $stmt The PreparedStatement that generated this RresultSet
	 * @param PHPResource $resource The PHP Resource Identifier
	 */
	public function __construct( PreparedStatement $stmt, $resource ) {
		$this->stmt = $stmt;
		$this->resource = $resource;
		$this->rownum = 0;
		$this->mode = $stmt->getConnection()->getMode();
		$this->row = new ResultSetRow( $this );
	}

	/**
	 * Returns the ResultSet Fetch Mode
	 *
	 * @return string
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * Iterator::rewind() method. Called once at the beginning of a foreach loop
	 *
	 * Simply calls seek(0) (seeks current result set to row 0)
	 */
	public function rewind() {
		return $this->seek(0);
	}

	/**
	 * Iterator::current() method. Called on a foreach() loop to fetch the value (as in $key => $value)
	 *
	 * @return ResultSet This object
	 */
	public function current() {
		return $this->row;
	}

	/**
	 * Iterator::key() method. Called on a foreach() loop to fetch the key (as in $key => $value)
	 * 
	 * @return int The current row number
	 */
	public function key() {
		return $this->rownum;
	}

	/**
	 * Iterator::valid() method. Called on every pass of a foreach() loop to check if the current iteration is valid.
	 * (If false, the loop is broken)
	 *
	 * @return boolean Wether we have a hydrated row loaded
	 */
	public function valid() {
		return ( !$this->row->isEmpty() );
	}

	/**
	 * Returns the type of field by name or index
	 *
	 * @return string
	 */
	public abstract function getFieldType( $name );

	/**
	 * Iterator::next() method. Called on every pass of a foreach() loop, at the end, to advance the cursor.
	 * Moves to the next row in the result set, subsequently calling loadRow()
	 *
	 * @return boolean The result of loadRow upon moving to next one
	 */
	public function next() {
		// Workaround for allowing both foreach() and while() loops
		// This bit is only called the first time when using:
		// while( $rs->next() )
		if ( $this->row->isEmpty() && $this->rownum == 0 ) {
			return $this->loadRow();
		}

		// This is called every other time.
		$this->rownum++;
		return $this->loadRow();
	}

	/**
	 * Returns the underlying native PHP Resource
	 * 
	 * @returns php_resource
	 */
	public function getResource() {
		return $this->resource;
	}

	/**
	 * Gets the Connection
	 *
	 * @return Connection
	 */
	public function getConnection() {
		return $this->stmt->getConnection();
	}
	
	/**
	 * Sets the Fetch Mode for this ResultSet.
	 * The mode must be set when iteration hasn't started yet.
	 */
	public function setMode( $mode ) {
		if ( $this->rownum > 0 ) {
			throw new SQLException( "You can't change the ResultSet Fetch Mode in the middle of an Itereation (Current row: %d)", $this->rownum );
		}
		$this->mode = $mode;
	}

	/**
	 * Returns the current row number
	 *
	 * @return int The current row number
	 */
	public function rownum() {
		return $this->rownum;
	}

	/**
	 * Returns the ResultSetRow for the current row.
	 *
	 * @return array
	 */
	public function row() {
		return $this->row;
	}

	/**
	 * Returns the array of selected fields.
	 * 
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}
	
	/**
	 * Returns the amount of selected rows
	 *
	 * @return int selectedRows
	 */
	public function selectedRows() {
		return $this->selectedRows;
	}

	/**
	 * Returns wether this ResultSet containts the given field $name
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function has( $table, $field=null ) {
		return $this->row->has( $table, $field );
	}

	/**
	 * Gets a value by table and field name
	 *
	 * @param string $table The table name
	 * @param string $name The field name
	 * @return mixed The row field value
	 * @throws SQLException If the field doesn't exist
	 */
	public function get( $table, $field=null ) {
		if ( $this->row->isEmpty() ) {
			throw new SQLException( sprintf( 'Can\'t get field "%s.%s" - ResultSet is empty', $table, $field ) );
		} else {
			return $this->row->get( $table, $field );
		}
	}

	/**
	 * Overloaded Value Getter.
	 * 
	 * This method acts as a proxy to ResultSetRow::__get(), setting the table from this getter parameter, if needed.
	 *
	 * @see ResultSetRow::get
	 */
	public function __get( $field ) {
		switch( $this->mode ) {
			case Connection::MODE_MULTI:
				if ( $this->tabnum > 1 ) {
					return $this->row->fromTable( $field );
				} else {
					return $this->row->get( $this->tables[0], $field );
				}
				break;
			default:
				return $this->row->get( $field );
		}
	}

	/**
	 * Return the array of tables in this ResultSet
	 *
	 * @param array
	 */
	public function getTables() {
		return $this->tables;
	}

	/**
	 * A ResultSet subclass must implement this method, seeking the underlying resource identifier to $rownum position
	 *
	 * @param int $rownum The row to seek to
	 */
	public abstract function seek( $rownum );

	/**
	 * Loads a row of data into the ResultSet.
	 * This method should generally be called by next() and seek(), or any other positioning methods.
	 */
	protected abstract function loadRow();

	/**
	 * String conversion method
	 */
	public function __toString() {
		$str = sprintf( "%s [ID #%s]\n", get_class( $this ),  $this->id() );
		$str.= sprintf( " * Selected Rows: %d\n", $this->selectedRows );
		$str.= sprintf( " * Current Row #: %d\n", $this->rownum );
		$str.= sprintf( " * Tables in RS : %d\n", $this->tabnum );
		foreach( $this->tables as $table ) {
			$str.= sprintf( "  -> %s\n", $table );
		}
		if ( $this->row && !$this->row->isEmpty() ) {
			$str.= " * Fields:\n";
			foreach( $this->row->getValues() as $field => $value ) {
				$str.= sprintf( "  -> %-20s = %s\n", $field, $value );
				if ( is_array( $value ) ) {
					foreach( $value as $col => $val ) {
						$str.= sprintf( "    | => %-25s = %s\n", $col, $val );
					}
				}
			}
		}
		return $str;
	}

}
