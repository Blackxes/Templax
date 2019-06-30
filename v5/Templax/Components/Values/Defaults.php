<?php
/**********************************************************************************************
 * 
 * @File: contains default values for a template parsing
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

namespace Templax\Components\Defaults;

class Defaults extends \ParameterBag\ParameterBag {
	
	/**
	 * construction
	 * 
	 * @param array $markup - default template markup
	 * @param array $hooks - default hooks
	 * @param array $options - default options
	 */
	public function __construct( array $markup = [], array $hooks = [], array $options = [] ) {
		parent::__construct([
			"markup" => $markup,
			"hooks" => $hooks,
			"options" => $options,
		]);
	}
}