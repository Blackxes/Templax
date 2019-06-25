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

use \Templax\Source;
use \Templax\Source\Classes;

require_once( TEMPLAX_ROOT . "/Source/Classes/ParameterBag.php" );
require_once( TEMPLAX_ROOT . "/Source/Classes/Miscellaneous.php" );

//_____________________________________________________________________________________________
class Response extends Classes\ParameterBag {

	/**
	 * construction
	 * 
	 * @param string $replacement - the replacement
	 * @param mixed $value - the value
	 * @param \Templax\Source\Models\Query|null $query - the post query
	 * @param int|null $offset - the offset for the rule extraction iterator
	 */
	public function __construct( string $replacement = null, $value = "", namespace\Query $postQuery = null, int $indexOffset = null, array $dataCache = null ) {

		parent::__construct( array(
			"replacement" => $replacement,
			"value" => $value,
			"postQuery" => $postQuery,
			"indexOffset" => $indexOffset,
			"context" => "",
			"dataCache" => (is_null($dataCache)) ? null : new Classes\ParameterBag( $dataCache )
		));
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
		if ( $this->isNull("replacement") )
			$this->set( "replacement", $process->get(["currentQuery", "rawRule"]) );
		
		return $this;
	}

	/**
	 * value hook - returns the resolved value
	 * 
	 * @return mixed - the value
	 */
	public function value( $value ) {

		return (string) Classes\Miscellaneous::resolveValues( $value );
	}
}

//_____________________________________________________________________________________________
//