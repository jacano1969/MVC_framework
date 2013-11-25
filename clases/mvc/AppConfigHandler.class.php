<?php


namespace mvc;

use core\Object;

/**
 * AppConfigHandler are used for extra configuration any kind of plugins or configurable componentes might need.
 *
 * You can write your own AppConfigHandler and use it load config values from the standard AppContext config.
 */
abstract class AppConfigHandler extends Object {

	/**
	 * Config section for this handler
	 */
	protected $section = null;

	/**
	 * Config values
	 */
	protected $config = array();

	/**
	 * Instantiates a new AppConfigHandler for the supplied Config
	 *
	 * @param AppConfig $config
	 */
	public function __construct( AppConfig $config ) {
		$config->loadHandler( $this );
	}

	/**
	 * Returns the section or grouping element for this configuration handler
	 *
	 * @return string
	 */
	public function getSection() {
		return $this->section;
	}

	/**
	 * Gets the given value for this AppConfigHandler
	 *
	 * @param string $name
	 * @return string
	 */
	public function getValue( $name ) {
		return $this->config[$name];
	}

	/**
	 * Sets the given value for this AppConfigHandler
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setValue( $name, $value ) {
		if ( !array_key_exists( $name, $this->config ) ) {
			throw new AppConfigException( sprintf( 'Unsupported config directive "%s" for %s', $name, get_class() ) );
		}
		$this->config[$name] = $value;
	}

}
