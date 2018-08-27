<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * query model
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Models/Rule.php" );

//_____________________________________________________________________________________________
class Query extends namespace\Rule {

	/**
	 * current process
	 * 
	 * @var \Templax\Source\Models\Process
	 */
	public $process;

	/**
	 * the context from the template for the current rule
	 * 
	 * @var string
	 */
	public $template;

	/**
	 * post querry
	 * 
	 * @var \Templax\Source\Models\Query
	 */
	public $postQuery;

	/**
	 * the template context of the current query
	 * 
	 * @var null|string
	 */
	public $context;

	/**
	 * construction
	 * 
	 * @param \Templax\Source\Models\Process $process - current process
	 * @param \Templax\Source\Models\Rule $rule - the rule
	 * @param string $template - the context from the template for the current rule
	 * @param \Templax\Source\Models\Query $postQuery - post query
	 */
	public function __construct( namespace\Process $process, namespace\Rule $rule,
		string $template, ?namespace\Query $postQuery
	)
	{
		$this->id = $rule->id;
		$this->rawRule = $rule->rawRule;
		$this->request = $rule->request;
		$this->key = $rule->key;
		$this->value = $rule->value;
		$this->commandValue = $rule->commandValue;
		$this->options = $rule->options;
		$this->prioKey = $rule->prioKey;

		$this->process = $process;
		$this->template = $template;
		$this->postQuery = $postQuery;
		$this->context = "";
	}
}

//_____________________________________________________________________________________________
//