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

use core\CoreException;

/**
 * CURLException
 */
class CURLException extends CoreException {

	protected $errno = 0;
	protected $error = null;

	public function __construct( $errno, $error ) {
		$this->errno = $errno;
		$this->error = $error;

		$this->message = sprintf( 'Curl Error: [%s] %s', $errno, $error );
	}

	public function getErrorNumber() {
		return $this->errno;
	}

	public function getError() {
		return $this->error;
	}

}