<?php


namespace mvc\views;

/**
 * The ViewLoader interface defines a means to load a view, from paths, databases, or any other data store system.
 * A View can have multiple ViewLoaders, that will be called in order, until view data can be successfully retrieved.
 */
interface ViewLoader extends ViewHelper {

	/**
	 * Loads the data for the supplied View, returning true if OK, or throwing a ViewException when something fails.
	 *
	 * @param ViewRenderer $renderer
	 * @param View $view
	 * @return boolean
	 */
	public function load( ViewRenderer $renderer, View $view );

	/**
	 * Saves the data for the supplied View and $params
	 *
	 * @param View $view
	 * @param array $params
	 */
	public function save( View $view, array $params=array() );

}
