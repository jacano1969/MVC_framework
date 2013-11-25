<?php


namespace core\session;

use core\Object;
use io\File;

/**
 * Implements a simple    File Session Handling mechanism, similar to PHP's native one.
 * Use as basis for your own SessionHandler
 */
class FileSessionHandler implements SessionHandler {

	/**
	 * File Pointer to Session file
	 */
	private $fp = null;

	/**
	 * File save path
	 */
	private $path = null;

	/**
	 * File save prefix
	 */
	private $name = null;

	/**
	 * Opens the Session
	 *
	 * @param string $path
	 * @param string $name
	 */
	public function open( $path, $name ) {
		if ( !$path ) {
			throw new Exception( 'No session save path defined (Check php.ini session.save_path)' );
		}
		if ( !is_dir( $path ) || !is_writable( $path ) ) {
			throw new Exception( sprintf( 'Session save path "%s" does not exist, or is not writable', $path ) );
		}
		$this->path = $path;
		$this->file = 'sess_';
	}

	/**
	 * Closes the Session
	 */
	public function close() {
	}

	/**
	 * Reads the Session data
	 *
	 * @param string $id
	 */
	public function read( $id ) {
		$file = $this->getFile( $id );
		if ( $file->exists() ) {
			return $file->read();
		} else {
			return false;
		}
	}

	/**
	 * Writes the Session data
	 *
	 * @param string $id
	 * @param string $data
	 */
	public function write( $id, $data ) {
		$file = $this->getFile( $id );
		$writer = $file->getWriter();
		$writer->write( $data );
		$writer->close();
	}

	/**
	 * Destroy the Session data
	 *
	 * @param string $id
	 */
	public function destroy( $id ) {
		$file = $this->getFile( $id );
		if ( $file->exists() ) {
			$file->delete();
		}
	}

	/**
	 * Garbage Collector.
	 *
	 * @param int $maxLifeTime
	 */
	public function gc( $maxLifeTime ) {
		echo "Checking $maxLifeTime";
	}

	/**
	 * Returns the full path to the session save path
	 *
	 * @return string
	 */
	private function getFile( $id ) {
		return new File( sprintf( '%s/%s%s', $this->path, $this->file, $id ) );
	}

	/**
	 * Return the session alive IDs according to maxLifeTime
	 *
	 * @param int $maxLifeTime
	 * @return array
	 */
	public function getAliveIDs( $maxLifeTime ) {
	}

	/**
	 * FileSessionHandler works with php's native session handling configuration. No properties supported
	 *
	 * @throws SessionException
	 */
	public function setProperty( $name, $value ) {
		throw new Exception( sprintf( 'Unsupported SessionHandler property "%s" for %s', $name, get_class() ) );
	}

}
