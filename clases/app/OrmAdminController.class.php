<?php

namespace app;

use mvc\App;
use mvc\Model;
use mvc\Controller;
use util\Hashtable;
use sql\ConnectionFactory;
use sql\schema\Table;
use orm\admin\ActiveRecordGenerator;

/**
 * ORM Generation Page Controller
 */
class OrmAdminController extends Controller {

	const ERROR_ARGS = 1;
	const ERROR_TABLE_NOT_FOUND = 2;
	const ERROR_GENERATOR = 4;

	/**
	 * Array of generated classes. This array is filled using the generator, reading all classes in the orm path.
	 */
	protected $classes = array();

	public function defaultAction( Model $model, Hashtable $args ) {
		$this->loadClasses( new ActiveRecordGenerator( App::getContext() ) );
		$tables = ConnectionFactory::getDefault()->getTables();
		foreach( $tables as $t ) {
			$this->attachTable( $t, $model->add( 'tables' ) );
		}
	}

	protected function getPrintSize( $size ) {
		if ( $size > 1048576 ) {
			return sprintf( '%1.2fMB', $size / 1048576 );
		} else {
			return sprintf( '%1.2fKB', $size / 1024 );
		}
	}

	protected function loadClasses( ActiveRecordGenerator $generator ) {
		$this->classes = $generator->getClasses();
	}

	protected function attachTable( Table $table, Model $model ) {
		$tChild = $model->add( 'table' );
		$tChild['schema'] = $table->getSchema();
		$tChild['name'] = $table->getName();
		$tChild['type'] = $table->getType();
		$tChild['count'] = $table->getRecordCount();
		$tChild['size'] = $table->getSize();
		$tChild['_size'] = $this->getPrintSize( $table->getSize() );

		$primary=array();
		$tChild->add( 'primary-key' );
		foreach( $table->getPrimaryKeys() as $c ) {
			$pkChild = $tChild->{'primary-key'}->add( 'column' );
			$pkChild['name'] = $c->getName();
			$primary[]=$c->getName();
		}
		$tChild->add( 'columns' );
		$columns = $table->getColumns();
		foreach( $columns as $c ) {
			$cChild = $tChild->columns->add( 'column' );
			$cChild['name'] = $c->getName();
			$cChild['type'] = $c->getType();
			$cChild['null'] = $c->getNull() ? 'true' : 'false';
			$cChild['nullstring'] = $c->getNull() ? 'Si' : 'No';
			$cChild['primary-key'] = in_array($c->getName(),$primary)? 'PRI' : '';
		}


		$tChild->add( 'constraints' );
		foreach( $table->getForeignKeys() as $name => $fk ) {
			$cnChild = $tChild->{'constraints'}->add( 'foreign-key' );
			$cnChild['name'] = $name;
			foreach( $fk as $cols ) {
				$cChild = $cnChild->add( 'column' );
				$cChild['local'] = $cols['local']->getName();
				$cChild['foreign-schema'] = $cols['foreign']->getSchema();
				$cChild['foreign-table'] = $cols['foreign']->getTable();
				$cChild['foreign-name'] = $cols['foreign']->getName();
			}
		}
		$tChild->add( 'class' );
		if ( array_key_exists( $table->getName(), $this->classes ) ) {
			$class = $this->classes[$table->getName()];
			$name = explode( '\\', $class['class'] );
			$base = explode( '\\', $class['base'] );
			$tChild->class['name'] = array_pop( $name );
			$tChild->class['base'] = array_pop( $base );
			$tChild->class['full-name'] = $class['class'];
			$tChild->class['file'] = $class['file'];
			$tChild->class['date'] = $class['date'];
			foreach( $class['fields'] as $field => $column ) {
				$fChild = $tChild->class->add( 'field' );
				$fChild['name'] = $field;
			}
			$tChild->class->add( 'primary-key' );
			foreach( $class['pk'] as $column ) {
				$cChild = $tChild->class->{'primary-key'}->add( 'column' );
				$cChild['name'] = $column;
			}
			foreach( $class['fk'] as $name => $fk ) {
				$fkChild = $tChild->class->addChild( 'foreign-key' );
				$fkChild['const'] = $fk['const'];
				$fkChild['name'] = $name;
				$fkChild['foreign-class'] = substr( $fk['foreignClass'], strrpos( $fk['foreignClass'], '\\' ) + 1 );
				foreach( $fk['localFields'] as $i => $field ) {
					$cChild = $fkChild->add( 'column' );
					$cChild['local'] = $field;
					$cChild['foreign'] = $fk['foreignFields'][$i];
				}
			}
			foreach( $class['methods'] as $name => $props ) {
				$mChild = $tChild->class->add( 'method' );
				$mChild['name'] = $name;
				$mChild['access'] = $props['access'];
				$mChild['static'] = $props['static'];
				$mChild['abstract'] = $props['abstract'];
				$mChild->add( 'params' );
				foreach( $props['params'] as $p ) {
					$pChild = $mChild->params->add( 'param' );
					$pChild['name'] = $p['name'];
					$pChild['default'] = $p['default'];
				}
			}
		}
	}
}
