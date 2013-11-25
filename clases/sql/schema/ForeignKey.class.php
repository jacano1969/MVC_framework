<?php

namespace sql\schema;

use core\Object;
use sql\Connection;

/**
 * Represents a database schema Foreign Key. A ForeignKey is a column implementing Constraint, with the constraint name.
 */
class ForeignKey extends Column {

	protected $constraint;

	public function __construct( Connection $conn, $constraint, $db, $table, $name ) {
		parent::__construct( $conn, $db, $table, $name );
		$this->constraint = $constraint;
	}

	public function getConstraint() {
		return $this->constraint;
	}
}
