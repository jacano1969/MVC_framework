<?php

namespace mvc\views\renderers;

use mvc\Model;
use mvc\views\View;
use mvc\views\ViewRenderer;

/**
 * Renders an Image.
 */
class ImageViewRenderer extends ViewRenderer {

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
	 * Renders the provided Model as an XML File.
	 *
	 * @param Model $model
	 * @param View $view
	 */
	public function render( Model $model, View $view ) {
		header( "Content-Length: ". strlen($model->getData()), true);
		header( "Content-Type: ".$model->getDataType(), true );
		$this->setHeadersfromModel($model);
		echo $model->getData();
		//XXX
		//This exit method removes ugly segmentation faults
		exit;
	}

	public function Exception( \Exception $exception ) {
	}

}
