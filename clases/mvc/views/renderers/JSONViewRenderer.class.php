<?php

namespace mvc\views\renderers;

use mvc\Model;
use mvc\views\View;
use mvc\views\ViewRenderer;

/**
 * Renders a JSON view .
 */
class JSONViewRenderer extends ViewRenderer {


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
	 * Renders the Provided Model
	 *
	 * @param Model $model
	 * @param View $view
	 */
	public function render( Model $model, View $view ) {
//		header( 'content-type: text/plain; charset=UTF-8' );
		echo json_encode($this->getJSON($model));
	}

	private function getJSON( Model $model ){
		$json = array();
		var_dump($model);
		foreach( $model as $node )
		{
			$json[$node->getName()] = $this->renderNode( $node );
		}
		return $json ;
	}




	private function renderNode( Model $node ) {
		$json = array();
		var_dump($node);
		foreach( $node->attributes() as $attr ) {
			$content = (string)$attr;
			$json[$attr->getName()] = is_numeric( $content ) ? (int)$content : $content;
		}
		$content = (string)$node;
		if ( $content ) {
			$json['__content'] = is_numeric( $content ) ? (int)$content : $content;
		}

		if ( sizeof( $node->children() ) ) {
			$children = array();
			foreach( $node->children() as $c ) {
				if ( !isset( $children[$c->getName()] ) ) {
					$children[$c->getName()] = array();
				}
				$children[$c->getName()][] = $this->renderNode( $c );
				foreach( $children as $name => $array ) {
					$json[$name] = sizeof( $array ) > 1 ? $array : $array[0];
				}
			}
		}
	}

	public function Exception( \Exception $exception ) {
		return $exception->getMessage();
	}

}
