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

// include engine
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
	static public $pManager = null;

	/**
	 * the rule parser
	 * 
	 * @var \Templax\Source\RuleParser|null
	 */
	static public $rParser = null;

	/**
	 * query parser
	 * 
	 * @var \Templax\Source\QueryParser|null
	 */
	static public $qParser = null;

	/**
	 * defaults parsing set
	 * 
	 * @var \Templax\Source\Models\ParsingSet|null
	 */
	static private $baseParsingSet;

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
	static private $initialized;

	/**
	 * no construction needed since Templax is a static one
	 */
	private function __construct() {}

	/**
	 * extracts, removes and returns an item from the array by reference
	 * 
	 * @param array &$markup - the markup
	 * 
	 * @return array - the options
	 */
	static public function _custom_array_remove( array &$array, $key ) {

		// check if exists, copy and return item
		if ( array_key_exists($key, $array) ) {

			$var = $array[$key];

			unset($array[$key]);

			return $var;
		}

		// else null..
		return null;
	}

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
	static public function Init( array $templates = array(), array $markup = array(), array $options = array() ) {
		
		// create instances
		self::$pManager = new Source\ProcessManager();
		self::$tManager = new Source\TemplateManager( $templates );
		self::$rParser = new Source\RuleParser();
		self::$qParser = new Source\QueryParser();

		// define base parsing set for backup
		self::$baseParsingSet = new Models\ParsingSet(
			new Models\Template( null, "" ),
			$markup,
			$options,
			null
		);

		// finish
		self::$initialized = true;
		self::$processing = false;

		return true;
	}

	/**
	 * parses the given template or the template behind the id with the given markup
	 * 
	 * @param \Templax\Source\Models\Template|string $source - the template source
	 * 	Template - a template instance
	 *  string - the template id (must be a registered template)
	 * 
	 * @return string
	 */
	static public function parse( $source, array $markup = array(), array $hooks = array(), Models\Process $parent = null ){

		// when not initialized several functionalities would'nt work
		if ( !self::$initialized )
			throw new \Exception( "Templax: initialize system first before parsing a template @see Templax::Init(...)" );

		// when no main process exists start the parsing run
		if ( !self::$pManager->mainProcessExists() )
			self::start( $hooks );

		// extract options before creating set because the markup gets the "_options" removed
		// its cleaner when looking at the markup and not seeing the "_options" index
		$options = (array) self::_custom_array_remove($markup, "_options");
		$set = new Models\ParsingSet($source, $markup, $options, $parent );

		// process template
		$content = self::processTemplate( $set, function($query) {

			return self::$qParser->parse( $query );
		});

		// when the main process is deleted shut down the parser
		if ( !self::$pManager->mainProcessExists() )
			self::shutdown();

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
		
		// if invalid parsing set no parsing will happen
		if ( !$pSet->verify() )
			throw new \Exception( "Templax: invalid parsing set. Check the template definition" );

		// create new process
		$process = self::$pManager->create( $pSet );

		// create query markup from merged user over base markup
		$process->rMergeMarkup( self::$baseParsingSet->getMarkup() );

		// when no rendering is allowed
		if ( !$process->getOption("render") )
			return "";
		
		// function values
		$queryingTemplate = $pSet->getTemplate()->getValue();
		$content = "";
		$offset = 0;
		$lastIterator = $offset;
		$postQueries = array();

		// final template parsing
		while( preg_match($GLOBALS["Templax"]["ExtractionRegex"]["ExtractRule"], $queryingTemplate, $rawRule, PREG_OFFSET_CAPTURE, $offset) ) {
			
			// store current iterator
			$iterator = $rawRule[0][1];
			
			$rule = self::$rParser->parse( $process, $rawRule[0][0] );

			// to avoid conflicts width post query request only the content
			// in the current context will be passed
			$query = new Models\Query( $process, $rule, substr($queryingTemplate, $offset), null );

			$process->setCurrentQuery( $query );
			
			// get response and review
			$response = ( $callback( $query ) )->review( $process );			

			// adjust offset to ensure to not parse any post queries or unecessary strings
			// when a postquery is set usually a offset is defined - so consinder that one too
			$offset = $iterator + ( ($response->hasOffset()) ? $response->offset : strlen($response->getReplacement()) );

			// to ensure that we replace only rules for a specific scope we need to extract
			// that specific scope and only replace the rule within the scope in the template
			// in other words - the substring only belongs to the currently parsed rule
			// while still containing other string elements like html tags or xml etc.
			$response->setContext( substr($queryingTemplate, $lastIterator, ($iterator - $lastIterator) + strlen($response->getReplacement())) );

			// store last iterator to extract next context correctly
			$lastIterator = $iterator + strlen( $response->getReplacement() );

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

		// delete this process
		self::$pManager->delete( $process );
		
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
		$replacementValue = $response->getValue();

		// alias
		$query = $response->getPostQueryRef();

		// when post query consider it to be parsed later on again
		// and use a unique replacement value to identify and replace it later on
		if ( $postQueries !== null ) {

			if ( !is_null($query) ) {
				
				$uid = "placeholder_" . str_replace("-", "_", $query->getKey()) . "_" . self::$rParser::getRuleIterator();

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
		$content = str_replace( $response->getReplacement(), $replacementValue, $response->getContext() );

		return $content;
	}

	/**
	 * shuts the parser down and resets everything 
	 * 
	 * @return boolean - true on success otherwise false
	 */
	static private function shutdown() {

		self::$rParser->shutdown();

		return true;
	}

	/**
	 * starts a parsing run / this includes multiple template processings
	 * 
	 * @param array $hooks - the hooks for this run
	 * 
	 * @return boolean
	 */
	static private function start( array $hooks = array() ) {

		// start the rule parser
		self::$rParser->start( $hooks );

		// finish with the processing state
		self::$processing = true;

		return true;
	}
}

//_____________________________________________________________________________________________
//