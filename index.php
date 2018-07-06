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
	\Templax\Templax::define( array("base" => __DIR__ . "\\index.html") );

	// scan examples and build markup
	$markup = array(
		"title" => "Templax - Table of Contents"
	);
	$examples = preg_grep("/^\./", scandir("./Examples"), PREG_GREP_INVERT );

	foreach( $examples as $index => $dir ) {
		if ( file_exists("index.php") ) {
			$markup["examples"][$dir] = array(
				"title" => str_replace("_", " ", $dir ),
				"path" => "./Examples/" . $dir . "/index.php"
			);
		}
	}

	$content = \Templax\Templax::parse("base", $markup);
	echo $content;

//_____________________________________________________________________________________________
//