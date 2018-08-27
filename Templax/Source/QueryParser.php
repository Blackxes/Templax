<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * query parser - parses the given query and returns the built response
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source;

use \Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Templax.php" );
require_once( TEMPLAX_ROOT . "/Source/Models/Response.php" );

//_____________________________________________________________________________________________
class QueryParser {

	/**
	 * construction
	 */
	public function __construct() {}

	/**
	 * parse entrance - parses the request by the given query
	 * 
	 * @param \Templax\Source\Models\Query $query - the query
	 * 
	 * @return \Templax\Source\Models\Response|null
	 */
	public function parse( Models\Query $query ) {

		// render check will cancel everything
		if ( !$query->options["render"] )
			return null;
		
		// when a regular marker
		if ( is_null($query->key) )
			return new Models\Response( null, $query->value );
		
		// call request function and parse the query
		if ( method_exists($this, $query->request) ) {

			$func = $query->request;
			return $this->$func( $query );
		}

		// on no match at all
		return null;
	}

	/**
	 * processes the "foreach" command
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function foreach( Models\Query $query ) {

		// when no key no value for the foreach can be used
		if ( is_null($query->key) )
			return null;
		
		// get command area
		$area = $this->getTemplateArea( $query );

		// when no rendering is allowed return and define the area as replacement
		if ( !$query->getOption("render") || !is_array($query->value) )
			return new Models\Response( $area["full"], null );
		
		// build content
		$content = "";
		
		foreach( $query->value as $id => $markup ) {
			$content .= \Templax\Templax::parse(
				new Models\Template( (string) $id, $area["template"], null, null, true ),
				$markup, 
				null,
				$query->process
			);
		}
		
		return new Models\Response( $area["full"], $content );
	}

	/**
	 * processes the "templateSelect" command
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function templateSelect( Models\Query $query ) {

		// no template can be selected when the key is missing
		if ( is_null($query->key) )
			return null;

		// check template existance
		if ( !\Templax\Templax::$tManager->has($query->value) )
			return new Models\Response( $query->rawRule, "" );
		
		$content = \Templax\Templax::parse( $query->value, $query->commandValue );

		return new Models\Response( $query->rawRule, $content );
	}

	

	//_________________________________________________________________________________________
	// replaces the rule with the requested template
	//
	// param1 (\Templax\Source\Models\Query) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	public function template( Models\Query $query ) {

		if ( !$query->getOption("render") || !$query->key )
			return Models\Response();
		
		$response = new Models\Response( $query->rawRule, "" );
		
		// when the template doesnt exists return and define as post query
		// but not when its already a post query
		if ( !\Templax\Templax::$tManager->has($query->key) ) {

			if ( is_null(!$query->postQuery) )
				return new Models\Response( $query->rawRule, $query->rawRule, $query );

			return new Models\Response( $query->rawRule, "" );
		}

		$value = $query->value;

		$content = \Templax\Templax::parse( $query->key, ($value && is_array($value)) ? $value : array() );
		
		return new Models\Response( $query->rawRule, $content );
	}

	//_________________________________________________________________________________________
	// registers the given template inline
	//
	// param1 ( \Templax\Source\Models\Query ) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	public function templateInline( Models\Query $query ) {

		if ( !$query->getOption("render") || !$query->getKey() )
			return new Models\Response();
		
		$response = new Models\Response();

		// extact inline template
		preg_match($GLOBALS["Templax"]["ExtractionRegex"]["extractArea"]( $query ),
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
	// parses the part within the case command when the requested key is not null or false
	//
	// param1 (\Templax\Source\Models\Query) expects the query
	//
	// return \Templax\Source\Models\Response
	//
	public function case( Models\Query $query ) {

		if ( !$query->key )
			return new \Templax\Source\Models\Response();
		
		preg_match($GLOBALS["Templax"]["ExtractionRegex"]["extractArea"]( $query ),
			$query->template, $templateMatch);

		if ( !$query->value )
			return new Models\Response( ($templateMatch) ? $templateMatch[0] : $query->rawRule, "" );
		
		$markup = $query->value;

		// when the markup is not an array define the value in the markup as the key itself
		$content = \Templax\Templax::parse(
			$templateMatch[1], ( is_array($markup) ) ? $markup : array( $query->key => $markup)
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
	public function if ( Models\Query $query ) {

		if ( !$query->key )
			return new Models\Response();
		
		preg_match($GLOBALS["Templax"]["ExtractionRegex"]["extractArea"]( $query ),
			$query->template, $templateMatch);

		if ( !$query->process->queryMarkup[ $query->key ] )
			return new Models\Response( ($templateMatch) ? $templateMatch[0] : $query->rawRule, "" );
		
		$markup = $query->commandValue;
		$content = \Templax\Templax::parse(
			$templateMatch[1],
			is_array($markup) ? $markup : array()
		);

		return new Models\Response( $templateMatch[0], $content );
		
	}

	/**
	 * extracts a substring from a template
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 */
	public function getTemplateArea( Models\Query $query ) {

		preg_match( $GLOBALS["Templax"]["ExtractionRegex"]["extractArea"]( $query ), $query->template, $match );

		// area => describes the complete match of the regex
		// template => describes the area within the marker
		//
		return array( "full" => (string) $match[0], "template" => (string) $match[1] );
	}
}

//_____________________________________________________________________________________________
//