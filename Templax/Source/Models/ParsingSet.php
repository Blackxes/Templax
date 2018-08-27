<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * parsing set model - this one contains information about the following template parse
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class ParsingSet {

	/**
	 * contains the source wether its an template instance or a template id
	 * 
	 * @var string|\Templax\Source\Models\Template|null (but only before construction)
	 */
	public $source;

	/**
	 * the markup for this parse run
	 * 
	 * @var array|null
	 */
	public $markup;

	/**
	 * the options for this parse run
	 * 
	 * @var array|null
	 */
	public $options;

	/**
	 * stores the parent process of this template process
	 * 
	 * @var null|\Templax\Source\Models\Process
	 */
	public $parentProcess;

	/**
	 * describes wether this parsing set is valid / initially true
	 * 
	 * @var boolean
	 */
	public $valid = true;

	/**
	 * construction
	 * 
	 * @param
	 */
	public function __construct( $source, ?array $markup, ?array $options, namespace\Process $parentProcess = null ) {
		
		$this->source = $source;
		$this->markup = is_null($markup) ? array() : $markup;
		$this->options = is_null($options) ? array() : $options;
		$this->parentProcess = $parentProcess;

		$this->valid = $this->Init();
	}

	/**
	 * initializes this parsing set
	 * 
	 * @return boolean
	 */
	public function Init() {

		// when the source is null
		if ( is_null($this->source) )
			return false;
		
		// it either has to be a string or a template instance
		//
		// when string treat either as ..
		if ( is_string($this->source) ) {
			
			// .. a subtemplate with no id but content ..
			if ( !\Templax\Templax::$tManager->has($this->source) )
				$this->source = new namespace\Template( null, $this->source );
			
			// .. or an existing template id
			else
				$this->source = \Templax\Templax::$tManager->get($this->source);
		}

		// when not even an instance is given its invalid
		else if ( !is_a($this->source, "\Templax\Source\Models\Template") )
			return false;
		
		// when a template instance - validate
		else
			return $this->source->validate();
		
		return true;
	}
}

//_____________________________________________________________________________________________
//