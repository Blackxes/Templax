<?php

/**********************************************************************************************
 * 
 * @File testing of the render engine Templax.
 * 
 * default test version is always the latest one. If you want to test a specific version
 * simply pass the "v" get parameter and it selects the correct version automatically
 * 
 * @Author: Alexander Bassov
 * 
**********************************************************************************************/

namespace TemplaxTest;

require_once( __DIR__ . "/Version.php" );

#----------------------------------------------------------------------------------------------
# remotely enable templax debugging
const TEMPLAX_ENABLE_DEBUGGING = true;
define( "TEMPLAX_LATEST_VERSION", \TemplaxTest\Version::latest(), true );

#----------------------------------------------------------------------------------------------
class TemplaxTest {
	
	/**
	 * construction
	 */
	public function __construct() {}
	
	/**
	 * runs a test on either a given or latest version
	 * 
	 * @param $v defines the testing version
	 * 	to change testing version either use the "v" get parameter
	 * 	or pass the version you want to test to this function
	 * 	prio order: parameter passing > get parameter
	 * 	when null the latest version is used
	 */
	public function run( string $v = null ) {
		
		$v = is_null($v) ? TEMPLAX_LATEST_VERSION : \TemplaxTest\Version::parseVersion( $v );

		if ( $v ) throw new \PrintfException( "unknown version %s. Possible versions %s", $v, \TemplaxTest\Version::versionRange() );

		$context = \TemplaxTest\Context::get( $v );

		$context->init();
	}
}