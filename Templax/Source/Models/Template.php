<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	template model
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Models;

require_once ( TEMPLAX_ROOT . "/Source/Manager/TemplateManager.php" );

//_____________________________________________________________________________________________
class Template {

	private $id;
	private $value; // actual template string
	private $markup; // default markup for this template
	private $options; // default options for this template

	//_________________________________________________________________________________________
	public function __construct( $id, $value, array $markup = array(), array $options = array() ) {

		$this->id = $id;
		$this->value = $value;
		$this->markup = $markup;
		$this->options = $options;
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setId( $id ) { $this->id = $id; }
	public function setValue( $value ) { $this->value = $value; }
	public function setMarkup( $markup ) { $this->markup = $markup; }
	public function setOptions( $options ) { $this->options = $options; }

	public function getId() { return $this->id; }
	public function getValue() { return $this->value; }
	public function getMarkup() { return $this->markup; }
	public function getOptions() { return $this->options; }
	//	
}

//_____________________________________________________________________________________________
//