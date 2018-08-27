<?php

/**
 * displays all tests
 */

// includes and definitions
require_once ( "./Templax/Templax.php" );
require_once ( "./Dependencies/Logfile/Logfile.php" );

\Templax\Templax::define( array(
	"base" => array( "path" => __DIR__ . "\\index.html", "marker" => "base" ),
	"tests" => array( "path" => __DIR__ . "\\index.html", "marker" => "tests" )
));

// markup building
$markup = array( "content" => "tests" );
$tests = preg_grep( "/^\./", scandir( "./Tests" ), PREG_GREP_INVERT );

foreach( $tests as $index => $file ) {
	$markup["templateSelect-content"]["tests"][$index] = array(
		"label" => $file,
		"link" => "./Tests/" . $file
	);
}

echo \Templax\Templax::parse( "base", $markup );