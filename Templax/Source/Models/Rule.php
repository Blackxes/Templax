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

//_____________________________________________________________________________________________
class Rule {

	/**
	 * rule id
	 * 
	 * @var int
	 */
	public $id;

	/**
	 * the rule thats been extracted from the template
	 * 
	 * @var string
	 */
	public $rawRule;

	/**
	 * contains the request from this rule
	 * null when no request is found
	 * 
	 * @var string|null
	 */
	public $request;

	/**
	 * contains the key - always in combination with the request
	 * 
	 * @var string|null
	 */
	public $key;

	/**
	 * contains the value from the markup associated to the request
	 * 
	 * @var mixed
	 */
	public $value;

	/**
	 * when the request is a command this value contains the value/markup for the request
	 * 
	 * @var mixed
	 */
	public $commandValue;

	/**
	 * contains the options for this rule
	 * 
	 * @var array|null
	 */
	public $options;

	/**
	 * the prio key defines wether the request or the key value should be taken
	 * to extract the value from the markup
	 */
	public $prioKey;

	/**
	 * construction
	 * 
	 * @param int $id - the rule id
	 * @param string $rawRule - the raw extracted rule
	 * @param string|null $request - the request from the rule
	 * @param string|null $key - the key - always in combination with a command
	 */
	public function __construct( int $id, string $rawRule, ?string $request, ?string $key ) {

		$this->id = $id;
		$this->rawRule = $rawRule;
		$this->request = $request;
		$this->key = $key;
		
		$this->options = array();
	}

	/**
	 * returns an option
	 * 
	 * @return mixed
	 */
	public function getOption( $option ) {

		return $this->options[$option];
	}
}

//_____________________________________________________________________________________________
//