<?php
class IntranetJournal
{
	protected $_journalId		        = NULL;
	protected $_fullName                = NULL;


	public function __construct($journalId = NULL, $name = NULL)
	{
		$this->_journalId 		                    = $journalId;
		$this->_fullName 		                    = $name;
	}


	public function getJournalId()
	{
		return $this->_journalId;
	}
	public function getFullName()
	{
		return $this->_fullName;
	}
}
class IntranetJournalComplete
{
	protected $_journalId		        = NULL;
	protected $_fullName                = NULL;
	protected $_ISOAbbreviation         = NULL;
	protected $_journalAreaID           = NULL;
	protected $_electronicISSN          = NULL;
	protected $_printedISSN             = NULL;
	protected $_reference               = NULL;



	public function __construct($journalId = NULL, $name = NULL, $ISOAbbreviation = NULL, $journalAreaID = NULL, $electronicISSN = NULL, $printedISSN = NULL, $reference = NULL)
	{
		$this->_journalId 		                    = $journalId;
		$this->_fullName 		                    = $name;
		$this->_ISOAbbreviation 		            = $ISOAbbreviation;
		$this->_journalAreaID 		                = $journalAreaID;
		$this->_electronicISSN 		                = $electronicISSN;
		$this->_printedISSN 		                = $printedISSN;
		$this->_reference 		                    = $reference;
	}


	public function getJournalId()
	{
		return $this->_journalId;
	}
	public function getFullName()
	{
		return $this->_fullName;
	}
	public function getISOAbbreviation()
	{
		return $this->_ISOAbbreviation;
	}
	public function getJournalAreaId()
	{
		return $this->_journalAreaID;
	}
	public function getElectronicISSN()
	{
		return $this->_electronicISSN;
	}
	public function getPrintedISSN()
	{
		return $this->_printedISSN;
	}
	public function getReference()
	{
		return $this->_reference;
	}
}
