<?php

namespace mvc;

use io\File;

/**
 * 
 * Represents a FOP view file in the MVC framework MVC Model. It extends XSLView class by implementing its render method
 */
class FOPViewRenderer extends ViewRenderer {

	/**
	 * FOP Extension
	 */
	protected static $ext = 'fop.xsl';

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
	 * Renders the Provided Model, according to the given mode
	 *
	 * @param Model $model
	 */
	public function render( Model $model, $mode=self::MODE_SERVER ) {
		$tmpPdfFile = $this->getTempPdfFile();
		$tmpXmlFile = $this->getTempXmlFile( $model );
		$xslFile = $this->getFile();
		$this->outputHeaders($tmpPdfFile);

		$this->outputFop( $tmpXmlFile, self::$basePath . $this->getFile(), $tmpPdfFile );
		return;
	}

	private function outputHeaders( $fileName ) {
		header("Content-Type: application/pdf" );
		header("Content-Disposition: attachment; filename=\"$fileName.pdf\"");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}

	private function getTempPdfFile() {
		return tempnam('/tmp','fop');
	}

	private function getTempXmlFile( Model $model ) {
		$tmpXmlFile = tempnam('/tmp','fopxml');
		$xslFile = $this->getFile();

		$fpXml = fopen($tmpXmlFile,'wb');
		fwrite($fpXml,$model->asXML());
		fclose($fpXml);

		return $tmpXmlFile;
	}

	private function outputFop( $xmlFile, $xslFile, $pdfFile ) {
		system("fop -xml '$xmlFile' -xsl '$xslFile' -pdf '$pdfFile' &>/dev/null");
		$fp = fopen($pdfFile,'rb');
		fpassthru($fp);
		fclose($fp);
		unlink($pdfFile);
		unlink($xmlFile);
	}

}
