<?php

namespace orm\admin;

use core\CoreException;

/**
 * ActiveRecordGeneratorException
 */
class ActiveRecordGeneratorException extends CoreException {

	const ERROR_CONFIG = 1;

	/**
	 * Array of generator errors.
	 */
	protected $errors = array();

	public function addError( $type, $error ) {
		$this->errors[$type] = $error;
	}

	public function getErrors() {
		return $this->errors;
	}

}
