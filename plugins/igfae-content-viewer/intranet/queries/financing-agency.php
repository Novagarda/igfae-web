<?php
function getSelectFinancingAgencies()
{

    $sql = "SELECT 
        f.FinancingAgencyID as 'financingAgencyId' , f.AgencyName as 'agencyName'
    FROM 
    FinancingAgency f
    ORDER BY 
        f.AgencyName
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetFinancingAgency(
            $row->financingAgencyId,
            $row->agencyName
        );        
        
    }
   
    return $result;
}

function getSelectSearchFinancingAgencies($texto, $page)
{

    $where = "";
    $params = [];
    if (isset($texto)) {
        $where .= " AND (f.AgencyName LIKE %s)";
        $params[] = '%' . $texto . '%';
        // Añadimos dos veces el parámetro para la clausula where del select count
        $params[] = '%' . $texto . '%';
    }

    $sql = "SELECT x.totalCounts as 'totalCounts',
            f.FinancingAgencyID as 'idFA',
			f.AgencyName as 'name' 
		FROM 
        FinancingAgency f,
        (select count(*) as totalCounts FROM FinancingAgency f WHERE 1=1 {$where}) as x 
        WHERE 1=1
        {$where} 
		ORDER BY 
        f.AgencyName
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
        $resultArray[] = array("id" => $row->idFA, "full_name" => $row->name);
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