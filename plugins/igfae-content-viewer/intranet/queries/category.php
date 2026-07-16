<?php
function getSelectCategoryTypes()
{    
   
    $sql = "SELECT DISTINCT
        c.CategoryTypeID as 'id', c.Name as 'name' 
    FROM 
    CategoryType c
    WHERE 1=1        
    ORDER BY 
        c.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);
    
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetCategoryType(
            $row->id,
            $row->name
        );       
    }

    return $result;
}
function getSelectCategoriesByType($type)
{
    $where = "";
    $params = [];
    
    if (isset($type)) {
        $where .= " AND c.CategoryTypeID = %d";
        $params[] = $type;
    }
    $sql = "SELECT DISTINCT
        c.CategoryID as 'id', c.Name as 'name' 
    FROM 
    Category c
    WHERE 1=1
        {$where}  
    ORDER BY 
        c.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);
    
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetCategory(
            $row->id,
            $row->name
        );       
    }

    return $result;
}
