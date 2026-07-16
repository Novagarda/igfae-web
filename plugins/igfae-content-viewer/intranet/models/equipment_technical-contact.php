<?php
class IntranetEquipmentTechnicalContact
{

	protected $_equipament_technicalContact_id = NULL;
	protected $_equipment_technicalContact_name  = NULL;

	public function __construct($id = NULL, $name = NULL)
	{
		$this->_equipament_technicalContact_id = $id;
		$this->_equipment_technicalContact_name = $name;
	}

	public function getEquipmentTechnicalContactId()
	{
		return $this->_equipament_technicalContact_id;
	}
	public function getEquipmentTechnicalContactName()
	{
		return $this->_equipment_technicalContact_name;
	}
}
