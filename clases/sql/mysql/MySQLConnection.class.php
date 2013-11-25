<?php

namespace sql\mysql;

use util\Logger;
use sql\ConnectionFactory;
use sql\Connection;
use sql\SQLException;
use sql\schema\Table;
use sql\schema\Column;
use sql\schema\ForeignKey;

/**
 * Class MySQLConnection
 *
 * This class extends Connection providing the specific methods for accesing mysql databases.
 * This class is registered as a Connection Driver on ConnectionFactory by default on the standard MVC framework distribution.
 */
class MySQLConnection extends Connection {

	/**
	 * Currently selected database.
	 */
	protected $db = null;

	/**
	 * Gets the Connection Resource Identifier 
	 *
	 * @return string
	 */
	public function getResourceId() {
		return $this->resource;
	}

	/**
	 * Connects to a MySQL database
	 *
	 * @throws SQLException If there's an error connecting to the database.
	 */
	public function connect( $flags ) {
		$this->flags = $flags;
		if ( $this->flags & self::PERSISTENT ) {
			$func = 'mysql_pconnect';
		} else {
			$func = 'mysql_connect';
		}
		if ( $this->resource && !( $this->flags & self::PERSISTENT ) ) {
			mysql_close( $this->resource );
		}

        if (!$this->user) $this->user = ini_get("mysql.default_user");
        if (!$this->pass) $this->pass = ini_get("mysql.default_password");

	    $this->resource = @$func( $this->host, $this->user, $this->pass, true );

		if ( !$this->resource ) {
			$msg = sprintf( 'Error connecting to database at "%s".', $this->host );
			if ( $this->user ) {
				$msg.= sprintf( ' Username: "%s".', $this->user );
			}
			$msg.= sprintf( ' Using password? %s.', $this->pass ? 'Yes' : 'No' );
			$msg.= sprintf( ' MySQL Error: %s', mysql_error() );
			throw new SQLException( $msg );
		}
		if ( $this->path ) {
			$this->select( $this->path );
		}
	}

	public function setTimezone( $timezone ) {
		$res = mysql_query( sprintf( "set time_zone='%s'", $timezone ), $this->resource );
		if ( $res !== true ) {
			throw new SQLException( sprintf( 'Unsupported Time Zone: "%s". Have you loaded your server ZoneInfo into MySQL?', $timezone ) );
		}
	}

	/**
	 * Sets character encoding for this connection
	 *
	 * @param string $encoding
	 */
	public function setEncoding( $encoding ) {
		$res = mysql_query( sprintf( 'set names %s', $this->getMySQLEncoding( $encoding ) ), $this->resource );
		if ( $res !== true ) {
			throw new SQLException( sprintf( 'Unknown Character Set: "%s"', $encoding ) );
		}
	}

	/**
	 * Selects the MySQL Database to use
	 *
	 * @param string $db
	 * @throws SQLException If there's an error selecting the database.
	 */
	public function select( $db ) {
		$this->db = $db;
		if ( !mysql_select_db( $db, $this->resource ) ) {
			throw new SQLException( mysql_error( $this->resource ), mysql_errno( $this->resource ) );
		}
		$this->path = $db;
	}

	/**
	 * Prepares a statement, instantiating and returning the appropiate MySQLPreparedStatement object
	 *
	 * @param string $sql The Query string.
	 * @return MySQLPreparedStatement
	 */
	public function prepare( $sql ) {
		return new MySQLPreparedStatement( $this, $sql );
	}

    /** 
     * Returns a new appropiate BlobHandler for this connection
     *
     * @param $table The table name
     * @param $primaryKey The table's primary key name
     * @param $blobName The BLOB's column name
     */
    public function newBlobHandler( $table, $primaryKey, $blobName ) {
		return new MySQLBlobHandler( $this, $table, $primaryKey, $blobName );
	}


	/**
	 * Executes the provided sql query
	 *
	 * @return mixed For a SELECT statement, returns a ResultSet. Otherwise, the number of affected rows.
	 * @throws SQLException In case of an error executing the query.
	 */ 
	public function query( $sql, $params=null ) {
		$stmt = $this->prepareWithParams( $sql, $params );
		return $stmt->execute();
	}

	/**
	 * List the tables in the supplied database.
	 *
	 * @return Tables
	 */
	public function getTables( $schema=null ) {
		if ( $schema === null ) {
			$schema = $this->db;
		}
		if ( !$schema ) {
			throw new SQLException( 'Cant get database schema tables. No database supplied, and no default database selected' );
		}
		try
		{
			$stmt = $this->prepare( "select * from information_schema.tables where table_type='BASE TABLE' and  table_schema=:schema order by table_name" );
			$stmt->bind( ':schema', $schema );
			$rs = $stmt->execute();
			$tables = array();
			foreach( $rs as $row ) {
				$t = new Table( $this, $row->TABLE_SCHEMA, $row->TABLE_NAME );
				$t->setType( $row->TABLE_TYPE );
				$t->setSize( $row->DATA_LENGTH );
				$t->setEncoding( $row->TABLE_COLLATION );
				$tables[] = $t;
			}

			return $tables;
		}catch (Exception $e ){
			throw new SQLException( 'Cant get database schema tables. No database supplied, and no default database selected' );
		}

	}

	/**
	 * Gets a single Table for the supplied database/table name
	 *
	 * @return Table
 	 */
	public function getTable( $schema, $table ) {
		$stmt = $this->prepare( "select * from information_schema.tables where table_schema=:schema and table_name=:table" );
		$stmt->bind( ':schema', $schema  );
		$stmt->bind( ':table', $table );
		$rs = $stmt->execute();
		if ( $row = $rs->next() ) {
			$t = new Table( $this, $row->TABLE_SCHEMA, $row->TABLE_NAME );
			$t->setType( $row->TABLE_TYPE );
			$t->setSize( $row->DATA_LENGTH );
			$t->setEncoding( $row->TABLE_COLLATION );
			return $t;
		} else {
			return null;
		}

	}


	/**
	 * List the columns in the supplied table.
	 *
	 * @return array(Column)
 	 */
	public function getColumns( $schema, $table ) {
		$stmt = $this->prepare( "select cols.TABLE_SCHEMA, cols.TABLE_NAME, cols.COLUMN_NAME, cols.COLUMN_TYPE, cols.IS_NULLABLE, cols.COLUMN_KEY, cols.EXTRA
			, fks.REFERENCED_TABLE_SCHEMA, fks.CONSTRAINT_NAME, fks.REFERENCED_TABLE_SCHEMA, fks.REFERENCED_TABLE_NAME, fks.REFERENCED_COLUMN_NAME
			from information_schema.columns cols
			left join information_schema.key_column_usage fks on ( cols.table_schema = fks.table_schema and cols.table_name = fks.table_name and cols.column_name = fks.column_name )
			where cols.table_schema=:schema
			and cols.table_name=:table
			order by cols.ordinal_position" );
		$stmt->bind( ':schema', $schema  );
		$stmt->bind( ':table', $table );
		$rs = $stmt->execute();
		$cols = array();
		foreach( $rs as $row ) {
			$c = new Column( $this, $row->TABLE_SCHEMA, $row->TABLE_NAME, $row->COLUMN_NAME );
			$c->setType( $row->COLUMN_TYPE );
			$c->setNull( $row->IS_NULLABLE == 'YES' );
			$c->setPrimaryKey( $row->COLUMN_KEY == 'PRI' );
			$c->setExtra( $row->EXTRA );
			if ( $row->REFERENCED_TABLE_SCHEMA ) {
				$c->setForeignKey( new ForeignKey( $this, $row->CONSTRAINT_NAME, $row->REFERENCED_TABLE_SCHEMA, $row->REFERENCED_TABLE_NAME, $row->REFERENCED_COLUMN_NAME ) );
			}
			$cols[] = $c;
		}

		return $cols;

	}

	/**
	 * Lists the sequences in the supplied table
	 *
	 * @param string $schema
	 * @param string $table
	 * @return array(Sequence)
	 */
	public function getSequences( $schema, $table ) {
	}

	/**
	 * List the indices in the supplied table.
	 *
	 * @param string $schema
	 * @param string $table
	 * @return array(Column)
 	 */
	public function getIndices( $schema, $table ) {
	}

	/**
	 * MySQL doesn't support sequences. Returns false.
	 *
	 * @return boolean FALSE
	 */
	public function hasSequences() {
		return false;
	}

	/**
	 * MySQL support auto incremented columns. Returns true.
	 *
	 * @return boolean TRUE
	 */
	public function hasLastInsertID() {
		return true;
	}

	/**
	 * MySQL doesn't suppport sequences, so this method always returns null
	 * 
	 * In order to get the last_insert_id() value (corresponding to auto_increment columns) use $stmt->getLastInsertID();
	 * 
	 * @param string $seq
	 * @return null
	 */
	public function getSequence( $seq ) {
		return null;
	}

	/**
	 * Translates encodings to mysql encodings
	 *
	 * @param string $encoding
	 * @return string
	 */
	protected function getMySQLEncoding( $encoding ) {
		switch( $encoding ) {
			case 'ISO-8859-1':
				return 'latin1';
			default:
				return $encoding;
		}
	}
}
