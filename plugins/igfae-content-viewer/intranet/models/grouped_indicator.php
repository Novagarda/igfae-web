<?php
class IntranetGroupedIndicator
{
    public $title = NULL;
    public $researchArea = NULL;
    public $researchProgram = NULL;
    public $internationalConsortium = NULL;
    public $date = NULL;

    public $indicator = NULL;
    public $researchGroup = NULL;
    public $workLine = NULL;
    public $authors = NULL;

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of researchArea
     */ 
    public function getResearchArea()
    {
        return $this->researchArea;
    }

    /**
     * Set the value of researchArea
     *
     * @return  self
     */ 
    public function setResearchArea($researchArea)
    {
        $this->researchArea = $researchArea;

        return $this;
    }

    /**
     * Get the value of researchProgram
     */ 
    public function getResearchProgram()
    {
        return $this->researchProgram;
    }

    /**
     * Set the value of researchProgram
     *
     * @return  self
     */ 
    public function setResearchProgram($researchProgram)
    {
        $this->researchProgram = $researchProgram;

        return $this;
    }

    /**
     * Get the value of internationalConsortium
     */ 
    public function getInternationalConsortium()
    {
        return $this->internationalConsortium;
    }

    /**
     * Set the value of internationalConsortium
     *
     * @return  self
     */ 
    public function setInternationalConsortium($internationalConsortium)
    {
        $this->internationalConsortium = $internationalConsortium;

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
     * Get the value of workLine
     */ 
    public function getWorkLine()
    {
        return $this->workLine;
    }

    /**
     * Set the value of workLine
     *
     * @return  self
     */ 
    public function setWorkLine($workLine)
    {
        $this->workLine = $workLine;

        return $this;
    }

    /**
     * Get the value of authors
     */ 
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set the value of authors
     *
     * @return  self
     */ 
    public function setAuthors($authors)
    {
        $this->authors = $authors;

        return $this;
    }

    /**
     * Get the value of researchGroup
     */ 
    public function getResearchGroup()
    {
        return $this->researchGroup;
    }

    /**
     * Set the value of researchGroup
     *
     * @return  self
     */ 
    public function setResearchGroup($researchGroup)
    {
        $this->researchGroup = $researchGroup;

        return $this;
    }
}