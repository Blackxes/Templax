<?php
/**********************************************************************************************
 * 
 * @File: contains \Templax\Components\Template\Template
 * 	stores information about a template
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

namespace Template\Components\Template;

use Components;

class Template extends \ParameterBag\ParameterBag {

	/**
	 * construction
	 * 
	 * @param string $key - the template identifier
	 * @param string $value - template value
	 * @param array $markup - markup of this template
	 * @param array $options - options of this template
	 * @param bool $isTemporary - defines whether this template is temporary
	 * 	temporary templates are not obliged to be registered in the template container
	 * 	and can exists with only the template value
	 */
	public function __construct( string $id, string $value, array $markup, array $options = [], bool $isTemporary = false ) {

		parent::__construct([
			"id" => $id,
			"value" => $value,
			"markup" => $markup,
			"options" => new InformationContainer\TemplateOptions( $options ),
			"isTemporary" => $isTemporary
		]);
	}
}