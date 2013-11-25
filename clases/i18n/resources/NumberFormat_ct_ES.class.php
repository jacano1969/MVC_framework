<?php

namespace i18n\resources;

use i18n\NumberFormat;

/**
 * NumberFormat Class for Catalan (Spain)
 */
class NumberFormat_ct_ES extends NumberFormat {

	protected $decimals = 2;

	protected $decSeparator = ',';

	protected $thdSeparator = '.';

	protected $currencySymbol = 'EUR';

	protected $currencySymbolHtml = '€';

	protected $currencySymbolPos = self::CURRPOS_AFTER;

}
