<?php
class IntranetCountry
{
	public $_code		        = NULL;
	public $_name               = NULL;

	public function __construct($code = NULL, $name = NULL)
	{
		$this->_code 		                = $code;
		$this->_name 		                = $name;
	}


	/**
	 * Get the value of _code
	 */ 
	public function getCode()
	{
		return $this->_code;
	}

	/**
	 * Set the value of _code
	 *
	 * @return  self
	 */ 
	public function setCode($_code)
	{
		$this->_code = $_code;

		return $this;
	}

	/**
	 * Get the value of _name
	 */ 
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Set the value of _name
	 *
	 * @return  self
	 */ 
	public function setName($_name)
	{
		$this->_name = $_name;

		return $this;
	}
}
