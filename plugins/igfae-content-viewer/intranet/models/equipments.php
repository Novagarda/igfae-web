<?php
	class IntranetEquipment
	{
		// FORMULARIO DE BUSQUEDA
		public $_equipmentID = NULL;
		public $_equipmentName = NULL;
		public $_equipmentPhoto = NULL;
		public $_equipmentPhotoID = NULL;
		public $_equipmentSN = NULL;
		public $_equipmentInventoryNumber = NULL;
		public $_equipmentInvoiceNumber = NULL;
		public $_equipmentFamily  = NULL;
		public $_equipmentFamilyID = NULL;
		public $_equipmentLaboratory = NULL;
		public $_equipmentLaboratoryID = NULL;
		public $_equipmentTechnicalContact = NULL;
		public $_equipmentResponsableFamily = NULL;
		public $_equipmentResponsableFamilyID = NULL;
		public $_equipmentAvailable = NULL;
		public $_equipmentLocation = NULL;
		public $_equipmentFundingCode = NULL;
		public $_equipmentOtherFunding = NULL;
		public $_equipmentAcceptanceDate = NULL;
		public $_equipmentAcceptance = NULL;
		public $_equipmentAcceptanceID = NULL;
		public $_technicalSpecifications = NULL;
		public $_equipmentComment = NULL;
		public $_deletePhoto = NULL;
		public $_deleteAcceptance = NULL;
		public $_amortizations = NULL;
		public $_maintenanceHistory = NULL;
		public $_documentations = NULL;
		public $_listDeleteDocumentations = NULL;
		public $_listChangeNameDocumentations = NULL;

		
		public function __construct()
		{
		}

		/**
		* Get the value of _equipmentID
		*/
		public function getEquipmentID()
		{
			return $this->_equipmentID;
		}

		/**
		* Set the value of _equipmentID
		*
		* @return  self
		*/
		public function setEquipmentID($_equipmentID)
		{
			$this->_equipmentID = $_equipmentID;

			return $this;
		}

		/**
		* Get the value of _equipmentName
		*/
		public function getEquipmentName()
		{
			return $this->_equipmentName;
		}

		/**
		* Set the value of _equipmentName
		*
		* @return  self
		*/
		public function setEquipmentName($_equipmentName)
		{
			$this->_equipmentName = $_equipmentName;

			return $this;
		}

		/**
		* Get the value of _equipmentPhoto
		*/
		public function getEquipmentPhoto()
		{
			return $this->_equipmentPhoto;
		}

		/**
		* Set the value of _equipmentPhoto
		*
		* @return  self
		*/
		public function setEquipmentPhoto($_equipmentPhoto)
		{
			$this->_equipmentPhoto = $_equipmentPhoto;

			return $this;
		}

		/**
		* Get the value of _equipmentPhotoID
		*/
		public function getEquipmentPhotoID()
		{
			return $this->_equipmentPhotoID;
		}

		/**
		* Set the value of _equipmentPhotoID
		*
		* @return  self
		*/
		public function setEquipmentPhotoID($_equipmentPhotoID)
		{
			$this->_equipmentPhotoID = $_equipmentPhotoID;

			return $this;
		}


		/**
		* Get the value of _equipmentSN
		*/
		public function getEquipmentSN()
		{
			return $this->_equipmentSN;
		}

		/**
		* Set the value of _equipmentSN
		*
		* @return  self
		*/
		public function setEquipmentSN($_equipmentSN)
		{
			$this->_equipmentSN = $_equipmentSN;

			return $this;
		}

		/**
		* Get the value of _equipmentInventoryNumber
		*/
		public function getEquipmentInventoryNumber()
		{
			return $this->_equipmentInventoryNumber;
		}

		/**
		* Set the value of _equipmentInventoryNumber
		*
		* @return  self
		*/
		public function setEquipmentInventoryNumber($_equipmentInventoryNumber)
		{
			$this->_equipmentInventoryNumber = $_equipmentInventoryNumber;

			return $this;
		}

		/**
		* Get the value of _equipmentInvoiceNumber
		*/
		public function getEquipmentInvoiceNumber()
		{
			return $this->_equipmentInvoiceNumber;
		}

		/**
		* Set the value of _equipmentInvoiceNumber
		*
		* @return  self
		*/
		public function setEquipmentInvoiceNumber($_equipmentInvoiceNumber)
		{
			$this->_equipmentInvoiceNumber = $_equipmentInvoiceNumber;

			return $this;
		}

		/**
		* Get the value of _equipmentFamily
		*/
		public function getEquipmentFamily()
		{
			return $this->_equipmentFamily;
		}

		/**
		* Set the value of _equipmentFamily
		*
		* @return  self
		*/
		public function setEquipmentFamily($_equipmentFamily)
		{
			$this->_equipmentFamily = $_equipmentFamily;

			return $this;
		}


		/**
		* Get the value of _equipmentFamilyID
		*/
		public function getFamilyId()
		{
			return $this->_equipmentFamilyID;
		}

		/**
		* Set the value of _equipmentFamilyID
		*
		* @return  self
		*/
		public function setFamilyId($_equipmentFamilyID)
		{
			$this->_equipmentFamilyID = $_equipmentFamilyID;

			return $this;
		}

		/**
		* Get the value of _equipmentLaboratory
		*/
		public function getEquipmentLaboratory()
		{
			return $this->_equipmentLaboratory;
		}

		/**
		* Set the value of _equipmentLaboratory
		*
		* @return  self
		*/
		public function setEquipmentLaboratory($_equipmentLaboratory)
		{
			$this->_equipmentLaboratory = $_equipmentLaboratory;

			return $this;
		}


		/**
		* Get the value of _equipmentLaboratoryID
		*/
		public function getLaboratoryId()
		{
			return $this->_equipmentLaboratoryID;
		}

		/**
		* Set the value of _equipmentLaboratoryId
		*
		* @return  self
		*/
		public function setLaboratoryId($_equipmentLaboratoryID)
		{
			$this->_equipmentLaboratoryID = $_equipmentLaboratoryID;

			return $this;
		}


		/**
		* Get the value of _equipmentTechnicalContact
		*/
		public function getEquipmentTechnicalContact()
		{
			return $this->_equipmentTechnicalContact;
		}

		/**
		* Set the value of _equipmentTechnicalContact
		*
		* @return  self
		*/
		public function setEquipmentTechnicalContact($_equipmentTechnicalContact)
		{
			$this->_equipmentTechnicalContact = $_equipmentTechnicalContact;

			return $this;
		}


		/**
		* Get the value of _equipmentResponsableFamily
		*/
		public function getEquipmentResponsableFamily()
		{
			return $this->_equipmentResponsableFamily;
		}

		/**
		* Set the value of _equipmentResponsableFamily
		*
		* @return  self
		*/
		public function setEquipmentResponsableFamily($_equipmentResponsableFamily)
		{
			$this->_equipmentResponsableFamily = $_equipmentResponsableFamily;

			return $this;
		}


		/**
		* Get the value of _equipmentResponsableFamilyID
		*/
		public function getResponsableFamilyId()
		{
			return $this->_equipmentResponsableFamilyID;
		}

		/**
		* Set the value of _equipmentResponsableFamilyID
		*
		* @return  self
		*/
		public function setResponsableFamilyId($_equipmentResponsableFamilyID)
		{
			$this->_equipmentResponsableFamilyID = $_equipmentResponsableFamilyID;

			return $this;
		}

		/**
		* Get the value of _equipmentAvailable
		*/
		public function getEquipmentAvailable()
		{
			return $this->_equipmentAvailable;
		}

		/**
		* Set the value of _equipmentAvailable
		*
		* @return  self
		*/
		public function setEquipmentAvailable($_equipmentAvailable)
		{
			$this->_equipmentAvailable = $_equipmentAvailable;

			return $this;
		}

		/**
		* Get the value of _equipmentLocation
		*/
		public function getEquipmentLocation()
		{
			return $this->_equipmentLocation;
		}

		/**
		* Set the value of _equipmentLocation
		*
		* @return  self
		*/
		public function setEquipmentLocation($_equipmentLocation)
		{
			$this->_equipmentLocation = $_equipmentLocation;

			return $this;
		}

		/**
		* Get the value of _equipmentFundingCode
		*/
		public function getEquipmentFundingCode()
		{
			return $this->_equipmentFundingCode;
		}

		/**
		* Set the value of _equipmentFundingCode
		*
		* @return  self
		*/
		public function setEquipmentFundingCode($_equipmentFundingCode)
		{
			$this->_equipmentFundingCode = $_equipmentFundingCode;

			return $this;
		}

		
		/**
		* Get the value of _equipmentOtherFunding
		*/
		public function getEquipmentOtherFunding()
		{
			return $this->_equipmentOtherFunding;
		}

		/**
		* Set the value of _equipmentOtherFunding
		*
		* @return  self
		*/
		public function setEquipmentOtherFunding($_equipmentOtherFunding)
		{
			$this->_equipmentOtherFunding = $_equipmentOtherFunding;

			return $this;
		}

		/**
		* Get the value of _equipmentAcceptanceDate
		*/
		public function getEquipmentAcceptanceDate()
		{
			return $this->_equipmentAcceptanceDate;
		}

		/**
		* Set the value of _equipmentAcceptanceDate
		*
		* @return  self
		*/
		public function setEquipmentAcceptanceDate($_equipmentAcceptanceDate)
		{
			$this->_equipmentAcceptanceDate = $_equipmentAcceptanceDate;

			return $this;
		}

		/**
		* Get the value of _equipmentAcceptance
		*/
		public function getEquipmentAcceptance()
		{
			return $this->_equipmentAcceptance;
		}

		/**
		* Set the value of _equipmentAcceptance
		*
		* @return  self
		*/
		public function setEquipmentAcceptance($_equipmentAcceptance)
		{
			$this->_equipmentAcceptance = $_equipmentAcceptance;

			return $this;
		}

		/**
		* Get the value of _equipmentAcceptanceID
		*/
		public function getEquipmentAcceptanceID()
		{
			return $this->_equipmentAcceptanceID;
		}

		/**
		* Set the value of _equipmentAcceptanceID
		*
		* @return  self
		*/
		public function setEquipmentAcceptanceID($_equipmentAcceptanceID)
		{
			$this->_equipmentAcceptanceID = $_equipmentAcceptanceID;

			return $this;
		}


		/**
		* Get the value of _technicalSpecifications
		*/
		public function getTechnicalSpecifications()
		{
			return $this->_technicalSpecifications;
		}

		/**
		* Set the value of _technicalSpecifications
		*
		* @return  self
		*/
		public function setTechnicalSpecifications($_technicalSpecifications)
		{
			$this->_technicalSpecifications = $_technicalSpecifications;

			return $this;
		}	

		/**
		* Get the value of _equipmentComment
		*/
		public function getComments()
		{
			return $this->_equipmentComment;
		}

		/**
		* Set the value of _equipmentComment
		*
		* @return  self
		*/
		public function setComments($_equipmentComment)
		{
			$this->_equipmentComment = $_equipmentComment;

			return $this;
		}	


		/**
		* Get the value of _deletePhoto
		*/ 
		public function getDeletePhoto()
		{
			return $this->_deletePhoto;
		}

		/**
		* Set the value of _deletePhoto
		*
		* @return  self
		*/ 
		public function setDeletePhoto($_deletePhoto)
		{
			$this->_deletePhoto = $_deletePhoto;

			return $this;
		}

		/**
		* Get the value of _deleteAcceptance
		*/ 
		public function getDeleteAcceptance()
		{
			return $this->_deleteAcceptance;
		}

		/**
		* Set the value of _deleteAcceptance
		*
		* @return  self
		*/ 
		public function setDeleteAcceptance($_deleteAcceptance)
		{
			$this->_deleteAcceptance = $_deleteAcceptance;

			return $this;
		}

				/**
		* Get the value of _amortizations
		*/ 
		public function getAmortizations()
		{
			return $this->_amortizations;
		}

		/**
		* Set the value of _amortizations
		*
		* @return  self
		*/ 
		public function setAmortizations($_amortizations)
		{
			$this->_amortizations = $_amortizations;

			return $this;
		}


		/**
		* Get the value of _maintenanceHistory
		*/ 
		public function getMaintenanceHistory()
		{
			return $this->_maintenanceHistory;
		}

		/**
		* Set the value of _maintenanceHistory
		*
		* @return  self
		*/ 
		public function setMaintenanceHistory($_maintenanceHistory)
		{
			$this->_maintenanceHistory = $_maintenanceHistory;

			return $this;
		}

		/**
		* Get the value of _documentations
		*/ 
		public function getDocumentations()
		{
			return $this->_documentations;
		}

		/**
		* Set the value of _documentations
		*
		* @return  self
		*/ 
		public function setDocumentations($_documentations)
		{
			$this->_documentations = $_documentations;

			return $this;
		}

		/**
		* Get the value of _listDeleteDocumentations
		*/ 
		public function getListDeleteDocumentations()
		{
			return $this->_listDeleteDocumentations;
		}

		/**
		* Set the value of _listDeleteDocumentations
		*
		* @return  self
		*/ 
		public function setListDeleteDocumentations($_listDeleteDocumentations)
		{
			$this->_listDeleteDocumentations = $_listDeleteDocumentations;

			return $this;
		}

		/**
		* Get the value of _listChangeNameDocumentations
		*/ 
		public function getListChangeNameDocumentations()
		{
			return $this->_listChangeNameDocumentations;
		}

		/**
		* Set the value of _listChangeNameDocumentations
		*
		* @return  self
		*/ 
		public function setListChangeNameDocumentations($_listChangeNameDocumentations)
		{
			$this->_listChangeNameDocumentations = $_listChangeNameDocumentations;

			return $this;
		}

		
	}

