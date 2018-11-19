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
		)
	);

	$markup = array(
		"offense" => null,
		"defense" => true,
		"if-defense" => array(
			"health" => "450",
		),
		"if-offense" => array(
			"damage" => 30
		),
		
	);

	$content = $parser->parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//