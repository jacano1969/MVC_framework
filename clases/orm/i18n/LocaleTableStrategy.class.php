<?php

namespace orm\i18n;

use core\Object;
use i18n\Locale;
use sql\schema\Table;

class LocaleTableStrategy extends Object implements I18NStrategy {

	/**
	 * Returns the internationalization table for the supplied LocalizedActiveRecord and Locale
	 *
	 * The LocaleTable strategy returns the record's schema name, with the same record's table name but with the locale string attached
	 *
	 * Example:
	 * records_schema.record_table => records_schema.record_table_en_GB
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NSchemaTable( $record, Locale $locale = null ) {
		if ( $locale === null ) $locale = Locale::getDefault();
		return sprintf( '%s.%s_%s', $record::getSchema(), $record::getTable(), $locale->getLocale() );
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
		foreach( $record::getPKFields() as $field ) {
            $field = $record::getField($field);
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
		$i18nTable = $table->getConnection()->getTable( $table->getSchema(), sprintf( '%s_%s', $table->getName(), $locale->getLocale() ) );
		$i18nCols = array();
		foreach( $i18nTable->getColumns() as $col ) {
			if ( !in_array( $col->getName(), $mainCols ) ) {
				$i18nCols[] = $col;
			}
		}
		return $i18nCols;
	}

}
