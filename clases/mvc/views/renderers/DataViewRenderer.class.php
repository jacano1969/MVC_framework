<?php

namespace mvc\views\renderers;

use mvc\Model;
use mvc\views\View;
use mvc\views\ViewRenderer;

/**
 * Generic Data View Renderer. This renderer returns the data node of the model as raw data, with the mime type as set in the "mime=" attribute of the data node.
 * Extra information in the data node serves to set headers for download the data as an attachment with filename.
 * Supported attributes:
 * - filename
 * - cache
 *
 * Example: (Download a text file with name "report.txt")
 * <data filename="report.txt" type="text/plain">
 * 		TEXT DATA HERE
 * </data>
 */
class DataViewRenderer extends ViewRenderer {

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
		echo"<pre>";
		var_dump($model);
		echo"</pre>";
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