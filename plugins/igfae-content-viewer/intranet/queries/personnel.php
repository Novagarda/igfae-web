<?php


function getSelectPersonnel()
{

    $sql = "SELECT 
            p1.PersonnelID as 'personnelID',
			concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) as 'name'
		FROM 
        Personnel p1	    
		ORDER BY 
        concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) 
			ASC";

    $rows = igfae_intranet_fetch_data($sql);
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetPersonnel(
            $row->personnelID,
            $row->name
        );
    }

    return $result;
}

/**
 * Obtiene el PersonnelID asociado a un usuario Wordpress
 */
function findPersonnelByWPUserId($wpUserId) {
    $sql = "SELECT 
        p1.PersonnelID as 'personnelID'
        FROM 
        Personnel p1
        WHERE p1.WPUserID = %d";

    $rows = igfae_intranet_fetch_data($sql, [$wpUserId]);
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result = $row->personnelID;
    }

    return $result;
}

/**
 * Obtiene el nombre del Personnel asociado al usuario de Wordpress
 */
function findPersonnelNameByWPUserId($wpUserId) {
    $sql = "SELECT 
        p1.name as 'Name'
        FROM 
        Personnel p1
        WHERE p1.WPUserID = %d";

    $rows = igfae_intranet_fetch_data($sql, [$wpUserId]);
    $result = NULL;

    foreach ($rows['rows'] as $row) {
        $result = $row->Name;
    }

    return $result;
}