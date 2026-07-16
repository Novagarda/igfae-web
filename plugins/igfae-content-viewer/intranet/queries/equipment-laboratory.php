<?php
function getSelectEquipmentLaboratory()
{

    $sql = "SELECT distinct
        a.LaboratoryID as 'equipmentLaboratoryId' , 
        a.EquipmentLaboratoryName as 'name'
    FROM 
    EquipmentLaboratory a
    ORDER BY 
        a.EquipmentLaboratoryName
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetEquipmentLaboratory(
            $row->equipmentLaboratoryId,
            $row->name
        );
        
    }

    return $result;
}
