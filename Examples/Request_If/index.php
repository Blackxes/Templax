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
		)
	);

	$markup = array(
		"health" => "200",
		"if-health" => array(
			"armor" => "450",
			"wusa" => "null Wusa"
		),
		"if-damage" => array(
			"wow" => "sick"
		)
	);

	$content = \Templax\Templax::parse( "base", $markup );
	echo $content;

//_____________________________________________________________________________________________
//