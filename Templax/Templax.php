<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * templax - the render engine using templates to create string content
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax;

use \Templax\Source\Models;

error_reporting( E_ALL & ~E_NOTICE );

define( "TEMPLAX_ROOT", __DIR__, true );

// include engine parts
require_once( TEMPLAX_ROOT . "/Configuration.php" );
require_once( TEMPLAX_ROOT . "/Source/ProcessManager.php" );
require_once( TEMPLAX_ROOT . "/Source/TemplateManager.php" );
require_once( TEMPLAX_ROOT . "/Source/QueryParser.php" );
require_once( TEMPLAX_ROOT . "/Source/RuleParser.php" );
require_once( TEMPLAX_ROOT . "/Source/Models/Query.php" );
require_once( TEMPLAX_ROOT . "/Source/Models/ParsingSet.php" );

//_____________________________________________________________________________________________
class Templax {

	/**
	 * the template manager
	 * 
	 * @var \Templax\Source\TemplateManager|null
	 */
	static public $tManager = null;

	/**
	 * the process manager
	 * 
	 * @var \Templax\Source\ProcessManager|null
	 */
	static private $pManager = null;

	/**
	 * the rule parser
	 * 
	 * @var \Templax\Source\RuleParser|null
	 */
	static private $rParser = null;

	/**
	 * query parser
	 * 
	 * @var \Templax\Source\QueryParser|null
	 */
	static private $qParser = null;

	/**
	 * the default markup for a template
	 * 
	 * @var array
	 */
	static private $baseMarkup = array();

	/**
	 * the default options for a template parsing
	 * 
	 * @var array
	 */
	static private $baseOptions = array();

	/**
	 * stores post values for keys in the markup by the rule-signature
	 * 
	 * @var array
	 */
	static private $hooks = array();

	/**
	 * stores the initial process
	 * 
	 * @var \Templax\Source\Models\Process|null
	 */
	static private $mainProcess;

	/**
	 * describes wether a template is currently process or not
	 * 
	 * @var boolean
	 */
	static private $processing;

	/**
	 * describes wether Templax is initialized or not
	 * 
	 * @var boolean
	 */
	static private $initialized = false;

	/**
	 * no construction needed since Templax is a static one
	 */
	private function __construct() {}

	/**
	 * initializes Templax / re-initialization are possible!
	 * 
	 * @param array $templates - set of template configuration that shall be registered
	 * 	templates are registerable even after the initialization
	 * 	@see TemplateManager::registerTemplateSet()
	 * 	@see TemplateManager::register()
	 * @param array $defaultMarkup - the default markup for all templates - rules overwrite defaults
	 * @param array $defaultOptions - the default options for all templates - rules overwrite defaults
	 * 
	 * @return boolean
	 */
	static public function Init( array $templates, array $defaultMarkup = array(), array $defaultOptions = array() ) {
		
		// create instances
		self::$pManager = new Source\ProcessManager();
		self::$tManager = new Source\TemplateManager( $templates );
		self::$rParser = new Source\RuleParser();
		self::$qParser = new Source\QueryParser();

		// define defaults - merge the Templax defaults over the user ones
		self::$baseMarkup = array_merge( $GLOBALS["Templax"]["Defaults"]["BaseMarkup"], $defaultMarkup );
		self::$baseOptions = array_merge( $GLOBALS["Templax"]["Defaults"]["BaseOptions"], $defaultOptions );

		// finish
		self::$initialized = true;
		self::$processing = false;

		return true;
	}

	/**
	 * parses the given template or the template behind the id with the given markup
	 * 
	 * Todo: complete function header comment
	 * 
	 * @return string
	 */
	static public function parse( $source, ?array $markup = array(), ?array $hooks = array(), Models\Process $parentProcess = null ) {

		// when not initialized several functionalities would'nt work
		if ( !self::$initialized )
			return print_r( "Templax" . __LINE__ . ": initialize system first before parsing a template @see Templax::Init(...)", "" );
		
		// get parsing set from verifying the source and its markup
		$pSet = new Models\ParsingSet($source, $markup, $markup["_options"], $parentProcess );

		// when parsing set is invalid no parsing will happen
		if ( !$pSet->valid )
			return print_r( "Templax " . __LINE__ . ": invalid parsing set - resulting from invalid template, template id or markup" , "" );
		
		// when the set is fine and the main process is not created
		// assign the main values for the following template process
		if ( !self::$processing ) {
			
			self::$rParser->hooks = $hooks;

			self::$processing = true;
		}

		// process template
		$content = self::processTemplate( $pSet, function($query) {
			return self::$qParser->parse( $query );
		});

		// when the main process is deleted the template has finished processing
		if ( is_null(self::$mainProcess) )
			self::$processing = false;

		return $content;
	}

	/**
	 * processes the given template with the given markup, options and passes for every rule
	 * the built query to the callback / the typ
	 * 
	 * @param \Templax\Source\Models\ParsingSet - the parsing set to process a template
	 * @param callable $callback - the callback in which the query is passed
	 * 	@return \Templax\Source\Models\Response - the response from the callback
	 * 
	 * @return string
	 */
	static public function processTemplate( Models\ParsingSet $pSet, callable $callback = null) {

		// create new process
		$process = self::$pManager->create( $pSet );

		// when its the main process
		if ( $process->isMainProcess )
			self::$mainProcess = $process;

		// create query markup from merged user over base markup
		$process->queryMarkup = array_merge( self::$baseMarkup, $pSet->markup );

		// when no rendering is allowed
		if ( !$process->getOption("render") )
			return "";
		
		// function values
		$queryingTemplate = $pSet->source->value;
		$content = "";
		$offset = 0;
		$lastIterator = $offset;
		$postQueries = array();


		// print_r("Main: {$process->id} parent: {$process->parent->id} signature: {$process->parent->query->request} \n");

		// final template parsing
		while( preg_match($GLOBALS["Templax"]["ExtractionRegex"]["extractRule"], $queryingTemplate, $rawRule, PREG_OFFSET_CAPTURE, $offset) ) {
			
			// store current iterator
			$iterator = $rawRule[0][1];
			
			$rule = self::$rParser->parse( $process, $rawRule[0][0] );

			// to avoid conflicts width post query request only the content
			// in the current context will be passed
			$query = new Models\Query( $process, $rule, substr($queryingTemplate, $offset), null );
			$process->query = $query;
			
			// check response and validate its values / replace invalid with empty string
			// and adjust types when not string
			$response = self::reviewResponse( $process, $callback( $query ) );

			// adjust offset to ensure to not parse any post queries or unecessary strings
			// when a postquery is set usually a offset is defined - so consinder that one too
			$offset = $iterator + ( ($response->hasOffset()) ? $response->offset : strlen($response->replacement) );

			// to ensure that we replace only rules for a specific scope we need to extract
			// that specific scope and only replace the rule within the scope in the template
			// in other words - the substring only belongs to the currently parsed rule
			// while still containing other string elements like html tags or xml etc.
			$response->context = substr( $queryingTemplate, $lastIterator, ($iterator - $lastIterator) + strlen($response->replacement) );

			// store last iterator to extract next context correctly
			$lastIterator = $iterator + strlen( $response->replacement );

			// process response and get the content piece of the current rule
			$content .= self::processResponse( $response, $postQueries );
		}

		// process post queries
		foreach( $postQueries as $index => $config ) {

			$response = self::reviewResponse( $process, $callback( $config["query"] ) );
			$content = str_replace( $config["placeholder"], $response->value, $content );
		}

		// attach the rest of the document
		$content .= substr( $queryingTemplate, $offset );
		
		return $content;
	}

	/**
	 * process the response and returns the replaced content
	 * 
	 * @param Models\Response $response - the response
	 * @param array|null $postQueries - a reference to the post queries
	 * 
	 * @return string
	 */
	static private function processResponse( Models\Response $response, array &$postQueries = null ) {

		// based on what query type the response returns the value for the replacement differs
		// initialy its the regular value from the response
		$replacementValue = $response->value;

		// alias
		$query = &$response->postQuery;

		// when post query consider it to be parsed later on again
		// and use a unique replacement value to identify and replace it later on
		if ( $postQueries !== null ) {

			if ( !is_null($response->postQuery) ) {
				
				$uid = "placeholder_" . str_replace("-", "_", $query->key) . "_" . self::$rParser::getRuleIterator();

				if ( !is_null($postQueries) ) {
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
		$content = str_replace( $response->replacement, $replacementValue, $response->context );

		return $content;
	}

	/**
	 * reviews the response and replaces all invalid values into valid ones
	 * and casts everything into the valid type
	 * basically this function cleans up the given response
	 * 
	 * @param \Templax\Source\Models\Process $process - the current process
	 * @param \Templax\Source\Models\Repsonse|null $response - the response
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	static private function reviewResponse( Models\Process $process, ?Models\Response $response ) {

		// when null given return a base response
		if ( is_null($response) )
			return new Models\Response( $process->query->rawRule );
		
		// when the replacement is invalid
		if ( !is_string($response->replacement) )
			$response->replacement = $process->query->rawRule;
		
		return $response;
	}
}

//_____________________________________________________________________________________________
//