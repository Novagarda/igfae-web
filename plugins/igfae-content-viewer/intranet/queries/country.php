<?php
function getSelectCountries()
{    
    $params = [];

    $sql = "SELECT 
        c.Code as 'code', c.Name as 'name' 
    FROM 
    Country c   
    ORDER BY 
        c.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetCountry(
            $row->code,
            $row->name
        );
    }

    return $result;
}
