<?php
function getSelectTeamReferences()
{
    $where = "";
    $params = [];    
    
    $sql = "SELECT DISTINCT
        tr.TeamReferenceID as 'id', tr.Name as 'name' 
    FROM 
    TeamReference tr   
    ORDER BY 
        tr.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);
    
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetTeamReference(
            $row->id,
            $row->name
        );       
    }

    return $result;
}
