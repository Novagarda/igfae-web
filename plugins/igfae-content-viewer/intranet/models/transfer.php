<?php
class IntranetTransfer
{
	// FORMULARIO DE BUSQUEDA
	public $_transferID						= NULL;
	public $_title                         	= NULL;
    public $_titleES                      	= NULL;
    public $_titleEN                        = NULL;
    public $_titleGL                        = NULL;
	public $_transferType					= NULL;
	public $_transferSubtype				= NULL;
	public $_reference						= NULL;
	public $_link							= NULL;
	public $_national						= NULL;
	public $_description					= NULL;
	public $_tTAType						= NULL;
	public $_tTAStartDate					= NULL;
	public $_tTACompanyTarget				= NULL;
	public $_reportLink						= NULL;
	public $_requestLink					= NULL;
	public $_tRLTypeID						= NULL;
	public $_annualIncome					= NULL;
	public $_annualCost						= NULL;
	public $_tTAEndDate						= NULL;
	public $_coordinatorIgfae				= NULL;
	public $_transferPersonnel				= NULL;
	public $_researchArea                	= NULL;
	public $_researchProgram                = NULL;
	public $_researchGroup                  = NULL;
	public $_asociatedPublications			= NULL;
	public $_experimentalCollaboration		= NULL;
	public $_partners						= NULL;
	public $_workLine                       = NULL;
	public $_keywords                    	= NULL;
	public $_comments						= NULL;
	public $_validated						= NULL;
	public $_published						= NULL;
	public $_validatedBy					= NULL;
	public $_publishedBy					= NULL;
	public $_validatedOn					= NULL;
	public $_publishedOn					= NULL;


	public function __construct(
	$transferID						= NULL,
	$title 							= NULL,
	$titleES 						= NULL,
	$titleEN 						= NULL,
	$titleGL 						= NULL,
	$transferType					= NULL,
	$transferSubtype				= NULL,
	$reference						= NULL,
	$link							= NULL,
	$national						= NULL,
	$description					= NULL,
	$tTAType						= NULL,
	$tTAStartDate					= NULL,
	$tTACompanyTarget				= NULL,
	$reportLink						= NULL,
	$requestLink					= NULL,
	$tRLTypeID						= NULL,
	$annualIncome					= NULL,
	$annualCost						= NULL,
	$tTAEndDate						= NULL,
	$coordinatorIgfae				= NULL,
	$transferPersonnel				= NULL,
	$researchArea                	= NULL,
	$researchProgram                = NULL,
	$researchGroup                  = NULL,
	$asociatedPublications 			= NULL,
	$experimentalCollaboration		= NULL,
	$partners 						= NULL,
	$workLine                       = NULL,
	$keywords                    	= NULL,
	$comments						= NULL,
	$validated						= NULL,
	$published						= NULL,
	$validatedBy					= NULL,
	$publishedBy					= NULL,
	$validatedOn					= NULL,
	$publishedOn					= NULL
	) {
		$this->_transferID 					= $transferID;
		$this->_title                       = $title;
        $this->_titleES                     = $titleES;
        $this->_titleEN                     = $titleEN;
        $this->_titleGL                     = $titleGL;
		$this->_transferType 				= $transferType;
		$this->_transferSubtype 		    = $transferSubtype;
		$this->_reference 		   			= $reference;
		$this->_link 		       			= $link;
		$this->_national					= $national;
		$this->_description					= $description;
		$this->_tTAType						= $tTAType;
		$this->_tTAStartDate				= $tTAStartDate;
		$this->_tTACompanyTarget			= $tTACompanyTarget;
		$this->_reportLink					= $reportLink;
		$this->_requestLink					= $requestLink;
		$this->_tRLTypeID					= $tRLTypeID;
		$this->_annualIncome				= $annualIncome;
		$this->_annualCost					= $annualCost;
		$this->_tTAEndDate					= $tTAEndDate;
		$this->_coordinatorIgfae			= $coordinatorIgfae;
		$this->_transferPersonnel			= $transferPersonnel;
		$this->_researchArea                = $researchArea;
		$this->_researchProgram             = $researchProgram;
		$this->_researchGroup               = $researchGroup;
		$this->_asociatedPublications		= $asociatedPublications;
		$this->_experimentalCollaboration	= $experimentalCollaboration;
		$this->_partners					= $partners;
		$this->_workLine                    = $workLine;
		$this->_keywords                 	= $keywords;
		$this->_comments					= $comments;
		$this->_validated					= $validated;
		$this->_published					= $published;
		$this->_validatedBy					= $validatedBy;
		$this->_publishedBy					= $publishedBy;
		$this->_validatedOn					= $validatedOn;
		$this->_publishedOn					= $publishedOn;
	}


	/**
	 * Get the value of _transferID
	 */ 
	public function getTransferID()
	{
		return $this->_transferID;
	}

	/**
	 * Set the value of _transferID
	 *
	 * @return  self
	 */ 
	public function setTransferID($transferID)
	{
		$this->_transferID = $transferID;

		return $this;
	}

	public function getTitle()
    {
        return $this->_title;
	}
	
    public function getTitleES()
    {
        return $this->_titleES;
	}
	
    public function getTitleEN()
    {
        return $this->_titleEN;
	}
	
    public function getTitleGL()
    {
        return $this->_titleGL;
    }
	
	public function getTransferType()
	{
		return $this->_transferType;
	}

	public function setTransferType($transferType)
	{
		$this->_transferType = $transferType;

		return $this;
	}

	
	public function getTransferSubtype()
	{
		return $this->_transferSubtype;
	}

	public function setTransferSubtype($transferSubtype)
	{
		$this->_transferSubtype = $transferSubtype;

		return $this;
	}

	
	public function getReference()
	{
		return $this->_reference;
	}

	public function setReference($reference)
	{
		$this->_reference = $reference;

		return $this;
	}

	
	public function getLink()
	{
		return $this->_link;
	}

	public function setLink($link)
	{
		$this->_link = $link;

		return $this;
	}

	
	public function getNational()
	{
		return $this->_national;
	}

	public function setNational($national)
	{
		$this->_national = $national;

		return $this;
	}

	
	public function getDescription()
	{
		return $this->_description;
	}

	public function setDescription($description)
	{
		$this->_description = $description;

		return $this;
	}

	
	public function getTTAType()
	{
		return $this->_tTAType;
	}

	public function setTTAType($_tTAType)
	{
		$this->_tTAType = $_tTAType;

		return $this;
	}

	
	public function getTTAStartDate()
	{
		return $this->_tTAStartDate;
	}

	public function setTTAStartDate($tTAStartDate)
	{
		$this->_tTAStartDate = $tTAStartDate;

		return $this;
	}

	
	public function getTTACompanyTarget()
	{
		return $this->_tTACompanyTarget;
	}

	public function setTTACompanyTarget($tTACompanyTarget)
	{
		$this->_tTACompanyTarget = $tTACompanyTarget;

		return $this;
	}

	
	public function getReportLink()
	{
		return $this->_reportLink;
	}

	public function setReportLink($reportLink)
	{
		$this->_reportLink = $reportLink;

		return $this;
	}

	
	public function getTRLTypeID()
	{
		return $this->_tRLTypeID;
	}

	public function setTRLTypeID($tRLTypeID)
	{
		$this->_tRLTypeID = $tRLTypeID;

		return $this;
	}

	
	public function getAnnualIncome()
	{
		return $this->_annualIncome;
	}

	public function setAnnualIncome($annualIncome)
	{
		$this->_annualIncome = $annualIncome;

		return $this;
	}

	
	public function getAnnualCost()
	{
		return $this->_annualCost;
	}

	public function setAnnualCost($annualCost)
	{
		$this->_annualCost = $annualCost;

		return $this;
	}

	
	public function getTTAEndDate()
	{
		return $this->_tTAEndDate;
	}

	public function setTTAEndDate($tTAEndDate)
	{
		$this->_tTAEndDate = $tTAEndDate;

		return $this;
	}

	
	public function getComments()
	{
		return $this->_comments;
	}

	public function setComments($comments)
	{
		$this->_comments = $comments;

		return $this;
	}

	public function getValidated()
    {
        return $this->_validated;
	}

	public function setValidated($validated)
    {
        $this->_validated = $validated;
        return $this;
	}
	
    public function getPublished()
    {
        return $this->_published;
	}

	public function setPublished($published)
    {
        $this->_published = $published;
        return $this;
	}
	
	public function getPublishedBy()
    {
        return $this->_publishedBy;
	}
	
    public function getPublishedOn()
    {
        return $this->_publishedOn;
	}
	
    public function getValidatedBy()
    {
        return $this->_validatedBy;
	}
	
    public function getValidatedOn()
    {
        return $this->_validatedOn;
	}
	
	public function getCoordinatorIgfae()
	{
		return $this->_coordinatorIgfae;
	}

	public function setCoordinatorIgfae($coordinatorIgfae)
	{
		$this->_coordinatorIgfae = $coordinatorIgfae;

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
	
	public function getKeywords()
    {
        return $this->_keywords;
    }

	public function setKeywords($keywords)
    {
        $this->_keywords = $keywords;

        return $this;
	}

	public function getTransferPersonnel()
    {
        return $this->_transferPersonnel;
    }

	public function setTransferPersonnel($transferPersonnel)
    {
        $this->_transferPersonnel = $transferPersonnel;

        return $this;
	}

	public function getAsociatedPublications()
	{
		return $this->_asociatedPublications;
	}

	public function setAsociatedPublications($asociatedPublications)
	{
		$this->_asociatedPublications = $asociatedPublications;

		return $this;
	}

	public function getPartners()
	{
		return $this->_partners;
	}

	public function setPartners($partners)
	{
		$this->_partners = $partners;

		return $this;
	}

	public function getRequestLink()
	{
		return $this->_requestLink;
	}

	public function setRequestLink($requestLink)
	{
		$this->_requestLink = $requestLink;

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
	
}
