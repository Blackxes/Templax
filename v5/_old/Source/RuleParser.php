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
use \Templax\Source\Classes;

//_____________________________________________________________________________________________
class RuleParser {

	/**
	 * the default options for a rule
	 * 
	 * @var array
	 */
	private $baseOptions;

	/**
	 * contains the hooks
	 * 
	 * @var array
	 */
	private $hooks;

	/**
	 * describes the initialization state of the parser
	 * 
	 * @var boolean
	 */
	private $initialized;

	/**
	 * internal rule iterator
	 * @see ProcessManager::$pIterator for more info
	 * 
	 * @var int
	 */
	static private $rIterator = 0;

	/**
	 * construction
	 */
	public function __construct() {

		$this->hooks = new Classes\ParameterBag();
		$this->baseOptions = $GLOBALS["Templax"]["Defaults"]["Rules"]["BaseOptions"];
	}
	
	/**
	 * this returns the value of a hook but respects the context of the request
	 * say we have this rule {{ foreach: fruits }}..{{ foreach end: fruits }}
	 * 
	 * this rule has 3 level of contexts. First is the whole fruits command.
	 * Second is the item and the third the markup behind the item.
	 * 
	 * This function provides hook access to the 2 level because this is
	 * where the command is defined. The rule parser cant access this level naturally
	 * because it is not a rule.
	 * Its a step inbetween where the id of the item is defined.
	 * Such as 0 as an index or a given id for the markup from the user.
	 * 
	 * In order to be able to overwrite the markup as a whole this function
	 * check for that hook signature and returns its result.
	 * 
	 * @param \Templax\Source\Models\Query $query - current query
	 * @param string $contextId - the top level command context id
	 * 	for example the index "0" in a foreach markup
	 * @param mixed &$backup - this value is returned when no hook is found
	 * 	because the hook might contain any value there is no way to check for null
	 * 	or false therefore this backup
	 * 
	 * @return mixed - either the hook or the given value
	 * 
	 */
	public function getHookValueFromCommandContext( Models\Query $query, string $contextId, $backup ) {
		
		// get command signature ..
		$signature = $query->getSignature( $query->get("process"), null, $contextId );
		
		// when exist return hook value
		return ( $this->hasHook($signature) ) ? $this->getHook( $signature ) : $backup;
	}

	/**
	 * returns the requested hook
	 * 
	 * @return mixed|null - null when hook does not exists
	 */
	public function getHook( string $signature ) {

		return $this->hooks[ $signature ];
	}

	/**
	 * returns all hooks
	 * 
	 * @return array
	 */
	public function getHooks() {

		return $this->hooks;
	}

	/**
	 * returns the rule iterator
	 * 
	 * @return int
	 */
	public function getRuleIterator() {

		return self::$rIterator;
	}

	/**
	 * returns the existance of a hook as boolean
	 * 
	 * @return boolean - true when exists else false
	 */
	public function hasHook( string $signature ) {

		return isset( $this->hooks[$signature] );
	}

	/**
	 * parses the given raw rule and returns a rule instance
	 * 
	 * @param \Templax\Source\Models\Process $process - the current process
	 * @param string $rawRule - the raw rule
	 * 
	 * @return \Templax\Source\Models\Rule
	 */
	public function parse( Models\Process $process, string $rawRule ) {
		
		// extract value from raw rule
		$config = $GLOBALS["Templax"]["ExtractionRegex"];
		
		preg_match( $config["ExtractRequest"], $rawRule, $requestMatch );
		preg_match( $config["ExtractKey"], $rawRule, $keyMatch );

		// initial rule
		$rule = new Models\Rule(
			self::$rIterator++, $rawRule,
			$requestMatch[1], $keyMatch[1],
			$process->all("markup"), $process->all("options")
		);

		// when value an array parse its configuration
		// this value possibly get overwritten when the current value
		// in an array and contains the "value" key
		$value = $rule->get("value");

		// parse the config when array
		if ( is_array($value) ) {

			// extract options
			if ( isset($value["_options"]) )
				$rule->merge( "options", (array) $markupValue["_options"] );
			
			// define the value
			if ( isset($value["value"]) )
				$value = $value["value"];
		}
		
		// apply base values
		$rule->rMerge("options", Classes\Miscellaneous::resolveValues($this->baseOptions) );

		// check hooks
		if ( isset($this->hooks[$rule->getSignature($process)]) )
			$rule->set("value", Classes\Miscellaneous::resolveValues($this->hooks[$rule->getSignature($process)]) );

		return $rule;
	}

	/**
	 * shuts the parser down and resets everything
	 * 
	 * @return boolean - true on success otherwise false
	 */
	public function shutdown() {

		$this->hooks = array();

		return true;
	}

	/**
	 * prepares the rule parser for a template processing runs
	 * 
	 * @param array $hooks - the hooks
	 * 
	 * @return boolean
	 */
	public function start( array $hooks = array() )  {

		$this->hooks = $hooks;

		return true;
	}
}

//_____________________________________________________________________________________________
//