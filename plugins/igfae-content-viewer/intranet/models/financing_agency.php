<?php
class IntranetFinancingAgency
{
	protected $_financingAgencyId		      = NULL;
	protected $_financingAgencyName           = NULL;

	public function __construct($financingAgencyId = NULL, $financingAgencyName = NULL)
	{
		$this->_financingAgencyId 		                = $financingAgencyId;
		$this->_financingAgencyName 		            = $financingAgencyName;
	}

	
	public function getFinancingAgencyId()
	{
		return $this->_financingAgencyId;
	}
	public function getFinancingAgencyName()
	{
		return $this->_financingAgencyName;
	}
}