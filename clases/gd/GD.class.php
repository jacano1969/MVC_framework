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
 * @version $Rev: 16464 $
 * @updated $Date$
 *
 * @copyright The MVC framework Team <lucasrsp@gmail.com> http://MVC framework.sf.net
 */

namespace gd;

use core\Object;

/**
 * GD Wrapper. Convenience methods that wrap the underlying GD Library
 */
class GD extends Object {

	const TYPE_JPEG = IMAGETYPE_JPEG;
	const TYPE_PNG = IMAGETYPE_PNG;
	const TYPE_GIF = IMAGETYPE_GIF;
    const TYPE_SWF = IMAGETYPE_SWF;

	private static $info = null;
	private static $types = null;

	/**
	 * Checks whether the GD Library is loaded, throwing an exception if it isn't
	 */
	public static function __static() {
		if ( !extension_loaded( 'gd' ) ) {
			throw new GDException( 'GD Library not loaded' );
		}
	}

	/**
	 * Loads GD Library info
	 */
	private static function loadInfo() {
		if ( static::$info === null ) {
			static::$info = gd_info();
		}
	}

	/**
	 * Loads GD Library supported types
	 */
	private static function loadSupportedTypes() {
		if ( static::$types === null ) {
			static::$types = array();
			$typesBit = imagetypes();
			if ($typesBit & IMG_PNG) $types[] = self::TYPE_PNG;
			if ($typesBit & IMG_JPG) $types[] = self::TYPE_JPEG;
			if ($typesBit & IMG_GIF) $types[] = self::TYPE_GIF;
            if ($typesBit & IMG_SWF) $types[] = self::TYPE_SWF;
		}
	}

	/**
	 * Gets GD Library info
	 *
	 * @return array
	 */
	public static function getInfo() {
		static::loadInfo();
		return static::$info;
	}

	/**
	 * Gets GD Library version
	 */
	public static function getVersion() {
		static::loadInfo();
		return static::$info['GD Version'];
	}

	/**
	 * Returns whether the installed GD Library supports the supplied Type
	 *
	 * @param string $type
	 */
	public static function supports( $type ) {
		static::loadSupportedTypes();
		return in_array($type, static::$types);
	}
}
