<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	example of basic marker usage
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

require_once ( "../../Templax/Templax.php" );

//_____________________________________________________________________________________________

	// load template
	\Templax\Templax::define(
		array(
			"base" => __DIR__ . "/index.html"
		)
	);

	$markup = array(
		"page-title" => "Foreach request",
		"fruits" => array(
			"fruit1" => array( "label" => "banana" ),
			"0" => array( "label" => "apple" ),
			"_wusa" => array( "label" => "citrus" )
		)
	);

	$content = \Templax\Templax::parse("base", $markup);
	echo $content;

//_____________________________________________________________________________________________
//