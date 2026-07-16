<?php
function getSelectEquipmentTechnicalContact()
{

    $sql = "SELECT 
        etc.EquipmentTechnicalContactID as 'equipmentTechnicalContactId' , 
       concat(if(etc.TechnicalName is not null, concat(etc.TechnicalName,' / '), ' - / '), 
		 if(etc.TechnicalEmail is not null, concat(etc.TechnicalEmail,' / '), ' - / '), 
		 if(etc.TechnicalTelephone is not null, concat(etc.TechnicalTelephone,''), ' -')) as name
    FROM 
    EquipmentTechnicalContact etc";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetEquipmentTechnicalContact(
            $row->equipmentTechnicalContactId,
            $row->name
        );
        
    }

    return $result;
}
