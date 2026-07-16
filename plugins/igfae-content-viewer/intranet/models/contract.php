<?php
class IntranetContract
{
	public $_contractId		        = NULL;
	public $_name                   = NULL;

	public function __construct($contractId = NULL, $name = NULL)
	{
		$this->_contractId 		                	= $contractId;
		$this->_name 		                        = $name;
	}


	/**
	 * Get the value of _contractId
	 */ 
	public function getContractId()
	{
		return $this->_contractId;
	}	

	/**
	 * Get the value of _name
	 */ 
	public function getName()
	{
		return $this->_name;
	}

	
}
