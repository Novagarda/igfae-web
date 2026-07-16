<?php
function getSelectEquipmentResponsableFamily()
{

    $sql = "SELECT distinct efr.PersonnelID as 'equipmentFamilyResponsableID' , 
            concat(if(p.name is not null, concat(p.name,' '), ' '), 
		 if(p.FirstSurname is not null, concat(p.FirstSurname,' '), ' '), 
		 if(p.SecondSurname is not null, concat(p.SecondSurname,''), ''))  as 'name' 
            FROM EquipmentFamilyResponsable efr 
            left join Personnel p on p.PersonnelID = efr.PersonnelID 
            order by efr.EquipmentFamilyResponsableID ";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetEquipmentResponsableFamily(
            $row->equipmentFamilyResponsableID,
            $row->name
        );
        
    }

    return $result;
}

function getSelectEquipmentResponsableFamilyByFamily($family){

    $sql = "SELECT distinct efr.PersonnelID as 'equipmentFamilyResponsableID' , 
            concat(if(p.name is not null, concat(p.name,' '), ' '), 
		 if(p.FirstSurname is not null, concat(p.FirstSurname,' '), ' '), 
		 if(p.SecondSurname is not null, concat(p.SecondSurname,''), ''))  as 'name' 
            FROM EquipmentFamilyResponsable efr 
            left join Personnel p on p.PersonnelID = efr.PersonnelID 
            where FamilyID in ({$family})
            order by efr.EquipmentFamilyResponsableID ";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = array(
            $row->equipmentFamilyResponsableID,
            $row->name
        );
        
    }    

    return $result;
}
