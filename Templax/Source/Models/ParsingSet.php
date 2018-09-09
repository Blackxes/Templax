<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * parsing set model
 * information set for the template processor function to work with
 * 
 * it contains the fundamental values to complete a template parsing without errors
 * 
 * when this set is invalid - the processing doesnt start
 * 
 * @author: Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Models/BaseProcessSet.php" );

//_____________________________________________________________________________________________
class ParsingSet extends namespace\BaseProcessSet {

	/**
	 * parent process
	 */
	protected $parent;

	/**
	 * the template instance
	 * 
	 * @var \Templax\Source\Models\Template
	 */
	protected $template;

	/**
	 * construction
	 * 
	 * @param \Templax\Source\Models\ParsingSet|\Templax\Source\Models\Template|string $source
	 * 	ParsingSet - the parsing set this instance adapts
	 * 	Template - a template instance
	 * 	string - template id or value / when id not found in the manager value is interpreted as template value
	 * @param array|null $markup - the markup for this set
	 * @param array|null $options - the options for this set
	 */
	public function __construct( $source, array $markup = array(), array $options = array(), namespace\Process $parent = null ) {


		// when a set itself overwrite this set with the given one
		if ( is_a($source, "\Templax\Source\Models\ParsingSet") ) {
			foreach( get_object_vars($source) as $key => $value ) {

				// check set existance
				$func = "set" . ucfirst($key);

				// var_dump($func, gettype($value) );

				if ( method_exists($this, $func) )
					$this->$func( $value );
			}
		}

		// else initialize
		else
			$this->Init( $source, $markup, $options, $parent );
	}

	/**
	 * validates the source of this parsing set
	 * 
	 * @return boolean - true on valid otherwise false
	 */
	private function buildSource( $source ) {

		// simple null check
		if ( is_null($source) )
			return null;

		// check if its a template instance
		if ( !is_string($source) ) {
			if ( is_a($source, "\Templax\Source\Models\Template") && $source->verify() )
				return $source;
		}
		
		// if its not empty and registered
		// get the template
		else if ( \Templax\Templax::$tManager->has($source) )
			return \Templax\Templax::$tManager->get($source);

		// otherwise create a new template with the source as value
		else
			return new namespace\Template( null, $source );
	}

	/**
	 * initializes this parsing set
	 * 
	 * Todo: complete function comment
	 */
	public function Init( $source, array $markup = array(), array $options = array(), namespace\Process $parent = null ) {
		
		// define the rest values
		$this->markup = $markup;
		$this->options = $options;
		$this->parent = $parent;
		
		$this->template = $this->buildSource( $source );

		return true;
	}

	/**
	 * returns the parent process
	 * 
	 * @return \Templax\Source\Models\Process|null - null when no parent is defined
	 */
	public function getParent() {

		return $this->parent;
	}
	
	/**
	 * returns the template otherwise null if not defined
	 * 
	 * @return \Templax\Source\Models\Template|null
	 */
	public function getTemplate() {

		return $this->template;
	}

	/**
	 * sets the parent process
	 * 
	 * @param \Templax\Source\Models\Process $parent - the parent process
	 * 
	 * @return $this
	 */
	public function setParent( namespace\Process $parent = null ) {

		$this->parent = $parent;

		return $this;
	}

	/**
	 * sets the template
	 * 
	 * @param \Templax\Source\Models\Template $template - the template
	 * 
	 * @return $this
	 */
	public function setTemplate( namespace\Template $template ) {

		$this->template = $template;

		return $this;
	}

	/**
	 * validates this set and returns true on valid otherwise false
	 * 
	 * @return boolean - true on valid otherwise false
	 */
	public function verify() {

		$a = !is_null($this->template);
		$b = is_a( $this->template, "\Templax\Source\Models\Template");
		$c = $this->template->verify();

		return $a || $b || $c;
		
		// it only depends on the source wether this set is valid or not
		return !is_null($this->template) || is_a( $this->template, "\Templax\Source\Models\Template") || !$this->template->verify();
	}
}

//_____________________________________________________________________________________________
//