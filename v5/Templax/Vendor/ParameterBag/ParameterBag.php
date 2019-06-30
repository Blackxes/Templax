<?php
/**********************************************************************************************
 * 
 * @File: contains \ParameterBag
 * 	a container class which provides a way to manage properties easier than writing
 * 	for every property a getter/setter or any other manipulation/reading function
 * 
 * @Author: Alexander Bassov
 * @Email: alexander.bassov@trentmann.com
 * 
/*********************************************************************************************/

namespace ParameterBag;

class ParameterBag {

	/**
	 * params
	 * 
	 * @var array
	 */
	protected $_params;

	/**
	 * blocks the manipulating functions in this and childs bags
	 * 
	 * @var boolean
	 */
	private $_readonly;

	/**
	 * contains the blocked parameter
	 */
	private $_blocked;

	/**
	 * construction
	 * 
	 * @param mixed $params - initial params or parameter bag
	 * 	__NAMESPACE__\ParameterBag - a parameter bag
	 * 	array - params
	 * @param boolean $readonly - defines wether this bag is read only
	 */
	public function __construct( $params = [], bool $readonly = false, array $blocked = [] ) {

		$this->_params = [];
		$this->_blocked = [];

		if ( is_a($params, __NAMESPACE__ . "\ParameterBag") )
			$this->initByParameterBag( $params );
		
		else if ( is_array($params) )
			$this->init( $params );
		
		else
			throw new \Exception( "ParameterBag: initial parameter invalid. Expects either array or \Conference\Core\Classes\ParameterBag" );
		
		// check blocked parameter
		if ( !empty($blocked) )
			$this->block( $blocked );
		
		$this->_readonly = $readonly;
	}

	/**
	 * returns the parameter of the requested param
	 * 
	 * @param array|string - the param or a parampath
	 * 
	 * @return array|null - null on invalid param else params
	 */
	public function all( $request = [] ) {

		$info = $this->getParamInfo( (array) $request );

		// only when $this params are requested
		// or the requested value is a bag
		return ( $info["isBag"] || !$info["paramKey"] ) ? $info["params"] : null;
	}

	/**
	 * blocks this bag and defines it as readonly
	 */
	public function block( array $params = null ) {

		// when nothing is passed simply block write access
		if ( is_null($params) )
			$this->_readonly = true;
		
		// else block valid params / getting the intersecting keys
		// prevents the ::_blocked array to overblow if someone tries to pass thousands of entries
		// which are not even defined in $this bag
		else
			$this->_blocked = array_intersect( array_keys($this->_params), $params );

		return $this;
	}

	/**
	 * fully unsets the parameter
	 * 
	 * HeadComment:
	 */
	protected function clear( $request ) {

		$info = $this->getParamInfo( (array) $request );
		
		if ( !$this->verifyAccess($info["paramKey"]) )
			return null;

		# nothing to reset when not a bag
		if ( !$info["isBag"] )
			return null;
		
		$info["params"] = [];
		
		return $this;
	}


	/**
	 * initializes this parameter bag
	 * 
	 * @param array $param - initial parameter
	 */
	private function init( array $params ) {

		$this->_params = $params;

		return $this;
	}

	/**
	 * initializes this bag by another bag
	 * 
	 * @param \Conference\Core\Classes $bag - the other bag
	 */
	private function initByParameterBag( namespace\ParameterBag $bag ) {

		$this->replace( $bag->all() );

		return $this;
	}

	/**
	 * returns the requested param value
	 * 
	 * @param array|string - the param or the parampath
	 * @param boolean - is this true and the request an array
	 * 	the request wont be treated as path to a param but rather
	 * 	a list of requested params and this functions returns their value then
	 * 	Note: paths are still possible
	 * @param boolean - when true the key is used to insert the values
	 * 	else and incrementing integer is used
	 * 
	 * @return mixed|null - the value or null on invalid param
	 */
	public function get( $request, $paramSet = false, $associative = true ) {
		
		if ( $paramSet ) {
			
			if ( !\is_array($request) )
				return null;
				
			$reducer = \Closure::bind(function($carry, $item) use ($associative){
				$carry[ !$associative ? count($carry) : $item ] = $this->getParamInfo( (array) $item )[ "value" ];
				return $carry;
			}, $this );
			
			return \array_reduce( $request, $reducer, [] );
		}
			
		
		return $this->getParamInfo( (array) $request )[ "value" ];
	}
	
	/**
	 * returns an array containing information about the requested param
	 * 
	 * @param array $paramPath - the param path
	 * 
	 * @return ParameterBag|array
	 * 	ParameterBag - when a deeper parameter is requested
	 * 	array - information about the requested parameter
	 * 		array(
	 * 			"scope" => params of $this,
	 * 			"bagScope" => when "value" is a bag this contains its params
	 * 			"value" => the requested value
	 * 			"paramKey" => the requested param
	 * 		)
	 */
	private function getParamInfo( $paramPath ) {

		// default
		// params => depending on wether the value is a bag
		//	this contains either the params of $this or the params of the value
		// parent => params of the parent bag
		// value => the requested value
		// paramKey => the requested param key
		// isBag => defines wether the value is a bag or not
		// 
		$info = array( "params" => &$this->_params, "value" => null, "parent" => null, "paramKey" => "", "isBag" => false );

		if ( empty($paramPath) )
			return $info;
		
		$paramKey = array_shift( $paramPath );
		$info[ "paramKey" ] = $paramKey;

		$param = null;
		
		// overwrite params on different conditions
		//
		// check hooks of current scope
		if ( method_exists($this, $paramKey) )
			$param = $this->{$paramKey}( $this->_params[$paramKey] );

		// otherwise use the value when exists
		else if ( array_key_exists($paramKey, $this->_params) )
			$param = &$this->_params[ $paramKey ];
	
		// get possible bag scope
		if ( is_a($param, __NAMESPACE__ . "\ParameterBag") ) {

			// overwrite params scope with params from values bags params
			$info[ "params" ] = &$param->_params;
			$info[ "parent" ] = &$this->_params;
			$info[ "isBag" ] = true;
		}

		// when path not empty check if parmeter bag
		if ( !empty($paramPath) ) {	

			// get deeper request
			if ( $info["isBag"] )
				return array_merge( $param->getParamInfo($paramPath), ["parent" => &$param->_params] );

			// when request fails return empty params
			unset( $info["params"] );

			return $info;
		}

		return array_merge( $info, ["value" => &$param] );
	}

	/**
	 * returns existance of a param as boolean
	 * 
	 * @param array|string $request - the param or a parampath
	 * 
	 * @return boolean|null - boolean if param valid else null
	 */
	public function has( $request ) {

		$info = $this->getParamInfo( (array) $request );

		if ( !$info["isBag"] && !$info["params"] && $info["parent"] )
			return null;

		// when value is a bag use parents param else the requested scope
		return array_key_exists( $info["paramKey"], ($info["isBag"]) ? $info["parent"] : $info["params"] );
	}
	
	/**
	 * returns boolean wether null or not
	 * 
	 * @param array|string - the param or param path
	 * 
	 * @return boolean|null - boolean on valid path else null
	 */
	public function isNull( $request ) {

		$info = $this->getParamInfo( (array) $request );

		return $info["paramKey"] && is_null($info["value"]);
	}

	/**
	 * returns all keys of the requested param
	 * 
	 * @param array|string - the param or param path
	 * 
	 * @return array|null - array containing keys else null on invalid param
	 */
	public function keys( $request = array() ) {

		$info = $this->getParamInfo( (array) $request );

		// only when $this params are requested
		// or the requested value is a bag
		return ( $info["isBag"] || !$info["paramKey"] ) ? array_keys($info["params"]) : null;
	}
	
	/**
	 * merges the given params over the params of the requested bag
	 * 
	 * @param array|string - the param or parampath
	 * 
	 * @return null - when access is denied
	 * @return $this - when merge was successful
	 */
	public function merge( $request, ...$params ) {

		if ( !$params )
			return $this;

		$info = $this->getParamInfo( (array) $request );
		
		if ( !$this->verifyAccess( (string) $info["paramKey"]) )
			return null;

		// only when bag or $this scope
		if ( !is_null($request) && !$info["isBag"] )
			return null;
		
		$info[ "params" ] = array_merge( $info["params"], call_user_func_array("array_merge", $params) );

		return $this;
	}

	/**
	 * returns the requested param value by reference
	 * 
	 * @param array|string - the param or the parampath
	 * 
	 * @return null - when access is denied
	 * @return mixed - the reference on the requested param
	 */
	public function &ref( $request ) {

		$info = $this->getParamInfo( (array) $request );
		
		if ( !$this->verifyAccess($info["paramKey"]) )
			return null;
		
		return $this->getParamInfo( (array) $request )[ "value" ];
	}

	/**
	 * removes a param
	 * 
	 * @param array|string - the param or the parampath
	 * 
	 * @return null - when access is denied
	 * @return mixed - when item removal was successful
	 */
	public function remove( $request ) {

		$info = $this->getParamInfo( (array) $request );
		
		if ( !$this->verifyAccess($info["paramKey"]) )
			return null;

		// only when bag or $this scope
		if ( !$info["paramKey"] )
			return null;
		
		// get correct scope cause the "params" key refers to params
		// depending wether the value is a bag or not
		$scope = null;

		if ( $info["isBag"] )
			$scope = &$info["parent"];
		else
			$scope = &$info["params"];

		$removed = $scope[$info["paramKey"]];

		unset( $scope[$info["paramKey"]] );

		return $removed;
	}

	/**
	 * replaces the requested params with the given ones
	 * 
	 * @param array|string $request - the param or the param path
	 * @param array $params - the params
	 * 
	 * @return null - when access has been denied
	 * @return $this - when replacement was successful
	 */
	public function replace( $request, array $params ) {

		$info = $this->getParamInfo( (array) $request );
		
		if ( !$this->verifyAccess($info["paramKey"]) )
			return null;

		// only when bag or $this scope
		if ( !is_null($request) && !$info["isBag"] )
			return null;
		
		$info[ "params" ] = $params;

		return $this;
	}

	/**
	 * merges the given params under the params of the requested bag
	 * 
	 * @param array|string - the param or parampath
	 * 
	 * @return null - when access has been denied
	 * @return $this - when merge was successful
	 */
	public function rMerge( $request, ...$params ) {

		if ( !$params )
			return $this;

		$info = $this->getParamInfo( (array) $request );
		
		if ( !$this->verifyAccess($info["paramKey"]) )
			return null;

		// only when bag or $this scope
		if ( !is_null($request) && !$info["isBag"] )
			return null;
			
		$info[ "params" ] = array_merge( call_user_func_array("array_merge", array_reverse($params)), $info["params"] );

		return $this;
	}

	/**
	 * sets the value of the parameter value
	 * 
	 * @param array|string - the param or the parampath
	 * 
	 * @return $this|null - null on invalid param else $this
	 * 	Note! $this is the scope of the called class NOT the requested param
	 */
	public function set( $request, $value ) {
		
		$info = $this->getParamInfo( (array) $request );

		if ( !$this->verifyAccess($info["paramKey"]) )
			return null;

		// when paramkey is empty - invalid request
		if ( !$info["paramKey"] )
			return null;
		
		// get correct scope cause the "params" key refers to params
		// depending wether the value is a bag or not
		$scope = null;

		if ( $info["isBag"] )
			$scope = &$info["parent"];
		else
			$scope = &$info["params"];
		
		$scope[ $info["paramKey"] ] = $value;

		return $this;
	}

	/**
	 * returns the values of the requested params
	 * 
	 * @param array|string $request - the param or the param path
	 * 
	 * @return array|null - the values or null on invalid param
	 */
	public function values( $request = array() ) {

		$info = $this->getParamInfo( (array) $request );

		// only when $this params are requested
		// or the requested value is a bag
		return ( $info["isBag"] || !$info["paramKey"] ) ? array_values($info["params"]) : null;
	}

	/**
	 * verifies the reading access of this bag
	 * 
	 * @param string $key - the requested param key
	 */
	private function verifyAccess( string $key ) {
		
		return !$this->_readonly && !in_array($key, $this->_blocked);
	}

}

//_____________________________________________________________________________________________
//