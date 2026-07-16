<?php
function getSelectScientificFirms(){
    
    $params = [];

    $sql = "SELECT 
        sf.ScientificFirmID as 'id', sf.Firm as 'firm'
    FROM 
    ScientificFirm sf   
    ORDER BY 
    sf.Firm
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetScientificFirm(
            $row->id,
            $row->firm
        );
    }

    return $result;
}
