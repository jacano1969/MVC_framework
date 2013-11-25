<?php


namespace sql;

use core\Object;
use util\Logger;
use util\LoggerFactory;

/**
 * This abstract class should be extended by database-specific connection drivers.
 * 
 * MVC framework provides some default drivers (look under php/sql/*)
 */
abstract class Connection extends Object {

	/**
	 * Bitmask value for flag PERSISTENT
	 */
	const PERSISTENT = 1;

	/**
	 * Bitmask value for flag DEBUG
	 */
	const DEBUG = 2;

	/**
	 * Bitmask value for flag LOG
	 */ 
	const LOG = 4;
	
	/**
	 * Bitmask value for flag COUNT
	 */
	const COUNT = 8;

	/**
	 * Bitmask value for flag MEASURE
	 */
	const MEASURE = 16;

	/**
	 * Value for fetch mode MultiDimensional
	 */
	const MODE_MULTI = 'MULTI';

	/**
	 * Value for fetch mode Associative
	 */
	const MODE_ASSOC = 'ASSOC';

	/**
	 * Value for fetch mode Numeric
	 */
	const MODE_NUM = 'NUM';

	/**
	 * Connection Name
	 */
	protected $name = null;

	/**
	 * Conenction Host
 	 */	
	protected $host = null;

	/**
	 * Connection User
 	 */
	protected $user = null;

	/**
	 * Connection Pass
 	 */
	protected $pass = null;

	/**
	 * Connection Path (scheme, etc)
 	 */
	protected $path = null;

	/**
	 * The native PHP Resource Link
	 */
	protected $resource = null;
	
	/**
	 * Fetch Mode for ResultSets
	 */
	protected $mode = self::MODE_ASSOC;
	
	/**	 
	 * A bitmask of flags for this connection 
	 */
	protected $flags = 0;

	/**
	 * Logger instance used for logging.
	 */
	protected $logger = null;

	/**
	 * Connection query count
	 */
	protected $queryCount = 0;

	/**
	 * Query Total Time
	 */
	protected $totalTime = 0;

	/**
	 * Query Time
	 */
	protected $queryTime = 0;


	/**
	 * Instantiates a new Connection object, and calls connect()
	 *
	 * @param $params Connection Parameters (host, user, pass, path)
	 * @param int $flags Binary flags
	 */ 
	public function __construct( $name, $params, $flags=0 ) {
		$this->name = $name;
		$this->host = isset( $params['host'] ) ? $params['host'] : null;
		$this->user = isset( $params['user'] ) ? $params['user'] : null;
		$this->pass = isset( $params['pass'] ) ? $params['pass'] : null;
		$this->path = isset( $params['path'] ) ? $params['path'] : null;
		$this->connect( $flags );
	}

	/**
	 * Returns this connection name
	 *
	 * @return string
 	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the Connection Host
	 *
	 * @return string
 	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * Returns the connection username
	 *
	 * @return string
 	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Returns the connection password
	 *
	 * @return string
 	 */
	public function getPass() {
		return $this->pass;
	}

	/**
	 * Returns the connection path
	 *
	 * @return string
 	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Gets the Connection resource identifier.
	 *
	 * @return string
	 */
	public abstract function getResourceId();

	/**
	 * Performs the actual connection to the database. An implementing driver should call the native database function.
	 *
	 * @throws SQLException
	 */
	public abstract function connect( $flags );

	/**
	 * Sets this connection timezone.
	 *
	 * @param string $timezone
 	 */
	public abstract function setTimezone( $timezone );

	/**
 	 * Sets this connection encoding.
	 *
	 * @param string $encoding
 	 */
	public abstract function setEncoding( $encoding );

	/**
	 * Prepares an SQL statement, returning the appropiate PreparedStatement object
	 *
	 * @param string $sql The Query string.
	 * @throws SQLException
	 */
	public abstract function prepare( $sql );


	/**
	 * Returns a new appropiate BlobHandler for this connection
	 *
	 * @param $table The table name
	 * @param $primaryKey The table's primary key name
	 * @param $blobName The BLOB's column name
	 */
	public abstract function newBlobHandler( $table, $primaryKey, $blobName );

	/**
	 * Returns true if the database connection driver supports sequences
	 * Sequences are generated before insert statements 
	 *
	 * @param boolean
	 */
	public abstract function hasSequences();

	/**
	 * Returns true if the database connection driver supports last insert id values (ala MySQL)
	 * These values are generated after insert statements
	 * 
	 * @param boolean
	 */
	public abstract function hasLastInsertID();

	/**
	 * Gets the next sequence value, given its name
	 *
	 * @param string $seq The sequence name
	 */
	public abstract function getSequence( $seq );

	/**
	 * Executes the provided $query
	 *
	 * @param string $query
	 * @return mixed Depending on the type of statement executed.
	 * @throws SQLException In case of an error executing the query.
	 */
	public abstract function query( $query );

	/**
	 * List the tables in the supplied database schema.
	 *
	 * @param string $schema
	 * @return array(Table)
 	 */
	public abstract function getTables( $schema );

	/**
	 * Gets a single Table for the supplied database schema/table name
	 *
	 * @param string $schema
	 * @param string $table
	 * @return Table
 	 */
	public abstract function getTable( $schema, $table );

	/**
	 * List the columns in the supplied table.
	 *
	 * @param string $schema
	 * @param string $table
	 * @return array(Column)
 	 */
	public abstract function getColumns( $schema, $table );

	/**
	 * List the indices in the supplied table.
	 *
	 * @param string $schema
	 * @param string $table
	 * @return array(Column)
 	 */
	public abstract function getIndices( $schema, $table );

	/**
	 * List the sequences in the supplied table
	 *
	 * @param string $schema
	 * @param string $table
	 * @return array(Sequence)
 	 */
	public abstract function getSequences( $schema, $table );

	/**
	 * Prepares a statement binding the provided positional params.
	 *
	 * The parameters can be either an array or individual function parameters (or null, for no params)
	 *
	 * @param string $query
	 * @param mixed $params
	 * @return PreparedStatement
	 */
	public function prepareWithParams( $query, $params = null ) {
		$stmt = $this->prepare( $query );
		if ( $params != null ) {
			if ( !is_array( $params ) ) {
				$params = array_slice( func_get_args(), 1 );
			}
			$stmt->setParams( $params );
		}
		return $stmt;
	}

	/**
	 * Executes the provided single value select $query, returning the value
	 * If the query doesn't yield a single value (multiple rows), or more than 1 field is requested,
	 * , this method will throw an Exception.
	 * For an insert, update or delete statement, this method will return the number of affected rows
	 *
	 * Examples:
	 * <code>
	 * // VALID EXAMPLES
	 * $val = $conn->getValue( "select count(*) from books" );
	 * $val = $conn->getValue( "select author from books where bookId=10" );
	 * $val = $conn->getValue( "update books set author='Author' where bookId=10" ); // $val will be affected_rows
	 *
	 * // NOT VALID
	 * $val = $conn->getValue( "select author, publisher from books where bookId=10" ); // MULTIPLE FIELDS
	 * $val = $conn->getValue( "select author from books" ); // MULTIPLE ROWS
	 * </code>
	 *
	 * @param string $query
	 * @return string The database value
	 * @throws SQLException If more than one row is returned or more than one value is requested.
	 */
	public function getValue( $query ) {
		$params = array_slice( func_get_args(), 1 );
		$stmt = $this->prepareWithParams( $query, $params );
		return $stmt->getValue();
	}

	/**
	 * Executes the provided single-field, multi-record query, returning
	 * the results as a list of values separated by comma.
	 *
	 * @param string $query
	 * @param mixed $params
	 * @return string The list of values
	 * @throws SQLException If more than one value is requested.
	 */
	public function getList( $query, $params=null ) {
		$stmt = $this->prepareWithParams( $query, $params );
		return $stmt->getList();
	}

	/**
	 * Gets the underlying PHP ResourceID
	 *
	 * @return PHPResourceID
	 */
	public function getResource() {
		return $this->resource;
	}

	/**
	 * Sets a connection flag. If the flag is preceded with the '~' modified, then the flag is unset.
	 * 
	 * Examples:
	 * <code>
	 * $db = ConnectionFactory::getDefault();
	 * $db->setFlag( Connection::DEBUG ); // Debugging enabled
	 * $db->setFlag( ~Connection::LOG ); // Logging disabled
	 * </code>
	 * 
	 * @param int $flag One of the Connection bitmask values.
	 */
	public function setFlag( $flag ) {
		$this->flags = ( ( $flag > 0 ) ? ( $this->flags | $flag ) : ( $this->flags & $flag ) );
	}

	/**
	 * Gets the Connection Flags
	 *
	 * @return int
	 */ 
	public function getFlags() {
		return $this->flags;
	}
	
	/**
	 * Sets the Fetch Mode (one of the MODE_* constants)
	 * 
	 * @param string $mode
	 */
	public function setMode( $mode ) {
		$this->mode = $mode;
	}
	
	/**
	 * Returns the Fetch Mode
	 * 
	 * @returns string $mode
	 */
	public function getMode() {
		return $this->mode;
	}

	/**
	 * Sets the Logger
	 *
	 * @param Logger $logger
	 */
	public function setLogger( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Sets the debug level (bitmaks).
	 */
	public function setDebugLevel( $debugLevel ) {
		$this->debugLevel = $debugLevel;
	}

	/**
	 * Returns the query count for this Connection
	 *
	 * @return int
	 */
	public function getQueryCount() {
		return $this->queryCount;
	}

	/**
	 * Returns the query execution total time
	 * 
	 * @return float
	 */
	public function getTotalTime() {
		return $this->totalTime;
	}

	/**
	 * Returns the query execution time
	 *
	 * @return float
	 */
	public function getQueryTime() {
		return $this->queryTime;
	}

	/**
	 * Logs the provided SQL string, according to flags set.
	 *
	 * @param string $sql The SQL to log.
	 */
	public function log( $sql ) {
		if ( !$this->logger ) {
			if ( !$this->logger = LoggerFactory::getDefault() ) {
				throw new SQLException( 'No Logger set, and no default Logger found.' );
			}
		}
		$msg = sprintf( 'Connection %s: %s', $this->name, preg_replace( '/[\n\t  ]/', ' ', $sql ) );
		$this->logger->logInfo( $msg );
	}

	public function getInClause( $name, array $arr ) {
		$binds = array();
		foreach( $arr as $key => $value ) {
			$binds[$name.$key] = $value;
		}
		$conds = join( ', ', array_keys( $binds ) );
		return array( $conds, $binds );
	}

	/**
	 * Returns a string representation of this Connection
	 *
	 * @return strin
	 */
	public function __toString() {
		$str = '';
		$str.= sprintf( "%s [%s]:\n", get_class( $this ), $this->id() );
		$str.= sprintf( "- Resource   : %s\n", $this->getResourceId() );
		$str.= sprintf( "- Query Count: %s\n", $this->flags & self::COUNT ? $this->queryCount : 'N/A' );
		$str.= sprintf( "- Total Time : %s\n", $this->flags & self::MEASURE ? $this->totalTime : 'N/A' );
		$str.= sprintf( "- Main Flags : %s\n", $this->flags );
		$str.= sprintf( "- Mode       : %s\n", $this->mode );
		$str.= sprintf( "- Logger     : %s\n", $this->logger );
		$str.= sprintf( "- Parameters :\n" );
		$str.= sprintf( "  * Host: %s\n", $this->host );
		$str.= sprintf( "  * User: %s\n", $this->user );
		$str.= sprintf( "  * Pass: %s\n", $this->pass );
		$str.= sprintf( "  * Path: %s\n", $this->path ? $this->path : '[None]' );
		return $str;
	}

	/**
	 * Performs counting, logging, debugging and measuring of a query.
	 *
	 * @param float $time The measured time before executing the query.
	 */
	public function queryDone( &$query, $time ) {
		$time2 = microtime( true ) - $time;
		$this->queryTime = $time2;
		if ( $this->flags & Connection::MEASURE ) {
			$took = sprintf( '(Took: %1.5f)', $time2 );
			$this->totalTime += $time2;
		}

		if ( $this->flags & Connection::COUNT ) {
			$this->queryCount++;
		}
		if ( $this->flags & Connection::LOG ) {
			$this->log( sprintf( 'SQL [%s] %s=> %s', $this->resource, ( isset( $took ) ? $took : '' ), $query ) );
		}
		if ( $this->flags & Connection::DEBUG ) {
			printf( "SQL [%s] %s=> %s\n", $this->resource, ( isset( $took ) ? $took : '' ), $query );
		}
	}
}
