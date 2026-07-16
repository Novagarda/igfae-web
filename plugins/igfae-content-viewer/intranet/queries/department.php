<?php
function getSelectDepartments()
{
    $where = "";
    $params = [];

    $sql = "SELECT 
        d.DepartmentID as 'id', d.Name as 'name' 
    FROM 
    Department d   
    ORDER BY 
        d.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetDepartment(
            $row->id,
            $row->name
        );
    }

    return $result;
}
