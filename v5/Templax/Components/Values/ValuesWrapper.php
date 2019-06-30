<?php
/**********************************************************************************************
 * 
 * @File: contains \Templax\Components\Defaults\ValuesWrapper
 * 	stores and manages all values about \Templax
 * 	defaults / templates / caching stuff / etc.
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

namespace Templax\Components\Defaults;

use \Templax\Components\Template;

class ValuesWrapper extends Template\TemplateContainer {
	
	/**
	 * construction
	 */
	public function __construct( array $values = [] ) {
		parent::__construct( $values );
	}
}