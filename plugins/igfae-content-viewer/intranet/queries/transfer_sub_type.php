<?php
function getSelectTransferSubtype($id)
{

    $sql = "SELECT 
        tst.TransferSubtypeID as 'TransferSubtypeID' , tst.Name as 'Name'
        FROM 
        TransferSubtype tst
        WHERE tst.TransferTypeID = $id
        ORDER BY 
        tst.Name
        ASC";

        $rows = igfae_intranet_fetch_data($sql);

        $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetTransferSubType(
        $row->TransferSubtypeID,
        NULL,
        $row->Name
        );
    }

      return $result;

}



