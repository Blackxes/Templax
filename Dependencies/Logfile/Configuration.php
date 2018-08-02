<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	logfile configuration file / default configurations etc..
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Logfile\Configuration;

// default permission to log messages
const LOGFILE_ENABLE = true;

// permission to retrieve logfile
// dont know why you should set this to false / but i still implemented it
const LOGFILE_RETRIEVE_CLOSED = true;
const LOGFILE_RETRIEVE_OPEN = true;

// permission to display logs
// Todo: implement its use
const LOGFILE_DISPLAY = true;

// template of the log
// define the marker "message", "type" and "code" to make them display
const LOGFILE_DISPLAY_METHOD = "###TYPE### log: ###MESSAGE### with code ###CODE###";

//_____________________________________________________________________________________________
//