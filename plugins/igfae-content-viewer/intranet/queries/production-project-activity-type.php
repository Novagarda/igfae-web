<?php
function getSelectTypesActivities()
{

    $sql = "SELECT 
			a.ActivityTypeID as 'activityTypeId' , a.Name as 'activityType'
		FROM 
        ProductionProjectActivityType a
		ORDER BY 
			a.Name
			ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetActivityType(
            $row->activityTypeId,
            $row->activityType
        );
    }

    return $result;
}
