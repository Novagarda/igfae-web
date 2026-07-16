<?php
class IntranetEquipmentLaboratory
{

	protected $_equipament_laboratory_id = NULL;
	protected $_equipment_laboratory_name  = NULL;

	public function __construct($id = NULL, $name = NULL)
	{
		$this->_equipament_laboratory_id = $id;
		$this->_equipment_laboratory_name = $name;
	}

	public function getEquipmentLaboratoryId()
	{
		return $this->_equipament_laboratory_id;
	}
	public function getEquipmentLaboratoryName()
	{
		return $this->_equipment_laboratory_name;
	}
}
