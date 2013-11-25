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
 * AuthException
 */
class AuthException extends CoreException {

	const LOGIN_REQUIRED		= 1;
	const ACCESS_DENIED			= 2;
	const SESSION_EXPIRED		= 3;
	const SESSION_ERROR			= 4;
	const INVALID_CREDENTIALS	= 5;
	const UNKNOWN_ERROR			= 6;

	public function __construct( AppRequest $request, $error=0 ) {
		$this->error = $error;

		switch( $this->error ) {
			case self::UNKNOWN_ERROR: $errmsg = 'Unknown Error'; break;
			case self::LOGIN_REQUIRED: $errmsg = 'Login Required'; break;
			case self::ACCESS_DENIED: $errmsg = 'Access Denied'; break;
			case self::SESSION_EXPIRED: $errmsg = 'Session Expired'; break;
			case self::SESSION_ERROR: $errmsg = 'Session Error'; break;
			case self::INVALID_CREDENTIALS: $errmsg = 'Invalid Credentials'; break;
		}

		$this->message = sprintf( 'Unauthorized Action: "%s.%s": [%d] %s', $request->getController()->getName(), $request->getAction(), $this->error, $errmsg );
	}

	public function getError() {
		return $this->error;
	}

}
