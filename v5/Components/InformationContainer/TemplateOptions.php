<?php
/**********************************************************************************************
 * 
 * @File: contains \Templax\Components\InformationContainer\TemplateOptions
 * 	stores information about a template
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

namespace Templax\Components\InformationContainer;

class TemplateOptions extends \ParameterBag\ParameterBag {

	/**
	 * construction
	 */
	public function __construct( array $options = [] ) {

		parent::__construct( TEMPLAX_DEFAULTS_INFO_CONTAINER_TEMPLATE_OPTIONS + $options );
	}
}