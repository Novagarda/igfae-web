<?php
class IntranetMonitorType {
    public $monitorTypeId  = NULL;
    public $name  = NULL;
    public $description  = NULL;

    

    /**
     * Get the value of monitorTypeId
     */ 
    public function getMonitorTypeId()
    {
        return $this->monitorTypeId;
    }

    /**
     * Set the value of monitorTypeId
     *
     * @return  self
     */ 
    public function setMonitorTypeId($monitorTypeId)
    {
        $this->monitorTypeId = $monitorTypeId;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

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
}