<?php
class IntranetAuthorizationRequest
{
	public $user	            = NULL;
    public $entity              = NULL;
    public $requestDate         =   NULL;
    public $authorizationDate   =   NULL;
    public $status              =   NULL;

	public function __construct($user = NULL, $entity = NULL, $requestDate = NULL, $authorizationDate = NULL, $status = NULL)
	{
		$this->user 		                = $user;
        $this->entity 		                = $entity;
        $this->requestDate 		            = $requestDate;
        $this->authorizationDate 		    = $authorizationDate;
        $this->status 		                = $status;
	}


	/**
	 * Get the value of user
	 */ 
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set the value of user
	 *
	 * @return  self
	 */ 
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

    /**
     * Get the value of entity
     */ 
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the value of entity
     *
     * @return  self
     */ 
    public function setEntity($entity)
    {
        $this->entity = $entity;

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
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
