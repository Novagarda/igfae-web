<?php
function getSelectWorkline()
{

    $sql = "SELECT DISTINCT
			w.WorkLineID as 'workLineId' , w.Name as 'workLineName'
		FROM 
        WorkLine w
		ORDER BY 
			w.Name
		ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetWorkline(
            $row->workLineId,
            $row->workLineName
        );
    }

    return $result;
}
