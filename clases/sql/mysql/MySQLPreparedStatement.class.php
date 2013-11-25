<?php

namespace sql\mysql;

use util\Logger;
use sql\Connection;
use sql\ConnectionFactory;
use sql\PreparedStatement;
use sql\SQLException;
use util\Hashtable;

/**
 * MySQL Database specific PreparedStatement class.
 */
class MySQLPreparedStatement extends PreparedStatement {

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
	 * Binds a parameter by name.
	 * In MySQL, binding parameters by name simply replaces them in the query string, storing the parameters internally.
	 * 
	 * @param string $name The parameter to bind. It has to start with ':'
	 * @param string $value The value to bind.
	 */
	public function bind( $name, $value ) {
		if ( !$name or $name[0] != ':' ) {
			throw new SQLException( 'Bind param name can\'t be null or empty or not beginning with ":"' );
		}
		$this->binds[$name] = $value;
		
	}

	/**
	 * Binds an array of name => value parameters
	 *
	 * @param array $binds An associative array of parameters names and values.
	 */
	public function setBinds( $binds ) {
		if ( !is_array( $binds ) && !($binds instanceof Hashtable) ) {
			throw new SQLException( 'You must provide an array or hashtable for setBinds().' );
		}
		$this->binds = $binds;
	}

	/**
	 * Adds an array of name => value parameters
	 *
	 * @param array $binds An associative array of parameters names and values.
	 */
	public function addBinds( $binds ) {
		if ( !is_array( $binds ) && !($binds instanceof Hashtable) ) {
			throw new SQLException( 'You must provide an array or hashtable for addBinds().' );
		}
		foreach( $binds as $name => $value ) {
			$this->binds[$name] = $value;
		}
	}

	public function getLimitedQuery() {
		$sql = $this->getQuery();
		if ( $this->limited ) {
			$sql.= sprintf( ' limit %s', $this->limitFrom );
			// Add upper range?
			if ( $this->limitTo ) {
				$sql.= sprintf( ', %s', $this->limitTo );
			}
		}
		return $sql;
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
		$res = @mysql_query( $query, $this->conn->getResource() );

		$this->conn->queryDone( $query, $time1 );

		if ( $res === false ) {
			throw new SQLException( mysql_error( $this->conn->getResource() ), mysql_errno( $this->conn->getResource() ), $query );
		}
		
		// Select, Show, Describe, Explain
		if ( is_resource( $res ) ) {
			return new MySQLResultSet( $this, $res );
		// Insert, Update, Delete
		} else {
			return mysql_affected_rows( $this->conn->getResource() );
		}
	}

	/**
	 * Returns the last insert id (auto_increment column value)
	 * Note that for the ext/mysql driver, the last insert id is bound to the connection, not the statement.
	 * So even if this method has to be implemented here, be careful when preparing/executing multiple statements, as
	 * the insert id retrieved will be the same for all.
	 *
	 * @return int LAST_INSERT_ID()
	 */
	public function getLastInsertID() {
		return mysql_insert_id( $this->conn->getResource() );
	}

	/**
	 * Gets the query string ready to execute in the database.
	 * 
	 * In MySQL, both positional and named parameters are simply replaced in the original query string.
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
			return $this->replacePosParameters();
		} else {
			return $this->sql;
		}
	}

	/**
	 * Callback Function for a Single parameter
	 *
	 * @param  string Single parameter
	 * @return string The parameter replaced, ready to be injected in the SQL code.
	 * 
	 **/
	protected function bindSingleParam($value)
	{
		switch( gettype( $value ) ) {
			case 'integer':
			case 'double':
				return $value;
			default:
				return sprintf( "'%s'",  mysql_real_escape_string( $value,$this->conn->getResource()) );
		}
	}

	/**
	 * Callback function used by getQuery to replace bind parameters.
	 *
	 * @param array $matches An array of matches, as supplied by preg_replace_callback
	 * @throws SQLException If the parameter is not boundable (hasn't been supplied with bind())
	 * @return string The parameter replaced, ready to be injected in the SQL code.
	 */
	protected function replaceBindParameter( $matches ) {
		$bind = $matches[0];
		if ( !array_key_exists( $bind, $this->binds ) ) {
			throw new SQLException( sprintf( 'Unbound Parameter: "%s"', $bind ) );
		}
		if ( $this->binds[$bind] === null ) return 'null';
		else if(is_array($this->binds[$bind])) return implode(',',array_map(array(&$this,'bindSingleParam'),$this->binds[$bind]));
		else return $this->bindSingleParam($this->binds[$bind]);
	}

	/**
	 * Replaces positional parameters in the query with values from the $this->params array
	 *
	 * @return string
	 */
	protected function &replacePosParameters() {
		$sql = $this->sql;
		$in = false;
		$idx = 0;
		for( $c=0; $c<strlen($sql); $c++ ) {
			if ( $sql[$c] == "'" && $sql[$c-1] != "\'" ) $in = !$in;
			elseif ( $sql[$c] == "?" && !$in ) {
				list( $type, $param ) = $this->params[$idx];
				$len = strlen( $param );
				if ( $type == self::INT || $type == self::DBL ) {
					$replace = "%s%s%s";
				} else {
					$replace = "%s'%s'%s";
				}
				$sql = sprintf( $replace, substr( $sql, 0, $c ), mysql_real_escape_string( $param, $this->conn->getResource() ), substr( $sql, $c+1 ) );
				$c+=$len+1;
				$idx++;
			}
		}
		return $sql;
	}

}
