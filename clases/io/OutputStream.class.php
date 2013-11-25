<?php

namespace io;

use core\Object;

/**
 * Defines an InputStream
 *
 */
interface OutputStream {

	/**
	 * Returns an OutputStreamWriter for this OutputStream
	 */
	public function getWriter();

}
