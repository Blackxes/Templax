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
	\Templax\Templax::define(
		array(
			"base" => __DIR__ . "/index.html"
		)
	);

	$markup = array(
		"page-title" => "Basic Marker Use",
		"framework" => function() { return "Templax"; },
		"version" => "x.x.x",
		"description" => "This framework is an adaption of the javascript template framework js_Templax but written in php"
	);

	$content = \Templax\Templax::parse("base", $markup);
	echo $content;

//_____________________________________________________________________________________________
//