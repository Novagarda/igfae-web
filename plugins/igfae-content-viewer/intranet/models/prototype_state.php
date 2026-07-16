<?php
class IntranetPrototypeState
{
	protected $_stateId		        = NULL;
	protected $_stateName         = NULL;

	public function __construct($stateId = NULL, $stateName = NULL)
	{
		$this->_stateId 		                    = $stateId;
		$this->_stateName 		                  = $stateName;
	}

	
	public function getPrototypeStateId()
	{
		return $this->_stateId;
	}
	public function getPrototypeStateName()
	{
		return $this->_stateName;
	}
}
