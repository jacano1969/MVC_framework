<?php

namespace sql;

use core\Object;

/**
 * This abstract class should be extended by database-specific PreparedStatement classes.
 */
abstract class PreparedStatement extends Object {

	const INT = 'INT';
	const DBL = 'DBL';
	const STR = 'STR';
	const BLOG = 'BLOB';

	/**
	 * The connection object that originated this PreparedStatement
	 */
	protected $conn = null;

	/**
	 * The prepared sql statement
	 */
	protected $sql = null;
	
	/**
	 * Array of positional parameters
	 */
	protected $params = array();

	/**
	 * Array of named parameters
	 */
	protected $binds = array();

	/**
	 * Wether the query is limited to a number of results
	 */
	protected $limited = false; 

	/**
	 * Lower range to limit the query to
	 */
	protected $limitFrom = null;

	/**
	 * Upper range to limit the query to
	 */
	protected $limitTo = null;

	/**
	 * Instantiates a new PreparedStatement object with the give Connection and SQL string.
	 *
	 * @param Connection $conn 
	 * @param string $sql
	 */
	public function __construct( Connection $conn, $sql ) {
		$this->conn = $conn;
		$this->sql = $sql;
	}

	/**
	 * Sets a parameter by position.
	 * 
	 * @param string $value The value to set
	 */
	abstract public function param( $type, $value );

	/**
	 * Sets an array of positional parameters
	 *
	 * @param array $params
	 */
	abstract public function setParams( array $params );

	/**
	 * Binds a parameter by name
	 *
	 * @param string $name The parameter to bind. It has to start with ':'
	 * @param string $value The value to bind.
	 */
	abstract public function bind( $name, $value );

	/**
	 * Binds an array of name => value parameters
	 *
	 * @param array $binds An associative array of parameters names and values.
	 */
	abstract public function setBinds( $binds );

	/**
	 * Adds an array of name => value parameters
	 * This method doesn't replace current binds, adding to them instead.
	 *
	 * @param array $binds An associative array of parameters names and values.
	 */
	abstract public function addBinds( $binds );

	/**
	 * Gets the Connection object for this PreparedStatement
	 *
	 * @return Connection
	 */
	public function getConnection() {
		return $this->conn;
	}

	/**
	 * Gets the list of positional parameters
	 *
	 * @return array The parameters
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * Gets the list of binds
	 *
	 * @return array The binds
	 */
	public function getBinds() {
		return $this->binds;
	}

	/**
	 * Limit the query results to this amount
	 * If both values are given, then a range is used to limit the query (i.e: 5-10)
	 * If only the first value is given, then the query is limited to that many results (i.e: 10)
	 *
	 * @param int $from
	 * @param int $to
	 */
	public function setLimit( $from, $to=null ) {
		$this->limited = true;
		$this->limitFrom = $from;
		$this->limitTo = $to;
	}

	/**
	 * Adds limiting sql code for the current prepared sql.
	 * This must be implemented by a database-specific driver, as limit syntax varies.
	 */
	abstract public function getLimitedQuery();

	/**
	 * Executes the prepared statement with its parameters replaced.
	 *
	 * @return mixed Depending on the type of statement executed.
	 * @throws SQLException In case of an error executing the query.
	 */
	abstract public function execute();

	/**
	 * Gets the last insert id value for this statement / connection
	 *
	 * @return int
	 */
	public abstract function getLastInsertID();

	/**
	 * Gets the prepared statement (without parameters)
	 *
	 * @return string
	 */
	public function getSql() {
		return $this->sql;
	}

	/**
	 * Gets the query string ready to execute in the database.
	 * 
	 * Implement this method on each driver's Connection subclass
	 *
	 * @return string The query string as expected to be executed, with parameters bound/replaced
	 */
	abstract public function getQuery();

	/**
	 * Executes the prepared statement, returning the single column value as result (for example, count(*) statements)
	 *
	 * For an insert, update or delete statement, this method will return the number of affected rows
	 * 
	 * @return mixed
	 */
	public function getValue() {
		$rs = $this->execute();

		if ( $rs instanceOf ResultSet ) {
			if ( $rs->selectedRows() > 1 ) {
				throw new SQLException( sprintf( "More than one row fetched (%d). Use query() instead\n", $rs->selectedRows() ) );
			} elseif ( $rs->selectedRows() == 1 ) {
				$rs->setMode( Connection::MODE_NUM );
				if ( sizeof( $rs->getFields() ) > 1 ) {
					throw new SQLException( sprintf( "More than one field requested (%d). Use query() instead\n", sizeof( $row ) ) );
				}
				$rs->next();
				return $rs->get( 0 );
			} else {
				// No results
				return null;
			}
		} else {
			return $rs;
		}
	}

	/**
	 * Executes the provided single-field, multi-record prepared statement, returning
	 * the results as a list of values separated by comma.
	 *
	 * @return string The list of values
	 * @throws SQLException If more than one value is requested.
	 */
	public function getList() {
		$rs = $this->execute();
		if ( !$rs instanceOf ResultSet ) {
			throw new SQLException( 'Could not get list. Query did not return a ResultSet' );
		}
		if ( $rs->selectedRows() == 0 )  {
			return -1;
		} else {
			$values = array();
			$rs->setMode( Connection::MODE_NUM );
			while( $rs->next() ) {
				$values[] = $rs->get(0);
			}
			return join( ',', $values );
		}
	}

	/**
	 * Returns a string representation of this Prepared Statement
	 */
	public function __toString() {
		try {
			$str = '';
			$str.= sprintf( "%s [ID #%s]:\n", get_class( $this ), $this->id() );
			$str.= sprintf( "- Query: \"%s\"\n", $this->getLimitedQuery() );
			return $str;
		} catch ( SQLException $e ) {
			return $e->getMessage();
		}
	}

}
