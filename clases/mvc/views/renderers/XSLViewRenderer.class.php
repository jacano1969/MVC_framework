<?php

namespace mvc\views\renderers;

use mvc\Model;
use mvc\views\View;
use mvc\views\ViewRenderer;
use mvc\views\ViewException;

/**
 * Renders an XSLView. Outputs the raw xsl data. For views that apply xsl views to model data, extend this class and override the render method.
 */
class XSLViewRenderer extends ViewRenderer {

	/**
	 * XSLView requires at least one loader
	 */
	protected $requireLoaders = true;

	/**
	 * XSLView supports compilers
	 */
	protected $supportCompilers = true;

	/**
	 * Returns the view data as a DomDocument, with the supplied data uri
	 *
	 * @param string $data
	 * @param string $uri=null
	 */
	public function getViewData( $data, $uri=null ) {
		libxml_use_internal_errors( true );

		$dom = new \DomDocument('1.0', 'UTF-8');
		$dom->loadXML( $data );
		$dom->documentURI = $uri;

		$errors = libxml_get_errors();
		if ( sizeof( $errors ) > 0 ) {
			$msg = array();
			foreach( $errors as $e ) {
				$msg[] = sprintf( 'Line %d: %s', $e->line, $e->message );
			}
			throw new ViewException( sprintf( "Errors in XSL View:\n%s", join( "", $msg ) ) );
		}

		return $dom;
	}

	/**
	 * Renders the Provided Model
	 *
	 * @param Model $model
	 * @param View $view
	 */
	public function render( Model $model, View $view ) {
		header( 'content-type: text/xml' );
		$xsl = $view->getData();
		print( $xsl->saveXML() );
	}

	/**
	 * Renders the provided exception
	 *
	 * @param \Exception $exception
	 */
	public function Exception( \Exception $exception ) {
		return $exception->getMessage();
	}
}
