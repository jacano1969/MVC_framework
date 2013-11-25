<?php
/**
 * Created by PhpStorm.
 * User: jacano
 * Date: 24/10/13
 * Time: 13:22
 *
 *
 * Este fichero es parte de un FrameWork MVC para PHP
 *
 *
 * @version $Id$
 * @copyright Jose Antonio Cano Gregorio <josea.cano.iw@impresionesweb.com>
 *
 */

use core\Object;
use io\File;
use core\CoreException;
use sql\ConnectionFactory;
use util\LoggerFactory;

// Default Classpath
$_CLASSPATH = array();
APP_PATH  !== null ? $_CLASSPATH[] = sprintf( '%s/clases', APP_PATH ) : null;
APPROOT !== null ? $_CLASSPATH[] = sprintf( '%s/clases', APPROOT ) : null;

require( APP_PATH . '\clases\util\FirePHPCore\FirePHP.class.php' );

spl_autoload_register('autoload_mvc');

$firephp = FirePHP::getInstance(true);

	/**
 * Se añade una ruta a la pila de rutas del CLASSPATH.
 *
 * @param string $path
 */
function add_classpath( $path ) {
    global $_CLASSPATH;
    $_CLASSPATH[] = $path;
}

//Hack para mínima implementacion namespace y use en nuestro framework para trabajar  php 5.1 and 5.2
function use_mvc($class){
	require( APP_PATH ."/clases/$class.class.php" );
}

function namespace_mvc($path){
	$CLASSPATH[]=$CLASSPATH[0]."/".$path;
}



/**
 * Se obtiene la pila de CLASSPATH actual
 *
 * @return array
 */
function get_classpath() {
    global $_CLASSPATH;
    return $_CLASSPATH;
}

/**
 * Funcion para dumpear -> variables/objects/arrays
 *
 * @param mixed $var
 */
function pprint( $var ) {
    if ( !defined( 'STDIN' ) ) echo '<pre>';
    echo psprint($var);
    if ( !defined( 'STDIN' ) ) echo '</pre>';
}

/**
 * funcion para convertir a string -> variables/objects/arrays
 *
 * @param mixed $var
 */
function psprint($var) {
    $str = "";
    switch( gettype( $var ) ) {
        case 'array':
            ob_start();
            print_r( $var );
            $str = ob_get_contents();
            ob_end_clean();
            break;
        case 'object':
            if ( is_subclass_of( $var, 'core\Object' ) ) {
                $str = $var->__toString();
            } else {
                $var=get_object_vars($var);
                ob_start();
                print_r( $var );
                $str = ob_get_contents();
                ob_end_clean();
                //$str = sprintf( "Objecto ID#%s - No __toString() metodo.", spl_object_hash( $var ) );
            }
            break;
        default:
            $str = strval($var);
    }
    return $str;
}

function oprint($ob) {

    echo "<pre>";
    var_dump($ob);
    echo "</pre>";

}


function _plog_msg($argsArray) {
    $str = "";
    $app = "";
    foreach( $argsArray as $a ) {
        $str .= $app . psprint($a);
        $app = " ";
    }
    return $str;
}

/**
 * funcion para error logging
 */
function plog_error() {
    $msg = _plog_msg(func_get_args());
	$firephp->fb($msg,FirePHP::ERROR);
    LoggerFactory::getDefault()->logError( $msg );
}

/**
 * funcion para  logear una excepcion
 */
function plog_exception() {
    $msg = _plog_msg(func_get_args());
	$firephp->fb($msg,FirePHP::ERROR);
	LoggerFactory::getDefault()->logException( $msg );
}

/**
 * funcion para logear informacion
 */
function plog_info() {
    $msg = _plog_msg(func_get_args());
	$firephp->fb($msg,FirePHP::INFO);
	LoggerFactory::getDefault()->logInfo( $msg );
}

/**
 * funcion para logear informacion de debug
 */
function plog_debug() {
    $msg = _plog_msg(func_get_args());
	$firephp->fb($msg,FirePHP::LOG);
	LoggerFactory::getDefault()->logDebug( $msg );
}

/**
 * funcion para logear warnings.
 */
function plog_warning() {
    $msg = _plog_msg(func_get_args());
	$firephp->fb($msg,FirePHP::WARM);
	LoggerFactory::getDefault()->logWarning( $msg );
}


function ptrace( $message ) {
	$firephp->fb($message . "\n" . \core\ErrorHandler::parseBacktrace(debug_backtrace(),1),FirePHP::TRACE);
    \pprint("Trace: $message\n" . \core\ErrorHandler::parseBacktrace(debug_backtrace(),1));
}

function cObj($object,$idx=0){
	$arr=(array)$object;
	return $arr[$idx];
}

/**
 * Sistema de autocarga de classes
 * @param string $class
 */
function autoload_mvc( $class ) {
    global $_CLASSPATH;
	$file = lookup_class( $class );
    if ( $file )  {
        require( $file );
        try {
            $m = new ReflectionMethod( $class, '__static' );
            if ( $m->class == $class || '\\'.$m->class == $class ) {
                $class::__static();
            }
        } catch ( ReflectionException $e ) {
            // Do nothing, this is thrown when method __static doesn't exist
        } catch ( Exception $e ) {
            pprint($e );
            trigger_error( sprintf( 'No se puedo cargar la clase "%s". (Classpath: "%s"): %s', $class, join( '", "', $_CLASSPATH ), $e->getMessage() ), E_USER_ERROR );
	        return false;
        }
    } elseif( defined( 'DOMPDF_INC_DIR') && file_exists(  DOMPDF_INC_DIR . "/" . mb_strtolower($class) . ".cls.php" ) ){
        require_once( DOMPDF_INC_DIR . "/" . mb_strtolower($class) . ".cls.php" );
    } else {
	    trigger_error( sprintf( 'Could not load class "%s". (Classpath: "%s")', $class, join( '", "', $_CLASSPATH ) ), E_USER_ERROR );
    }
	return true;
}

/**
 * Loads all classes in the supplied system path name
 *
 * @param string $path
 */
function load_path_classes( $path ) {
    global $_CLASSPATH;
    if ( !is_dir( $path ) ) {
        throw new CoreException( sprintf( 'No se puedo cargar la clase. La ruta no es correcta: "%s"', $path ) );
    }
    $cpath = null;
    foreach( $_CLASSPATH as $cp ) {
        if ( substr( $path, 0, strlen( $cp ) ) == $cp ) {
            $cpath = $cp;
            break;
        }
    }
    if ( $cpath === null ) {
        throw new CoreException( sprintf( 'No se puedo cargar la clase. La ruta "%s" esta en el Classpath (%s)', join( ', ', $_CLASSPATH ) ) );
    }

    $classes = array();
    $dir = new \DirectoryIterator( $path );
    foreach( $dir as $f ) {
        if ( preg_match( '/^([a-zA-Z0-9]*)\.class\.php$/', $f->getFilename(), $matches ) ) {
            $nsClass = str_replace( '/', '\\', substr( $f->getPathname(), strlen( $cpath ), -10 ) );
            require_once( $f->getPathname() );
            $classes[] = $nsClass;
        }
    }
    return $classes;
}

/**
 * Nos retorna el fichero con la ruta de una classe
 * en caso de encontrarse dentro de las rutas del Classpaths.
 *
 * @param string $class
 * @return string Returns Class File or null if not found.
 */
function lookup_class( $class ) {
    global $_CLASSPATH;
    foreach( $_CLASSPATH as $path ) {
        $file = sprintf( '%s/%s.class.php', $path, str_replace( '\\', '/', $class ) );
        if ( file_exists( $file ) ) {
            return $file;
        }
    }
    return null;
}

/**
 *
 * Nos pasa la ruta de acceso a una clase siempre que este dentro de las rutas
 * que se pasan como primer parametro.
 *
 *
 * @param array $arrns Array de namespace donde buscar la clase
 * @param string $class nombre de la clase
 * @param string $ns directorio adicional que se añade a los elementos de namespace
 */
function get_ns_class( array $arrns, $class, $ns=null ) {
    foreach( $arrns as $basens ) {
        $nsClass = sprintf( '%s%s\%s', $basens, $ns ? '\\'.$ns : '', $class );
        if ( class_exists( $nsClass, false ) || lookup_class( $nsClass ) ) {
            return $nsClass;
        }
    }
    return null;
}

/**
 * Nos retorna el namespace del objeto/classe parado por parametro
 *
 * @param Object $obj
 * @return string
 */
function get_namespace( $obj ) {
    if ( is_object( $obj ) ) {
        $class = get_class( $obj );
    } elseif ( class_exists( $obj, false ) ) {
        $class = $obj;
    }
    $idx = strrpos( $class, '\\' );
    $ns = substr( $class, 0, $idx );
    return ( $ns ? $ns : '' );
}

/**
 * Retorna el tipo mime de un fichero en funcion de la extension
 *
 * @param File $file
 * @return string
 */
function get_resource_mime_type( File $file ) {
    switch( $file->getExtension() ) {
        case 'js': return 'text/javascript';
        case 'css': return 'text/css';
        case 'jpg':
        case 'jpeg': return 'image/jpeg';
        case 'png': return 'image/png';
        case 'gif': return 'gif';
        case 'html': return 'text/html';
        default:
            return $file->getMimeType();
    }
}


function registerAutoloader($callback, $append=false)
{
	if($append)
	{
		spl_autoload_register($callback);
	}
	else
	{
		spl_autoload_unregister('autoload_mvc');
		spl_autoload_register($callback);
		spl_autoload_register('autoload_mvc');
	}
}



	// Start performance Measurement
define( 'TIME_START', microtime(true) );

// Strict coding!
error_reporting( E_ALL | E_STRICT );

// Set default exception handler
set_exception_handler( array( '\core\ErrorHandler', 'handleException' ) );

// Set default error handler
set_error_handler( array( '\core\ErrorHandler', 'handleError' ), E_ALL | E_STRICT );


?>