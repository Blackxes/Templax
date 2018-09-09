<?php

//_____________________________________________________________________________________________
/**********************************************************************************************
 * 
 * process model
 * 
 * @author Alexander Bassov
 * 
/*********************************************************************************************/

namespace Templax\Source\Models;

require_once( TEMPLAX_ROOT . "/Source/Models/ParsingSet.php" );

//_____________________________________________________________________________________________
class Process extends namespace\ParsingSet {

	/**
	 * current query
	 * 
	 * @var \Templax\Source\Models\Query
	 */
	private $currentQuery;

	/**
	 * the process id
	 * 
	 * @var int
	 */
	private $id;
	
	/**
	 * construction
	 * 
	 * @param int $id - process id
	 * @param \Templax\Source\Models\ParsingSet $set - the set for the template processing
	 */
	public function __construct( int $id, namespace\ParsingSet $set ) {

		parent::__construct( $set );

		// var_dump($this->parent);

		$this->id = $id;
		
		// premerge the defaults
		$this->rMergeOptions( $GLOBALS["Templax"]["Defaults"]["Process"]["BaseOptions"] );
		$this->rMergeOptions( $this->template->getOptions() );
		$this->rMergeMarkup( $this->template->getMarkup() );
	}

	/**
	 * returns the process id
	 * 
	 * @return int
	 */
	public function getId() {

		return $this->id;
	}

	/**
	 * returns the template
	 * 
	 * @var \Templax\Source\Models\Template
	 */
	public function getTemplate() {

		return $this->template;
	}

	/**
	 * returns the next template of the parents or even this one
	 * which have a valid template id and serve as a root template
	 * 
	 * @return \Templax\Source\Models\Template
	 */
	public function getNextRootTemplate() {

		// when no parent exists or this template is a root itself
		if ( is_null($this->parent) || \Templax\Templax::$tManager->has($this->template->getId()) )
			return $this->template;
		
		// or when valid get the parents template
		else if ( !$this->parent->template->isSub() )
			return $this->parent->template;
		
		// one of the parents has a valid template.. so go for it
		return $this->parent->getNextRootTemplate();
	}

	/**
	 * returns the current query
	 * 
	 * @return \Templax\Source\Models\Query|null
	 */
	public function getCurrentQuery() {
		
		return $this->currentQuery;
	}

	/**
	 * returns a reference to the current query
	 * 
	 * @return \Templax\Source\Models\Query|null - null when no query is defined
	 */
	public function &getCurrentQueryRef() {

		return $this->currentQuery;
	}

	/**
	 * returns true when this process is the main process otherwise false
	 * 
	 * @return boolean
	 */
	public function isMainProcess() {

		// the main process has always the id 0
		return !( (bool) $this->id );
	}

	/**
	 * sets the current query
	 * 
	 * @param \Templax\Source\Models\Query $query - query
	 * 
	 * @return $this
	 */
	public function setCurrentQuery( namespace\Query $query ) {

		$this->currentQuery = $query;
		return $this;
	}
}

//_____________________________________________________________________________________________
//