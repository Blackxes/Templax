<?php
/**********************************************************************************************
 * 
 * @File: contains \Templax\Templax as singleton
 * 	use \Templax\Templax::getInstance() when manipulating a separate created instance
 * 	when calling ::getInstance() the first time the framework creates an instance on its own
 * 	usually this is not necessary when creating a regular instance of \Templax\Templax
 * 	since its creation is within the constructor
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

namespace Templax;

require_once( __DIR__ . "/autoload.php" );

class Templax extends Components\Values\ValuesWrapper {
	
	/**
	 * main instance of \Templax\Templax
	 * 
	 * @var \Templax\Templax
	 */
	static private $instance;
	
	/**
	 * constructs a single instance of \Templax\Templax
	 * 
	 * @param array $templates - initial templates
	 * 	@see \Templax\Components\Template\TemplateContainer
	 * @param array $markup - the default markup
	 * @param array $hooks - default hooks
	 * @param array $options - default options
	 */
	public function __construct( array $templates = [], array $markup = [], array $hooks = [], array $options = [] ) {
		self::getInstance();
		
		parent::__construct( $templates );
	}
	
	/**
	 * initilization. use this to overwrite current defaults or to post initialize
	 * when the definition by construction was not possible
	 */
	public function init( array $templates = [], array $markup = [], array $hooks = [], array $options = [] ) {
		
		$instance = self::getInstance();
		$instance->registerTemplates( $templates );
		
		return $this;
	}
}