<?php

namespace core\session;

use core\Object;
use util\Date;
use sql\ConnectionFactory;
use mvc\Exception;

/**
 * Implements a simple File Session Handling mechanism, similar to PHP's native one.
 * Use as basis for your own SessionHandler
 */
class DBSessionHandler extends Object implements SessionHandler {

	/**
	 * Session save path
	 */
	private $path = null;

	/**
	 * Session save name
	 */
	private $name = null;

	/**
	 * Connection ID to use
	 */
	private $connId = null;

	/**
	 * Connection URL to use
	 */
	private $connUrl = null;

	/**
	 * Connection Handler
	 */
	private $conn = null;

	/**
	 * Session table.
	 *
	 * Default: sessions
	 */
	private $table = 'sessions';

	/**
	 * Session column id.
	 *
	 * Default: id
	 */
	private $colId = 'id';

	/**
	 * Session column data.
	 *
	 * Default: data
	 */
	private $colData = 'data';

	/**
	 * Session column time.
	 *
	 * Default: time
	 */
	private $colTime = 'time';

	/**
	 * SQL Statement used for reading session data
	 */
	private $rStmt = null;

	/**
	 * SQL Statement used for checking session exists.
	 *
	 */
	private $cStmt = null;

	/**
	 * SQL Statement used for updating session data.
	 */
	private $uStmt = null;

	/**
	 * SQL Statement used for inserting session data.
	 */
	private $iStmt = null;

	/**
	 * SQL Statement used for deleting session data.
	 */
	private $dStmt = null;

	/**
	 * SQL Statement used by the garbage collector.
	 */
	private $gcStmt = null;

	/**
	 * Opens the connection to the database and sets up the SQL Statements for handling session data.
	 *
	 * @throws SessionException If the connection can't be opened.
	 */
	private function openConnection() {
		if ( $this->connId === null && $this->connUrl === null ) {
			throw new SessionException( sprintf( "%s: Either a 'conn-id' or 'conn-url' Property is required for this Session Handler. Please check configuration", get_class() ) );
		}
		try {
			if ( $this->connId ) {
				$this->conn = ConnectionFactory::getConnection( $this->connId );
			} else {
				$this->conn = ConnectionFactory::newConnection( 'SESSION', $this->connUrl );
			}
			$this->rStmt = $this->conn->prepare( sprintf( "select * from %s where %s=:id", $this->table, $this->colId ) );
			$this->cStmt = $this->conn->prepare( sprintf( "select count(*) as count from %s where %s=:id", $this->table, $this->colId ) );
			$this->uStmt = $this->conn->prepare( sprintf( "update %s set %s=:time, %s=:data where %s=:id", $this->table, $this->colTime, $this->colData, $this->colId ) );
			$this->iStmt = $this->conn->prepare( sprintf( "insert into %s ( %s, %s, %s ) values ( :id, :data, :time )", $this->table, $this->colId, $this->colData, $this->colTime ) );
			$this->dStmt = $this->conn->prepare( sprintf( "delete from %s where %s=:id", $this->table, $this->colId ) );
            $this->aStmt = $this->conn->prepare( sprintf( "select * from %s where %s>=:time", $this->table, $this->colTime ) );
            $this->gcStmt = $this->conn->prepare( sprintf( "delete from %s where %s<:time", $this->table, $this->colTime ) );
		} catch ( Exception $e ) {
			throw new Exception( sprintf( "%s: Error setting up Session Connection: %s", get_class(), $e->getMessage() ) );
		}
	}

	/**
	 * Opens the Session
	 *
	 * @param string $path
	 * @param string $name
	 */
	public function open( $path, $name ) {
		$this->openConnection();
		$this->path = $path;
		$this->name = $name;
		return true;
	}

	/**
	 * Closes the Session
	 */
	public function close() {
		static::gc(0);
		return true;
	}

	/**
	 * Reads the Session data
	 *
	 * @param string $id
	 */
	public function read( $id ) {
		try {
			$this->rStmt->bind( ':id', $id );
			$rs = $this->rStmt->execute();
			if ( $rs->next() ) {
				return $rs->get( $this->colData );
			} else {
				return false;
			}
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	/**
	 * Writes the Session data
	 *
	 * @param string $id
	 * @param string $data
	 */
	public function write( $id, $data ) {
		if ( $this->conn ) {
			try {
				$this->cStmt->bind( ':id', $id );
				$rs = $this->cStmt->execute();
				$rs->next();
				if ( $rs->count ) {
					$stmt = $this->uStmt;
				} else {
					$stmt = $this->iStmt;
				}
				$now = new Date();
				$stmt->bind( ':id', $id );
				$stmt->bind( ':data', $data );
				$stmt->bind( ':time', $now->toSqlFull() );
				$stmt->execute();
			} catch ( Exception $e ) {
				throw new Exception( $e->getMessage() );
			}
		}
	}

	/**
	 * Destroy the Session data
	 *
	 * @param string $id
	 */
	public function destroy( $id ) {
		try {
			$this->dStmt->bind( ':id', $id );
			$this->dStmt->execute();
		} catch ( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}
	}

	/**
	 * Garbage Collector.
	 *
	 * @param int $maxLifeTime
	 */
	public function gc( $maxLifeTime ) {
        if ( $maxLifeTime == 0 ) $maxLifeTime = 60 * 60 * 5;
        try {
            $t = time() - $maxLifeTime;
            $d = new Date($t);
			if ( isset( $this->gcStmt ) ) {
				$this->gcStmt->bind( ':time', $d->toSqlFull() );
				$rs = $this->gcStmt->execute();
			}
        } catch ( Exception $e ) {
            throw new Exception( $e->getMessage() );
        }
	}

	/**
	 * Return the session alive IDs, accordin to maxLifeTime
	 *
	 * @param int $maxLifeTime
	 * @return array
	 */
    public function getAliveIDs( $maxLifeTime ) {
        try {
            $t = time() - $maxLifeTime;
            $d = new Date($t);
            $this->aStmt->bind( ':time', $d->toSqlFull() );
            $rs = $this->aStmt->execute();
			$ret = array();
			while( $rs->next() ) {
				$ret[] = $rs->get( $this->colId );
			}
            return $ret;
        } catch ( Exception $e ) {
            throw new Exception( $e->getMessage() );
        }
    }

	/**
	 * Sets a property for DBSessionHandler.
	 * Supported properties:
	 * - conn-id:  Connection ID to use. Must be added as ConnectionFactory Connection with than name
	 * - conn-url: Connection URL. Connection URL to use, will instantiate a new Connection specific to this Session Handler
	 * - table: Database table to use
	 * - colId: Column name for session id
	 * - colData: Column name for session data
	 * - colTime: Column name for session timestamp
	 */
	public function setProperty( $name, $value ) {
		switch( $name ) {
			case 'conn-id':
				$this->connId = $value;
				break;
			case 'conn-url':
				$this->connUrl = $value;
				break;
			case 'table':
				$this->table = $value;
				break;
			case 'col-id':
				$this->colId = $value;
				break;
			case 'col-data':
				$this->colData = $value;
				break;
			case 'col-time':
				$this->colTime = $value;
				break;
			default:
				throw new Exception( sprintf( 'Unsupported SessionHandler property "%s" for %s', $name, get_class() ) );
		}
	}

}
