<?php
class IntranetPrototype
{
    // FORMULARIO DE BUSQUEDA
    public $_id                        = NULL;
    public $_startDate                 = NULL;
    public $_endDate                   = NULL;
    public $_state                     = NULL;
    public $_name                      = NULL;
    public $_nameES                    = NULL;
    public $_nameEN                    = NULL;
    public $_nameGL                    = NULL;
    public $_publicationDate           = NULL;
    public $_type                      = NULL;
    public $_iGFAEResearchers          = NULL;
    public $_researchProgram           = NULL;
    public $_researchArea              = NULL;
    public $_experimentalCollaboration = NULL;
    public $_researchGroup             = NULL;
    public $_workLine                  = NULL;
    public $_iPCoordinators            = NULL;
    public $_validated                 = NULL;
    public $_validatedBy               = NULL;
    public $_validatedOn               = NULL;
    public $_promotionalDescription    = NULL;
    public $_promotionalDescriptionES  = NULL;
    public $_promotionalDescriptionEN  = NULL;
    public $_promotionalDescriptionGL  = NULL;
    public $_description               = NULL;
    public $_descriptionES             = NULL;
    public $_descriptionEN             = NULL;
    public $_descriptionGL             = NULL;
    public $_slug                      = NULL;
    public $_website                   = NULL;
    public $_access                    = NULL;
    public $_documentationAccess       = NULL;
    public $_file                      = NULL;
    public $_fileId                    = NULL;
    public $_otherResearchers          = NULL;
    public $_deliverables              = NULL;
    public $_projects                  = NULL;
    public $_organizationsLinked       = NULL;
    public $_comments                  = NULL;
    public $_deleteImage               = NULL;


    //TODO FORMULARIO INSERT

    public function __construct(
        $id = NULL,
        $startDate = NULL,
        $endDate = NULL,
        $state = NULL,
        $name = NULL,
        $nameES = NULL,
        $nameEN = NULL,
        $nameGL = NULL,
        $publicationDate = NULL,
        $type = NULL,
        $iGFAEResearchers = NULL,
        $researchProgram = NULL,
        $researchGroup = NULL,
        $researchArea = NULL,
        $experimentalCollaboration = NULL,
        $workLine = NULL,
        $iPCoordinators = NULL,
        $validated = NULL,
        $validatedBy = NULL,
        $validatedOn = NULL,
        $description = NULL,
        $descriptionES = NULL,
        $descriptionEN = NULL,
        $descriptionGL = NULL,
        $promotionalDescription = NULL,
        $promotionalDescriptionES = NULL,
        $promotionalDescriptionEN = NULL,
        $promotionalDescriptionGL = NULL,
        $slug = NULL,
        $website = NULL,
        $access = NULL,
        $documentationAccess = NULL,
        $file = NULL,
        $fileId = NULL,
        $otherResearchers = NULL,
        $deliverables = NULL,
        $projects = NULL,
        $organizationsLinked = NULL,
        $comments = NULL

    ) {
        $this->_id                           = $id;
        $this->_startDate                    = $startDate;
        $this->_endDate                      = $endDate;
        $this->_state                        = $state;
        $this->_name                         = $name;
        $this->_publicationDate              = $publicationDate;
        $this->_type                         = $type;
        $this->_iGFAEResearchers             = $iGFAEResearchers;
        $this->_researchProgram              = $researchProgram;
        $this->_researchArea                 = $researchArea;
        $this->_experimentalCollaboration    = $experimentalCollaboration;
        $this->_researchGroup                = $researchGroup;
        $this->_workLine                     = $workLine;
        $this->_iPCoordinators               = $iPCoordinators;
        $this->_validated                    = $validated;
        $this->_validatedBy                  = $validatedBy;
        $this->_validatedOn                  = $validatedOn;
        $this->_promotionalDescription       = $promotionalDescription;
        $this->_promotionalDescriptionES     = $promotionalDescriptionES;
        $this->_promotionalDescriptionEN     = $promotionalDescriptionEN;
        $this->_promotionalDescriptionGL     = $promotionalDescriptionGL;
        $this->_description                  = $description;
        $this->_descriptionES                = $descriptionES;
        $this->_descriptionEN                = $descriptionEN;
        $this->_descriptionGL                = $descriptionGL;
        $this->_slug                         = $slug;
        $this->_website                      = $website;
        $this->_access                       = $access;
        $this->_documentationAccess          = $documentationAccess;
        $this->_file                         = $file;
        $this->_fileId                       = $fileId;
        $this->_otherResearchers             = $otherResearchers;
        $this->_deliverables                 = $deliverables;
        $this->_projects                     = $projects;
        $this->_organizationsLinked          = $organizationsLinked;
        $this->_comments                     = $comments;
        $this->_nameES                       = $nameES;
        $this->_nameEN                       = $nameEN;
        $this->_nameGL                       = $nameGL;       
    }

    public function setIdPrototype($idPrototype)
    {
        $this->_id = $idPrototype;
    }
    public function getId()
    {
        return $this->_id;
    }
    public function getStartDate()
    {
        return $this->_startDate;
    }
    public function getEndDate()
    {
        return $this->_endDate;
    }
    public function getState()
    {
        return $this->_state;
    }
    public function getName()
    {
        return $this->_name;
    }
    public function getNameES()
    {
        return $this->_nameES;
    }
    public function getNameEN()
    {
        return $this->_nameEN;
    }
    public function getNameGL()
    {
        return $this->_nameGL;
    }
    public function getPublicationDate()
    {
        return $this->_publicationDate;
    }
    public function getType()
    {
        return $this->_type;
    }
    public function getIGFAEResearchers()
    {
        return $this->_iGFAEResearchers;
    }
    public function getResearchArea()
    {
        return $this->_researchArea;
    }
    public function getResearchProgram()
    {
        return $this->_researchProgram;
    }
    public function getExperimentalCollaboration()
    {
        return $this->_experimentalCollaboration;
    }
    public function getResearchGroup()
    {
        return $this->_researchGroup;
    }
    public function getWorkLine()
    {
        return $this->_workLine;
    }
    public function getIPCoordinator()
    {
        return $this->_iPCoordinators;
    }
    public function getValidated()
    {
        return $this->_validated;
    }
    public function getValidatedBy()
    {
        return $this->_validatedBy;
    }
    public function getValidatedOn()
    {
        return $this->_validatedOn;
    }
    public function getDescription()
    {
        return $this->_description;
    }

    public function getPromotionalDescription()
    {
        return $this->_promotionalDescription;
    }

    public function getPromotionalDescriptionES()
    {
        return $this->_promotionalDescriptionES;
    }

    public function getPromotionalDescriptionEN()
    {
        return $this->_promotionalDescriptionEN;
    }

    public function getPromotionalDescriptionGL()
    {
        return $this->_promotionalDescriptionGL;
    }

    public function getDescriptionES()
    {
        return $this->_descriptionES;
    }

    public function getDescriptionEN()
    {
        return $this->_descriptionEN;
    }

    public function getDescriptionGL()
    {
        return $this->_descriptionGL;
    }

    public function getSlug()
    {
        return $this->_slug;
    }

    public function getWebsite()
    {
        return $this->_website;
    }

    public function getAccess()
    {
        return $this->_access;
    }

    public function getDocumentationAccess()
    {
        return $this->_documentationAccess;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public function getFileId()
    {
        return $this->_fileId;
    }

    public function getOtherResearchers()
    {
        return $this->_otherResearchers;
    }

    public function getDeliverables()
    {
        return $this->_deliverables;
    }

    public function getProjects()
    {
        return $this->_projects;
    }

    public function getOrganizationsLinked()
    {
        return $this->_organizationsLinked;
    }

    public function getComments()
    {
        return $this->_comments;
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
}

