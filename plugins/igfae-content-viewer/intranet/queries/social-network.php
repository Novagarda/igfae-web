<?php
function getSelectSocialNetworks(){
    
    $params = [];

    $sql = "SELECT 
        sn.SocialNetworkID as 'id', sn.Name as 'name'
    FROM 
    SocialNetwork sn   
    ORDER BY 
    sn.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetSocialNetwork(
            $row->id,
            $row->name
        );
    }

    return $result;
}
