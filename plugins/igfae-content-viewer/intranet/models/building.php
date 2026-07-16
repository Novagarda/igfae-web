<?php
class IntranetBuilding
{
	public $_buildingId		        = NULL;
	public $_building                   = NULL;

	public function __construct($buildingId = NULL, $name = NULL)
	{
		$this->_buildingId 		                = $buildingId;
		$this->_building 		                    = $name;
	}


	
	

	/**
	 * Get the value of _buildingId
	 */ 
	public function getBuildingId()
	{
		return $this->_buildingId;
	}

	/**
	 * Set the value of _buildingId
	 *
	 * @return  self
	 */ 
	public function setBuildingId($_buildingId)
	{
		$this->_buildingId = $_buildingId;

		return $this;
	}

	/**
	 * Get the value of _building
	 */ 
	public function getBuilding()
	{
		return $this->_building;
	}

	/**
	 * Set the value of _building
	 *
	 * @return  self
	 */ 
	public function setBuilding($_building)
	{
		$this->_building = $_building;

		return $this;
	}
}
