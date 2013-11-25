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

namespace util\mail;

use core\Object;
use util\Validator;
use util\ValidationException;

/**
 * EmailValidator implements Validator for e-mail addresses
 *
 */
class EmailValidator implements Validator {

	/**
	 * Validates the supplied e-mail address.
	 *
	 * @throws ValidationException If validation fails.
	 */
	public function validate( $data ) {
		if ( !preg_match( '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $data ) ) {
			throw new ValidationException( sprintf( 'Invalid e-mail address: "%s"', $data ) );
		}
	}
}
