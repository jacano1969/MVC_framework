<?php
/**
 * This file is part of MVC framework
 *
 * MVC framework is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * MVC framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MVC framework; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @author $Author$
 * @version $Rev$
 * @updated $Date$
 *

 */

namespace mvc;

use core\Object;
use mvc\App;
use util\Date;
use util\Hashtable;
use sql\ResultSet;
use sql\ResultSetRow;
use orm\ActiveRecord;
use orm\ActiveRecordIterator;
use ds\DataSource;
use ds\DataSourceRow;
use mvc\model\ActiveRecordDecorator;
use mvc\model\ResultSetRowDecorator;
use mvc\model\DataSourceRowDecorator;

/**
 * The Model class extends SimpleXMLElement to provide extra convenience functionality.
 * Model also allows to set raw data, with type and optional name, for each model node, using the
 * SimpleXMLElement extending hack devised by Loïc Hoguin here: http://blog.extend.ws/2008/02/20/extending-simplexml/
 */
class Model extends \SimpleXMLElement {

	/**
	 * Raw data
	 *
	 * @var
	 */
	protected static $data = null;

	/**
	 * Raw data type
	 *
	 * @var
	 */
	protected static $dataType = null;

	/**
	 * Raw data name
	 * 
	 * @var
	 */
	protected static $dataName = null;

	const MSG_CUSTOM = 'custom';
	const MSG_INFO = 'info';
	const MSG_OK = 'ok';
	const MSG_WARN = 'warn';
	const MSG_ERROR = 'error';

	/**
	 * Gets a new Model instance for the supplied $app
	 *
	 * @param App $app
	 */
	public static function getInstance( AppRequest $request ) {
		return new static( sprintf( '<app request="%s" />', $request->getId() ) );
	}

	/**
	 * Gets the app node.
	 *
	 * @return string
	 */
	public function getAppNode() {
		$app = $this->xpath( '/app' );
		return $app[0];
	}

	/**
	 * Sets the provided data as raw data for this model.
	 * This method is useful for views that return raw data, such as DataView or ImageView. 
	 *
	 * @param mixed $data The raw data.
	 * @param string $type The data mime type.
	 * @param string $name Optional data name.
	 * @return void
	 */
	public static function setData( $data, $type, $name=null ) {

		static::$data = $data;
		static::$dataType = $type;
		if ( $name ) {
			static::$dataName = $name;
		}
	}

	/**
	 * Gets the raw data for this model.
	 *
	 * @return mixed
	 */
	public static function getData() {
        oprint(static::$data);
		return static::$data;
	}

	/**
	 * Sets the raw data type for this model.
	 *
	 * @static
	 * @param  $type
	 * @return void
	 */
	public static function setDataType( $type ) {
		static::$dataType = $type;
	}

	/**
	 * Gets the raw data type for this model.
	 * 
	 * @static
	 * @return string
	 */
	public static function getDataType() {
		return static::$dataType;
	}

	/**
	 * Gets the raw data name for this model.
	 *
	 * @static
	 * @return string
	 */
	public static function getDataName() {
		return static::$dataName;
	}

	/**
	 * Sets the raw dat name for this model.
	 *
	 * @param  $name
	 * @return void
	 */
	public function setDataName( $name ) {
		static::$dataName = $name;
	}

	/**
	 * Alias to addChild.
	 *
	 * @param string $name
	 * @param string $value=null
	 * @return Model
	 */
	public function add( $name, $value=null ) {
		return $this->addChild( $name, $value );
	}

	/**
	 * Checks whether this model node has a child with the supplied name
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function has( $name ) {
		return $this->$name && gettype( $this->$name ) !== null;
	}

	/**
	 * Adds a message to the model with the supplied type
	 *
	 * @param string $msg
	 * @param string $type
	 */
	public function addMessage( $msg, $type ) {
		$app = $this->getAppNode();
		if ( !$app->has( 'messages' ) ) {
			$app->add( 'messages' );
		}
		$item = $app->messages->add( 'message' );
		$item['type'] = $type;
		$item['msg'] = App::encode( $msg );
		return $item;
	}

	/**
	 * Adds an error message to the Model
	 *
	 * @param string $msg
	 */
	public function addError( $msg ) {
		return $this->addMessage( $msg, self::MSG_ERROR );
	}

	/**
	 * Adds a warning message to the model
	 *
	 * @param string $msg
	 */
	public function addWarn( $msg ) {
		return $this->addMessage( $msg, self::MSG_WARN );
	}

	/**
	 * Adds an info message to the Model
	 *
	 * @param string $msg
	 */
	public function addInfo( $msg ) {
		return $this->addMessage( $msg, self::MSG_INFO );
	}

	/**
	 * Adds an ok message to the Model
	 *
	 * @param string $msg
	 */
	public function addOkMsg( $msg ) {
		return $this->addMessage( $msg, self::MSG_OK );
	}

	/**
	 * Adds another Model to this model. This method aggregates both models, getting all messages, exceptions and data.
	 * The app/request metadata nodes remain untouched
	 *
	 * @param Model $model
	 */
	public function addModel( Model $model ) {
		foreach( $model->children() as $node ) {
			$n = $this->add( $node->getName() );
			foreach( $node->attributes() as $attr => $value ) {
				$n[$attr] = (string)$value;
			}
			$n->addModel( $node );
		}
	}

	/**
	 * Adds a DataSource to the Model
	 *
	 * @param DataSource $ds The DataSource to attach to the model
	 * @param DataSourceDecorator $decorator = null. Optional. DataSourceDecorator to call on each row.
	 * @param array $fields = null. Optional. If specified, only the supplied data source fields will be attached to the model
	 */
	public function addDataSource( DataSource $ds, DataSourceRowDecorator $decorator = null, array $fields = null ) {
		foreach( $ds as $idx => $row ) {
			$item = $this->add( 'item' );
			$item['idx'] = $idx;
			$item->addDataSourceRow( $row, $decorator, $fields );
		}
	}

	/**
	 * Adds a DataSourceRow to the Model
	 *
	 * @param DataSourceRow $row The DataSourceRow to attach to the model
	 * @param DataSourceDecorator $decorator = null. Optional. DataSourceDecorator to call on this row.
	 * @param array $fields = null. Optional. If specified, only the supplied data source fields will be attached to the model
	 */
	public function addDataSourceRow( DataSourceRow $row, DataSourceRowDecorator $decorator = null, array $fields = null ) {
		$dsFields = $row->getDataSource()->getFields();
		foreach( $dsFields as $field ) {
			$id = $field->getId();
			if ( is_array( $fields ) && !in_array( $id, $fields ) ) {
				continue;
			}
			$value = $field->format( $row->$id );
			$this[$id] = $value;
		}

		if ( $decorator ) {
			$decorator->decorate( $this, $row );
		}
	}

	/**
	 * Adds ResultSet to the Model
	 *	
	 * @param ResultSet $rs The ResultSet to get the data from
	 * @param ResultSetRowDecorator $decorator = null. Optional. ResultSetRowDecorator to call on each row
	 * @param array $fields = null. Optional. If specified, only the supplied result set fields will be attached to the model
	 */
	public function addResultSet( ResultSet $rs, ResultSetRowDecorator $decorator = null, array $fields = null, $tags = false ) {
		foreach( $rs as $idx => $row ) {
			$item = $this->add( 'item' );
			$item['idx'] = $idx;
			$item->addResultSetRow( $row, $decorator, $fields, $tags );
		}
	}

	/**
	 * Adds a result set row to the model
	 *
	 * @param ResultSetRow $rs The ResultSetRow to add to the model
	 * @param ResultSetRowDecorator $decorator = null. Optional. ResultSetRowDecorator to call on this row
	 * @param array $fields = null. Optional. If specified, only the supplied result set fields will be attached to the model
	 */
	public function addResultSetRow( ResultSetRow $row, ResultSetRowDecorator $decorator = null, array $fields = null, $tags = false ) {
		$rsFields = $row->getResultSet()->getFields();
		foreach($rsFields as $tab => $tabFields) {
			foreach($tabFields as $col ) {
				if ( is_array( $fields ) && !in_array( $col, $fields ) ) {
					continue;
				}
				$v = App::encode($row->get( $col ));

				$value = null;
				$setBlob = false;
				switch( $row->getResultSet()->getFieldType( $col ) ) {
					case 'date':
					case 'datetime':
						$value = Date::parse( $v )->format();
						break;
					case 'string':
						$value = $v;
						break;
					case 'blob':
						$value = $v;
						$setBlob = true;
						break;
					default:
						$value = $v;
				}

				if ( $tags ) {
				    $this->$col = $value;
				} else {
				    if ($setBlob) {
					$this->$col = $value;
				    } else {
					$this[$col] = $value;
				    }
				}
			}
		}

		// Call Decorator
		if ( $decorator ) {
			$decorator->decorate( $this, $row );
		}
	}

	/**
	 * Adds an ActiveRecordIterator to the model
	 *
	 * @param ActiveRecordIterator $iter
	 * @param ModelDecorator $decorator Optional. ModelDecorator to apply to each row
	 * @param array $fields Optional. If provided, only the specified fields will be attached to the model
	 */
	public function addIterator( ActiveRecordIterator $iter, ActiveRecordDecorator $decorator = null, array $fields = null, $tags=false ) {
		$this['total'] = $iter->length();
		foreach( $iter as $idx => $obj ) {
			$item = $this->add( 'item' );
			$item['idx'] = $idx + 1;

			$item->addRecord( $obj, $decorator, $fields, $tags );
		}
	}

	/**
	 * Adds an ActiveRecord to the model
	 *
	 * @param ActiveRecord $obj
	 * @param ModelDecorator $decorator Optional. ModelDecorator to apply to each row
	 * @param array $fields Optional. If provided, only the specified fields will be attached to the model
	 */
	public function addRecord( ActiveRecord $obj, ActiveRecordDecorator $decorator = null, array $fields = null, $tags=false ) {
		if ( $obj == null ) return;
		$this->addValues( $obj->getValues( $fields ), $tags );

		// Call Decorator
		if ( $decorator ) {
			$decorator->decorate( $this, $obj );
		}
	}

	public function addValues( array $values, $tags=false ) {
		foreach( $values as $field => $value ) {
			if ( $tags ) {
				$this->$field = App::encode( $value );
			} else {
				if ( is_string( $value ) ) {
					if ( strlen( $value ) >= 2000 ) {
						$this[$field] = '*';
					} else if ( strlen( $value ) >= 256 ) {
						$this->$field = App::encode( $value );
					} else {
						$this[$field] = App::encode( $value );
					}
				} else {
					$this[$field] = App::encode( $value );
				}
			}
		}
	}

	/**
	 * Adds a Hashtable to the model.
	 *
	 * @param Hashtable $table
	 * @param boolean $recursive (Default false). Wether to attach arrays or Hashtables recursively.
	 * @param array $fields Optional. If provided, only the specified fields will be attached to the model
	 */
	public function addHashtable( Hashtable $table, $recursive=false, array $fields = null ) {
		foreach( $table->toArray() as $field => $value ) {
			if ( is_array( $fields ) && !in_array( $field, $fields ) ) {
				continue;
			}
			if ( $recursive && $value instanceOf Hashtable ) {
				$this->add( $field )->addHashtable( $value, true );
			} elseif ( $recursive && is_array( $value ) ) {
				$this->add( $field )->addArray( $value, true );
			} else {
				$this[$field] = (string)$value;
			}
		}
	}

	/**
	 * Adds an indexed array to the model, as a series of item nodes with key="" val="" attributes
	 *
	 * @param array $array
	 */
	public function addArrayExt( array $array ) {
		$idx = 0;
		foreach( $array as $key => $val ) {
			$this->item[$idx]['idx'] = $idx+1;
			$this->item[$idx]['key'] = $key;
            $this->item[$idx]['val'] = ($val);
			//$this->item[$idx]['val'] = App::encode($val);
			$idx++;
		}
	}

    /**
     * Adds an indexed array to the model, as a series of item nodes with key="" val="" attributes
     *
     * @param array $array
     */
    public function addArray( array $array ) {
        foreach( $array as $key => $val ) {
            $this[$key] = $val;
        }
    }



	/**
	 * Adds an array as attributes to the current node, as key="val"
	 *
	 * @param array $array
	 */
	public function addArrayAttributes( array $array ) {
		foreach( $array as $key => $val ) {
			$this[$key] = $val;
		}
	}

	/**
	 * Adds an exception to the Model.
	 *
	 * @param Exception $exception
	 */
	public function addException( \Exception $exception ) {
		$app = $this->getAppNode();
		if ( !$app->has( 'exceptions' ) ) {
			$app->add( 'exceptions' );
		}
		$e = $app->exceptions->add( 'exception' );
		$e['class'] = get_class( $exception );
		$e['message'] = $exception->getMessage();
		$e->backtrace[0]['file'] = $exception->getFile();
		$e->backtrace[0]['line'] = $exception->getLine();
		foreach( $exception->getTrace() as $trace ) {
			$tnode = $e->add( 'backtrace' );
			if ( isset( $trace['class'] ) ) {
				$func = $trace['class'] . $trace['type'] . $trace['function'];
			} else {
				$func = $trace['function'];
			}
			$args = array();
			if ( isset( $trace['args'] ) ) foreach( $trace['args'] as $i => $arg ) {
				if ( is_object( $arg ) ) {
					$args[] = sprintf( '[%s]', get_class( $arg ) );
				} elseif( is_array( $arg ) ) {
					$args[] = sprintf( 'Array[%d]', sizeof( $arg ) );
				} else {
					$args[] = sprintf( '"%s"', $arg );
				}
			}
			$func .= sprintf( '(%s)', implode( ', ', $args ) );
			$tnode['func'] = $func;
			$tnode['file'] = isset( $trace['file'] ) ? $trace['file'] : 'Anonymous';
			$tnode['line'] = isset( $trace['line'] ) ? $trace['line'] : 0;
		}
	}

	/**
	 * Gets this model as a DomDocument
	 *
	 * @return DomDocument
	 */
	public function getDOM() {
		return dom_import_simplexml( $this )->ownerDocument;
	}


	/**
	 * Gets the model as an XML string, optionally formatted for better readability ($pretty=true)
	 *
	 * @param boolean $pretty=false
	 * @return string
	 */
	public function getXML( $pretty=false ) {
		if ( !$pretty ) {
			return $this->asXML();
		} else {
			$dom = $this->getDOM();
			$dom->formatOutput = true;
			return $dom->saveXML();
		}
	}

	/**
	 * Alias to getXML (pretty printed).
	 *
	 * @return string
	 */
	public function __toString() {
		$xml = $this->getXML( true );
		return $xml ? $xml : '';
	}

}
