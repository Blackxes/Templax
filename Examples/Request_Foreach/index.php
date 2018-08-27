<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	example of basic marker usage
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

require_once ( "../../Dependencies/Logfile/Logfile.php" );
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
			array(
				"label" => "banana",
				"variants" => array(
					array( "value" => "tasty" ),
					array( "value" => "horrible" )
				)
			),
			array( "label" => "apple" ),
			array( "label" => "citrus" )
		)
	);

	$hooks = array(
		"base_page-title" => "Neuer Titel"
	);

	$content = \Templax\Templax::parse( "base", $markup, $hooks );
	echo $content;

//_____________________________________________________________________________________________
//