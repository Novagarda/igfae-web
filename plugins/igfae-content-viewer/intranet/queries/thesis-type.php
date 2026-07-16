<?php
function getSelectThesisTypes()
{

    $sql = "SELECT 
        a.ThesisTypeID as 'thesisTypeId' , a.Name as 'thesisType'
    FROM 
    ThesisType a
    ORDER BY 
        a.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetThesisType(
            $row->thesisTypeId,
            $row->thesisType
        );        
        
    }

    return $result;
}
