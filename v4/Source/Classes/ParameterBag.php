<?php
//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * provides functional handling with a property
 * also provides hooks for properties when they may need to be parsed beforehand
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Classes;

//_____________________________________________________________________________________________
class ParameterBag {

	/**
	 * params
	 * 
	 * @var array
	 */
	protected $_params;

	protected $_readonly;

	/**
	 * construction
	 * 
	 * @param mixed $params - initial params or parameter bag
	 * 	__NAMESPACE__\ParameterBag - a parameter bag
	 * 	array - params
	 * @param boolean $readonly - defines wether this bag is read only
	 */
	public function __construct( $params = array(), bool $readonly = false ) {

		$this->_params = is_a( $params, __NAMESPACE__ . "\ParameterBag" )
			? $params->all()
			: (array) $params;
		
		$this->_readonly = $readonly;
	}

	/**
	 * returns the parameter of the requested param
	 * 
	 * @param array|string - the param or a parampath
	 * 
	 * @return array|null - null on invalid param else params
	 */
	public function all( $request = array() ) {

		$info = $this->getParamInfo( (array) $request );

		// only when $this params are requested
		// or the requested value is a bag
		return ( $info["isBag"] || !$info["paramKey"] ) ? $info["params"] : null;
	}

	/**
	 * returns the requested param value
	 * 
	 * @param array|string - the param or the parampath
	 * 
	 * @return mixed|null - the value or null on invalid param
	 */
	public function get( $request ) {
		
		return $this->getParamInfo( (array) $request )[ "value" ];
	}

	/**
	 * returns the requested param value by reference
	 * 
	 * @param array|string - the param or the parampath
	 * 
	 * @return mixed|null - the reference or null on invalid param
	 */
	public function &getRef( $request ) {

		if ( $this->_readonly )
			return null;
		
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
		$info = array( "params" => &$this->_params, "value" => null, "parent" => null, "paramKey" => null, "isBag" => false );

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
			$info[ "params" ] = &$param->params;
			$info[ "parent" ] = &$this->_params;
			$info[ "isBag" ] = true;
		}

		// when path not empty check if parmeter bag
		if ( !empty($paramPath) ) {	

			// get deeper request
			if ( $info["isBag"] )
				return array_merge( $param->getParamInfo($paramPath), ["parent" => &$param->params] );

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
	 * @return $this|null - null on invalid param else $this
	 */
	public function merge( $request, ...$params ) {

		if ( $this->_readonly ) return null;

		if ( !$params ) return $this;

		$info = $this->getParamInfo( (array) $request );

		// only when bag or $this scope
		if ( !is_null($request) && !$info["isBag"] )
			return null;
		
		$info[ "params" ] = array_merge( $info["params"], call_user_func_array("array_merge", $params) );

		return $this;
	}

	/**
	 * removes a param
	 * 
	 * @param array|string - the param or the parampath
	 * 
	 * @return mixed|null - the removed item or null on invalid param
	 */
	public function remove( $request ) {

		if ( $this->_readonly )
			return null;

		$info = $this->getParamInfo( (array) $request );

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
	 * @return $this|null - $this or null on invalid param
	 */
	public function replace( $request, array $params ) {

		if ( $this->_readonly )
			return null;

		$info = $this->getParamInfo( (array) $request );

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
	 * @return $this|null - null on invalid param else $this
	 */
	public function rMerge( $request, ...$params ) {
		
		if ( $this->_readonly )
			return null;

		if ( !$params )
			return $this;

		$info = $this->getParamInfo( (array) $request );

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

		if ( $this->_readonly )
			return null;
		
		$info = $this->getParamInfo( (array) $request );

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
}

//_____________________________________________________________________________________________
//