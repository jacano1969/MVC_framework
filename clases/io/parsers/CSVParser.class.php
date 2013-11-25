<?php


namespace io\parsers;

use core\Object;
use io\InputStreamReader;
use io\InputStreamParser;
use io\IOException;

/**
 * CSVParser parses csv content, with the provided settings
 *
 */
class CSVParser extends Object implements InputStreamParser {

	/**
	 * Field separator (such as ',' or ';')
	 */
	protected $separator = null;

	/**
	 * Optional field text wrapper (such as ' or ")
	 */
	protected $wrapper = null;

	/**
	 * Array of field names.
	 */
	protected $fields = null;

	/**
	 * Current parsed data
	 */
	protected $data = array();

	/**
	 * Instantiates a new CSVParsers with the supplied field separator (default ',') and text wrapper (default none).
	 *
	 * @param string $separator
	 * @param string $wrapper
	 */
	public function __construct( $separator=',', $wrapper=null, array $fields=null ) {
		$this->separator = $separator;
		$this->wrapper = $wrapper;
		$this->fields = $fields;
	}

	/**
	 * Parses an InputStreamReader chunk of data, getting its csv data.
	 *
	 * @param InputStreamReader $reader
	 * @param string $data
	 */
	public function parse( InputStreamReader $reader, $data ) {
		$data = str_getcsv( $data, $this->separator, $this->wrapper );
		if ( $this->fields !== null ) {
			if ( sizeof( $this->fields ) != sizeof( $data ) ) {
				throw new Exception( sprintf( 'Error parsing position %s: Expected %d fields, parsed %d', $reader->key(), sizeof( $this->fields ), sizeof( $data ) ) );
			} else {
				$data = array_combine( $this->fields, $data );
			}
		}
		return $data;
	}

}
