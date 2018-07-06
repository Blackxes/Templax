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
			"base" => __DIR__ . "/index.html",
			"placeholder"  => __DIR__ . "/placeholder.html",
		)
	);

	$markup = array(
		"placeholder" => array(
			"some-marker-in-another-template" => "Text defined for the placeholder template"
		)
	);

	$content = \Templax\Templax::parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//