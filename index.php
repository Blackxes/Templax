<?php
/**********************************************************************************************
 * 
 * @File examples listing of Templax
 * 
 * @Author: Alexander Bassov
 * 
**********************************************************************************************/

error_reporting( E_ALL & ~E_NOTICE );
ini_set( "display_errors", true );

# get example list
$v = "5";
$list = preg_grep("/^\./", scandir("./Examples/v{$v}"), PREG_GREP_INVERT );

$examples = "";

foreach( $list as $index => $dir ) {
	if ( file_exists("index.php") ) {
		$examples .= sprintf( '<li><a href="./Examples/v%s/%s">%s</a></li>', $v, $dir, $dir );
	}
}

$content = sprintf( file_get_contents("./index.html"),  $v, $examples );

echo $content;