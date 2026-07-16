<?php
function getSelectJournalAreas()
{

    $sql = "SELECT 
			j.JournalAreaID as 'journalAreaId' , j.Area as 'name'
		FROM 
        JournalArea j
		ORDER BY 
        j.Area
			ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetJournalArea(
            $row->journalAreaId,
            $row->name
        );
    }

    return $result;
}
