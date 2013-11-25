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
* @version $Rev: 13917 $
* @updated $Date$
*
* @copyright The MVC framework Team <lucasrsp@gmail.com> http://MVC framework.sf.net
*/

namespace io;

use core\Object;

/**
 * Defines an abstract InputStreamReader that can take any kind of InputStream and reads from it.
 */
abstract class InputStreamReader extends Object implements \Iterator {

	/**
	 * The InputStream
	 */
	protected $stream = null;

	/**
	 * InputStreamParser to use, if any
	 */
	protected $parser = null;

	/**
	 * Current data read
	 */
	protected $data = null;

	/**
	 * Current data position in the reader. Depends on the type of InputStreamReader
	 */
	protected $position = null;

	/**
	 * Instantiates an InputStreamReader for the supplied InputStream and optional InputStreamParser
	 *
	 * @param InputStream $stream
	 * @param InputStreamParser $parser
	 */
	public function __construct( InputStream $stream, InputStreamParser $parser=null ) {
		$this->stream = $stream;
		$this->parser = $parser;
		$this->open();
	}

	/**
	 * Gets the underlying InputStream for this reader
	 *
	 * @return InputStream
	 */
	public function getStream() {
		return $this->stream;
	}

	/**
	 * Gets the InputStreamParser for this reader
	 *
	 * @return InputStreamParser
	 */
	public function getParser() {
		return $this->parser;
	}

	/**
	 * Reads a chunk of data from the underlying input stream
	 *
	 * @param int $bytes Amount of bytes to read
	 */
	abstract public function read( $bytes=4096 );

	/**
	 * Opens the underlying InputStream, when necessary
	 */
	abstract public function open();

	/**
	 * Closes the underlying InputStream
	 */
	abstract public function close();

	/**
	 * Iterator::key
	 * Returns the current line number
	 *
	 * @return int
	 */
	public function key() {
		return $this->position;
	}

	/**
	 * Iterator::current.
	 * Returns the current data
	 *
	 * @return string
	 */
	public function current() {
		return $this->data;
	}

	/**
	 * Iterator::valid
	 * Returns whether the iterator is still valid
	 *
	 * @return boolean
	 */
	public function valid() {
		return $this->data !== null;
	}

	/**
	 * On destruction, close the InputStream handler
	 */
	/** COMMENTED DUE TO UNEXPECTED SEGMENTATION FAULT BUGS
	public function XXXXX__destruct() {
		$this->close();
	}
	*/

}
