<?php
namespace sql;

use core\CoreException;

/**
 * Class SQLException
 */
class SQLException extends CoreException {

	private $query;
    private $errorCode;
	/**
	 * Instantiates a SQLException object with the default message and an optional SQL Error Number.
	 *
	 * @param string $message The Exception message
	 * @param int $errno Optional SQL error number
	 * @param string $sql Optional SQL Query
	 */
	public function __construct( $message, $errno=0, $query=null ) {
		$this->message = $message;
        $this->errorCode = $errno;
		if ( $errno > 0 ) {
			$this->message = sprintf( 'DB Error (%d): %s', $errno, $message );
		}
		if ( $query != null ) {
			$this->query = $query;
		}
	}

	/**
	 * Return the query that raised the exception, if available.
	 */
	public function getQuery() {
		return $this->query;
	}

    /**
     * Return the server error code that raised the exception, if available.
     */
    public function getErrorCode() {
        return $this->errorCode;
    }
}
