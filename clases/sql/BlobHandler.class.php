<?php

namespace sql;

use core\Object;

/**
 * Class BlobHandler
 * 
 * This class is meant to be extended by database-specific subclasses (MySQLResultSet, OCIResultSet, etc)
 *
 * It's a BlobHandler which will serve for updating big files on database 
 */
abstract class BlobHandler extends Object {

	/**
	 * The Connection this ResultSet originated from
	 */
	protected $connection = null;

	/**
	 * Table name
	 */
	protected $table = null;
	/**
	 * Table name
	 */
	protected $db = null;

	/**
	 * Primary key
	 */
	protected $primaryKey = null;

	/**
	 * Column name of blob
	 */
	protected $blobName = null;

	/**
	 * Instantiates a new BlobHandler object with the given Connection, PHP Resource Identifier
	 *
	 * @param Connection $connection The Connection object 
	 * @param $table The table name
	 * @param $primaryKey The table's primary key name
	 * @param $blobName The BLOB's column name
	 */
	public function __construct( Connection $connection, $db,$table, $primaryKey, $blobName ) {
		$this->connection = $connection;
		$this->table = $table;
		$this->db = $db;
		$this->blobName = $blobName;
		$this->primaryKey = $primaryKey;
	}

	public function getConnection() {
		return $this->connection;
	}

	public function getBlobName() {
		return  $this->blobName;
	}

	public function getPrimaryKey() {
		return  $this->primaryKey;
	}

	public function getTable() {
		return  $this->table;
	}
	public function getDB() {
		return  $this->db;
	}

	/***
	 * Updates the given record on the database with the blobdata
	 *
	 * @param $blobData The binary data
	 * @param $keyValue The key value to update
	 *
	 * @return bytes written
	 */
	public abstract function updateOn( $blobData, $keyValue );

}
