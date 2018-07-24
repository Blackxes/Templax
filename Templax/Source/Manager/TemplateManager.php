<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	manages template processes
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Manager;

require_once ( TEMPLAX_ROOT . "/Source/Models/Template.php" );

//_____________________________________________________________________________________________
class TemplateManager {

	private $templates;
	private $fileCache; // stores already streamed files

	public $lastRegistrationError;

	//_________________________________________________________________________________________
	//
	// param1 (array) expects initial templates
	//		key represent the template id and the value its assossiated template file
	//		when the template file contains multiple templates
	//		the value must be defined as key represent the template file and its value
	//		the marker that surrounds the requested template
	// 
	public function __construct( array $templates = array() ) {

		$this->templates = array();
		$this->fileCache = array();
		
		$this->registerTemplateSet( $templates );
	}

	//_________________________________________________________________________________________
	// registers a set of templates
	//
	// param1 (array) expects the template defined the following
	//		either a single template:
	//			array( template id => path/to/file );
	//
	//		or multiple templates in one file
	//			array(
	//				template id => array(
	//					"path" => path/to/file,
	//					"marker" => ###SURROUNDING_MARKER###
	//					"options" => array( "option" => value )
	//				)
	//			)
	//
	// content of files are being cached in order to avoid unecessary file streaming
	//
	public function registerTemplateSet( array $templates ) {

		if ( !$templates )
			return false;

		foreach( $templates as $id => $config ) {

			// default set / p => params
			if ( !is_array($config) ) {
				$this->register( $id, is_string( $config ) ? $config : "" );
				continue;
			}

			// reassign the params list with given values when provided
			// otherwise use default
			$pList = array( "markup" => array(), "options" => array(), "marker" => "", "path" => "" );
			foreach( $pList as $item => $default )
				$pList[$item] = isset($config[$item]) ? $config[$item] : $default;

			$this->register( $id, $pList["path"], $pList["marker"], $pList["markup"], $pList["options"] );
		}

		// print error logs
		foreach( \Templax\Templax::$logfile->getOpenLogs() as $index => $log )
			print_r( "\n" . $log->getMessage() );

		return true;
	}

	//_________________________________________________________________________________________
	// registers a template instance
	//
	// param1 ( \Templax\Source\Models\Template ) expects the template instance
	//
	// return boolean
	//
	public function registerTemplateInstance( \Templax\Source\Models\Template $template ) {

		if ( $template == null || !$template->getId() || $this->has($template->getId()) )
			return false;
		
		$this->templates[$template->getId()] = $template;

		return true;
	}

	//_________________________________________________________________________________________
	// registers a template
	//
	// param1 (string) expects the template id
	// param2 (string|array) expects the template configuration
	//		when string the value represents the file path
	//			path/to/file
	//		when array it must include the following indecies
	//			"path" => "path/to/template.html"
	//			"marker" => ###MARKER_THAT_SURROUNDS_THE_TEMPLATE###
	// param3 (array) expects the default markup
	// param4 (array) expects the default options
	//
	// return boolean
	//
	public function register( $id, $filePath, $marker = "", array $markup = array(), array $options = array()) {

		if ( !$id || !$filePath || $this->has($id) ) {
			$this->lastRegistrationError = "invalid values for template (id/config)";
			return false;
		}

		if ( $this->has($id) ) {
			$this->lastRegistrationError = "template {$id} already registered";
			return false;
		}

		// using key and end avoids errors when trying to access array indecies
		// this only works because the array contains only 2 values
		$templateString = ( $marker )
			? $this->extractTemplate( $filePath, $marker )
			: $this->getFileTemplate( $filePath );
		
		$template = new \Templax\Source\Models\Template( $id, $templateString, $options );
		$this->templates[$id] = $template;
		
		return true;
	}

	//_________________________________________________________________________________________
	// returns the inner content of a file
	// this functions removes all line breaks and replaces them with an empty string
	// this is why this function is not called eg. "getFileContent"
	// 
	// because files may include multiple files streaming the same file over and over again
	// would slow the performance down. To reduce space line breaks and unecessary spaces
	// will be removed when caching the content of a file
	//
	// param1 (string) expects the file path including the filename and extension
	// param2 (boolean) defines wether the file content shall be cached or not
	//
	// return string
	//
	public function getFileTemplate( $file, $cache = true ) {

		if ( is_string($file) && $this->fileCache[$file] )
			return $this->fileCache[$file];
		
		else if ( !$file || !file_exists($file) )
			return \Templax\Templax::$log->logReturn( "file {$file} doesnt exist", "" );
		
		$fileContent = preg_replace("/\r\n/", "", preg_replace("/\s{2,}/", "", file_get_contents($file)) );

		if ($cache)
			$this->fileCache[$file] = $fileContent;
		
		return $fileContent;
	}

	//_________________________________________________________________________________________
	// returns the template surrounded by the given marker
	//
	// param1 (string) expects the template string
	// param2 (string) expects the marker
	//
	// return string
	//
	private function extractTemplate( $file, $marker ) {
		
		if ( !$file || !$marker )
			return "";
		
		$prepMarker = "###" . strtoupper($marker) . "###";
		$regex = "/" . $prepMarker . "(.*)" . $prepMarker . "/";
		
		preg_match(
			"/" . $prepMarker . "(.*)" . $prepMarker . "/",
			$this->getFileTemplate($file),
			$template
		);
		
		return $template ? $template[1] : "";
	}

	//_________________________________________________________________________________________
	// returns the existance of a template as boolean
	//
	// param1 (string) expects the template id
	//
	// return boolean
	//
	public function has( $id ) {
		return (is_string($id)) ? isset($this->templates[$id]) : false;
	}

	//_________________________________________________________________________________________
	// returns a template
	//
	// param1 (string) expects the template id
	//
	// return \Templax\Models\Template
	//
	public function get( $id, $all = false ) {
		return ( (!$all) ? $this->templates[$id] : $this->templates );
	}
}

//_____________________________________________________________________________________________
//