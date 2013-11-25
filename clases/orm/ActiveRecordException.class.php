<?php

namespace orm;

use core\CoreException;

/**
 * ActiveRecordException
 */
class ActiveRecordException extends CoreException {

	public function __construct( $message, $sql = "" ) {
		if ($sql) {
			parent::__construct( sprintf("%s (SQL:%s)", $message, $sql ) );
		}
		else {
			parent::__construct( $message );
		}
	}
}
