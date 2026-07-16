<?php
class IntranetExperimentalCollaboration
{
	// FORMULARIO DE BUSQUEDA
	public $_experimentalCollaborationID	  = NULL;
	public $_name              			  	  = NULL;
	public $_researchArea                	  = NULL;
	public $_researchProgram                  = NULL;
	public $_coordinatorIgfae   			  = NULL;
    public $_startDate        		          = NULL;
	public $_endDate                          = NULL;
	public $_consortium						  = NULL;
    public $_researchProject                  = NULL;
    public $_researchGroup                    = NULL;
    public $_workLine                         = NULL;
    public $_facilities                       = NULL;
    public $_validated                        = NULL;
	public $_published                        = NULL;
	public $_abbreviation					  = NULL;
	public $_mouDate						  = NULL;
	public $_mouLink						  = NULL;
	public $_webPage						  = NULL;
	public $_external						  = NULL;
	public $_abstract						  = NULL;
	public $_asociatedPublications			  = NULL;
	public $_asociatedUsers					  = NULL;
	public $_comments						  = NULL;
	public $_publishedBy                	  = NULL;
    public $_publishedOn                 	  = NULL;
    public $_validatedBy                	  = NULL;
    public $_validatedOn                 	  = NULL;

	public function __construct(
		$experimentalCollaborationId = NULL, 
		$name = NULL,
		$researchArea = NULL,
		$researchProgram = NULL,
		$coordinatorIgfae = NULL,
		$startDate = NULL,
		$endDate = NULL,
		$consortium		= NULL,
		$researchProject = NULL,
		$researchGroup = NULL ,
		$workLine = NULL,
		$facilities = NULL,
		$validated = NULL,
		$published = NULL,
		//Campos de pagina new
		$abbreviation = NULL,
		$mouDate = NULL,
		$mouLink = NULL,
		$webPage = NULL,
		$external = NULL,
		$abstract = NULL,
		$asociatedPublications = NULL,
		$asociatedUsers = NULL,
		$comments 	= NULL,
		$publishedBy             = NULL,
        $publishedOn             = NULL,
        $validatedBy             = NULL,
        $validatedOn             = NULL
	) {
		$this->_experimentalCollaborationID 		= $experimentalCollaborationId;
		$this->_name 		                        = $name;
		$this->_researchArea 		                = $researchArea;
		$this->_researchProgram 		            = $researchProgram;
		$this->_coordinatorIgfae 		            = $coordinatorIgfae;
		$this->_startDate 		                    = $startDate;
		$this->_endDate 		                    = $endDate;
		$this->_consortium							= $consortium;
		$this->_researchProject 		            = $researchProject;
		$this->_researchGroup 		                = $researchGroup;
		$this->_workLine 		                    = $workLine;
		$this->_facilities 		                    = $facilities;
		$this->_validated 		                    = $validated;
		$this->_published 		                    = $published;
		$this->_abbreviation						= $abbreviation;
		$this->_mouDate								= $mouDate;
		$this->_mouLink								= $mouLink;
		$this->_webPage								= $webPage;
		$this->_external							= $external;
		$this->_abstract							= $abstract;
		$this->_asociatedPublications				= $asociatedPublications;
		$this->_asociatedUsers						= $asociatedUsers;
		$this->_comments							= $comments;
		$this->_publishedBy                			= $publishedBy;
        $this->_publishedOn                 		= $publishedOn;
        $this->_validatedBy               			= $validatedBy;
        $this->_validatedOn                			= $validatedOn;
	}
	
	public function getExperimentalCollaborationId()
	{
		return $this->_experimentalCollaborationID;
	}
	public function getName()
	{
		return $this->_name;
	}
	public function getResearchArea()
	{
		return $this->_researchArea;
	}
	public function getResearchProgram()
	{
		return $this->_researchProgram;
	}
	public function getCoordinatorIgfae()
	{
		return $this->_coordinatorIgfae;
	}
	public function getStartDate()
	{
		return $this->_startDate;
	}
	public function getEndDate()
	{
		return $this->_endDate;
	}
	public function getResearchProject()
	{
		return $this->_researchProject;
	}
	public function getResearchGroup()
	{
		return $this->_researchGroup;
	}
	public function getWorkLine()
	{
		return $this->_workLine;
	}
	public function getFacilities()
	{
		return $this->_facilities;
	}
	public function getValidated()
	{
		return $this->_validated;
	}
	public function getPublished()
	{
		return $this->_published;
	}
	public function getAbbreviation()
	{
		return $this->_abbreviation;
	}
	public function getMouDate()
	{
		return $this->_mouDate;
	}
	public function getMouLink()
	{
		return $this->_mouLink;
	}
	public function getWebPage()
	{
		return $this->_webPage;
	}
	public function getExternal()
	{
		return $this->_external;
	}
	public function getAbstract()
	{
		return $this->_abstract;
	}
	public function getAsociatedPublications()
	{
		return $this->_asociatedPublications;
	}
	public function getAsociatedUsers()
	{
		return $this->_asociatedUsers;
	}
	public function getComments()
	{
		return $this->_comments;
	}
	public function setMouLink($mouLinks)
	{
		$this->_mouLink = $mouLinks;
	}
	public function setResearchArea($researchArea)
	{
		$this->_researchArea = $researchArea;
	}
	public function setResearchGroup($researchGroup)
	{
		$this->_researchGroup = $researchGroup;
	}
	public function setResearchProgram($researchProgram)
	{
		$this->_researchProgram = $researchProgram;
	}
	public function setResearchProject($researchProject)
	{
		$this->_researchProject = $researchProject;
	}
	public function setAsociatedPublications($asociatedPublications)
	{
		$this->_asociatedPublications = $asociatedPublications;
	}
	public function setWorkLine($workLine)
	{
		$this->_workLine = $workLine;
	}
	public function setCoordinatorIgfae($coordinatorIgfae)
	{
		$this->_coordinatorIgfae = $coordinatorIgfae;
	}
	public function setAsociatedUsers($asociatedUsers)
	{
		$this->_asociatedUsers = $asociatedUsers;
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
	public function getConsortium()
    {
        return $this->_consortium;
    }
}
