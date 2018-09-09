<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	example of basic marker usage
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

// require_once ( "../../Dependencies/Logfile/Logfile.php" );
require_once ( "../../Templax/Templax.php" );

//_____________________________________________________________________________________________

	// load template
	\Templax\Templax::Init(
		array(
			"base" => array( "file" => __DIR__ . "/index.html", "marker" => "base" ),
			"logged_in" => array( "file" => __DIR__ . "/index.html", "marker" => "logged_in" ),
			"logged_out" => array( "file" => __DIR__ . "/index.html", "marker" => "logged_out" )
		)
	);

	// the key "login-status" defines the template
	// change it back and forth between "logged_in" and "logged_out"
	// and the parser uses the template defined under the value
	//
	// when the template is not found an empty string is used
	//
	$markup = array(
		"login-status" => "logged_in",
		"templateSelect-login-status" => array(
			"user" => "Blackxes"
		)
	);

	$content = \Templax\Templax::parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//