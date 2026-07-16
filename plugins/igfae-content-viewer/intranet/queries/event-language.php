<?php
function getSelectlanguages()
{

    $sql = "SELECT 
			a.languageID as 'languageID' , a.name as 'name' , a.isoCode as 'isoCode'
		FROM 
        Language a
		ORDER BY 
			a.name
		ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetLanguage(
            $row->languageID,
            $row->name,
            $row->isoCode
        );
    }

    return $result;
}