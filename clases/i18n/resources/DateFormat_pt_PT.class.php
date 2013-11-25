<?php

namespace i18n\resources;

use i18n\DateFormat;

/**
 * Formatter for Spanish (Spain)
 */
class DateFormat_pt_PT extends DateFormat {

	protected $shortMonths = array(
					  1 => 'Jan'
					, 2 => 'Fev'
					, 3 => 'Mar'
					, 4 => 'Abr'
					, 5 => 'Mai'
					, 6 => 'Jun'
					, 7 => 'Jul'
					, 8 => 'Ago'
					, 9 => 'Set'
					, 10 => 'Out'
					, 11 => 'Nov'
					, 12 => 'Dez'
				);

	protected $longMonths = array(
					  1 => 'Janeiro'
					, 2 => 'Fevereiro'
					, 3 => 'Mar�o'
					, 4 => 'Abril'
					, 5 => 'Mayo'
					, 6 => 'Junho'
					, 7 => 'Julho'
					, 8 => 'Agosto'
					, 9 => 'Setembro'
					, 10 => 'Outubro'
					, 11 => 'Novembro'
					, 12 => 'Dezembro'
				);

	protected $shortWeekDays = array(
					  0 => 'Dom'
					, 1 => 'Seg'
					, 2 => 'Ter'
					, 3 => 'Qua'
					, 4 => 'Qui'
					, 5 => 'Sex'
					, 6 => 'Sab'
				);

	protected $longWeekDays = array(
					  0 => 'Domingo'
					, 1 => 'Segunda'
					, 2 => 'Ter�a'
					, 3 => 'Quarta'
					, 4 => 'Quinta'
					, 5 => 'Sexta'
					, 6 => 'S�bado'
				);

	protected $dateFormats = array(
				  self::DATE_FORMAT_DEFAULT	=> '%D %b %Y'
				, self::DATE_FORMAT_SHORT	=> '%d/%M/%Y'
				, self::DATE_FORMAT_MEDIUM	=> '%d %b %Y'
				, self::DATE_FORMAT_LONG	=> '%d %B %Y'
				, self::DATE_FORMAT_FULL	=> '%A, %d %B %Y'
				, self::DATE_FORMAT_SQL		=> '%Y-%M-%D'
				, self::DATE_FORMAT_WITH_TIME	=> '%D %b %Y %H:%I'
				);

	protected $timeFormats = array(
				  self::TIME_FORMAT_DEFAULT	=> '%H:%I'
				, self::TIME_FORMAT_SHORT	=> '%H:%I'
				, self::TIME_FORMAT_LONG	=> '%H:%I:%S'
				, self::TIME_FORMAT_FULL	=> '%H:%I:%S %Z'
				, self::TIME_FORMAT_SQL		=> '%H:%I:%S'
				);
}
