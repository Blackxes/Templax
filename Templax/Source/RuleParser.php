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
	public $hooks;

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

		$this->hooks = array();
		$this->baseOptions = $GLOBALS["Templax"]["Defaults"]["Rules"]["BaseOptions"];
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

		$config = $GLOBALS["Templax"]["ExtractionRegex"];
		
		preg_match( $config["extractRequest"], $rawRule, $requestMatch );
		preg_match( $config["extractKey"], $rawRule, $keyMatch );
		
		$rule = new Models\Rule( ++self::$rIterator, $rawRule, $requestMatch[1], $keyMatch[1] );

		// the prio key defines wether the key or the request shall be taken to gather the value from the markup
		// when a regular marker is defined the prio key is the request
		// but when a command is defined the key is the prio key
		$rule->prioKey = ( $keyMatch ) ? $keyMatch[1] : $requestMatch[1];
		
		// the value from the marker for the current rule
		$requestValue = $process->queryMarkup[ $rule->prioKey ];
		$ruleOptions = array();

		// when the value contains more than a simple value
		if ( is_array($requestValue) ) {

			// get the options for the this rule
			if ( isset($markupValue["_options"]) && is_array($markupValue["_options"]) )
				$ruleOptions = $markupValue["_options"];

			// get the actual value for this rule
			// and overwrite the request value
			//
			// This HAS to be the last operation in this scope
			// because the actual request array will be overwritten with the value
			if ( isset($markupValue["value"]) )
				$requestValue = $requestValue["value"];
		}

		// resolve possible callables within given values
		//
		// the prio list for everything that is overwritable is the following
		// templax -> user -> rule
		//
		$rule->options = $this->resolveValues(array_merge(
			$this->baseOptions,
			$process->options,
			$ruleOptions
		));

		// build the rule signature
		$signature = "";

		// when its a sub template than include the parent as a selector
		if ( $process->template->isSub )
			$signature .= $process->parent->query->prioKey . "_";
		
		$signature .= $process->template->id . "_";
		$signature .= $rule->prioKey;

		$rule->signature = $signature;

		// check for hooks
		$rule->value = ( isset($this->hooks[$rule->signature]) )
			? $this->resolveValues( $this->hooks[$rule->signature] )
			: $this->resolveValues( $requestValue );

		// command value
		$rule->commandValue = $this->resolveValues( $process->queryMarkup["{$rule->request}-{$rule->key}"] );

		return $rule;
	}

	/**
	 * resolve values into actual values
	 * the value can be callable and this function resolve the value out of it
	 * 
	 * @param mixed $values - the values that shall be resolved
	 * 
	 * @return mixed
	 */
	private function resolveValues( $values ) {

		if ( empty($values) )
			return $values;
		
		if ( !is_array($values) )
			$values = array( "__value" => $values );

		foreach( $values as $option => $value )
			if ( is_callable($value) )
				$values[$option] = $value();

		return ( isset($values["__value"]) ) ? $values["__value"] : $values;
	}

	/**
	 * returns the rule iterator
	 * 
	 * @return int
	 */
	public function getRuleIterator() {

		return self::$rIterator;
	}
}

//_____________________________________________________________________________________________
//