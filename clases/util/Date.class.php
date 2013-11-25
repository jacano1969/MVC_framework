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
use core\Comparable;
use i18n\DateFormat;
use i18n\FormatException;

/**
 * General purpose Date class.
 * 
 * It provides several convenience methods for parsing and formatting dates.
 */
class Date extends Object implements Comparable {

	/**
	 * Time Unit constants
	 */
	const SECONDS = 0;
	const MINUTES = 1;
	const HOURS = 2;
	const DAY = 3;
	const WEEKDAY = 4;
	const MONTH = 5;
	const WEEKYEAR = 6;
	const YEAR = 7;
	const WEEK = 8;
	const TIMEZONE = 9;
	
	/**
	 * The underlying timestamp.
	 */
	protected $time = null;

	/**
	 * Date fields
	 */
	protected $fields = array(
			  self::SECONDS 	=> 0
			, self::MINUTES 	=> 0
			, self::HOURS		=> 0
			, self::DAY			=> 0
			, self::WEEKDAY		=> 0
			, self::MONTH		=> 0
			, self::WEEKYEAR	=> 0
			, self::YEAR		=> 0
			, self::WEEK		=> 0
			);

	/**
	 * Wether this Date's time needs to be calculated (if fields have changed)
	 */
	protected $mustCalculateTime = false;

	/**
	 * Wether this Date's fields need to be calculated (if time has changed)
	 */
	protected $mustCalculateFields = false;

	/**
	 * Instantiates a new Date object with the given timestamp.
	 * If null, the time is now
	 *
	 * @param int $time
	 */
	public function __construct( $time=0 ) {
		if ( $time !== null ) {
			$time = ( $time !== 0 ? $time : time() );
			$this->time = $time;
			$this->mustCalculateFields = true;
		}
	}

	/**
	 * Gets a Date object from a PHP DateTime object.
	 *
	 * @param DateTime $date
	 * @return Date
	 */
	public static function fromPhpDate( \DateTime $date ) {
		return new Date( $date->getTimestamp() );
	}

	/**
	 * Sets the default timezone.
	 *
	 * @param string $tz
	 */
	public static function setDefaultTimezone( $tz ) {
		date_default_timezone_set( $tz );
	}

	/**
	 * Returns the underlying timestamp
	 *
	 * @return int
	 */
	public function getTime() {
		if ( $this->mustCalculateTime ) {
			$this->calculateTime();
		}
		return $this->time;
	}

	/**
	 * Sets the timestamp.
	 *
	 * @param int $time
	 */ 
	public function setTime( $time ) {
		$this->time = $time;
		$this->mustCalculateFields = true;
	}

	/**
	 * Performs Date add
	 *
	 * @param int $type
	 * @param int $number
	 */
	public function add( $type, $number ) {
		return $this->arithmeticOp( $type, '+', $number );
	}

	/**
	 * Performs Date substraction
	 *
	 * @param int $type
	 * @param int $number
	 */
	public function substract( $type, $number ) {
		return $this->arithmeticOp( $type, '-', $number );
	}

	/**
	 * Performs arithmethic (only + or -) operation on current object
	 *
	 * It uses strtotime to perform complex arithmetic (adding weeks, days, months, years)
	 * @param int $type
	 * @param string $op
	 * @param int $number
	 *
	 */
	private function arithmeticOp( $type, $op, $number )
	{
		$seconds = 0;
		$tm_exp = null;

		switch( $type ) {
			case self::SECONDS:	$seconds = $number; break; 
			case self::MINUTES:	$seconds = $number * 60; break;
			case self::HOURS:	$seconds = $number * 3600; break;
			case self::WEEK:	$tm_exp = "$number week"; break;
			case self::DAY:		$tm_exp = "$number days"; break;
			case self::MONTH:	$tm_exp = "$number months"; break;
			case self::YEAR:	$tm_exp = "$number years"; break;
		}

		if ($seconds && $op=='+') {
			$this->time += $seconds;
		}
		elseif ($seconds && $op=='-') {
			$this->time -= $seconds;
		}
		elseif ($tm_exp!=null) {
			$this->time = strtotime( $op. $tm_exp, $this->time );
		}
		$this->mustCalculateFields = true;

		return $this;
	}

	/**
	 * Parses a date-time string, instantiating a Date object.
	 *
	 * @param string $date
	 * @return Date
	 */
	public static function parse( $datetime, $format=null ) {
        if ( $datetime == '0000-00-00' ) {
            return new NullDate();
        } elseif ( $format === null && ( $t = strtotime( $datetime ) ) !== false ) {
			return new Date( $t );
		} elseif ( $format !== null ) {
			return DateFormat::getDefault()->parse( $datetime, $format );
		} else {
			return new NullDate();
		}
	}

	/**
	 * Formats the Date based on one of the DateFormat default formats
	 *
	 * This method uses the default DateFormat instance (for the default locale)
	 *
	 * @param it $format
	 * @return string
	 */
	public function format( $format=null ) {
		if ( $format == null ) {
			$format = DateFormat::DATE_FORMAT_DEFAULT;
		}
		return DateFormat::getDefault()->date( $this, $format );
	}

	/**
	 * Formats the time based on one of the DateFormat default formats
	 *
	 * This method uses the default DateFormat instance (for the default locale)
	 *
	 * @param it $format
	 * @return string
	 */
	public function time( $format=null ) {
		if ( $format == null ) {
			$format = DateFormat::TIME_FORMAT_DEFAULT;
		}
		return DateFormat::getDefault()->time( $this, $format );
	}

	/**
	 * Formats the Date with a custom format (those available to strftime function)
	 *
	 * @param string $format
	 * @return string
	 */
	public function str( $format ) {
		return strftime( $format, $this->time );
	}

	/**
	 * Formats a string in SQL ISO Format (YYYY-MM-DD)
	 *
	 * @return string
	 */
	public function toSql() {
		return $this->format( DateFormat::DATE_FORMAT_SQL );
	}

	/**
	 * Formats a string in SQL ISO Format, including time (YYYY-MM-DD HH:II::SS)
	 *
	 * @return string
	 */
	public function toSqlFull() {
		$df = DateFormat::getDefault();
		return $df->date($this, DateFormat::DATE_FORMAT_SQL ) . ' ' . $df->time($this, DateFormat::TIME_FORMAT_SQL );
	}

	/**
	 * Overloaded field getter.
	 *
	 * @param string $field The field to get
	 * @return mixed
	 */
	public function __get( $field ) {
		if ( $this->mustCalculateFields ) {
			$this->calculateFields();
			$this->mustCalculateFields = false;
		}
		switch( $field ) {
			case 'seconds':		return $this->fields[self::SECONDS];
			case 'minutes':		return $this->fields[self::MINUTES];
			case 'hours':		return $this->fields[self::HOURS];
			case 'day':		return $this->fields[self::DAY];
			case 'weekday':		return $this->fields[self::WEEKDAY];
			case 'month':		return $this->fields[self::MONTH];
			case 'weekyear':	return $this->fields[self::WEEKYEAR];
			case 'year':		return $this->fields[self::YEAR];
			case 'tz':		return $this->fields[self::TIMEZONE];
			default:
				throw new FormatException( sprintf( 'Unrecognized date field "%s"', $field ) );
		}
	}

	/**
	 * Overloaded field setter
	 *
	 * @param string $field
	 * @param string $value
	 */
	public function __set( $field, $value ) {
		if ( $this->mustCalculateFields ) {
			$this->calculateFields();
			$this->mustCalculateFields = false;
		}
		switch( $field ) {
			case 'seconds':		$this->fields[self::SECONDS] = $value; break;
			case 'minutes':		$this->fields[self::MINUTES] = $value; break;
			case 'hours':		$this->fields[self::HOURS] = $value; break;
			case 'day':		$this->fields[self::DAY] = $value; break;
			case 'weekday':		$this->fields[self::WEEKDAY] = $value; break;
			case 'month':		$this->fields[self::MONTH] = $value; break;
			case 'weekyear':	$this->fields[self::WEEKYEAR] = $value; break;
			case 'year':		$this->fields[self::YEAR] = $value; break;
			default:
				throw new FormatException( sprintf( 'Unrecognized date field "%s"', $field ) );
		}
		$this->mustCalculateTime = true;
	}

	/**
	 * Return fields as array
	 *
	 * @return array The Fields.
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 * Calculates the fields according to the time.
	 */
	private function calculateFields() {
		$data = getdate( $this->time );
		$this->fields[self::SECONDS]	= $data['seconds'];
		$this->fields[self::MINUTES]	= $data['minutes'];
		$this->fields[self::HOURS]	= $data['hours'];
		$this->fields[self::DAY]	= $data['mday'];
		$this->fields[self::WEEKDAY]	= $data['wday'];
		$this->fields[self::MONTH]	= $data['mon'];
		$this->fields[self::WEEKYEAR]	= date('W', $this->time);
		$this->fields[self::YEAR]	= $data['year'];
		$this->fields[self::TIMEZONE]	= '';

		$this->mustCalculateFields = false;
	}

	/**
	 * Calculates the time according to the fields.
	 */
	private function calculateTime() {
		$this->time = mktime( $this->fields[self::HOURS], $this->fields[self::MINUTES]
					, $this->fields[self::SECONDS], $this->fields[self::MONTH]
					, $this->fields[self::DAY], $this->fields[self::YEAR] );

		$this->mustCalculateTime = false;
	}

	/**
	 * Returns a string representation of this Date object.
	 * This essentially calls Date::format with DATE_FORMAT_DEFAULT
	 *
	 * @return string
	 */
	public function __toString() {
		try {
			return $this->format( DateFormat::DATE_FORMAT_DEFAULT );
		} catch ( FormatException $e ) {
			return sprintf( 'Date Formatting Error: "%s"', $e->getMessage() );
		} catch ( Exception $e ) {
			print_r($e);
		}
	}

	/**
	 * Returns wether two Date Objects are equal.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function equals( Object $obj ) {
		return $obj->time == $this->time;
	}

	/**
	 * Returns wether this Date Object is after than the compared one.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function greaterThan( Object $obj ) {
		return $this->time > $obj->time;
	}

	/**
	 * Returns wether this Date Object is before than the compared one.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function lesserThan( Object $obj ) {
		return $this->time < $obj->time;
	}

	/**
	 * Returns wether this Date Object is after or equal than the compared one.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function greaterOrEqualThan( Object $obj ) {
		return $this->time >= $obj->time;
	}

	/**
	 * Returns wether this Date Object is before or equal than the compared one.
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function lesserOrEqualThan( Object $obj ) {
		return $this->time <= $obj->time;
	}

	/**
	 * Returns a NullDate Object
	 *
	 * @return Date
	 */
	public static function nullDate() {
		return new NullDate();
	}

}

/**
 * Represents a Null Date.
 */
class NullDate extends Date {

	/**
	 * Instantiates a null Date
	 * 
	 * @param int $time null
	 */
	public function __construct( $time=null ) {
		$this->time = null;
	}

	/**
	 * Returns null
	 */
	public function toSql() {
		return null;
	}

	/**
	 * Returns ''
	 */
	public function format( $format=null ) {
		return '';
	}

	/**
	 * Returns ''
	 */
	public function __toString() {
		return '';
	}

	/**
	 * Returns wether the given $date is also null
	 *
	 * @param Object $obj
	 * @return boolean
	 */
	public function equals( Object $obj ) {
		return $obj->time === null;
	}

	/**
	 * Returns False 
	 *
	 * @param Object $obj
	 */
	public function greaterThan( Object $obj ) {
		return false;
	}

	/**
	 * Returns False
	 *
	 * @param Object $obj
	 */
	public function lesserThan( Object $obj ) {
		return false;
	}

	/**
	 * Returns False
	 *
	 * @param Object $obj
	 */
	public function greaterOrEqualThan( Object $obj ) {
		return false;
	}

	/**
	 * Returns False
	 *
	 * @param Object $obj
	 */
	public function lesserOrEqualThan( Object $obj ) {
		return false;
	}
}
