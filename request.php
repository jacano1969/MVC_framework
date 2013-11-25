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
 * MVC  Application Router
 *
 * El fichero .htaccess se encarga que las aplicaciones realizadas con este framework ejecuten este fichero.
 *
 * Este fichero carga todos los parametros de trabajo del fichero de configuracion app.json,
 * este fichero y el de configuracion estara ubicado en el raiz de la aplicacion
 * pero no estara accesible para el cliente (via .htaccess directive)
 */

	use util\Hashtable;
	use mvc\JSONAppConfig;
	use mvc\App;
	use mvc\RESTRequest;
	use mvc\AppContext;
	use mvc\AppException;
	use mvc\views\ViewException;


/**
 * Change this constant to wherever you have your placed your config file.
 * If you use a relative path, bear in mind it'll be relative to DOCUMENT_ROOT,
 * but it's VERY recommended you place your configuration file OUTSIDE the DOCUMENT_ROOT,
 * or at least forbid web server retrieval by an .htacess directive.
 */
define( 'APP_CONFIG', 'app.json' );

/**
 * NO NEED TO TOUCH ANYTHING BELOW THIS LINE
 */


// APP_PATH y APPROOT Constantes
if ( !defined( 'APP_PATH' ) ) {
    define( 'APP_PATH', dirname( __FILE__ )  );
}
//define( 'APPROOT', APP_PATH);
define( 'APPROOT', isset( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] : null );


require( APP_PATH . '/bootstrap.php' );


	/*
	mvc_use('util\Hashtable');
	mvc_use('mvc\JSONAppConfig');
	mvc_use('mvc\App');
	mvc_use('mvc\RESTRequest');
	mvc_use('mvc\AppContext');
	mvc_use('mvc\AppException');
	mvc_use('mvc\views\ViewException');
*/


$config =  new JSONAppConfig( APP_CONFIG ) ;

$context = new AppContext( $config);

if ( isset( $_REQUEST['request'] ) ) {
	$controler=($_REQUEST['request']=='')?'index':$_REQUEST['request'];
    $request = new RESTRequest( $context, $controler, new Hashtable( $controler) );
    $result = App::run( $request );

    $result->render();
} else {
    echo "No Request";
}
?>