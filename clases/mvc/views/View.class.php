<?php

namespace mvc\views;

use core\Object;
use util\Date;

/**
 * Represents a unique View. A View has the following properties:
 *
 * - module: The view module
 * - name: The view name
 * - data: The view data
 * - dataURI: If appliable
 */
class View extends Object {

	/**
	 * View Module
	 */
	protected $module = null;
	/**
	 * View Name
	 */
	protected $name = null;
	/**
	 * The View logical data, if any.
	 */
	protected $data = null;
	/**
	 * The View raw data, if any
	 */
	protected $rawData = null;
	/**
	 * The View data uri, if any.
	 */
	protected $dataURI = null;
	/**
	 * View last modification time
	 */
	protected $mtime = null;

	/**
	 * Instantiates a new View Object for the given module and name.
	 *
	 * @return View
	 */
	public function __construct( $module, $name ) {
		$this->module = $module;
		$this->name = $name;
	}

	/**
	 * Gets the view module
	 *
	 * @return string
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * Gets the view name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets the view data
	 *
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Sets the view data, with optional uri
	 *
	 * @param mixed $data
	 * @param string $uri
	 */
	public function setData( $data, $uri=null ) {
		$this->data = $data;
		$this->dataURI = $uri;
		return $this;
	}

	/**
	 * Gets the view raw data
	 *
	 * @return string
	 */
	public function getRawData() {
		return $this->rawData;
	}

	/**
	 * Sets the view raw data.
	 *
	 * @param string $rawData
	 */
	public function setRawData( $rawData ) {
		$this->rawData = $rawData;
	}

	/**
	 * Sets the view last modification time
	 *
	 * @param Date $mtime
	 */
	public function setMTime( Date $mtime ) {
		$this->mtime = $mtime;
	}

	/**
	 * Gets the view last modification time
	 *
	 * @return Date
	 */
	public function getMTime() {
		return $this->mtime;
	}

}
