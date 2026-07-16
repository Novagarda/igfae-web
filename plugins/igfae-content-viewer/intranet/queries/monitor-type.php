<?php
function getSelectMonitorsTypes()
{       
    $sql = "SELECT mt.MonitorTypeID,
                   mt.Description as description 
            from MonitorType  mt
            order BY mt.Description ASC";


    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $monitorType = new IntranetMonitorType();
        $monitorType -> setMonitorTypeId($row->MonitorTypeID);
        $monitorType -> setDescription($row->description);    
        
        $result[] = $monitorType;
    }

    return $result;
}
