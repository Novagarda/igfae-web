<?php
class IntranetQuarter {
    protected $quarterId  = NULL;
    protected $year  = NULL;
    protected $quarter  = NULL;
    protected $startDate  = NULL;
    protected $endDate  = NULL;
    
    


    /**
     * Get the value of quarterId
     */ 
    public function getQuarterId()
    {
        return $this->quarterId;
    }

    /**
     * Set the value of quarterId
     *
     * @return  self
     */ 
    public function setQuarterId($quarterId)
    {
        $this->quarterId = $quarterId;

        return $this;
    }

    /**
     * Get the value of year
     */ 
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set the value of year
     *
     * @return  self
     */ 
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the value of quarter
     */ 
    public function getQuarter()
    {
        return $this->quarter;
    }

    /**
     * Set the value of quarter
     *
     * @return  self
     */ 
    public function setQuarter($quarter)
    {
        $this->quarter = $quarter;

        return $this;
    }

    /**
     * Get the value of startDate
     */ 
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     *
     * @return  self
     */ 
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of endDate
     */ 
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     *
     * @return  self
     */ 
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }
}
