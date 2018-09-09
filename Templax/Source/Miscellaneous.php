<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * contains several functionalities which doesnt belong somewhere specifically
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source;

//_____________________________________________________________________________________________
class Miscellaneous {

	/**
	 * resolve the values when functions
	 * 
	 * @param array|mixed $values - the value
	 */
	static public function resolveValues( $values ) {
		
		// when empty/ object or just null return the value
		if ( is_null($values) || empty($value) || is_object($value) )
			return $values;
		
		// when not array insert into one to resolve the value easier
		if ( !is_array($values) )
			$values = array( "__value" => $values );

		// resolve ..
		foreach( $values as $option => $value )
			if ( is_callable($value) )
				$values[$option] = $value();

		// return the array or the value stored temporary in the array
		return ( isset($values["__value"]) ) ? $values["__value"] : $values;
	}
}

//_____________________________________________________________________________________________
//