<?php

namespace i18n;

/**
 * Translator Interface for i18n
 */
interface Translator {

	/**
	 * Gets a Translator instance for the supplied Locale
	 *
	 * @param Locale $locale
	 * @return Translator
	 */
	public static function getInstance( Locale $locale=null );

	/**
	 * Loads i18n translation strings for the supplied domain
	 *
	 * @param string $domain
	 */
	public function load( $domain );

	/**
	 * Gets a translation string for a given key.
	 * If a default value is supplied, and the translation isn't found, the default value should be returned.
	 *
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	public function get( $key, $default=null );

	/**
	 * Updates a translation string for a given domain and key
	 *
	 * @param string $domain
	 * @param string $key
	 * @param string $txt
	 */
	public function save( $domain, $key, $txt );

}
