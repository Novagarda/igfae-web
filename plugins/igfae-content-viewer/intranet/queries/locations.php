<?php
function getLocations($order){
    
    $params = [];
    $order = getOrderLocations($order);

    $sql = "SELECT 
	              ml.MapLocationID, 
	              ml.Name, 
	              p.PersonnelID,
	              concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) as PersonnelName
             FROM MapLocation ml
       INNER JOIN Personnel p
               ON p.MapLocationID = ml.MapLocationID 
         ORDER BY $order ;";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = array(
          "locationId"=>  $row->MapLocationID,
          "name"=>  $row->Name,
          "personnelId"=>  $row->PersonnelID,
          "personnelName"=>  $row->PersonnelName
        );
    }

    return $result;
}


function getOrderLocations($order)
{
    if (!isset($order)) {
        return ' ml.Name asc';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: 
                $sort .= 'ml.Name ';
                break;
            case 1:  
                $sort .= 'PersonnelName ';
                break;
            default:
                $sort .= 'ml.Name ';
                break;                                                                                   
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}


