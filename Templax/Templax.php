<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	html template parser main file / to use include this file and its done
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax;

error_reporting(E_ALL & ~E_NOTICE);

use \Templax\Source\Models;
use \Templax\Source\Manager;
use \Templax\Source\Parser;

define( "TEMPLAX_ROOT", __DIR__, true );

require_once ( TEMPLAX_ROOT . "/Configuration.php" );
require_once ( TEMPLAX_ROOT . "/Source/Manager/ProcessManager.php" );
require_once ( TEMPLAX_ROOT . "/Source/Manager/TemplateManager.php" );
require_once ( TEMPLAX_ROOT . "/Source/Parser/RequestParser.php" );
require_once ( TEMPLAX_ROOT . "/Source/Parser/RuleParser.php" );
require_once ( TEMPLAX_ROOT . "/Source/Models/Query.php" );

//_____________________________________________________________________________________________
class Templax {

	static private $tManager = null;
	static private $pManager = null;

	static private $defined = false;

	static private $defaultMarkup = array();
	static private $defaultOptions = array();

	static public $logfile = null;

	//_________________________________________________________________________________________
	// defines the parser with the given values
	//
	// param1 (array) expects the template sets - there are 2 ways of defining a template config
	//		method1:	array( "templateid" => "path/to/template" );
	//		method2:	array( "templateid" => array(
	//						"path" => "path/to/template",
	//						"marker" => "###MARKER_SURROUNDING_TEMPLATE###"
	//					));
	//
	// param2 (array) expects the default markup for this parser
	//		these values will be overwritten in this order
	//			templateOptions -> options passed to the "->parse" function
	// param3 (array) expects the default option set / values will be overwritten when passing
	//		a option set to the parsing function
	//
	// return boolean
	//		true - when defined
	//		false - invalid values/ missing dependencies
	// 
	static public function define( array $templates, array $markup = array(),
		array $options = array() ): bool
	{

		if ( $defined )
			return self::$logfile->logReturn( "Templax: Parser already defined - reset first or use the associated class operations" );

		// check dependencies
		if (!self::checkDependencies( $GLOBALS["Templax"]["Dependencies"], function($file) {
			self::$logfile->log( "Templax: Dependency {$name} ({$file})is missing");
		}))
		{
			return false;
		}

		self::$tManager = new Manager\TemplateManager();
		self::$pManager = new Manager\ProcessManager();	

		self::$logfile = new \Logfile\Logfile;
		
		self::$defaultMarkup = $markup;
		self::$defaultOptions = $options;

		self::$tManager->registerTemplateSet( $templates );
		
		self::$defined = true;

		return true;
	}

	//_________________________________________________________________________________________
	// checks wether all dependencies are given
	//
	// param1 (array) expects the dependencies
	//		$libraryName => $pathToFile
	// param2 (callback) expects the callback that invokes when a missing dependency has been found
	//		callback parameteres
	//			param1 (string) expects the name of the library
	//			param2 (string) expects the path to the file
	//
	// return boolean
	//		true - when all dependencies are present
	//		false - when dependencies are missing - pull the logs from the internal logfile
	//
	static private function checkDependencies( array $dependencies, callable $callback = null,
		bool $cancelOnFirstFound = false): bool
	{

		// dont need to check when there are no dependencies
		if ( count($dependencies) )
			return true;

		$valid = true;
		
		// check dependency existance and react how defined
		// pass file to callback and return when necessary
		//
		foreach( $dependencies as $name => $file ) {

			if ( !file_exists($file) && $callback !== null ) {

				$callback( $name, $file );

				if ( $cancelOnFirstFound ) return false;
				else if ( $valid ) $valud = false;
			}
		}

		return $valid;
	}

	//_________________________________________________________________________________________
	// parses the given template id with the given markup based on the given options
	//
	// default markup and options are applied before the template is parsed
	//
	// param1 (string) expects either a template id or a template string
	// 		template ids have higher prio due to the fact that they are verified first
	//		when the template is not found / given value will be parsed as undefined template
	// param2 (array) expects the markup for the requested template
	//
	// return string
	//		empty string - when something failed - get open logs from the internal logfile
	//		string - parsed template
	//
	static public function parse( string $id, array $markup = array() ): string {

		if ( !self::$defined ) {
			print_r("Please check the dependencies or defined values you passed to define()");
			return "";
		}

		if ( !self::$tManager || !self::$pManager )
			return self::$logfile->logReturn( "Please call the '::define()' function to initially define the parser", "" );

		$pSet = self::verifyParsingSet( $id, $markup );

		$content = self::processTemplate( $pSet["template"], $pSet["markup"], $pSet["options"], function( $query ) {
			return \Templax\Source\Parser\RequestParser::parse( $query );
		});

		// display error of the parsing
		foreach( \Templax\Templax::$logfile->getOpenLogs() as $index => $log )
			print_r( "\n" . $log->getMessage() );

		return $content;
	}

	//_________________________________________________________________________________________
	// verifies the given params and returns an (assossiated) array
	// replaces the invalid values with defaults
	//
	// param1 (string) expects the template id
	// param2 (array) expects the markup
	//
	// return array - the parsing set
	//
	static private function verifyParsingSet( string $templateId, array $markup ): array {


		// implement functionality that defaults are applied => markup/options
		$parsingSet = array(
			"template" => ( self::$tManager->has($templateId) )
				? self::$tManager->get( $templateId )
				: new Models\Template( null, $templateId ),
			"markup" => $markup,
			"options" => ($markup["_options"]) ? $markup["_options"] : array()
		);
		
		return $parsingSet;
	}

	//_________________________________________________________________________________________
	// queries through the rules in the given template and replaces the callbacks result
	// with the matched rule. Returns the parsed content when done
	//
	// param1 (\Templax\Source\Models\Template) expects the template
	// param2 (array) expects the markup
	// param3 (array) expects the options
	// param4 (callable) expects the callback function in which the query will be passed
	//
	// return string
	//
	static public function processTemplate( Models\Template $template, array $_markup = array(), 
		array $_options = array(), callable $_callback = null ): string
	{
		//
		$options = array_merge( self::$defaultOptions, $_options );

		// register current template
		$process = self::$pManager->create( $template, $_markup, $options );
		$process->setQueryMarkup( array_merge(self::buildBaseMarkup($process), $template->getMarkup(), $_markup) );
		
		// render only when allowed
		if ( !$process->getOption("render") )
			return "";

		// constant values / they are used not changed
		$regExtractRule = $GLOBALS["Templax"]["Configuration"]["Regex"]["extractRule"];
		$callback = ( !$_callback ) ? function() {} : $_callback;

		// values that are reassigned all over again
		$queryingTemplate = $template->getValue();
		$content .= "";
		$offset = 0;
		$lastIterator = $offset;
		$postQueries = array();

		while( preg_match($regExtractRule, $queryingTemplate, $match, PREG_OFFSET_CAPTURE, $offset) ) {

			// store current iterator
			$iterator = $match[0][1];
			
			$rule = Parser\RuleParser::parse( $process, $match[0][0] );

			// to avoid conflicts width post query request only the content
			// in the current context will be passed
			$query = new Models\Query( $process, $rule, substr($queryingTemplate, $offset), null );
			$process->setCurrentQuery( $query );
			
			// check response and validate its values / replace invalid with empty string
			// and adjust types when not string
			$response = self::reviewResponse( $process, $callback( $query ) );

			// adjust offset to ensure to not parse any post queries or unecessary strings
			// when a postquery is set usually a offset is defined - so consinder that one too
			$offset = $iterator + ( ($response->hasOffset()) ? $response->getOffset() : strlen($response->getReplacement()) );

			// to ensure that we replace only rules for a specific scope we need to extract
			// that specific scope and only replace the rule within the scope in the template
			// in other words - the substring only belongs to the currently parsed rule
			// while still containing other string elements like html tags or xml etc.
			$response->setContext(
				substr($queryingTemplate, $lastIterator, ($iterator - $lastIterator) + strlen($response->getReplacement()))
			);

			// store last iterator to extract next context correctly
			$lastIterator = $iterator + strlen($response->getReplacement());

			// process response and get the content piece of the current rule
			$content .= self::processResponse( $response, $postQueries );
		}

		// process post queries
		foreach( $postQueries as $index => $config ) {

			$response = self::reviewResponse( $process, $callback( $config["query"] ) );
			$content = str_replace( $config["placeholder"], $response->getValue(), $content );
		}

		// attach the rest of the document
		$content .= substr( $queryingTemplate, $offset );
		
		return $content;
	}

	//_________________________________________________________________________________________
	// processes the given response and returns the replaced content
	//
	// param1 (\Templax\Source\Models\Response) expects the response
	// param2 (array) expects the post queries
	//
	// return string - the parsed content based on the given response
	//
	static private function processResponse( Models\Response $response,
		array &$postQueries = null ): string
	{
		
		// based on what query type the response returns the value for the replacement differs
		// initialy its the regular value from the response
		$replacementValue = $response->getValue();

		// alias
		$query = &$response->getPostQuery();

		// when post query consider it to tbe parsed later on again
		// and use a unique replacement value to identify and replace it later on
		if ( $postQueries !== null ) {
			if ( $response->isPostQuery() ) {
				
				$uid = "placeholder_" . str_replace("-", "_", $query->getKey()) . "_" . Parser\RuleParser::getRuleCount();
				if ( $postQueries !== null ) {
					array_push($postQueries, array("placeholder" => $uid, "query" => $query) );
					$replacementValue = $uid;
				}
			}
		}

		// !! THE HEART LINE of this framework !! YEAAAAAH !!
		// - and the one in the post query post processing
		//
		// finally replacing the content with its value
		//
		// content is only replaced in the given context
		// this is done like this because the str_replace function doesnt have a limiter
		// nor has the grep_replace function the possibility to interpret html tags as regex
		// so we need to define our own scope an assign the replaced content to the already parsed one
		$content = str_replace( $response->getReplacement(), $replacementValue, $response->getContext() );

		return $content;
	}

	//_________________________________________________________________________________________
	// builds the base markup for a template process
	//
	// param1 (\Templax\Source\Models\Process) expects the template process
	//
	// return array - the base markup of the proces
	//
	static private function buildBaseMarkup( Models\Process $process ): array {

		$markup = array_merge(
			self::$defaultMarkup,
			array(
				"tx-template" => $process->getTemplate()->getId()
			),
			$process->getUserMarkup()
		);

		return $markup;
	}

	//_________________________________________________________________________________________
	// verifies and replaces invalid values of the passed response
	// replacing invalid values avoids parsetime errors
	//
	// param1 (\Templax\Source\Models\Process) expects the querying template process
	// param2 (\Templax\Source\Models\Response) expects the response
	//
	// return \Templax\Source\Models\Response - the reviewed and adjusted response
	//
	static private function reviewResponse( Models\Process $process,
		Models\Response $response ): Models\Response
	{

		// when no response is given
		if ( !$response )
			return new Models\Response( $process->getCurrentQuery()->getRawRule() );

		// replace replacement into the rule to avoid ugly rule showup
		if ( !is_string($response->getReplacement()) || !$response->getReplacement() )
			$response->setReplacement( $process->getCurrentQuery()->getRawRule() );
		
		// cast into string when not string
		if ( !is_string($response->getValue()) ) {
			$value = $response->getValue();
			$response->setValue( ( is_array($value) || is_object($value) )
				? ""
				: (string) $response->getValue()
			);
		}

		return $response;
	}

	//_________________________________________________________________________________________
	// returns the template manager
	//
	// return reference \Templax\Source\Manager\TemplateManager - the template manager
	//
	static public function &templateManager(): Manager\TemplateManager {

		return self::$tManager;
	}

	//_________________________________________________________________________________________
	// shorthand for accessing the templates operations via the template manager
	//
	// param1 (string) expects the template id
	//
	// return boolean
	//		true - template is registered
	//		false - template is not registered
	//
	static public function hasTemplate( string $id ): bool {

		return self::$tManager->has( $id );
	}

	//_________________________________________________________________________________________
	// shorthand for accessing the templates operations via the template manager
	//
	// return reference \Templax\Models\Template - the template instance
	//
	static public function &getTemplates(): Models\Template {

		return self::$tManager->get( null, true );
	}

	//_________________________________________________________________________________________
	// shorthand for accessing the templates operations via the template manager
	//
	// param1 (string) expects the template id
	//
	// return reference \Templax\Source\Models\Template - the template instance
	//
	static public function &getTemplate( string $id ): Models\Template {

		return self::$tManager->get( $id );
	}
}

//_____________________________________________________________________________________________
//