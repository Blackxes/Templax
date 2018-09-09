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
		"page-title" => "Request Case",
		"image" => "http://via.placeholder.com/120x120",
		"firstname" => "Alexander",
		"lastname" => "Bassov"
	);

	$hooks = array(
		"base_image" => "http://via.placeholder.com/120x125"
	);

	$content = \Templax\Templax::parse("base", $markup, $hooks );
	echo $content;

//_____________________________________________________________________________________________
//