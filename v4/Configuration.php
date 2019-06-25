<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * templax configurations
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

// general configurations
$GLOBALS["Templax"]["General"]["Version"] = "4.0.0";

// defaults / subsitution
$GLOBALS["Templax"]["Defaults"] = array(

	
	// rules defaults
	"Rules" => array(
		
		// base options for rules
		"BaseOptions" => array(

			// describes wether this rule will be rendered or not
			"render" => true,
			
			// defines wether the following template shall be parsed or not
			"parse" => true
		)
	),

	// process defaults
	"Process" => array(

		// base options for processes
		"BaseOptions" => array(

			// describes wether the resulting content of this process shall be displayed
			// synonym could be "process" to define wether this template should process or not
			"render" => true,

			// callable in which the query built based on the current processing rule in the template
			// is passed. This callable needs to return a \Templax\Source\Models\Response
			//
			// when null then the \Templax\Source\QueryParser will process the query
			//
			// @see \Templax\Source\Models\Response
			//
			"callback" => null,

			// defines wether the following template shall be parsed or not
			"parse" => true
		)
	)
);

/**********************************************************************************************
 * 
 * Extracting belonging configurations
 * dont change anything except you know what you are doing!
 * 
 * If you got a better solution for the regex mail me - ill test it and improve it.
 * 
/*********************************************************************************************/

// regex to extract several strings
$GLOBALS["Templax"]["ExtractionRegex"] = array(

	// extract X regardless of spaces
	// String: "{{ X }}"
	//
	"ExtractRule" => "/{{\s*(?:[^<>])*?\s*}}/",

	// extract X regardless of spaces
	// String: "X[: Y]"
	"ExtractRequest" => "/([\w-]+)(?:[\w\s:-]+)?/",

	// extract X regardless of spaces
	// String: "[Y]: X"
	//
	"ExtractKey" => "/:\s*([\w-]+)?/",

	// extract X regardless of spaces based on the given query
	// String: "rawRule X {{ queryRequest end: queryKey }}"
	//
	"ExtractArea" => function( \Templax\Source\Models\Query $query, string $customKey = null ) {

		// custom key has higher prio
		// building the range as array to avoid checking for the custom key multiple times
		//
		$range = ( !is_null($customKey) )
			? array( "start" => "{{\s*{$key}\s*}}", "request" => $customKey, "key" => $customKey )
			: array( "start" => $query->get("rawRule"), "request" => $query->get("request"), "key" => $query->get("key") );
		
		return "/{$range["start"]}(.*?){{\s*\/{$range["request"]}\s*:\s*{$range["key"]}\s*}}/";
	}
);

//_____________________________________________________________________________________________
// 