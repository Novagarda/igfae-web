<?php
function getSelectBuildings()
{    
    $params = [];

    $sql = "SELECT 
        b.BuildingID as 'id', b.Building as 'name' 
    FROM 
    Building b   
    ORDER BY 
        b.Building
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetBuilding(
            $row->id,
            $row->name
        );
    }

    return $result;
}
