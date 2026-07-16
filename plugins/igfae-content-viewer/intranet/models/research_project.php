<?php
class IntranetResearchProject
{
    // FORMULARIO DE BUSQUEDA
    public $_id                          = NULL;
    public $_title                       = NULL;
    public $_scope                       = [];
    public $_typeActivity                = [];
    public $_researchArea                = [];
    public $_researchProgram             = [];
    public $_collaborationExperiment     = [];
    public $_researchGroup               = [];
    public $_workLine                    = [];
    public $_announcement                = NULL;
    public $_annualGifteDateFrom         = NULL;
    public $_annualGifteDateUntil        = NULL;
    public $_annualGifteAmountFrom       = NULL;
    public $_annualGifteAmountUntil      = NULL;
    public $_annualGiftedAmountUsc       = NULL;
    public $_startDate                   = NULL;
    public $_concessionDate              = NULL;
    public $_keywords                    = [];
    public $_financingAgencies           = [];
    public $_reference                   = NULL;
    public $_amountOverheadFrom          = NULL;
    public $_amountOverheadUntil         = NULL;
    public $_amountWithoutOverheads      = NULL;
    public $_feder                       = [];    
    public $_justification               = [];
    public $_extended                    = [];
    public $_indirectCostsFrom           = NULL;
    public $_indirectCostsUntil          = NULL;
    public $_indirectCosts               = NULL;
    public $_members                     = [];
    public $_typeProject                 = [];
    public $_budgetLink                  = NULL;
    public $_competitive                 = [];
    public $_igfaeLeader                 = [];    
    public $_validated                   = [];
    public $_published                   = [];
    public $_projectCoordinator          = [];
    public $_principalResearcher         = [];
    public $_associateResearchers        = [];
    public $_technical        = [];

    //datos alta-edit-detalle
    public $titleEn                   = NULL;
    public $titleEs                   = NULL;
    public $titleGl                   = NULL;
    public $description               = NULL;
    public $website                   = NULL;
    public $activityTypeId            = NULL;
    public $uscActivityTypeId         = NULL;
    public $uscActivityType           = NULL;
    public $uscSubActivityTypeId      = NULL;
    public $uscSubActivityType        = NULL;
    public $uscProjectReference  = NULL;
    public $fundingAgenciesId        = NULL;
    public $fundingAgencies        = NULL;
    public $granted        = NULL;
    public $officeCode        = NULL;
    public $reference2        = NULL;
    public $ipCoordinatesUsersIDs = [];
    public $ipCoordinatesUsers = [];
    public $ipUsersIDs = [];
    public $ipUsers = [];
    public $associatedUsersIDs = [];
    public $associatedUsers = [];
    public $technicianUsersIDs = [];
    public $technicianUsers = [];
    public $endDate = NULL;
    public $financingAgenciesIDs = [];
    public $totalAmount = NULL;
    public $cofinancing = NULL;
    public $cofinancingType = NULL;
    public $resolution = NULL;
    public $endDateExtended = NULL;
    public $federPercent = NULL;
    public $researchAreasIDs = [];
    public $researchProgramsIDs = [];
    public $experimentsIDs = [];
    public $researchGroupID = NULL;
    public $workLineId = NULL;
    public $competitiveCall = NULL;
    public $comment = NULL;
    public $validatedBy = NULL;
    public $validatedOn = NULL;
    public $publishedBy = NULL;
    public $publishedOn = NULL;
    public $projectRequest = NULL;
    public $awardedAllotment = NULL;
    public $annualGifted = NULL;
    public $annualExecuted = NULL;
    public $annualJustified = NULL;
    public $partners = NULL;
    public $workTeam = [];
    public $consortium = NULL;

    
    



    public function __construct(
        $id = NULL,
        $title = NULL,
        $scope = [],
        $typeActivity = [],
        $researchArea = [],
        $researchProgram = [],
        $collaborationExperiment = [],
        $researchGroup = [],
        $workLine = [],
        $announcement = NULL,
        $annualGifteDateFrom = NULL,
        $annualGifteDateUntil = NULL,
        $annualGifteAmountFrom = NULL,
        $annualGifteAmountUntil = NULL,
        $annualGiftedAmountUsc = NULL,
        $startDate = NULL,
        $concessionDate = NULL,
        $keywords = [],
        $financingAgencies = [],
        $reference = NULL,
        $amountOverheadFrom = NULL,
        $amountOverheadUntil = NULL,
        $amountWithoutOverheads = NULL,
        $feder = [],        
        $justification = [],
        $extended = [],
        $indirectCostsFrom = NULL,
        $indirectCostsUntil = NULL,
        $indirectCosts = NULL,
        $members = [],
        $typeProject = [],
        $budgetLink = NULL,
        $competitive = [],
        $igfaeLeader = [],  
        $reference2 = NULL,  
        $validated = [],
        $published = [],
        $projectCoordinator = [],
        $principalResearcher = [],
        $associateResearchers = []
    ) {
        $this->_id      = $id;
        $this->_title = $title;
        $this->_scope = $scope;
        $this->_typeActivity = $typeActivity;
        $this->_researchArea = $researchArea;
        $this->_researchProgram = $researchProgram;
        $this->_collaborationExperiment = $collaborationExperiment;
        $this->_researchGroup = $researchGroup;
        $this->_workLine = $workLine;
        $this->_announcement = $announcement;
        $this->_annualGifteDateFrom = $annualGifteDateFrom;
        $this->_annualGifteDateUntil = $annualGifteDateUntil;
        $this->_annualGifteAmountFrom = $annualGifteAmountFrom;
        $this->_annualGifteAmountUntil = $annualGifteAmountUntil;
        $this->_annualGiftedAmountUsc = $annualGiftedAmountUsc;        
        $this->_startDate = $startDate;
        $this->_concessionDate = $concessionDate;
        $this->_keywords = $keywords;
        $this->_financingAgencies = $financingAgencies;
        $this->_reference = $reference;
        $this->_amountOverheadFrom = $amountOverheadFrom;
        $this->_amountOverheadUntil = $amountOverheadUntil;
        $this->_amountWithoutOverheads = $amountWithoutOverheads;        
        $this->_feder = $feder;       
        $this->_justification = $justification;
        $this->_extended = $extended;
        $this->_indirectCostsFrom = $indirectCostsFrom;
        $this->_indirectCostsUntil = $indirectCostsUntil;
        $this->_indirectCosts = $indirectCosts;        
        $this->_members = $members;
        $this->_typeProject = $typeProject;
        $this->_budgetLink = $budgetLink;
        $this->_competitive = $competitive;
        $this->_igfaeLeader =$igfaeLeader;
        $this->reference2 =$reference2;    
        $this->_validated = $validated;
        $this->_published = $published;
        $this->_projectCoordinator = $projectCoordinator;
        $this->_principalResearcher = $principalResearcher;
        $this->_associateResearchers = $associateResearchers;       
    }


    public function setIdProject($idProject)
    {
        $this->_id = $idProject;
    }
    public function getId()
    {
        return $this->_id;
    }
    public function getTitle()
    {
        return $this->_title;
    }
    public function getScope()
    {
        return $this->_scope;
    }
    public function getTypeActivity()
    {
        return $this->_typeActivity;
    }
    public function getResearchArea()
    {
        return $this->_researchArea;
    }
    public function getResearchProgram()
    {
        return $this->_researchProgram;
    }
    public function getCollaborationExperiment()
    {
        return $this->_collaborationExperiment;
    }
    public function getResearchGroup()
    {
        return $this->_researchGroup;
    }
    public function getWorkLine()
    {
        return $this->_workLine;
    }
    public function getAnnouncement()
    {
        return $this->_announcement;
    }
    public function getAnnualGifteDateFrom()
    {
        return $this->_annualGifteDateFrom;
    }
    public function getAnnualGifteDateUntil()
    {
        return $this->_annualGifteDateUntil;
    }
    public function getAnnualGifteAmountFrom()
    {
        return $this->_annualGifteAmountFrom;
    }
    public function getAnnualGifteAmountUntil()
    {
        return $this->_annualGifteAmountUntil;
    }
    public function getAnnualGiftedAmountUsc()
    {
        return $this->_annualGiftedAmountUsc;
    }
    public function getStartDate()
    {
        return $this->_startDate;
    }
    public function getConcessionDate()
    {
        return $this->_concessionDate;
    }
    public function getKeywords()
    {
        return $this->_keywords;
    }
    public function getFinancingAgencies()
    {
        return $this->_financingAgencies;
    }
    public function getReference()
    {
        return $this->_reference;
    }
    public function getAmountOverheadFrom()
    {
        return $this->_amountOverheadFrom;
    }
    public function getAmountOverheadUntil()
    {
        return $this->_amountOverheadUntil;
    }
    public function getAmountWithoutOverheads()
    {
        return $this->_amountWithoutOverheads;
    }
    public function getFeder()
    {
        return $this->_feder;
    }    
    public function getJustification()
    {
        return $this->_justification;
    }
    public function getExtended()
    {
        return $this->_extended;
    }
    public function getIndirectCostsFrom()
    {
        return $this->_indirectCostsFrom;
    }
    public function getIndirectCostsUntil()
    {
        return $this->_indirectCostsUntil;
    }
    public function getIndirectCosts()
    {
        return $this->_indirectCosts;
    }
    public function getMembers()
    {
        return $this->_members;
    }
    public function getTypeProject()
    {
        return $this->_typeProject;
    }
    public function getBudgetLink()
    {
        return $this->_budgetLink;
    }
    public function getCompetitive()
    {
        return $this->_competitive;
    }
    public function getIgfaeLeader()
    {
        return $this->_igfaeLeader;
    }
    public function getValidated()
    {
        return $this->_validated;
    }
    public function getPublished()
    {
        return $this->_published;
    }


    /**
     * Set the value of _title
     *
     * @return  self
     */ 
    public function set_title($_title)
    {
        $this->_title = $_title;

        return $this;
    }

    /**
     * Set the value of _scope
     *
     * @return  self
     */ 
    public function set_scope($_scope)
    {
        $this->_scope = $_scope;

        return $this;
    }

    /**
     * Set the value of _typeActivity
     *
     * @return  self
     */ 
    public function setActivityType($_typeActivity)
    {
        $this->_typeActivity = $_typeActivity;

        return $this;
    }

    /**
     * Set the value of _researchArea
     *
     * @return  self
     */ 
    public function set_researchArea($_researchArea)
    {
        $this->_researchArea = $_researchArea;

        return $this;
    }

    /**
     * Set the value of _researchProgram
     *
     * @return  self
     */ 
    public function set_researchProgram($_researchProgram)
    {
        $this->_researchProgram = $_researchProgram;

        return $this;
    }


    /**
     * Set the value of _collaborationExperiment
     *
     * @return  self
     */ 
    public function set_collaborationExperiment($_collaborationExperiment)
    {
        $this->_collaborationExperiment = $_collaborationExperiment;

        return $this;
    }

    /**
     * Set the value of _researchGroup
     *
     * @return  self
     */ 
    public function set_researchGroup($_researchGroup)
    {
        $this->_researchGroup = $_researchGroup;

        return $this;
    }

    /**
     * Set the value of _workLine
     *
     * @return  self
     */ 
    public function set_workLine($_workLine)
    {
        $this->_workLine = $_workLine;

        return $this;
    }

    /**
     * Set the value of _announcement
     *
     * @return  self
     */ 
    public function set_announcement($_announcement)
    {
        $this->_announcement = $_announcement;

        return $this;
    }

    /**
     * Set the value of _annualGiftedAmountUsc
     *
     * @return  self
     */ 
    public function set_annualGiftedAmountUsc($_annualGiftedAmountUsc)
    {
        $this->_annualGiftedAmountUsc = $_annualGiftedAmountUsc;

        return $this;
    }

    /**
     * Set the value of _startDate
     *
     * @return  self
     */ 
    public function set_startDate($_startDate)
    {
        $this->_startDate = $_startDate;

        return $this;
    }

    /**
     * Set the value of _concessionDate
     *
     * @return  self
     */ 
    public function set_concessionDate($_concessionDate)
    {
        $this->_concessionDate = $_concessionDate;

        return $this;
    }

    /**
     * Set the value of _keywords
     *
     * @return  self
     */ 
    public function set_keywords($_keywords)
    {
        $this->_keywords = $_keywords;

        return $this;
    }

    /**
     * Set the value of _financingAgencies
     *
     * @return  self
     */ 
    public function set_financingAgencies($_financingAgencies)
    {
        $this->_financingAgencies = $_financingAgencies;

        return $this;
    }

    /**
     * Set the value of _reference
     *
     * @return  self
     */ 
    public function set_reference($_reference)
    {
        $this->_reference = $_reference;

        return $this;
    }

    /**
     * Set the value of _amountWithoutOverheads
     *
     * @return  self
     */ 
    public function set_amountWithoutOverheads($_amountWithoutOverheads)
    {
        $this->_amountWithoutOverheads = $_amountWithoutOverheads;

        return $this;
    }

    /**
     * Set the value of _feder
     *
     * @return  self
     */ 
    public function set_feder($_feder)
    {
        $this->_feder = $_feder;

        return $this;
    }

    /**
     * Set the value of _justification
     *
     * @return  self
     */ 
    public function setAnnualjustification($_justification)
    {
        $this->_justification = $_justification;

        return $this;
    }

    /**
     * Set the value of _extended
     *
     * @return  self
     */ 
    public function set_extended($_extended)
    {
        $this->_extended = $_extended;

        return $this;
    }

    /**
     * Set the value of _indirectCosts
     *
     * @return  self
     */ 
    public function set_indirectCosts($_indirectCosts)
    {
        $this->_indirectCosts = $_indirectCosts;

        return $this;
    }

    /**
     * Set the value of _typeProject
     *
     * @return  self
     */ 
    public function set_typeProject($_typeProject)
    {
        $this->_typeProject = $_typeProject;

        return $this;
    }

    /**
     * Set the value of _budgetLink
     *
     * @return  self
     */ 
    public function set_budgetLink($_budgetLink)
    {
        $this->_budgetLink = $_budgetLink;

        return $this;
    }

    /**
     * Set the value of _competitive
     *
     * @return  self
     */ 
    public function set_competitive($_competitive)
    {
        $this->_competitive = $_competitive;

        return $this;
    }

    /**
     * Set the value of _igfaeLeader
     *
     * @return  self
     */ 
    public function set_igfaeLeader($_igfaeLeader)
    {
        $this->_igfaeLeader = $_igfaeLeader;

        return $this;
    }

    /**
     * Set the value of _validated
     *
     * @return  self
     */ 
    public function set_validated($_validated)
    {
        $this->_validated = $_validated;

        return $this;
    }

    /**
     * Set the value of _published
     *
     * @return  self
     */ 
    public function set_published($_published)
    {
        $this->_published = $_published;

        return $this;
    }

    /**
     * Get the value of titleEn
     */ 
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * Set the value of titleEn
     *
     * @return  self
     */ 
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;

        return $this;
    }

    /**
     * Get the value of titleEs
     */ 
    public function getTitleEs()
    {
        return $this->titleEs;
    }

    /**
     * Set the value of titleEs
     *
     * @return  self
     */ 
    public function setTitleEs($titleEs)
    {
        $this->titleEs = $titleEs;

        return $this;
    }

    /**
     * Get the value of titleGl
     */ 
    public function getTitleGl()
    {
        return $this->titleGl;
    }

    /**
     * Set the value of titleGl
     *
     * @return  self
     */ 
    public function setTitleGl($titleGl)
    {
        $this->titleGl = $titleGl;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of website
     */ 
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set the value of website
     *
     * @return  self
     */ 
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get the value of activityTypeId
     */ 
    public function getActivityTypeId()
    {
        return $this->activityTypeId;
    }

    /**
     * Set the value of activityTypeId
     *
     * @return  self
     */ 
    public function setActivityTypeId($activityTypeId)
    {
        $this->activityTypeId = $activityTypeId;

        return $this;
    }

    /**
     * Get the value of uscActivityTypeId
     */ 
    public function getUscActivityTypeId()
    {
        return $this->uscActivityTypeId;
    }

    /**
     * Set the value of uscActivityTypeId
     *
     * @return  self
     */ 
    public function setUscActivityTypeId($uscActivityTypeId)
    {
        $this->uscActivityTypeId = $uscActivityTypeId;

        return $this;
    }

    /**
     * Get the value of uscActivityType
     */ 
    public function getUscActivityType()
    {
        return $this->uscActivityType;
    }

    /**
     * Set the value of uscActivityType
     *
     * @return  self
     */ 
    public function setUscActivityType($uscActivityType)
    {
        $this->uscActivityType = $uscActivityType;

        return $this;
    }

    /**
     * Get the value of uscSubActivityTypeId
     */ 
    public function getUscSubActivityTypeId()
    {
        return $this->uscSubActivityTypeId;
    }

    /**
     * Set the value of uscSubActivityTypeId
     *
     * @return  self
     */ 
    public function setUscSubActivityTypeId($uscSubActivityTypeId)
    {
        $this->uscSubActivityTypeId = $uscSubActivityTypeId;

        return $this;
    }

    /**
     * Get the value of uscSubActivityType
     */ 
    public function getUscSubActivityType()
    {
        return $this->uscSubActivityType;
    }

    /**
     * Set the value of uscSubActivityType
     *
     * @return  self
     */ 
    public function setUscSubActivityType($uscSubActivityType)
    {
        $this->uscSubActivityType = $uscSubActivityType;

        return $this;
    }

    /**
     * Get the value of fundingAgenciesId
     */ 
    public function getFundingAgenciesId()
    {
        return $this->fundingAgenciesId;
    }

    /**
     * Set the value of fundingAgenciesId
     *
     * @return  self
     */ 
    public function setFundingAgenciesId($fundingAgenciesId)
    {
        $this->fundingAgenciesId = $fundingAgenciesId;

        return $this;
    }

    /**
     * Get the value of fundingAgencies
     */ 
    public function getFundingAgencies()
    {
        return $this->fundingAgencies;
    }

    /**
     * Set the value of fundingAgencies
     *
     * @return  self
     */ 
    public function setFundingAgencies($fundingAgencies)
    {
        $this->fundingAgencies = $fundingAgencies;

        return $this;
    }

    /**
     * Get the value of granted
     */ 
    public function getGranted()
    {
        return $this->granted;
    }

    /**
     * Set the value of granted
     *
     * @return  self
     */ 
    public function setGranted($granted)
    {
        $this->granted = $granted;

        return $this;
    }

    /**
     * Get the value of officeCode
     */ 
    public function getOfficeCode()
    {
        return $this->officeCode;
    }

    /**
     * Set the value of officeCode
     *
     * @return  self
     */ 
    public function setOfficeCode($officeCode)
    {
        $this->officeCode = $officeCode;

        return $this;
    }

    /**
     * Get the value of reference2
     */ 
    public function getReference2()
    {
        return $this->reference2;
    }

    /**
     * Set the value of reference2
     *
     * @return  self
     */ 
    public function setReference2($reference2)
    {
        $this->reference2 = $reference2;

        return $this;
    }

    /**
     * Get the value of ipCoordinatesUsersIDs
     */ 
    public function getIpCoordinatesUsersIDs()
    {
        return $this->ipCoordinatesUsersIDs;
    }

    /**
     * Set the value of ipCoordinatesUsersIDs
     *
     * @return  self
     */ 
    public function setIpCoordinatesUsersIDs($ipCoordinatesUsersIDs)
    {
        $this->ipCoordinatesUsersIDs = $ipCoordinatesUsersIDs;

        return $this;
    }

    /**
     * Get the value of ipCoordinatesUsers
     */ 
    public function getIpCoordinatesUsers()
    {
        return $this->ipCoordinatesUsers;
    }

    /**
     * Set the value of ipCoordinatesUsers
     *
     * @return  self
     */ 
    public function setIpCoordinatesUsers($ipCoordinatesUsers)
    {
        $this->ipCoordinatesUsers = $ipCoordinatesUsers;

        return $this;
    }

    /**
     * Get the value of ipUsersIDs
     */ 
    public function getIpUsersIDs()
    {
        return $this->ipUsersIDs;
    }

    /**
     * Set the value of ipUsersIDs
     *
     * @return  self
     */ 
    public function setIpUsersIDs($ipUsersIDs)
    {
        $this->ipUsersIDs = $ipUsersIDs;

        return $this;
    }

    /**
     * Get the value of ipUsers
     */ 
    public function getIpUsers()
    {
        return $this->ipUsers;
    }

    /**
     * Set the value of ipUsers
     *
     * @return  self
     */ 
    public function setIpUsers($ipUsers)
    {
        $this->ipUsers = $ipUsers;

        return $this;
    }

    /**
     * Get the value of associatedUsersIDs
     */ 
    public function getAssociatedUsersIDs()
    {
        return $this->associatedUsersIDs;
    }

    /**
     * Set the value of associatedUsersIDs
     *
     * @return  self
     */ 
    public function setAssociatedUsersIDs($associatedUsersIDs)
    {
        $this->associatedUsersIDs = $associatedUsersIDs;

        return $this;
    }

    /**
     * Get the value of associatedUsers
     */ 
    public function getAssociatedUsers()
    {
        return $this->associatedUsers;
    }

    /**
     * Set the value of associatedUsers
     *
     * @return  self
     */ 
    public function setAssociatedUsers($associatedUsers)
    {
        $this->associatedUsers = $associatedUsers;

        return $this;
    }

    /**
     * Get the value of technicianUsersIDs
     */ 
    public function getTechnicianUsersIDs()
    {
        return $this->technicianUsersIDs;
    }

    /**
     * Set the value of technicianUsersIDs
     *
     * @return  self
     */ 
    public function setTechnicianUsersIDs($technicianUsersIDs)
    {
        $this->technicianUsersIDs = $technicianUsersIDs;

        return $this;
    }

    /**
     * Get the value of technicianUsers
     */ 
    public function getTechnicianUsers()
    {
        return $this->technicianUsers;
    }

    /**
     * Set the value of technicianUsers
     *
     * @return  self
     */ 
    public function setTechnicianUsers($technicianUsers)
    {
        $this->technicianUsers = $technicianUsers;

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
     * Get the value of financingAgenciesIDs
     */ 
    public function getFinancingAgenciesIDs()
    {
        return $this->financingAgenciesIDs;
    }

    /**
     * Set the value of financingAgenciesIDs
     *
     * @return  self
     */ 
    public function setFinancingAgenciesIDs($financingAgenciesIDs)
    {
        $this->financingAgenciesIDs = $financingAgenciesIDs;

        return $this;
    }

    /**
     * Get the value of totalAmount
     */ 
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set the value of totalAmount
     *
     * @return  self
     */ 
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get the value of cofinancing
     */ 
    public function getCofinancing()
    {
        return $this->cofinancing;
    }

    /**
     * Set the value of cofinancing
     *
     * @return  self
     */ 
    public function setCofinancing($cofinancing)
    {
        $this->cofinancing = $cofinancing;

        return $this;
    }

    /**
     * Get the value of cofinancingType
     */ 
    public function getCofinancingType()
    {
        return $this->cofinancingType;
    }

    /**
     * Set the value of cofinancingType
     *
     * @return  self
     */ 
    public function setCofinancingType($cofinancingType)
    {
        $this->cofinancingType = $cofinancingType;

        return $this;
    }

    /**
     * Get the value of resolution
     */ 
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set the value of resolution
     *
     * @return  self
     */ 
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get the value of endDateExtended
     */ 
    public function getEndDateExtended()
    {
        return $this->endDateExtended;
    }

    /**
     * Set the value of endDateExtended
     *
     * @return  self
     */ 
    public function setEndDateExtended($endDateExtended)
    {
        $this->endDateExtended = $endDateExtended;

        return $this;
    }

    /**
     * Get the value of federPercent
     */ 
    public function getFederPercent()
    {
        return $this->federPercent;
    }

    /**
     * Set the value of federPercent
     *
     * @return  self
     */ 
    public function setFederPercent($federPercent)
    {
        $this->federPercent = $federPercent;

        return $this;
    }

    /**
     * Get the value of researchAreasIDs
     */ 
    public function getResearchAreasIDs()
    {
        return $this->researchAreasIDs;
    }

    /**
     * Set the value of researchAreasIDs
     *
     * @return  self
     */ 
    public function setResearchAreasIDs($researchAreasIDs)
    {
        $this->researchAreasIDs = $researchAreasIDs;

        return $this;
    }

    /**
     * Get the value of researchProgramsIDs
     */ 
    public function getResearchProgramsIDs()
    {
        return $this->researchProgramsIDs;
    }

    /**
     * Set the value of researchProgramsIDs
     *
     * @return  self
     */ 
    public function setResearchProgramsIDs($researchProgramsIDs)
    {
        $this->researchProgramsIDs = $researchProgramsIDs;

        return $this;
    }

    /**
     * Get the value of experimentsIDs
     */ 
    public function getExperimentsIDs()
    {
        return $this->experimentsIDs;
    }

    /**
     * Set the value of experimentsIDs
     *
     * @return  self
     */ 
    public function setExperimentsIDs($experimentsIDs)
    {
        $this->experimentsIDs = $experimentsIDs;

        return $this;
    }

    /**
     * Get the value of researchGroupID
     */ 
    public function getResearchGroupID()
    {
        return $this->researchGroupID;
    }

    /**
     * Set the value of researchGroupID
     *
     * @return  self
     */ 
    public function setResearchGroupID($researchGroupID)
    {
        $this->researchGroupID = $researchGroupID;

        return $this;
    }

    /**
     * Get the value of workLineId
     */ 
    public function getWorkLineId()
    {
        return $this->workLineId;
    }

    /**
     * Set the value of workLineId
     *
     * @return  self
     */ 
    public function setWorkLineId($workLineId)
    {
        $this->workLineId = $workLineId;

        return $this;
    }

    /**
     * Get the value of competitiveCall
     */ 
    public function getCompetitiveCall()
    {
        return $this->competitiveCall;
    }

    /**
     * Set the value of competitiveCall
     *
     * @return  self
     */ 
    public function setCompetitiveCall($competitiveCall)
    {
        $this->competitiveCall = $competitiveCall;

        return $this;
    }

    /**
     * Get the value of comment
     */ 
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the value of comment
     *
     * @return  self
     */ 
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the value of uscProjectReference
     */ 
    public function getUscProjectReference()
    {
        return $this->uscProjectReference;
    }

    /**
     * Set the value of uscProjectReference
     *
     * @return  self
     */ 
    public function setUscProjectReference($uscProjectReference)
    {
        $this->uscProjectReference = $uscProjectReference;

        return $this;
    }

    /**
     * Get the value of projectRequest
     */ 
    public function getProjectRequest()
    {
        return $this->projectRequest;
    }

    /**
     * Set the value of projectRequest
     *
     * @return  self
     */ 
    public function setProjectRequest($projectRequest)
    {
        $this->projectRequest = $projectRequest;

        return $this;
    }

    /**
     * Get the value of awardedAllotment
     */ 
    public function getAwardedAllotment()
    {
        return $this->awardedAllotment;
    }

    /**
     * Set the value of awardedAllotment
     *
     * @return  self
     */ 
    public function setAwardedAllotment($awardedAllotment)
    {
        $this->awardedAllotment = $awardedAllotment;

        return $this;
    }

    /**
     * Get the value of annualGifted
     */ 
    public function getAnnualGifted()
    {
        return $this->annualGifted;
    }

    /**
     * Set the value of annualGifted
     *
     * @return  self
     */ 
    public function setAnnualGifted($annualGifted)
    {
        $this->annualGifted = $annualGifted;

        return $this;
    }

    /**
     * Get the value of annualExecuted
     */ 
    public function getAnnualExecuted()
    {
        return $this->annualExecuted;
    }

    /**
     * Set the value of annualExecuted
     *
     * @return  self
     */ 
    public function setAnnualExecuted($annualExecuted)
    {
        $this->annualExecuted = $annualExecuted;

        return $this;
    }

    /**
     * Get the value of annualJustified
     */ 
    public function getAnnualJustified()
    {
        return $this->annualJustified;
    }

    /**
     * Set the value of annualJustified
     *
     * @return  self
     */ 
    public function setAnnualJustified($annualJustified)
    {
        $this->annualJustified = $annualJustified;

        return $this;
    }

    /**
     * Get the value of partners
     */ 
    public function getPartners()
    {
        return $this->partners;
    }

    /**
     * Set the value of partners
     *
     * @return  self
     */ 
    public function setPartners($partners)
    {
        $this->partners = $partners;

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
     * Get the value of validatedOn
     */ 
    public function getValidatedOn()
    {
        return $this->validatedOn;
    }

    /**
     * Set the value of validatedOn
     *
     * @return  self
     */ 
    public function setValidatedOn($validatedOn)
    {
        $this->validatedOn = $validatedOn;

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
     * Get the value of publishedOn
     */ 
    public function getPublishedOn()
    {
        return $this->publishedOn;
    }

    /**
     * Set the value of publishedOn
     *
     * @return  self
     */ 
    public function setPublishedOn($publishedOn)
    {
        $this->publishedOn = $publishedOn;

        return $this;
    }

    /**
     * Get the value of workTeam
     */ 
    public function getWorkTeam()
    {
        return $this->workTeam;
    }

    /**
     * Set the value of workTeam
     *
     * @return  self
     */ 
    public function setWorkTeam($workTeam)
    {
        $this->workTeam = $workTeam;

        return $this;
    }

    /**
     * Get the value of consortium
     */ 
    public function getConsortium()
    {
        return $this->consortium;
    }

    /**
     * Set the value of consortium
     *
     * @return  self
     */ 
    public function setConsortium($consortium)
    {
        $this->consortium = $consortium;

        return $this;
    }

    /**
     * Get the value of _projectCoordinator
     */ 
    public function getProjectCoordinator()
    {
        return $this->_projectCoordinator;
    }

    /**
     * Get the value of _principalResearcher
     */ 
    public function getPrincipalResearcher()
    {
        return $this->_principalResearcher;
    }

    /**
     * Get the value of _associateResearchers
     */ 
    public function getAssociateResearchers()
    {
        return $this->_associateResearchers;
    }

    /**
     * Get the value of _technical
     */ 
    public function getTechnical()
    {
        return $this->_technical;
    }

    /**
     * Set the value of _technical
     *
     * @return  self
     */ 
    public function setTechnical($_technical)
    {
        $this->_technical = $_technical;

        return $this;
    }
}
