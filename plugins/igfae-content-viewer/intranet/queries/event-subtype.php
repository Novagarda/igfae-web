<?php
function getSelectEventSubType($type)
{
    $where = "";
    $params = [];

    if (isset($type) && gettype($type) == "array") {
        $typesCount = count($type);
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholders = implode(',', $paramsPlaceholders);

        $where .= " AND (a.EventTypeId IN ({$placeholders}))";
        $params = array_merge($params, $type);
    } else {
        $where .= " AND a.EventTypeId = %d ";
        $params[] = $type;
    }


    $sql = "SELECT 
			a.EventSubTypeID as 'subtypeId' , a.EventTypeId as 'typeId', a.Name as 'name'
		FROM 
        EventSubType a
        WHERE 1=1
        {$where}  
		ORDER BY 
			a.Name
		ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetEventSubType(
            $row->subtypeId,
            $row->typeId,
            $row->name
        );
    }

    return $result;
}
