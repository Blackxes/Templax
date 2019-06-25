<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * contains several functionalities which doesnt belong somewhere specifically
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Classes;

//_____________________________________________________________________________________________
class Miscellaneous {

	/**
	 * removes an index from the array by reference and returns the removed index
	 * 
	 * @param array &$markup - the markup
	 * 
	 * @return array - the options
	 */
	static public function array_remove( array &$array, $key ) {

		// check if exists, copy and return item
		if ( array_key_exists($key, $array) ) {

			$var = $array[$key];

			unset( $array[$key] );

			return $var;
		}

		// else null..
		return null;
	}

	/**
	 * resolve the values when functions
	 * 
	 * @param array|mixed $values - the value
	 * 
	 * @return mixed - the resolved value(s)
	 */
	static public function resolveValues( $values ) {
		
		// when empty/ object or just null return the value
		if ( is_null($values) || empty($values) || is_object($value) )
			return $values;
		
		// when not array insert into one to resolve the value easier
		if ( !is_array($values) )
			$values = array( "__value" => $values );

		// resolve ..
		foreach( $values as $option => $value )
			if ( !is_string($value) && is_callable($value) )
				$values[$option] = $value();

		// return the array or the value stored temporary in the array
		return ( isset($values["__value"]) ) ? $values["__value"] : $values;
	}
}

//_____________________________________________________________________________________________
//