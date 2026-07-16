<?php
class IntranetAuthorization
{
	public $authorizationID	            = NULL;
    public $requesterID                 = NULL;
    public $requesterName               =   NULL;
    public $entityNameID                =   NULL;
    public $entityName                  =   NULL;
    public $entityID                    =   NULL;
    public $requestDate                 =   NULL;
    public $authorizationDate           =   NULL;
    public $authorizerID                =   NULL;
    public $authorizerBy                =   NULL;
    public $authorizationStatusID       =   NULL;
    public $statusName                  =   NULL;
    public $requestComment              =   NULL;
    public $authorizationComment        =   NULL;
    public $applyDate                   =   NULL;

	public function __construct(
        $authorizationID = NULL, 
        $requesterID = NULL, 
        $requesterName = NULL, 
        $entityNameID = NULL, 
        $entityName = NULL, 
        $entityID = NULL, 
        $requestDate = NULL, 
        $authorizationDate = NULL, 
        $authorizerID = NULL, 
        $authorizerBy = NULL, 
        $authorizationStatusID = NULL, 
        $statusName = NULL, 
        $requestComment = NULL, 
        $authorizationComment = NULL,
        $applyDate = NULL)
	{
		$this->authorizationID 		    = $authorizationID;
        $this->requesterID 		        = $requesterID;
        $this->requesterName 		    = $requesterName;
        $this->entityNameID 		    = $entityNameID;
        $this->entityName 		        = $entityName;
        $this->entityID 		        = $entityID;
        $this->requestDate 		        = $requestDate;
        $this->requestDate 		        = $requestDate;
        $this->authorizationDate 		= $authorizationDate;
        $this->authorizerID 		    = $authorizerID;
        $this->authorizerBy 		    = $authorizerBy;
        $this->authorizationStatusID 	= $authorizationStatusID;
        $this->statusName 		        = $statusName;
        $this->requestComment 		    = $requestComment;
        $this->authorizationComment 	= $authorizationComment;
        $this->applyDate 	            = $applyDate;
	}



    /**
     * Get the value of authorizationComment
     */ 
    public function getAuthorizationComment()
    {
        return $this->authorizationComment;
    }

    /**
     * Set the value of authorizationComment
     *
     * @return  self
     */ 
    public function setAuthorizationComment($authorizationComment)
    {
        $this->authorizationComment = $authorizationComment;

        return $this;
    }

    /**
     * Get the value of requestComment
     */ 
    public function getRequestComment()
    {
        return $this->requestComment;
    }

    /**
     * Set the value of requestComment
     *
     * @return  self
     */ 
    public function setRequestComment($requestComment)
    {
        $this->requestComment = $requestComment;

        return $this;
    }

    /**
     * Get the value of statusName
     */ 
    public function getStatusName()
    {
        return $this->statusName;
    }

    /**
     * Set the value of statusName
     *
     * @return  self
     */ 
    public function setStatusName($statusName)
    {
        $this->statusName = $statusName;

        return $this;
    }

    /**
     * Get the value of authorizationStatusID
     */ 
    public function getAuthorizationStatusID()
    {
        return $this->authorizationStatusID;
    }

    /**
     * Set the value of authorizationStatusID
     *
     * @return  self
     */ 
    public function setAuthorizationStatusID($authorizationStatusID)
    {
        $this->authorizationStatusID = $authorizationStatusID;

        return $this;
    }

    /**
     * Get the value of authorizerBy
     */ 
    public function getAuthorizerBy()
    {
        return $this->authorizerBy;
    }

    /**
     * Set the value of authorizerBy
     *
     * @return  self
     */ 
    public function setAuthorizerBy($authorizerBy)
    {
        $this->authorizerBy = $authorizerBy;

        return $this;
    }

    /**
     * Get the value of authorizerID
     */ 
    public function getAuthorizerID()
    {
        return $this->authorizerID;
    }

    /**
     * Set the value of authorizerID
     *
     * @return  self
     */ 
    public function setAuthorizerID($authorizerID)
    {
        $this->authorizerID = $authorizerID;

        return $this;
    }

    /**
     * Get the value of authorizationDate
     */ 
    public function getAuthorizationDate()
    {
        return $this->authorizationDate;
    }

    /**
     * Set the value of authorizationDate
     *
     * @return  self
     */ 
    public function setAuthorizationDate($authorizationDate)
    {
        $this->authorizationDate = $authorizationDate;

        return $this;
    }

    /**
     * Get the value of requestDate
     */ 
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set the value of requestDate
     *
     * @return  self
     */ 
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * Get the value of entityID
     */ 
    public function getEntityID()
    {
        return $this->entityID;
    }

    /**
     * Set the value of entityID
     *
     * @return  self
     */ 
    public function setEntityID($entityID)
    {
        $this->entityID = $entityID;

        return $this;
    }

    /**
     * Get the value of entityName
     */ 
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Set the value of entityName
     *
     * @return  self
     */ 
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * Get the value of entityNameID
     */ 
    public function getEntityNameID()
    {
        return $this->entityNameID;
    }

    /**
     * Set the value of entityNameID
     *
     * @return  self
     */ 
    public function setEntityNameID($entityNameID)
    {
        $this->entityNameID = $entityNameID;

        return $this;
    }

    /**
     * Get the value of requesterName
     */ 
    public function getRequesterName()
    {
        return $this->requesterName;
    }

    /**
     * Set the value of requesterName
     *
     * @return  self
     */ 
    public function setRequesterName($requesterName)
    {
        $this->requesterName = $requesterName;

        return $this;
    }

    /**
     * Get the value of requesterID
     */ 
    public function getRequesterID()
    {
        return $this->requesterID;
    }

    /**
     * Set the value of requesterID
     *
     * @return  self
     */ 
    public function setRequesterID($requesterID)
    {
        $this->requesterID = $requesterID;

        return $this;
    }

	/**
	 * Get the value of authorizationID
	 */ 
	public function getAuthorizationID()
	{
		return $this->authorizationID;
	}

	/**
	 * Set the value of authorizationID
	 *
	 * @return  self
	 */ 
	public function setAuthorizationID($authorizationID)
	{
		$this->authorizationID = $authorizationID;

		return $this;
	}

    /**
     * Get the value of applyDate
     */ 
    public function getApplyDate()
    {
        return $this->applyDate;
    }

    /**
     * Set the value of applyDate
     *
     * @return  self
     */ 
    public function setApplyDate($applyDate)
    {
        $this->applyDate = $applyDate;

        return $this;
    }
}
