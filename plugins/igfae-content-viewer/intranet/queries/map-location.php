<?php
function getSelectMapLocations(){
    
    $params = [];

    $sql = "SELECT 
        ml.MapLocationID as 'id', ml.Name as 'name'
    FROM 
    MapLocation ml   
    ORDER BY 
    ml.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetMapLocation(
            $row->id,
            $row->name
        );
    }

    return $result;
}
