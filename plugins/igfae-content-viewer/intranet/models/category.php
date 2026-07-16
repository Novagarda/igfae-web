<?php
class IntranetCategory
{
	public $_categoryId		        = NULL;
	public $_name                   = NULL;

	public function __construct($categoryId = NULL, $name = NULL)
	{
		$this->_categoryId 		                = $categoryId;
		$this->_name 		                    = $name;
	}


	/**
	 * Get the value of _categoryId
	 */ 
	public function getCategoryId()
	{
		return $this->_categoryId;
	}	

	/**
	 * Get the value of _name
	 */ 
	public function getName()
	{
		return $this->_name;
	}

	
}
class IntranetCategoryType
{
	public $_categoryTypeId		        = NULL;
	public $_name                   = NULL;

	public function __construct($categoryId = NULL, $name = NULL)
	{
		$this->_categoryTypeId 		                = $categoryId;
		$this->_name 		                    	= $name;
	}


	/**
	 * Get the value of _categoryId
	 */ 
	public function getCategoryTypeId()
	{
		return $this->_categoryTypeId;
	}	

	/**
	 * Get the value of _name
	 */ 
	public function getName()
	{
		return $this->_name;
	}

	
}
