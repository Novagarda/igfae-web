<?php
class FundingAgency {
    protected $_fundingAgencyID		            = NULL;
    protected $_name                            = NULL;
    
    public function __construct( $fundingAgencyID = NULL, $name = NULL) {
        $this->_fundingAgencyID 		    = $fundingAgencyID; 	
        $this->_name 		                = $name; 	
     

	}

	public function getFundingAgencyId()
	{
		return $this->_fundingAgencyID;
	}
	public function getFundingAgency()
	{
		return $this->_name;
	}

}
