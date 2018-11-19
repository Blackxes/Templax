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
	 * defines wether this query is a post query
	 */
	private $isPostQuery;

	/**
	 * post query data to pass data through rule parsing
	 * 
	 * @var null|string
	 */
	static private $cacheData;

	/**
	 * construction
	 * 
	 * @param \Templax\Source\Models\Process $process - current process
	 * @param \Templax\Source\Models\Rule $rule - the rule
	 * @param string $context - the context from the template for the current rule
	 * @param \Templax\Source\Models\Query $postQuery - post query
	 */
	public function __construct( namespace\Process $process, namespace\Rule $rule, string $context, $isPostQuery = false ) {		

		// parent "rule" construction
		parent::__construct( $rule->getId(), $rule->getRawRule(), $rule->getRequest(), $rule->getKey(), $rule->getMarkup(), $rule->getOptions() );
		$this->value = $rule->getValue();

		// query constructions
		$this->process = $process;
		$this->context = $context;
		$this->isPostQuery = $isPostQuery;
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
	public function isPostQuery() {

		return $this->isPostQuery;
	}

	/**
	 * defines the post query state
	 * 
	 * @param boolean $state - the state
	 * 
	 * @return $this
	 */
	public function setIsPostQuery( bool $state ) {

		$this->isPostQuery = $state;

		return $this;
	}

	/**
	 * sets cache data
	 * 
	 * @param string $key - the cache key
	 * @param mixed|!object - the value
	 * 
	 * @return $this
	 */
	public function setCacheData( string $key, $value ) {

		$this->cacheData[ $key ] = $value;

		return $this;
	}

	/**
	 * returns requested cache data
	 * 
	 * @param string $key - the cache data key
	 * 
	 * @return mixed|null - the value or null when not found
	 */
	public function getCacheData( string $key = "" ) {

		if ( empty($key) )
			return $this->cacheData;

		return $this->cacheData[ $key ];
	}
}

//_____________________________________________________________________________________________
//