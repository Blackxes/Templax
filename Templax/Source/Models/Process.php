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
	//
	// param1 (int) expects the process id
	// param2 (\Templax\Source\Models\Templax) expects the template
	// param3 (array) expects the user markup
	// param4 (array) expects the options for the template
	// 		these options are aggressive ones - you have defaults ones you define
	//		when registering a template and these options are the ones you pass when invoking
	//		the parse function of the templax instance
	// param5 (bool) describes wether this process is a subprocess of another one
	//
	public function __construct( int $id, namespace\Template $template, array $userMarkup = array(),
		array $options = array(), bool $isSubProcess = false )
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
	public function setId( int $id ) { $this->id = $id; }
	public function setTemplate( namespace\Template $template ) { $this->template = $template; }
	public function setUserMarkup( array $userMarkup ) { $this->userMarkup = $userMarkup; }
	public function setQueryMarkup( array $queryMarkup ) { $this->queryMarkup = $queryMarkup; }
	public function setOptions( array $options ) { $this->options = $options; }
	public function setCurrentQuery( namespace\Query $query = null ) { $this->currentQuery = $query; }
	public function setIsSubProcess( bool $isSubProcess ) { $this->isSubProcess = $isSubProcess; }
	public function setParentProcess( namespace\Process $process ) { $this->parentProcess = $process; }
	//
	public function getId(): int { return $this->id; }
	public function getTemplate(): namespace\Template { return $this->template; }
	public function getUserMarkup(): array { return $this->userMarkup; }
	public function getQueryMarkup(): array { return $this->queryMarkup; }
	public function getOptions(): array { return $this->options; }
	public function getOption( $option ) { return $this->options[$option]; }
	public function getCurrentQuery(): namespace\Query { return $this->currentQuery; }
	public function getIsSubProcess(): bool { return $this->isSubProcess; }
}

//_____________________________________________________________________________________________
//