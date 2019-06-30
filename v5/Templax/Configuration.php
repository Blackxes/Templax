<?php
/**********************************************************************************************
 * 
 * @File configuration file
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
**********************************************************************************************/

# general defaults
const TEMPLAX_DEFAULTS = [

	"process" => [

		# default callback
		"callback" => \Templax\Components\Query\
	]
];

#----------------------------------------------------------------------------------------------
# default options
const TEMPLAX_DEFAULT_OPTIONS = [

	# default options for templates
	"template" => [

		# defines whether this template is allowed to be parsed
		# this options is the root permission to start processing the template
		"parse" => true,

		# defines the permission to interpret the commands within the template
		"interpret" => true
	],

	# default options for a rule
	"rule" => [
		
		# defines whether this rule may be rendered
		"render" => true,

		# permission to be able to hook this rule
		"allowHooking" => true
	],

	# defaults options for a process
	"process" => []
];

#----------------------------------------------------------------------------------------------
# regex # dont change anything except you know what you are doing!
const TEMPLAX_REGEX = [

	# extracts x regardless of spaces # String: "{{ X }}"
	"rule" => "/{{\s*(?:[^<>])*?\s*}}/",

	# extracts x regardless of spaces # String: "X[: Y]"
	"request" => "/([\w-]+)(?:[\w\s:-]+)?/",

	# extracts x regardless of spaces # String: "[Y]: X"
	"key" => "/:\s*([\w-]+)?/",

	# extracts x regardless of spaces # String: "[Y]: Z | X"
	"options" => "\|(.*?)}}",

	# extracts x regardless of spaces # String: "rawrule X {{ /request: key }}"
	"area" => function( string $rawRule, string $key ) {
		
		# since the pipe is a regex token prepend a backslash to correct it
		$rule = str_replace( "|", "\\|", $rawRule );

		return "/{$rawRule}\s*(.*?)\s*{{\s*\/{$request}\s*}}/";
	}
];