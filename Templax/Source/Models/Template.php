<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	template model
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class Template {

	private $id;
	private $value; // actual template string
	private $markup; // default markup for this template
	private $options; // default options for this template

	//_________________________________________________________________________________________
	public function __construct( $id, string $value, array $markup = array(),
		array $options = array() )
	{

		$this->id = $id;
		$this->value = $value;
		$this->markup = $markup;
		$this->options = $options;
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setId( string $id ) { $this->id = $id; }
	public function setValue( string $value ) { $this->value = $value; }
	public function setMarkup( array $markup ) { $this->markup = $markup; }
	public function setOptions( array $options ) { $this->options = $options; }

	public function getId() { return $this->id; }
	public function getValue(): string { return $this->value; }
	public function getMarkup(): array { return $this->markup; }
	public function getOptions(): array { return $this->options; }
	//	
}

//_____________________________________________________________________________________________
//