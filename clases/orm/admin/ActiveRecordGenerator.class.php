<?php

namespace orm\admin;

use core\Object;
use io\File;
use core\CoreException;
use mvc\App;
use mvc\AppContext;
use sql\schema\Table;
use util\Factory;
use util\Date;
use i18n\DateFormat;

/**
 * ActiveRecordGenerator
 */
class ActiveRecordGenerator extends Object {

	/**
	 * ActiveRecord generation namespace
	 */
	protected $namespace = null;

	/**
	 * camelCase ActiveRecord fields setting
	 */
	protected $camelCase = true;

	/**
	 * ForeignKey ActiveRecord support
	 */
	protected $foreignKeys = true;

	/**
	 * Overwrite ActiveRecords setting
	 */
	protected $overwrite = false;

	/**
	 * Internationalization strategy
	 */
	protected $i18nStrategy = null;

	/**
	 * Instantiates a new ActiveRecordGenerator for the supplied AppContext.
	 *
	 * Configuration is loaded from the AppContext Config.
	 *
	 * @param AppContext $context
	 */
	public function __construct( AppContext $context ) {
		$this->loadConfiguration( $context );
	}

	public function getNamespace() {
		return $this->namespace;
	}

	public function getCamelCase() {
		return $this->camelCase;
	}

	public function getForeignKeys() {
		return $this->foreignKeys;
	}

	public function getOverwrite() {
		return $this->overwrite;
	}

	public function getI18NStrategy() {
		return $this->i18nStrategy;
	}

	public function getClasses() {
		list( $ormPath, $basePath ) = $this->getPaths();
		$classes = array();
		foreach( \load_path_classes( $ormPath ) as $class ) {
			$ref = new \ReflectionClass( $class );
			foreach( $ref->getStaticProperties() as $prop => $value ) {
				switch( $prop ) {
					case 'schema': $schema = $value; break;
					case 'table': $table = $value; break;
					case 'fields': $fields = $value; break;
					case 'pkFields': $pkFields = $value; break;
					case 'fkFields': $fkFields = $value; break;
				}
			}

			foreach( $ref->getConstants() as $const => $name ) {
				if ( isset( $fkFields[$name] ) ) {
					$fkFields[$name]['const'] = strtolower( $const );
				}
			}

			$methods = array();
			foreach( $ref->getMethods() as $m ) {
				if ( $m->getDeclaringClass()->getName() === $ref->getName() ) {
					$params = array();
					foreach( $m->getParameters() as $p ) {
						$param['name'] = $p->getName();
						if ( $p->isOptional() ) {
							if ( $p->getDefaultValue() === null ) $default = 'null';
							elseif ( $p->getDefaultValue() === true ) $default = 'true';
							elseif ( $p->getDefaultValue() === false ) $default = 'false';
							else $default = $p->getDefaultValue();
						} else {
							$default = null;
						}
						$param['default'] = $default;
						$params[] = $param;
					}
					$access = $m->isPublic() ? 'public' : ( $m->isProtected() ? 'protected' : 'private' );
					$methods[$m->getName()]['access'] = $access;
					$methods[$m->getName()]['static'] = $m->isStatic() ? 'true' : 'false';
					$methods[$m->getName()]['abstract'] = $m->isAbstract() ? 'true' : 'false';
					$methods[$m->getName()]['params'] = $params;
				}
			}

			$file = new File( $ref->getFileName() );
			$date = new Date( $file->getMTime() );

			$classes[$table] = array(
					'class' => $class
					, 'base' => $ref->getParentClass()->getParentClass()->getName()
					, 'file' => $file->getName()
					, 'date' => $date->format( DateFormat::DATE_FORMAT_WITH_TIME )
					, 'schema' => $schema
					, 'table' => $table
					, 'fields' => $fields
					, 'pk' => $pkFields
					, 'fk' => $fkFields
					, 'methods' => $methods
				);
		}
		return $classes;
	}

	public function generateClass( Table $table, $class ) {
		list( $ormPath, $basePath ) = $this->getPaths();
		$file = new File( sprintf( '%s/%s.class.php', $ormPath, $class ) );

		try {
			$writer = $file->getWriter();
			$writer->write( "<?php\n" );
			$writer->write( $this->getFileComment() );
			$writer->write( $this->getImports() );
			$writer->write( $this->getComment( $class ) );
			$writer->write( $this->getBody( $class ) );
			$writer->close();
			return $file;
		} catch( CoreException $e ) {
			throw new ActiveRecordGeneratorException( sprintf( 'Could not generate class. Error: %s', $e->getMessage() ) ); 
		}
	}

	public function generateBaseClass( Table $table, $class, array $fkIncludes=null, array $fkNames=null, array $fkClasses=null, $localized=false ) {
		list( $ormPath, $basePath ) = $this->getPaths();
		$file = new File( sprintf( '%s/%s.class.php', $basePath, $class ) );

		$baseClass = $localized ? 'LocalizedActiveRecord' : 'ActiveRecord';

		try {
			$writer = $file->getWriter();
			$writer->write( "<?php\n" );
			$writer->write( $this->getFileComment() );
			$writer->write( $this->getBaseImports( $baseClass ) );
			$writer->write( $this->getBaseComment( $class ) );
			$writer->write( $this->getBaseHeader( $class, $baseClass ) );
			$writer->write( $this->getBaseFields( $class, $table, $fkIncludes == null ? array() : $fkIncludes, $fkNames == null ? array() : $fkNames, $fkClasses == null ? array() : $fkClasses, $localized ) );
			$writer->write( $this->getBaseStatic( $class, $table ) );
			$writer->write( $this->getBaseFooter() );
			$writer->close();
			return $file;
		} catch( \Exception $e ) {
			throw new ActiveRecordGeneratorException( sprintf( 'Could not generate class. Error: %s', $e->getMessage() ) ); 
		}
	}

	private function getPaths() {
		$paths = \get_classpath();
		if ( !isset( $paths[1] ) ) {
			throw new ActiveRecordGeneratorException( sprintf( 'No application classpath defined other than MVC framework base classpath: %s', join( ',', $paths ) ) );
		}
		$ormPath = sprintf( '%s/%s', $paths[1], str_replace( '\\', DIRECTORY_SEPARATOR, $this->namespace ) );
		$basePath = sprintf( '%s/base', $ormPath );
		if ( !is_dir( $ormPath ) && !@mkdir( $ormPath, 0777, true ) ) {
			$err = error_get_last();
			throw new ActiveRecordGeneratorException( sprintf( 'Could not create ActiveRecord classes path: %s. Error: %s', $ormPath, $err['message'] ) );
		}
		if ( !is_dir( $basePath ) && !@mkdir( $basePath, 0777, true ) ) {
			$err = error_get_last();
			throw new ActiveRecordGeneratorException( sprintf( 'Could not create ActiveRecord base classes path: %s. Error: %s', $ormPath, $err['message'] ) );
		}

		return array( $ormPath, $basePath );
	}

	private function loadConfiguration( AppContext $context ) {
		$conf = new ActiveRecordConfigHandler( $context->getConfig() );
		if ( $ns = $conf->getValue( $conf::ORM_NAMESPACE ) ) {
			$this->namespace = $ns;
		} else {
			// Check if we have an "orm" Factory for this context
			if ( Factory::isRegistered( 'orm' ) ) {
				$ns = Factory::orm()->getNamespaces();
				if ( !isset( $ns[0] ) ) {
					throw new ActiveRecordGeneratorException( 'Factory "orm" does not have any namespaces registered' );
				}
				$this->namespace = $ns[0];
			} else {
				$this->namespace = 'app\orm';
			}
		}

		if ( $cc = $conf->getValue( $conf::CAMEL_CASE ) ) {
			$this->camelCase = ( $cc === 'true' );
		} 

		if ( $fk = $conf->getValue( $conf::FOREIGN_KEY ) ) {
			$this->foreignKey = ( $fk === 'true' );
		}

		if ( $ow = $conf->getValue( $conf::OVERWRITE ) ) {
			$this->overwrite = ( $ow === 'true' );
		}

		if ( $strat = $conf->getValue( $conf::I18N_STRATEGY ) ) {
			if ( !class_exists( $strat ) ) {
				throw new ActiveRecordGeneratorException( sprintf( 'Invalid I18NStrategy class: %s. Class not found', $strat ) );
			}
			$ref = new \ReflectionClass( $strat );
			if ( !$ref->implementsInterface( 'php\orm\i18n\I18NStrategy' ) ) {
				throw new ActiveRecordGeneratorException( sprintf( 'Invalid I18NStrategy class: %s. Class does not implement php\orm\i18n\I18NStrategy', $strat ) );
			}
			$this->i18nStrategy = $strat;
		}
	}

	private function getFileComment() {
		$str = "/**\n";
		$str.= " * This file has been auto-generated by ORM Generator\n";
		$str.= " * \n";
		$str.= " * @version \$Rev\$\n";
		/*
		$str.= sprintf( " * @author %s\n", $this->author );
		if ( $this->copyright != '' ) {
			$str.= sprintf( " * @copyright %s\n", $this->copyright );
		}
		 */
		$str.= " */\n\n";
		return $str;
	}

	private function getImports() {
		$str = sprintf( "namespace %s;\n\n", $this->namespace );
		return $str;
	}

	private function getComment( $class ) {
		$str = "/**\n";
		$str.= sprintf( " * Auto-generated %s class. Add your code here.\n", $class );
		$str.= " * \n";
		/*
		foreach( explode( "\\\\", $this->arNS ) as $i => $ns ) {
			$str.= sprintf( " * @%s %s\n", ( $i == 0 ? 'package' : 'subpackage' ), $ns );
		}
		 */
		$str.= " */\n";
		return $str;
	}

	private function getBody( $class ) {
		$str = sprintf( "class %s extends \\%s\\base\\%s {\n\n", $class, $this->namespace, $class );
		$str.= "}\n";
		return $str;
	}

	private function getBaseImports( $baseClass ) {
		$str = sprintf( "namespace %s\\base;\n\n", $this->namespace );
		$str.= "use \mvc\\App;\n";
		$str.= "use \sql\\ConnectionFactory;\n";
		$str.= sprintf( "use \orm\\%s;\n\n", $baseClass );
		return $str;
	}

	private function getBaseComment( $class ) {
		$str = "/**\n";
		$str.= sprintf( " * Auto-generated %s class. DO NOT MODIFY THIS FILE. It will be recreated each time you regenerate the ORM.\n", $class );
		$str.= " *\n";
		/*
		foreach( explode( "\\\\", $this->S ) as $i => $pck ) {
			$str.= sprintf( " * @%s %s\n", ( $i == 0 ? 'package' : 'subpackage' ), $pck );
		}
		 */
		$str.= " */\n";
		return $str;
	}

	private function getBaseHeader( $class, $baseClass ) {
		return sprintf( "class %s extends %s {\n\n", $class, $baseClass );
	}

	private function getBaseFields( $class, Table $table, array $fkIncludes, array $fkNames, array $fkClasses, $localized ) {
		$str = "";

		$maxlen = 0;
		foreach( $table->getColumns() as $col ) {
			if ( strlen( $col->getName() ) > $maxlen ) $maxlen = strlen( $col->getName() ) + 2;
		}

		if ( $localized ) {
			$strat = $this->i18nStrategy;
			$i18nCols = $strat::getI18NFields( $table );
		} else {
			$i18nCols = array();
		}

		if ( sizeof( $i18nCols ) ) foreach( $i18nCols as $col ) {
			if ( strlen( $col->getName() ) > $maxlen ) $maxlen = strlen( $col->getName() ) + 2;
		}

		$fks = $table->getForeignKeys();
		foreach( $fks as $name => $fk ) {
			if ( isset( $fkIncludes[$name] ) && $fkIncludes[$name] == 'on' ) {
				$fkName = $fkNames[$name];
				$str.= sprintf( "\tconst %-{$maxlen}s = '%s';\n", 'FK_'.strtoupper( $name ), $this->getCamelCased( $fkName, true ) );
			}
		}
		$str.= "\n";

		$str.= "\tprotected static \$conn = null;\n\n";

		$str.= sprintf( "\tprotected static \$schema = '%s';\n\n", $table->getSchema() );

		$str.= sprintf( "\tprotected static \$table = '%s';\n\n", $table->getName() );

		if ( $localized ) {
			$str.= sprintf( "\tprotected static \$strategy = '%s';\n\n", $this->i18nStrategy );
		}

		$str.= "\tprotected static \$fields = array(\n";
		foreach( $table->getColumns() as $i => $col ) {
			if ( $i > 0 ) $s = ', ';
			else $s = '  ';
			$f = $col->getName();
			$str.= sprintf( "\t\t\t{$s}%-{$maxlen}s => %s\n", sprintf( "'%s'", $this->camelCase ? $this->getCamelCased( $f ) : $f ), "'$f'" );
		}
		$str.= "\t\t\t);\n\n";

		if ( sizeof( $i18nCols ) ) {
			$str.= "\tprotected static \$i18nFields = array(\n";
			foreach( $i18nCols as $i => $col ) {
				if ( $i > 0 ) $s = ', ';
				else $s = '  ';
				$f = $col->getName();
				$str.= sprintf( "\t\t\t{$s}%-{$maxlen}s => %s\n", sprintf( "'%s'", $this->camelCase ? $this->getCamelCased( $f ) : $f ), "'$f'" );
			}
			$str.= "\t\t\t);\n\n";
		}

		$str.= "\tprotected static \$pkFields = array(\n";
		foreach( $table->getPrimaryKeys() as $i => $col ) {
			if ( $i > 0 ) $s = ', ';
			else $s = '  ';
			$f = $col->getName();
			$str.= sprintf( "\t\t\t{$s}%-{$maxlen}s\n", sprintf( "'%s'", $this->camelCase ? $this->getCamelCased( $f ) : $f ) );
		}
		$str.= "\t\t\t);\n\n";

		$str.= "\tprotected static \$fkFields = array(\n";
		$i = 0;
		foreach( $fks as $name => $fk ) {
			if ( isset( $fkIncludes[$name] ) && $fkIncludes[$name] == 'on' ) {
				if ( $i > 0 ) $s = ', ';
				else $s = '  ';

				$cc_local = array();
				$uc_foreign = array();
				$cc_foreign = array();
				foreach( $fk as $cols ) {
					$cc_local[] = $this->getCamelCased( $cols['local']->getName() );
					$uc_foreign[] = strtoupper( $cols['foreign']->getName() );
					$cc_foreign[] = $this->getCamelCased( $cols['foreign']->getName() );
				}

				$fk_class = sprintf( '\\%s\\%s', $this->namespace, $this->getCamelCased( $fkClasses[$name], true ) );

				$fk_array = sprintf( "array(\n", strtoupper( $name ) );
				$fk_array.= sprintf( "\t\t\t\t\t\t  'localFields'     => array( '%s' )\n", implode( "', '", $cc_local ) );
				$fk_array.= sprintf( "\t\t\t\t\t\t, 'foreignClass'    => '%s'\n", $fk_class );
				$fk_array.= sprintf( "\t\t\t\t\t\t, 'foreignFields'   => array( '%s' )\n", implode( "', '", $cc_foreign ) );
				$fk_array.= "\t\t\t\t\t\t)";

				$str.= sprintf( "\t\t\t{$s}self::%-{$maxlen}s => %s\n", 'FK_'.strtoupper( $name ), $fk_array );
				$i++;
			}
		}
		$str.= "\t\t\t);\n\n";

		$str.= "\tprotected static \$sequences = array(\n";
		$i=0;
		foreach( $table->getSequences() as $name => $seq ) {
			if ( $i > 0 ) $s = ', ';
			else $s = '  ';
			$str.= sprintf( "\t\t\t{$s}'%-{$maxlen}s' => %s\n", $name, sprintf( "'%s'", $this->getCamelCased( $seq->getName() ) ) );
			$i++;
		}
		$str.= "\t\t\t);\n\n";

		$str.= "\tprotected \$values = array(\n";
		foreach( $table->getColumns() as $i => $col ) {
			if ( $i > 0 ) $s = ', ';
			else $s = '  ';
			$str.= sprintf( "\t\t\t{$s}%-{$maxlen}s => null\n", sprintf( "'%s'", $this->getCamelCased( $col->getName() ) ) );
		}
		$str.= "\t\t\t);\n\n";

		if ( sizeof( $i18nCols ) ) {
			$str.= "\tprotected \$i18nValues = array(\n";
			foreach( $i18nCols as $i => $col ) {
				if ( $i > 0 ) $s = ', ';
				else $s = '  ';
				$str.= sprintf( "\t\t\t{$s}%-{$maxlen}s => null\n", sprintf( "'%s'", $this->getCamelCased( $col->getName() ) ) );
			}
			$str.= "\t\t\t);\n\n";
		}

		$str.= "\tprotected \$fkValues = array(\n";
		$i = 0;
		foreach( $table->getForeignKeys() as $name => $fk ) {
			if ( isset( $fkIncludes[$name] ) && $fkIncludes[$name] == 'on' ) {
				if ( $i > 0 ) $s = ', ';
				else $s = '  ';
				$str.= sprintf( "\t\t\t{$s}self::%-{$maxlen}s => null\n", 'FK_'.strtoupper( $name ) );
				$i++;
			}
		}
		$str.= "\t\t\t);\n\n";

		return $str;
	}

	private function getBaseStatic( $class, Table $table ) {
		$str = "\t/**\n";
		$str.= "\t * Performs static initialization\n";
		$str.= "\t */\n";
		$str.= "\tpublic static function __static() {\n";
		$str.= sprintf( "\t\tself::\$conn = ConnectionFactory::getConnection('%s');\n", $table->getConnection()->getName() );
		$str.= "\t}\n\n";
		return $str;
	}

	private function getBaseFooter() {
		$str = "}\n";
		return $str;
	}

	private function getCamelCased( $field, $upperFirst=false ) {
		$cc = str_replace( '_', ' ', $field );
		$cc = ucwords( $cc );
		$cc = str_replace( ' ', '', $cc );
		if ( !$upperFirst ) {
			$cc = strtolower( substr( $cc, 0, 1 ) ).substr( $cc, 1 );
		}
		return $cc;
	}

}
