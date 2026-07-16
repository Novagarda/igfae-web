<?php
class IntranetScientificFirm
{
	public $_scientificFirmId        = NULL;
    public $_firm                   = NULL;
    
    

	public function __construct($scientificFirm = NULL, $name = NULL)
	{
		$this->_scientificFirmId 		                = $scientificFirm;
        $this->_firm 		                            = $name;
        
	}	

	/**
	 * Get the value of _scientificFirmId
	 */ 
	public function getScientificFirmId()
	{
		return $this->_scientificFirmId;
	}

	/**
	 * Set the value of _scientificFirmId
	 *
	 * @return  self
	 */ 
	public function setScientificFirmId($_scientificFirmId)
	{
		$this->_scientificFirmId = $_scientificFirmId;

		return $this;
	}

    /**
     * Get the value of _firm
     */ 
    public function getFirm()
    {
        return $this->_firm;
    }

    /**
     * Set the value of _firm
     *
     * @return  self
     */ 
    public function setFirm($_firm)
    {
        $this->_firm = $_firm;

        return $this;
    }
}
