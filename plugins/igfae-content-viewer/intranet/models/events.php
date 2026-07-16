<?php

class IntranetEvents
{
	// FORMULARIO DE BUSQUEDA
	public $_title                       = NULL;
	public $_titleES                       = NULL;
	public $_titleEN                        = NULL;
	public $_titleGL                        = NULL;
	public $_igfaeorganizated            = NULL;
	public $_eventType		             = NULL;
	public $_eventSubType                = NULL;
	public $_researchArea                = NULL;
	public $_researchProgram             = NULL;
	public $_researchGroup               = NULL;
	public $_workline                    = NULL;
	public $_researchProject             = NULL;
	public $_startDate                   = NULL;
	public $_endDate                     = NULL;
	public $_speakerIgfae                = NULL;
	public $_membership                  = NULL;
	public $_speakerNoIgfae         	 = NULL;
	public $_email                       = NULL;
	public $_eventTalkLink               = NULL;
	public $_keywords                    = NULL;
	public $_validated                   = NULL;
	public $_published                   = NULL;
	public $_organizatedUser             = NULL;
	public $_institution                 = NULL;
	public $_igfaeMemberPresence         = NULL;
	public $_comments                    = NULL;
	public $_description                 = NULL;
	public $_venue                       = NULL;
	public $_locationLink                = NULL;
	public $_language                    = NULL;
	public $_streaming                   = NULL;
	public $_webSite                     = NULL;
	public $_participants                = NULL;
	public $_signatureLink               = NULL;
	public $_collaborationExperiment     = NULL;
	public $_assistants               = NULL;
	public $_eventID                  = NULL;
	public $_eventDates                  = NULL;
	public $_publishedBy                 = NULL;
	public $_publishedOn                 = NULL;
	public $_validatedBy                 = NULL;
	public $_validatedOn                 = NULL;
	public $_abstract                    = NULL;
	public $_assistantsIgfae               = NULL;
	public $_assistantsNoIgfae               = NULL;
	public $_esCreador					= NULL;
	


	public function __construct()
	{
	}

	/**
	 * Get the value of _eventType
	 */
	public function getEventType()
	{
		return $this->_eventType;
	}

	/**
	 * Set the value of _eventType
	 *
	 * @return  self
	 */
	public function setEventType($_eventType)
	{
		$this->_eventType = $_eventType;

		return $this;
	}



	/**
	 * Get the value of _eventSubType
	 */
	public function getEventSubType()
	{
		return $this->_eventSubType;
	}

	/**
	 * Set the value of _eventSubType
	 *
	 * @return  self
	 */
	public function setEventSubType($_eventSubType)
	{
		$this->_eventSubType = $_eventSubType;

		return $this;
	}

	/**
	 * Get the value of _title
	 */
	public function getTitle()
	{
		return $this->_title;
	}

	/**
	 * Set the value of _title
	 *
	 * @return  self
	 */
	public function setTitle($_title)
	{
		$this->_title = $_title;

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
	 * Set the value of _ResearchProgram
	 *
	 * @return  self
	 */
	public function setResearchProgram($_researchProgram)
	{
		$this->_researchProgram = $_researchProgram;

		return $this;
	}

	/**
	 * Get the value of _eventType
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
	 * Get the value of _workline
	 */
	public function getWorkline()
	{
		return $this->_workline;
	}

	/**
	 * Set the value of _eventType
	 *
	 * @return  self
	 */
	public function setWorkline($_workline)
	{
		$this->_workline = $_workline;

		return $this;
	}

	/**
	 * Get the value of _researchProject
	 */
	public function getResearchProject()
	{
		return $this->_researchProject;
	}

	/**
	 * Set the value of _researchProject
	 *
	 * @return  self
	 */
	public function setResearchProject($_researchProject)
	{
		$this->_researchProject = $_researchProject;

		return $this;
	}

	/**
	 * Get the value of _igfaeorganizated
	 */
	public function getIGFAEOrganizated()
	{
		return $this->_igfaeorganizated;
	}

	/**
	 * Set the value of _igfaeorganizated
	 *
	 * @return  self
	 */
	public function setIGFAEOrganizated($_igfaeorganizated)
	{
		$this->_igfaeorganizated = $_igfaeorganizated;

		return $this;
	}

	/**
	 * Get the value of _SpeakerIgfae
	 */
	public function getSpeakerIgfae()
	{
		return $this->_speakerIgfae;
	}

	/**
	 * Set the value of _Speaker Igfae
	 *
	 * @return  self
	 */
	public function setSpeakerIgfae($_SpeakerIgfae)
	{
		$this->_speakerIgfae = $_SpeakerIgfae;

		return $this;
	}

	/**
	 * Get the value of _speakerNoIgfae
	 */
	public function getSpeakerNoIgfae()
	{
		return $this->_speakerNoIgfae;
	}

	/**
	 * Set the value of _speakerNoIgfae
	 *
	 * @return  self
	 */
	public function setSpeakerNoIgfae($_speakerNoIgfae)
	{
		$this->_speakerNoIgfae = $_speakerNoIgfae;

		return $this;
	}

	/**
	 * Get the value of _eventType
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
	 * Get the value of _keywords
	 */
	public function getKeywords()
	{
		return $this->_keywords;
	}

	/**
	 * Set the value of _keywords
	 *
	 * @return  self
	 */
	public function setKeywords($_keywords)
	{
		$this->_keywords = $_keywords;

		return $this;
	}

	/**
	 * Get the value of _membership
	 */
	public function getMembership()
	{
		return $this->_membership;
	}

	/**
	 * Set the value of _membership
	 *
	 * @return  self
	 */
	public function setMembership($_membership)
	{
		$this->_membership = $_membership;

		return $this;
	}

	/**
	 * Get the value of _Email
	 */
	public function getEmail()
	{
		return $this->_email;
	}

	/**
	 * Set the value of _Email
	 *
	 * @return  self
	 */
	public function setEmail($_email)
	{
		$this->_email = $_email;

		return $this;
	}

	/**
	 * Get the value of _eventTalkLink
	 */
	public function getEventTalkLink()
	{
		return $this->_eventTalkLink;
	}

	/**
	 * Set the value of _eventTalkLink
	 *
	 * @return  self
	 */
	public function setEventTalkLink($_eventTalkLink)
	{
		$this->_eventTalkLink = $_eventTalkLink;

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
	 * Get the value of _eventPersonnel
	 */
	public function getOrganizatedUser()
	{
		return $this->_organizatedUser;
	}

	/**
	 * Set the value of _organizatedUser
	 *
	 * @return  self
	 */
	public function setOrganizatedUser($_organizatedUser)
	{
		$this->_organizatedUser = $_organizatedUser;

		return $this;
	}
	/**
	 * Get the value of _institution
	 */
	public function getInstitution()
	{
		return $this->_institution;
	}

	/**
	 * Set the value of _institution
	 *
	 * @return  self
	 */
	public function setInstitution($_institution)
	{
		$this->_institution = $_institution;

		return $this;
	}
	/**
	 * Get the value of _igfaeMemberPresence
	 */
	public function getIgfaeMemberPresence()
	{
		return $this->_igfaeMemberPresence;
	}

	/**
	 * Set the value of _institution
	 *
	 * @return  self
	 */
	public function setIgfaeMemberPresence($_igfaeMemberPresence)
	{
		$this->_igfaeMemberPresence = $_igfaeMemberPresence;

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
	 * Get the value of _description
	 */
	public function getDescription()
	{
		return $this->_description;
	}

	/**
	 * Set the value of _description
	 *
	 * @return  self
	 */
	public function setDescription($_description)
	{
		$this->_description = $_description;

		return $this;
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
	public function setTitleES($_title)
	{
		$this->_titleES = $_title;

		return $this;
	}
	public function setTitleEN($_title)
	{
		$this->_titleEN = $_title;

		return $this;
	}
	public function setTitleGL($_title)
	{
		$this->_titleGL = $_title;

		return $this;
	}
	/**
	 * Get the value of _venue
	 */
	public function getVenue()
	{
		return $this->_venue;
	}

	/**
	 * Set the value of _venue
	 *
	 * @return  self
	 */
	public function setVenue($_venue)
	{
		$this->_venue = $_venue;

		return $this;
	}
	/**
	 * Get the value of _locationLink
	 */
	public function getLocationLink()
	{
		return $this->_locationLink;
	}

	/**
	 * Set the value of _locationLink
	 *
	 * @return  self
	 */
	public function setLocationLink($_locationLink)
	{
		$this->_locationLink = $_locationLink;

		return $this;
	}
	/**
	 * Get the value of _language
	 */
	public function getLanguage()
	{
		return $this->_language;
	}

	/**
	 * Set the value of _language
	 *
	 * @return  self
	 */
	public function setLanguage($_language)
	{
		$this->_language = $_language;

		return $this;
	}
	/**
	 * Get the value of _streaming
	 */
	public function getStreaming()
	{
		return $this->_streaming;
	}

	/**
	 * Set the value of _streaming
	 *
	 * @return  self
	 */
	public function setStreaming($_streaming)
	{
		$this->_streaming = $_streaming;

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
	 * Set the value of _streaming
	 *
	 * @return  self
	 */
	public function setWebSite($_webSite)
	{
		$this->_webSite = $_webSite;

		return $this;
	}
	/**
	 * Get the value of _participants
	 */
	public function getParticipants()
	{
		return $this->_participants;
	}

	/**
	 * Set the value of _participants
	 *
	 * @return  self
	 */
	public function setParticipants($_participants)
	{
		$this->_participants = $_participants;

		return $this;
	}
	/**
	 * Get the value of _signatureLink
	 */
	public function getSignatureLink()
	{
		return $this->_signatureLink;
	}

	/**
	 * Set the value of _signatureLink
	 *
	 * @return  self
	 */
	public function setSignatureLink($_signatureLink)
	{
		$this->_signatureLink = $_signatureLink;

		return $this;
	}
	/**
	 * Get the value of _assistants
	 */
	public function getAssistants()
	{
		return $this->_assistants;
	}

	/**
	 * Set the value of _assistants
	 *
	 * @return  self
	 */
	public function setAssistants($_assistants)
	{
		$this->_assistants = $_assistants;

		return $this;
	}
	/**
	 * Get the value of _eventID
	 */
	public function getEventID()
	{
		return $this->_eventID;
	}

	/**
	 * Set the value of _eventID
	 *
	 * @return  self
	 */
	public function setEventID($_eventID)
	{
		$this->_eventID = $_eventID;

		return $this;
	}
	/**
	 * Get the value of _eventDates
	 */
	public function getEventDates()
	{
		return $this->_eventDates;
	}

	/**
	 * Set the value of _eventDates
	 *
	 * @return  self
	 */
	public function setEventDates($_eventDates)
	{
		$this->_eventDates = $_eventDates;

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
	 * Get the value of _abstract
	 */
	public function getAbstract()
	{
		return $this->_abstract;
	}

	/**
	 * Set the value of _abstract
	 *
	 * @return  self
	 */
	public function setAbstract($_abstract)
	{
		$this->_abstract = $_abstract;

		return $this;
	}


	/**
	 * Get the value of _assistantsIgfae
	 */
	public function getAssistantsIgfae()
	{
		return $this->_assistantsIgfae;
	}

	/**
	 * Set the value of _assistantsIgfae
	 *
	 * @return  self
	 */
	public function setAssistantsIgfae($_assistantsIgfae)
	{
		$this->_assistantsIgfae = $_assistantsIgfae;

		return $this;
	}

	/**
	 * Get the value of _assistantsNoIgfae
	 */
	public function getAssistantsNoIgfae()
	{
		return $this->_assistantsNoIgfae;
	}

	/**
	 * Set the value of _assistantsNoIgfae
	 *
	 * @return  self
	 */
	public function setAssistantsNoIgfae($_assistantsNoIgfae)
	{
		$this->_assistantsNoIgfae = $_assistantsNoIgfae;

		return $this;
	}

	/**
	 * Get the value of _esCreador
	 */ 
	public function getEsCreador()
	{
		return $this->_esCreador;
	}

	/**
	 * Set the value of _esCreador
	 *
	 * @return  self
	 */ 
	public function setEsCreador($_esCreador)
	{
		$this->_esCreador = $_esCreador;

		return $this;
	}
}
