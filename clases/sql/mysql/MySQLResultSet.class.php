<?php
namespace sql\mysql;

use sql\Connection;
use sql\PreparedStatement;
use sql\ResultSet;
use sql\SQLException;

/**
 * Class MySQLResultSet
 *
 * This class extends ResultSet providing specific methods for accessing data retrieved from a MySQL database.
 */
class MySQLResultSet extends ResultSet {

	protected $fieldsTypes;
	
	/**
	 * Instantiates a new MySQLResultSet Object, doing some required initialization.
	 */
	public function __construct( PreparedStatement $stmt, $resource ) {
		parent::__construct( $stmt, $resource );
		$this->selectedRows = mysql_num_rows( $this->resource );
		$this->fieldsTypes = array();
		while( $col = mysql_fetch_field( $this->resource ) ) {
			$table = ( $col->table ? $col->table : 0 );

			//do class resolving here...
			$this->fields[$table][] = $col->name;
			$this->fieldsTypes[ $col->name ] = $col->type == 'blob' && $col->max_length > 0 ? 'string' : $col->type; 
			if ( !in_array( $table, $this->tables ) ) $this->tables[] = $table;
		}
		$this->tabnum = sizeof( $this->tables );
	}

	/** COMMENTED DUE TO UNEXPECTED SEGMENTATION FAULT BUGS
	public function XXXXX__destruct(){
		$this->free();
		unset($this);
	}
	***/

	public function free(){
		@mysql_free_result($this->resource);
	}

	/**
	 * Returns the type of field by name or index
	 *
	 * @return string
	 */
	public function getFieldType( $name ) {
		return $this->fieldsTypes[ $name ];
	}

	/**
	 * Loads a row of data into the ResultSet.
	 *
 	 * @throws SQLException In case of an error fetching the result
	 */
	protected function loadRow() {
		switch( $this->mode ) {
			case Connection::MODE_NUM:
				$values = mysql_fetch_array( $this->resource, MYSQL_NUM );
				break;
			case Connection::MODE_ASSOC:
				$values = mysql_fetch_array( $this->resource, MYSQL_ASSOC );
				break;
			case Connection::MODE_MULTI:
				$row = mysql_fetch_array( $this->resource, MYSQL_NUM );
				if ( !$row ) {
					$values = null;
					break;
				}
				$idx = 0;
				$values = array();
				foreach( $this->fields as $tab => $fields ) {
					foreach( $fields as $fld ) {
						$values[$tab][$fld] = $row[$idx];
						$idx++;
					}
				}
				break;
		}
		if ( !$values ) {
			$this->row->setValues( null );
			if ( ( $mysql_errno = mysql_errno( $this->getConnection()->getResource() ) ) > 0 ) {
				throw new SQLException( mysql_error( $this->getConnection()->getResource() ), $mysql_errno );
			} else {
				return false;
			}
		}
		$this->row->setValues( $values );
		return $this->row;
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
		if ( !mysql_data_seek( $this->resource, $rownum ) ) {
			return false;
		}

		$this->rownum = $rownum;
		$this->loadRow();
		return true;
	}
}
