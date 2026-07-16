<?php
class IntranetScope
{
	protected $_scopeId		        = NULL;
	protected $_scopeName           = NULL;

	public function __construct($scopeId = NULL, $scopeName = NULL)
	{
		$this->_scopeId 		                = $scopeId;
		$this->_scopeName 		                = $scopeName;
	}

	
	public function getScopeId()
	{
		return $this->_scopeId;
	}
	public function getScopeName()
	{
		return $this->_scopeName;
	}
}