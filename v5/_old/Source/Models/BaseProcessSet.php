<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * contains the member that goes along for nearly the whole process time of a template
 * including functionalities
 * 
 * @author Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

use \Templax\Source\Classes;

require_once( TEMPLAX_ROOT . "/Source/Classes/ParameterBag.php" );

//_____________________________________________________________________________________________
class BaseProcessSet extends Classes\ParameterBag {

	/**
	 * construction
	 * 
	 * @param array $markup - the markup for this set
	 * @param array $options - the options for this set
	 */
	public function __construct( array $markup = array(), array $options = array() ) {

		parent::__construct( array(
			"markup" => new Classes\ParameterBag( $markup ),
			"options" => new Classes\ParameterBag( $options )
		));
	}
}

//_____________________________________________________________________________________________
//