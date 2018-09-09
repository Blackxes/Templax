<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * contains the member that goes along for nearly the whole process time of a template
 * including functionalities
 * 
 * @author Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class BaseProcessSet {

	/**
	 * markup
	 * 
	 * @var array|null
	 */
	protected $markup;

	/**
	 * options
	 * 
	 * @var array|null
	 */
	protected $options;

	/**
	 * construction
	 * 
	 * @param array $markup - the markup for this set
	 * @param array $options - the options for this set
	 */
	public function __construct( array $markup = array(), array $options = array() ) {
		
		$this->markup = $markup;
		$this->options = $options;
	}

	/**
	 * returns the markup
	 * 
	 * @return array
	 */
	public function getMarkup() {

		return $this->markup;
	}

	/**
	 * returns a key of the markup
	 * 
	 * @return mixed|null - null if the item doesnt exist
	 */
	public function getMarkupItem( $key ) {

		return $this->markup[ $key ];
	}

	/**
	 * returns all options
	 * 
	 * @return array
	 */
	public function getOptions() {

		return $this->options;
	}

	/**
	 * returns a option setting
	 * 
	 * @param string $option - the option key
	 * 
	 * @return mixed - null if option doesnt exists
	 */
	public function getOption( $option ) {

		return $this->options[ $option ];
	}

	/**
	 * merges the passed markup with $this markup
	 * 
	 * @param array $markup - the markup
	 * 
	 * @return $this
	 */
	public function mergeMarkup( array $markup ) {

		$this->markup = array_merge( $this->markup, $markup );

		return $this;
	}

	/**
	 * merges the passed option set with $this options
	 * 
	 * @param array $options - the options
	 * 
	 * @return $this
	 */
	public function mergeOptions( array $options ) {

		$this->options = array_merge( $this->options, $options );
		
		return $this;
	}

	/**
	 * sets the markup
	 * 
	 * @param array $markup - the markup
	 * 
	 * @return $this
	 */
	public function setMarkup( array $markup ) {

		$this->markup = $markup;

		return $this;
	}

	/**
	 * sets the options
	 * 
	 * @param array $options - the options
	 * 
	 * @return $this
	 */
	public function setOptions( array $options ) {

		$this->options = $options;

		return $this;
	}

	/**
	 * merges $this markup over the passed markup
	 * 
	 * @param array $markup - the markup
	 * 
	 * @return $this
	 */
	public function rMergeMarkup( array $markup ) {

		$this->markup = array_merge( $markup, $this->markup );

		return $this;
	}

	/**
	 * merges $this options over the passed options
	 * 
	 * @param array $options - the options
	 * 
	 * @return $this
	 */
	public function rMergeOptions( array $options ) {

		$this->options = array_merge( $options, $this->options );

		return $this;
	}
}

//_____________________________________________________________________________________________
//