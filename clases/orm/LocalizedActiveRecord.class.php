<?php

namespace orm;

use mvc\App;
use core\Object;
use i18n\Locale;
use sql\ResultSetRow;
use sql\SQLException;

/**
 * Localized Active Record
 */
abstract class LocalizedActiveRecord extends ActiveRecord {

	/**
	 * I18N Fields
	 */
	protected static $i18nFields = array();

	/**
	 * I18NStrategy
	 */
	protected static $strategy = null;

	/**
	 * Current Locale for this LocalizedActiveRecord.
	 */
	protected $locale = null;

	/**
	 * I18N Fields values
	 */
	protected $i18nValues = array();

	/**
	 * Whether the i18n record is new (not hydrated from database)
	 */
	protected $i18nNew = false;

	/**
	 * Whether the i18n record has been modified
	 */
	protected $i18nModified = false;

	/**
	 * Gets the i18n fields for this ActiveRecord
	 *
	 * @return array
	 */
	public static function getI18NFields() {
		return static::$i18nFields;
	}

	/**
	 * Gets the database i18n schema field for the supplied ActiveRecord field
	 *
	 * @param string $field
	 * @return string
	 */
	public static function getI18NField( $field ) {
		if ( !isset( static::$i18nFields[$field] ) ) {
			throw new ActiveRecordException( sprintf( 'Invalid ActiveRecord i18n field: "%s" for "%s"', $field, get_called_class() ) );
		}
		return static::$i18nFields[$field];
	}

	/**
	 * Gets the schema.table this LocalizedActiveRecord represents.
	 * This depends on the I18NStrategy used.
	 *
	 * @param Locale $locale[=null]
	 * @return string
	 */
	public static function getI18NSchemaTable( Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		$strat = static::$strategy;
		return $strat::getI18NSchemaTable( get_called_class(), $locale );
	}

	/**
	 * Gets the current locale for this LocalizedActiveRecord
	 *
	 * @return Locale
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * Returns the field values for the provided array of fields (all fields if null)
	 *
	 * @param array $field An array of field names to get the values for
	 * @return array The associative array of field=>value pairs.
	 */
	public function getValues( array $fields=null ) {
		$values = parent::getValues( $fields );
		foreach( $this->i18nValues as $field => $value ) {
			if ( $fields === null || in_array( $field, $fields ) ) {
				$values[$field] = $value;
			}
		}
		return $values;
	}

	/**
	 * Overloaded field getter
	 *
	 * This method will first try to fetch an i18n field, and then fall back to parent::__get() for non-i18n fields
	 *
	 * @param string $field The table field to get
	 * @return mixed The field value
	 * @throws ActiveRecordException
	 */
	public function __get( $field ) {
		if ( array_key_exists( $field, $this->i18nValues ) ) {
			return $this->i18nValues[$field];
		} else {
			return parent::__get( $field );
		}
	}

	/**
	 * Overloaded field setter
	 *
	 * This method will first try to set an i18n field, and then fall back to parent::__set() for non-i18n fields
	 *
	 * @param string $field The field to set.
	 * @param mixed $value The value to set.
	 * @throws ActiveRecordException
	 */
	final public function __set( $field, $value ) {
		if ( array_key_exists( $field, $this->i18nValues ) ) {
			if ( ( $this->i18nValues[$field] === null && $value !== null ) || $value != $this->i18nValues[$field] ) {
				$this->i18nValues[$field] = $value;
				$this->i18nModified = true;
			}
		} else {
			parent::__set( $field, $value );
		}
	}

	/**
	 * Gets a LocalizedActiveRecord by its primary key.
	 *
	 * This method expects an amount of primary key values equal to this ActiveRecord primary key fields, with an optional last Locale parameter.
	 * If the amount is equal, then locale is considered null (default locale used)
	 * If the amount is pk fields+1, then the last parameter will be checked to enforce a Locale object.
	 * Any other amount of parameters fails.
	 *
	 * @param * The primary key fields
	 * @param Locale $locale[=null]
	 * @return LocalizedActiveRecord
	 */
	public static function getByPK() {
		$values = func_get_args();
		if ( sizeof( static::$pkFields ) < sizeof( $values ) ) {
			$locale = array_pop( $values );
		} else {
			$locale = Locale::getDefault();
		}
		if ( !$locale instanceOf Locale || sizeof( static::$pkFields ) != sizeof( $values ) ) {
			throw new ActiveRecordException( sprintf( 'Wrong number of PK fields, or last argument is not a Locale instance. Expected %d, Received %d', sizeof( static::$pkFields ), $values ) );
		}

		return static::getObject( get_called_class(), static::$pkFields, $values, $locale );
	}

	/**
	 * Gets an ActiveRecord by the given array of field => values and optional Locale
	 * 
	 * If more than one row is returned, an exception is thrown.
	 * If no rows are returned, this method returns null
	 *
	 * @param array $fields
	 * @param Locale $locale=null
	 * @return LocalizedActiveRecord
	 * @throws ActiveRecordException If any of the fields is invalid, or more than 1 row is returned.
	 */
	public static function getByFields( array $fields, Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		$stmt = static::getQueryByFields( $fields, $locale );
		$rs = $stmt->execute();
		if ( $rs->selectedRows() > 1 ) {
			throw new ActiveRecordException( sprintf( 'Query for fields "%s" with values "%s" returned more than one row. Use listByFields instead?', join( ', ', array_keys( $fields ) ), join( ', ', array_values( $fields ) ) ) );
		} elseif ( $rs->next() ) {
			$ar = new static();
			$ar->locale = $locale;
			$ar->hydrate( $rs->current() );
			return $ar;
		} else {
			return null;
		}
	}

	/**
	 * Gets an ActiveRecordIterator for the given array of field => values conditions and optional Locale 
	 *
	 * This method always return an iterator, that can be empty, if no rows are selected
	 *
	 * If the array of fields is null, then all records are returned (the same as calling listAll)
	 *
	 * @param array $fields
	 * @param Locale $locale[=null]
	 * @return ActiveRecordIterator
  	 * @throws ActiveRecordException If any of the fields is invalid
	 */ 
	public static function listByFields( array $fields=null, Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		$stmt = static::getQueryByFields( $fields, $locale );
		$rs = $stmt->execute();
		$ar = new static();
		$ar->locale = $locale;
		return new ActiveRecordIterator( $ar, $rs );
	}

	/**
	 * Gets an ActiveRecordIterator for all the records in the table (unfiltered), with the optional Locale
	 *
	 * @param Locale $locale[=null]
	 * @return ActiveRecordIterator
	 * @throws ActiveRecordException
	 */
	public static function listAll( Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		return static::listByFields();
	}

	/**
	 * Inserts this ActiveRecord into the Database
	 * This method also calls insertLocalization
	 *
	 * @param Locale $locale[=null]
	 * @throws ActiveRecordException
	 */
	public function insert( Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		parent::insert();
		$this->insertLocalization( $locale );
	}

	/**
	 * Inserts this LocalizedActiveRecord i18n record into the Database, for the supplied Locale $locale
	 *
	 * @param Locale $locale[=null]
	 * @throws ActiveRecordException
	 */
	public function insertLocalization( Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();

		$conn = static::$conn;

		list( $pkConds, $pkBinds ) = $this->getFieldBinds( static::$pkFields, self::BIND_INSERT );
		list( $conds, $binds ) = $this->getFieldBinds( array_keys( static::$i18nFields ), self::BIND_INSERT );
		$conds = array_merge( $pkConds, $conds );
		$binds = array_merge( $pkBinds, $binds );

		$strat = static::$strategy;
		$sql = sprintf( 'insert into %s ( %s ) values ( %s )', $strat::getI18NSchemaTable( get_called_class(), $locale ), implode( ', ', $conds ), implode( ', ', array_keys( $binds ) ) );
		$stmt = $conn->prepare( $sql );
		$stmt->setBinds( $binds );

		try {
			$stmt->execute();
			$this->i18nNew = false;
			$this->i18nModified = false;
		} catch ( SQLException $e ) {
			throw new ActiveRecordException( $e->getMessage() );
		}
	}

	/**
	 * Updates the database with this ActiveRecord values.
	 * This method also calls updateLocalization
	 *
	 * @param Locale $locale[=null]
	 * @throws ActiveRecordException
	 */
	public function update( Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		parent::update();
		$this->updateLocalization( $locale );
	}

	/**
	 * Updates this LocalizedActiveRecord i18n record, for the supplied Locale $locale
	 *
	 * @param Locale $locale[=null]
	 * @throws ActiveRecordException
	 */
	public function updateLocalization( Locale $locale = null ) {
		if ( $locale === null ) $locale = Locale::getDefault();

		$conn = static::$conn;

		list( $fields, $binds1 ) = $this->getFieldBinds( array_keys( static::$i18nFields ), self::BIND_UPDATE );
		list( $conds, $binds2 ) = $this->getFieldBinds( static::$pkFields, self::BIND_UPDATE );
		$binds = array_merge( $binds1, $binds2 );

		$strat = static::$strategy;
		$sql = sprintf( 'update %s set %s where %s', $strat::getI18NSchemaTable( get_called_class(), $locale ), implode( ', ', $fields ), implode( ' and ', $conds ) );

		$stmt = $conn->prepare( $sql );
		$stmt->setBinds( $binds );

		try {
			$stmt->execute();
			$this->i18nModified = false;
		} catch ( SQLException $e ) {
			throw new ActiveRecordException( $e->getMessage() );
		}
	}

	/**
	 * Deletes the underlying record from the database, and all its localizations.
	 *
	 * @throws ActiveRecordException
	 */
	public function delete() {
		$conn = static::$conn;

		foreach( App::getContext()->getLocales() as $locale ) {
			list( $conds, $binds ) = $this->getFieldBinds( static::$pkFields, self::BIND_DELETE );
			$sql = sprintf( 'delete from %s where %s', $this->getI18NSchemaTable( new Locale( $locale ) ), implode( ' and ', $conds ) );
			$stmt = $conn->prepare( $sql );
			$stmt->setBinds( $binds );
			try {
				$stmt->execute();
			} catch ( SQLException $e ) {
				throw new ActiveRecordException( sprintf( 'Error deleting LocalizedActiveRecord: %s', $e->getMessage() ) );
			}
		}

		return parent::delete();
	}

	/**
	 * Saves this LocalizedActiveRecord to the database.
	 *
	 * The localized record keep a different save state than the main record, and thus will be checked for the new/modified flags independently, for the current locale
	 *
	 * It is possible that:
	 * - The parent record is inserted and the localized record inserted
	 * - The parent record is updated and the localized record inserted or updated
	 * - The parent record is untouched and the localized record inserted or updated
	 *
	 * @param Locale $locale[=null]
	 * @return boolean Wether the ActiveRecord has been actually saved (false if it wasn't at all modified)
	 */
	public function save( Locale $locale=null ) {
		parent::save();

		if ( $this->i18nModified ) {
			if ( $this->i18nNew ) {
				$this->insertLocalization( $locale );
			} else {
				$this->updateLocalization( $locale );
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Clears this ActiveRecord fields (essentially revert it to a freshly instantiated ActiveRecord)
	 */
	public function clear() {
		parent::clear();
		foreach( $this->i18nValues as $field => $value ) {
			$this->i18nValues[$field] = null;
		}
		$this->i18nNew = true;
		$this->i18nModified = false;
	}

	/**
	 * Hydrates this LocalizedActiveRecord with fields from the provided ResultSetRow
	 *
	 * @param ResultSetRow $row
	 */
	public function hydrate( ResultSetRow $row ) {
		parent::hydrate( $row );
		foreach( static::$i18nFields as $field => $dbField ) {
			$this->i18nValues[$field] = ( $row->has( $dbField ) ? $row->get( $dbField ) : null );
		}
	}

	/**
	 * Returns whether this ActiveRecord is new, with extra check for the supplied locale
	 *
	 * @return boolean
	 */
	public function isNew( Locale $locale=null ) {
		return $this->new || $this->i18nNew;
	}

	/**
	 * Returns whether this ActiveRecord is modified, with extra check for the supplied locale
	 *
	 * @return boolean
	 */
	public function isModified( Locale $locale=null ) {
		return $this->modified || $this->i18nModified;
	}

	/**
	 * Returns a string representation of this ActiveRecord
	 *
	 * @return string
	 */
	public function __toString() {
		$str = parent::__toString();
		$str.= "Localized fields:\n";
		foreach( $this->i18nValues as $field => $value ) {
			if ( strlen( $value ) > 500 ) {
				$str.= sprintf( "  -> %-25s = [Long string: %d chars]\n", $field, strlen( $value ) );
			} else {
				$str.= sprintf( "  -> %-25s = %s\n", $field, $value );
			}
		}
		return $str;
	}

	/**
	 * Builds and returns a PreparedStatement for the given array of field => values
	 *
	 * This method performs the necessary join for the configured internationalization strategy.
	 * 
	 * @param array $fields
	 * @return PreparedStatement The PreparedStatement 
	 */
	protected static function getQueryByFields( array $fields=null, Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		$conn = static::$conn;

		$strategy = static::$strategy;

		$sql = sprintf( 'select main.*, i18n.*
			from %s main
			join %s i18n on ( %s )'
			, static::getSchemaTable()
			, $strategy::getI18NSchemaTable( get_called_class(), $locale )
			, $strategy::getI18NJoins( get_called_class(), $locale )
		);

		if ( $fields !== null ) {
			list( $conds, $binds ) = static::getValueBinds( $fields );
			$sql.= sprintf( ' where %s', implode( ' and ', $conds ) );
			$stmt = $conn->prepare( $sql );
			$stmt->setBinds( $binds );
		} else {
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
	 * @param array $values
	 * @param Locale $locale[=null]
	 */
	protected static function getObject( $class, array $fields, array $values, Locale $locale=null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		$id = serialize( $values );
		if ( !isset( static::$cache[$class][$id] ) ) {
			static::$cache[$class][$id] = $class::getByFields( array_combine( $fields, $values ), $locale );
		}
		return static::$cache[$class][$id];
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
			list( $schema, $dbField ) = isset( static::$i18nFields[$field] ) ? array( 'i18n', static::$i18nFields[$field] ) : array( 'main', static::$fields[$field] );
			$bind = ':'.$dbField;
			switch( $flag ) {
				case self::BIND_SELECT:
					$conds[] = sprintf( '%s.%s=%s', $schema, $dbField, $bind );
					$binds[$bind] = $value;
					break;

				case self::BIND_INSERT:
					$conds[] = $dbField;
					$binds[$bind] = $value;
					break;

				case self::BIND_UPDATE:
				case self::BIND_DELETE:
					$conds[] = sprintf( '%s=%s', $dbField, $bind );
					$binds[$bind] = $value;
					break;
			}
		}
		return array( $conds, $binds );
	}

}
