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
	 * construction
	 * 
	 * @param \Templax\Source\Models\Process $process - current process
	 * @param \Templax\Source\Models\Rule $rule - the rule
	 * @param string $context - the context from the template for the current rule
	 * @param boolean $isPostQuery - describes wether this query is a post query or regular one
	 */
	public function __construct( namespace\Process $process, namespace\Rule $rule, string $context, bool $isPostQuery = false ) {

		// parent "rule" construction
		parent::__construct( $rule->get("id"), $rule->get("rawRule"), $rule->get("request"), $rule->get("key"), $rule->all("markup"), $rule->all("options") );

		$this->set( "value", $rule->get("value") );

		$this->merge( null, array(
			"context" => $context,
			"process" => $process,
			"isPostQuery" => $isPostQuery,
			"dataCache" => $process->get("dataCache")
		));
	}
}

//_____________________________________________________________________________________________
//