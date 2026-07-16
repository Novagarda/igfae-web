<?php
class IntranetExternalDirector {
    protected $_externalDirectorId		                = NULL;
    protected $_name                                    = NULL;
    
    public function __construct( $externalDirectorId = NULL, $name = NULL) {
        $this->_externalDirectorId 		                    = $externalDirectorId; 	
        $this->_name 		                                = $name; 		
     

	}

	public function getExternalDirectorId()
	{
		return $this->_externalDirectorId;
	}
	public function getName()
	{
		return $this->_name;
	}
}
