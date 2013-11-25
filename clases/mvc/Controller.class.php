<?php


namespace mvc;

use core\Object;
use util\Hashtable;

/**
 * Base Controller class
 */
abstract class Controller extends Object {

	/**
	 * The AppRequest
	 */
	protected $request = null;

	/**
	 * Controller module
	 */ 
	protected $module = null;

	/**
	 * Controller Name
	 */
	protected $name = null;

	/**
	 * Action being executed.
	 */
	protected $action = null;

	/**
	 * Array of available Soap Actions.
	 */
	protected $soapActions = array();

	/**
	 * Default view type
	 */
	protected $view = null;

	/**
	 * Page Title
	 */
	protected $title = null;

	/**
	 * Sub Requests results
	 */
	protected $results = array();

	/**
	 * Default Constructor 
	 *
	 * @param AppRequest $request The application request
	 * @param string $module The Controller module (namespace)
	 * @param string $name The Controller name
	 */
	public function __construct( AppRequest $request, $module, $name ) {
		$this->request = $request;
		$this->module = $module;
		$this->name = $name;
	}

	/**
	 * Adds a sub request result to this controller.
	 * When you perform a sub request in an action, and you want that result
	 * to be aggregated in the main request result, then call this method on the AppResult returned by App::run
	 *
	 * @param AppResult $result
	 */
	public function addResult( AppResult $result ) {
		$this->results[] = $result;
	}

	/**
	 * Returns Controller module
	 *
	 * @return string
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Returns Controller name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns Action being executed.
	 *
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

    /**
     * Returns Action being executed.
     *
     * @return string
     */
    public function setAction($action) {
        $this->action=$action;
    }



    /**
	 * Returns the default view type for this controller.
	 * If protected $view = '...' is defined, then this view type will be used when no view has been supplied either by _POST or _GET method.
	 * If undefined and not supplied, default view type is always 'xsl'
	 *
	 * @return string
	 */
	public function getView() {
		return $this->view;
	}

	/**
	 * Sets the Page title
	 *
	 * @param string $title
	 */
	public function setTitle( $title ) {
		$this->title = $title;
		return $this;
	}

	/**
	 * Gets the page title
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Every Controller class must have a defaultAction method.
	 *
	 * @param Model $model
	 * @param Hashtable $args
	 * @return View
	 */
	abstract public function defaultAction( Model $model, Hashtable $args );

	/**
	 * This action, available to all Controllers, provides a single gateway for SOAP Actions.
	 *
	 * @param Model $model
	 * @param Hashtable $args
	 * @return Soap Response
	 */
	public function soapAction( Model $model, Hashtable $args ) {
		ini_set( 'soap.wsdl_cache_enabled', 0 );

		$file = WSDLGenerator::getFile( $this );

		$server = new SoapServer( $file->getName(), array( 'soap_version' => SOAP_1_2 ) );
		$server->setClass( get_class( $this ), $this->module, $this->name, $this->app );
		$server->handle();
		exit;
	}

	/**
	 * This action is used for testing purposes. If no params are provided, then a soap client form is presented.
	 *
	 * @param Model $model
	 * @param Hashtable $args
	 */
	public function soapTestAction( Model $model, Hashtable $args ) {
		ini_set( 'soap.wsdl_cache_enabled', 0 );

		$file = WSDLGenerator::getFile( $this, true );
		$client = new SoapClient( $file->getName(), array( 'trace' => 1 ) );

		$model->controller = get_called_class();

		if ( !$args->soap_action ) {
			foreach( $client->__getFunctions() as $i => $func ) {
				if ( preg_match( '/^(.*)\s(.*)\((.*)\)$/U', $func, $matches ) ) {
					$model->methods->method[$i]['name'] = $matches[2];
					$model->methods->method[$i]['return_type'] = $matches[1];
					if ( preg_match_all( '/(\w+)\s\$(\w+)/', $matches[3], $m ) ) {
						foreach( $m[1] as $idx => $type ) {
							$model->methods->method[$i]->param[$idx]['type'] = $type;
							$model->methods->method[$i]->param[$idx]['name'] = $m[2][$idx];
						}
					}
				}
			}
		} else {
			try {
				if ( !$args->soap_params ) {
					$params = array();
				} else {
					$params = $args->soap_params;
				}
				$xml = $client->__soapCall( $args->soap_action, $params );

				header( 'content-type:text/xml' );
				echo $xml;
				exit;
			} catch ( SoapFault $fault ) {
				$model->soap->request = $client->__getLastRequest();
				$model->soap->response = $client->__getLastResponse();
				$args->view = 'xml';
				return '../soap_error';
			}
		}
		return '../soap_test';
	}

	/**
	 * This action, available to all Controllers, returns a self-describing Model, listing available Actions, parameters, etc.
	 *
	 * @param Model $model
	 * @param Hashtable $args
	 */
	public function infoAction( Model $model, Hashtable $args ) {
		$model->controller = get_called_class();
		//ReflectionObject::export($this);
		$class = new ReflectionClass($this);
		$i=0;
		foreach( $class->getConstants() as $name => $value ) {
			$model->const[$i]['name'] = $name;
			$model->const[$i]['value'] = $value;
			$i++;
		}
		$i=0;
		foreach( $class->getMethods() as $method ) {
			if ( substr( $method->getName(), -6 ) == 'Action' && $method->getName() != 'soapAction' && $method->getName() != 'infoAction' ) {
				$model->action[$i]['type'] = 'NORMAL';
				$model->action[$i]['name'] = $method->getName();
				$i++;
			}
		}
		foreach( $this->soapActions as $soap ) {
			$model->action[$i]['type'] = 'SOAP';
			$model->action[$i]['name'] = $soap;
		}
		$i=0;
		foreach( $this->factories as $name => $factory ) {
			$model->factory[$i]['name'] = $name;
			foreach( $factory->getNamespaces() as $j => $ns ) {
				$model->factory[$i]->namespace[$j] = $ns;
			}
			$i++;
		}
		return '../info';
	}

	/**
	 * Executed before perform, this method can be overriden to globally perform additional tasks for the controller.
	 *
	 * @param Model $model
	 * @param Hashtable $args
	 * @return void
	 */
	protected function prepare( Model $model, Hashtable $args ) {
	}

	/**
	 * Executed after perform, this method can be overriden to globally perform additional tasks for the controller.
	 * This method does not modify the view returned by the action
	 *
	 * @param Model $model
	 * @param Hashtable $args
	 * @return void
	 */
	protected function finish( Model $model, Hashtable $args ) {
	}

	/**
	 * Get the available SOAP Actions for this Controller
	 * 
	 * @return array
	 */
	public function getSoapActions() {
		return $this->soapActions;
	}

	/**
	 * Checks authorization for controller and action.
	 *
	 * Overload this method in your controller to provide controller-specific authorization checks,
	 * or provide a new base controller extending this one for app-wide authorization checks.
	 *
	 * @param string $action The Action
	 * @param Hashtable $args
	 * @return int 0 If ok, AuthException::<ERROR_CODE> if not.
	 */
	public function checkAuth() {
		return 0;
	}

	/**
	 * Calls an action to perform.
	 *
	 * @param Model $model The base Data Model to attach data to.
	 * @return View
	 * @throws AuthException If the user/context isn't authorized.
	 */
	public function perform( Model $model ) {
		// Check authorization
		$check = $this->checkAuth();
		if ( $check > 0 ) {
			throw new AuthException( $this->request, $check );
		}

		$this->action = $this->request->getAction();
		$method = sprintf( '%sAction', $this->action );

		if ( !method_exists( $this, $method ) ) {
			throw new AppException( sprintf( 'Invalid Method/Action: "%s" for Controller "%s"', $this->action, get_called_class() ) );
		}

		$this->prepare( $model, $this->request->getArgs() );

		$ret = $this->$method( $model, $this->request->getArgs() );
		if ( !$ret ) $ret = $this->getAction() != 'default' ? sprintf( '%s:%s', $this->name, $this->getAction() ) : $this->name;

		$this->finish( $model, $this->request->getArgs() );

		return $ret;
	}

	/**
	 * Returns the array of sub request results
	 *
	 * @return array(AppResult)
	 */
	public function getResults() {
		return $this->results;
	}
}
