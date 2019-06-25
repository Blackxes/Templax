<?php
/**********************************************************************************************
 * 
 * @File: contains the autoloader for TemplaxPhp
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

spl_autoload_register( function($className) {
	$file = __DIR__ . preg_replace( "/\\Templax/", "", $className ) . ".php";
	
	return file_exists( $file ) ? require_once( $file )  : false;
});