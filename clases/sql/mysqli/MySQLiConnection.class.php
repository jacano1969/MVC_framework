<?php

namespace sql\mysqli;

use util\Logger;
use sql\ConnectionFactory;
use sql\Connection;
use sql\SQLException;
use sql\schema\Table;
use sql\schema\Column;
use sql\schema\ForeignKey;

/**
 * Class MySQLiConnection
 *
 * This class extends Connection providing the specific methods for accesing mysql databases using PHP mysqli driver.
 * This class is registered as a Connection Driver on ConnectionFactory by default on the standard MVC framework distribution.
 */
class MySQLiConnection extends \sql\mysql\MySQLConnection {

	/**
	 * Gets the Connection Resource Identifier
	 *
	 * @return string
	 */
	public function getResourceId() {
		return $this->resource->host_info;
	}

	/**
	 * Connects to a MySQL database
	 *
	 * @throws SQLException If there's an error connecting to the database.
	 */
	public function connect( $flags ) {
		$this->flags = $flags;
		if ( $this->flags & self::PERSISTENT ) {
			$host = sprintf( 'p:%s', $this->host );
		} else {
			$host = $this->host;
		}
		if ( $this->resource && !( $this->flags & self::PERSISTENT ) ) {
			$this->resource->close();
		}
		if ( $this->user && $this->pass ) {
			$this->resource = new \mysqli( $this->host, $this->user, $this->pass );
		} elseif ( $this->user ) {
			$this->resource = new \mysqli( $this->host, $this->user );
		} else {
			$this->resource = new \mysqli( $this->host );
		}
		if ( $this->resource->connect_error ) {
			$msg = sprintf( 'Error connecting to database at "%s".', $this->host );
			if ( $this->user ) {
				$msg.= sprintf( ' Username: "%s".', $this->user );
			}
			$msg.= sprintf( ' Using password? %s.', $this->pass ? 'Yes' : 'No' );
			$msg.= sprintf( ' MySQL Error: [%d] %s', $this->resource->connect_errno, $this->resource->connect_error );
			throw new SQLException( $msg );
		}
		if ( $this->path ) {
			$this->select( $this->path );
		}
	}

	public function setTimezone( $timezone ) {
		$res = $this->resource->query( sprintf( "set time_zone='%s'", $timezone ) );
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
		$res = $this->resource->query( sprintf( 'set names %s', $this->getMySQLEncoding( $encoding ) ) );
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
		if ( !$this->resource->select_db( $db ) ) {
			throw new SQLException( $this->resource->error, $this->resource->errno );
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
		return new MySQLiPreparedStatement( $this, $sql );
	}

    /** 
     * Returns a new appropiate BlobHandler for this connection
     *
     * @param $table The table name
     * @param $primaryKey The table's primary key name
     * @param $blobName The BLOB's column name
     */
    public function newBlobHandler( $table, $primaryKey, $blobName ) {
		return new MySQLiBlobHandler( $this, $table, $primaryKey, $blobName );
	}
}
