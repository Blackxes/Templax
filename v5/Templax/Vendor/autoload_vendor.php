<?php
/**********************************************************************************************
 * 
 * @File: contains the vendor autoloader as well as the one for Templax
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

# Templax autoloader
spl_autoload_register( function($className) {
	$file = AUTOLOAD_ROOT . preg_replace( "/\\Templax\\\/", DIRECTORY_SEPARATOR, $className, 1 ) . ".php";
	
	return file_exists( $file ) ? require_once( $file ) : false;
});

#----------------------------------------------------------------------------------------------
# vendor autoloader
foreach( preg_grep("/[^\.{1,2}(autoload_vendor.php)]/", scandir(__DIR__)) as $i => $fileName ) {
	spl_autoload_register( function($className) use ($fileName) {
		$file = preg_replace( "/\//", DIRECTORY_SEPARATOR, __DIR__ . "/$className" . ".php" );
		
		return file_exists( $file ) ? require_once( $file ) : false;
	});
}