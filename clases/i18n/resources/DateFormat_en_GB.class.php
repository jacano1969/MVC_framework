<?php

namespace i18n\resources;

use i18n\DateFormat;

/**
 * Formatter for English (Great Britain)
 */
class DateFormat_en_GB extends DateFormat {

	protected $shortMonths = array(
					  1 => 'Jan'
					, 2 => 'Feb'
					, 3 => 'Mar'
					, 4 => 'Apr'
					, 5 => 'May'
					, 6 => 'Jun'
					, 7 => 'Jul'
					, 8 => 'Aug'
					, 9 => 'Sep'
					, 10 => 'Oct'
					, 11 => 'Nov'
					, 12 => 'Dec'
				);

	protected $longMonths = array(
					  1 => 'January'
					, 2 => 'February'
					, 3 => 'March'
					, 4 => 'April'
					, 5 => 'May'
					, 6 => 'June'
					, 7 => 'July'
					, 8 => 'August'
					, 9 => 'September'
					, 10 => 'October'
					, 11 => 'November'
					, 12 => 'December'
				);

	protected $shortWeekDays = array(
					  0 => 'Sun'
					, 1 => 'Mon'
					, 2 => 'Tue'
					, 3 => 'Wed'
					, 4 => 'Thu'
					, 5 => 'Fri'
					, 6 => 'Sat'
				);

	protected $longWeekDays = array(
					  0 => 'Sunday'
					, 1 => 'Monday'
					, 2 => 'Tuesday'
					, 3 => 'Wednesday'
					, 4 => 'Thursday'
					, 5 => 'Friday'
					, 6 => 'Saturday'
				);

	protected $dateFormats = array(
				  self::DATE_FORMAT_DEFAULT	=> '%b %d %Y'
				, self::DATE_FORMAT_SHORT	=> '%m-%d-%Y'
				, self::DATE_FORMAT_MEDIUM	=> '%b %d %Y'
				, self::DATE_FORMAT_LONG	=> '%B %d %Y'
				, self::DATE_FORMAT_FULL	=> '%W, %B %d, %Y'
				, self::DATE_FORMAT_SQL		=> '%Y-%M-%D'
				);

	protected $timeFormats = array(
				  self::TIME_FORMAT_DEFAULT	=> '%H:%I'
				, self::TIME_FORMAT_SHORT	=> '%H:%I'
				, self::TIME_FORMAT_LONG	=> '%H:%I:%S'
				, self::TIME_FORMAT_FULL	=> '%H:%I:%S %Z'
				, self::TIME_FORMAT_SQL		=> '%H:%I:%S'
				);
}
