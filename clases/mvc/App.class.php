<?php

namespace mvc;

use core\Object;
use core\CoreException;
use util\Hashtable;
use util\LoggerFactory;
use mvc\AuthException;
use sql\ConnectionFactory;

/**
 * Application Entry Point.
 *
 * This class resolves the controller and action to execute, with the proper model and view.
 *
 * It's also available as a static class for several utility methods
 */
class App extends Object {

	/**
	 * The current context on which this App is being run.
	 */
	private static $context = null;

	/**
	 * Gets the current App Context
	 *
	 * @return AppContext
	 */
	public static function getContext() {
		if ( static::$context === null ) {
			throw new AppException( 'Cant return AppContext. No context set. Call App::run() first?' );
		}
		return static::$context;
	}

	/**
	 * Shortcut to App::getContext()->encode()
	 *
	 * @return string
	 */
	public static function encode( $string ) {
        return $string;
		return static::getContext()->encode( $string );
	}

	/**
	 * Shortcut to App::getContext()->decode()
	 *
	 * @return string
	 */
	public static function decode( $string ) {
		return static::getContext()->decode( $string );
	}

	/**
	 * Runs the provided AppRequest $request.
	 * Returns an AppResult object, containing the generated Model Object, and any action exceptions.
	 *
	 * Requests are completely independent from each other, and can be aggregated in a single AppResult Object,
	 * calling the AppResult::addResult() method. This will aggregate the Model Data, AppRequest, and generated exceptions.
	 *
	 * You can also run a request from inside a Controller (when the final AppResult hasn't been generated yet), by calling
	 * Controller::addResult( App::run( ... ) ) 
	 *
	 * Those results will be added to the parent AppResult, allowing from multiple levels of sub-requests.
	 *
	 * @param string $request
	 * @param Hashtable $args
	 */
	public static function run( AppRequest $request ) {
		$savedContext = null;
		if ( static::$context !== null ) {
			$savedContext = static::$context;
		}
		if ( static::$context !== $request->getContext() ) {
			static::$context = $request->getContext()->switchContext();
		}
		$request->resolve();

		// Get Controller and Action
		$controller = $request->getController();
		$action = $request->getAction();

		// Instantiate model
		$model = Model::getInstance( $request );
		$result = new AppResult( $request, $model );

		try {
			$start = static::measure();

			// Try to perform the action
			$view = $controller->perform( $model->add( 'data' ) );
			$result->addResults( $controller->getResults() );
			$result->setView( $view );

			if ( $request->getContext()->getDebug() ) {
				$result->setPerformance( $request, static::getPerformance( $start ) );
			}

		} catch ( \Exception $e ) {
			// An exception was thrown when performing the action. Trap it in AppResult

			// Log Exception if we have a logger
			if ( $logger = LoggerFactory::getDefault() ) {
				$logger->logException( "Got " . \get_class($e) . ": " . $e->getMessage() . " while running request. Trace: " . \core\ErrorHandler::parseBacktrace($e->getTrace()) );
			}
			$result->addException( $e );
		}

		if ( $savedContext !== null && $savedContext !== static::$context ) {
			static::$context = $savedContext->switchContext();
		}

		return $result;
	}

	/**
	 * Returns the timer timestamp
	 *
	 * @return float
	 */
	public static function measure() {
		$time = microtime( true );
		$queries = ConnectionFactory::getQueryCount();
		if ( function_exists( 'memory_get_usage' ) ) {
			return array( $time, memory_get_usage(), $queries );
		} else {
			return array( $time, 0, $queries );
		}
	}

	/**
	 * Gets the performance according to start timer/mem/queries array
	 *
	 * @param array $start( time, mem, queries )
	 * @return array
	 */
	public static function getPerformance( array $start ) {
		$end = static::measure();
		$total = $end[0] - $start[0];
		if ( function_exists( 'memory_get_usage' ) ) {
			$mem = ( $end[1] - $start[1] ) / 1024;
			if ( $mem > 1024 ) {
				$memstr = sprintf( '%1.2f MB', $mem / 1024 );
			} else {
				$memstr = sprintf( '%1.2f KB', $mem );
			}
		} else {
			$memstr = 'N/A';
		}
		$queries = $end[2] - $start[2];
		if ( extension_loaded( 'xdebug' ) ) {
			$str = sprintf( 'Time: %1.5f - Mem: %s - Queries: %d - Xdebug Profile: %s', $total, $memstr, $queries, xdebug_get_profiler_filename() );
		} else {
			$str = sprintf( 'Time: %1.5f - Mem: %s - Queries: %d', $total, $memstr, $queries );
		}
		return array( 'time' => $total, 'mem' => $memstr, 'queries' => $queries, 'msg' => $str );
	}

}
