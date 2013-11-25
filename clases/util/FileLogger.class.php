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

namespace util;

/**
 * FileLogger. Logs messages to files.
 */
class FileLogger extends Logger {

	/**
	 * Log File
	 */
	private $file;

	/**
	 * Wether the Log has been open.
	 */
	private $open = false;

	/**
	 * Log File pointer
	 */
	private $fp = null;

	/**
	 * Instantiates a new FileLogger with the given file as $params['path']
	 *
	 * @param int $level
	 * @param array $params
	 */
	public function __construct( $name, $level, array $params ) {
		parent::__construct( $name, $level, $params );
		$this->file = $params['path'];
	}
	
	/**
	 * Opens the log file for writing.
	 */
	public function open() {
		if ( !( $this->fp = @fopen( $this->file, 'at' ) ) ) {
			printf( 'Could not open Log File "%s". Check path and permissions', $this->file );
			exit(1);
		} else {
			$this->open = true;
		}
	}

	/**
	 * Writes a string to the Log File.
	 *
	 * @param string $str The string to write.
	 */
	public function write( $str ) {
		if ( !$this->open ) {
			$this->open();
		}
		if ( !$this->fp ) {
			printf( "No File Pointer! Can't write to log" );
			exit(1);
		}
		fwrite( $this->fp, $str );
		fflush( $this->fp );
	}

	/**
	 * Returns a string representation of this FileLogger
	 *
	 * @return string
	 */
	public function __toString() {
		return sprintf( '%s (File: %s), (Level: %d)', get_class( $this ), $this->file, $this->level );
	}

}
