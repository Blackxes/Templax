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
	\Templax\Templax::Init(
		array(
			"base" => __DIR__ . "/index.html",
			"placeholder"  => __DIR__ . "/placeholder.html",
		)
	);

	$markup = array(
		"placeholder" => array(
			"marker" => "Text defined for the placeholder template"
		)
	);

	$content = \Templax\Templax::parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//