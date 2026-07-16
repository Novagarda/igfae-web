<?php
function getSelectProjectTypes()
{

    $sql = "SELECT 
        p.ProjectTypeID as 'projectTypeId' , p.Name as 'projectTypeName'
    FROM 
    ProjectType p
    ORDER BY 
        p.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetProjectType(
            $row->projectTypeId,
            $row->projectTypeName
        );        
        
    }
   
    return $result;
}
