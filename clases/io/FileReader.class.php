<?php

namespace io;

use core\Object;

/**
 * Implemets an InputStreamReader to read files.
 */
class FileReader extends InputStreamReader {

	/**
	 * File handler
	 */
	protected $handler;

	/**
	 * Instantiates a new FileReader for the supplied InputStream and optional InputStreamParser
	 *
	 * @param InputStream $stream
	 * @param InputStreamParser $parser [=null]
	 */
	public function __construct( InputStream $stream, InputStreamParser $parser=null ) {
		if ( !$stream instanceOf File ) {
			throw new Exception( sprintf( 'Invalid InputStream %s for FileReader. This InputStreamReader requires a File', get_class( $stream ) ) );
		}
		parent::__construct( $stream, $parser );
	}

	/**
	 * Gets data from the currently open file, up to the supplied $bytes (4096 by default)
	 *
	 * If an InputStreamParser is set, then it is returned with the line set, otherwise, the line as a string is returned
	 *
	 * @parma int $bytes [=4096]
	 * @return mixed
	 */
	public function read( $bytes=4096 ) {
		if ( $this->handler === false ) {
			throw new Exception( sprintf( 'Error reading %d chars from file "%s": Invalid file handler', $this->file->getName() ) );
		}
		$data = @fgets( $this->handler, $bytes );
		if ( $this->parser !== null && $data ) {
			return $this->parser->parse( $this, $data );
		} else {
			return $data;
		}
	}

	/**
	 * Opens the underlying file handler
	 */ 
	public function open() {
		$this->handler = @fopen( $this->stream->getName(), 'rb' );
		if ( $this->handler === false ) {
			$err = error_get_last();
			throw new Exception( sprintf( 'Could not open file "%s" for reading: %s', $this->stream->getName(), $err['message'] ) );
		}
	}

	/**
	 * Closes the underlying File handler
	 */
	public function close(){
		@fclose($this->handler);
	}

	/**
	 * Iterator::next
	 * Advances to next line
	 */ 
	public function next() {
		$this->data = $this->read();
		if ( $this->data ) {
			$this->position++;
		} else {
			$this->data = null;
		}
	}

	/**
	 * Iterator::rewind
	 * Rewinds the reader to the beginning of the file
	 */
	public function rewind() {
		rewind( $this->handler );
		$this->data = $this->read();
		$this->position = 0;
	}

}
