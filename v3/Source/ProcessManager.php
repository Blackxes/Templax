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
	 * construction
	 */
	public function __construct() {
		
		$this->processes = array();
	}

	/**
	 * creates a new process and registers it afterwards automatically
	 * and returns the process
	 * 
	 * @param \Templax\Source\Models\ParsingSet $set - the parsing set
	 * 
	 * @return \Templax\Source\Models\Process
	 */
	public function &create( \Templax\Source\Models\ParsingSet $set ) {

		// exit;
		
		// manual adjustments
		$process = new Models\Process( self::$pIterator, $set, !(bool) $this->getProcessCount() );

		// every process except the main process has a parent
		if ( !$process->isMainProcess() )
			$process->setParent( $set->getParent() );
		else
			$this->mainProcess = &$process;
		
		// register
		$this->processes[ self::$pIterator ] = $process;

		// increase the process counter
		self::$pIterator++;

		// returns the last inserted process / therefore the current created one
		return $this->getLast();
	}

	/**
	 * deletes a process and returns its success state
	 * 
	 * @param \Templax\Source\Models\Process|int $id - the process id or the process itself
	 * 
	 * @return boolean - true when delete otherwise false
	 */
	public function delete( $_id ) {
		
		// get id
		$id = is_a( $_id, "\Templax\Source\Models\Process" ) ? $_id->getId() : $_id;
		
		unset( $this->processes[$id] );

		// delete main process when nothings left / the main process is always the last one
		// when parsing a template
		if ( !$this->getProcessCount() )
			$this->mainProcess = null;
		
		return !$this->has($id);
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
	 * returns the current count of processes
	 * 
	 * @return int
	 */
	public function getProcessCount() {

		return count( $this->processes );
	}

	/**
	 * returns the existance of a process as boolean
	 * 
	 * @param int $id - the process id
	 * 
	 * @return boolean
	 */
	public function has( int $id ) {

		return isset( $this->processes[$id] ) && !is_null( $this->processes[$id] );
	}

	/**
	 * returns the existance of the main process
	 * 
	 * @return boolean - true when the process exists otherwise false
	 */
	public function mainProcessExists() {

		// the main process has always the id 0
		return !is_null( $this->mainProcess );
	}
}

//_____________________________________________________________________________________________
//