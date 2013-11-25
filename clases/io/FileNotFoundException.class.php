<?php


namespace io;

/**
 * Class FileNotFoundException
 */
class FileNotFoundException extends IOException {

	protected $file;

	/**
	 * Instantiates a FileNotFoundException object with the default message .
	 *
	 * @param string $message The Exception message
	 * @file string file name which does not exist
	 */
	public function __construct( $message, $file=null ) {
		$this->message = $message;
		if ( $file != null ) {
			$this->file = $file;
			$this->message .=" (File: $file)";
		}
	}
}
