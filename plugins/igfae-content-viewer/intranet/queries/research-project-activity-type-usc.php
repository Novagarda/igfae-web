<?php
function getSelectResearchTypesActivitiesUSC()
{

    $sql = "SELECT 
			a.USCActivityTypeID as 'activityTypeUSCId' , a.Name as 'activityTypeUSC'
		FROM 
        USCActivityType a
		ORDER BY 
			a.Name
			ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetActivityType(
            $row->activityTypeUSCId,
            $row->activityTypeUSC
        );
    }

    return $result;
}
