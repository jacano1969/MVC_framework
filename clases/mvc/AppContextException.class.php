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

namespace mvc;

use core\CoreException;

/**
 * AppContextException
 */
class AppContextException extends CoreException {

	const ERROR_ENCODING = 1;
	const ERROR_TIMEZONE = 2;
	const ERROR_LOCALE = 3;
	const ERROR_LOGGER = 4;
	const ERROR_FACTORY = 5;
	const ERROR_CONNECTION = 6;
	const ERROR_SESSION = 7;
	const ERROR_VIEW = 8;

	/**
	 * Instantiates a new AppContextException with the supplied context error type and message
	 *
	 * @param int $error
	 * @param string $message
	 */
	public function __construct( $error, $message ) {
		$this->error = $error;
		switch( $this->error ) {
			case self::ERROR_ENCODING: $errmsg = 'Encoding Error'; break;
			case self::ERROR_TIMEZONE: $errmsg = 'Timezone Error'; break;
			case self::ERROR_LOCALE: $errmsg = 'Locale Error'; break;
			case self::ERROR_LOGGER: $errmsg = 'Logger Error'; break;
			case self::ERROR_FACTORY: $errmsg = 'Factory Error'; break;
			case self::ERROR_CONNECTION: $errmsg = 'Connection Error'; break;
			case self::ERROR_SESSION: $errmsg = 'Session Error'; break;
			case self::ERROR_VIEW: $errmsg = 'View Error'; break;
			default: 
				$errmsg = 'Unknown Error';
		}
		$this->message = sprintf( '[%d] %s: %s', $this->error, $errmsg, $message );
	}

	/**
	 * Gets the context error type
	 *
	 * @return int
	 */
	public function getError() {
		return $this->error;
	}

}
