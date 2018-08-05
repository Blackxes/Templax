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
// general things
$GLOBALS["Templax"]["Configuration"]["General"] = array(
	"version" => "1.0.0"
);

//_____________________________________________________________________________________________
// debugging stuff
$GLOBALS["Templax"]["Configuration"]["Debugging"]["PrintErrors"] = true;

//_____________________________________________________________________________________________
// dependencies
$GLOBALS["Templax"]["Dependencies"] = array(
	"Logfile" => "\Logfile\Logfile",
);

//_____________________________________________________________________________________________
// parsing regex
$GLOBALS["Templax"]["Configuration"]["Regex"] = array(
	"extractRule" => "/{{\s*(?:[^<>])*?\s*}}/",
	"extractRequest" => "/([\w-]+)(?:[\w\s:-]+)?/",
	"extractKey" => "/{{\s*[\w-]+\s*:\s*([\w-]+)*?\s*}}/",
);

// for better look at this regex
//
// param1 (\Templax\Source\Models\Query) expects the query
//
$GLOBALS["Templax"]["Configuration"]["Regex"]["extractArea"] = function( \Templax\Source\Models\Query $query ) {
	return "/{$query->getRawRule()}(.*?){{\s*{$query->getRequest()}\s+end\s*:\s*{$query->getKey()}\s*}}/";
};

//_____________________________________________________________________________________________
// actual parsing configurations like templates and defaults
$GLOBALS["Templax"]["Configuration"]["Parsing"] = array(
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
		)
	)
);

//_____________________________________________________________________________________________
//