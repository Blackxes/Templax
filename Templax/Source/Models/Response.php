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
	 * contains the value the str_replace uses to replace the rule with the associated value
	 * 
	 * @var string
	 */
	public $replacement;

	/**
	 * contains the value that replaces the replacement
	 * 
	 * @var mixed
	 */
	public $value;

	/**
	 * contains the post query
	 * 
	 * @var null|\Templax\Source\Models\Query
	 */
	public $postQuery;

	/**
	 * contains the offset for the rule extraction iterator
	 * when null the offset is calculated automatically
	 * 
	 * @var null|int
	*/
	public $indexOffset;

	/**
	 * the current substring of the template this response belongs to
	 * 
	 * @var string
	 */
	public $context;

	/**
	 * construction
	 * 
	 * @param string $replacement - the replacement
	 * @param mixed $value - the rule
	 * @param null|\Templax\Source\Models\Query $query - the post query
	 * @param null|int $offset - the offset for the rule extraction iterator
	 */
	public function __construct( ?string $replacement, $value = "", namespace\Query $query = null, int $offset = null ) {

		$this->replacement = $replacement;
		$this->query = $query;
		$this->indexOffset = $offset;

		// cast the value into string when not but respect arrays and objects
		if ( !is_string($value) ) {

			$this->value = ( !is_object($value) && !is_array($value) )
				? (string) $value
				: "";
		}
		else
			$this->value = $value;
	}

	/**
	 * returns the definition of an offset as boolean
	 * 
	 * @return boolean
	 */
	public function hasOffset() {

		return !is_null($this->indexOffset);
	}
}

//_____________________________________________________________________________________________
//