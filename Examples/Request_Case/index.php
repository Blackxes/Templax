<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	example of basic marker usage
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

require_once ( __DIR__ . "/../../v4/Templax.php" );

//_____________________________________________________________________________________________
	// parser instance
	$parser = new \Templax\Templax();

	// load template
	$parser->Init(
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

	$content = $parser->parse( "base", $markup, $hooks );
	echo $content;

//_____________________________________________________________________________________________
//