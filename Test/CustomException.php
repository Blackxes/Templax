<?php

/**********************************************************************************************
 * 
 * @File overrides the core exception class and makes it a exception printf
 * 
 * @Author: Alexander Bassov
 * 
**********************************************************************************************/

class PrintfException extends \Exception {

	/**
	 * construction
	 */
	public function __construct( string $format, ...$args ) {

		parent::__construct( printf($format, $args) );
	}
}
