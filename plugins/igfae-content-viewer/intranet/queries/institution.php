<?php
function getSelectInstitutions()
{

    $sql = "SELECT 
        i.InstitutionID as 'institutionID' , 
        i.InstitutionName as 'name'
    FROM 
    Institution i
    ORDER BY 
        i.InstitutionName
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetInstitution(
            $row->institutionID,
            $row->name
        );
        
    }

    return $result;
}