<?php
class IntranetResearchGroup
{
	protected $_researchGroupId		            = NULL;
	protected $_name                				= NULL;
	protected $_acronym                				= NULL;

	public function __construct($id = NULL, $name = NULL, $acronym = NULL)
	{
		$this->_researchGroupId 		        = $id;
		$this->_name 		                    = $name;
		$this->_acronym 		                = $acronym;
	}

	
	public function getResearchGroupId()
	{
		return $this->_researchGroupId;
	}
	public function getResearchGroupName()
	{
		return $this->_name;
	}
}
