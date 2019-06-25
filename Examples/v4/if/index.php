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
		"is_offense" => false,
		"is_defense" => true,
		"damage" => "30 Attack",
		"armor" => "25 Armor"
	);

	$content = $parser->parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//