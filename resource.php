<?php

use io\File;

// APP_PATH y APPROOT Constantes
if ( !defined( 'APP_PATH' ) ) {
	define( 'APP_PATH', dirname( __FILE__ )  );
}
//define( 'APPROOT', APP_PATH);
define( 'APPROOT', isset( $_SERVER['DOCUMENT_ROOT'] ) ? $_SERVER['DOCUMENT_ROOT'] : null );


require( 'bootstrap.php' );

if ( isset( $_REQUEST['resource'] ) ) {
	foreach( array( APPROOT, APP_PATH) as $path ) {
		$file = new File( sprintf( '%s/%s', $path, $_REQUEST['resource'] ) );
		if ( $file->exists() ) {
			header( sprintf( 'Content-Type: %s', get_resource_mime_type( $file ) ) );
			echo $file->read();
			exit;
		}
	}
}
