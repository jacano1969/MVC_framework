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
use core\CoreException;

include_once('Mail.php');

/**
 * General purpose Mail class.
 */
class Mail extends \Mail {
	/*

	protected $encoding = 'quoted-printable';
	protected $headEncoding = 'quoted-printable';
	protected $textEncoding = 'quoted-printable';
	protected $htmlEncoding = 'quoted-printable';
	
	protected $charset = 'ISO-8859-1';
	protected $headCharset = 'ISO-8959-1';
	protected $textCharset = 'ISO-8859-1';
	protected $htmlCharset = 'ISO-8859-1';
	
	protected $eol = "\r\n";
	protected $delayFileIO = false;

	public function __construct() {
	}

	public function setEncoding( $encoding ) {
		$this->encoding = $encoding;
		$this->setHeadEncoding( $encoding );
		$this->setTextEncoding( $encoding );
		$this->setHtmlEncoding( $encoding );
	}

	public function getEncoding() {
		return $this->encoding;
	}

	public function setCharset( $charset ) {
		$this->charset = $charset;
		$this->setHeadCharset( $charset );
		$this->setTextCharset( $charset );
		$this->setHtmlCharset( $charset );
	}

	public function getCharset() {
		return $this->charset;
	}

	public function setHeadEncoding( $encoding ) {
		$this->headEncoding = $encoding;
	}

	public function getHeadEncoding() {
		return $this->headEncoding;
	}

	public function setTextEncoding( $encoding ) {
		$this->textEncoding = $encoding;
	}

	public function getTextEncoding() {
		return $this->textEncoding;
	}

	public function setHtmlEncoding( $encoding ) {
		$this->htmlEncoding = $encoding;
	}

	public function getHtmlEncoding() {
		return $this->htmlEncoding;
	}

	public function setHeadCharset( $charset ) {
		$this->headCharset = $charset;
	}

	public function getHeadCharget() {
		return $this->headCharget;
	}

	public function setTextCharset( $charset ) {
		$this->textCharset = $charset;
	}

	public function getTextCharget() {
		return $this->textCharget;
	}

	public function setHtmlCharset( $charset ) {
		$this->htmlCharset = $charset;
	}

	public function getHtmlCharget() {
		return $this->htmlCharget;
	}

	public function setTextBody( $text ) {
		$this->textBody = $text;
	}

	public function getTextBody() {
		return $this->textBody;
	}

	public function setHTMLBody( $html ) {
		$this->htmlBody = $html;
	}

	public function getHTMLBody() {
		return $this->htmlBody;
	}

	public function addHTMLImageData( $name, $data, $type='application/octet-stream' ) {
		$this->images[] = array( 'name' => $name, 'type' => $type, 'data' => $data );
	}

	public function addHTMLImage( $name, $file, $type='application/octet-stream' ) {
		if ( !file_exists( $file ) ) {
			throw new CoreException( sprintf( 'File "%s" not found', $file ) );
		}
		$fp = fopen( $file, 'rb' );
		$data = fread( $fp, filesize( $file ) );
		fclose( $fp );

		$this->addHTMLImageData( $name, $data, $type );
	}

	public function addAttachmentData( $name, $data, $type='application/octet-stream' ) {
		$this->attachments[] = array( 'name' => $name, 'type' => $type, 'data' => $data );
	}

	public function addAttachment( $name, $file, $type='application/octet-stream' ) {
		if ( !file_exists( $file ) ) {
			throw new CoreException( sprintf( 'File "%s" not found', $file ) );
		}
		$fp = fopen( $file, 'rb' );
		$data = fread( $fp, filesize( $file ) );
		fclose( $fp );

		$this->addAttachmentData( $name, $data, $type );
	}
	 */
}
