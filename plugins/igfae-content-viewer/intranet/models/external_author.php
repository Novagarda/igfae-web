<?php
class IntranetExternalAuthor {
    protected $_externalAuthorId		                = NULL;
    protected $_name                                    = NULL;
    protected $_surname                                 = NULL;
    
    public function __construct( $externalAuthorId = NULL, $name = NULL, $surname = NULL) {
        $this->_externalAuthorId 		                    = $externalAuthorId; 	
        $this->_name 		                                = $name; 	
        $this->_surname 		                            = $surname; 	
     

	}

	public function getExternalAuthorId()
	{
		return $this->_externalAuthorId;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function getSurname()
	{
		return $this->_surname;
	}
}
