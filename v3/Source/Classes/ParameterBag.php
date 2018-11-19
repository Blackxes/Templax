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

// namespace

//_____________________________________________________________________________________________
class ParameterBag {

	/**
	 * value
	 * 
	 * @var mixed
	 */
	protected $params;

	/**
	 * construction
	 * 
	 * @param array $params - initial params
	 */
	protected function __construct( array $params = array() ) {

		$this->params = $params;
	}

	/**
	 * returns all parameter
	 * 
	 * @return array - the params
	 */
	public function all() {

		return $this->params;
	}

	/**
	 * returns a value by key
	 * 
	 * @param string|int $key - the parameter key
	 * 
	 * @return mixed - the requested key
	 */
	public function get( $key ) {
		
		if ( !is_string($key) || !is_int($key) )
			return null;
		
		$value = $this->params[$key];
		
		// check if a hook exists
		if ( is_string($value) && method_exists($this, $value) )
			return $this->$value();
		
		return $value;
	}

	/**
	 * returns the existance of a param as boolean
	 * 
	 * @param string|int $key - the param key
	 * 
	 * @return boolean - true if exists else false
	 */
	public function has( $key ) {

		if ( !is_string($key) || !is_int($key) )
			return null;

		return isset( $this->params[$key] );
	}

	/**
	 * returns all keys
	 * 
	 * @return array - the keys
	 */
	public function keys() {

		return array_keys( $this->params );
	}
	
	/**
	 * merges given params over this ones
	 * 
	 * @param array $params - params
	 * 
	 * @return $this
	 */
	protected function merge( array $params ) {

		$this->params = array_merge( $params, $this->pararms );

		return $this;
	}

	/**
	 * replaces full params with given ones
	 * 
	 * @param array $params - the pararms
	 * 
	 * @return $this
	 */
	protected function replace( array $params ) {

		$this->params = $params;

		return $this;
	}

	/**
	 * merges this params over the given ones
	 * 
	 * @param array - the params
	 * 
	 * @return $this
	 */
	protected function rmerge( arary $params ) {

		$this->params = array_merge( $this->params, $params );

		return $this;
	}

	/**
	 * sets a value by key
	 * 
	 * @param string|int $key - the parameter key
	 * @param mixed $value - the value
	 * 
	 * @return $this
	 */
	protected function set( $key, $value ) {

		if ( !is_string($key) && !is_int($key) )
			return $this;

		$this->params[$key] = $value;

		return $this;
	}

	/**
	 * returns the values
	 * 
	 * @return array - the values
	 */
	public function values() {

		return array_values( $this->params );
	}
}

//_____________________________________________________________________________________________
//