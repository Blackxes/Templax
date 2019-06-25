<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * rule model
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Models/BaseProcessSet.php" );
require_once( TEMPLAX_ROOT . "/Source/Classes/Miscellaneous.php" );

use \Templax\Source\Classes;

//_____________________________________________________________________________________________
class Rule extends namespace\BaseProcessSet {

	/**
	 * construction
	 * 
	 * @param int $id - the rule id
	 * @param string $rawRule - the raw extracted rule
	 * @param string|null $request - the request from the rule
	 * @param string|null $key - the key - always in combination with a command
	 * @param array $markup - current markup
	 * @param array $options - rule options
	 */
	public function __construct( int $id, string $rawRule, ?string $request, ?string $key, array $markup = array(), array $options = array() ) {

		parent::__construct();
		
		$this->merge( null, array(
			"id" => $id,
			"key" => $key,
			"rawRule" => $rawRule,
			"request" => $request,
			"markup" => new Classes\ParameterBag( $markup ),
			"options" => new Classes\ParameterBag( $options ),
			"signature" => null,
			
			"commandValue" => null,
			"commandSignature" => null,
			"prioKey" => null,
			"value" => null
		));
	}

	/**
	 * returns the command value
	 * 
	 * @return mixed|null - null when no command value is present
	 */
	public function commandValue() {
		
		return $this->get(["markup", $this->get("commandSignature") ]);
	}

	/**
	 * returns the command signature
	 * under which key the value for the commands lies
	 */
	public function commandSignature() {

		if ( $this->isNull("request") || $this->isNull("key") )
			return "";

		return $this->get( "request" ) . "-" . $this->get( "key" );
	}

	/**
	 * returns the priority key of this rule
	 * 
	 * @return string|null - null when the key nor the request is given
	 */
	public function prioKey() {
		
		return ( !$this->isNull("key") ) ? $this->get( "key" ) : $this->get( "request" );
	}

	/**
	 * returns the signature of this rule based on the given template
	 * 
	 * @param \Templax\Source\Models\Process $process - the current process
	 * @param bool $rebuild - on true forces the rule to rebuild the signature
	 * @param string $contextId - when defined this id is used instead of the prioKey
	 * 	resulting in a different signature this rule can check for a different hook
	 * 	@see \Templax\Source\RuleParser::getHookValueFromCommandContext( ... )
	 * 
	 * @return string
	 */
	public function getSignature( namespace\Process &$process = null, bool $rebuild = null, string $contextId = null ) {

		// return cached signature when not forced to rebuild
		// or when no process is passed simply return this signature because we have nothing else
		if ( !$rebuild && !$this->isNull( "signature" ) && !is_null($contextId) || is_null($process) )
			return $this->get( "signature" );

		// the signature is built out of a root template
		// the possible template id ( when not the root template itself )
		// and the prioKey separated by "_"
		$signature = array();
		
		// the root template
		$signature["root"] = $process->getNextRootTemplate()->get( "id" );

		// include the query key when defined but from the parent
		// cause at this point no query is declared
		$parent = $process->get( "parentProcess" );

		// when parent exists add the parent
		if ( !$process->isNull( "parentProcess") )
			$signature["parent"] = $parent->get([ "currentQuery", "key" ]);
		
		// the possible template id
		$tid = $process->get( ["template", "id"] );

		// only when its not the root or empty
		if ( $tid != $signature["root"] && (string) $tid != "" )
			$signature["tid"] = $tid;
		
		// prio key
		$signature["prio"] = $this->get( "prioKey" );

		// include the context id but return before caching
		// because this is a different context / this is not the rule context
		// its the command context
		if ( !is_null($contextId) )
			return implode( "_", $signature ) . "_" . $contextId;

		// cache but not when the context id is given
		// its a special case
		$this->set( "signature" , implode("_", $signature) );
		
		return $this->get( "signature" );
	}

	/**
	 * returns the value
	 * 
	 * @return mixed
	 */
	public function value( $value ) {
		
		// when the value is defined return that one
		if ( !is_null($value) )
			return Classes\Miscellaneous::resolveValues( $value );

		return Classes\Miscellaneous::resolveValues( $this->get(["markup", $this->get("prioKey")]) );
	}
}

//_____________________________________________________________________________________________
//