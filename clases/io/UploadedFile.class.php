<?php

namespace io;

/**
 * Class for uploaded files,   
 */
class UploadedFile extends File {

	/**
	 * File name as uploaded by the client
	 */
	private $clientName;

	/**
	 * File mime type
	 */
	private $type;

	/**
	 * Uploading error code
	 */
	private $error;

	/**
	 * Uploaded file size.
	 */
	private $size;

	/**
	 * Error messages
	 */
	private $messages = array(
			UPLOAD_ERR_OK => 'File Uploaded OK'
			, UPLOAD_ERR_INI_SIZE => 'Max ini file size exceeded'
			, UPLOAD_ERR_FORM_SIZE => 'Max form file size exceeded'
			, UPLOAD_ERR_PARTIAL => 'File only partially uploaded'
			, UPLOAD_ERR_NO_FILE => 'No file was uploaded'
			, UPLOAD_ERR_NO_TMP_DIR => 'Temporary directory missing'
			, UPLOAD_ERR_CANT_WRITE => 'Could not write file to temporary dir'
			, UPLOAD_ERR_EXTENSION => 'File extension not allowed'
		);
		
	/**
	 * Instantiates a new UploadedFile object for a standard php $_FILES array item.
	 *
	 * Array properties:
	 * - tmp_name: Temporal file name where php stored the uploaded file
	 * - name: Client file name
	 * - type: File mime type
	 * - error: Uploading error code
	 * - size: File size
	 * 
	 * @param array $file
	 */
	public function __construct( array $file ) {
		parent::__construct( $file['tmp_name'] );
		$this->clientName = $file['name'];
		$this->error = $file['error'];
		$this->type = $file['type'];
		$this->size = $file['size'];
	}

	/**
	 * Gets the uploaded file client file name
	 *
	 * @return string
	 */
	public function getClientName() {
		return $this->clientName;
	}

	/**
	 * Gets the file mime type
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Returns the file size.
	 *
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * Returns the temporary file name for this uplodaed file, as a File object
	 *
	 * @return File
	 */
	public function getFile() {
		return new File( $this->filename );
	}

	/**
	 * Gets the uploading process error message
	 *
	 * @return int
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Gets the error message 
	 *
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->messages[$this->error];
	}

	/**
	 * Moves the uploaded file to the target File location
	 *
	 * @param File $target
	 */
	public function move( File $target ) {
		move_uploaded_file( $this->filename, $target->getName() );
	}

	/**
	 * Returns a string representation of this UploadedFile, with all its properties.
	 *
	 * @return string
	 */
	public function __toString() {
		$str = parent::__toString();
		$str = sprintf( "[%s (ID #%s)]\n", get_class( $this ), $this->id() );
		$str.= sprintf( " - Name : %s\n", $this->getClientName() );
		$str.= sprintf( " - Type : %s\n", $this->getType() );
		$str.= sprintf( " - Size : %s\n", $this->getSize() );
		$str.= sprintf( " - File : %s\n", $this->filename );
		$str.= sprintf( " - Error: %s (%s)\n", $this->getError(), $this->getErrorMessage() );
		return $str;
	}
		
}
