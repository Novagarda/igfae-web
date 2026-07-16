<?php
class IntranetJournalArea
{
	protected $_journalAreaId		        = NULL;
	protected $_area                = NULL;

	public function __construct($_journalAreaId = NULL, $name = NULL)
	{
		$this->_journalAreaId 		            = $_journalAreaId;
		$this->_area 		                    = $name;
	}

	
	public function getJournalAreaId()
	{
		return $this->_journalAreaId;
	}
	public function getArea()
	{
		return $this->_area;
	}
}
