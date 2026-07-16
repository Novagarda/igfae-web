<?php
function getSelectEquipmentFamily()
{

    $sql = "SELECT distinct
        a.FamilyID as 'equipmentFamilyId' , 
        a.EquipmentFamilyName as 'name'
    FROM 
    EquipmentFamily a
    ORDER BY 
        a.EquipmentFamilyName
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetEquipmentFamily(
            $row->equipmentFamilyId,
            $row->name
        );
        
    }

    return $result;
}
