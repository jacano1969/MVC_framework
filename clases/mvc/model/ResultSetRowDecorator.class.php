<?php

namespace mvc\model;

use mvc\Model;
use sql\ResultSetRow;

/**
 * This Decorator can be applied to ResultSetRow attached to the Model.
 */
interface ResultSetRowDecorator {

	public function decorate( Model $model, ResultSetRow $row );

}
