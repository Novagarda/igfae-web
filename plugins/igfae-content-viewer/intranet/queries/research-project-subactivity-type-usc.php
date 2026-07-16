<?php
function getSelectResearchTypesSubActivitiesUSC()
{
 
    $sql = "SELECT 
			a.USCSubactivityTypeID as 'subActivityTypeUSCId' , a.Name as 'subActivityTypeUSC', a.Code as 'code'
		FROM 
        USCSubactivityType a
		ORDER BY 
			a.Name
            ASC";
             
    $rows = igfae_intranet_fetch_data($sql);
 
    $result = [];
 
    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetSubActivityType(
            $row->subActivityTypeUSCId,
            $row->subActivityTypeUSC,
            $row->code
        );
    }
 
    return $result;
}