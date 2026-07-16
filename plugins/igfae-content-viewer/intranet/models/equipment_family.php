<?php
class IntranetEquipmentFamily
{

	protected $_equipament_family_id = NULL;
	protected $_equipment_family_name  = NULL;

	public function __construct($id = NULL, $name = NULL)
	{
		$this->_equipament_family_id = $id;
		$this->_equipment_family_name = $name;
	}

	public function getEquipmentFamilyId()
	{
		return $this->_equipament_family_id;
	}
	
	public function getEquipmentFamilyName()
	{
		return $this->_equipment_family_name;
	}
}
