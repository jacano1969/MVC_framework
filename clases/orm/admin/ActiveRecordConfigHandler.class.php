<?php

namespace orm\admin;

use mvc\AppConfigHandler;

/**
 * Implements a AppConfigHandler for the ActiveRecordGenerator.
 */
class ActiveRecordConfigHandler extends AppConfigHandler {

	const ORM_FLAG = 'enabled';
	const ORM_NAMESPACE = 'namespace';
	const CLASSPATH = 'classpath';
	const CAMEL_CASE = 'camel-case';
	const FOREIGN_KEY = 'foreign-key';
	const OVERWRITE = 'overwrite';
	const I18N_STRATEGY = 'i18n-strategy';

	protected $section = 'orm-admin';

	protected $config = array(
			  self::ORM_FLAG => 'true'
			, self::ORM_NAMESPACE => 'orm'
			, self::CLASSPATH => 'proyectos/SVN/classes/orm'
			, self::CAMEL_CASE => 'false'
			, self::FOREIGN_KEY => 'true '
			, self::OVERWRITE => 'true'
			, self::I18N_STRATEGY => null
		);

}

