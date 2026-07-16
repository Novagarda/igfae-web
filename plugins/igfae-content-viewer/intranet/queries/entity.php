<?php
function getSelectEntities()
{    
    $params = [];

    $sql = "SELECT 
                EntityNameID as 'id', 
                Name as 'name' 
            FROM  EntityName e
            ORDER BY  Name ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetEntity(
            $row->id,
            $row->name
        );
    }

    return $result;
}
