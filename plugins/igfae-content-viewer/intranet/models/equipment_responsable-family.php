<?php
class IntranetEquipmentResponsableFamily
{

	protected $_equipament_responsable_family_id = NULL;
	protected $_equipment_responsable_family_name  = NULL;

	public function __construct($id = NULL, $name = NULL)
	{
		$this->_equipament_responsable_family_id = $id;
		$this->_equipment_responsable_family_name = $name;
	}

	public function getEquipmentResponsableFamilyId()
	{
		return $this->_equipament_responsable_family_id;
	}
	
	public function getEquipmentResponsableFamilyName()
	{
		return $this->_equipment_responsable_family_name;
	}
}
