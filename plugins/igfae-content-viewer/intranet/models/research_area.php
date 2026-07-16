<?php
class IntranetResearchArea {
    protected $_researchAreaId		            = NULL;
    protected $_researchAreaName                = NULL;
    
    public function __construct( $researchId = NULL, $name = NULL) {
        $this->_researchAreaId 		                    = $researchId; 	
        $this->_researchAreaName 		                = $name; 	
     

	}

	public function getResearchAreaId()
	{
		return $this->_researchAreaId;
	}
	public function getResearchAreaName()
	{
		return $this->_researchAreaName;
	}

}
