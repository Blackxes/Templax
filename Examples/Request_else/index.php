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
		return null;
	};
	
	$markup = array(
		"if-login-state" => array(
			"message" => "ifstate"
		),
		"else-login-state" => array(
			"message" => "elsestate"
		),
		"login-state" => $loginState
	);

	$content = $parser->parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//