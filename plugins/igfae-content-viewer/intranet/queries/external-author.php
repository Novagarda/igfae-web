<?php
function getSelectExternalAuthors($texto, $page)
{
    $where = "";   
    $params = []; 
    if (isset($texto)) {
        $where .= " AND (ea.Name LIKE %s OR ea.Surname LIKE %s OR concat(ea.Name, ' ', ea.Surname ) LIKE %s) ";
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        // Añadimos dos veces el parámetro para la clausula where del select count
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
    }        
    
    $sql = "SELECT x.totalCounts as 'totalCounts', 
            ea.ExternalAuthorID as 'externalAuthorID',
			concat(ea.Name, ' ', ea.Surname )  as 'name',
            ea.Surname as 'surname'
		FROM 
        ExternalAuthor ea,
        (select count(*) as totalCounts FROM ExternalAuthor ea WHERE 1=1 {$where} ) as x        
        WHERE 1=1
        {$where} 
		ORDER BY 
        concat(ea.Name, ' ', ea.Surname )
            ASC";
    // Obtenemos los resultados en grupos
    $amount = IGFAE_INTRANET_NUMBER_ELEMENTS_SELECT_ASYNC;
    if (is_null($page)) {
        $page = 1;
    }
    $offset = (($page-1) * $amount);
    $sql .= " LIMIT {$offset}, {$amount}";
    
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->externalAuthorID, "full_name" => $row->name);
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

function getExternalAuthor($id)
{
    $where = "";
    $params = [];
    $where .= " AND (ea.ExternalAuthorID = %d)";
    $params[] = $id;
    
    $sql = "SELECT ea.ExternalAuthorID as 'externalAuthorID', concat(ea.Name, ' ', ea.Surname )  as 'name'
    FROM 
    ExternalAuthor ea      
    WHERE 1=1
    {$where};";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->externalAuthorID, "full_name" => $row->name);
    }

    return $resultArray[0];
    
}