<?php

namespace io;

use core\Object;

/**
 * Defines an abstract OutputStreamWriter that can take any kind of OutputStream and writes from it.
 */
abstract class OutputStreamWriter extends Object {

	/**
	 * The OutputStream
	 */
	protected $stream = null;

	/**
	 * Instantiates an OutputStreamWriter for the supplied OutputStream
	 *
	 * @param OutputStream $stream
	 */
	public function __construct( OutputStream $stream ) {
		$this->stream = $stream;
		$this->open();
	}

	/**
	 * Gets the underlying OutputStream for this writer
	 *
	 * @return OutputStream
	 */
	public function getStream() {
		return $this->stream;
	}

	/**
	 * Writes data to the underlying OutputStream
	 *
	 * @param string $data The data to write
	 */
	abstract public function write( $data );

	/**
	 * Opens the underlying OutputStream, when necessary
	 */
	abstract public function open();

	/**
	 * Closes the underlying OutputStream
	 */
	abstract public function close();

	/**
	 * On destruction, close the OutputStream handler
	 */
	/** COMMENTED DUE TO UNEXPECTED SEGMENTATION FAULT BUGS
	public function XXXXX__destruct() {
		$this->close();
	}
	*/

}
