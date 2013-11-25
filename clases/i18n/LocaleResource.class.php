<?php

namespace i18n;

use core\Object;

/**
 * This class represents a Localized resource.
 */
class LocaleResource extends Object {

	/**
	 * Gets a LocaleResource Instance for the given Locale
	 *
	 * @param Locale $locale
	 * @return LocaleResource Class
	 */
	public static function getInstance( Locale $locale ) {
		$sp = explode( "\\", get_called_class() ); 
		$cls = array_pop( $sp );
		$ns = join( "\\", $sp );
		$class = sprintf( '%s\resources\%s_%s', $ns, $cls, $locale->getLocale() );
		if ( !\lookup_class( $class ) ) {
			throw new I18NException( sprintf( 'No valid %s Class found for locale "%s".', $cls, $locale->getLocale() ) );
		}
		return new $class();
	}

	/**
	 * Gets the default LocaleResource instance for this Class and defaut Locale.
	 *
	 * @return LocaleResource
	 */
	public static function getDefault() {
		if ( !static::$default ) {
			static::$default = static::getInstance( Locale::getDefault() );
		}
		return static::$default;
	}

}
