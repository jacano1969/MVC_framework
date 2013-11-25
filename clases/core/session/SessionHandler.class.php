<?php


namespace core\session;

use core\Object;

/**
 * This Interface is analog to PHP's native session save type
 */
interface SessionHandler {

	/**
	 * Opens the Session
	 *
	 * @param string $path
	 * @param string $name
	 */
	public function open( $path, $name );

	/**
	 * Closes the Session
	 */
	public function close();

	/**
	 * Reads the Session data
	 *
	 * @param string $id
	 */
	public function read( $id );

	/**
	 * Writes the Session data
	 *
	 * @param string $id
	 * @param string $data
	 */
	public function write( $id, $data );

	/**
	 * Destroy the Session data
	 *
	 * @param string $id
	 */
	public function destroy( $id );

	/**
	 * Garbage Collector.
	 *
	 * @param int $maxLifeTime
	 */
	public function gc( $maxLifeTime );

    /**
     * Get a list of live sessions
     *
     * @param int $maxLifeTime
     */
    public function getAliveIDs( $maxLifeTime );

	/**
	 * Sets a property for this Session Handler. Each session handler must define its own properties and property handling logic.
	 * 
	 * @param string $name
	 * @param string $value
	 */
	public function setProperty( $name, $value );

}
