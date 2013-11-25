<?php

namespace mvc\views\renderers;

use mvc\Model;
use mvc\views\View;
use mvc\views\ViewException;

/**
 * Renders an HTML View. It extends XSLViewRenderer class implementing its render method
 */
class HTMLViewRenderer extends XSLViewRenderer {

	/**
	 * Renders the Provided Model as an HTML document
	 *
	 * @param Model $model
	 * @param View $view
	 */
	public function render( Model $model, View $view ) {
		libxml_use_internal_errors( false );
		$processor = new \XSLTProcessor();
		$res = @$processor->importStylesheet( $view->getData() );
		if ( !$res ) {
			$err = error_get_last();
			throw new ViewException( sprintf( 'Error loading view: "%s"', $err['message'] ) );
		}

		$doc = $processor->transformToDoc( $model->getDOM() );

	//	ob_start("ob_gzhandler");
		header( "content-type: text/html; charset=utf-8" );
		print( $doc->saveHTML() );
		return;
	}
}
