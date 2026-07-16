<?php
class IntranetSocialNetwork
{
	public $_socialNetworkId        = NULL;
    public $_name                   = NULL;
    

	public function __construct($socialNetworkId = NULL, $name = NULL)
	{
		$this->_socialNetworkId 		                = $socialNetworkId;
        $this->_name 		                            = $name;
        
	}



	/**
	 * Get the value of _socialNetworkId
	 */ 
	public function getSocialNetworkId()
	{
		return $this->_socialNetworkId;
	}

	/**
	 * Set the value of _socialNetworkId
	 *
	 * @return  self
	 */ 
	public function setSocialNetworkId($_socialNetworkId)
	{
		$this->_socialNetworkId = $_socialNetworkId;

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
