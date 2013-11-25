<?php

namespace mvc\views\compilers;

use core\Object;
use mvc\views\View;
use mvc\views\ViewCompiler;
use mvc\views\ViewLoader;
use mvc\views\ViewRenderer;
use mvc\views\ViewException;

class I18NXSLViewCompiler extends Object implements ViewCompiler {

	/**
	 * Translators to use
	 */
	protected $translators = array();

	/*
	 * Show missing setting
	 */
	protected $showMissing = 0;

	/**
	 * Compiles the provided view for the provided ViewRenderer
	 *
	 * @param ViewRenderer $renderer
	 * @param View $view
	 */
	public function compile( ViewRenderer $renderer, ViewLoader $loader, View $view ) {
		$xml = $view->getData();

		// Set up XPath
		$xpath = new \DOMXpath( $xml );
		$xpath->registerNamespace( 'i18n', 'http://apache.org/cocoon/i18n/2.1' );

		// Load Translations for the domain.
		$domains = array();
		$nodes = $xpath->query( '//i18n:domain' );
		if ( $nodes->length == 0 ) {
			$domains[] = $view->getName();
		} else {
			foreach( $nodes as $node ) {
				$domains[] = (String)$node->nodeValue;
			}
		}
		$this->loadDomains( $domains );

		// Compile imported stylesheets and include its templates
		$nodes = $xpath->query( '//xsl:import' );
		foreach( $nodes as $node ) {
			$href = $node->getAttribute( 'href' );
			$sp = explode( '/', $href );
			$impViewFile = array_pop( $sp );
			$module = $view->getModule() . join( '/', $sp ) . '/';
			$impViewName = str_replace( '.xsl', '', $impViewFile );
			$viewClass = get_class( $view );
			$v = $renderer->load( $module, $impViewName );
		}

		// Replace i18n:text elements
		$nodes = $xpath->query( '//i18n:text' );
		foreach( $nodes as $node ) {
			$txt = $this->translate( (string)$node->nodeValue );
			$txt = $this->applyMods( $txt, (string)$node->getAttribute('mods') );
			$node->parentNode->insertBefore( $xml->createTextNode( $txt ), $node );
			$node->parentNode->removeChild( $node );
		}

		// Replace i18n:attr elements
		$nodes = $xpath->query( '//@i18n:attr' );
		foreach( $nodes as $node ) {
			$attrs = explode( ' ', $node->value );
			foreach( $attrs as $attr ) {
				$txt = $this->translate( $node->parentNode->getAttribute( $attr ) );
				$node->parentNode->setAttribute( $attr, $txt );
			}
			$node->parentNode->removeAttribute( 'i18n:attr' );
		}

		// Replace i18n:translate elements. This is slightly trickier...
		$nodes = $xpath->query( '//i18n:translate' );
		foreach( $nodes as $node ) {
			// Get the translated text.
			$txt = $this->translate( trim( (string)$node->nodeValue ) );
			// Split sections. We need to process each one as an individual node. Split both index and named parameters.
			//$sections = preg_split( '/({.*}|%\([0-9]*\)s)/U', $txt, -1, PREG_SPLIT_DELIM_CAPTURE );
			$trans = $txt;
			$sections = preg_split( '/({.*}|%\([0-9]*\)s)/U', $trans, -1, PREG_SPLIT_DELIM_CAPTURE );
			foreach( $sections as $s ) {
				// Index Param
				if ( preg_match( '/%\(([0-9]*)\)s/', $s, $matches ) ) {
					// Get i18n:param value
					$p = $xpath->query( sprintf( "i18n:param[@index='%d']", $matches[1] ), $node );
					if ( $p->length > 0 ) {
						// Create xsl node and append actual value
						$n = $xml->createElement( 'xsl:value-of' );
						$n->setAttribute( 'select', $p->item(0)->getAttribute('value') );
					} else {
						// Param not set. Create debug text node.
						$n = $xml->createTextNode( $matches[0] );
					}
				// Named Param
				} elseif ( preg_match( '/^{(.*)}$/', $s, $matches ) ) {
					// Get i18n:param value
					$p = $xpath->query( sprintf( "i18n:param[@name='%s']", $matches[1] ), $node );
					if ( $p->length > 0 ) {
						// Create xsl node and append actual value.
						$n = $xml->createElement( 'xsl:value-of' );
						$n->setAttribute( 'select', $p->item(0)->getAttribute('value') );
					} else {
						// Param not set. Create debug text node.
						$n = $xml->createTextNode( $matches[0] );
					}
				} else {
					// Simple text section. Just create node.
					$s = $this->applyMods( $s, (string)$node->getAttribute('mods') );
					$n = $xml->createTextNode( $s );
				}
				// Append section as a node
				$node->parentNode->insertBefore( $n, $node );
			}
			// Finally, remove the whole <i18n:translate> structure
			$node->parentNode->removeChild( $node );
		}

		$view->setRawData( $xml->saveXML() );
	}

	/**
	 * Apply 'mod' arguments on the supplied translated string
	 *
	 * @param string txt The stringbeing translated
	 * @param string $mods The mods being applied.
	 * @return string
	 */
	private function applyMods( $txt, $mods ) {
		if ( $mods ) foreach( explode( ' ', $mods ) as $mod ) {
			switch( $mod ) {
				case 'uppercase':
					$txt = strtoupper( $txt );
					break;
				default:
					throw new I18NException( sprintf( 'Error Compiling view. Unknown text modifier: "%s"', $mod ) );
			}
		}
		return $txt;
	}

	/** 
	 * Sets a property. An I18NXSLViewCompiler supports the following properties:
	 *
	 * - translator: A class implementing php\i18n\Translator. More than one translator can be set, and they'll be called in succession until a successfully translated string is returned.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setProperty( $name, $value ) {
		switch( $name ) {
			case 'translator':
				$this->addTranslator( $value );
				break;
			case 'show-missing':
				$this->setShowMissing( $value );
				break;
			default:
				throw new ViewException( sprintf( 'Unsupported property for %s: "%s"', get_called_class(), $name ) );
		}
	}

	/**
	 * Clears all registered translators
	 */
	public function clearProperties() {
		$this->translators = array();
	}

	/**
	 * Checks that at least one translator is defined.
	 *
	 * @throws ViewException
	 */
	public function checkProperties() {
		if ( sizeof( $this->translators ) == 0 ) {
			throw new ViewException( sprintf( '%s Configuration error: No translators defined', get_called_class() ) );
		}
	}

	/**
	 * Adds a Translator class
	 *
	 * @param string $translator
	 */
	public function addTranslator( $translator ) {
		if ( !\lookup_class( $translator ) ) {
			throw new ViewException( sprintf( 'Invalid Translator %s for %s. Class not found', $translator, get_called_class() ) );
		}
		$this->translators[] = $translator::getInstance();
	}

	/**
	 * Returns the translators
	 *
	 * @return array
	 */
	public function getTranslators() {
		return $this->translators;
	}

	/**
	 * Determines wheter to show missing translation keys or not
	 * @param mixed $showMissing
	 */
	public function setShowMissing( $showMissing ) {
		$this->showMissing = intval($showMissing);
	}

	/**
	 * Returns the showMissing setting
	 */
	public function getShowMissing() {
		return $this->showMissing;
	}

	/**
	 * Loads the supplied array of domains into all registered translators
	 *
	 * @param array $domains
	 */
	protected function loadDomains( array $domains ) {
		foreach( $this->translators as $t ) {
			foreach( $domains as $d ) {
				$t->load( $d );
			}
		}
	}

	/**
	 * Translates the supplied key. This method will try each of the registered translators in turn, and return the first successfully translated string.
	 */
	protected function translate( $key ) {
		foreach( $this->translators as $t ) {
			$value = $t->get( $key, null );
			if ( $value !== null && $value !== false ) return ($value);
		}
		if ($this->getShowMissing()) {
			return "[" . $key . "]";
		}
		return false;
	}

}
