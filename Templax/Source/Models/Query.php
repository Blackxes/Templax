<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	query model
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Models;

require_once ( TEMPLAX_ROOT . "/Source/Models/Rule.php" );

//_____________________________________________________________________________________________
class Query extends namespace\Rule {

	//_________________________________________________________________________________________
	// Todo: header comment of constructor
	//
	public function __construct( namespace\Process $process, namespace\Rule $rule,
		string $template, namespace\Query $postQuery = null )
	{

		parent::__construct(
			$rule->getId(), $rule->getRawRule(), $rule->getRequest(),
			$rule->getKey(), $rule->getValue(), $rule->getCommandValue(), $rule->getOptions()
		);

		$this->process = $process;
		$this->template = $template;
		$this->postQuery = $postQuery;
		$this->context = "";
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setProcess( namespace\Process $process ) { $this->process = $process; }
	public function setTemplate( string $template ) { $this->template = $template; }
	public function setPostQuery( namespace\Query $isPostQuery ) { $this->postQuery = $postQuery; }
	public function setContext( string $context ) { $this->context = $context; }
	//
	public function getProcess(): namespace\Process { return $this->process; }
	public function getTemplate(): string { return $this->template; }
	public function getPostQuery(): namespace\Query { return $this->postQuery; }
	public function getIsPostQuery(): bool { return $this->postQuery !== null; }
	public function getContext(): string { return $this->context; }
}

//_____________________________________________________________________________________________
//

