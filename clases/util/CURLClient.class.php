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
 * Wraps around the native PHP5 Curl Client.
 */
class CURLClient extends Object {

	/**
	 * URL
	 */
	private $url = null;

	/**
	 * CURL resource
	 */
	private $curl = null;

	/**
	 * CURL Parameters
	 */
	private $params = array();

	/**
	 * When included, this method statically checks curl client is compiled into php
	 *
	 * @throws CURLException
	 */
	public static function __static() {
		if ( !function_exists( 'curl_init' ) ) {
			throw new CURLException( 'cURL not available!' );
		}
	}

	/**
	 * Instantiates a CURLClient with default curl options (return transfer, follow location)
	 *
	 * @return CURLClient
	 */
	public function __construct() {
		$this->curl = curl_init();
		curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $this->curl, CURLOPT_FOLLOWLOCATION, true );
	}

	/**
	 * Sets CURL URL
	 *
	 * @param string $url
	 */
	public function setURL( $url ) {
		$this->url = $url;
		curl_setopt( $this->curl, CURLOPT_URL, $url );
	}

	/**
	 * Sets the CURL Cookie jar file, both for sending and storing cookies.
	 *
	 * @param string $file
	 */
	public function setCookiesFile( $file ) {
		curl_setopt( $this->curl, CURLOPT_COOKIEFILE, $file );
		curl_setopt( $this->curl, CURLOPT_COOKIEJAR, $file );
	}

	/**
	 * Clears parameters
	 */ 
	public function clearParams() {
		$this->params = array();
	}

	/**
	 * Adds a parameter
	 *
	 * @param string $param
	 * @param string $value
	 */
	public function addParam( $param, $value ) {
		$this->params[$param] = $value;
	}

	/**
	 * Performs a GET operation on the supplied url
	 *
	 * @param string $url
	 * @return string The fetched data
	 */
	public function get( $url ) {
		$this->setURL( $url );

		$data = curl_exec( $this->curl );
		if ( $errno = curl_errno( $this->curl ) ) {
			throw new CURLException( $errno, curl_error( $this->curl ) );
		}
		return $data;
	}

	/**
	 * Performs a POST operation on the supplied url
	 *
	 * @param string $url
	 * @return execution result
	 */
	public function post( $url ) {
		$this->setURL( $url );

		$params = '';
		foreach( $this->params as $p => $v ) {
			$params.= '&'.$p.'='.urlencode( $v );
		}
		curl_setopt( $this->curl, CURLOPT_POST, true );
		curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $params );

		return curl_exec( $this->curl );
	}

	/**
	 * Gets any curl errors
	 *
	 * @return string ("error number: error message")
	 */
	public function getError() {
		return sprintf( '%s: %s', curl_errno(), curl_error() );
	}
}
