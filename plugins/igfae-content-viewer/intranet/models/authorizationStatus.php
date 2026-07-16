<?php
class IntranetAuthorizationStatus
{
	public $_id		        = NULL;
	public $_status                   = NULL;

	public function __construct($id = NULL, $status = NULL)
	{
		$this->_id 		                = $id;
		$this->_status 		            = $status;
	}



	/**
	 * Get the value of _id
	 */ 
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Set the value of _id
	 *
	 * @return  self
	 */ 
	public function setId($_id)
	{
		$this->_id = $_id;

		return $this;
	}


	/**
	 * Get the value of _status
	 */ 
	public function getStatus()
	{
		return $this->_status;
	}

	/**
	 * Set the value of _status
	 *
	 * @return  self
	 */ 
	public function setStatus($_status)
	{
		$this->_status = $_status;

		return $this;
	}
}
