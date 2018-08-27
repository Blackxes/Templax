<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * configurations
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

// no namespace .. *sad*^

//_____________________________________________________________________________________________
// generals configurations
$GLOBALS["Templax"]["General"] = array(
	"version" => "2.0.1"
);

//_____________________________________________________________________________________________
// debugging stuff
$GLOBALS["Templax"]["Defaults"] = array(
	"Rules" => array(
		"BaseOptions" => array(
			"render" => true
		)
	),
	"Process" => array(
		"BaseOptions" => array(
			"render" => true
		)
	),
	"BaseMarkup" => array(),
	"BaseOptions" => array(
		"render" => true,
		"cache" => true
	)
);

//_____________________________________________________________________________________________
// dependencies
// *removed*

//_____________________________________________________________________________________________
// regex to extract several string
$GLOBALS["Templax"]["ExtractionRegex"] = array(
	"extractRule" => "/{{\s*(?:[^<>])*?\s*}}/",
	"extractRequest" => "/([\w-]+)(?:[\w\s:-]+)?/",
	"extractKey" => "/{{\s*[\w-]+\s*:\s*([\w-]+)*?\s*}}/"
);

// for better look at the regex
$GLOBALS["Templax"]["ExtractionRegex"]["extractArea"] = function( \Templax\Source\Models\Query $query ) {

	// extract everything in between
	// $query->rawRule
	// and
	// {{$query->request end: $query->key}}
	// 
	return "/{$query->rawRule}(.*?){{\s*{$query->request}\s+end\s*:\s*{$query->key}\s*}}/";
};

//_____________________________________________________________________________________________
// 