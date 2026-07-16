<?php
class IntranetProjectType
{
	protected $_projectTypeId		        = NULL;
	protected $_projectTypeName           = NULL;

	public function __construct($projectTypeId = NULL, $projectTypeName = NULL)
	{
		$this->_projectTypeId 		                = $projectTypeId;
		$this->_projectTypeName 		            = $projectTypeName;
	}

	
	public function getProjectTypeId()
	{
		return $this->_projectTypeId;
	}
	public function getProjectTypeName()
	{
		return $this->_projectTypeName;
	}
}