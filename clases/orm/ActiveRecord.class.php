<?php

namespace orm;

use core\Object;
use sql\Connection;
use sql\PreparedStatement;
use sql\ResultSetRow;
use sql\SQLException;

/**
 * Base ActiveRecord class.
 */
abstract class ActiveRecord extends Object {

	/**
	 * Flag for select binds
	 */
	const BIND_SELECT = 1;

	/**
	 * Flag for insert binds
	 */
	const BIND_INSERT = 2;

	/***
	 * Flag for update binds
	 */
	const BIND_UPDATE = 3;

	/**
	 * Flag for delete binds
	 */
	const BIND_DELETE = 4;

	/**
	 * The Connection used for reading and writing with this ActiveRecord
	 */
	protected static $conn = null;

	/**
	 * The Database schema this ActiveRecord table belongs to
	 */
	protected static $schema = null;

	/**
	 * The table this ActiveRecord wraps.
	 */
	protected static $table = null;

	/**
	 * The database fields mappings
	 */
	protected static $fields = array();

	/**
	 * The primary key fields
	 */
	protected static $pkFields = array();

	/**
	 * The sequenced fields
	 */
	protected static $sequences = array();

	/**
	 * The field values
	 */
	protected $values = array();

	/**
	 * ORM Object Cache
	 */
	protected static $cache = array();

	/**
	 * Wether this ActiveRecord is new (not hydrated from database)
	 */
	protected $new = true;

	/**
	 * Wether this ActiveRecord has been modified
	 */
	protected $modified = false;

	/**
	 * An alias for this ActiveRecord. Used for hydrating from mult-type ResultSet Objects.
	 */
	protected $alias = null;

	/**
	 * Instantiates a new ActiveRecord.
	 * If the optional parameter ResultSetRow is supplied, then the ActiveRecord is hydrated from it.
	 * The optional parameter alias allows from hydrating ActiveRecord objects from multi-table selects with aliases.
	 *
	 * @param ResultSetRow $row
	 * @param string $alias
	 */
	final public function __construct( ResultSetRow $row=null, $alias=null ) {
		$this->alias = $alias;
		if ( $row ) {
			$this->hydrate( $row );
		}
	}

	/**
	 * Gets the primary key fields of this Active Record.
	 *
	 * @return array
	 */
	public static function getPKFields() {
		return static::$pkFields;
	}

	/**
	 * Gets the ForeignKey Fields for this Active Record
	 */
	public static function getFKFields() {
		return static::$fkFields;
	}

	/**
	 * Gets the fields for this ActiveRecord
	 *
	 * @return array
	 */
	public static function getFields() {
		return static::$fields;
	}

	/**
	 * Gets the database schema field for the supplied ActiveRecord field.
	 *
	 * @param string $field
	 * @return string
	 */
	public static function getField( $field ) {
		if ( !isset( static::$fields[$field] ) ) {
			throw new ActiveRecordException( sprintf( 'Invalid ActiveRecord field: "%s" for "%s"', $field, get_called_class() ) );
		}
		return static::$fields[$field];
	}

	/**
	 * Sets the connection object used to read/write with this ActiveRecord.
	 */
	public static function setConnection( Connection $conn ) {
		static::$conn = $conn;
	}

	/**
	 * Gets the Connection object used to read/write with this ActiveRecord.
	 *
	 * @return Connection
	 */
	public static function getConection() {
		return static::$conn;
	}

	/**
	 * Gets the schema this ActiveRecord table belongs to
	 *
	 * @return string
	 */
	public static function getSchema() {
		return static::$schema;
	}

	/**
	 * Gets the table this ActiveRecord represents.
	 *
	 * @return string
	 */
	public static function getTable() {
		return static::$table;
	}

	/**
	 * Gets the database.table this ActiveRecord represents.
	 *
	 * @return string
	 */
	public static function getSchemaTable() {
		return ( static::$schema ? sprintf( '%s.%s', static::$schema, static::$table ) : static::$table );
	}

	/**
	 * Returns the field values for the provided array of fields (all fields if null)
	 *
	 * @param array $field An array of field names to get the values for
	 * @return array The associative array of field=>value pairs.
	 */
	public function getValues( array $fields=null ) {
		if ( $fields == null ) {
			return $this->values;
		}
		$values = array();
		foreach( $fields as $field ) {
			if ( array_key_exists( $field, $this->values ) ) {
				$values[$field] = $this->values[$field];
			}
		}
		return $values;
	}

	/**
	 * Overloaded field getter
	 *
	 * @param string $field The field to get.
	 * @return mixed The field value.
	 * @throws ActiveRecordException
	 */
	public function __get( $field ) {
		if ( array_key_exists( $field, static::$fkFields ) ) {
			$fkClass = static::$fkFields[$field]['foreignClass'];
			return static::getObject( static::$fkFields[$field]['foreignClass'], static::$fkFields[$field]['foreignFields'], array_values( $this->getValues( static::$fkFields[$field]['localFields'] ) ) );
		} elseif ( array_key_exists( $field, $this->values ) ) {
			return $this->values[$field];
		} else {
			throw new ActiveRecordException( sprintf( 'Invalid field: "%s -> %s"', get_class($this), $field ) );
		}
	}

	/**
	 * Overloaded field setter
	 *
	 * @param string $field The table field to set.
	 * @param mixed $value The value to set.
	 * @throws ActiveRecordException
	 */
	public function __set( $field, $value ) {
		if ( array_key_exists( $field, $this->fkValues ) ) {
			$fkClass = static::$fkFields[$field]['foreignClass'];
			if ( !is_object( $value ) || $value instanceOf $fkClass ) {
				throw new ActiveRecordException( sprintf( 'Value for field "%s" must an object of class "%s". %s given', $field, $fkClass, gettype( $value ) ) );
			}
			$this->fkValues[$field] = $value;

            foreach( static::$fkFields[$field]['foreignFields'] as $idx => $ff ) {
                $this->values[static::$fkFields[$field]['localFields'][$idx]] = $value->$ff;
            }
			$this->modified = true;
	
		} elseif ( array_key_exists( $field, $this->values ) ) {
			if ( ( $this->values[$field] === null && $value !== null ) || $value != $this->values[$field] ) {
				$this->values[$field] = $value;
				$this->modified = true;
			}
		} else {
			throw new ActiveRecordException( sprintf( 'Invalid field: "%s -> %s"', get_class($this), $field ) );
		}
	}

	/**
	 * Reverse foreign key getter.
	 *
	 * When calling an undefined method on an ActiveRecord, an ORM class with the called method name will be tried to be found
	 * and if a Foreign Key pointing to this class is found in that class, then an ActiveRecordIterator with all the referencing class records
	 * is returned.
	 *
	 * Example: Table users.group_id references groups.group_id
	 *
	 * <code php>
	 * $group = Group::getByPK( 1 );
	 * $users = $group->User(); // Will return an ActiveRecordIterator with all the User Objects for Group 1.
	 * </code>
	 *
	 * @param string $method
	 * @param array $params
	 */
	public function __call( $method, $params ) {
		$ref = sprintf( '%s\\%s', \get_namespace( $this ), $method );
		if ( !class_exists( $ref ) ) {
			throw new ActiveRecordException( sprintf( 'Invalid Referencing Foreign class: "%s" for ORM: %s. Class does not exist', $ref ) );
		}
		foreach( $ref::getFKFields() as $fk ) {
			if ( $fk['foreignClass'] == '\\'.get_called_class() ) {
				$values = $this->getValues( $fk['foreignFields'] );
				break;
			}
		}
		if ( !isset( $values ) ) {
			throw new ActiveRecordException( sprintf( 'Invalid Foreign Key Reverse fetch: Class "%s" does not references "%s"', $ref, get_called_class() ) );
		}

		return $ref::listByFields( $values );
	}

	/**
	 * Gets an ActiveRecord by its primary key.
	 *
	 * @param * The primary key fields
	 * @return ActiveRecord
	 */
	public static function getByPK() {
		$values = func_get_args();
		if ( sizeof( static::$pkFields ) != sizeof( $values ) ) {
			throw new ActiveRecordException( sprintf( 'Wrong number of PK fields. Expected %d, Received %d', sizeof( static::$pkFields ), $values ) );
		}

		return static::getObject( get_called_class(), static::$pkFields, $values );
	}

	/**
	 * Gets an ActiveRecord by the given array of field => values
	 * 
	 * If more than one row is returned, an exception is thrown.
	 * If no rows are returned, this method returns null
	 *
	 * @param array $fields
	 * @erturn ActiveRecord
	 * @throws ActiveRecordException If any of the fields is invalid, or more than 1 row is returned.
	 */
	public static function getByFields( array $fields ) {
		$stmt = static::getQueryByFields( $fields );
		$rs = $stmt->execute();
		if ( $rs->selectedRows() > 1 ) {
			throw new ActiveRecordException( sprintf( 'Query for fields "%s" with values "%s" returned more than one row. Use listByFields instead?', join( ', ', array_keys( $fields ) ), join( ', ', array_values( $fields ) ) ) );
		} elseif ( $rs->next() ) {
			$ar = new static();
			$ar->hydrate( $rs->current() );
			return $ar;
		} else {
			return null;
		}
	}

	/**
	 * Gets an ActiveRecordIterator for the given array of field => values conditions
	 *
	 * This method always return an iterator, that can be empty, if no rows are selected
	 *
	 * If the array of fields is null, then all records are returned (the same as calling listAll)
	 *
	 * @param array $fields
	 * @return ActiveRecordIterator
  	 * @throws ActiveRecordException If any of the fields is invalid
	 */ 
	public static function listByFields( array $fields=null ) {
		$stmt = static::getQueryByFields( $fields );
		$rs = $stmt->execute();
		return new ActiveRecordIterator( new static(), $rs );
	}

	/**
	 * Gets an ActiveRecordIterator for the given sql, and parameters
	 *
	 * This method always return an iterator, that can be empty, if no rows are selected
	 *
	 * Examples:
	 *
	 * User::listBySQL("select * from users where status=:status", ":status", "enabled");
	 *
	 * User::listBySQL("select * from users where active>0" );
	 *
	 * @param string $sql
	 * @param mixed n...
	 * @return ActiveRecordIterator
  	 * @throws ActiveRecordException If any of the fields is invalid
	 */ 
	public static function listForSQL() {
		$sql = "";
		$binds = array();
		$args = func_get_args();
		if (!isset($args[0])) throw new ActiveRecordException("No SQL param specified");
		$sql = $args[0];
		$n = count($args)-1;
		for( $i = 1; $i < $n; $i += 2 ) {
			if (!isset($args[$i]) || !isset($args[$i+1])) throw new ActiveRecordException("Invalid argument pair for binds");
			$binds[$args[$i]] = $args[$i+1];
		}
		$conn = static::$conn;
		$stmt = $conn->prepare( $sql );
		$stmt->setBinds( $binds );
		try {
			$rs = $stmt->execute();
			return new ActiveRecordIterator( new static(), $rs );
		} catch ( SQLException $e ) {
            throw new ActiveRecordException( $e->getMessage(), $stmt->getQuery() );
		}
	}

	/**
	 * Gets an ActiveRecordIterator for all the records in the table (unfiltered)
	 *
	 * @return ActiveRecordIterator
	 * @throws ActiveRecordException
	 */
	public static function listAll() {
		return static::listByFields();
	}

	/**
	 * Inserts this ActiveRecord into the Database
	 *
	 * @throws ActiveRecordException
	 */
	 public function insert() {
		$conn = static::$conn;

		if ( $conn->hasSequences() ) {
			$this->loadSequences( $conn );
		} else {
			$this->clearSequences();
		}
		list( $conds, $binds ) = $this->getFieldBinds( array_keys( static::$fields ), self::BIND_INSERT );
		
		$sql = sprintf( 'insert into %s ( %s ) values ( %s )', $this->getSchemaTable(), implode( ', ', $conds ), implode( ', ', array_keys( $binds ) ) );
		$stmt = $conn->prepare( $sql );
		$stmt->setBinds( $binds );

		try {
			$stmt->execute();
			if ( $conn->hasLastInsertID() ) $this->loadInsertID( $stmt );
			$this->new = false;
			$this->modified = false;
		} catch ( SQLException $e ) {
            throw new ActiveRecordException( $e->getMessage(), $stmt->getQuery() );
		}
	}

	/**
	 * Updates the database with this ActiveRecord values.
	 *
	 * @throws ActiveRecordException
	 */
	public function update() {
		$conn = static::$conn;

		list( $fields, $binds1 ) = $this->getFieldBinds( array_keys( static::$fields ), self::BIND_UPDATE );
		list( $conds, $binds2 ) = $this->getFieldBinds( static::$pkFields, self::BIND_UPDATE ); 
		$binds = array_merge( $binds1, $binds2 );

		$sql = sprintf( 'update %s set %s where %s', $this->getSchemaTable(), implode( ', ', $fields ), implode( ' and ', $conds ) );

		$stmt = $conn->prepare( $sql );
		$stmt->setBinds( $binds );

		try {
			$stmt->execute();
			$this->modified = false;
		} catch ( SQLException $e ) {
            throw new ActiveRecordException( $e->getMessage(), $stmt->getQuery() );
		}

	}

	/**
	 * Deletes the underlying record from the database, based on its Primary Key fields.
	 * 
	 * @throws ActiveRecordException
	 */
	public function delete() {
		$conn = static::$conn;

		list( $conds, $binds ) = $this->getFieldBinds( static::$pkFields, self::BIND_DELETE );

		$sql = sprintf( 'delete from %s where %s', $this->getSchemaTable(), implode( ' and ', $conds ) );

		$stmt = $conn->prepare( $sql );
		$stmt->setBinds( $binds );

		if ( $stmt->execute() ) {
			$this->clear();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Saves this ActiveRecord to the database.
	 *
	 * @return boolean Wether the ActiveRecord has been actually saved (false if it wasn't at all modified)
	 */
	public function save() {
		if ( $this->isModified() ) {
			if ( $this->new ) {
				$this->insert();
			} else {
				$this->update();
			}
			$this->modified = false;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Clears this ActiveRecord fields (essentially revert it to a freshly instantiated ActiveRecord)
	 */
	public function clear() {
		foreach( $this->values as $field => $value ) {
			$this->values[$field] = null;
		}
		$this->new = true;
		$this->modified = false;
	}

	/**
	 * Hydrates this ActiveRecord with fields from the provided ResultSetRow
	 *
	 * @param ResultSetRow $row
	 */
	public function hydrate( ResultSetRow $row ) {
		if ( $row->getResultSet()->getMode() == Connection::MODE_MULTI ) {
			$table = ( $this->alias !== null ? $this->alias : static::$table );
			foreach( static::$fields as $field => $dbField ) {
				$this->values[$field] = ( $row->has( $table, $dbField ) ? $row->get( $table, $dbField ) : null );
			}
		} else {
			foreach( static::$fields as $field => $dbField ) {
				$this->values[$field] = ( $row->has( $dbField ) ? $row->get( $dbField ) : null );
			}
		}
		$this->new = false;
	}

	/**
	 * Returns whether this ActiveRecord is new
	 *
	 * @return boolean
	 */
	public function isNew() {
		return $this->new;
	}

	/**
	 * Returns whether this ActiveRecord is modified
	 *
	 * @return boolean
	 */
	public function isModified() {
		return $this->modified;
	}

	/**
	 * Returns a string representation of this ActiveRecord
	 *
	 * @return string
	 */
	public function __toString() {
		$str = sprintf( "[%s (ID #%s)] %s\n", get_called_class()
							, $this->id()
							, ( $this->modified ? ( $this->new ? '* NEW *' : '* MODIFIED *' ) : null ) );
		$str.= "Fields:\n";
		foreach( $this->values as $field => $value ) {
			if ( strlen( $value ) > 500 ) {
				$str.= sprintf( "  -> %-25s = [Long string: %d chars]\n", $field, strlen( $value ) );
			} else {
				$str.= sprintf( "  -> %-25s = %s\n", $field, $value );
			}
		}
		$str.= "Foreign Objects:\n";
		foreach( $this->fkValues as $field => $value ) {
			$str.= sprintf( "  -> %-25s = %s\n", $field, ( $value === null ? '[Not Requested]' : sprintf( '[%s (ID #%s)]', get_class( $value ), $value->id() ) ) );
		}
		return $str."\n";
	}

	/**
	 * Resets all the sequenced fields to null
	 */
	protected function clearSequences() {
		foreach( static::$sequences as $field ) {
			$this->values[$field] = null;
		}
	}

	/**
	 * Loads the sequences next values onto the local fields.
	 */
	protected function loadSequences( Connection $conn ) {
		foreach( static::$sequences as $field ) {
			$this->values[$field] = $conn->getSequence( $field );
		}
	}

	/**
	 * Loads the last insert id onto the local fields.
	 */
	protected function loadInsertID( PreparedStatement $stmt ) {
		foreach( static::$sequences as $field ) {
			$this->values[$field] = $stmt->getLastInsertID();
		}
	}

	/**
	 * Builds and returns a PreparedStatement for the given array of field => values conditions
	 *
	 * If the array of fields is null, then no conditions are applied
	 * 
	 * @param array $fields
	 * @return PreparedStatement The PreparedStatement 
	 */
	protected static function getQueryByFields( array $fields=null ) {
		$conn = static::$conn;
		if ( $fields !== null ) {
			list( $conds, $binds ) = static::getValueBinds( $fields );
			$sql = sprintf( 'select * from %s where %s', static::getSchemaTable(), implode( ' and ', $conds ) );
			$stmt = $conn->prepare( $sql );
			$stmt->setBinds( $binds );
		} else {
			$sql = sprintf( 'select * from %s', static::getSchemaTable() );
			$stmt = $conn->prepare( $sql );
		}
		return $stmt;
	}

	/**
	 * Gets an ActiveRecord Object for the supplied class, fields, and values
	 *
	 * This is a helper method that checks whether an object for the supplied field/values combination exists in the current ActiveRecord cache,
	 * and is used both for getting a self object by primary key, or a foreign object by its foreign key
	 *
	 * @param string $class
	 * @param array $fields
	 * @parma array $values
	 */
	protected static function getObject( $class, array $fields, array $values ) {
		$id = serialize( $values );
		if ( !isset( static::$cache[$class][$id] ) ) {
			static::$cache[$class][$id] = $class::getByFields( array_combine( $fields, $values ) );
		}
		return static::$cache[$class][$id];
	}

	/**
	 * Calls getValueBinds by fetching the values for the provided fields, thus building the required field=>value pairs array
	 *
	 * @param string $fields database fields
	 * @param $flag The type of binds
	 */
	protected function getFieldBinds( array $fields, $flag=self::BIND_SELECT ) {
		return $this->getValueBinds( array_combine( $fields, $this->getValues( $fields ) ), $flag );
	}

	/**
	 * Builds a list of SQL conditions and their corresponding binds
	 * from an array of field=>value pairs, returning an array with two
	 * values: The Array of SQL conditions and the Array of binds.
	 * 
	 * @param $values The array of values.
	 * @param $flag The type of binds
	 */
	protected static function getValueBinds( array $values, $flag=self::BIND_SELECT ) {
		$conds = array();
		$binds = array();
		foreach( $values as $field => $value ) {
			$dbField = static::$fields[$field];
			$bind = ':'.$dbField;
			switch( $flag ) {
				case self::BIND_SELECT:
				case self::BIND_UPDATE:
				case self::BIND_DELETE:
					$conds[] = sprintf( '%s=%s', $dbField, $bind );
					$binds[$bind] = $value;
					break;

				case self::BIND_INSERT:
					$conds[] = $dbField;
					$binds[$bind] = $value;
					break;
			}
		}
		return array( $conds, $binds );
	}

}
