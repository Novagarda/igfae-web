<?php
function getSelectLocations($building){
    
    $where = "";
    $params = [];
    
    if (isset($building)) {
        $where .= " AND l.BuildingID = %d ";
        $params[] = $building;
    }
    
    $sql = "SELECT 
        l.LocationID as 'id', l.Room as 'room', l.BuildingID as 'buildingID' 
    FROM 
    Location l   
    WHERE 1=1
    {$where}  
    ORDER BY 
        l.Room
    ASC";
    
    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetLocation(
            $row->id,
            $row->room,
            $row->buildingID
        );
    }

    return $result;
}
