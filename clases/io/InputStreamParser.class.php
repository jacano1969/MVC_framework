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

/**
 * InputStreamParsers are meant to provide extra parsing functionality to an InputStreamReader, parsing each chunk of data as it is returned.
 */
interface InputStreamParser {

	/**
	 * Parses the supplied data as returned by the InputStreamReader
	 *
	 * @param InputStreamReader
	 * @param string $data
	 */
	public function parse( InputStreamReader $reader, $data );

}
