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

use \Templax\Source;
use \Templax\Source\Models;
use \Templax\Source\Classes;

define( "TEMPLAX_ROOT", __DIR__, true );

// Basics
require_once( TEMPLAX_ROOT . "/Configuration.php" );
require_once( TEMPLAX_ROOT . "/Source/Classes/ParameterBag.php" );
require_once( TEMPLAX_ROOT . "/Source/Classes/Miscellaneous.php" );

// components
require_once( TEMPLAX_ROOT . "/Source/ProcessManager.php" );
require_once( TEMPLAX_ROOT . "/Source/TemplateManager.php" );
require_once( TEMPLAX_ROOT . "/Source/QueryParser.php" );
require_once( TEMPLAX_ROOT . "/Source/RuleParser.php" );

// models
require_once( TEMPLAX_ROOT . "/Source/Models/Query.php" );
require_once( TEMPLAX_ROOT . "/Source/Models/ParsingSet.php" );

//_____________________________________________________________________________________________
// templax - the render engine/framework
class Templax extends Source\TemplateManager {

	/**
	 * defines wether the framework has been booted or not
	 * it only can be booted once
	 * 
	 * @var boolean
	 */
	static private $booted;

	/**
	 * instance of the framework
	 * 
	 * @var \Templax\Templax
	 */
	static public $instance = null;

	/**
	 * the process manager
	 * 
	 * @var \Templax\Source\ProcessManager|null
	 */
	private $pManager = null;

	/**
	 * the rule parser
	 * 
	 * @var \Templax\Source\RuleParser|null
	 */
	public $rParser = null;

	/**
	 * query parser
	 * 
	 * @var \Templax\Source\QueryParser|null
	 */
	public $qParser = null;

	/**
	 * defaults parsing set
	 * 
	 * @var \Templax\Source\Models\ParsingSet|null
	 */
	private $baseParsingSet;

	/**
	 * describes wether a template is currently processing or not
	 * 
	 * @var boolean
	 */
	private $processing;

	/**
	 * construction
	 */
	public function __construct() {

		parent::__construct( $templates );

		// check if booting was successful
		if ( !static::$booted )
			throw new \Exception( "Templax: automatic booting of system was not successful. Try to boot it manually by calling \Templax\Templax::boot()" );
		
		// when post booting the instance shall always remain the same
		if ( !is_null(static::$instance) )
			return $this->fromInstance( static::$instance );
		
		// create instances
		$this->pManager = new Source\ProcessManager();
		$this->rParser = new Source\RuleParser();
		$this->qParser = new Source\QueryParser();

		$this->baseParsingSet = new Models\ParsingSet( new Models\Template(null, "") );

		$this->processing = false;
	}

	/**
	 * initially defines the framework
	 * 
	 * @return boolean - true on success else false
	 */
	static public function boot() {

		if ( !is_null(static::$booted) )
			return true;
		
		static::$booted = true;
		static::$instance = new \Templax\Templax();

		return true;
	}

	/**
	 * initializes this instance from another one
	 */
	public function fromInstance( \Templax\Templax $rhs ) {

		$this->pManager = $rhs->pManager;
		$this->rParser = $rhs->rParser;
		$this->qParser = $rhs->qParser;

		$this->baseParsingSet = $rhs->baseParsingSet;
		$this->processing = $rhs->processing;

		// update instance
		static::$instance->merge( null, $this->all() );

		return $this;
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
	public function Init( array $templates = array(), array $markup = array(), array $options = array() ) {

		$this->registerTemplateSet( $templates );
		
		// overwrite base parsing set configurations
		$this->baseParsingSet->merge( null, array(
			"markup" => new Classes\ParameterBag( $markup ),
			"options" => new Classes\ParameterBag( $options )
		));

		// finish
		$this->initialized = true;

		return $this;
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
	public function parse( $source, array $markup = null, array $hooks = null, Models\Process $parent = null ){
		
		// when not initialized several functionalities would'nt work
		if ( !$this->initialized )
			throw new \Exception( "Templax: initialize system first before parsing a template @see Templax::Init(...)" );

		// when no main process exists start the parsing run
		if ( !$this->pManager->mainProcessExists() )
			$this->start( (array) $hooks );

		// extract options before creating set because the markup gets the "_options" removed
		// its cleaner when looking at the markup and not seeing the "_options" index
		$options = (array) Classes\Miscellaneous::array_remove( $markup, "_options" );
		$set = new Models\ParsingSet( $source, $markup, $options, $parent );

		// process template
		$content = $this->processTemplate( $set, function($query) {
			return $this->qParser->parse( $query );
		});

		// when the main process is deleted shut down the parser
		if ( !$this->pManager->mainProcessExists() )
			$this->shutdown();

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
	public function processTemplate( Models\ParsingSet $pSet, callable $callback = null) {
		
		// if invalid parsing set no parsing will happen
		if ( !$pSet->verify() )
			throw new \Exception( "Templax: invalid parsing set. Check the template definition" );

		// create new process
		$process = $this->pManager->create( $pSet );

		// create query markup from merged user over base markup
		$process->rMerge("markup", $this->baseParsingSet->all("markup") );

		// when no rendering is allowed
		if ( !$process->get(["options", "render"]) )
			return "";
		
		// function values
		$queryingTemplate = $pSet->get(["template", "value"]);
		$content = "";
		$offset = 0;
		$lastIterator = $offset;
		$postQueries = array();

		// final template parsing
		while( preg_match($GLOBALS["Templax"]["ExtractionRegex"]["ExtractRule"], $queryingTemplate, $rawRule, PREG_OFFSET_CAPTURE, $offset) ) {
			
			// store current iterator
			$iterator = $rawRule[0][1];
			
			$rule = $this->rParser->parse( $process, $rawRule[0][0] );

			// to avoid conflicts width post query request only the content
			// in the current context will be passed
			$query = new Models\Query( $process, $rule, substr($queryingTemplate, $offset) );

			$process->set("currentQuery", $query );
			
			// get response and review
			$response = ( $callback($query) )->review( $process );

			// store possible data cache and update process
			if ( !$response->isNull("dataCache") ) {
				
				$this->pManager->getDataCache()->merge( null, $response->all("dataCache") );
				$this->pManager->updateProcess( $process );
			}

			// adjust offset to ensure to not parse any post queries or unecessary strings
			// when a postquery is set usually a offset is defined - so consinder that one too
			$offset = $iterator + (!$response->isNull("indexOffset") ? $response->get("indexOffset") : strlen($response->get("replacement")) );

			// to ensure that we replace only rules for a specific scope we need to extract
			// that specific scope and only replace the rule within the scope in the template
			// in other words - the substring only belongs to the currently parsed rule
			// while still containing other string elements like html tags or xml etc.
			$response->set("context", substr($queryingTemplate, $lastIterator, ($iterator - $lastIterator) + strlen($response->get("replacement"))) );

			// store last iterator to extract next context correctly
			$lastIterator = $iterator + strlen( $response->get("replacement") );

			// process response and get the content piece of the current rule
			$content .= $this->processResponse( $response, $postQueries );
		}

		// process post queries
		foreach( $postQueries as $index => $config ) {
			
			$response = $callback($config["query"])->review( $process );

			$content = str_replace( $config["identifier"], $response->get("value"), $content );
		}

		// attach the rest of the document
		$content .= substr( $queryingTemplate, $offset );

		// delete this process
		$this->pManager->delete( $process );
		
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
	private function processResponse( Models\Response $response, array &$postQueries = null ) {

		// based on what query type the response returns the value for the replacement differs
		// initialy its the regular value from the response
		$replacementValue = $response->get("value");

		// alias
		$query = $response->getRef("postQuery");

		// when its a post query replace current context with a unique identifier (placeholder)
		// and parse it when all other rules in the current template parsing are parsed
		if ( $postQueries !== null && !is_null($query) ) {
			
			// create identifier based on the rules iterator which always counts up
			// this provides a definite unique value
			$uid = "placeholder_" . str_replace("-", "_", $query->get("key")) . "_" . $this->rParser->getRuleIterator();

			// define as post query and store for later parsing
			$query->set( "isPostQuery", true );

			$postQueries[] = array(
				"identifier" => $uid,
				"query" => $query
			);

			$replacementValue = $uid;
		}

		// !! THE HEART LINE of this framework !! YEAAAAAH !!
		// - and the one in the post query post processing
		//
		// finally replacing the content with its value
		//
		// content is only replaced in the given context
		// this is done like this because the str_replace function doesnt have a limiter
		// nor has the grep_replace function the possibility to interpret html tags as regex
		// so we need to define our own scope and assign the replaced content to the already parsed one
		$content = str_replace( $response->get("replacement"), $replacementValue, $response->get("context") );

		return $content;
	}

	/**
	 * shuts the parser down and resets everything 
	 * 
	 * @return boolean - true on success otherwise false
	 */
	private function shutdown() {

		$this->rParser->shutdown();

		return true;
	}

	/**
	 * starts a parsing run / this includes multiple template processings
	 * 
	 * @param array $hooks - the hooks for this run
	 * 
	 * @return boolean
	 */
	private function start( array $hooks = array() ) {

		// start the rule parser
		$this->rParser->start( $hooks );

		// finish with the processing state
		$this->processing = true;

		return true;
	}
}

//_____________________________________________________________________________________________
// pre initialize
\Templax\Templax::boot();

//_____________________________________________________________________________________________
//