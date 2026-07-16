<?php
class IntranetTRLType
{
	protected $_trlId		        = NULL;
	protected $_trlName           = NULL;

	public function __construct($trlId = NULL, $trlName = NULL)
	{
		$this->_trlId 		                = $trlId;
		$this->_trlName 		            = $trlName;
	}

	
	public function getTrlId() 
	{
		return $this->_trlId;
	}
	public function getTrlName()
	{
		return $this->_trlName;
	}
}