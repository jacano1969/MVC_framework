<?php


namespace mvc\views;

/**
 * Classes implementing this interface can be registered as View Compilers in the View
 * and they'll be called in succession when View::compile() method is called.
 */
interface ViewCompiler extends ViewHelper {

	/**
	 * Compiles the provided View for the provided ViewRenderer
	 *
	 * The compilation should happen inside the view's represented object.
	 *
	 * @param ViewRenderer $renderer
	 * @param View $view
	 * @return void
	 */
	public function compile( ViewRenderer $renderer, ViewLoader $loader, View $view );

}
