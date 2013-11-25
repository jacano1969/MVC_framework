<?php

namespace orm\i18n;

use core\Object;
use i18n\Locale;
use sql\schema\Table;
use sql\SQLException;

class LocaleSchemaStrategy extends Object implements I18NStrategy {

	/**
	 * Returns the internationalization table for the supplied LocalizedActiveRecord and Locale
	 *
	 * The LocaleSchema strategy returns the record's schema name with the locale string attached, with the same record's table name
	 *
	 * Example:
	 * records_schema.record_table => records_schema_en_GB.record_table
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NSchemaTable( $record, Locale $locale = null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		return sprintf( '%s_%s.%s', $record::getSchema(), $locale->getLocale(), $record::getTable() );
	}

	/**
	 * Returns the internationalization joins for the supplied LocalizedActiveRecord and Locale
	 *
	 * The LocaleSchema strategy simply returns the primary key joins.
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NJoins( $record, Locale $locale = null ) {
		$joins = array();
		foreach( $record::getPKFields() as $f ) {
			$field = $record::getField( $f );
			$joins[] = sprintf( 'main.%s = i18n.%s', $field, $field );
		}
		return implode( ' and ', $joins );
	}

	/**
	 * Returns the internationalization fields for the supplied parent Table and Locale
	 *
	 * @param Table $table
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NFields( Table $table, Locale $locale = null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		$mainCols = array();
		foreach( $table->getPrimaryKeys() as $col ) {
			$mainCols[] = $col->getName();
		}
		$schema = sprintf( '%s_%s', $table->getSchema(), $locale->getLocale() );
		$i18nTable = $table->getConnection()->getTable( $schema, $table->getName() );
		if ( !$i18nTable ) {
			throw new SQLException( sprintf( 'Table does not exists: %s.%s', $schema, $table->getName() ) );
		}
		$i18nCols = array();
		foreach( $i18nTable->getColumns() as $col ) {
			if ( !in_array( $col->getName(), $mainCols ) ) {
				$i18nCols[] = $col;
			}
		}
		return $i18nCols;
	}

}
