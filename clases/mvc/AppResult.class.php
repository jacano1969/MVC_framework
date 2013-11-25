<?php

namespace mvc;

use core\Object;

/**
 * The AppResult class is created and returned by the App::run( AppRequest $request ) method, containing:
 * - Original AppRequest
 * - Generated Model data
 * - Uncaught Exceptions
 *
 * AppResults can be aggregated into other AppResults for a single, complex, multiple-sub-requests AppResult Object.
 *
 * Example:
 * <code php>
 * // Run our first request, order coffe from starbucks at the shopping center.
 *
 * $args = new Hashtable();
 * $args->what = 'coffe';
 * $args->milk = true;
 *
 * $context = new AppContext( 'shopping-center.xml' );
 * $request = new AppRequest( $context, 'shops/starbucks:order', $args );
 * $result = App::run( $request );
 *
 * // Now ask for directions to the guard outside the door
 * $args = new Hashtable();
 * $args->what = 'directions';
 * $args->where = 'parking-lot';
 * $request = new AppRequest( $context, 'guards/starbucks_guard:ask', $args );
 *
 * // The $result object will now contain the result of both requests: Ordering Coffe, and how to get to the parking lot.
 * $result->addResult( App::run( $request ) );
 *
 * // Show to the user how we proudly drink our coffe while we walk to the parking lot.
 * $result->render();
 * </code>
 */
class AppResult extends Object {

	/**
	 * Array of AppRequest that generated this AppResult
	 */
	private $requests = array();

	/**
	 * The generated model
	 */
	private $model = null;

	/**
	 * Array of Exceptions added to this AppResult
	 */
	private $exceptions = array();

	/**
	 * Performance Array (one element for each request).
	 * Each performance element is an array with the following data:
	 * - time
	 * - mem
	 * - queries
	 * - msg
	 */
	private $performance = array();

	/**
	 * Instantiates a new AppResult for the supplied AppRequest and Model
	 *
	 * @param AppRequest $request
	 * @param Model $model
	 */
	public function __construct( AppRequest $request, Model $model ) {
		$this->requests[] = $request;
		$this->model = $model;
	}

	/**
	 * Adds another AppResult to this result, aggregating its data, exceptions and requests.
	 *
	 * @param AppResult $result
	 */
	public function addResult( AppResult $result ) {
		foreach( $result->getRequests() as $r ) {
			$this->requests[] = $r;
		}
		foreach( $result->getPerformance() as $id => $p ) {
			$this->performance[$id] = $p;
		}
		foreach( $result->getExceptions() as $e ) {
			$this->exceptions[] = $e;
		}

		$this->model->data->addModel( $result->getModel()->data );
	}

	/**
	 * Adds the array of supplied AppResults to this AppResult
	 *
	 * @param array $results(AppResult)
	 */
	public function addResults( array $results ) {
		foreach( $results as $r ) {
			if ( !$r instanceOf AppResult ) {
				throw new AppException( sprintf( 'Cant add result. "%s" is not an instance of AppResult', get_class( $r ) ) );
			}
			$this->addResult( $r );
		}
	}

	/**
	 * Returns this AppResult Model
	 *
	 * @return Model
	 */
	public function getModel() {
		return $this->model;
	}

	/**
	 * Returns the requests aggregated in this AppResult
	 *
	 * @return array
	 */
	public function getRequests() {
		return $this->requests;
	}

	/**
	 * Adds an Exception to this AppResult
	 *
	 * @param \Exception $e
	 */
	public function addException( \Exception $e ) {
		$this->exceptions[] = $e;
	}

	/**
	 * Returns whether this AppResult has any exceptions
	 *
	 * @return boolean
	 */
	public function hasExceptions() {
		return sizeof( $this->exceptions ) > 0;
	}

	/**
	 * Returns this result exceptions
	 *
	 * @return array
	 */
	public function getExceptions() {
		return $this->exceptions;
	}

	/**
	 * The view name for this result
	 *
	 * @param string $name
	 */
	public function setView( $view ) {
		$this->view = $view;
	}

	/**
	 * Sets the performance for the supplied AppRequest
	 *
	 * @param array $performance.
	 */
	public function setPerformance( AppRequest $request, array $performance ) {
		$this->performance[$request->getId()] = $performance;
	}

	/**
	 * Returns the performance array for this result requests.
	 *
	 * @return array
	 */
	public function getPerformance() {
		return $this->performance;
	}

	/**
	 * Renders this AppResult. This method attaches all the request and debug data to the model
	 */
	public function render() {
		if ( $this->hasExceptions() ) {
			list( $renderer, $view ) = $this->resolveExceptionView();
		} else {
			list( $renderer, $view ) = $this->resolveView( $this->getMainRequest()->getController()->getModule(), $this->view );
		}
		if ( $this->getMainRequest()->getContext()->getDebug() ) {
			$this->model['debug'] = 1;
		}
		foreach( $this->requests as $request ) {
			$r = $this->model->add( 'request' );
			$r['id'] = $request->getId();
			$r['module'] = '/' . $request->getModule();
			$r['controller'] = $request->getController()->getName();
			$r['action'] = $request->getAction();
			foreach( $request->getArgs() as $key => $val ) {
				if( in_array( $key, array( 'request' ) ) ) continue;
				$value = !is_array( $val ) ? addslashes( str_replace( "\n", "", $val ) ) : sprintf( 'Array[%d]', sizeof( $val ) );
				$param = $r->add( 'param', $value );
				$param['name'] = $key;
			}
			if ( isset( $this->performance[$request->getId()] ) ) {
				$p = $this->performance[$request->getId()];
				$r->add( 'performance' );
				$r->performance['time'] = sprintf( '%1.5f', $p['time'] );
				$r->performance['mem'] = $p['mem'];
				$r->performance['queries'] = $p['queries'];
			}
		}
		foreach( $this->exceptions as $e ) {
			$this->model->addException( $e );
		}

		$renderer->render( $this->model, $view );
	}

	/**
	 * Gets the main (first) request for this result
	 *
	 * @return AppRequest
	 */
	public function getMainRequest() {
		if ( !isset( $this->requests[0] ) ) {
			throw new AppException( 'No Requests in Result.' );
		}
		return $this->requests[0];
	}

	/**
	 * Resolves the ViewRenderer and View out of the main request, and returns both objects
	 *
	 * @param string $module
	 * @param string $name
	 * @return arra( ViewRenderer, View )
	 */
	private function resolveView( $module, $name ) {
		$request = $this->getMainRequest();
		$renderer = $request->getContext()->getViewRenderer( $request->getView() );

		$view = $renderer->load( $module, $name );
		return array( $renderer, $view );
	}

	/**
	 * Resolves the View to use for exceptions
	 *
	 * @return View
	 */
	private function resolveExceptionView() {
		foreach( $this->exceptions as $e ) {
			list( $module, $view ) = $this->getMainRequest()->getContext()->getErrorView( $e );
			return $this->resolveView( $module, $view );
		}
	}

}
