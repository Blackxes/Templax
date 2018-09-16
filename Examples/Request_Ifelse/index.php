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
			"base" => array( "file" => __DIR__ . "/index.html", "marker" => "base" )
		)
	);
	
	$markup = array(
		"elseif-login-state" => array(
			"if" => array(
				
			),
			"else" => array(

			)
		),
		"login-state" => array(
		)
	);

	$content = \Templax\Templax::parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//