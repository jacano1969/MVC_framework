<?php

namespace io;

/**
 * FileWriter provides method to write to a File Object.
 */
class FileWriter extends OutputStreamWriter {

	/**
	 * Mode Write (places the pointer at the beginning, truncating the file)
	 */
	const MODE_WRITE = 'wb';

	/**
	 * Mode append (places the pointer at the end of the file)
	 */
	const MODE_APPEND = 'ab';

	/**
	 * File handler
	 */
	protected $handler;

	/**
	 * Writing mode
	 */
	protected $mode = null;

	public function __construct( OutputStream $stream, $mode=self::MODE_WRITE ) {
		if ( !$stream instanceOf File ) {
			throw new Exception( sprintf( 'Invalid OutputStream %s for FileWriter. This OutputStreamWriter requires a File', get_class( $stream ) ) );
		}

		$this->mode = $mode;
		parent::__construct( $stream );
	}

	/**
	 * Opens the file handler
	 */
	public function open() {
		$this->handler = @fopen( $this->stream->getName(), $this->mode );
		if ( $this->handler === false ) {
			$err = error_get_last();
			throw new Exception( sprintf( 'Could not open file "%s" for writing (mode: "%s"). Error (%d): %s', $this->stream->getName(), $this->mode, $err['type'], $err['message'] ) );
		}
	}

	/**
	 * Puts the data on this file writer
	 *
	 * @param string $data
	 */
	public function write( $data ) {
		if ( $this->handler === false ) {
			throw new Exception( sprintf( 'Cannot write data. Invalid FileWriter handler' ) );
		}
		fputs( $this->handler, $data );
		return $this;
	}

	/**
	 * Closes the FileWriter handler
	 */
	public function close(){
		@fclose($this->handler);
	}

}
