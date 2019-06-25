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
			"base" => array( "file" => __DIR__ . "/index.html", "marker" => "base" )
		)
	);

	$loginState = function() {
		return false;
	};
	
	$markup = array(
		"logged_in" => $loginState,
		"message-when-logged" => "Logged in",
		"message-when-not" => "Logged out"
	);

	$content = $parser->parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//