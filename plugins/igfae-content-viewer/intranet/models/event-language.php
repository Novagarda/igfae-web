<?php

class IntranetLanguage
{
	protected $_languageID	        = NULL;
	protected $_name                = NULL;
	protected $_isoCode             = NULL;

	public function __construct($languageID = NULL, $name = NULL, $isoCode = NULL)
	{

		$this->_languageID	 		                = $languageID;
		$this->_name 		                        = $name;
		$this->_isoCode 		                    = $isoCode;
	}


	public function getLanguageID()
	{
		return $this->_languageID;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function getIsoCode()
	{
		return $this->_isoCode;
	}
}
