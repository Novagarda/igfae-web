<?php
class IntranetWorkline {
    protected $_worklineId		            = NULL;
    protected $_worklineName                = NULL;
    
    public function __construct( $worklineId = NULL, $name = NULL) {
        $this->_worklineId 		                    = $worklineId; 	
        $this->_worklineName 		                = $name; 	
     

	}

	public function getWorklineId()
	{
		return $this->_worklineId;
	}
	public function getWorklineName()
	{
		return $this->_worklineName;
	}

}
