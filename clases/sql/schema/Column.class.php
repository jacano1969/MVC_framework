<?php

namespace sql\schema;

use core\Object;
use sql\Connection;

/**
 * Class Column represents a database schema Column.
 *
 * This is useful for navigating database metadata, in a driver-independent way. Each driver must implement the methods used by the schema descriptors.
 */
class Column extends Object {

	/**
	 * The Connection object for this column.
	 */
	protected $conn = null;

	/**
	 * The database schema the table this column belongs to
	 */
	protected $schema = null;

	/**
	 * The table this column belongs to
	 */
	protected $table = null;

	/**
	 * Column name
	 */
	protected $name = null;

	/**
	 * Column type (driver specific)
	 */
	protected $type = null;

	/**
	 * Whether this column can be null
	 */
	protected $null = null;

	/**
	 * Whether this column is part of its table primary key
	 */
	protected $pk = false;

	/**
	 * Foreign Column referenced by this column
	 */
	protected $fk = null;

	/**
	 * Extra information for this column. Any database driver can set this to whatever arbitrary information is useful for its definition.
	 */
	protected $extra = null;

	/**
	 * Instantiates a new Column object for the supplied database/schema, table and column name, as retrieved from Connection $conn
	 *
	 * @param Connection $conn
	 * @param string $schema
	 * @param string $table
	 * @param string $name
	 */
	public function __construct( Connection $conn, $schema, $table, $name ) {
		$this->conn = $conn;
		$this->schema = $schema;
		$this->table = $table;
		$this->name = $name;
	}

	public function getSchema() {
		return $this->schema;
	}

	public function getTable() {
		return $this->table;
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

	public function getNull() {
		return $this->null;
	}

	public function setNull( $null ) {
		$this->null = $null;
	}

	public function setExtra( $extra ) {
		$this->extra = $extra;
	}

	public function getExtra() {
		return $this->extra;
	}

	public function setPrimaryKey( $pk ) {
		$this->pk = $pk;
	}

	public function isPrimaryKey() {
		return $this->pk;
	}

	public function setForeignKey( Column $fk ) {
		$this->fk = $fk;
	}

	public function getForeignKey() {
		return $this->fk;
	}

}
