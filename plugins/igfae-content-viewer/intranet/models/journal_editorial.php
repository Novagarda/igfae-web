<?php
class IntranetJournalEditorial
{
	protected $_journalEditorialId		        = NULL;
	protected $_name                = NULL;

	public function __construct($journalEditorialId = NULL, $name = NULL)
	{
		$this->_journalEditorialId 		        = $journalEditorialId;
		$this->_name 		                    = $name;
	}

	
	public function getJournalEditorialId()
	{
		return $this->_journalEditorialId;
	}
	public function getName()
	{
		return $this->_name;
	}
}
