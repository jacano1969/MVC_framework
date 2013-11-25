<?php

namespace mvc\model;

use mvc\Model;
use orm\ActiveRecord;

/**
 * This Decorator can be applied to ActiveRecord attached to the Model.
 */
interface ActiveRecordDecorator {

	public function decorate( Model $model, ActiveRecord $record );

}
