<?php

namespace mvc\model;

use mvc\Model;
use ds\DataSourceRow;

/**
 * This Decorator can be applied to DataSourceRow attached to the Model.
 */
interface DataSourceRowDecorator {

	public function decorate( Model $model, DataSourceRow $row );

}
