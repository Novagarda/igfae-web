<?php
function getSelectTransferType()
{

    $sql = "SELECT 
			tt.TransferTypeID as 'TransferTypeID', tt.Name as 'Name'
		FROM TransferType tt
		ORDER BY 
			tt.Name
		ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetTransferType(
            $row->TransferTypeID,
            $row->Name
        );
    }

    return $result;
}
