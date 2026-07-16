<?php
function getSelectJournalEditorials()
{

    $sql = "SELECT 
			j.JournalEditorialID as 'journalEditorialId' , j.Name as 'name'
		FROM 
        JournalEditorial j
		ORDER BY 
        j.Name
			ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetJournalEditorial(
            $row->journalEditorialId,
            $row->name
        );
    }

    return $result;
}
