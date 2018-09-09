<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	Templax - a framework to devide the use of php and html in one file
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

require_once ( "./Templax/Templax.php" );

//_____________________________________________________________________________________________
	// load template
	\Templax\Templax::Init( array(
		"base" => array( "file" => __DIR__ . "\\index.html", "marker" => "base", "markup" => array( "wusa" => 43) ),
		"examples" => array( "file" => __DIR__ . "\\index.html", "marker" => "examples" )
	));

	// scan examples and build markup
	$markup = array(
		"content" => "examples",
		"_options" => array(
			"render" => 1
		)
	);
	$examples = preg_grep("/^\./", scandir("./Examples"), PREG_GREP_INVERT );

	foreach( $examples as $index => $dir ) {
		if ( file_exists("index.php") ) {
			$markup["templateSelect-content"]["examples"][$dir] = array(
				"title" => str_replace("_", " ", $dir ),
				"path" => "./Examples/" . $dir . "/index.php"
			);
		}
	}
	
	$content = \Templax\Templax::parse("base", $markup );
	
	echo $content;

//_____________________________________________________________________________________________
//