<?php
class IntranetTransferSubType
{
	public $_transferSubId		        = NULL;
	public $_transferId		        = NULL;
	public $_transferName           = NULL;

	public function __construct($transferSubId = NULL, $transferId = NULL, $transferName = NULL)
	{
		$this->_transferSubId 		                = $transferSubId;
		$this->_transferId 		                = $transferId;
		$this->_transferName 		            = $transferName;
	}

	
	public function getTransferSubTypeId()
	{
		return $this->_transferSubId;
	}
	public function getTransferSubTypeName()
	{
		return $this->_transferName;
	}
}