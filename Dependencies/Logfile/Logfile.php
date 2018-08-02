<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	logs message and returns them on call
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Logfile;

use Logfile\Source\Models;

const LOGFILE_ROOT = __DIR__;
const DS = DIRECTORY_SEPARATOR;

require_once ( __DIR__ . DS . "Configuration.php" );
require_once ( __DIR__ . DS . "Source". DS . "Models" . DS . "Log.php" );

//_____________________________________________________________________________________________
class Logfile {

	private $logs;

	//_________________________________________________________________________________________
	public function __construct() {
		$this->logs = array();
	}

	//_________________________________________________________________________________________
	// logs a simple message and returns the given values
	//
	// param1 (string) expects the logging message
	// param2 (mixed) expects the return value
	//
	// return mixed
	//
	public function logReturn( $message, $return ) {
		
		$this->register( new Models\Log( $message) );
		return $return;
	}

	//_________________________________________________________________________________________
	// logs a simple message and returns the status of the insertion
	//
	// param1 (string) expects the message
	//
	// return boolean
	//
	public function log( $message ) {

		$this->register( new Models\Log($message) );
		return (bool) $result;
	}

	//_________________________________________________________________________________________
	// logs a detailed log and returns a value
	//
	// param1 (string) expects the log message
	// param2 (type) expects the log type
	// param3 (code) expects the log code
	// param4 (mixed) expects the return value
	//
	// return mixed
	//
	public function logReturnFull( $message, $type, $code, $return ) {

		$this->register( new Models\Log($message, $type, $code) );
		return $return;
	}

	//_________________________________________________________________________________________
	// logs a detailed message and returns the status of the insertion
	//
	// param1 (string) expects the log message
	// param2 (type) expects the log type
	// param3 (code) expects the log code
	//
	// return boolean
	//
	public function logFull( $message, $type, $code ) {

		$this->register( new Models\Log($message, $type, $code) );
		return (bool) $result;
	}

	//_________________________________________________________________________________________
	// registers a log
	//
	// param1 (\Logfile\Source\Models\Log) expects a log instance
	//
	// return boolean
	//
	private function register( \Logfile\Source\Models\Log $log ) {

		if ( \Logfile\Configuration\LOGFILE_ENABLE )
			return (bool) array_push( $this->logs, $log );
		
		return false;
	}

	//_________________________________________________________________________________________
	// returns all not so far pulled logs
	//
	// return array
	//
	public function getOpenLogs() {
		
		// again as in configuration / why should you use it?
		if ( !\Logfile\Configuration\LOGFILE_RETRIEVE_OPEN )
			return array();

		// loop from behind / set them on closed and return 
		// store log count to avoid calling count() the whole time
		$openLogs = array();

		foreach ( array_reverse($this->logs) as $index => $currentLog ) {
			
			if ( !$currentLog->getOpen() )
				return $openLogs;
			
			$currentLog->setOpen( false );
			array_push( $openLogs, clone($currentLog) );
		}
		
		return $openLogs;
	}

	//_________________________________________________________________________________________
	// returns all inserted logs so far
	//
	// return array
	//
	public function getClosedLogs() {

		// well well - heavy description reader you are see i
		if ( !\Logfile\Configuration\LOGFILE_RETRIEVE_CLOSED )
			return array();
		
		// this time loop from the front and quit when a open appears
		$closedLogs = array();
		foreach( $this->logs as $index => $currentLog ) {
			if ( $currentLog->getOpen() )
				break;
			
			array_push( $closedLogs, clone($currentLog) );
		}
		
		return $closedLogs;
	}
}

//_____________________________________________________________________________________________
//