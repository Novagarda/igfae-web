<?php
function getSelectTRLType()
{

    $sql = "SELECT 
			tt.TRLTypeID as 'TRLTypeID', tt.Name as 'Name'
		FROM TRLType tt
		ORDER BY 
			tt.Name
        ASC";
        
    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetTRLType(
            $row->TRLTypeID,
            $row->Name
        );
    }

    return $result;
}
