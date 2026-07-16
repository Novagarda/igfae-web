<?php
class IntranetProductionProject
{
    // FORMULARIO DE BUSQUEDA
    public $_id                          = NULL;
    public $_typeActivity                = NULL;
    public $_title                         = NULL;
    public $_titleES                       = NULL;
    public $_titleEN                        = NULL;
    public $_titleGL                        = NULL;
    public $_researchArea                = NULL;
    public $_researchProgram                = NULL;
    public $_collaborationExperiment        = NULL;
    public $_researchGroup                = NULL;
    public $_journal                    = NULL;
    public $_articleNumber                = NULL;
    public $_spiIndexing                    = NULL;
    public $_doi                         = NULL;
    public $_thesisType                  = NULL;
    public $_internationalMention        = NULL;
    public $_keywords                    = NULL;
    public $_authors                       = NULL;
    public $_thesisDirector                 = NULL;
    public $_externalAuthor                = NULL;
    public $_exthernalThesisDirector     = NULL;
    public $_journalEditorial           = NULL;
    public $_citation                    = NULL;
    public $_validated                     = NULL;
    public $_published                    = NULL;
    public $_orcid                    = NULL;

    // FORMULARIO INSERT
    public $_researchInspire             = NULL;
    public $_isbn                        = NULL;
    public $_acceptanceDate              = NULL;
    public $_publicationDate             = NULL;
    public $_thesisReadingDate           = NULL;
    public $_thesisReadingPlace          = NULL;
    public $_qualification               = NULL;
    public $_linkArticle                 = NULL;
    public $_linkThesisCertificate       = NULL;
    public $_volume                      = NULL;
    public $_chapterBook                 = NULL;
    public $_homepage                    = NULL;
    public $_finalPage                   = NULL;
    public $_numberPages                 = NULL;
    public $_numberAuthors               = NULL;
    public $_numberAuthorsIgfae          = NULL;
    public $_abstract                    = NULL;
    public $_linkProductionProject       = NULL;
    public $_comments                    = NULL;
    public $_researchProject             = NULL;
    public $_publishedBy                 = NULL;
    public $_publishedOn                 = NULL;
    public $_validatedBy                 = NULL;
    public $_validatedOn                 = NULL;
    public $_internationalCoAuthorship   = NULL;
    public $_externalSources             = NULL;

    public function __construct(
        $id = NULL,
        $typeActivity = NULL,
        $title = NULL,
        $titleES = NULL,
        $titleEN = NULL,
        $titleGL = NULL,
        $researchArea = NULL,
        $researchProgram = NULL,
        $collaborationExperiment = NULL,
        $researchGroup = NULL,
        $journal = NULL,
        $articleNumber = NULL,
        $spiIndexing = NULL,
        $doi = NULL,
        $thesisType = NULL,
        $internationalMention = NULL,
        $keywords = NULL,
        $authors = NULL,
        $thesisDirector = NULL,
        $externalAuthor = NULL,
        $exthernalThesisDirector = NULL,
        $journalEditorial = NULL,
        $citation = NULL,
        $validated = NULL,
        $published = NULL,
        // Campos new
        $researchInspire             = NULL,
        $acceptanceDate              = NULL,
        $publicationDate             = NULL,
        $thesisReadingDate           = NULL,
        $thesisReadingPlace          = NULL,
        $qualification               = NULL,
        $isbn                        = NULL,
        $linkArticle                 = NULL,
        $linkThesisCertificate       = NULL,
        $volume                      = NULL,
        $chapterBook                 = NULL,
        $homepage                    = NULL,
        $finalPage                   = NULL,
        $numberPages                 = NULL,
        $numberAuthors               = NULL,
        $numberAuthorsIgfae          = NULL,
        $abstract                    = NULL,
        $linkProductionProject       = NULL,
        $comments                    = NULL,
        $researchProject             = NULL,
        $publishedBy             = NULL,
        $publishedOn             = NULL,
        $validatedBy             = NULL,
        $validatedOn             = NULL,
        $internationalCoAuthorship = NULL,
        $externalSources           = NULL,
        $orcid = NULL
    ) {
        $this->_id                                  = $id;
        $this->_typeActivity                     = $typeActivity;
        $this->_title                             = $title;
        $this->_titleES                             = $titleES;
        $this->_titleEN                             = $titleEN;
        $this->_titleGL                             = $titleGL;
        $this->_researchArea                     = $researchArea;
        $this->_researchProgram                 = $researchProgram;
        $this->_collaborationExperiment         = $collaborationExperiment;
        $this->_researchGroup                     = $researchGroup;
        $this->_journal                         = $journal;
        $this->_articleNumber                     = $articleNumber;
        $this->_spiIndexing                     = $spiIndexing;
        $this->_doi                             = $doi;
        $this->_thesisType                         = $thesisType;
        $this->_internationalMention             = $internationalMention;
        $this->_keywords                         = $keywords;
        $this->_authors                         = $authors;
        $this->_thesisDirector                     = $thesisDirector;
        $this->_externalAuthor                     = $externalAuthor;
        $this->_exthernalThesisDirector         = $exthernalThesisDirector;
        $this->_journalEditorial                 = $journalEditorial;
        $this->_citation                         = $citation;
        $this->_validated                         = $validated;
        $this->_published                         = $published;
        $this->_orcid                             = $orcid;
        $this->_researchInspire                   = $researchInspire;
        $this->_isbn                   = $isbn;
        $this->_acceptanceDate                   = $acceptanceDate;
        $this->_publicationDate                   = $publicationDate;
        $this->_thesisReadingDate                   = $thesisReadingDate;
        $this->_thesisReadingPlace                   = $thesisReadingPlace;
        $this->_qualification                   = $qualification;
        $this->_linkArticle                   = $linkArticle;
        $this->_linkThesisCertificate                   = $linkThesisCertificate;
        $this->_volume                   = $volume;
        $this->_chapterBook                   = $chapterBook;
        $this->_homepage                   = $homepage;
        $this->_finalPage                   = $finalPage;
        $this->_numberPages                   = $numberPages;
        $this->_numberAuthors                   = $numberAuthors;
        $this->_numberAuthorsIgfae                   = $numberAuthorsIgfae;
        $this->_abstract                   = $abstract;
        $this->_linkProductionProject                   = $linkProductionProject;
        $this->_comments                   = $comments;
        $this->_researchProject                   = $researchProject;
        $this->_publishedBy                 = $publishedBy;
        $this->_publishedOn                 = $publishedOn;
        $this->_validatedBy                 = $validatedBy;
        $this->_validatedOn                 = $validatedOn;
        $this->_internationalCoAuthorship   = $internationalCoAuthorship;
        $this->_externalSources   = $externalSources;
    }

    public function setIdProject($idProject)
    {
        $this->_id = $idProject;
    }
    public function getId()
    {
        return $this->_id;
    }
    public function getTypeActivity()
    {
        return $this->_typeActivity;
    }
    public function getIsbn()
    {
        return $this->_isbn;
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
    public function getResearchInspire()
    {
        return $this->_researchInspire;
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
    public function getJournal()
    {
        return $this->_journal;
    }
    public function getArticleNumber()
    {
        return $this->_articleNumber;
    }
    public function getSpiIndexing()
    {
        return $this->_spiIndexing;
    }
    public function getDoi()
    {
        return $this->_doi;
    }
    public function getThesisType()
    {
        return $this->_thesisType;
    }
    public function getInternationalMention()
    {
        return $this->_internationalMention;
    }
    public function getKeywords()
    {
        return $this->_keywords;
    }
    public function getAuthors()
    {
        return $this->_authors;
    }
    public function getThesisDirector()
    {
        return $this->_thesisDirector;
    }
    public function getExternalAuthor()
    {
        return $this->_externalAuthor;
    }
    public function getExternalThesisDirector()
    {
        return $this->_exthernalThesisDirector;
    }
    public function getJournalEditorial()
    {
        return $this->_journalEditorial;
    }
    public function getCitation()
    {
        return $this->_citation;
    }
    public function getValidated()
    {
        return $this->_validated;
    }
    public function getPublished()
    {
        return $this->_published;
    }
    public function getAcceptanceDate()
    {
        return $this->_acceptanceDate;
    }
    public function getPublicationDate()
    {
        return $this->_publicationDate;
    }
    public function getThesisReadingDate()
    {
        return $this->_thesisReadingDate;
    }
    public function getThesisReadingPlace()
    {
        return $this->_thesisReadingPlace;
    }
    public function getQualification()
    {
        return $this->_qualification;
    }
    public function getLinkArticle()
    {
        return $this->_linkArticle;
    }
    public function getLinkThesisCertificate()
    {
        return $this->_linkThesisCertificate;
    }
    public function getVolume()
    {
        return $this->_volume;
    }
    public function getChapterBook()
    {
        return $this->_chapterBook;
    }
    public function getHomePage()
    {
        return $this->_homepage;
    }
    public function getFinalPage()
    {
        return $this->_finalPage;
    }
    public function getNumberPages()
    {
        return $this->_numberPages;
    }
    public function getNumberAuthors()
    {
        return $this->_numberAuthors;
    }
    public function getNumberAuthorsIgfae()
    {
        return $this->_numberAuthorsIgfae;
    }
    public function getAbstract()
    {
        return $this->_abstract;
    }
    public function getLinkProductionProject()
    {
        return $this->_linkProductionProject;
    }
    public function getComments()
    {
        return $this->_comments;
    }
    public function getResearchProject()
    {
        return $this->_researchProject;
    }
    public function setAuthors($authors)
    {
        $this->_authors = $authors;
    }
    public function setExtAuthors($authors)
    {
        $this->_externalAuthor = $authors;
    }
    public function setExtDirectors($directors)
    {
        $this->_exthernalThesisDirector = $directors;
    }
    public function setResearchGroup($researchGroups)
    {
        $this->_researchGroup = $researchGroups;
    }
    public function setResearchAreas($researchAreas)
    {
        $this->_researchArea = $researchAreas;
    }
    public function setResearchPrograms($researchPrograms)
    {
        $this->_researchProgram = $researchPrograms;
    }   
    public function setResearchProjects($researchProjects)
    {
        $this->_researchProject = $researchProjects;
    }   
    public function setCollaborationExperiments($experiments)
    {
        $this->_collaborationExperiment = $experiments;
    }     
    public function setJournalsEditorials($editorials)
    {
        $this->_journalEditorial = $editorials;
    } 
    public function setQualifications($qualifications)
    {
        $this->_qualification = $qualifications;
    } 
    public function setThesisDirector($thesisDirector)
    {
        $this->_thesisDirector = $thesisDirector;
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
     * Get the value of _internationalCoAuthorship
     */ 
    public function getInternationalCoAuthorship()
    {
        return $this->_internationalCoAuthorship;
    }

    /**
     * Set the value of _internationalCoAuthorship
     *
     * @return  self
     */ 
    public function setInternationalCoAuthorship($_internationalCoAuthorship)
    {
        $this->_internationalCoAuthorship = $_internationalCoAuthorship;

        return $this;
    }
     /**
     * Get the value of _externalSources
     */ 
    public function getExternalSources()
    {
        return $this->_externalSources;
    }

    /**
     * Set the value of _externalSources
     *
     * @return  self
     */ 
    public function setExternalSources($_externalSources)
    {
        $this->_externalSources = $_externalSources;

        return $this;
    }

    /**
     * Get the value of _orcid
     */ 
    public function get_orcid()
    {
        return $this->_orcid;
    }
}

