<?php

namespace io;

use core\Object;
use core\CoreException;

/**
 * General purpose File class.
 */
class File extends Object implements InputStream, OutputStream {

	/**
	 * Filename
	 */
	protected $filename = null;

	/**
	 * Instantiates a new File object with the given filename
	 *
	 * @param string $filename
	 */
	public function __construct( $filename ) {
		$this->filename = $filename;
	}

	/**
	 * Returns the filename
	 *
	 * @param string
	 */
	public function getName() {
		return $this->filename;
	}

	/**
	 * Gets the file size.
	 *
	 * @return int
	 */ 
	public function getSize() {
		return filesize( $this->filename );
	}

	/**
	 * Gets the file type.
	 *
	 * @return string
	 */
	public function getType() {
		return filetype( $this->filename );
	}

	/**
	 * Tries to get the mime type. If the fileinfo extension isn't loaded, an exception is thrown
	 */
	public function getMimeType() {
		if ( !extension_loaded( 'fileinfo' ) ) {
			throw new CoreException( sprintf( 'Cant get file info on %s. You need to load the file info extension', $this->filename ) );
		}
		$info = new \FInfo(FILEINFO_MIME_TYPE);
		return $info->file( $this->filename );
	}

	/**
	 * Gets the file permissions
	 *
	 * @return int
	 */
	public function getPermissions() {
		return fileperms( $this->filename );
	}

	/**
	 * Check wether a file exists
	 *
	 * @return boolean
	 */
	public function exists() {
		return file_exists( $this->filename );
	}

	/**
	 * Gets the content of the file.
	 *
	 * @return string
	 */
	public function read() {
		return file_get_contents( $this->filename );
	}

	/**
	 * Gets a FileWriter for this File object and the supplied write mode
	 *
	 * @param string $mode
	 * @return FileWriter
	 */
	public function getWriter( $mode=FileWriter::MODE_WRITE ) {
		return new FileWriter( $this, $mode );
	}

	/**
	 * Gets a FileReader for this File object and optional FileParser
	 *
	 * @param FileParser $parser [=null]
	 * @return FileReader
	 */
	public function getReader( InputStreamParser $parser=null ) {
		return new FileReader( $this, $parser );
	}

	/**
	 * Deletes the file.
	 *
	 * @return void
	 */
	public function delete() {
		unlink( $this->filename );
	}

	/**
	 * Check wether a file readable 
	 *
	 * @return boolean
	 */
	public function isReadable() {
		return is_readable( $this->filename );
	}

	/**
	 * Check wether a file is writeable	
	 *
	 * @return boolean
 	 */
	public function isWriteable() {
		return is_writeable( $this->filename );
	}

	/**
	 * Check whether this file is a regular file.
	 *
	 * @return boolean
	 */
	public function isFile() {
		return is_file( $this->filename );
	}

	/**
	 * Gets file modification time
	 */
	public function getMTime() {
		return filemtime( $this->filename );
	}

	/**
	 * Checks wether this File is newer than the given file.
	 *
	 * @param File $file
	 * @return boolean
	 */
	public function isNewerThan( File $file ) {
		return $this->getMTime() > $file->getMTime();
	}

	/**
	 * Checks wether this File is older than the given file.
	 *
	 * @param File $file
	 * @return boolean
	 */
	public function isOlderThan( File $file ) {
		return $this->getMTime() < $file->getMTime();
	}

	/**
	 * Gets the directory path for this file
	 *
	 * @return string
	 */
	public function getPath() {
		return dirname( realpath( $this->filename ) );
	}

	/**
	 * Gets this file extension
	 *
	 * @return string
	 */
	public function getExtension() {
		return pathinfo( $this->filename, PATHINFO_EXTENSION );
	}

	/**
	 * Returns a string representation of this file (the actual filename)
	 *
	 * @return string
	 */
	public function __toString() {
		return strval($this->filename);
	}

}
