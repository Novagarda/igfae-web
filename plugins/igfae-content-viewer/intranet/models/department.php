<?php
class IntranetDepartment
{
	public $_departmentId		        = NULL;
	public $_name                   = NULL;

	public function __construct($departmentId = NULL, $name = NULL)
	{
		$this->_departmentId 		                = $departmentId;
		$this->_name 		                        = $name;
	}


	/**
	 * Get the value of _departmentId
	 */ 
	public function getDepartmentId()
	{
		return $this->_departmentId;
	}	

	/**
	 * Get the value of _name
	 */ 
	public function getName()
	{
		return $this->_name;
	}

	
}
