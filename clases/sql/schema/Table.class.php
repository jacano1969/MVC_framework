<?php
namespace sql\schema;

use core\Object;
use sql\Connection;

/**
 * Class Table represents a database schema Table.
 *
 * This is useful for navigating database metadata, in a driver-independent way. Each driver must implement the methods used by the schema descriptors.
 */
class Table extends Object {

	/**
	 * The Connection object for this table.
	 */
	protected $conn = null;

	/**
	 * The database schema this table belongs to
	 */
	protected $schema = null;

	/**
	 * The table name
	 */
	protected $name = null;

	/**
	 * Table type (table, view)
	 */
	protected $type = null;

	/**
	 * Table size
	 */
	protected $size = 0;

	/**
	 * Table encoding
	 */
	protected $encoding = null;

	/**
	 * Table columns
	 */
	protected $columns = null;

	/**
	 * Table indices
	 */
	protected $indices = null;

	/**
	 * Table sequences
	 */
	protected $sequences = null;

	/**
	 * Instantiates a new Table object for the supplied schema and table name, as retrieved from Connection $conn
	 *
	 * @param Connection $conn
	 * @param string $schema
	 * @param string $name
	 */
	public function __construct( Connection $conn, $schema, $name ) {
		$this->conn = $conn;
		$this->schema = $schema;
		$this->name = $name;
	}

	public function getConnection() {
		return $this->conn;
	}

	public function getSchema() {
		return $this->schema;
	}

	public function getName() {
		return $this->name;
	}

	public function getType() {
		return $this->type;
	}

	public function setType( $type ) {
		$this->type = $type;
	}

	public function getSize() {
		return $this->size;
	}

	public function setSize( $size ) {
		$this->size = $size;
	}

	public function getRecordCount() {
		return $this->conn->getValue( sprintf( 'select count(*) from %s.%s', $this->schema, $this->name ) );
	}

	public function getEncoding() {
		return $this->encoding;
	}

	public function setEncoding( $encoding ) {
		$this->encoding = $encoding;
	}

	public function getColumns() {
		if ( $this->columns === null ) {
			$this->columns = $this->conn->getColumns( $this->schema, $this->name );
		}
		return $this->columns;
	}

	public function addColumn( Column $column ) {
		if ( $this->columns === null ) {
			$this->columns = array();
		}
		$this->columns[] = $column;
	}

	public function getIndices() {
		return $this->indices;
	}

	public function addIndex( Index $index ) {
		if ( $this->indices === null ) {
			$this->indices = array();
		}
		$this->indices[] = $index;
	}

	public function getPrimaryKeys() {
		$pks = array();
		foreach( $this->getColumns() as $col ) {
			if ( $col->isPrimaryKey() ) $pks[] = $col;
		}
		return $pks;
	}

	public function getForeignKeys() {
		$fks = array();
		foreach( $this->getColumns() as $col ) {
			if ( $fk = $col->getForeignKey() ) {
				$name = $fk->getConstraint();
				$fks[$name][] = array( 'local' => $col, 'foreign' => $col->getForeignKey() );
			}
		}
		return $fks;
	}

	public function getSequences() {
		$seqs = array();
		foreach( $this->getColumns() as $col ) {
			if ( $col->getExtra() == 'auto_increment' ) {
				$seqs['auto_increment'] = $col;
			}
		}
		return $seqs;
	}

}
