<?php

namespace orm\i18n;

use i18n\Locale;
use sql\schema\Table;

/**
 * Internationalization strategies for ActiveRecords implement a means to
 * perform ActiveRecord localization using the LocalizedActiveRecord class.
 */
interface I18NStrategy {

	/**
	 * Returns the internationalization table for the supplied LocalizedActiveRecord and Locale
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NSchemaTable( $record, Locale $locale = null );

	/**
	 * Returns the internationalization joins for the supplied LocalizedActiveRecord and Locale
	 *
	 * @param $record
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NJoins( $record, Locale $locale = null );

	/**
	 * Returns the internationalization fields for the supplied parent Table and Locale
	 *
	 * This method should check the internationalization table for any fields apart from the supplied parent table primary key, and return those.
	 *
	 * @param Table $table
	 * @param Locale $locale
	 * @return string
	 */
	public static function getI18NFields( Table $table, Locale $locale = null );

}
