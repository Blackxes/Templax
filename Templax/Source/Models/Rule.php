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

use \Templax\Source;

require_once( TEMPLAX_ROOT . "/Source/Miscellaneous.php" );

//_____________________________________________________________________________________________
class Rule extends namespace\BaseProcessSet {

	/**
	 * rule id
	 * 
	 * @var int
	 */
	protected $id;

	/**
	 * contains the key - always in combination with the request
	 * 
	 * @var string|null
	 */
	protected $key;

	/**
	 * the rule thats been extracted from the template
	 * 
	 * @var string
	 */
	protected $rawRule;

	/**
	 * contains the request from this rule
	 * null when no request is found
	 * 
	 * @var string|null
	 */
	protected $request;

	/**
	 * rule signature
	 * 
	 * @var string
	 */
	protected $signature;

	/**
	 * the value of this rule
	 * 
	 * @var mixed
	 */
	protected $value;

	/**
	 * construction
	 * 
	 * @param int $id - the rule id
	 * @param string $rawRule - the raw extracted rule
	 * @param string|null $request - the request from the rule
	 * @param string|null $key - the key - always in combination with a command
	 */
	public function __construct( int $id, string $rawRule, ?string $request, ?string $key, array $markup = array(), array $options = array() ) {

		parent::__construct( $markup, $options );

		$this->id = $id;
		$this->key = $key;
		$this->rawRule = $rawRule;
		$this->request = $request;
	}

	/**
	 * returns the command value
	 * 
	 * @return mixed|null - null when no command value is present
	 */
	public function getCommandValue() {

		if ( is_null($this->request) || is_null($this->key) )
			return "";
		
		return$this->markup[ $this->request . "-" . $this->key ];
	}

	/**
	 * returns the id
	 */
	public function getId() {

		return $this->id;
	}

	/**
	 * returns the key
	 * 
	 * @return string
	 */
	public function getKey() {

		return $this->key;
	}

	/**
	 * returns the requested option
	 * 
	 * @return mixed
	 */
	public function getOption( $option ) {

		return $this->options[ $option ];
	}

	/**
	 * returns all options 
	 * 
	 * @return array
	 */
	public function getOptions() {

		return $this->options;
	}

	/**
	 * returns the priority key of this rule
	 * 
	 * @return string|null - null when the key nor the request is given
	 */
	public function getPrioKey() {

		return ( !is_null($this->key) ) ? $this->key : $this->request;
	}

	/**
	 * returns the raw rule
	 * 
	 * @return string
	 */
	public function getRawRule() {

		return $this->rawRule;
	}

	/**
	 * returns the request
	 * 
	 * @return string
	 */
	public function getRequest() {

		return $this->request;
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
		if ( !$rebuild && !is_null($this->signature) && !is_null($contextId) || is_null($process) )
			return $this->signature;

		// the signature is built out of a root template
		// the possible template id ( when not the root template itself )
		// and the prioKey separated by "_"
		$signature = array();
		
		// the root template
		$signature["root"] = $process->getNextRootTemplate()->getId();

		// include the query key when defined but from the parent
		// cause at this point no query is declared
		$parent = $process->getParent();

		// when parent exists add the parent
		if ( !is_null($parent) )
			$signature["parent"] = $process->getParent()->getCurrentQuery()->getKey();
		
		// the possible template id
		$tid = $process->getTemplate()->getId();

		// only when its not the root or empty
		if ( $tid != $signature["root"] && (string) $tid != "" )
			$signature["tid"] = $tid;
		
		// prio key
		$signature["prio"] = $this->getPrioKey();

		// include the context id but return before caching
		// because this is a different context / this is not the rule context
		// its the command context
		if ( !is_null($contextId) )
			return implode( "_", $signature ) . "_" . $contextId;

		// cache but not when the context id is given
		// its a special case
		$this->signature = implode( "_", $signature );
		
		return $this->signature;
	}

	/**
	 * returns the value
	 * 
	 * @return mixed
	 */
	public function getValue() {

		// when the value is defined return that one
		if ( !is_null($this->value) )
			return Source\Miscellaneous::resolveValues( $this->value );
		
		return Source\Miscellaneous::resolveValues( $this->markup[$this->getPrioKey()] );
	}

	/**
	 * sets the value
	 * 
	 * @var mixed
	 */
	public function setValue( $value ) {

		$this->value = $value;
	}
}

//_____________________________________________________________________________________________
//