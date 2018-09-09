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
	private $process;

	/**
	 * the context from the template for the current rule
	 * 
	 * @var string
	 */
	private $context;

	/**
	 * post querry
	 * 
	 * @var \Templax\Source\Models\Query
	 */
	private $postQuery;

	/**
	 * the template context of the current query
	 * 
	 * @var null|string
	 */
	// private $queryContext;

	/**
	 * construction
	 * 
	 * @param \Templax\Source\Models\Process $process - current process
	 * @param \Templax\Source\Models\Rule $rule - the rule
	 * @param string $context - the context from the template for the current rule
	 * @param \Templax\Source\Models\Query $postQuery - post query
	 */
	public function __construct(
		namespace\Process $process,
		namespace\Rule $rule,
		string $context,
		namespace\Query $postQuery = null )
	{		
		// parent "rule" construction
		parent::__construct( $rule->getId(), $rule->getRawRule(), $rule->getRequest(), $rule->getKey(), $rule->getMarkup(), $rule->getOptions() );
		$this->value = $rule->getValue();

		// query constructions
		$this->process = $process;
		$this->context = $context;
		$this->postQuery = $postQuery;
		// $this->queryContext = "";
	}

	/**
	 * returns the process
	 * 
	 * @return \Templax\Source\Models\Process
	 */
	public function getProcess() {

		return $this->process;
	}

	/**
	 * returns the template context
	 * 
	 * @return string
	 */
	public function getContext() {

		return $this->context;
	}

	/**
	 * returns the post querry - null when no query is defined
	 * 
	 * @return \Templax\Source\Models\null
	 */
	public function getPostQuery() {

		return $this->postQuery;
	}

	/**
	 * returns the queryContext
	 */
}

//_____________________________________________________________________________________________
//