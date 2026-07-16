<?php
class IntranetResearchProgram
{
	protected $_researchProgramId		            = NULL;
	protected $_name                				= NULL;
	protected $_acronym                				= NULL;

	public function __construct($id = NULL, $name = NULL, $acronym = NULL)
	{
		$this->_researchProgramId 		        = $id;
		$this->_name 		                    = $name;
		$this->_acronym 		                = $acronym;
	}

	public function getResearchProgramId()
	{
		return $this->_researchProgramId;
	}
	public function getResearchProgramName()
	{
		return $this->_name;
	}
}
