<?php

namespace orm\i18n;

use core\Object;
use i18n\Locale;

class I18NSchemaStrategy extends Object implements I18NStrategy {

	/**
	 * Returns the internationalization table for the supplied LocalizedActiveRecord and Locale
	 *
	 * The I18NSchema strategy returns the record's schema name with '_i18n' string attached, with the same record's table name
	 *
	 * Example:
	 * records_schema.record_table => records_schema_i18n.record_table
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NSchemaTable( $record, Locale $locale = null ) {
		return sprintf( '%s_i18n.%s', $record::getSchema(), $record::getTable() );
	}

	/**
	 * Returns the internationalization joins for the supplied LocalizedActiveRecord and Locale
	 *
	 * The I18NSchema strategy returns the primary key joins plus the locale-specific condition.
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NJoins( $record, Locale $locale = null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		$joins = array();
		foreach( $record::getPKFields() as $field ) {
			$joins[] = sprintf( 'main.%s = i18n.%s', $field, $field );
		}
		$joins[] = sprintf( "locale='%s'", $locale->getLocale() );
		return implode( ' and ', $joins );
	}

	/**
	 * Returns the internationalization fields for the supplied parent Table and Locale
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NFields( $record, Locale $locale = null ) {
		$mainCols = array();
		foreach( $table->getPrimaryKeys() as $col ) {
			$mainCols[] = $col->getName();
		}
		$schema = sprintf( '%s_i18n', $table->getSchema() );
		$i18nTable = $table->getConnection()->getTable( $schema, $table->getName() );
		$i18nCols = array();
		foreach( $i18nTable->getColumns() as $col ) {
			if ( $col->getName() != 'locale' && !in_array( $col->getName(), $mainCols ) ) {
				$i18nCols[] = $col;
			}
		}
		return $i18nCols;
	}


}
