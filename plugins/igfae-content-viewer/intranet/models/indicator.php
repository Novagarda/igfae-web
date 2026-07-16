<?php
class IntranetIndicator
{
    //campos busqueda
    public $indicator = NULL;
    public $rangeArticle = NULL;
    public $rangeIndexH = NULL;
    public $externalCollaboration = NULL;
    public $magazine = NULL;
    public $source = NULL;

    public $title = NULL;
    public $doi = NULL;
    public $cites = NULL;
    public $researchArea = NULL;
    public $researchProgram = NULL;
    public $collaboration = NULL;
    public $researchGroup = NULL;
    public $workLine = NULL;
    public $authors = NULL;
    public $publicationDate = NULL;
    public $updateDate = NULL;
    public $indexH = NULL;
    

    // Garants
    public $gindex = NULL;
    public $personnelID = NULL;
    public $name = NULL;
    public $externalSourceID = NULL;
  

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of doi
     */ 
    public function getDoi()
    {
        return $this->doi;
    }

    /**
     * Set the value of doi
     *
     * @return  self
     */ 
    public function setDoi($doi)
    {
        $this->doi = $doi;

        return $this;
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
     * Get the value of collaboration
     */ 
    public function getCollaboration()
    {
        return $this->collaboration;
    }

    /**
     * Set the value of collaboration
     *
     * @return  self
     */ 
    public function setCollaboration($collaboration)
    {
        $this->collaboration = $collaboration;

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
     * Get the value of workLine
     */ 
    public function getWorkLine()
    {
        return $this->workLine;
    }

    /**
     * Set the value of workLine
     *
     * @return  self
     */ 
    public function setWorkLine($workLine)
    {
        $this->workLine = $workLine;

        return $this;
    }

    /**
     * Get the value of authors
     */ 
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set the value of authors
     *
     * @return  self
     */ 
    public function setAuthors($authors)
    {
        $this->authors = $authors;

        return $this;
    }

    /**
     * Get the value of publicationDate
     */ 
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * Set the value of publicationDate
     *
     * @return  self
     */ 
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    /**
     * Get the value of updateDate
     */ 
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Set the value of updateDate
     *
     * @return  self
     */ 
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get the value of indicator
     */ 
    public function getIndicator()
    {
        return $this->indicator;
    }

    /**
     * Set the value of indicator
     *
     * @return  self
     */ 
    public function setIndicator($indicator)
    {
        $this->indicator = $indicator;

        return $this;
    }

    /**
     * Get the value of rangeArticle
     */ 
    public function getRangeArticle()
    {
        return $this->rangeArticle;
    }

    /**
     * Set the value of rangeArticle
     *
     * @return  self
     */ 
    public function setRangeArticle($rangeArticle)
    {
        $this->rangeArticle = $rangeArticle;

        return $this;
    }

    /**
     * Get the value of rangeIndexH
     */ 
    public function getRangeIndexH()
    {
        return $this->rangeIndexH;
    }

    /**
     * Set the value of rangeIndexH
     *
     * @return  self
     */ 
    public function setRangeIndexH($rangeIndexH)
    {
        $this->rangeIndexH = $rangeIndexH;

        return $this;
    }

    /**
     * Get the value of externalCollaboration
     */ 
    public function getExternalCollaboration()
    {
        return $this->externalCollaboration;
    }

    /**
     * Set the value of externalCollaboration
     *
     * @return  self
     */ 
    public function setExternalCollaboration($externalCollaboration)
    {
        $this->externalCollaboration = $externalCollaboration;

        return $this;
    }

    /**
     * Get the value of magazine
     */ 
    public function getMagazine()
    {
        return $this->magazine;
    }

    /**
     * Set the value of magazine
     *
     * @return  self
     */ 
    public function setMagazine($magazine)
    {
        $this->magazine = $magazine;

        return $this;
    }

    /**
     * Get the value of indexH
     */ 
    public function getIndexH()
    {
        return $this->indexH;
    }

    /**
     * Set the value of indexH
     *
     * @return  self
     */ 
    public function setIndexH($indexH)
    {
        $this->indexH = $indexH;

        return $this;
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

    /**
     * Get the value of source
     */ 
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get the value of gindex
     */ 
    public function getGindex()
    {
        return $this->gindex;
    }

    /**
     * Set the value of gindex
     *
     * @return  self
     */ 
    public function setGindex($gindex)
    {
        $this->gindex = $gindex;

        return $this;
    }
	
	/**
     * Get the value of personnelID
     */ 
    public function getPersonnelID()
    {
        return $this->personnelID;
    }

    /**
     * Set the value of personnelID
     *
     * @return  self
     */ 
    public function setPersonnelID($personnelID)
    {
        $this->personnelID = $personnelID;

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
     * Get the value of externalSourceID
     */ 
    public function getExternalSourceID()
    {
        return $this->externalSourceID;
    }

    /**
     * Set the value of externalSourceID
     *
     * @return  self
     */ 
    public function setExternalSourceID($externalSourceID)
    {
        $this->externalSourceID = $externalSourceID;

        return $this;
    }

    /**
     * Get the value of cites
     */ 
    public function getCites()
    {
        return $this->cites;
    }

    /**
     * Set the value of cites
     *
     * @return  self
     */ 
    public function setCites($cites)
    {
        $this->cites = $cites;

        return $this;
    }
}