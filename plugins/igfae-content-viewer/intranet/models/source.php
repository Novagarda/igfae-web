<?php
class Source
{
	protected $_sourceID		                = NULL;
	protected $_name                            = NULL;

	public function __construct($sourceID = NULL, $name = NULL)
	{
		$this->_sourceID 		            = $sourceID;
		$this->_name 		                = $name;
    }

	/**
	 * Get the value of _sourceID
	 */ 
	public function getSourceID()
	{
		return $this->_sourceID;
	}

	/**
	 * Get the value of _name
	 */ 
	public function getName()
	{
		return $this->_name;
	}
}