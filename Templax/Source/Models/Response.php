<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	response model
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class Response {

	private $replacement;
	private $value;
	private $postQuery;
	private $indexOffset; // regex offset of the main template content regex

	//_________________________________________________________________________________________
	public function __construct( $replacement = "", $value = "", \Templax\Source\Models\Query $postQuery = null, $offset = null )
	{
		$this->replacement = $replacement;
		$this->value = $value;
		$this->postQuery = $postQuery;
		$this->offset = $offset;
		$this->context = "";

		if ( $this->postQuery )
			$this->postQuery->setIsPostQuery(true);
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setReplacement( $replacement ) { $this->replacement = $replacement; }
	public function setValue( $value ) { $this->value = $value; }
	public function setPostQuery( \Templax\Query $postQuery ) { $this->postQuery = $postQuery; }
	public function setOffset( $offset ) { $this->offset = $offset; }
	public function setContext( $context ) { $this->context = $context; }
	//
	public function getReplacement() { return $this->replacement; }
	public function getValue() { return $this->value; }
	public function getPostQuery() { return $this->postQuery; }
	public function getOffset() { return $this->offset; }
	public function getContext() { return $this->context; }
	//
	public function hasOffset() { return $this->offset !== null; }
	//
	public function isPostQuery() { return (bool) $this->postQuery; }
}

//_____________________________________________________________________________________________
//