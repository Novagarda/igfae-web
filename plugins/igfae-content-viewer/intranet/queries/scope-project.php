<?php
function getSelectScopesProject()
{

    $sql = "SELECT DISTINCT
        s.ScopeID as 'scopeId' , s.Name as 'scopeName'
    FROM 
    Scope s
    ORDER BY 
        s.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[$row->scopeId] = $row->scopeName;
    }

    return $result;
}
