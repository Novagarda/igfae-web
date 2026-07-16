<?php
class IntranetActivityType
{
	public $_activityTypeId		        = NULL;
	public $_activityType                = NULL;

	public function __construct($activityId = NULL, $activityType = NULL)
	{
		$this->_activityTypeId 		                    = $activityId;
		$this->_activityType 		                    = $activityType;
	}

	
	public function getActivityTypeId()
	{
		return $this->_activityTypeId;
	}
	public function getActivityType()
	{
		return $this->_activityType;
	}
}
