<?php


namespace core;
use mvc\Exception;



/**
 * Base Class for Console Apps
 */
abstract class ConsoleApp extends Object {

	/*
	 * Console Output Colours.
	 */
	const WHITE   = 29;
	const GREY    = 30;
	const RED     = 31;
	const GREEN   = 32;
	const YELLOW  = 33;
	const BLUE    = 34;
	const MAGENTA = 35;
	const CYAN    = 36;

	const ARG_NONE	   = 0;
	const ARG_OPTIONAL = 1;
	const ARG_REQUIRED = 2;

	protected $bars = array( 0 => '|', 1 => '\\', 2 => '-', 3 => '/' );

	/**
	 * Array of running threads
	 */
	protected static $threads = array();

	/**
	 * The script calling this App
	 */
	protected static $script = null;

	/**
	 * Array of available short options
	 */
	protected static $shortOptions = array();

	/**
	 * Array of available long options
	 */
	protected static $longOptions = array();

	/**
	 * Array of available long option aliases
	 */
	protected static $longAliases = array();

	/**
	 * Array of option/arguments boolean values
	 */
	protected static $optionArgs = array();

	/**
	 * Array of available script arguments
	 */
	protected static $arguments = array();

	/**
	 * Array of boolean states for arguments, according to requirement
	 */
	protected static $argumentsRequired = array();

	/**
	 * Array of available options/arguments help messages
	 */
	protected static $help = array();

	/**
	 * Array of supplied options
	 */
	protected $supplied = array();

	/**
	 * Array of supplied option arguments
	 */
	protected $suppliedOptionArgs = array();

	/**
	 * Array of supplied arguments
	 */
	protected $suppliedArguments = array();

	/**
	 * Instantiates a new ConsoleApp
	 *
	 * This method is called by run()
	 *
	 * @param int $th
	 */
	protected function __construct() {
	}

	/**
	 * Main Application Loop. Called by run()
	 *
	 * @param int $argc
	 * @param array $argv
	 */
	abstract protected function main( $argc, $argv );

	/**
	 * Reads a single character on key press
	 *
	 * @param string $prompt
	 * @param array $accept
	 * @return char
	 */
	public static function readc( $prompt, array $accept = array() ) {
		$accept[] = '';
		do {
			echo "\n$prompt ";
			$char = strtolower( trim( shell_exec( 'read -n1 char; echo $char' ) ) );
		} while ( sizeof( $accept ) > 1 && !in_array( $char, $accept ) );

		return $char;
	}

	/**
	 * Call this to start a new thread.
	 * By default, the thread number 0 is instantiated and ran.
	 *
	 * @param int $th The thread number (default 0)
	 */
	public static function run( $threads=1 ) {
		static::$script = $_SERVER['argv'][0];
		try {
			$t = new static();
			$t->addOpt( 'h|help', 'This help scren' );
			$t->main( $_SERVER['argc'], $_SERVER['argv'] );
			exit(0);

		} catch ( Exception $ce ) {

			static::printError( $ce->getMessage() );
			static::printUsage();
			exit(1);
		}
	}

	/**
	 * Runs the provided method as a thread
	 *
	 * @param string $method
	 */
	protected function fork( $method ) {
		$pid = pcntl_fork();
		switch( $pid ) {
			case -1:
				throw new ConsoleException( 'Could not fork!' );
			case 0:
				// Children
				$this->$method();
				exit(0);
			default:
				// Parent
				static::$threads[] = $pid;
		}
		return $pid;
	}

	/**
	 * Wait for chldren to die.
	 */
	protected function wait() {
		foreach( static::$threads as $pid ) {
			pcntl_waitpid( $pid, $status );
		}
	}

	/**
	 * Prints a string followed by a newline character
	 */
	public static function println( $string ) {
		print $string."\n";
	}

	/**
	 * Returns a coloured string.
	 * 
	 * @param string $string
	 * @param int $colour
	 * @param boolean $bold
	 */
	public static function csprint( $string, $colour, $bold=true ) {
		return sprintf( "%c[%d;%dm%s%c[0m", 27, $bold ? 1 : 0 , $colour, $string, 27 );
	}

	/**
	 * Prints a coloured string.
	 *
	 * @param string $string
	 * @param int $colour
	 * @param boolean $bold
	 */
	public static function cprint( $string, $colour, $bold=true ) {
		print self::csprint( $string, $colour, $bold );
	}

	/**
	 * Prints a coloured string followed by a newline character.
	 *
	 * @param string $string
	 * @param int $colour
	 * @param boolean $bold
	 */
	public static function cprintln( $string, $colour, $bold=true ) {
		self::cprint( $string, $colour, $bold );
		print "\n";
	}

	/**
	 * Returns a coloured/formatted string.
	 * 
	 * @param string $string
	 * @param * args
	 */
	public static function csprintf( $string ) {
		$args = func_get_args();

		$patterns[] = '/(<white>(.*?)<\/white>)/';
		$patterns[] = '/(<grey>(.*?)<\/grey>)/';
		$patterns[] = '/(<red>(.*?)<\/red>)/';
		$patterns[] = '/(<green>(.*?)<\/green>)/';
		$patterns[] = '/(<yellow>(.*?)<\/yellow>)/';
		$patterns[] = '/(<blue>(.*?)<\/blue>)/';
		$patterns[] = '/(<magenta>(.*?)<\/magenta>)/';
		$patterns[] = '/(<cyan>(.*?)<\/cyan>)/';
		$replaces[] = self::csprint( '$2', self::WHITE );
		$replaces[] = self::csprint( '$2', self::GREY );
		$replaces[] = self::csprint( '$2', self::RED );
		$replaces[] = self::csprint( '$2', self::GREEN );
		$replaces[] = self::csprint( '$2', self::YELLOW );
		$replaces[] = self::csprint( '$2', self::BLUE );
		$replaces[] = self::csprint( '$2', self::MAGENTA );
		$replaces[] = self::csprint( '$2', self::CYAN );

		return vsprintf( preg_replace( $patterns, $replaces, $string ), array_slice( $args, 1 ) );
	}

	/**
	 * Prints a coloured/formatted string.
	 *
	 * @param string $string
	 * @param * args
	 */
	public static function cprintf( $string ) {
		$args = func_get_args();
		print call_user_func_array( array( 'self', 'csprintf' ), $args );
	}

	/**
	 * Adds an option, with the given $short-name|$long-name and $help
	 *
	 * @param string $opt
	 * @param string $help
	 */
	protected static function addOpt( $opt, $help ) {
		$o = static::parseOption( $opt );
		static::$help[$o] = $help; 
		return $o;
	}

	/**
	 * Adds an argument
	 *
	 * @param string $arg
	 * @param string $help
	 */
	protected static function addArg( $arg, $help ) {
		if ( substr( $arg, -1 ) == '*' ) {
			$arg = substr( $arg, 0, -1 );
			$req = self::ARG_REQUIRED;
		} else {
			$req = self::ARG_OPTIONAL;
		}
		static::$arguments[] = $arg;
		static::$argumentsRequired[$arg] = $req;
		static::$help[$arg] = $help;
	}

	/**
	 * Parses and Resolves the given arguments against the list of available ones.
	 *
	 * @param int $argc
	 * @param array $argv
	 */
	protected function getopts( $argc, $argv, $from=1 ) {
		$argn = 0;
		$parsing = true;
		for( $i=$from; $i<$argc; $i++ ) {
			$arg = $argv[$i];

			// Long Option
			if ( substr( $arg, 0, 2 ) == '--' && $parsing ) {
				$opt = substr( $arg, 2 );
				if ( in_array( $opt, static::$longOptions ) ) {
					$o = $this->supplied[] = $opt;
				} elseif ( isset( static::$longAliases[$opt] ) ) {
					$o = $this->supplied[] = static::$longAliases[$opt];
				} else {
					throw new Exception( sprintf( "Invalid Option -- '%s'", $arg ) );
				}
				if ( static::$optionArgs[$o] == self::ARG_REQUIRED && $i < ( $argc -1 ) ) {
					$i++;
					if ( isset( $argv[$i] ) && substr( $argv[$i], 0, 1 ) != '-' ) {
						$this->suppliedOptionArgs[$o] = $argv[$i];
					} else {
						throw new Exception( sprintf( "Option requires an argument -- '%s'", $o ) );
					}
				} elseif ( static::$optionArgs[$o] == self::ARG_REQUIRED ) {
					throw new Exception( sprintf( "Option requires an argument -- '%s'", $o ) );
				} elseif ( static::$optionArgs[$o] == self::ARG_OPTIONAL ) {
					$next = $i + 1;
					// Only get it if the next argument IS NOT an option
					if ( isset( $argv[$next] ) && substr( $argv[$next], 0, 1 ) != '-' ) {
						$i++;
						$this->suppliedOptionArgs[$o] = $argv[$i];
					}
				}

			// Short Option
			} elseif ( substr( $arg, 0, 1 ) == '-' && $parsing ) {
				$opt = substr( $arg, 1 );

				// Split and iterate the option string (to support '-abc' concatenated options)
				$arr = str_split( $opt );
				foreach( $arr as $j => $o ) {
					if ( !in_array( $o, static::$shortOptions ) ) {
						throw new Exception( sprintf( "Invalid Option -- '%s'", $arr[$j] ) );
					}
					$this->supplied[] = $o;

					// Requires an argument, and it's not the last one in the string...
					if ( static::$optionArgs[$o] == self::ARG_REQUIRED && $j != ( sizeof( $arr ) -1 ) ) {
						throw new Exception( sprintf( "Option requires an argument -- '%s'", $o ) );

					// Requires an argument, and it's the last one. Get next arg
					} elseif ( static::$optionArgs[$o] == self::ARG_REQUIRED ) {
						$i++;
						// Only get it if the next argument IS NOT an option
						if ( isset( $argv[$i] ) && substr( $argv[$i], 0, 1 ) != '-' ) {
							$this->suppliedOptionArgs[$o] = $argv[$i];
						} else {
							throw new Exception( sprintf( "Option requires an argument -- '%s'", $o ) );
						}

					// It has an optional argument, and it's the last one in the string...
					} elseif ( static::$optionArgs[$o] == self::ARG_OPTIONAL && $j == ( sizeof( $arr ) -1 ) ) {
						$next = $i + 1;
						// Only get it if the next argument IS NOT an option
						if ( isset( $argv[$next] ) && substr( $argv[$next], 0, 1 ) != '-' ) {
							$i++;
							$this->suppliedOptionArgs[$o] = $argv[$i];
						}
					} 
				}

			// Plain Argument
			} else {
				if ( isset( static::$arguments[$argn] ) ) {
					$this->suppliedArguments[static::$arguments[$argn]] = $arg;
					$argn++;
				}
				$parsing = false;
			}

			if ( $this->opt('h') ) {
				static::printUsage();
				exit;
			}
		}
		foreach( static::$argumentsRequired as $arg => $req ) {
			if ( $req == self::ARG_REQUIRED && !isset( $this->suppliedArguments[$arg] ) ) {
				throw new Exception( sprintf( 'Required Argument "%s" not supplied', $arg ) );
			}
		}
	}

	/**
	 * Parses a provided Option, setting its availability
	 *
	 * @param string $option An option in the format s|long-opt (whic resolves to -s, --long-option)
 	 */
	private static function parseOption( $option ) {
		if ( substr( $option, -2 ) == '::' ) {
			$arg = self::ARG_OPTIONAL;
			$option = substr( $option, 0, -2 );
		} elseif ( substr( $option, -1 ) == ':' ) {
			$arg = self::ARG_REQUIRED;
			$option = substr( $option, 0, -1 );
		} else {
			$arg = self::ARG_NONE;
		}
		$sp = explode( '|', $option );
		if ( strlen( $sp[0] ) == 1 ) {
			$opt = static::$shortOptions[] = $sp[0];
		} else {
			$opt = static::$longOptions[] = $sp[0];
		}
		if ( isset( $sp[1] ) ) {
			static::$longAliases[$sp[1]] = $sp[0];
		}
		static::$optionArgs[$opt] = $arg;
		return $opt;
	}

	/**
	 * Returns wether an option has been supplied, with an optional default boolean value.
	 *
	 * @param string $opt
	 * @param boolean $default. (false by default)
	 */
	protected function opt( $opt, $default=false ) {
		$sup = in_array( $opt, $this->supplied );
		if ( !$sup ) {
			return $default;
		} else {
			return $sup;
		}
	}

	/**
	 * Gets the supplied argument to an option, with an optional default value.
	 *
	 * @param string $opt
	 * @param string $default
	 */
	protected function optArg( $opt, $default=null ) {
		if ( isset( $this->suppliedOptionArgs[$opt] ) ) {
			return $this->suppliedOptionArgs[$opt];
		} else {
			return $default;
		}
	}

	/**
	 * Gets an argument value, with an optional default value.
	 *
	 * @param string $arg
	 * @param string $default
	 */
	protected function arg( $arg, $default=null ) {
		if ( isset( $this->suppliedArguments[$arg] ) ) {
			return $this->suppliedArguments[$arg];
		} else {
			return $default;
		}
	}

	protected function progress( $index ) {
		$this->cprintf( " * Processing... <yellow>%s</yellow> [<magenta>%d</magenta>]\r", $this->bars[$index % 4], $index );
	}

	/**
	 * Prints an error message
	 *
	 * @param string $message
	 */
	protected static function printError( $message ) {
		self::cprintf( "<red>Error:</red> <white>%s</white>\n\n", $message );
	}

	/**
	 * Prints usage string
	 */
	protected static function printUsage() {
		self::cprintf( "<white>Usage:</white> %s [OPTIONS]", self::$script );
		foreach( static::$arguments as $arg ) {
			if ( static::$argumentsRequired[$arg] == self::ARG_REQUIRED ) {
				printf( " <%s>", $arg );
			} else {
				printf( " [%s]", $arg );
			}
		}
		print "\n\n";

		self::cprintf( "<white>Options:</white>\n" );

		$maxlen = 0;
		$longOptions = array_merge( array_keys( static::$longAliases ), static::$longOptions );
		foreach( $longOptions as $long ) {
			if ( strlen( $long ) > $maxlen ) $maxlen = strlen( $long ); 
		}
		$maxlen+=10;
		foreach( static::$shortOptions as $opt ) {
			echo "\t-$opt";
			if ( static::$optionArgs[$opt] == self::ARG_OPTIONAL ) {
				$arg = ' [VALUE]';
			} elseif ( static::$optionArgs[$opt] == self::ARG_REQUIRED ) {
				$arg = ' <VALUE>';
			} else {
				$arg = '';
			}

			if ( ( $long = array_search( $opt, static::$longAliases ) ) ) {
				printf( " --%-{$maxlen}s%s\n", $long.$arg, static::$help[$opt] );
			} else {
				printf( "%-".($maxlen+3)."s%s\n", "".$arg, static::$help[$opt] );
			}
		}
		foreach( static::$longOptions as $opt ) {
			printf( "\t--%-".($maxlen+3)."s%s\n", $opt, static::$help[$opt] );
		}
		echo "\n";
	}

	public function __toString() {
		$time = microtime(true) - $this->startTime;
		$str = $this->csprintf( "- App  : <white>%s</white>\n", get_called_class() );
		$str.= $this->csprintf( "- Time : <white>%1.5f</white>\n", $time );
		return $str;
	}

}
