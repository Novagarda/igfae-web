<?php
function getSelectResearchPrograms()
{

    $sql = "SELECT 
        a.ResearchProgramId as 'researchProgramId' , 
        concat (a.Name, ' (', a.Acronym, ')') as 'name'
    FROM 
    ResearchProgram a
    ORDER BY 
        a.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetResearchProgram(
            $row->researchProgramId,
            $row->name
        );
        
    }

    return $result;
}
