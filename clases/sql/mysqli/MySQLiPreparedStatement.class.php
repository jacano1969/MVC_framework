<?php

namespace sql\mysqli;

use util\Logger;
use sql\Connection;
use sql\ConnectionFactory;
use sql\PreparedStatement;
use sql\SQLException;
use util\Hashtable;
use sql\mysql\MySQLPreparedStatement

/**
 * MySQL Database specific PreparedStatement class, with the mysqli driver.
 */
class MySQLiPreparedStatement extends MySQLPreparedStatement {

	protected $stmt = null;

	/**
	 * Instantiates a new MySQLiPreparedStatement object with the give Connection and SQL string.
	 *
	 * @param Connection $conn
	 * @param string $sql
	 */
	public function __construct( Connection $conn, $sql ) {
		$this->conn = $conn;
		$this->sql = $sql;
		$this->stmt = new \mysqli_stmt( $conn->getResource(), $sql );
	}

	/**
	 * Sets a parameter by position.
	 * 
	 * @param string $value The value to set
	 */
	public function param( $type, $value ) {
		$this->params[] = array( $type, $value );
	}

	/**
	 * Sets an array of positional parameters
	 *
	 * @param array $params
	 */
	public function setParams( array $params ) {
		$this->params = $params;
	}

	/**
	 * Gets the query string ready to execute in the database.
	 * 
	 * In MySQLi, positional parameters are bound using native mysqli methods. Named parameters are replaced.
	 *
	 * @return string The query string ready to be executed
	 */
	public function getQuery() {
		if ( sizeof( $this->binds ) > 0 && sizeof( $this->params ) > 0 ) {
			throw new SQLException( "PreparedStatement error. You can't use both named and positional parameters in the same query" );
		}
		if ( sizeof( $this->binds ) > 0 ) {
			//return preg_replace_callback( "/[^'.*]:\w+[^.*']/", array( &$this, 'replaceBindParameter' ), $this->sql );
			return preg_replace_callback( "/:\w+/", array( &$this, 'replaceBindParameter' ), $this->sql );
		} elseif ( sizeof( $this->params ) > 0 ) {
			foreach( $this->params as $param ) {
				list( $type, $value ) = $param;
				$this->stmt->bind_param( $this->getMySQLiParamType( $type ), $value );
			}
			return $this->sql;
		} else {
			return $this->sql;
		}
	}

	/**
	 * Executes the prepared statement with its parameters bound.
	 *
	 * In MySQL, the parameter binding is simply done by calling getQuery, as there's no internal methods available.
	 *
	 * @return mixed For a SELECT statement, returns a ResultSet. Otherwise, the number of affected rows.
	 * @throws SQLException In case of an error executing the query.
	 */ 
	public function execute() {
		$query = $this->getLimitedQuery();

		$time1 = microtime(true);

		// Executes the query
		$res = $this->stmt->execute();
		$this->stmt->store_result();

		$this->conn->queryDone( $query, $time1 );

		if ( $res === false ) {
			// ERROR
			throw new SQLException( $this->conn->getResource()->error, $this->conn->getResource()->errno, $query );
		}

		$res = $this->stmt->result_metadata();
		if ( $res === false ) {
			// Insert, Update, Delete
			return $this->stmt->affected_rows;
		} else {
			return new MySQLiResultSet( $this, $res );
		}
	}

	/**
	 * Performs a fetch operation on this statement result, as per mysqli native implementation
	 */
	public function fetch() {
		return $this->stmt->fetch();
	}

	/**
	 * Binds values to results array
	 *
	 * @param array $results
	 */
	public function bindResults( array &$results ) {
		call_user_func_array( array( $this->stmt, 'bind_result' ), $results );
	}

	/**
	 * Gets the number of rows fetched from this statement
	 *
	 * @return int
	 */
	public function getNumRows() {
		return $this->stmt->num_rows;
	}

	/**
	 * Seeks data to the supplied row number
	 *
	 * @param int $rownum
	 */
	public function seek( $rownum ) {
		return $this->stmt->data_seek( $rownum );
	}

	/**
	 * Gets the last insert id for this statement
	 */
	public function getLastInsertID() {
		return $this->stmt->insert_id;
	}

	protected function getMySQLiParamType( $type ) {
		switch( $type ) {
			case self::INT: return 'i';
			case self::DBL: return 'd';
			case self::STR: return 's';
			case self::BLOB: return 'b';
		}
	}

}
