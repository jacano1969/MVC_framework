<?php

namespace mvc\views\compilers;

use core\Object;
use mvc\views\View;
use mvc\views\ViewCompiler;
use mvc\views\ViewLoader;
use mvc\views\ViewRenderer;
use mvc\views\ViewException;

class XSLViewCompiler extends Object implements ViewCompiler {

	/**
	 * Compiles an xsl view, replacing, if necessary, MVC framework short links with full routed links.
	 *
	 * @param ViewRenderer $renderer
	 * @param View $view
	 */
	public function compile( ViewRenderer $renderer, ViewLoader $loader, View $view ) {
		$xml = $view->getData();
		$xpath = new \DOMXpath( $xml );

		// Compile imported stylesheets and include its templates
		$nodes = $xpath->query( '//xsl:import' );
		foreach( $nodes as $node ) {
			$href = $node->getAttribute( 'href' );
			$sp = explode( '/', $href );
			$impViewFile = array_pop( $sp );
			$module = $view->getModule() ? $view->getModule() . join( '/', $sp ) . '/' : '';
			$impViewName = str_replace( '.xsl', '', $impViewFile );
			$viewClass = get_class( $view );
			$v = $renderer->load( $module, $impViewName );
		}

		if ( !isset( $_REQUEST['rewrite'] ) || $_REQUEST['rewrite'] != 'on' ) {

			// Check Attributes
			$attrs = $xpath->query( "//@*" );
			foreach( $attrs as $a ) {
				if ( strstr( $a->value, '/app/' ) && substr( $a->ownerElement->nodeName, 0, 3 ) != 'xsl' ) {
					$v = preg_replace( '/\/app\/(.*)/', '/app.php?request=$1', $a->value );
					$a->value = $v;
				}
			}
		}
	}

	public function setProperty( $name, $value ) {
		throw new ViewException( sprintf( '%s does not accept properties', get_class() ) );
	}

	public function clearProperties() {
	}

	public function checkProperties() {
	}

}
