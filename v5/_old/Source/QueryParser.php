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
require_once( TEMPLAX_ROOT . "/Source/Classes/Miscellaneous.php" );

//_____________________________________________________________________________________________
class QueryParser {

	/**
	 * construction
	 */
	public function __construct() {}

	/**
	 * renders the inner context when the value is not null
	 * the value (when not array) are casted into string
	 * 
	 * keys of the case command can also server as value selector
	 * just use the same name for the case as marker
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function case( Models\Query $query ) {

		if ( !$query->get("key") )
			return new Models\Response();
		
		$area = $this->getTemplateArea( $query );
		
		// dont render when the value does not exists or is empty
		$value = $query->get("value");

		if ( $value == "" || is_array($value) && empty($value) )
			return new Models\Response( $area["full"], "" );
		
		// use the markup or create a markup with $query->key => $query->value
		$value = $query->get("value");

		$content = \Templax\Templax::$instance->parse(
			$area["template"],
			is_array($value) ? $value : array( $query->get("key") => $value),
			array(),
			$query->get("process")
		);

		return new Models\Response( $area["full"], $content );
	}

	/**
	 * processes the "foreach" command
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function foreach( Models\Query $query ) {
		
		// get command area
		$area = $this->getTemplateArea( $query );

		// when no rendering is allowed return and define the area as replacement
		if ( !$query->get(["options", "render"]) || !is_array($query->get("value")) )
			return new Models\Response( $area["full"], null );
		
		// build content
		$content = "";
		
		foreach( $query->get("value") as $id => $markup ) {

			// check for a hook
			$markupValue = \Templax\Templax::$instance->rParser->getHookValueFromCommandContext( $query, $id, $markup );

			// add the index to the markup
			$markupValue["tx-index"] = $id;
			
			// parse content (with the possible value from the hook)
			$content .= \Templax\Templax::$instance->parse(
				new Models\Template( (string) $id, $area["template"] ),
				(array) $markupValue,
				array(),
				$query->get("process")
			);
		}
		
		return new Models\Response( $area["full"], $content );
	}

	/**
	 * extracts a substring from a template ( based on the given query )
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 */
	public function getTemplateArea( Models\Query $query ) {

		// use the query when given else extract the key area
		// $regex = 
		// if ( !is_null)
		preg_match( $GLOBALS["Templax"]["ExtractionRegex"]["ExtractArea"]( $query ), $query->get("context"), $match );

		// area => describes the complete match of the regex
		// template => describes the area within the marker
		//
		return array( "full" => (string) $match[0], "template" => (string) $match[1] );
	}

	/**
	 * behave just like the "case" command except the value has to be boolean
	 * and defines wether the inner context is rendered or not
	 * 
	 * when the value is not a boolean its not rendered
	 * casting into boolean results in not wanted behaviour
	 * such as "false" results in true
	 * 
	 * the markup is defined in the command value of the command
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function if( Models\Query $query ) {
		
		if ( !$query->get("key") )
			return new Models\Response();
		
		// from here the area is always needed to replace based on the result
		// the whole section of the rule
		$area = $this->getTemplateArea( $query );

		// store value result into the data cache to let the possible
		// else command know wether to render or not
		$value = $query->get("value");

		// dont render wether if or else when no boolean is given
		if ( !is_bool($value) )
			return new Models\Response( $area["full"] );

		// exit with filled response when result resolves to boolean false
		else if ( !$value )
			return new Models\Response( $area["full"], null, null, null, array( $query->get("commandSignature") => false ) );
		
		$content = \Templax\Templax::$instance->parse( $area["template"], (array) $query->get("markup")->all(), array(), $query->get("process") );

		// let other commands know this command succeeded
		$dataCache = array( $query->get("commandSignature") => true );

		// return with cached data
		return new Models\Response( $area["full"], $content, null, null, $dataCache );
	}

	/**
	 * parses the ifelse command / behaves just like the regular if else in programming languages
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function else( Models\Query $query ) {

		if ( !$query->get("key") )
			return new Models\Response();

		$area = $this->getTemplateArea( $query );

		// only render when the associated "if" command is not null and boolean false
		$result = $query->get(["dataCache", "if-{$query->get("key")}"]);

		if ( is_null($result) || $result )
			return new Models\Response( $area["full"], null );
		
		$content = \Templax\Templax::$instance->parse( $area["template"], (array) $query->get("markup")->all(), array(), $query->get("process") );

		// let other commands know the else succeeded - at the moment i dont know why - but its cool to use the datacache for reasons like this - long comment right?
		$dataCache = array( $query->get("commandSignature") => true );
		
		return new Models\Response( $area["full"], $content, null, null, $dataCache );
	}

	/**
	 * parse entrance - parses the request by the given query
	 * 
	 * @param \Templax\Source\Models\Query $query - the query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function parse( Models\Query $query ) {

		// render check will cancel everything
		if ( !((bool) $query->get(["options", "render"])) )
			return new Models\Response();
		
		// since this condition mostly fails
		// returns the default earlier than processing through the function
		if ( !method_exists($this, $query->get("request")) )
			return new Models\Response( null, $query->get("value") );

		// call request function and parse the query
		return $this->{ $query->get("request") }( $query );
	}

	/**
	 * replaces the rule with the requested template
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function template( Models\Query $query ) {

		if ( !$query->get(["options", "render"]) || !$query->get("key") )
			return Models\Response();
		
		// when the template does not exists return this query as a post query
		// there is a change that a templateInline might exist
		if ( !\Templax\Templax::$instance->has($query->get("key")) ) {

			var_dump( \Templax\Templax::$instance->get("placeholder_test"));

			// avoid recursive post quering when the template just doesnt exist
			// by checking wether a post query already has been defined
			if ( !($query->get("isPostQuery")) )
				return new Models\Response( $query->get("rawRule"), $query->get("rawRule"), $query );
			
			return new Models\Response( $query->get("rawRule"), "" );
		}

		// substitution
		$value = $query->get("value");
		
		$content = \Templax\Templax::$instance->parse( $query->get("key"), (array) $value, array(), $query->get("process") );

		return new Models\Response( $query->get("rawRule"), $content );
	}

	/**
	 * registers (and replaces the rule with) the template
	 * 
	 * Note: use "_options" and "_markup" to define the associated defaults
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function templateInline( Models\Query $query ) {

		if ( !$query->getOption("render") || !$query->getKey() )
			return new Models\Response();
		
		$area = $this->getTemplateArea( $query );

		\Templax\Templax::$tManager->registerTemplate(
			$query->getKey(),
			$area["template"],
			(array) $query->getCommandValue()["_markup"],
			(array) $query->getCommandValue()["_options"]
		);

		// predeclare response because the content might change
		// when this template shall be rendered inline
		$response = Models\Response( $query->getRawRule(), "" );

		if ( $query->getOption("renderInline") )
			$response->setValue( \Templax\Templax::parse($query->getKey(), array(), array(), $query->getProcess()) );
		
		return $response;
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
		if ( is_null($query->get("key")) )
			return Models\Response();

		// check template existance
		if ( !\Templax\Templax::$instance->has($query->get("value")) )
			return new Models\Response( $query->get("rawRule"), "" );
		
		$content = \Templax\Templax::$instance->parse( $query->get("value"), $query->get("commandValue"), array(), $query->get("process") );

		return new Models\Response( $query->get("rawRule"), $content );
	}
}

//_____________________________________________________________________________________________
//