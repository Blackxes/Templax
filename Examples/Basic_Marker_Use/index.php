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
	\Templax\Templax::Init( array("base" => __DIR__ . "/index.html") );

	$markup = array(
		"page-title" => "Basic Marker Use",
		"framework" => function() { return "Templax"; },
		"version" => $GLOBALS["Templax"]["General"]["version"],
		"description" => "This framework is an adaption of the javascript template framework js_Templax but written in php"
	);

	$hooks = array(
		"base_page-title" => "Hook title"
	);

	$content = \Templax\Templax::parse( "base", $markup, $hooks );
	echo $content;

//_____________________________________________________________________________________________
//