<?php

namespace mvc\views\compilers;

use core\Object;
use mvc\views\View;
use mvc\views\ViewCompiler;
use mvc\views\ViewLoader;
use mvc\views\ViewRenderer;
use mvc\views\ViewException;

/**
 * This XSL Compiler combines all imported / included views on the import tree (starting with the deepest one, when importing stops)
 * and combining all templates in one single view (the top level, initially loaded view).
 *
 * The XSL standard is respected, with "shallower" templates take precedence over deeper ones with the same name/match clause.
 *
 * This compiler is useful if you want to store compiled views in one single place/structure (such as a database), and not worry
 * about import paths and uris for the XSLTProcessor 
 */
class XSLViewCombinerCompiler extends Object implements ViewCompiler {

	/**
	 * Compiles XSL Views recursively (navigatin xsl:import and xsl:include tags), generating one single xsl view with all imported templates included.
	 *
	 * @param ViewRenderer $renderer
	 * @param ViewLoader $loader
	 * @param View $view
	 */
	public function compile( ViewRenderer $renderer, ViewLoader $loader, View $view ) {
		$xml = $view->getData();
		$xpath = new \DOMXpath( $xml );

		$first = $xpath->query( '//xsl:template[1]' )->item(0);

		// Compile imported stylesheets and include its templates
		$nodes = $xpath->query( '//xsl:import' );
		foreach( $nodes as $node ) {
			$href = $node->getAttribute( 'href' );
			$sp = explode( '/', $href );
			$impViewFile = array_pop( $sp );
			$module = $view->getModule() ? $view->getModule() . join( '/', $sp ) . '/' : '';
			$impViewName = str_replace( '.' . $loader->getExtension(), '', $impViewFile );

			$viewClass = get_class( $view );
			$v = new $viewClass( $module, $impViewName );
			$loader->loadViewFromFile( $renderer, $v );
			$templates = $this->compile( $renderer, $loader, $v );

			foreach( $templates as $template ) {
				// Overwrite deeper template if we already have it
				if ( $template->hasAttribute( 'name' ) ) {
					$deeper = $xpath->query( sprintf( "//xsl:template[@name=\"%s\"]", $template->getAttribute( 'name' ) ) );
				} else {
					$deeper = $xpath->query( sprintf( "//xsl:template[@match=\"%s\"]", $template->getAttribute( 'match' ) ) );
				}

				$iTemplate = $xml->importNode( $template, true );

				if ( $deeper->length == 0 ) {
					if ( $first ) {
						$first->parentNode->insertBefore( $iTemplate, $first );
					} else {
						$xml->documentElement->appendChild( $iTemplate );
					}
				}
			}
			$node->parentNode->removeChild( $node );
		}

		$view->setRawData( $xml->saveXML() );

		return $xpath->query( '//xsl:template' );
	}

	public function setProperty( $name, $value ) {
		throw new ViewException( sprintf( '%s does not accept properties', get_class() ) );
	}

	public function clearProperties() {
	}

	public function checkProperties() {
	}

}
