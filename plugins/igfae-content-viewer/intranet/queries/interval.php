<?php

function getSelectIntervals()
{

    $sql = "SELECT 
        i.IntervalID as 'intervalId' , i.Name as 'intervalName'
    FROM 
    `Interval` i
    ORDER BY i.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetInterval(
            $row->intervalId,
            $row->intervalName
        );        
        
    }
   
    return $result;
}

function getSelectIntervalsByType($typeIndicator)
{

    $sql = "SELECT 
                i.IntervalID as 'intervalId' , i.Name as 'intervalName'
            FROM 
                `Interval` i
            inner join IndicatorInterval ii 
                on ii.IntervalID = i.IntervalID and ii.IndicatorID = $typeIndicator
            ORDER BY i.Name ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetInterval(
            $row->intervalId,
            $row->intervalName
        );        
        
    }
   
    return $result;
}

function getIntervalsById($id) {

    $sql = "SELECT 
    i.IntervalID as 'intervalId' , i.Name as 'intervalName', i.MinValue as 'minValue', i.MaxValue as 'maxValue'
    FROM 
    `Interval` i
    WHERE i.IntervalID = $id ";

$rows = igfae_intranet_fetch_data($sql);

$result = NULL;

foreach ($rows['rows'] as $row) {
    $interval = new IntranetInterval();
    $interval -> setIntervalId($row->intervalId);
    $interval -> setIntervalName($row->intervalName);
    $interval -> setMinValue($row->minValue);
    $interval -> setMaxValue($row->maxValue);
        
    $result[] = $interval;
}

return $result;

}
