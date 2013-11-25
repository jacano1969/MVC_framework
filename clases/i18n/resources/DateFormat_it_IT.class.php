<?php

namespace i18n\resources;

use i18n\DateFormat;

/**
 * Formatter for Italian (Italy)
 */
class DateFormat_it_IT extends DateFormat {

	protected $shortMonths = array(
					  1 => 'Gen'
					, 2 => 'Feb'
					, 3 => 'Mar'
					, 4 => 'Apr'
					, 5 => 'Mag'
					, 6 => 'Giu'
					, 7 => 'Lug'
					, 8 => 'Ago'
					, 9 => 'Set'
					, 10 => 'Ott'
					, 11 => 'Nov'
					, 12 => 'Dic'
				);

	protected $longMonths = array(
					  1 => 'Gennaio'
					, 2 => 'Febbraio'
					, 3 => 'Marzo'
					, 4 => 'Aprile'
					, 5 => 'Maggio'
					, 6 => 'Giugno'
					, 7 => 'Luglio'
					, 8 => 'Agosto'
					, 9 => 'Settembre'
					, 10 => 'Ottobre'
					, 11 => 'Novembre'
					, 12 => 'Dicembre'
				);

	protected $shortWeekDays = array(
					  0 => 'Dom'
					, 1 => 'Lun'
					, 2 => 'Mar'
					, 3 => 'Mer'
					, 4 => 'Gio'
					, 5 => 'Ven'
					, 6 => 'Sab'
				);

	protected $longWeekDays = array(
					  0 => 'Domenica'
					, 1 => 'Lunedi'
					, 2 => 'Martedi'
					, 3 => 'Mercoledi'
					, 4 => 'Giovedi'
					, 5 => 'Venerdi'
					, 6 => 'Sabato'
				);

	protected $dateFormats = array(
				  self::DATE_FORMAT_DEFAULT	=> '%d %b %Y'
				, self::DATE_FORMAT_SHORT	=> '%d/%M/%Y'
				, self::DATE_FORMAT_MEDIUM	=> '%d %b %Y'
				, self::DATE_FORMAT_LONG	=> '%d %B %Y'
				, self::DATE_FORMAT_FULL	=> '%W, %d %B %Y'
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
