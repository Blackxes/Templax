<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * process model
 * 
 * @author Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class Process {

	/**
	 * the process id
	 * 
	 * @var int
	 */
	public $id;

	/**
	 * current template
	 * 
	 * @var \Templax\Source\Models\Template
	 */
	public $template;

	/**
	 * the user defined markup for this template
	 * 
	 * @var array|null
	 */
	public $userMarkup;

	/**
	 * the markup that will be used in the query
	 * this one is merged with some extra marker and the user markup
	 * 
	 * @var array
	 */
	public $queryMarkup = array();

	/**
	 * process options
	 * 
	 * @var array
	 */
	public $options = array();

	/**
	 * current query
	 * 
	 * @var \Templax\Source\Models\Query
	 */
	public $query;

	/**
	 * describes wether this process is a main process
	 * main processes are the ones who contains the initial template
	 * the one the user requested / not the subprocesses that follows afterwards
	 */
	public $isMainProcess = false;

	/**
	 * contains values for keys within the markup
	 * when the template processing reaches a rule signature
	 * that matches one of these hooks
	 * the value of this hook will be used for the further processing of "this" rule
	 * 
	 * but only on the level below the rule including the rule itself
	 * 
	 * @var array
	 */
	public $hooks;

	/**
	 * the parent process of this one
	 * 
	 * @var \Templax\Source\Models\Process|null
	 */
	public $parent;
	
	/**
	 * construction
	 * 
	 * Todo: complete function header
	 */
	public function __construct( int $id, namespace\ParsingSet $set, int $parent = null )
	{
		$this->id = $id;
		$this->template = $set->source;
		$this->userMarkup = $set->markup;
		$this->queryMarkup = array();
		$this->options = $set->options;
		$this->hooks = $set->hooks;

		$this->parent = null;
	}

	/**
	 * returns the value of a hook when defined else null
	 * 
	 * @param string $ruleSignature - the rule signature
	 * 
	 * @return mixed|null
	 */
	public function getHook( string $ruleSignature ) {

		if ( isset($this->hooks[$ruleSignature]) )
			return $this->hooks[$ruleSignature];
		
		return null;
	}

	/**
	 * returns the value of a option
	 * 
	 * @return mixed
	 */
	public function getOption( $option ) {

		return $this->options[ $option ];
	}
}

//_____________________________________________________________________________________________
//