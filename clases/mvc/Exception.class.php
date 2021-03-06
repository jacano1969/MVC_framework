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
 * General purpose Application Exception
 */
class Exception extends CoreException {

	public function __construct( $message, array $exceptions=null ) {
		$this->message = $message;
		if ( $exceptions !== null && sizeof( $exceptions ) > 0 ) {
			$messages = array();
			foreach( $exceptions as $e ) {
				$messages[] = $e->getMessage();
			}
			$this->message = sprintf( '%s: %s', $this->message, join( ', ', $messages ) );
		}
	}
}
