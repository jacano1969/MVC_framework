<?php

namespace i18n;

/**
 * This class simply defines language names in a locale fashion.
 */
class Language extends LocaleResource {

	/**
	 * Default Number Format Instance
	 */
	protected static $default = null;

	/**
	 * Array of language names
	 */
	protected $names = array();

	/**
	 * Returns a language name
	 *
	 * @param string $language
	 * @return string
	 */
	public function name( $language ) {
		if ( !isset( $this->names[$language] ) ) {
			throw new I18NException( sprintf( 'No name defined for Language "%s" on this locale', $language ) );
		}
		return $this->names[$language];
	}
}
