<?php


namespace mvc\views;

use mvc\views\View;

/**
 * The ViewHelper interface defines a standard way to set, clear and check properties for implementing classes attached to a View Class.
 */
interface ViewHelper {

	/**
	 * Sets a view helpr property. Each View Helper must define their own properties and way to handle them.
	 *
	 * @param string $name
	 * @param string $value
	 * @throws ViewException
	 */
	public function setProperty( $name, $value );

	/**
	 * Clears properties for this ViewHelper
	 */
	public function clearProperties();

	/**
	 * Checks ViewHelper configuration for consistency
	 *
	 * @throws ViewException If any errors are detected.
	 */
	public function checkProperties();

}
