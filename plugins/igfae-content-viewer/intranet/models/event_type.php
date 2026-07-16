<?php
class IntranetEventType {
    protected $_eventTypeId		            = NULL;
    protected $_eventTypeName               = NULL;
    
    public function __construct( $eventTypeId = NULL, $name = NULL) {
        $this->_eventTypeId	 		                = $eventTypeId; 	
        $this->_eventTypeName 		                = $name; 	
     

	}

	public function getEventTypeId()
	{
		return $this->_eventTypeId;
	}
	public function getEventTypeName()
	{
		return $this->_eventTypeName;
	}

}
