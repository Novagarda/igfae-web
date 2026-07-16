<?php
class IntranetWorkPlacements
{
	// FORMULARIO DE BUSQUEDA
	public $_workPlacementID				  = NULL;
	public $_displacedResearcher	  		  = NULL;
	public $_tutor              			  = NULL;
	public $_associatedContract               = NULL;
	public $_contractLink              		  = NULL;
	public $_institution               		  = NULL;
	public $_externalTutor              	  = NULL;
	public $_financiation					  = NULL;
	public $_financiatedBy					  = NULL;
	public $_cost							  = NULL;
	public $_researchArea                	  = NULL;
	public $_researchProgram                  = NULL;
    public $_startDate        		          = NULL;
    public $_endDate                          = NULL;
    public $_researchGroup                    = NULL;
	public $_workLine                         = NULL;
	public $_experimentalCollaboration        = NULL;
	public $_comment						  = NULL;
    public $_validated                        = NULL;
    public $_validatedBy                	  = NULL;
    public $_validatedOn                 	  = NULL;

	public function __construct(
		$workPlacementID				  = NULL,
		$displacedResearcher 			  = NULL,
		$tutor              			  = NULL,
		$associatedContract               = NULL,
		$contractLink              		  = NULL,
		$institution               		  = NULL,
		$externalTutor              	  = NULL,
		$financiation					  = NULL,
		$financiatedBy					  = NULL,
		$cost							  = NULL,
		$researchArea                	  = NULL,
		$researchProgram                  = NULL,
		$startDate        		          = NULL,
		$endDate                          = NULL,
		$researchGroup                    = NULL,
		$workLine                         = NULL,
		$experimentalCollaboration		  = NULL,
		$comment						  = NULL,
		$validated                        = NULL,
		$validatedBy                	  = NULL,
		$validatedOn                 	  = NULL
	) {
		$this->_workPlacementID 			= $workPlacementID;
		$this->_displacedResearcher 		= $displacedResearcher;
		$this->_tutor 		                = $tutor;
		$this->_associatedContract 		    = $associatedContract;
		$this->_contractLink 		        = $contractLink;
		$this->_institution 		        = $institution;
		$this->_externalTutor 		        = $externalTutor;
		$this->_financiation				= $financiation;		
		$this->_financiatedBy				= $financiatedBy;			
		$this->_cost						= $cost;			 
		$this->_researchArea 		        = $researchArea;
		$this->_researchProgram 		    = $researchProgram;
		$this->_startDate 		            = $startDate;
		$this->_endDate 		            = $endDate;
		$this->_researchGroup 		        = $researchGroup;
		$this->_workLine 		            = $workLine;
		$this->_experimentalCollaboration   = $experimentalCollaboration;
		$this->_comment						= $comment;	
		$this->_validated					= $validated;
		$this->_validatedBy					= $validatedBy;
		$this->_validatedOn					= $validatedOn;
	}	

	/**
	 * Get the value of _workPlacementID
	 */ 
	public function getWorkPlacementID()
	{
		return $this->_workPlacementID;
	}

	/**
	 * Set the value of _workPlacementID
	 *
	 * @return  self
	 */ 
	public function setWorkPlacementID($workPlacementID)
	{
		$this->_workPlacementID = $workPlacementID;

		return $this;
	}

	/**
	 * Get the value of _displacedResearcher
	 */ 
	public function getDisplacedResearcher()
	{
		return $this->_displacedResearcher;
	}

	/**
	 * Set the value of _displacedResearcher
	 *
	 * @return  self
	 */ 
	public function setDisplacedResearcher($displacedResearcher)
	{
		$this->_displacedResearcher = $displacedResearcher;

		return $this;
	}

	/**
	 * Get the value of _tutor
	 */ 
	public function getTutor()
	{
		return $this->_tutor;
	}

	/**
	 * Set the value of _tutor
	 *
	 * @return  self
	 */ 
	public function setTutor($tutor)
	{
		$this->_tutor = $tutor;

		return $this;
	}

	/**
	 * Get the value of _associatedContract
	 */ 
	public function getAssociatedContract()
	{
		return $this->_associatedContract;
	}

	/**
	 * Set the value of _associatedContract
	 *
	 * @return  self
	 */ 
	public function setAssociatedContract($associatedContract)
	{
		$this->_associatedContract = $associatedContract;

		return $this;
	}

	/**
	 * Get the value of _contractLink
	 */ 
	public function getContractLink()
	{
		return $this->_contractLink;
	}

	/**
	 * Set the value of _contractLink
	 *
	 * @return  self
	 */ 
	public function setContractLink($contractLink)
	{
		$this->_contractLink = $contractLink;

		return $this;
	}

	/**
	 * Get the value of _institution
	 */ 
	public function getInstitution()
	{
		return $this->_institution;
	}

	/**
	 * Set the value of _institution
	 *
	 * @return  self
	 */ 
	public function setInstitution($institution)
	{
		$this->_institution = $institution;

		return $this;
	}

	/**
	 * Get the value of _externalTutor
	 */ 
	public function getExternalTutor()
	{
		return $this->_externalTutor;
	}

	/**
	 * Set the value of _externalTutor
	 *
	 * @return  self
	 */ 
	public function setExternalTutor($externalTutor)
	{
		$this->_externalTutor = $externalTutor;

		return $this;
	}

	/**
	 * Get the value of _researchArea
	 */ 
	public function getResearchArea()
	{
		return $this->_researchArea;
	}

	/**
	 * Set the value of _researchArea
	 *
	 * @return  self
	 */ 
	public function setResearchArea($researchArea)
	{
		$this->_researchArea = $researchArea;

		return $this;
	}

	/**
	 * Get the value of _researchProgram
	 */ 
	public function getResearchProgram()
	{
		return $this->_researchProgram;
	}

	/**
	 * Set the value of _researchProgram
	 *
	 * @return  self
	 */ 
	public function setResearchProgram($researchProgram)
	{
		$this->_researchProgram = $researchProgram;

		return $this;
	}

    /**
     * Get the value of _startDate
     */ 
    public function getStartDate()
    {
        return $this->_startDate;
    }

    /**
     * Set the value of _startDate
     *
     * @return  self
     */ 
    public function setStartDate($startDate)
    {
        $this->_startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of _endDate
     */ 
    public function getEndDate()
    {
        return $this->_endDate;
    }

    /**
     * Set the value of _endDate
     *
     * @return  self
     */ 
    public function setEndDate($endDate)
    {
        $this->_endDate = $endDate;

        return $this;
    }

    /**
     * Get the value of _researchGroup
     */ 
    public function getResearchGroup()
    {
        return $this->_researchGroup;
    }

    /**
     * Set the value of _researchGroup
     *
     * @return  self
     */ 
    public function setResearchGroup($researchGroup)
    {
        $this->_researchGroup = $researchGroup;

        return $this;
    }

    /**
     * Get the value of _workLine
     */ 
    public function getWorkLine()
    {
        return $this->_workLine;
    }

    /**
     * Set the value of _workLine
     *
     * @return  self
     */ 
    public function setWorkLine($workLine)
    {
        $this->_workLine = $workLine;

        return $this;
    }

    /**
     * Get the value of _validated
     */ 
    public function getValidated()
    {
        return $this->_validated;
    }

    /**
     * Set the value of _validated
     *
     * @return  self
     */ 
    public function setValidated($validated)
    {
        $this->_validated = $validated;

        return $this;
    }

    /**
     * Get the value of _validatedBy
     */ 
    public function getValidatedBy()
    {
        return $this->_validatedBy;
    }

    /**
     * Set the value of _validatedBy
     *
     * @return  self
     */ 
    public function setValidatedBy($validatedBy)
    {
        $this->_validatedBy = $validatedBy;

        return $this;
    }

    /**
     * Get the value of _validatedOn
     */ 
    public function getValidatedOn()
    {
        return $this->_validatedOn;
    }

    /**
     * Set the value of _validatedOn
     *
     * @return  self
     */ 
    public function setValidatedOn($validatedOn)
    {
        $this->_validatedOn = $validatedOn;

        return $this;
    }

	/**
	 * Get the value of _experimentalCollaboration
	 */ 
	public function getExperimentalCollaboration()
	{
		return $this->_experimentalCollaboration;
	}

	/**
	 * Set the value of _experimentalCollaboration
	 *
	 * @return  self
	 */ 
	public function setExperimentalCollaboration($experimentalCollaboration)
	{
		$this->_experimentalCollaboration = $experimentalCollaboration;

		return $this;
	}


	/**
	 * Get the value of _financiation
	 */ 
	public function getFinanciation()
	{
		return $this->_financiation;
	}

	/**
	 * Set the value of _financiation
	 *
	 * @return  self
	 */ 
	public function setFinanciation($financiation)
	{
		$this->_financiation = $financiation;

		return $this;
	}

	/**
	 * Get the value of _financiatedBy
	 */ 
	public function getFinanciatedBy()
	{
		return $this->_financiatedBy;
	}

	/**
	 * Set the value of _financiatedBy
	 *
	 * @return  self
	 */ 
	public function setFinanciatedBy($financiatedBy)
	{
		$this->_financiatedBy = $financiatedBy;

		return $this;
	}

	/**
	 * Get the value of _cost
	 */ 
	public function getCost()
	{
		return $this->_cost;
	}

	/**
	 * Set the value of _cost
	 *
	 * @return  self
	 */ 
	public function setCost($cost)
	{
		$this->_cost = $cost;

		return $this;
	}

	/**
	 * Get the value of _comment
	 */ 
	public function getComment()
	{
		return $this->_comment;
	}

	/**
	 * Set the value of _comment
	 *
	 * @return  self
	 */ 
	public function setComment($comment)
	{
		$this->_comment = $comment;

		return $this;
	}
}
