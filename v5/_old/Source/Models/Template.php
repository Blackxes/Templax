<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * template model
 * 
 * @author Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Models/BaseProcessSet.php" );

//_____________________________________________________________________________________________
class Template extends namespace\BaseProcessSet {

	/**
	 * construction
	 * 
	 * @param string|null $id - the template id
	 * @param string|null $template - the actual template value
	 * @param array $markup - the markup
	 * @param array $options - the template options
	 */
	public function __construct( string $id = null, string $value = "", array $markup = array(), array $options = array() ) {

		parent::__construct( $markup, $options );

		$this->merge( null, array(
			"id" => $id,
			"value" => $value
		));
	}

	/**
	 * returns the template content/value
	 * 
	 * @return string
	 */
	public function value( $value ) {

		return (string) $value;
	}

	/**
	 * returns true on the template being a subtemplate - else false
	 * 
	 * @return boolean
	 */
	public function isSub() {

		// its sub when the id null or doesnt exist in the template manager registry
		return $this->isNull( "id" ) || !\Templax\Templax::$instance->get( $this->get("id") );
	}

	/**
	 * validates this template
	 * some value combinations are not possible therefor a check is needed
	 * 
	 * @return boolean
	 */
	public function verify() {

		// the template is valid in terms of id and template value
		// when the template behind the id is defined and registered
		// or null and the value is not null
		
		// when subtemplate is doesnt matter if the id or content is defined
		// its straight up valid because nothing is expected from sub templates
		if ( $this->isSub() )
			return true;

		// otherwise on "real" templates they need to exists
		// it does not have to contain content but rather having a registered id
		else if ( !is_null($this->id) && \Templax\Templax::$tManager->has($this->id) )
			return true;
		
		// when nothing matches - this template is invalid
		return false;
	}
}

//_____________________________________________________________________________________________
//