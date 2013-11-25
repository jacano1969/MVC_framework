<?php
namespace app;

use mvc\App;
use mvc\Exception;
use mvc\Model;
use util\Hashtable;
use sql\ConnectionFactory;
use orm\admin\ActiveRecordGenerator;

class TableController extends OrmAdminController {

	public function defaultAction( Model $model, Hashtable $args ) {

		if ( !$args->schema || !$args->table ) {
			throw new Exception( sprintf( 'No schema and/or table supplied' ) );
		}

		$generator = new ActiveRecordGenerator( App::getContext() );

		$model->addChild( 'generator' );
		$model->generator['namespace'] = $generator->getNamespace();
		$model->generator['camel-case'] = $generator->getCamelCase() ? 'true' : 'false';
		$model->generator['foreign-keys'] = $generator->getForeignKeys() ? 'true' : 'false';
		$model->generator['overwrite'] = $generator->getOverwrite() ? 'true' : 'false';
		$model->generator['i18n-strategy'] = $generator->getI18NStrategy();

		$this->loadClasses( $generator );
		$table = ConnectionFactory::getDefault()->getTable( $args->schema, $args->table );
		$this->attachTable( $table, $model );
	}

	public function generateAction( Model $model, Hashtable $args ) {
		if ( $args->schema && $args->table && $args->class_name ) {
			$conn = ConnectionFactory::getDefault();
			if ( $table = $conn->getTable( $args->schema, $args->table ) ) {
				try {
					$generator = new ActiveRecordGenerator( App::getContext() );
					switch( $args->type ) {
						case 'orm':
							$file = $generator->generateClass( $table, $args->class_name );
							$model->addOkMsg( sprintf( 'Class "%s" has been generated on file: "%s"', $args->class_name, $file ) );
							break;
						case 'base':
							$file = $generator->generateBaseClass( $table, $args->class_name, $args->fk, $args->fk_name, $args->fk_class, $args->localized == 'on' );
							$model->addOkMsg( sprintf( 'Base Class "%s" has been generated on file: "%s"', $args->class_name, $file ) );
							break;
						case 'both':
							$file1 = $generator->generateClass( $table, $args->class_name );
							$file2 = $generator->generateBaseClass( $table, $args->class_name, $args->fk, $args->fk_name, $args->fk_class, $args->localized == 'on' );
							$model->addOkMsg( sprintf( 'Class "%s" and its base have been generated on files: "%s", "%s"', $args->class_name, $file1, $file2 ) );
							break;
					}

				} catch ( Exception $e ) {
					$error = sprintf( '[%d] %s', self::ERROR_GENERATOR, $e->getMessage() );
					$err = $model->addError( $error );
					foreach( $e->getErrors() as $type => $error ) {
						$msg = $err->add( 'error' );
						$msg['type'] = $type;
						$msg['error'] = App::encode( $error );
					}
				}
			} else {
				$model->addError( sprintf( '[%d] Table not found', self::ERROR_TABLE_NOT_FOUND ) );
			}
		} else {
			$model->addError( sprintf( '[%d] Must supply database, table and class name', self::ERROR_ARGS ) );
		}
	}

}
