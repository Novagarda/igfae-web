<?php
class IntranetInstitution {
    protected $_institutionID		            = NULL;
    protected $_institutionName                = NULL;
    
    public function __construct( $institutionID = NULL, $name = NULL) {
        $this->_institutionID 		                    = $institutionID; 	
        $this->_institutionName 		                = $name; 	
     

	}

	public function getInstitutionID()
	{
		return $this->_institutionID;
	}
	public function getInstitutionName()
	{
		return $this->_institutionName;
	}

}
