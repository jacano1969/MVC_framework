<?php

namespace sql\mysqli;

use sql\Connection;
use sql\PreparedStatement;
use sql\ResultSetRow;
use sql\SQLException;
use sql\mysql\MySQLResultSet;

/**
 * Class MySQLiResultSet
 *
 * This class extends ResultSet providing specific methods for accessing data retrieved from a MySQL database, using mysqli driver
 */
class MySQLiResultSet extends MySQLResultSet {

	protected $values = array();
	protected $result = array();

	/**
	 * Instantiates a new MySQLResultSet Object, doing some required initialization.
	 *
	 * @param PreparedStatement $stmt The PreparedStatement that generated this RresultSet
	 * @param MySQLi_Result $resource The MySQLi_Result Object
	 */
	public function __construct( PreparedStatement $stmt, $resource ) {
		$this->stmt = $stmt;
		$this->resource = $resource;
		$this->rownum = 0;
		$this->mode = $stmt->getConnection()->getMode();
		$this->row = new ResultSetRow( $this );

		$this->selectedRows = $this->stmt->getNumRows();
		$this->fieldsTypes = array();
		$this->values = array();
		while( $col = $this->resource->fetch_field() ) {
			$table = ( $col->table ? $col->table : 0 );

			//do class resolving here...
			$this->fields[$table][] = $col->name;
			$this->values[] = &$this->result[$col->name];
			$this->fieldsTypes[ $col->name ] = $col->type; 
			if ( !in_array( $table, $this->tables ) ) $this->tables[] = $table;
		}
		$this->tabnum = sizeof( $this->tables );
		$this->stmt->bindResults( $this->values );
	}

	/**
	 * Frees the mysql result
	 */
	public function free(){
		$this->resource->free();
	}

	/**
	 * Loads a row of data into the ResultSet.
	 *
 	 * @throws SQLException In case of an error fetching the result
	 */
	protected function loadRow() {
		switch( $this->mode ) {
			case Connection::MODE_NUM:
				$row = $this->stmt->fetch();
				$values = $this->values;
				break;
			case Connection::MODE_ASSOC:
				$row = $this->stmt->fetch();
				$values = $this->result;
				break;
			case Connection::MODE_MULTI:
				$row = $this->stmt->fetch();
				$idx = 0;
				$values = array();
				foreach( $this->fields as $tab => $fields ) {
					foreach( $fields as $fld ) {
						$values[$tab][$fld] = $this->values[$idx];
						$idx++;
					}
				}
				break;
		}
		if ( $row === true ) {
			// ROW RETURNED
			$this->row->setValues( $values );
			return $this->row;

		} elseif ( $row === null ) {
			// NO MORE DATA
			$this->row->setValues( null );
			return false;

		} elseif ( $row === false ) {
			// ERROR
			throw new SQLException( $this->getConnection()->getResource()->error, $this->getConnection()->getResource()->errno );
		}
	}

	/**
	 * Seeks to $rownum row, loading it.
	 *
	 * @return boolean False if there are no selected rows or there's no data to fetch. Otherwise true.
	 * @param SQLException If seeking beyond the number of selected rows.
	 */
	public function seek( $rownum ) {
		if ( $this->selectedRows == 0 ) {
			return false;
		}
		if ( $rownum >= $this->selectedRows ) {
			throw new SQLException( sprintf( 'Error seeking data on row %d. Selected Rows: %d', $rownum, $this->selectedRows ) );
		}
		$this->stmt->seek( $rownum );

		$this->rownum = $rownum;
		$this->loadRow();
		return true;
	}


}
