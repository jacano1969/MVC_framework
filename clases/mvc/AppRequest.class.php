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
use core\CoreException;
use io\UploadedFile;
use util\Hashtable;

/**
 * Represents a single Application Request in MVC framework
 * AppRequest implementing classes defined how requests are parsed to determine the controller, action, view and args to execute
 *
 */
abstract class AppRequest extends Object {

	/**
	 * Unique, auto-generated request id.
	 */
	protected $id = null;

	/**
	 * The context to run this request on
	 */
	protected $context = null;

	/**
	 * The request string
	 */
	protected $request = null;

	/**
	 * The request arguments
	 */
	protected $args = null;

	/**
	 * Controller resolved by this request
	 */
	protected $controller = null;

	/**
	 * Action resolved by this request
	 */
	protected $action = null;

	/**
	 * View resolved by this request
	 */
	protected $view = null;

	/**
	 * Instantiates a new AppRequest for the supplied Context, request and args
	 * The Request comes in the standard route [module/]<controller>[@action][.view]
	 *
	 * @param AppContext $context
	 * @param Request 
	 * @param Hashtable $args
	 */
	public function __construct( AppContext $context, $request, Hashtable $args = null ) {
		$this->id = md5(rand());
		$this->context = $context;
		$this->request = $request;
		$this->setArgs( $args );
	}

	/**
	 * Returns this request id
	 *
	 * @return string 
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Returns this request context
	 *
	 * @return AppContext
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * Returns this request args
	 *
	 * @return Hashtable
	 */
	public function getArgs() {
		return $this->args;
	}

	/**
	 * Sets the args for this request. NULL cleans them
	 *
	 * @param Hashtable $args
	 */
	public function setArgs( Hashtable $args = null ) {
		$this->args = $args !== null ? $args : new Hashtable();
		if( is_array( $_FILES ) && sizeof( $_FILES ) ) {
			$this->args->FILES = new Hashtable();
			foreach( $_FILES as $id => $file ) {
				$args->FILES->put($id, new UploadedFile($file));	
			}
		}
		if( is_array( $_GET ) && sizeof( $_GET ) ) {
			foreach( $_GET as $key => $value) {
				$args->put($key, $value);
			}
		}
		if( is_array( $_POST ) && sizeof( $_POST ) ) {
			foreach( $_POST as $key => $value) {
				$args->put($key, $value);
			}
		}
	}

	/**
	 * Sets the supplied argument for this request
	 *
	 * @param string $arg
	 * @param string $value
	 */
	public function setArg( $arg, $value ) {
		$this->args->put( $arg, $value );
	}

	/**
	 * Gets this Request resolved controller
	 *
	 * @return Controller
	 */
	public function getController() {
		return $this->controller;
	}

	/**
	 * Returns the module resolved by this request
	 *
	 * @return string
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Returns the action resolved by this request
	 *
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Returns the view resolved by this request
	 *
	 * @return string
	 */
	public function getView() {
		return $this->view;
	}

	/**
	 * Resolves the request, determining controller, module, action and view.
	 */
	abstract public function resolve();

}
