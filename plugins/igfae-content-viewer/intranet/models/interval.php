<?php
class IntranetInterval
{
	protected $intervalId		      = NULL;
    protected $intervalName           = NULL;
    protected $minValue               = NULL;
    protected $maxValue               = NULL;


	public function __construct($intervalId = NULL, $intervalName = NULL)
	{
		$this->intervalId 		= $intervalId;
		$this->intervalName 	= $intervalName;
	}

	


	/**
	 * Get the value of intervalId
	 */ 
	public function getIntervalId()
	{
		return $this->intervalId;
	}

	/**
	 * Set the value of intervalId
	 *
	 * @return  self
	 */ 
	public function setIntervalId($intervalId)
	{
		$this->intervalId = $intervalId;

		return $this;
	}

    /**
     * Get the value of intervalName
     */ 
    public function getIntervalName()
    {
        return $this->intervalName;
    }

    /**
     * Set the value of intervalName
     *
     * @return  self
     */ 
    public function setIntervalName($intervalName)
    {
        $this->intervalName = $intervalName;

        return $this;
    }

    /**
     * Get the value of minValue
     */ 
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * Set the value of minValue
     *
     * @return  self
     */ 
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * Get the value of maxValue
     */ 
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Set the value of maxValue
     *
     * @return  self
     */ 
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }
}