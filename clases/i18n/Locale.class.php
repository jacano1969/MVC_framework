<?php

namespace i18n;

use core\Object;

/**
 * Base Locale Class, providing all the static methods for Localized/Internationalized information manipulation.
 * 
 * This Class acts as a static Proxy to one of the php::i18n::locales::* classes.
 */
class Locale extends Object {

	/**
	 * The Locale in use
	 */
	private static $defaultLocale = null;

	/**
	 * Locale name (ie: 'es_ES', 'en_GB')
	 */
	protected $locale = null;

	/**
	 * Locale language (ie: 'es', 'en')
	 */
	protected $language = null;

	/**
	 * Locale Country (ie: 'ES', 'GB')
	 */
	protected $country = null;

	/**
	 * Locale Display Name (ie: 'Espanol (Espana)', 'English (GB)')
	 */
	protected $name = null;

	/**
	 * Instantiates a new Locale Class for the given locale ( language_COUNTRY )
	 *
	 * @param string $locale
	 */
	public function __construct( $locale ) {
		$sp = preg_split( '/_/', $locale );
		$this->language = $sp[0];
		$this->country = $sp[1];
		$this->locale = $locale;
	}

	/**
	 * Sets the default Locale
	 *
	 * @param string $locale
	 */
	public static function setDefault( $locale ) {
		return self::$defaultLocale = new Locale( $locale );
	}

	/**
	 * Gets the Default Locale
	 *
	 * @return Locale
	 */
	public static function getDefault() {
		if ( !self::$defaultLocale ) {
			throw new I18NException( 'No default Locale Available. Use setLocale() first?' );
		}
		return self::$defaultLocale;
	}

	/**
	 * Clears the default locale
	 */
	public static function clearDefault() {
		self::$defaultLocale = null;
	}

	/**
	 * Returns the Locale ID string (language_COUNTRY)
	 *
	 * @return string
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * Returns the Locale Name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets the Locale language
	 *
	 * @return string
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * Gets the Language name for this locale
	 *
	 * @return string
	 */
	public function getLanguageName() {
		return Language::getDefault()->name( $this->language );
	}

	/**
	 * Gets the Locale Country
	 *
	 * @return string
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Returns a string representation of this locale
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->getLocale();
	}
}
