<?php

namespace mvc\views\renderers;

use mvc\Model;
use mvc\views\View;
use mvc\views\ViewRenderer;

/**
 * Renders an XML view
 */
class XMLViewRenderer extends ViewRenderer {

	/**
	 * This View Renderer doesn't have any real view data.
	 *
	 * @param string $data
	 * @param string $uri
	 * @return string
	 */
	public function getViewData( $data, $uri=null ) {
		return $data;
	}

	/**
	 * Renders the provided Model as an XML document.
	 *
	 * @param Controller $controller
	 * @param Model $model
	 * @param View $view
	 */
	public function render( Model $model, View $view ) {
		header( "Content-Type: text/xml; charset=UTF-8");
		print $model->getXML(true);
	}

	/**
	 * Renders the provided Exception and return the result
	 *
	 * @param \Exception $exception
	 * @return string
	 */
	public function Exception( \Exception $exception ) {
		return $exception->getMessage();
	}

}
