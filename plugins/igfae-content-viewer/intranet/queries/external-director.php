<?php
function getSelectExternalDirectors(){    
    $sql = "SELECT 
            ed.ExternalDirectorID as 'externalDirectorID',
			ed.Name  as 'name'
		FROM 
        ExternalDirector ed
		ORDER BY 
        ed.Name
			ASC";

    $rows = igfae_intranet_fetch_data($sql);
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetExternalDirector(
            $row->externalDirectorID,
            $row->name
        );
    }

    return $result;
}