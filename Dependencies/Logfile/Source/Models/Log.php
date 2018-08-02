<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	log class
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Logfile\Source\Models;

//_____________________________________________________________________________________________
class Log {

	private $message;
	private $type;
	private $code;
	private $open;

	//_________________________________________________________________________________________
	public function __construct( $message, $type = null, $code = null ) {

		$this->message = $message;
		$this->type = $type;
		$this->code = $code;
		$this->open = true;
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setMessage( $message ) { $this->message = $message; }
	public function setType( $type ) { $this->type = $type; }
	public function setCode( $code ) { $this->code = $code; }
	public function setOpen( $status ) { $this->open = $status; }
	//
	public function getMessage() { return $this->message; }
	public function getType() { return $this->type; }
	public function getCode() { return $this->code; }
	public function getOpen() { return $this->open; }
	//
}

//_____________________________________________________________________________________________
//
