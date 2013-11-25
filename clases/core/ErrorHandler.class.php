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
 * @version $Revision$
 * @updated $Date$
 *

 */

namespace core;

use util\LoggerFactory;

/**
 * MVC framework Base Exception Handler.
 *
 * Extend this class and implement your own handle method to provide a new exception handler
 */
class ErrorHandler extends Object {

	/**
	 * Handles the supplied Exception.
	 *
	 * @param Exception $exception
	 */
	public static function handleException( \Exception $exception ) {
		$message = sprintf( "Uncaught %s: %s\n", get_class( $exception ), $exception->getMessage() );
		$message.= sprintf( "  At %s:%d\n", $exception->getFile(), $exception->getLine() );
		$message.= static::parseBacktrace( $exception->getTrace() );
		try {
			LoggerFactory::getDefault()->logError( $message );
		} catch ( \Exception $e ) {
			$message.= sprintf( "** Warning: Could not log exception: %s\n", $e->getMessage() );
		}
		\pprint( $message );
		exit;
	}

	/**
	 * Handles the supplied error
	 *
	 * @param int $errno The internal PHP error number
	 * @param string $errmsg Error message
	 * @param string $file File that produced that error
	 * @param int $line Line at which the error was produced
	 * @param array $ctxt The error context
	 */
	public static function handleError( $errno, $errmsg, $file, $line, $ctxt ) {
		if ( error_reporting() == 0 ) return false; // error-control operator used (@)
		$message = sprintf( "(%d) %s\n", $errno, $errmsg );
		$message.= sprintf( "  At %s:%d\n", $file, $line );
		$message.= static::parseBacktrace( debug_backtrace(), 2 );

		try {
			LoggerFactory::getDefault()->logError( $message );
		} catch ( \Exception $e ) {
			$message.= sprintf( "** Warning: Could not log error: %s\n", $e->getMessage() );
		}
		\pprint( $message );
		exit;
	}

	/**
	 * Parses and exception backtrace (as returned from Exception::getTrace())
	 * and returns a readable string ready to be printed or logged.
	 *
	 * The second (optional) parameter allows from parsing from a specific position, starting at the deepest one.
	 *
	 * @param array $trace
	 * @return string
	 */
	public static function parseBacktrace( array $trace, $from=0 ) {
		$str = '';
		for( $i=$from; $i<count($trace); $i++ ) {
			$t = $trace[$i];
			if ( isset( $t['class'] ) ) {
				$func =  $t['class'] . $t['type'] . $t['function'];
			} else {
				$func = $t['function'];
			}
			$args = array();
			if ( isset( $t['args'] ) ) foreach( $t['args'] as $idx => $arg ) {
				if ( is_object( $arg ) ) {
					$args[] = sprintf( '[%s]', get_class( $arg ) );
				} elseif( is_array( $arg ) ) {
					$args[] = sprintf( 'Array[%d]', sizeof( $arg ) );
				} elseif ( strlen( $arg ) > 100 ) {
					$args[] = sprintf( 'String[%s]', strlen( $arg ) );
				} else {
					$args[] = sprintf( '"%s"', $arg );
				}
			}
			$func.= sprintf( '(%s)', implode( ', ', $args ) );
			if ( isset( $t['file'] ) ) {
				$file = $t['file'].':'.$t['line'];
			} else {
				$file = 'Anonymous';
			}
			$str.= sprintf( "  [%d] At %s (%s)\n", ($i-$from), $func, $file );
		}
		return $str;
	}

}
