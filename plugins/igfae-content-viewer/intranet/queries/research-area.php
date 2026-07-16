<?php
function getSelectResearchAreas()
{

    $sql = "SELECT 
			a.ResearchAreaID as 'researchAreaId' , a.Name as 'researchAreaName'
		FROM 
        ResearchArea a
		ORDER BY 
			a.Name
		ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetResearchArea(
            $row->researchAreaId,
            $row->researchAreaName
        );
    }

    return $result;
}
