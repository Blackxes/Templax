<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * response model
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class Response {

	/**
	 * the current substring of the template this response belongs to
	 * 
	 * @var string
	 */
	private $context;

	/**
	 * contains the offset for the rule extraction iterator
	 * when null the offset is calculated automatically
	 * 
	 * @var null|int
	 */
	private $indexOffset;

	/**
	 * contains the post query
	 * 
	 * @var null|\Templax\Source\Models\Query
	 */
	private $postQuery;

	/**
	 * contains the value the str_replace uses to replace the rule with the associated value
	 * 
	 * @var string
	 */
	private $replacement;

	/**
	 * contains the value that replaces the replacement
	 * 
	 * @var mixed
	 */
	private $value;

	/**
	 * construction
	 * 
	 * @param string $replacement - the replacement
	 * @param mixed $value - the value
	 * @param \Templax\Source\Models\Query|null $query - the post query
	 * @param int|null $offset - the offset for the rule extraction iterator
	 */
	public function __construct( string $replacement = null, $value = "", namespace\Query $postQuery = null, int $offset = null ) {

		$this->indexOffset = $offset;
		$this->postQuery = $postQuery;
		$this->replacement = $replacement;
		$this->value = $value;

		$this->resolve();
	}

	/**
	 * returns the context
	 * 
	 * @return string
	 */
	public function getContext() {

		return $this->context;
	}

	/**
	 * returns the index offset
	 * 
	 * @return int|null
	 */
	public function getIndexOffset() {

		return $this->indexOffset;
	}

	/**
	 * returns the post query
	 * 
	 * @return \Templax\Source\Models\Query|null - null on no query
	 */
	public function getPostQuery() {

		return $this->postQuery;
	}

	/**
	 * returns the post query as a reference
	 * 
	 * @return \Templax\Source\Models\Query|null
	 */
	public function &getPostQueryRef() {

		return $this->postQuery;
	}

	/**
	 * returns the replacement
	 * 
	 * @return string
	 */
	public function getReplacement() {

		return $this->replacement;
	}

	/**
	 * returns the value
	 * 
	 * @return string|null
	 */
	public function getValue() {

		return $this->value;
	}

	/**
	 * returns the definition of an offset as boolean
	 * 
	 * @return boolean
	 */
	public function hasOffset() {

		return !is_null($this->indexOffset);
	}

	/**
	 * sets the context
	 * 
	 * @param string $context - the context
	 * 
	 * @return $this;
	 */
	public function setContext( string $context ) {

		$this->context = $context;

		return $this;
	}

	/**
	 * resolves the value into a string
	 * functions get invoked and every other value is casted into a string
	 * 
	 * @return true
	 */
	public function resolve() {

		$this->value = (string) ( is_callable($this->value) ) ? call_user_func($this->value) : $this->value;

		return true;
	}


	/**
	 * reviews this response and replaces in valid values 
	 * 
	 * @param \Templax\Source\Models\Process $process - the current process
	 * 
	 * @return $this
	 */
	public function review( namespace\Process &$process ) {

		// when no replacement is given use the rule itself
		if ( is_null($this->replacement) )
			$this->replacement = $process->getCurrentQuery()->getRawRule();
		
		return $this;
	}
}

//_____________________________________________________________________________________________
//