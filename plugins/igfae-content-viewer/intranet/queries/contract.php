<?php
function getSelectContracts()
{
    $where = "";
    $params = [];

    $sql = "SELECT 
        c.ContractID as 'id', c.Name as 'name' 
    FROM 
    Contract c   
    ORDER BY 
        c.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetContract(
            $row->id,
            $row->name
        );
    }

    return $result;
}
