<?php
class IntranetLocation
{
	public $_locationId		        = NULL;
    public $_room                   = NULL;
    public $_buildingId             = NULL;

	public function __construct($locationId = NULL, $name = NULL, $buildingId = NULL)
	{
		$this->_locationId 		                = $locationId;
        $this->_room 		                    = $name;
        $this->_buildingId 		                = $buildingId;
	}

	/**
	 * Get the value of _locationId
	 */ 
	public function getLocationId()
	{
		return $this->_locationId;
	}

	/**
	 * Set the value of _locationId
	 *
	 * @return  self
	 */ 
	public function setLocationId($_locationId)
	{
		$this->_locationId = $_locationId;

		return $this;
	}

    /**
     * Get the value of _room
     */ 
    public function getRoom()
    {
        return $this->_room;
    }

    /**
     * Set the value of _room
     *
     * @return  self
     */ 
    public function setRoom($_room)
    {
        $this->_room = $_room;

        return $this;
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
}
