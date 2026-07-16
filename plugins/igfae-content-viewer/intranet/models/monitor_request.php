<?php
class IntranetMonitorRequest
{
    public $researchArea = [];
    public $researchProgram = [];
    public $collaborationExperiment = [];
    public $researchGroup = [];
    public $researchs = [];
    public $startDate = NULL;
    public $endDate = NULL;
    public $objectives = [];
    public $source = NULL;

    public function __construct(
        $researchArea = [],
        $researchProgram = [],
        $collaborationExperiment = [],
        $researchGroup = [],
        $researchs = [],
        $startDate = NULL,
        $endDate = NULL,
        $objectives = [],
        $source = NULL
    ) {
        $this->researchArea      = $researchArea;
        $this->researchProgram = $researchProgram;
        $this->collaborationExperiment = $collaborationExperiment;
        $this->researchGroup = $researchGroup;
        $this->researchs = $researchs;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->objectives = $objectives;
        $this->source = $source;
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
     * Get the value of collaborationExperiment
     */ 
    public function getCollaborationExperiment()
    {
        return $this->collaborationExperiment;
    }

    /**
     * Set the value of collaborationExperiment
     *
     * @return  self
     */ 
    public function setCollaborationExperiment($collaborationExperiment)
    {
        $this->collaborationExperiment = $collaborationExperiment;

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

    /**
     * Get the value of researchs
     */ 
    public function getResearchs()
    {
        return $this->researchs;
    }

    /**
     * Set the value of researchs
     *
     * @return  self
     */ 
    public function setResearchs($researchs)
    {
        $this->researchs = $researchs;

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

    /**
     * Get the value of objectives
     */ 
    public function getObjectives()
    {
        return $this->objectives;
    }

    /**
     * Set the value of objectives
     *
     * @return  self
     */ 
    public function setObjectives($objectives)
    {
        $this->objectives = $objectives;

        return $this;
    }

    /**
     * Get the value of source
     */ 
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the value of source
     *
     * @return  self
     */ 
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
}
