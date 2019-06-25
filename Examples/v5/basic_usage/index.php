<?php
/**********************************************************************************************
 * 
 * testing basic marker usage
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

class Params {
	protected $params;
	public function __construct( $p ) {
		$this->params = $p;
	}
	public function set( $key, $value ) {
		$this->params[ $key ] = $value;
	}
	public function get( $key ) {
		return $this->params[ $key ];
	}
}

class Container extends \Params {
	public function __construct( $p ) {
		parent::__construct( $p );
	}
}

class Base extends \Container {
	static public $instance;
	public $values = [];

	public function __construct() {
		$this->values = [];
	}

	static public function getInstance() {
		if ( !is_null(self::$instance) )
			return self::$instance;
		
		self::$instance = new \Base;
		
		return self::$instance;
	}
}

class Child {
	public function staticChange() {
		$instance = \Base::getInstance();
		$instance->set( "static", "child" );
	}
	public function localChange() {
		$variable = new \Base;
		$variable::$instance->set( "local", "child" );
	}
}
$child = new Child();

var_dump( \Base::$instance );
$child->staticChange();

var_dump( \Base::$instance );
$child->localChange();

var_dump( \Base::$instance );
exit;

require_once( __DIR__ . "/../../../v5/Templax.php" );



$parser = \Templax\Templax();

var_dump( $parser );