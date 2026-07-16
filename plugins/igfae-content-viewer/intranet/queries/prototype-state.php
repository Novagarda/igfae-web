<?php
function getSelectPrototypeState()
{

    $sql = "SELECT 
			a.PrototypeStateID as 'stateId' , a.Name as 'stateName'
		FROM 
        PrototypeState a
		ORDER BY 
			a.Name
			ASC";
    $rows = igfae_intranet_fetch_data($sql);
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetPrototypeState(
            $row->stateId,
            $row->stateName
        );
    }

    return $result;
}
