<?php


namespace sql;

use core\Object;

/**
 * Class ConnectionFactory
 * 
 * This class provides static methods for managing database Connections.
 */
class ConnectionFactory extends Object {

	/**
	 * The list of available database drivers (Connection subclasses)
	 */
	private static $drivers = array(
				'mysql' => 'sql\mysql\MySQLConnection',
				'mysqli' => 'sql\mysqli\MySQLiConnection'
			);

	/**
	 * The list (array) of instantiated connections (Connection subclasses objects)
	 * 
	 * The array key is generated based on the connection parameters, so for every 
	 * unique set of connection parameters, a single connection will be returned.
	 * (see getConnection())
	 */
	private static $connections = array();

	/**
	 * Private constructor to avoid instantiation.
	 */
	private function __construct() {
	}

	/**
	 * Registers a new database driver (Connection subclass)
	 *
	 * @param string $name The Connection Driver name (this is used as the protocol in the connection url)
	 * @param string $class The Connection subclass
	 */
	public static function registerDriver( $name, $class ) {
		self::$drivers[$name] = $class;
	}

	/**
	 * Deregisters a database driver.
	 *
	 * @param string $name the Connection Driver name.
	 */ 
	public static function deregisterDriver( $name ) {
		unset( self::$drivers[$name] );
	}

	/**
	 * Gets a new Connection instance for the provided Connection URL and flags
	 * 
	 * The URL is in the form:
	 * <protocol>://[user][:pass]@<host>[/dbname]
	 *
	 * The <protocol> is used to identify the driver to use.
	 * Use ConnectionFactory::listDrivers() to get a list of drivers available (including those you may have registered)
	 * 
	 * Examples:
	 * <code>
	 * $conn = ConnectionFactory::newConnection( 'mysql://myuser:mypass@localhost' ); 
	 * $conn = ConnectionFactory::newConnection( 'mysql://localhost' ); 
	 * $conn = ConnectionFactory::newConnection( 'mysql://myuser@localhost/database' );
	 * </code>
	 * 
	 * @param string $name The Connection name
	 * @param string $url The Connection URL
	 * @param int $flags The Connection flags
	 * @return Connection
	 */
	public static function newConnection( $name, $url, $flags=0 ) {
		$params = parse_url( $url );
		if ( $params['host'] == 'socket' ) {
			$params['host'] = ':'.$params['path'];
			unset( $params['path'] );
		} elseif ( isset( $params['path'] ) ) {
			$params['path'] = substr( $params['path'], 1 );
		}

		if ( !array_key_exists( $params['scheme'], self::$drivers ) ) {
			throw new SQLException( sprintf( 'No suitable driver found for "%s".', $params['scheme'] ) );
		}
		$class = self::$drivers[$params['scheme']];

		return new $class( $name, $params, $flags );
	}

	/**
	 * Adds a Connection to the ConnectionFactory so it can then be retrieved statically
	 *
	 * @param Connection $connection
	 */
	public static function addConnection( Connection $connection ) {
		self::$connections[$connection->getName()] = $connection;
	}

	/**
	 * Gets a previously added connection by name
	 * 
	 * @param string $name
	 * @return Connection
	 * @throws Exception
	 */
	public static function getConnection( $name ) {
		if ( array_key_exists( $name, self::$connections ) ) {
			return self::$connections[$name];
		} else {
			throw new SQLException( sprintf( 'No Connection found for name: "%s"', $name ) );
		}
	}

	/**
	 * Sets the default Connection
	 * 
	 * The default connection has the reserved name 'DEFAULT'
	 * 
	 * @param string $url The Connection URL
	 * @param int $flags Optional.
	 */
	public static function setDefault( Connection $connection ) {
		self::$connections['DEFAULT'] = $connection;
	}

	/**
	 * Returns the Default Connection
	 *
	 * @return Connection
	 */
	public static function getDefault() {
		if ( !isset( self::$connections['DEFAULT'] ) ) {
			throw new SQLException( 'No Default Connection' );
		}
		return self::$connections['DEFAULT'];
	}

	/**
	 * Clears all added Connections
	 */
	public static function clearConnections() {
		self::$connections = array();
	}

	/**
	 * Gets total query count (for all open connections)
	 *
	 * @return int
	 */
	public static function getQueryCount() {
		$total = 0;
		foreach( self::$connections as $conn ) {
			$total += $conn->getQueryCount();
		}
		return $total;
	}

	/**
	 * Gets total query execution time (for all open connections)
	 *
	 * @return float
	 */
	public static function getTotalTime() {
		$total = 0;
		foreach( self::$connections as $conn ) {
			$total += $conn->getTotalTime();
		}
		return $total;
	}

	/**
	 * Lists all available Connection Drivers
	 *
	 * @return string
	 */
	public static function listDrivers() {
		echo "Available Drivers:\n";
		foreach( self::$drivers as $name => $class ) {
			printf( " - %10s %s\n", $name, $class );
		}
	}

	/**
	 * Lists all active connections and total query count
	 *
	 * @return string
	 */
	public static function listConnections() {
		printf( "Listing %s active connection(s)\n", sizeof( self::$connections ) );
		printf( "Total Queries Executed: %s (Time: %1.5f)\n", self::getQueryCount(), self::getTotalTime() );
		echo "-------\n";
		foreach( self::$connections as $name => $conn ) {
			printf( " - Connection Name: %s\n", $name );
			echo $conn;
		}
	}
}
