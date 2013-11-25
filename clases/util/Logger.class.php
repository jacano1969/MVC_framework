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
 * @author  $Author$
 * @version $Rev$
 * @updated $Date$
 *

 */

namespace util;

use core\Object;

/**
 * Base Logger Class.
 */
abstract class Logger extends Object
{

	/**
	 * Log Level Constants
	 */
	const LEVEL_ERROR     = 1;
	const LEVEL_EXCEPTION = 2;
	const LEVEL_WARNING   = 3;
	const LEVEL_INFO      = 4;
	const LEVEL_DEBUG     = 5;
	/**
	 * Logger Name
	 */
	protected $name = null;
	/**
	 * Logger threshold level
	 */
	protected $level = self::LEVEL_EXCEPTION;
	/**
	 * Logger parameters
	 */
	protected $params = array();

	/**
	 * Instantiates a new Logger Class with the given $params and log level
	 *
	 * @param string $name
	 * @param int    $level
	 * @param array  $params
	 */
	public function __construct($name, $level, array $params)
	{
		$this->name   = $name;
		$this->level  = $level;
		$this->params = $params;
	}

	/**
	 * Opens the Log for writing.
	 */
	abstract public function open();

	/**
	 * Gets the logger name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Gets the logger path
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->params['path'];
	}

	/**
	 * Gets the log level
	 *
	 * @return int
	 */
	public function getLevel()
	{
		return $this->level;
	}

	/**
	 * Sets the log level
	 *
	 * @param int $level
	 */
	public function setLevel($level)
	{
		$this->level = $level;
	}

	/**
	 * Logs a PHP Error.
	 *
	 * @param string $message
	 */
	public function logError($message)
	{
		$this->log(self::LEVEL_ERROR, $message);
	}

	/**
	 * Logs a Message for a given level
	 *
	 * @param string $message
	 * @param int    $level One of the LEVEL_* constants
	 */
	public function log($level, $message)
	{
		switch ($this->level) {
			case self::LEVEL_ERROR:
				$type = 'ERR';
				break;
			case self::LEVEL_EXCEPTION:
				$type = 'EXC';
				break;
			case self::LEVEL_WARNING:
				$type = 'WARN';
				break;
			case self::LEVEL_INFO:
				$type = 'INFO';
				break;
			case self::LEVEL_DEBUG:
				$type = 'DBG';
				break;
		}
		if ($level <= $this->level) {
			$msg = sprintf("[%s] [%s] %s:%s\n"
				, date('Y-m-d H:i:s')
				, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost'
				, $type
				, $message);
			$this->write($msg);
		}
	}

	/**
	 * Writes a string to the log.
	 *
	 * @param string $str The string to write.
	 */
	abstract public function write($str);

	/**
	 * Logs an Exception.
	 *
	 * @param string $message
	 */
	public function logException($message)
	{
		$this->log(self::LEVEL_EXCEPTION, $message);
	}

	/**
	 * Logs a Warning
	 *
	 * @param string $message
	 */
	public function logWarning($message)
	{
		$this->log(self::LEVEL_WARNING, $message);
	}

	/**
	 * Logs an Info Message
	 *
	 * @param string $message
	 */
	public function logInfo($message)
	{
		$this->log(self::LEVEL_INFO, $message);
	}

	/**
	 * Logs a debug message
	 *
	 * @param string $message
	 */
	public function logDebug($message)
	{
		$this->log(self::LEVEL_DEBUG, $message);
	}

	/**
	 * Returns a string representation of this logger
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sprintf('%s (Level: %d)', get_class($this), $this->level);
	}
}
