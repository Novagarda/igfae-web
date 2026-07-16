<?php
function getSelectEventType()
{

    $sql = "SELECT 
			a.EventTypeID as 'eventTypeId' , a.Name as '_eventTypeName'
		FROM 
        EventType a
		ORDER BY 
			a.Name
		ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetEventType(
            $row->eventTypeId,
            $row->_eventTypeName
        );
    }

    return $result;
}