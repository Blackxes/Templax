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
class Query extends \Templax\Source\Models\Rule {

	//_________________________________________________________________________________________
	public function __construct( $process, \Templax\Source\Models\Rule $rule, $template, $isPostQuery ) {

		parent::__construct(
			$rule->getId(), $rule->getRawRule(), $rule->getRequest(),
			$rule->getKey(), $rule->getValue(), $rule->getCommandValue(), $rule->getOptions()
		);

		$this->process = $process;
		$this->template = $template;
		$this->isPostQuery = $isPostQuery;
		$this->context = "";
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setProcess( $process ) { $this->process = $process; }
	public function setTemplate( $template ) { $this->template = $template; }
	public function setIsPostQuery( $isPostQuery ) { $this->isPostQuery = $isPostQuery; }
	public function setContext( $context ) { $this->context = $context; }
	//
	public function getProcess() { return $this->process; }
	public function getTemplate() { return $this->template; }
	public function getIsPostQuery() { return $this->isPostQuery; }
	public function getContext() { return $this->context; }
}

//_____________________________________________________________________________________________
//

