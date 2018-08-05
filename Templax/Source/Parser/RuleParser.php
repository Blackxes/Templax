<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	parses the given raw rule and returns a rule instance
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Parser;

use \Templax\Source\Models;

require_once ( TEMPLAX_ROOT ."/Source/Models/Rule.php" );

//_____________________________________________________________________________________________
class RuleParser {

	static private $ruleIterator = 0;

	//_____________________________________________________________________________________________
	// no construction of this class is needed
	private function __construct() {}

	//__________________________________________________________________________________________
	// parses the given raw rule into a rule instance thats being used later on
	//
	// param1 (\Templax\Source\Models\Process) expects the template process
	// param2 (string) expects the raw matched rule
	//
	// return \Templax\Models\Rule
	//
	static public function parse( Models\Process &$process, string $rawRule ): Models\Rule {

		$config = $GLOBALS["Templax"]["Configuration"];
		
		preg_match( $config["Regex"]["extractRequest"], $rawRule, $requestMatch );
		preg_match( $config["Regex"]["extractKey"], $rawRule, $keyMatch );
		
		$rule = new Models\Rule( ++self::$ruleIterator, $rawRule, $requestMatch[1], $keyMatch[1] );

		// check if there are inline options
		$queryMarkup = $process->getQueryMarkup();

		// the key has a higher prio than the request when it comes to selecting
		// the value from the markup
		$prioKey = (($keyMatch) ? $keyMatch[1] : $requestMatch[1]);
		$markupValue = $queryMarkup[ $prioKey ];
		$requestValue = $queryMarkup[ $prioKey ];
		$customOptions = array();

		if ( is_array($markupValue) ) {
			if ( $markupValue["_options"] && is_array($markupValue["_options"]) )
				$customOptions = $markupValue["_options"];
			
			if ( $markupValue["value"] !== null )
				$requestValue = $markupValue["value"];
		}

		$commandOptions = $config["Parsing"]["optionSets"][ $requestMatch[1] ];

		$options = array_merge(
			$config["Parsing"]["optionSets"]["default"]["rule"],
			($commandOptions) ? $commandOptions : array(),
			$customOptions
		);

		// process values of the arrays which can be callables
		$rule->setOptions( self::resolveValues($options) );
		$rule->setValue( self::resolveValues($requestValue) );
		$rule->setCommandValue( self::resolveValues($queryMarkup[ $rule->getRequest() . "-" . $rule->getKey()]) );

		return $rule;
	}

	//_________________________________________________________________________________________
	// resolve values of keys into "real" values / baiscally when a function they are called
	// and the result overwrites the old value
	//
	// param1 (array|single mixed) expects the options
	//		either a set of values
	//		or a single value that will be checked when callable
	// param2 (mixed) expects the default return value when no value is given
	//
	// return array | mixed
	//
	static private function resolveValues( $values ) {

		if ( !$values ) return $values;
		
		if ( !is_array($values) ) $values = array( "__value" => $values );

		foreach( $values as $option => $value )
			if ( is_callable($value) )
				$values[$option] = $value();

		return ($values["__value"]) ? $values["__value"] : $values;
	}

	//_________________________________________________________________________________________
	// returns the current rule iteration count
	//
	// return int - the current iteration of the rules (the count)
	//
	static public function getRuleCount(): int {

		return self::$ruleIterator;
	}
}

//_____________________________________________________________________________________________
//