<?php
class IntranetPersonnel
{
	protected $_personnelId		                = NULL;
	protected $_name                            = NULL;

	public function __construct($personnelId = NULL, $name = NULL)
	{
		$this->_personnelId 		                    = $personnelId;
		$this->_name 		                = $name;
	}

	public function getPersonnelId()
	{
		return $this->_personnelId;
	}
	public function getName()
	{
		return $this->_name;
	}
}
class IntranetUser
{
	public $_personnelId		             = NULL;
	public $_wpUserId   		             = NULL;
	public $_name                            = NULL;
	public $_firstSurname                    = NULL;
	public $_secondSurname                   = NULL;
	public $_gender                          = NULL;
	public $_researchArea                    = NULL;
	public $_researchProgram                 = NULL;
	public $_workLine                        = NULL;
	public $_researchGroup                   = NULL;
	public $_collaborationExperiment         = NULL;
	public $_category         				 = NULL;
	public $_xuntaCategory                   = NULL;
	public $_igfaeCategory                   = NULL;
	public $_ueCategory                      = NULL;
	public $_igfaeDepartmentIDs              = NULL;
	public $_igfaeDepartment                 = NULL;
	public $_teamReference                   = NULL;
	public $_contractType                    = NULL;
	public $_validated                       = NULL;
	public $_published                       = NULL;
	public $_rol                             = NULL;
	public $_userReference                   = NULL;
	public $_birthDate                  	 = NULL;
	public $_idCard                   	     = NULL;
	public $_photoID                   	     = NULL;
	public $_photo                  	     = NULL;
	public $_phone                   	     = NULL;
	public $_mobile                   	     = NULL;
	public $_building                   	 = NULL;
	public $_location                   	 = NULL;
	public $_email                   	     = NULL;
	public $_orcid                     	     = NULL;
	public $_researchId                      = NULL;
	public $_scopus                     	 = NULL;
	public $_socialNetwork                   = NULL;
	public $_scientificFirm                  = NULL;
	public $_webSite                         = NULL;
	public $_country                         = NULL;
	public $_ue                              = NULL;
	public $_igfaeID                         = NULL;
	public $_biography                       = NULL;
	public $_mapLocation                     = NULL;
	public $_comments                        = NULL;
	public $_inspireRef                      = NULL;
	public $_foreign                         = NULL;
	public $_publishedBy                 = NULL;
	public $_publishedOn                 = NULL;
	public $_validatedBy                 = NULL;
	public $_validatedOn                 = NULL;
	public $_transfers     				 = NULL;
	public $_tutorWorkplacements     	 = NULL;
	public $_workplacements		 		 = NULL;
	public $_events		 				 = NULL;
	public $_productionProjects 		 = NULL;
	public $_researchProjects 			 = NULL;
	public $_startDate                 = NULL;
	public $_endDate                   = NULL;
	public $_prototypes                   = NULL;
	public $_deleteImage                   = NULL;	
	public $_userSupervisor                   = NULL;


	public function __construct()
	{
	}

	/**
	 * Get the value of _personnelId
	 */
	public function getPersonnelId()
	{
		return $this->_personnelId;
	}

	/**
	 * Set the value of _personnelId
	 *
	 * @return  self
	 */
	public function setPersonnelId($_personnelId)
	{
		$this->_personnelId = $_personnelId;

		return $this;
	}
	/**
	 * Get the value of _wpUserId
	 */
	public function getWpUserId()
	{
		return $this->_wpUserId;
	}

	/**
	 * Set the value of _wpUserId
	 *
	 * @return  self
	 */
	public function setWpUserId($_wpUserId)
	{
		$this->_wpUserId = $_wpUserId;

		return $this;
	}
	/**
	 * Get the value of _name
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Set the value of _name
	 *
	 * @return  self
	 */
	public function setName($_name)
	{
		$this->_name = $_name;

		return $this;
	}

	/**
	 * Get the value of _firstSurname
	 */
	public function getFirstSurname()
	{
		return $this->_firstSurname;
	}

	/**
	 * Set the value of _firstSurname
	 *
	 * @return  self
	 */
	public function setFirstSurname($_firstSurname)
	{
		$this->_firstSurname = $_firstSurname;

		return $this;
	}

	/**
	 * Get the value of _secondSurname
	 */
	public function getSecondSurname()
	{
		return $this->_secondSurname;
	}

	/**
	 * Set the value of _secondSurname
	 *
	 * @return  self
	 */
	public function setSecondSurname($_secondSurname)
	{
		$this->_secondSurname = $_secondSurname;

		return $this;
	}

	/**
	 * Get the value of _gender
	 */
	public function getGender()
	{
		return $this->_gender;
	}

	/**
	 * Set the value of _gender
	 *
	 * @return  self
	 */
	public function setGender($_gender)
	{
		$this->_gender = $_gender;

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
	public function setResearchArea($_researchArea)
	{
		$this->_researchArea = $_researchArea;

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
	public function setResearchProgram($_researchProgram)
	{
		$this->_researchProgram = $_researchProgram;

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
	public function setWorkLine($_workLine)
	{
		$this->_workLine = $_workLine;

		return $this;
	}

	/**
	 * Get the value of _collaborationExperiment
	 */
	public function getCollaborationExperiment()
	{
		return $this->_collaborationExperiment;
	}

	/**
	 * Set the value of _collaborationExperiment
	 *
	 * @return  self
	 */
	public function setCollaborationExperiment($_collaborationExperiment)
	{
		$this->_collaborationExperiment = $_collaborationExperiment;

		return $this;
	}

	/**
	 * Get the value of _xuntaCategory
	 */
	public function getXuntaCategory()
	{
		return $this->_xuntaCategory;
	}

	/**
	 * Set the value of _xuntaCategory
	 *
	 * @return  self
	 */
	public function setXuntaCategory($_xuntaCategory)
	{
		$this->_xuntaCategory = $_xuntaCategory;

		return $this;
	}

	/**
	 * Get the value of _igfaeCategory
	 */
	public function getIgfaeCategory()
	{
		return $this->_igfaeCategory;
	}

	/**
	 * Set the value of _igfaeCategory
	 *
	 * @return  self
	 */
	public function setIgfaeCategory($_igfaeCategory)
	{
		$this->_igfaeCategory = $_igfaeCategory;

		return $this;
	}

	/**
	 * Get the value of _ueCategory
	 */
	public function getUeCategory()
	{
		return $this->_ueCategory;
	}

	/**
	 * Set the value of _ueCategory
	 *
	 * @return  self
	 */
	public function setUeCategory($_ueCategory)
	{
		$this->_ueCategory = $_ueCategory;

		return $this;
	}

	/**
	 * Get the value of _igfaeDepartment
	 */
	public function getIgfaeDepartment()
	{
		return $this->_igfaeDepartment;
	}

	/**
	 * Set the value of _igfaeDepartment
	 *
	 * @return  self
	 */
	public function setIgfaeDepartment($_igfaeDepartment)
	{
		$this->_igfaeDepartment = $_igfaeDepartment;

		return $this;
	}

	/**
	 * Get the value of _igfaeDepartment
	 */
	public function getIgfaeDepartmentIDs()
	{
		return $this->_igfaeDepartmentIDs;
	}

	/**
	 * Set the value of _igfaeDepartment
	 *
	 * @return  self
	 */
	public function setIgfaeDepartmentIDs($_igfaeDepartmentIDs)
	{
		$this->_igfaeDepartmentIDs = $_igfaeDepartmentIDs;

		return $this;
	}


	/**
	 * Get the value of _teamReference
	 */
	public function getTeamReference()
	{
		return $this->_teamReference;
	}

	/**
	 * Set the value of _teamReference
	 *
	 * @return  self
	 */
	public function setTeamReference($_teamReference)
	{
		$this->_teamReference = $_teamReference;

		return $this;
	}

	/**
	 * Get the value of _contractType
	 */
	public function getContractType()
	{
		return $this->_contractType;
	}

	/**
	 * Set the value of _contractType
	 *
	 * @return  self
	 */
	public function setContractType($_contractType)
	{
		$this->_contractType = $_contractType;

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
	public function setValidated($_validated)
	{
		$this->_validated = $_validated;

		return $this;
	}
	/**
	 * Set the value of _validated
	 *
	 * @return  self
	 */
	public function setValidatedBy($_validated)
	{
		$this->_validatedBy = $_validated;

		return $this;
	}
	/**
	 * Set the value of _validated
	 *
	 * @return  self
	 */
	public function setValidatedOn($_validated)
	{
		$this->_validatedOn = $_validated;

		return $this;
	}
	/**
	 * Get the value of _published
	 */
	public function getPublished()
	{
		return $this->_published;
	}

	/**
	 * Set the value of _published
	 *
	 * @return  self
	 */
	public function setPublished($_published)
	{
		$this->_published = $_published;

		return $this;
	}
	/**
	 * Set the value of _published
	 *
	 * @return  self
	 */
	public function setPublishedBy($_published)
	{
		$this->_publishedBy = $_published;

		return $this;
	}
	/**
	 * Set the value of _published
	 *
	 * @return  self
	 */
	public function setPublishedOn($_published)
	{
		$this->_publishedOn = $_published;

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
	public function setResearchGroup($_researchGroup)
	{
		$this->_researchGroup = $_researchGroup;

		return $this;
	}
	/**
	 * Get the value of _category
	 */
	public function getCategory()
	{
		return $this->_category;
	}

	/**
	 * Set the value of _category
	 *
	 * @return  self
	 */
	public function setCategory($_category)
	{
		$this->_category = $_category;

		return $this;
	}

	/**
	 * Get the value of _rol
	 */
	public function getRol()
	{
		return $this->_rol;
	}

	/**
	 * Set the value of _rol
	 *
	 * @return  self
	 */
	public function setRol($_rol)
	{
		$this->_rol = $_rol;

		return $this;
	}

	/**
	 * Get the value of _userReference
	 */
	public function getUserReference()
	{
		return $this->_userReference;
	}

	/**
	 * Set the value of _userReference
	 *
	 * @return  self
	 */
	public function setUserReference($_userReference)
	{
		$this->_userReference = $_userReference;

		return $this;
	}

	/**
	 * Get the value of _birthDate
	 */
	public function getBirthDate()
	{
		return $this->_birthDate;
	}

	/**
	 * Set the value of _birthDate
	 *
	 * @return  self
	 */
	public function setBirthDate($_birthDate)
	{
		$this->_birthDate = $_birthDate;

		return $this;
	}

	/**
	 * Get the value of _idCard
	 */
	public function getIdCard()
	{
		return $this->_idCard;
	}

	/**
	 * Set the value of _idCard
	 *
	 * @return  self
	 */
	public function setIdCard($_idCard)
	{
		$this->_idCard = $_idCard;

		return $this;
	}

	/**
	 * Get the value of _photo
	 */
	public function getPhoto()
	{
		return $this->_photo;
	}

	/**
	 * Set the value of _photo
	 *
	 * @return  self
	 */
	public function setPhoto($_photo)
	{
		$this->_photo = $_photo;

		return $this;
	}
	/**
	 * Get the value of _photo
	 */
	public function getPhotoID()
	{
		return $this->_photoID;
	}

	/**
	 * Set the value of _photo
	 *
	 * @return  self
	 */
	public function setPhotoID($_photo)
	{
		$this->_photoID = $_photo;

		return $this;
	}
	/**
	 * Get the value of _phone
	 */
	public function getPhone()
	{
		return $this->_phone;
	}

	/**
	 * Set the value of _phone
	 *
	 * @return  self
	 */
	public function setPhone($_phone)
	{
		$this->_phone = $_phone;

		return $this;
	}

	/**
	 * Get the value of _mobile
	 */
	public function getMobile()
	{
		return $this->_mobile;
	}

	/**
	 * Set the value of _mobile
	 *
	 * @return  self
	 */
	public function setMobile($_mobile)
	{
		$this->_mobile = $_mobile;

		return $this;
	}

	/**
	 * Get the value of _building
	 */
	public function getBuilding()
	{
		return $this->_building;
	}

	/**
	 * Set the value of _building
	 *
	 * @return  self
	 */
	public function setBuilding($_building)
	{
		$this->_building = $_building;

		return $this;
	}

	/**
	 * Get the value of _location
	 */
	public function getLocation()
	{
		return $this->_location;
	}

	/**
	 * Set the value of _location
	 *
	 * @return  self
	 */
	public function setLocation($_location)
	{
		$this->_location = $_location;

		return $this;
	}

	/**
	 * Get the value of _orcid
	 */
	public function getOrcid()
	{
		return $this->_orcid;
	}

	/**
	 * Set the value of _orcid
	 *
	 * @return  self
	 */
	public function setOrcid($_orcid)
	{
		$this->_orcid = $_orcid;

		return $this;
	}

	/**
	 * Get the value of _researchId
	 */
	public function getResearchId()
	{
		return $this->_researchId;
	}

	/**
	 * Set the value of _researchId
	 *
	 * @return  self
	 */
	public function setResearchId($_researchId)
	{
		$this->_researchId = $_researchId;

		return $this;
	}

	/**
	 * Get the value of _scopus
	 */
	public function getScopus()
	{
		return $this->_scopus;
	}

	/**
	 * Set the value of _scopus
	 *
	 * @return  self
	 */
	public function setScopus($_scopus)
	{
		$this->_scopus = $_scopus;

		return $this;
	}
	/**
	 * Get the value of _inspireRef
	 */
	public function getInspireRef()
	{
		return $this->_inspireRef;
	}

	/**
	 * Set the value of _inspireRef
	 *
	 * @return  self
	 */
	public function setInspireRef($_inspireRef)
	{
		$this->_inspireRef = $_inspireRef;

		return $this;
	}
	/**
	 * Get the value of _email
	 */
	public function getEmail()
	{
		return $this->_email;
	}

	/**
	 * Set the value of _email
	 *
	 * @return  self
	 */
	public function setEmail($_email)
	{
		$this->_email = $_email;

		return $this;
	}

	/**
	 * Get the value of _socialNetwork
	 */
	public function getSocialNetwork()
	{
		return $this->_socialNetwork;
	}

	/**
	 * Set the value of _socialNetwork
	 *
	 * @return  self
	 */
	public function setSocialNetwork($_socialNetwork)
	{
		$this->_socialNetwork = $_socialNetwork;

		return $this;
	}

	/**
	 * Get the value of _scientificFirm
	 */
	public function getScientificFirm()
	{
		return $this->_scientificFirm;
	}

	/**
	 * Set the value of _scientificFirm
	 *
	 * @return  self
	 */
	public function setScientificFirm($_scientificFirm)
	{
		$this->_scientificFirm = $_scientificFirm;

		return $this;
	}

	/**
	 * Get the value of _webSite
	 */
	public function getWebSite()
	{
		return $this->_webSite;
	}

	/**
	 * Set the value of _webSite
	 *
	 * @return  self
	 */
	public function setWebSite($_webSite)
	{
		$this->_webSite = $_webSite;

		return $this;
	}

	/**
	 * Get the value of _country
	 */
	public function getCountry()
	{
		return $this->_country;
	}

	/**
	 * Set the value of _country
	 *
	 * @return  self
	 */
	public function setCountry($_country)
	{
		$this->_country = $_country;

		return $this;
	}
	/**
	 * Get the value of _transfers
	 */
	public function getTransfers()
	{
		return $this->_transfers;
	}

	/**
	 * Set the value of _country
	 *
	 * @return  self
	 */
	public function setTransfers($_transfers)
	{
		$this->_transfers = $_transfers;

		return $this;
	}
	/**
	 * Get the value of _tutorWorkplacements
	 */
	public function getTutorWorkplacements()
	{
		return $this->_tutorWorkplacements;
	}

	/**
	 * Set the value of _tutorWorkplacements
	 *
	 * @return  self
	 */
	public function setTutorWorkplacements($_wp)
	{
		$this->_tutorWorkplacements = $_wp;

		return $this;
	}
	/**
	 * Get the value of _workplacements
	 */
	public function getWorkplacements()
	{
		return $this->_workplacements;
	}

	/**
	 * Set the value of _tutorWorkplacements
	 *
	 * @return  self
	 */
	public function setWorkplacements($_wp)
	{
		$this->_workplacements = $_wp;

		return $this;
	}
	/**
	 * Get the value of _events
	 */
	public function getEvents()
	{
		return $this->_events;
	}

	/**
	 * Set the value of _events
	 *
	 * @return  self
	 */
	public function setEvents($_event)
	{
		$this->_events = $_event;

		return $this;
	}
	/**
	 * Get the value of _productionProjects
	 */
	public function getProductionProject()
	{
		return $this->_productionProjects;
	}

	/**
	 * Set the value of _productionProjects
	 *
	 * @return  self
	 */
	public function setProductionProject($_productionProjects)
	{
		$this->_productionProjects = $_productionProjects;

		return $this;
	}
	/**
	 * Get the value of _researchProjects
	 */
	public function getResearchProject()
	{
		return $this->_researchProjects;
	}

	/**
	 * Set the value of _researchProjects
	 *
	 * @return  self
	 */
	public function setResearchProject($_researchProjects)
	{
		$this->_researchProjects = $_researchProjects;

		return $this;
	}
	/**
	 * Get the value of _ue
	 */
	public function getUe()
	{
		return $this->_ue;
	}

	/**
	 * Set the value of _ue
	 *
	 * @return  self
	 */
	public function setUe($_ue)
	{
		$this->_ue = $_ue;

		return $this;
	}

	/**
	 * Get the value of _igfaeID
	 */
	public function getIgfaeID()
	{
		return $this->_igfaeID;
	}

	/**
	 * Set the value of _igfaeID
	 *
	 * @return  self
	 */
	public function setIgfaeID($_igfaeID)
	{
		$this->_igfaeID = $_igfaeID;

		return $this;
	}

	/**
	 * Get the value of _biography
	 */
	public function getBiography()
	{
		return $this->_biography;
	}

	/**
	 * Set the value of _biography
	 *
	 * @return  self
	 */
	public function setBiography($_biography)
	{
		$this->_biography = $_biography;

		return $this;
	}

	/**
	 * Get the value of _mapLocation
	 */
	public function getMapLocation()
	{
		return $this->_mapLocation;
	}

	/**
	 * Set the value of _mapLocation
	 *
	 * @return  self
	 */
	public function setMapLocation($_mapLocation)
	{
		$this->_mapLocation = $_mapLocation;

		return $this;
	}

	/**
	 * Get the value of _comments
	 */
	public function getComments()
	{
		return $this->_comments;
	}

	/**
	 * Set the value of _comments
	 *
	 * @return  self
	 */
	public function setComments($_comments)
	{
		$this->_comments = $_comments;

		return $this;
	}

	/**
	 * Get the value of _foreign
	 */
	public function getForeign()
	{
		return $this->_foreign;
	}

	/**
	 * Set the value of _foreign
	 *
	 * @return  self
	 */
	public function setForeign($_foreign)
	{
		$this->_foreign = $_foreign;

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
	public function setStartDate($_startDate)
	{
		$this->_startDate = $_startDate;

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
	public function setEndDate($_endDate)
	{
		$this->_endDate = $_endDate;

		return $this;
	}	
	/**
	 * Get the value of _currentContratStartDate
	 */
	public function getCurrentContratStartDate()
	{
		return $this->_currentContratStartDate;
	}

	/**
	 * Set the value of _currentContratStartDate
	 *
	 * @return  self
	 */
	public function setCurrentContratStartDate($_currentContratStartDate)
	{
		$this->_currentContratStartDate = $_currentContratStartDate;

		return $this;
	}	
	/**
	 * Get the value of _currentContratStartDate
	 */
	public function getCurrentContratEndDate()
	{
		return $this->_currentContratEndDate;
	}

	/**
	 * Set the value of _currentContratStartDate
	 *
	 * @return  self
	 */
	public function setCurrentContratEndDate($_currentContratEndDate)
	{
		$this->_currentContratEndDate = $_currentContratEndDate;

		return $this;
	}	
	
	/**
	 * Get the value of _prototypes
	 */ 
	public function getPrototypes()
	{
		return $this->_prototypes;
	}

	/**
	 * Set the value of _prototypes
	 *
	 * @return  self
	 */ 
	public function setPrototypes($_prototypes)
	{
		$this->_prototypes = $_prototypes;

		return $this;
	}

	/**
	 * Get the value of _deleteImage
	 */ 
	public function getDeleteImage()
	{
		return $this->_deleteImage;
	}

	/**
	 * Set the value of _deleteImage
	 *
	 * @return  self
	 */ 
	public function setDeleteImage($_deleteImage)
	{
		$this->_deleteImage = $_deleteImage;

		return $this;
	}

	/**
	 * Get the value of _userSupervisor
	 */
	public function getUserSupervisor()
	{
		return $this->_userSupervisor;
	}

	/**
	 * Set the value of _userSupervisor
	 *
	 * @return  self
	 */
	public function setUserSupervisor($_userSupervisor)
	{
		$this->_userSupervisor = $_userSupervisor;

		return $this;
	}
}
