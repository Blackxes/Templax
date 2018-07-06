<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	query model
	
	@Author: Alexander Bassov
	@Email: blackxes@gmx.de
	@Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Templax\Source\Models;

require_once ( TEMPLAX_ROOT . DS . "Source" . DS . "Models" . DS . "Rule.php" );

//_____________________________________________________________________________________________
class Query extends \Templax\Source\Models\Rule {

	//_________________________________________________________________________________________
	public function __construct( $processId, \Templax\Source\Models\Rule $rule, $template, $isPostQuery ) {

		parent::__construct(
			$rule->getId(), $rule->getRawRule(), $rule->getRequest(),
			$rule->getKey(), $rule->getValue(), $rule->getOptions()
		);

		$this->processId = $processId;
		$this->template = $template;
		$this->isPostQuery = $isPostQuery;
		$this->context = "";
	}

	//_________________________________________________________________________________________
	// basic setter/getter
	//
	public function setProcessId( $id ) { $this->processId = $id; }
	public function setTemplate( $template ) { $this->template = $template; }
	public function setIsPostQuery( $isPostQuery ) { $this->isPostQuery = $isPostQuery; }
	public function setContext( $context ) { $this->context = $context; }
	//
	public function getProcessId() { return $this->processId; }
	public function getTemplate() { return $this->template; }
	public function getIsPostQuery() { return $this->isPostQuery; }
	public function getContext() { return $this->context; }
}

//_____________________________________________________________________________________________
//

