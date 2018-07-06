<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	manages template processes
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Manager;

require_once ( TEMPLAX_ROOT . DS . "Source" . DS . "Models" . DS . "Process.php" );

//_____________________________________________________________________________________________
class ProcessManager {

	private $processes;
	static private $processIterator;

	//_________________________________________________________________________________________
	public function __construct() {

		$this->processes = array();
		$this->processIterator = 0;
	}

	//_________________________________________________________________________________________
	// creates a new template process
	//
	// param1 (\Templax\Source\Models\Template) expects the template instance
	// param2 (array) expects the user markup
	// param3 (array) expects the options
	//
	// return \Templax\Source\Models\Process
	//
	public function create(
		\Templax\Source\Models\Template $template,
		array $userMarkup = array(),
		array $options = array() )
	{
		$process = new \Templax\Source\Models\Process( ++self::$processIterator, $template, $userMarkup );

		// apply global default, template and passed options
		$process->setOptions( array_merge(
			$GLOBALS["Templax"]["CONFIG"]["PARSING"]["optionSets"]["default"]["template"],
			$template->getOptions(),
			$options)
		);

		return $process;
	}

	//_________________________________________________________________________________________
	// returns the existance of a process as boolean
	//
	// param1 (int) expects the process id
	//
	// return boolean
	//
	public function has( $id ) {
		return (bool) $this->processes[ $id ];
	}

	//_________________________________________________________________________________________
	// returns a reference to a process
	//
	// param1 (int) expects the process id
	//
	// return \Templax\Source\Models\Process
	//
	public function &get( $id ) {
		return $this->processes[ $id ];
	}

	//_________________________________________________________________________________________
	// delete a process
	//
	// param1 (int) expects the process id
	//
	// return boolean
	//
	public function delete( $id ) {
		if ( !$this->has($id) )
			return false;

		unset( $this->processes[$id] );

		return true;
	}
}

//_____________________________________________________________________________________________
//