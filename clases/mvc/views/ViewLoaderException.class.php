<?php


namespace mvc\views;

use core\CoreException;

/**
 * Thrown by ViewLoaders 
 */
class ViewLoaderException extends CoreException {

	/**
	 * The ViewLoader that threw this exception
	 */
	private $loader = null;

	public function __construct( ViewLoader $loader, $message ) {
		$this->loader = $loader;
		$this->message = sprintf( '%s: %s', get_class( $loader ), $message );
	}
}
