<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	example of basic marker usage
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

// require_once ( "../../Dependencies/Logfile/Logfile.php" );
require_once ( "../../v4/Templax.php" );

//_____________________________________________________________________________________________

	echo '<pre>';

	// load template
	$parser = new \Templax\Templax();
	$parser->Init( array(
		"base" => array(
			"file" => __DIR__ . "/index.html",
			"markup" => array(
				"page-title" => "wusa"
			)
		)
	));

	$markup = array(
		"page-title" => "Basic Marker Use",
		"framework" => function() { return "Templax"; },
		"version" => $GLOBALS["Templax"]["General"]["Version"],
		"description" => "This framework is an adaption of the javascript template framework js_Templax but written in php"
	);

	$hooks = array(
		"base_page-title" => "Hook title"
	);

	$content = $parser->parse( "base", $markup, $hooks );

	echo '</pre>';
	echo $content;

//_____________________________________________________________________________________________
//