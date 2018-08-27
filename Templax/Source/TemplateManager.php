<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * manages the templates within templax
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source;

use \Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Models/Template.php" );

//_____________________________________________________________________________________________
class TemplateManager {

	/**
	 * registered templates
	 * 
	 * @var array
	 */
	private $templates = array();

	/**
	 * stores the files content as $fileName => $content
	 * files can contain multiple templates and to avoid streaming the same file all over again
	 * this precious member stores their content
	 * 
	 * @var array
	 */
	private $cache = array();

	/**
	 * construction
	 * 
	 * @param array $templates - template that shall be registered initially
	 * 	supports the following 2 methods
	 * 	the one template file
	 * 		$templateid => $fileName
	 * 
	 * 	multiple templates in one file
	 * 		array(
	 * 			"template_id" => array(
	 * 				"file" => "path/To/File/Including/FileName/And.Extension"
	 * 				"marker" => ###MARKER_THAT_SURROUNDS_TEMPLATE###
	 * 			),
	 * 			etc.
	 * 		)
	 */
	public function __construct( array $templates = array() ) {
		
		// initialize templates
		if ( !empty($templates) )
			$this->registerTemplateSet($templates);
	}

	/**
	 * extracts the content from a file
	 * this function removes all line breaks and tabs.. etc. a one line string will be returned
	 * 
	 * @param string $file - the file path including the full name
	 * @param boolean $cache - describes wether this file should be cached
	 * 
	 * @return string
	 */
	public function getFileTemplate( string $file, bool $cache = true ) {

		// when in cache
		if ( isset($this->cache[$file]) )
			return $this->cache[$file];
		
		// on invalid values
		if ( empty($file) )
			return print_r( "Templax: invalid filename", false );
		
		// on not existing file
		if ( !file_exists($file) )
			return print_r( "Templax: file '{$file}' doesnt exist", false );
		
		$content = preg_replace("/\r\n/", "", preg_replace("/\s{2,}/", "", file_get_contents($file)) );

		// cache when permission given
		if ( $cache ) $this->cache[$file] = $content;

		return $content;
	}

	/**
	 * returns the template within a file surrounded by the template id
	 * 
	 * @param string @id - the template id
	 * @param string @file - the file in which the template is defined
	 * 
	 * @return string|null
	 */
	public function extractTemplateFromFile( string $id, string $file ) {

		// on invalid values
		if ( empty($id) )
			return print_r( "Templax: invalid id in in extracting the template from file '{$file}'", false );
		
		// on not existing file
		if ( !file_exists($file) )
			return print_r( "Templax: file '{$file}' doesnt exist", false );
		
		// extract template
		$marker = "###" . strtoupper($id) . "###";
		$regex = "/" . $marker . "(.*)" . $marker . "/";

		preg_match( $regex, $this->getFileTemplate( $file ), $template );

		return ($template) ? $template[1] : null;
	}

	/**
	 * returns the template instance or null when not found or invalid id
	 * 
	 * @param string $id - the template id
	 * 
	 * @return \Templax\Source\Models\Template|null
	 */
	public function &get( $id ) {

		if ( !$this->has($id) )
			return null;
		
		return $this->templates[$id];
	}

	/**
	 * returns all templates
	 * 
	 * @return array
	 */
	public function getTemplates() {
		
		return $this->templates;
	}

	/**
	 * returns the existance of a registered template as boolean
	 * 
	 * @param string $id - the template id
	 * 
	 * @return boolean
	 */
	public function has( $id ) {
		
		return is_string($id) && isset($this->templates[$id]);
	}

	/**
	 * registers a template
	 * 
	 * @param string $id - the template id
	 * @param array $config - the template configuration @see __construct( ... )
	 * 
	 * @return boolean
	 */
	public function register( string $id, array $args ) {
		
		if ( empty($id) || empty($args) )
			return print_r( "Templax: invalid values in registration", false );
		
		// register either single template file or multiple template file

		// substitutions
		$cache = ( isset($args["options"]["cache"]) )
			? $args["options"]["cache"]
			: true;
		
		$tContent = !empty( $args["marker"] )
			? $this->extractTemplateFromFile( $args["marker"], $args["file"] )
			: $this->getFileTemplate( $args["file"], $cache );

		if ( !$tContent )
			return print_r( "Templax: failed to extract template from file '{$file}' failed", false );

		$this->templates[$id] = new Models\Template( $id, $tContent, $args["markup"], $args["options"] );

		return true;
	}

	/**
	 * registers multiple templates as a whole
	 * 
	 * @param array $templates - the templates that shall be registered
	 * @param bool $cancelOnFail - defines wether the registration should cancel
	 * 	further registrationg when one template failed to be registered
	 * 
	 * @return boolean
	 */
	public function registerTemplateSet( array $templates, bool $cancelOnFail = false ) {

		// nothing to register ..
		if ( empty($templates) )
			return !$cancelOnFail;

		// the full base of a template registration configuration
		$regBase = array( "markup" => array(), "options" => array(), "file" => "", "marker" => "" );

		// try to register every template but perfom checks on type to avoid type exceptions
		foreach( $templates as $id => $config ) {
			
			$regArgs = $regBase;
			$valid = true;
			
			// when invalid id nothing to register further more
			if ( !is_string($id) || empty($id) )
				$valid = false;
			
			// when no configuration is given nothing to register
			else if ( !empty($config) ) {

				// on single template file
				if ( is_string($config) )
					$regArgs["file"] = $config;
					
				// on (expected) multiple template files
				else if ( is_array($config) )
					foreach( $config as $key => $value )
						$regArgs[$key] = $value;
				
				else $valid = false;
			}
			
			// invalid when no valid values are given
			else $valid = false;

			if ( (!$valid || !$this->register($id, $regArgs)) && $cancelOnFail )
				return false;
		}

		return true;
	}
}

//_____________________________________________________________________________________________
//