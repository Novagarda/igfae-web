<?php
function getSelectResearchGroups()
{
    $sql = "SELECT 
        a.ResearchGroupId as 'researchGroupId' , concat (a.Name, ' (', a.Acronym, ')') as 'name'
    FROM 
    ResearchGroup a
    ORDER BY 
        a.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetResearchGroup(
            $row->researchGroupId,
            $row->name
        );
    }

    return $result;
}
