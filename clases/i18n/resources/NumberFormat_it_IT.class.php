<?php

namespace i18n\resources;

use i18n\NumberFormat;

/**
 * NumberFormat Class for Italian (Italy)
 */
class NumberFormat_it_IT extends NumberFormat {

	protected $decimals = 2;

	protected $decSeparator = ',';

	protected $thdSeparator = '.';

	protected $currencySymbol = 'EUR';

	protected $currencySymbolHtml = '&euro;';

	protected $currencySymbolPos = self::CURRPOS_AFTER;

}
