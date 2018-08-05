<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	manages template processes

	template processes are information container about the currently processing template
	
	subtemplates contain their own template process but when refereing to the template id
	it referes to the parents template id - its like an anonymous process
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Manager;

use \Templax\Source\Models;

require_once ( TEMPLAX_ROOT . "/Source/Models/Process.php" );

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
	public function create( Models\Template $template, array $userMarkup = array(),
		array $options = array() ): Models\Process
	{
		$process = new Models\Process( ++self::$processIterator, $template, $userMarkup );
		
		// apply global default, template and passed options
		$process->setOptions( array_merge(
			$GLOBALS["Templax"]["Configuration"]["Parsing"]["optionSets"]["default"]["template"],
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
	public function has( int $id ): bool {

		return (bool) $this->processes[ $id ];
	}

	//_________________________________________________________________________________________
	// returns a reference to a process
	//
	// param1 (int) expects the process id
	//
	// return \Templax\Source\Models\Process
	//
	public function &get( int $id ): Models\Process {

		return $this->processes[ $id ];
	}

	//_________________________________________________________________________________________
	// delete a process
	//
	// param1 (int) expects the process id
	//
	// return boolean
	//
	public function delete( int $id ): bool {

		if ( !$this->has($id) ) return false;

		unset( $this->processes[$id] );

		return true;
	}
}

//_____________________________________________________________________________________________
//