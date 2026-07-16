<?php
function getSelectQuarters()
{       
    $sql = "SELECT QuarterID,
                concat(q.`Year`, '-Q', q.Quarter) as quarter
            from Quarter q
            where q.`Year` <= year(date_add(now(), interval 1 year))
            order BY q.`Year`, q.Quarter";


    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $quarter = new IntranetQuarter();
        $quarter -> setQuarterId($row->QuarterID);
        $quarter -> setQuarter($row->quarter);    
        
        $result[] = $quarter;
    }

    return $result;
}
