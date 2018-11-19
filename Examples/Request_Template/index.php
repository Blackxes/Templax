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
			"base" => __DIR__ . "/index.html",
			"placeholder"  => __DIR__ . "/placeholder.html",
		)
	);

	$markup = array(
		"placeholder" => array(
			"marker" => "Text defined for the placeholder template"
		)
	);

	$hooks = array(
		"placeholder_placeholder_marker" => "Wusa"
	);

	$content = $parser->parse( "base", $markup, $hooks );
	echo $content;

//_____________________________________________________________________________________________
//