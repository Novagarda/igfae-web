<?php
class IntranetTransferType
{
	public $_transferId		        = NULL;
	public $_transferName           = NULL;

	public function __construct($transferId = NULL, $transferName = NULL)
	{
		$this->_transferId 		                = $transferId;
		$this->_transferName 		            = $transferName;
	}

	
	public function getTransferId()
	{
		return $this->_transferId;
	}
	public function getTransferName()
	{
		return $this->_transferName;
	}
}