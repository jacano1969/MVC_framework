<?php


namespace mvc;

/**
 * Config implementing classes define a means to load a configuration for an AppContext setup.
 */
interface AppConfig {

	/**
	 * Gets the App ID
	 *
	 * @return string
	 */
	public function getId();

	/**
	 * Gets the App Name
	 *
	 * @return string
	 */
	public function getName();

	/**
	 * Gets the config encoding
	 *
	 * @return string
	 */
	public function getEncoding();

	/**
	 * Gets the default config timezone
	 *
	 * @return string
	 */
	public function getTimezone();

	/**
	 * Gets the default config locale
	 *
	 * @return string
	 */
	public function getDefaultLocale();

	/**
	 * Gets an array of supported locales, other than the default (no need to redefine it)
	 *
	 * @return array
	 */
	public function getSupportedLocales();

	/**
	 * Gets an array of logger definitions
	 *
	 * Logger definitions can have several properties:
	 *  - name: Optional. The logger name. if no name, then it's assumed to be the default logger ('default')
	 *  - path: Required. The logger path. See LoggerFactory for supported paths.
	 *  - level: Required. The log level. See Logger for log levels.
	 */
	public function getLoggers();

	/**
	 * Gets an array of factory definitions
	 *
	 * Factory definitions have a name, and N namespaces for each one.
	 */
	public function getFactories();

	/**
	 * Gets an array of classpath entries
	 */
	public function getClassPathEntries();

	/**
	 * Gets an array of connection driver definitions
	 *
	 * Connection drivers have a name and a class that must extend php\sql\Connection, registering the driver.
	 */
	public function getConnectionDrivers();

	/**
	 * Gets an array of connection definitions
	 *
	 * Connection definitions have a name, url, and optional flags
	 */
	public function getConnections();

	/**
	 * Gets the session handler definition
	 *
	 * Session Handler definitions have a class and handler-specific properties
	 */
	public function getSessionHandler();

	/**
	 * Gets the views definitions
	 * 
	 * View definitions have:
	 * - name: Required. The view name, registering the available extension.
	 * - class: Required. The view class.
	 * - loaders: Optional. Array of ViewLoaders. Each view loader has:
	 *   * class: Required. The ViewLoader class
	 *   * props: Array of name => value properties
	 * - compilers: Optional. Array of ViewCompilers. Each view compiler has:
	 *   * class: Required. The ViewCompiler class
	 *   * props: Array of name => value properties
	 *
	 * @return array
	 */
	public function getViews();

	/**
	 * Gets the error view definitions. 
	 * Error view definitions have:
	 * - class: Array key. Required. Exception class to render with this error view
	 * - name: Required. Error view name
	 * - module: Optional. Error view module
	 *
	 * @return array
	 */public function getErrorViews();

	/**
	 * Gets a list of debug-ips
	 */
	public function getDebugIps();

	/**
	 * Loads a AppConfigHandler with this Config values.
	 */
	public function loadHandler( AppConfigHandler $handler );

	/**
	 * Returns a string representation of this Config, that should be something meaningful for the implementing class (such as the loaded file for file-based config implementations)
	 *
	 * @return string
	 */
	public function __toString();

}
