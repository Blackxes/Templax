<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	parses the given query and builds content based on the request
	request can be commands and manipulated content really heavily
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Parser;

use \Templax\Source\Models;

require_once ( TEMPLAX_ROOT . "/Templax.php" );
require_once ( TEMPLAX_ROOT . "/Source/Models/Response.php" );

//_____________________________________________________________________________________________
class RequestParser {

	//_________________________________________________________________________________________
	// no construction of this class is needed
	private function __construct() {}
	
	//_________________________________________________________________________________________
	// parses the given query - when the request matches a command
	// the commands takes over the parsing and interpreting the query
	// however this function returns the value of the command since it invokes the function
	//
	// param1 (\Templax\Source\Models\Query) expects the query instance
	//
	// return \Templax\Source\Models\Response
	//
	static public function parse( Models\Query $query ): Models\Response {

		// commands need to check theirselves wether the request will be rendered or not
		if ( method_exists(get_called_class(), $query->getRequest()) ) {
			$func = $query->getRequest();
			return self::$func( $query );
		}

		// when no command given
		if ( !$query->getOption("render") )
			return new Models\Response();
		
		if ( !$query->getKey() )
			return new Models\Response( null, $query->getValue() );
		
		// default
		return new Models\Response();
	}

	//_________________________________________________________________________________________
	// extract the area surrounded by the command and parses it times given indecies
	// in the markup / using the given markup for the iterations
	//
	// param1 (\Templax\Source\Models\Query) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	static public function foreach( Models\Query $query ): Models\Response {

		if ( !$query->getKey() )
			return new Models\Response();

		preg_match($GLOBALS["Templax"]["Configuration"]["Regex"]["extractArea"]( $query ),
			$query->getTemplate(), $templateMatch);
		
		$content = "";

		// when no array no parsing will happen
		if ( !$query->getOption("render") || !is_array($query->getValue()) )
			return new Models\Response( $templateMatch[0], "" );

		foreach( $query->getValue() as $index => $markup ) {

			$content .= \Templax\Templax::parse(
				$templateMatch[1], is_array( $markup ) ? $markup : array()
			);
		}
		
		return new Models\Response( $templateMatch[0], $content );
	}

	//_________________________________________________________________________________________
	// replaces the rule with the requested template
	//
	// param1 (\Templax\Source\Models\Query) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	static public function template( Models\Query $query ): Models\Response {

		if ( !$query->getOption("render") || !$query->getKey() )
			return \Templax\Source\Models\Response();
		
		$response = new Models\Response( $query->getRawRule(), "" );
		
		// when the template doesnt exists return and define as post query
		// but not when its already a post query
		if ( !\Templax\Templax::hasTemplate($query->getKey()) ) {
			if ( !$query->getIsPostQuery() )
				return new Models\Response( $query->getRawRule(), $query->getRawRule(), $query );
			return new Models\Response( $query->getRawRule(), "" );
		}

		$value = $query->getValue();

		$content = \Templax\Templax::parse( $query->getKey(), ($value && is_array($value)) ? $value : array() );
		
		return new Models\Response( $query->getRawRule(), $content );
	}

	//_________________________________________________________________________________________
	// registers the given template inline
	//
	// param1 ( \Templax\Source\Models\Query ) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	static public function templateInline( Models\Query $query ): Models\Response {

		if ( !$query->getOption("render") || !$query->getKey() )
			return new Models\Response();
		
		$response = new Models\Response();

		// extact inline template
		preg_match($GLOBALS["Templax"]["Configuration"]["Regex"]["extractArea"]( $query ),
			$query->getTemplate(), $templateMatch);
		
		// register new template with the given markup and options
		// markup is being filtered for everything but the _options property
		$value = $query->getValue();
		$key = $query->getKey();

		$markup = array_filter( is_array($value) ? $value : array(), function ($key) { return ($key != "_options"); }, ARRAY_FILTER_USE_KEY );
		$options = is_array( $value["_options"] ) ? $value["_options"] : array();

		$response->setReplacement( $templateMatch[0] );

		$result = \Templax\Templax::getTemplateManager()->registerTemplateInstance(
			new Models\Template( $key, $templateMatch[1], $markup, $options )
		);
		
		return $response;
	}

	//_________________________________________________________________________________________
	// uses the template given in the markup / when template not found
	// its replaced with an empty string
	//
	// param1 (\Templax\Source\Models\Query) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	static public function templateSelect( Models\Query $query ): Models\Response {

		if ( !$query->getKey() )
			return new Models\Response();
		
		preg_match($GLOBALS["Templax"]["Configuration"]["Regex"]["extractArea"]( $query ),
			$query->getTemplate(), $templateMatch);
		
		if ( !\Templax\Templax::hasTemplate($query->getValue()) )
			return new Models\Response( ($templateMatch) ? $templateMatch[0] : $query->getRawRule(), "" );
		
		$markup = $query->getCommandValue();
		$content = \Templax\Templax::parse(
			$query->getValue(),
			is_array($markup) ? $markup : array()
		);

		return new Models\Response( $templateMatch[0], $content );
	}

	//_________________________________________________________________________________________
	// parses the part within the case command when the requested key is not null or false
	//
	// param1 (\Templax\Source\Models\Query) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	static public function case( Models\Query $query ): Models\Response {

		if ( !$query->getKey() )
			return new \Templax\Source\Models\Response();
		
		preg_match($GLOBALS["Templax"]["Configuration"]["Regex"]["extractArea"]( $query ),
			$query->getTemplate(), $templateMatch);

		if ( !$query->getValue() )
			return new Models\Response( ($templateMatch) ? $templateMatch[0] : $query->getRawRule(), "" );
		
		$markup = $query->getValue();

		// when the markup is not an array define the value in the markup as the key itself
		$content = \Templax\Templax::parse(
			$templateMatch[1], ( is_array($markup) ) ? $markup : array( $query->getKey() => $markup)
		);

		return new Models\Response( $templateMatch[0], $content );
	}

	//_________________________________________________________________________________________
	// parses the part within the if command when the key within the markup returns true
	//
	// param1 (\Templax\Source\Models\Query) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	static public function if ( Models\Query $query ): Models\Response {

		if ( !$query->getKey() )
			return new Models\Response();
		
		preg_match($GLOBALS["Templax"]["Configuration"]["Regex"]["extractArea"]( $query ),
			$query->getTemplate(), $templateMatch);

		if ( !$query->getProcess()->getQueryMarkup()[ $query->getKey() ] )
			return new Models\Response( ($templateMatch) ? $templateMatch[0] : $query->getRawRule(), "" );
		
		$markup = $query->getCommandValue();
		$content = \Templax\Templax::parse(
			$templateMatch[1],
			is_array($markup) ? $markup : array()
		);

		return new Models\Response( $templateMatch[0], $content );
		
	}
}

//_____________________________________________________________________________________________
//