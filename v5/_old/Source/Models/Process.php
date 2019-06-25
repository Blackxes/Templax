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

require_once( TEMPLAX_ROOT . "/Source/Classes/ParameterBag.php" );
require_once( TEMPLAX_ROOT . "/Source/Models/ParsingSet.php" );

use \Templax\Source\Classes;

//_____________________________________________________________________________________________
class Process extends namespace\ParsingSet {
	
	/**
	 * construction
	 * 
	 * @param int $id - process id
	 * @param \Templax\Source\Models\ParsingSet $set - the set for the template processing
	 * @param boolean $isMainProcess - describes wether this process is the main process or not
	 * @param int $scopeLevel - describes the current scope level. How "deep" is this process?
	 */
	public function __construct( int $id, namespace\ParsingSet $pSet, bool $isMainProcess = false, int $scopeLevel = 0, array $dataCache = array() ) {

		parent::__construct( $pSet );

		$this->merge( null, array(
			"id" => $id,
			"currentQuery" => null,
			"isMainProcess" => $isMainProcess,
			"scopeLevel" => $scopeLevel,
			"dataCache" => new Classes\ParameterBag( $dataCache )
		));
		
		// set options and markup separately
		$templateMarkup = $this->get([ "template", "markup" ]);

		$this->rMerge( "markup", $templateMarkup->all() );
		$this->rMerge( "options", $GLOBALS["Templax"]["Defaults"]["Process"]["BaseOptions"] );
	}

	/**
	 * returns the next template of the parents or even this one
	 * which have a valid template id and serve as a root template
	 * 
	 * @return \Templax\Source\Models\Template
	 */
	public function getNextRootTemplate() {

		// when no parent exists or this template is a root itself
		if ( $this->isNull("parentProcess") || \Templax\Templax::$instance->has($this->get(["template", "id"])) )
			return $this->get("template");
		
		// or when valid get the parents template
		else if ( !$this->get(["parentProcess", "template"])->isSub() )
			return $this->get(["parentProcess", "template"]);
		
		// one of the parents has a valid template.. so go for it
		return $this->get("parentProcess")->getNextRootTemplate();
	}
}

//_____________________________________________________________________________________________
//