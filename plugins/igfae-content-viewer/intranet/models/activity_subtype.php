<?php
class IntranetSubActivityType
{
	protected $_activitySubTypeId		        = NULL;
	protected $_activitySubType                = NULL;
	protected $_code 				            = NULL;

	public function __construct($activitySubTypeId = NULL, $activitySubType = NULL, $code = NULL)
	{
		$this->_activitySubTypeId 		                    = $activitySubTypeId;
		$this->_activitySubType 		                    = $activitySubType;
		$this->_code										= $code;
	}

	
	public function getActivitySubTypeId()
	{
		return $this->_activitySubTypeId;
	}
	public function getActivitySubType()
	{
		return $this->_activitySubType;
	}
	public function getCode()
	{
		return $this->_code;
	}
}
