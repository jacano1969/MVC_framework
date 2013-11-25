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

use core\Object;

/**
 * HTTPAuth class. Standarized method for http based authentication.
 */
class HTTPAuth extends Object {

	public static function request( $realm, $messageDenied='Accesso Denegado' ) {
		header( 'WWW-Authenticate: Basic realm="'.$realm.'"' );
		header( 'HTTP/1.0 401 Unauthorized' );
		echo $messageDenied;
		exit;
	}

	public static function getUser() {
		return isset( $_SERVER['PHP_AUTH_USER'] ) ? $_SERVER['PHP_AUTH_USER'] : null;
	}

	public static function getPass() {
		return isset( $_SERVER['PHP_AUTH_PW'] ) ? $_SERVER['PHP_AUTH_PW'] : null;
	}
	
}
