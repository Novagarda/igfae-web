<?php
class IntranetMonitorIndicator {
    public $monitorIndicatorId  = NULL;
    public $monitorTypeId  = NULL;
    public $monitorTypeDescription  = NULL;
    public $indicator  = NULL;
    public $objective  = NULL;
    public $date  = NULL;
    public $expectance  = NULL;
    public $value  = NULL;
    public $validated  = NULL;
    public $published  = NULL;
    public $validatedBy  = NULL;
    public $publishedBy  = NULL;


    /**
     * Get the value of monitorIndicatorId
     */ 
    public function getMonitorIndicatorId()
    {
        return $this->monitorIndicatorId;
    }

    /**
     * Set the value of monitorIndicatorId
     *
     * @return  self
     */ 
    public function setMonitorIndicatorId($monitorIndicatorId)
    {
        $this->monitorIndicatorId = $monitorIndicatorId;

        return $this;
    }

    /**
     * Get the value of monitorTypeId
     */ 
    public function getMonitorTypeId()
    {
        return $this->monitorTypeId;
    }

    /**
     * Set the value of monitorTypeId
     *
     * @return  self
     */ 
    public function setMonitorTypeId($monitorTypeId)
    {
        $this->monitorTypeId = $monitorTypeId;

        return $this;
    }

    /**
     * Get the value of indicator
     */ 
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * Set the value of indicator
     *
     * @return  self
     */ 
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;

        return $this;
    }

    /**
     * Get the value of objective
     */ 
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * Set the value of objective
     *
     * @return  self
     */ 
    public function setObjective($objective)
    {
        $this->objective = $objective;

        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of expectance
     */ 
    public function getExpectance()
    {
        return $this->expectance;
    }

    /**
     * Set the value of expectance
     *
     * @return  self
     */ 
    public function setExpectance($expectance)
    {
        $this->expectance = $expectance;

        return $this;
    }

    /**
     * Get the value of value
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return  self
     */ 
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of validated
     */ 
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set the value of validated
     *
     * @return  self
     */ 
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get the value of published
     */ 
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set the value of published
     *
     * @return  self
     */ 
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get the value of validatedBy
     */ 
    public function getValidatedBy()
    {
        return $this->validatedBy;
    }

    /**
     * Set the value of validatedBy
     *
     * @return  self
     */ 
    public function setValidatedBy($validatedBy)
    {
        $this->validatedBy = $validatedBy;

        return $this;
    }

    /**
     * Get the value of publishedBy
     */ 
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }

    /**
     * Set the value of publishedBy
     *
     * @return  self
     */ 
    public function setPublishedBy($publishedBy)
    {
        $this->publishedBy = $publishedBy;

        return $this;
    }

    /**
     * Get the value of monitorTypeDescription
     */ 
    public function getMonitorTypeDescription()
    {
        return $this->monitorTypeDescription;
    }

    /**
     * Set the value of monitorTypeDescription
     *
     * @return  self
     */ 
    public function setMonitorTypeDescription($monitorTypeDescription)
    {
        $this->monitorTypeDescription = $monitorTypeDescription;

        return $this;
    }
}