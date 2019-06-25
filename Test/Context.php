<?php

/**********************************************************************************************
 * 
 * @File returns the correct engine context
 * when version 4 is requested this class returns the class scope of it
 * 
 * @Author: Alexander Bassov
 * 
**********************************************************************************************/

namespace TemplaxTest;

class Context {

	/**
	 * construction
	 */
	private function __construct() {}
	
	/**
	 * returns the correct context of the requested testing
	 */
	static public function get( string $v ) {
	}
}