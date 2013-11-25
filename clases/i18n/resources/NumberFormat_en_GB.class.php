<?php

namespace i18n\resources;

use i18n\NumberFormat;

/**
 * NumberFormat Class for English (GB)
 */
class NumberFormat_en_GB extends NumberFormat {

	protected $decimals = 2;

	protected $decSeparator = '.';

	protected $thdSeparator = ',';

	protected $currencySymbol = 'GBP';

	protected $currencySymbolHtml = '&#163;';

	protected $currencySymbolPos = self::CURRPOS_BEFORE;

}
