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

use util\Factory;

/**
 * Implement this interface to provide AppRequestResolvers that return a controller, action and view.
 */
class RESTRequest extends AppRequest {

	/**
	 * Resolves the REST request string, which must be in the format:
	 *
	 * request=[module/]<controller>[:action][.view][&arg=value...]
	 *
	 * @param string $request
	 */
	public function resolve() {
		if ( preg_match( '/((\w*\/)*)(\w+)(\.\w+)?(\.\w+)*/', $this->request, $matches ) ) {
			$m = $matches[1] != null ? $matches[1] : ''; // Module path
			$c = $matches[3]; // Controller name
			$a = isset( $matches[4] ) && $matches[4] != null ? substr( $matches[4], 1 ) : 'default'; // Action
			$v = isset( $matches[5] ) && $matches[5] != null ? substr( $matches[5], 1 ) : null; // View
		} else {
			throw new AppException( sprintf( 'Invalid request: "%s"', $this->request ) );
		}
		$ns = str_replace( '/', '\\', $m );


		try {
			$this->controller = $this->resolveController( $m, $ns, $c );
			$this->module = $m;
			$this->action = $a ? $a : 'default';
            $this->controller->setAction($this->action);
			$this->view = $v;
		} catch ( CoreException $e ) {
			throw new AppException( sprintf( 'Error processing request: "%s".', $e->getMessage() ) );
		}
	}

	/**
	 * Resolves the Controller, and returns its instance. Call this method from the AppRequestResolver method to get an actual controller instance.
	 *
	 * @param string $module
	 * @param string $namespace
	 * @param string $name
	 * @return Controller
	 */
	protected function resolveController( $module, $namespace, $name ) {

		$class = sprintf( '%s%s', $namespace, str_replace( '_', '', $name )   . 'Controller' );
		return Factory::controller( $class, $this, $module, $name );
	}
}
