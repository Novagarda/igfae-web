<?php
	class IntranetEquipmentBookingHistory
	{
		public $_equipmentReservationID = NULL;
		public $_equipmentID = NULL;
		public $_equipment_name = NULL;
		public $_personnel  = NULL;
		public $_temporal_location  = NULL;
		public $_start_date  = NULL;
		public $_end_date  = NULL;
		public $_creation_date  = NULL;
		public $_created_by  = NULL;

		public function __construct()
		{
			
		}


		public function getEquipmentReservationID()
		{
			return $this->_equipmentReservationID;
		}
		public function setEquipmentReservationID($_equipmentReservationID)
		{
			$this->_equipmentReservationID = $_equipmentReservationID;

			return $this;
		}

		public function getEquipmentID()
		{
			return $this->_equipmentID;
		}
		public function setEquipmentID($_equipmentID)
		{
			$this->_equipmentID = $_equipmentID;

			return $this;
		}
		

		public function getEquipmentName()
		{
			return $this->_equipment_name;
		}
		public function setEquipmentName($_equipment_name)
		{
			$this->_equipment_name = $_equipment_name;

			return $this;
		}
		
		public function getPersonnel()
		{
			return $this->_personnel;
		}
		public function setPersonnel($_personnel)
		{
			$this->_personnel = $_personnel;

			return $this;
		}

		public function getTemporalLocation()
		{
			return $this->_temporal_location;
		}
		public function setTemporalLocation($_temporal_location)
		{
			$this->_temporal_location = $_temporal_location;

			return $this;
		}

		public function getStartDate()
		{
			return $this->_start_date;
		}
		public function setStartDate($_start_date)
		{
			$this->_start_date = $_start_date;

			return $this;
		}

		public function getEndDate()
		{
			return $this->_end_date;
		}
		public function setEndDate($_end_date)
		{
			$this->_end_date = $_end_date;

			return $this;
		}

		public function getCreationDate()
		{
			return $this->_creation_date;
		}
		public function setCreationDate($_creation_date)
		{
			$this->_creation_date = $_creation_date;

			return $this;
		}

		public function getCreatedBy()
		{
			return $this->_created_by;
		}
		public function setCreatedBy($_created_by)
		{
			$this->_created_by = $_created_by;

			return $this;
		}
	}

