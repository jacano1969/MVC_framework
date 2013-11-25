<?php

namespace i18n\resources;

use i18n\DateFormat;

/**
 * Formatter for Catalan (Spain)
 */
class DateFormat_ct_ES extends DateFormat {

	protected $shortMonths = array(
					  1 => 'Ene'
					, 2 => 'Feb'
					, 3 => 'Mar'
					, 4 => 'Abr'
					, 5 => 'May'
					, 6 => 'Jun'
					, 7 => 'Jul'
					, 8 => 'Ago'
					, 9 => 'Sep'
					, 10 => 'Oct'
					, 11 => 'Nov'
					, 12 => 'Dic'
				);

	protected $longMonths = array(
					  1 => 'Enero'
					, 2 => 'Febrero'
					, 3 => 'Marzo'
					, 4 => 'Abril'
					, 5 => 'Mayo'
					, 6 => 'Junio'
					, 7 => 'Julio'
					, 8 => 'Agosto'
					, 9 => 'Septiembre'
					, 10 => 'Octubre'
					, 11 => 'Noviembre'
					, 12 => 'Diciembre'
				);

	protected $shortWeekDays = array(
					  0 => 'Diu'
					, 1 => 'Dil'
					, 2 => 'Dma'
					, 3 => 'Dme'
					, 4 => 'Dij'
					, 5 => 'Div'
					, 6 => 'Dis'
				);

	protected $longWeekDays = array(
					  0 => 'Diumenge'
					, 1 => 'Dilluns'
					, 2 => 'Dimarts'
					, 3 => 'Dimecres'
					, 4 => 'Dijous'
					, 5 => 'Divendres'
					, 6 => 'Dissabte'
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
