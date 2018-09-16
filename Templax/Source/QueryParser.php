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

		if ( !$query->getKey() )
			return new Models\Response();
		
		$area = $this->getTemplateArea( $query );
		
		// dont render when the value does not exists or is empty
		$value = $query->getValue();

		if ( $value == "" || is_array($value) && empty($value) )
			return new Models\Response( $area["full"], "" );
		
		// use the markup or create a markup with $query->key => $query->value
		$value = $query->getValue();

		$content = \Templax\Templax::parse(
			$area["template"],
			is_array($value) ? $value : array( $query->getKey() => $value),
			array(),
			$query->getProcess()
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
		if ( !$query->getOption("render") || !is_array($query->getValue()) )
			return new Models\Response( $area["full"], null );
		
		// build content
		$content = "";
		
		foreach( $query->getValue() as $id => $markup ) {

			// check for a hook
			$markupValue = \Templax\Templax::$rParser->getHookValueFromCommandContext( $query, $id, $markup );
			
			// parse content (with the possible value from the hook)
			$content .= \Templax\Templax::parse(
				new Models\Template( (string) $id, $area["template"] ),
				(array) $markupValue,
				array(),
				$query->getProcess()
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
		preg_match( $GLOBALS["Templax"]["ExtractionRegex"]["ExtractArea"]( $query ), $query->getContext(), $match );

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
		
		if ( !$query->getKey() )
			return new Models\Response();
		
		$area = $this->getTemplateArea( $query );

		// check render condition
		$value = $query->getValue();

		if ( !is_bool($value) || !$value )
			return new Models\Response( $area["full"], "" );

		$content = \Templax\Templax::parse(
			$area["template"],
			(array) $query->getCommandValue(),
			array(), 
			$query->getProcess()
		);

		return new Models\Response( $area["full"], $content );
	}

	/**
	 * parses the ifelse command / behaves just like the regular if else in programming languages
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function ifelse( Models\Query $query ) {

		// var_dump($query);

		// var_dump( $query );
		$area = $this->getTemplateArea( $query );
		// var_dump( $query->getCommandValue() );
		var_dump( $area );


		return new Models\Response( $area["full"], "" );
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
		if ( !((bool) $query->getOption("render")) )
			return new Models\Response();
		
		// since this condition mostly fails
		// returns the default earlier than processing through the function
		if ( !method_exists($this, $query->getRequest()) )
			return new Models\Response( null, $query->getValue() );

		// call request function and parse the query
		$func = $query->getRequest();

		return $this->$func( $query );
	}

	/**
	 * replaces the rule with the requested template
	 * 
	 * @param \Templax\Source\Models\Query $query - the processing query
	 * 
	 * @return \Templax\Source\Models\Response
	 */
	public function template( Models\Query $query ) {

		if ( !$query->getOption("render") || !$query->getKey() )
			return Models\Response();
		
		// when the template does not exists return this query as a post query
		// there is a change that a templateInline might exist
		if ( !\Templax\Templax::$tManager->has($query->getKey()) ) {

			// avoid recursive post quering when the template just doesnt exist
			// by checking wether a post query already has been defined
			if ( is_null(!$query->getPost) )
				return new Models\Response( $query->getRawRule(), $query->getRawRule(), $query );
			
			return new Models\Response( $query->getRawRule(), "" );
		}

		// substitution
		$value = $query->getValue();

		$content = \Templax\Templax::parse( $query->getKey(), (array) $value, array(), $query->getProcess() );

		return new Models\Response( $query->getRawRule(), $content );
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
		if ( is_null($query->getKey()) )
			return Models\Response();

		// check template existance
		if ( !\Templax\Templax::$tManager->has($query->getValue()) )
			return new Models\Response( $query->getRawRule(), "" );
		
		$content = \Templax\Templax::parse( $query->getValue(), $query->getCommandValue(), array(), $query->getProcess() );

		return new Models\Response( $query->getRawRule(), $content );
	}
}

//_____________________________________________________________________________________________
//