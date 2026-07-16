<?php
function getSelectKeywords($texto, $page)
{
    $where = "";
    $params = [];
    if (isset($texto)) {
        $where .= " AND (k.Name LIKE %s)";
        $params[] = '%' . $texto . '%';
        // Añadimos dos veces el parámetro para la clausula where del select count
        $params[] = '%' . $texto . '%';
    }



    $sql = "SELECT x.totalCounts as 'totalCounts',
			k.Name as 'name' 
		FROM 
        Keyword k,
        (select count(*) as totalCounts FROM Keyword k WHERE 1=1 {$where}) as x 
        WHERE 1=1
        {$where} 
		ORDER BY 
        k.Name
			ASC";
    // Obtenemos los resultados en grupos
    $amount = IGFAE_INTRANET_NUMBER_ELEMENTS_SELECT_ASYNC;
    if (is_null($page)) {
        $page = 1;
    }
    $offset = (($page - 1) * $amount);
    $sql .= " LIMIT {$offset}, {$amount}";
    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = array();
    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->name, "full_name" => $row->name);
    }
    if (isset($row)) {
        $result['items']  = $resultArray;
        $result['total_count'] = $row->totalCounts;
    } else {
        $result['items']  = [];
        $result['total_count'] = 0;
    }
    return $result;
}

function getKeyword($name)
{
    $where = "";
    $params = [];
    $where .= " AND (k.Name like %s )";
    $params[] = '%' . $name . '%';
    
    $sql = "SELECT  k.Name as 'name'
		FROM 
        Keyword k       
        WHERE 1=1
        {$where};";
    
    error_log($sql);
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("full_name" => $row->name);
    }
    
    return $resultArray[0];
}