<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * manages the processes
 * 
 * @author Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source;

use \Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Models/Process.php" );

//_____________________________________________________________________________________________
class ProcessManager {

	/**
	 * all registered processes
	 * 
	 * @var array
	 */
	private $processes;

	/**
	 * internal process iterator
	 * there was an idea to simple use "count" on the $processes array
	 * but when processes gets deleted the id from "count" would return an existing id
	 * and overwrite a process
	 * 
	 * @var int
	 */
	static private $pIterator = 0;

	/**
	 * default options
	 * 
	 * @var array
	 */
	private $baseOptions;


	/**
	 * construction
	 */
	public function __construct() {
		
		$this->processes = array();

		$this->baseOptions = $GLOBALS["Templax"]["Defaults"]["Process"]["BaseOptions"];
	}

	/**
	 * creates a new process and registers it afterwards automatically
	 * and returns the process
	 * 
	 * @param \Templax\Source\Models\ParsingSet $set - the parsing set
	 * 
	 * @return \Templax\Source\Models\Process
	 */
	public function create( Models\ParsingSet $set ) {

		// no creation when the set is invalid
		if ( !$set->valid )
			return false;
		
		// manual adjustments
		$set->options = array_merge( $this->baseOptions, $set->options );
		
		$process = new Models\Process( self::$pIterator, $set );

		// when no processes are registered so far than this one is the main
		$process->isMainProcess = empty( $this->processes );

		// define the parent when its not a main process
		if ( !$process->isMainProcess )
			$process->parent = $set->parentProcess;
		
		// final registration
		$this->processes[ ++self::$pIterator ] = $process;

		return $this->getLast();
	}

	/**
	 * returns a reference of the requested process instance
	 * 
	 * @param int $id - the process id
	 * 
	 * @return &\Templax\Source\Models\Process|null
	 */
	public function &get( int $id ) {

		if ( !$this->has($id) )
			return null;

		return $this->processes[ $id ];
	}

	/**
	 * returns the last inserted process or null when no processes exist
	 * 
	 * @return &\Templax\Source\Models\Process|null
	 */
	public function &getLast() {

		if ( empty($this->processes) )
			return null;
		
		// since every new process and so the last one always is appended to the end
		// return the last item from the processes
		end($this->processes);

		return $this->processes[ key($this->processes) ];
	}

	/**
	 * returns the existance of a process as boolean
	 * 
	 * @param int $id - the process id
	 * 
	 * @return boolean
	 */
	public function has( int $id ) {

		return isset( $this->processes[$id] ) && !is_null($this->processes[$id]);
	}

	/**
	 * deletes a process and returns its success state
	 * 
	 * @param int $id - the process id
	 * 
	 * @return boolean
	 */
	public function delete( int $id ) {
		
		unset( $this->processes[$id] );
		
		return !$this->has($id);
	}
}

//_____________________________________________________________________________________________
//