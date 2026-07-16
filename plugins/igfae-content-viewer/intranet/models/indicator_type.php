<?php
class IntranetIndicatorType
{
	protected $indicatorId		       = NULL;
	protected $indicatorName           = NULL;

	public function __construct($indicatorId = NULL, $indicatorName = NULL)
	{
		$this->indicatorId 		= $indicatorId;
		$this->indicatorName 	= $indicatorName;
	}

	

	/**
	 * Get the value of indicatorId
	 */ 
	public function getIndicatorId()
	{
		return $this->indicatorId;
	}

	/**
	 * Set the value of indicatorId
	 *
	 * @return  self
	 */ 
	public function setIndicatorId($indicatorId)
	{
		$this->indicatorId = $indicatorId;

		return $this;
	}

	/**
	 * Get the value of indicatorName
	 */ 
	public function getIndicatorName()
	{
		return $this->indicatorName;
	}

	/**
	 * Set the value of indicatorName
	 *
	 * @return  self
	 */ 
	public function setIndicatorName($indicatorName)
	{
		$this->indicatorName = $indicatorName;

		return $this;
	}
}