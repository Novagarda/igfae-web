<?php
class IntranetMapLocation
{
	public $_mapLocationId		        = NULL;
    public $_name                   = NULL;
    

	public function __construct($locationId = NULL, $name = NULL)
	{
		$this->_mapLocationId 		                    = $locationId;
        $this->_name 		                    = $name;
        
	}


	/**
	 * Get the value of _mapLocationId
	 */ 
	public function getMapLocationId()
	{
		return $this->_mapLocationId;
	}

	/**
	 * Set the value of _mapLocationId
	 *
	 * @return  self
	 */ 
	public function setMapLocationId($_mapLocationId)
	{
		$this->_mapLocationId = $_mapLocationId;

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
