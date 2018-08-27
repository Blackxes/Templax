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

//_____________________________________________________________________________________________
class Template {

	/**
	 * contains the template id - when null the template represents as a subtemplate
	 * 
	 * @var string|null
	 */
	public $id = null;

	/**
	 * contains the actual value of the template
	 * 
	 * @var string|null
	 */
	public $value = null;

	/**
	 * base markup for this template
	 * this template will be overwritten by any given template
	 * 
	 * @var array|null
	 */
	public $markup = null;

	/**
	 * base options for this template
	 * 
	 * @var array|null
	 */
	public $options = null;

	/**
	 * defines wether this template is usable or contains invalid value combinations
	 */
	private $valid = false;

	/**
	 * construction
	 * 
	 * @param string|null $id - the template id
	 * @param string|null $template - the actual template value
	 * @param array $markup - the markup
	 * @param array $options - the template options - not the default rule options!
	 * @param boolean $isSub - defines wether this template is a subtemplate or not
	 */
	public function __construct( ?string $id, ?string $template, ?array $markup = array(), ?array $options = array(), bool $isSub = false ) {
		
		$this->id = $id;
		$this->value = $template;
		$this->markup = $markup;
		$this->options = $options;

		$this->isSub = $isSub;
	}

	/**
	 * validates this template
	 * some value combinations are not possible therefor a check is needed
	 * 
	 * @return boolean
	 */
	public function validate() {

		// the template is valid in terms of id and template value
		// when the template behind the id is defined and registered
		// or null and the value is not null
		//
		// when subtemplate is doesnt matter if the id or content is defined
		// its straight up valid because nothing is expected from sub templates
		//
		if ( $this->isSub )
			$this->valid = true;
		
		// otherwise on "real" templates they need to exists or act like a subtemplate
		else if ( !is_null($this->id) || is_null($this->value) || \Templax\Templax::$tManager->has($this->id) )
			$this->valid = false;
		
		return $this->valid;
	}
}

//_____________________________________________________________________________________________
//