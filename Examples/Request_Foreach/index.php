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
			"base" => __DIR__ . "/index.html"
		)
	);

	$markup = array(
		"page-title" => "Foreach request",
		"fruits" => array(
			"banana" => array( "label" => "banana" ),
			"apple" => array( "label" => "apple" ),
			"citrus" => array( "label" => "citrus" )
		)
	);

	$hooks = array(
		// "base_fruits" => "Wow",
		// "base_fruits_banana" => array( "wusa" => "wow" ),
		"base_fruits_banana_wusa" => "Deep Banana",
	);

	$content = \Templax\Templax::parse( "base", $markup, $hooks );
	echo $content;

//_____________________________________________________________________________________________
//