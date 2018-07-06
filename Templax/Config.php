<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	html template parser main file / to use include this file and its done
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

// no namespace .. *sad*

//_____________________________________________________________________________________________
// parsing regex
$GLOBALS["Templax"]["CONFIG"]["REGEX"] = array(
	"extractRule" => "/{{\s*(?:[^<>])*?\s*}}/",
	"extractRequest" => "/([\w-]+)(?:[\w\s:-]+)?/",
	"extractKey" => "/{{\s*[\w-]+\s*:\s*([\w-]+)*?\s*}}/",
);

// for better look at this regex
//
// param1 (\Templax\Models\Query) expects the query
// param2 (string) expects the 
//
$GLOBALS["Templax"]["CONFIG"]["REGEX"]["extractArea"] = function( \Templax\Source\Models\Query $query ) {
	return "/{$query->getRawRule()}(.*?){{\s*{$query->getRequest()}\s+end\s*:\s*{$query->getKey()}\s*}}/";
};

//_____________________________________________________________________________________________
$GLOBALS["Templax"]["CONFIG"]["PARSING"] = array(
	"optionSets" => array(
		"default" => array(
			"rule" => array(
				"render" => true,
			),
			"template" => array(
				"render" => true,
			)
		),
		"templateInline" => array(
			"renderInline" => false
		),
		"case" => array(
			"render" => true
		)
	)
);

//_____________________________________________________________________________________________
//