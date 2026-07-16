<?php
function getSelectFundingAgencies()
{

    $sql = "SELECT DISTINCT
        f.FundingAgencyID as 'fundingAgencyId' , f.Name as 'fundingAgencyName'
    FROM 
    FundingAgency f
    ORDER BY 
        f.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new FundingAgency(
            $row->fundingAgencyId,
            $row->fundingAgencyName
        );       
    }

    return $result;
}
