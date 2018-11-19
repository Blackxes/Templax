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
		"page-title" => "Foreach request",
		"fruits" => array(
			"banana" => array( "label" => "banana" ),
			"apple" => array( "label" => "apple" ),
			"citrus" => array( "label" => "citrus" )
		)
	);

	$hooks = array(
		// "base_fruits" => "Wow",
		// "base_fruits_banana" => array( "wow" => "ss" ),
		"base_fruits_banana_label" => "Deep Banana",
	);

	$content = $parser->parse( "base", $markup, $hooks );
	echo $content;

//_____________________________________________________________________________________________
//