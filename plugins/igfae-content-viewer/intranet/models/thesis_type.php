<?php
class IntranetThesisType {
    protected $_thesisTypeId		            = NULL;
    protected $_thesisType                      = NULL;
    
    public function __construct( $thesisTypeId = NULL, $thesisType = NULL) {
        $this->_thesisTypeId 		                    = $thesisTypeId; 	
        $this->_thesisType      	                    = $thesisType; 	
     

	}

	public function getThesisTypeId() {
		return $this->_thesisTypeId;
	}
	public function getThesisType() {
		return $this->_thesisType;
	}	

}
