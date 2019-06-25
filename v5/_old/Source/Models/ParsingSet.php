<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * parsing set model
 * information set for the template processor function to work with
 * 
 * it contains the fundamental values to complete a template parsing without errors
 * 
 * when this set is invalid - the processing doesnt start
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Classes/ParameterBag.php" );
require_once( TEMPLAX_ROOT . "/Source/Models/BaseProcessSet.php" );

use \Templax\Source\Classes;

//_____________________________________________________________________________________________
class ParsingSet extends namespace\BaseProcessSet {

	/**
	 * construction
	 * 
	 * @param \Templax\Source\Models\ParsingSet|\Templax\Source\Models\Template|string $source
	 * 	ParsingSet - the parsing set this instance adapts
	 * 	Template - a template instance
	 * 	string - template id or value / when id not found in the manager value is interpreted as template value
	 * @param array|null $markup - the markup for this set
	 * @param array|null $options - the options for this set
	 */
	public function __construct( $source, array $markup = array(), array $options = array(), namespace\Process $parentProcess = null ) {

		// use source when its a parsingset else just normally initialize
		parent::__construct();

		if ( is_a($source, __NAMESPACE__ . "\ParsingSet") )
			$this->merge( null, $source->all() );

		else {

			$this->merge( null, array(
				"template" => $this->buildSource($source),
				"markup" => new Classes\ParameterBag( (array) $markup ),
				"options" => new Classes\ParameterBag( (array) $options ),
				"parentProcess" => $parentProcess
			));
		}
	}

	/**
	 * validates the source of this parsing set
	 * 
	 * @return boolean - true on valid otherwise false
	 */
	private function buildSource( $source ) {

		// simple null check
		if ( is_null($source) )
			return null;

		// check if its a template instance
		if ( !is_string($source) && is_a($source, "\Templax\Source\Models\Template") )
			return $source;
		
		// if its not empty and registered / get the template
		else if ( \Templax\Templax::$instance->has($source) )
			return \Templax\Templax::$instance->get($source );

		// otherwise create a new template with the source as value
		return new namespace\Template( null, $source );
	}

	/**
	 * validates this set and returns true on valid otherwise false
	 * 
	 * @return boolean - true on valid otherwise false
	 */
	public function verify() {

		$template = $this->get( "template" );
		
		// it only depends on the source wether this set is valid or not
		return !is_null( $template ) || is_a( $template, "\Templax\Source\Models\Template" ) || !$template->verify();
	}
}

//_____________________________________________________________________________________________
//