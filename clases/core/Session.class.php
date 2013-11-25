<?php


namespace core;

use core\Object;
use core\session\SessionHandler;

/**
 * Main Session Handling Class.
 * Provides methods for getting and setting session variables,
 * abstract from the underlying Session Handler
 */
class Session extends Object {

	/**
	 * Session handler
	 */
	private static $handler = null;

	/**
	 * Protected Constructor to avoid instantiation.
	 */
	private function __construct() {
	}

	/**
	 * Sets the given SessionHandler implementation as the session handler.
	 *
	 * @param string $class
	 */
	public static function setHandler( SessionHandler $sessionHandler ) {
		session_set_save_handler(
			array( $sessionHandler, 'open' ),
			array( $sessionHandler, 'close' ),
			array( $sessionHandler, 'read' ),	
			array( $sessionHandler, 'write' ),
			array( $sessionHandler, 'destroy' ),
			array( $sessionHandler, 'gc' )
			);

		if ( !isset( $_SESSION ) ) {
			session_start();
		}
		self::$handler = $sessionHandler;
	}

	/**
	 * Gets the Session ID
	 *
	 * @return string
	 */
	public static function getID() {
		return $_COOKIE['PHPSESSID'];
	}

	/**
	 * Gets a Session value
	 *
	 * @param string $name The session variable name
	 * @return mixed The session variable value
	 */
	public static function get( $name ) {
		return ( isset( $_SESSION[$name] ) ? $_SESSION[$name] : null );
	}

	/**
	 * Checks wether all the given arguments (1+) are present in the Session
	 *
	 * @param mixed *
	 * @return boolean
	 */
	public static function has() {
		$args = func_get_args();
		foreach( $args as $arg ) {
			if ( !isset( $_SESSION[$arg] ) ) return false;
		}
		return true;
	}

	/**
	 * Sets a Session value
	 *
	 * @param string $name The session variable name
	 * @param mixed $value The session variable value
	 */
	public static function set( $name, $value ) {
		$_SESSION[$name] = $value;
	}

	/**
	 * Deletes a Session value
	 *
	 * @param string $name The session variable name
	 */
	public static function del( $name ) {
		unset( $_SESSION[$name] );
	}

	/**
	 * Destroys the current session.
	 *
	 * @static
	 * @return void
	 */
	public static function destroy() {
		session_destroy();
	}

	/**
	 * Returns a string representation of the Session.
	 * 
	 * @return string
	 */
	public static function dump() {
		$str = sprintf( "Session: %s\n%s\n", $_COOKIE['PHPSESSID'], str_repeat( '-', 41 ) );
		foreach( $_SESSION as $name => $value ) {
			if ( !is_object( $value ) ) {
				$str.= sprintf( "%-20s -> %s\n", $name, $value );
			} else {
				$str.= sprintf( "%-20s -> Object ID# %s\n", $name.' ('.get_class( $value ).')', $value->id() );
			}
		}
		return $str;
	}

	/**
	 * Gets the live session
	 *
	 * @return array
	 */
	public static function getLiveSessions( $maxLifeTime ) {
		return self::$handler->getAliveIDs( $maxLifeTime );
	}
}

