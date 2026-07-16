<?php
class IntranetEntity
{
	public $_entityId		        = NULL;
	public $_name                   = NULL;

	public function __construct($entityId = NULL, $name = NULL)
	{
		$this->_entityId 		                = $entityId;
		$this->_name 		                    = $name;
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

	/**
	 * Get the value of _entityId
	 */ 
	public function getEntityId()
	{
		return $this->_entityId;
	}

	/**
	 * Set the value of _entityId
	 *
	 * @return  self
	 */ 
	public function setEntityId($_entityId)
	{
		$this->_entityId = $_entityId;

		return $this;
	}
}
