<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	process class
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Models;

//_____________________________________________________________________________________________
class Process {

	private $id;
	private $template;
	private $userMarkup;
	private $baseMarkup;
	private $options;
	private $currentQuery;
	private $isSubProcess;

	//_________________________________________________________________________________________
	public function __construct( $id, $template, array $userMarkup = array(),
		array $options = array(), $isSubProcess = false )
	{

		$this->id = $id;
		$this->template = $template;
		$this->userMarkup = $userMarkup;
		$this->queryMarkup = array();
		$this->options = $options;
		$this->currentQuery = null;
		$this->isSubProcess = $isSubProcess;
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	public function setId( $id ) { $this->id = $id; }
	public function setTemplate( \Templax\Source\Models\Template $template ) { $this->template = $template; }
	public function setUserMarkup( array $userMarkup ) { $this->userMarkup = $userMarkup; }
	public function setQueryMarkup( array $queryMarkup ) { $this->queryMarkup = $queryMarkup; }
	public function setOptions( array $options ) { $this->options = $options; }
	public function setCurrentQuery( \Templax\Source\Models\Query $query ) { $this->currentQuery = $query; }
	public function setIsSubProcess( $isSubProcess ) { $this->isSubProcess = $isSubProcess; }

	public function getId() { return $this->id; }
	public function getTemplate() { return $this->template; }
	public function getUserMarkup() { return $this->userMarkup; }
	public function getQueryMarkup() { return $this->queryMarkup; }
	public function getOptions() { return $this->options; }
	public function getOption( $option ) { return $this->options[$option]; }
	public function getCurrentQuery() { return $this->currentQuery; }
	public function getIsSubProcess() { return $this->isSubProcess; }
}

//_____________________________________________________________________________________________
//