<?php
class IntranetEventSubType {
	public $_eventSubTypeId		           = NULL;
	public $_eventTypeID                    = NULL;
    public $_eventSubTypeName               = NULL;
    
    public function __construct( $eventSubTypeId = NULL, $evenTypeId = NULL, $name = NULL) {
		$this->_eventSubTypeId	 		                = $eventSubTypeId; 	
		$this->_eventTypeID	 		                    = $evenTypeId;
        $this->_eventSubTypeName 		                = $name; 	
     

	}

	public function getEventSubTypeId()
	{
		return $this->_eventSubTypeId;
	}
	public function getEventTypeId()
	{
		return $this->_eventTypeID;
	}
	public function getEventSubTypeName()
	{
		return $this->_eventSubTypeName;
	}
	/**
	 * Set the value of _eventSubTypeId
	 *
	 * @return  self
	 */ 
	public function setEventSubTypeId($_eventSubTypeId)
	{
		$this->_eventSubTypeId = $_eventSubTypeId;

		return $this;
	}
	/**
	 * Set the value of _eventTypeId
	 *
	 * @return  self
	 */ 
	public function setEventTypeId($_eventTypeID)
	{
		$this->_eventTypeID = $_eventTypeID;

		return $this;
	}
/**
	 * Set the value of _eventSubTypeName
	 *
	 * @return  self
	 */ 
	public function setEventSubTypeName($_eventSubTypeName)
	{
		$this->_eventSubTypeName = $_eventSubTypeName;

		return $this;
	}
}
