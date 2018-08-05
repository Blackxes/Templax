<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	rule model
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class Rule {

	private $id;
	private $rawRule;
	private $request;
	private $key;
	private $value;
	private $commandValue;
	private $options;

	//_________________________________________________________________________________________
	// Todo: header comment of constructor
	//
	public function __construct( int $id, string $rawRule, string $request,
		$key = "", $value = "", $commandValue = "", array $options = array() )
	{
		$this->id = $id;
		$this->rawRule = $rawRule;
		$this->request = $request;
		$this->key = $key;
		$this->value = $value;
		$this->commandValue = $commandValue;
		$this->options = $options;
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setId( int $id ) { $this->id = $id; }
	public function setRawRule( string $rawRule ) { $this->rawRule = $rawRule; }
	public function setRequest( string $request ) { $this->request = $request; }
	public function setKey( string $key ) { $this->key = $key; }
	public function setValue( $value ) { $this->value = $value; }
	public function setCommandValue( $value ) { $this->commandValue = $value; }
	public function setOptions( array $options ) { $this->options = $options; }
	public function setOption( string $key, $value ) { $this->options[$key] = $value; }
	//
	public function getId(): int { return $this->id; }
	public function getRawRule(): string { return $this->rawRule; }
	public function getRequest(): string { return $this->request; }
	public function getKey() { return $this->key; }
	public function getValue() { return $this->value; }
	public function getCommandValue() { return $this->commandValue; }
	public function getOptions(): array { return $this->options; }
	public function getOption( $option ) { return $this->options[$option]; }
	//
}

//_____________________________________________________________________________________________
//