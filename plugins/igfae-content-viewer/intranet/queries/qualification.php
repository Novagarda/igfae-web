<?php
function getSelectQualifications()
{

    $sql = "SELECT 
			q.QualificationID as 'qualificationId' , q.Name as 'name'
		FROM 
        Qualification q
		ORDER BY 
			q.Name
		ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetQualification(
            $row->qualificationId,
            $row->name
        );
    }

    return $result;
}
