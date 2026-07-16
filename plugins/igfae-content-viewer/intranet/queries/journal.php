<?php
function getSelectJournals($texto, $page)
{
    $where = "";
    $params = [];
    if (isset($texto)) {
        $where .= " AND (j.FullName LIKE %s)";
        $params[] = '%' . $texto . '%';
        // Añadimos dos veces el parámetro para la clausula where del select count
        $params[] = '%' . $texto . '%';
    }

    $sql = "SELECT x.totalCounts as 'totalCounts', 
			j.JournalID as 'journalId' , j.FullName as 'fullName'
		FROM 
        Journal j,
        (select count(*) as totalCounts FROM Journal j WHERE 1=1 {$where} ) as x  
        WHERE 1=1
        {$where} 
		ORDER BY 
        j.FullName
			ASC";
    // Obtenemos los resultados en grupos    
    $amount = IGFAE_INTRANET_NUMBER_ELEMENTS_SELECT_ASYNC;
    if (is_null($page)) {
        $page = 1;
    }
    $offset = (($page - 1) * $amount);
    $sql .= " LIMIT {$offset}, {$amount}";
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->journalId, "full_name" => $row->fullName);
    }
    $result['items']  = $resultArray;
    $result['total_count'] = $row->totalCounts;

    return $result;
}

function getJournal($id)
{
    $where = "";
    $params = [];
    $where .= " AND (j.JournalID = %d)";
    $params[] = $id;
    
    $sql = "SELECT  j.JournalID as 'journalId' , j.FullName as 'fullName'
		FROM 
        Journal j       
        WHERE 1=1
        {$where};";
    
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->journalId, "full_name" => $row->fullName);
    }
    
    return $resultArray[0];
}

function saveNewJournal($journal)
{
    // Recuperamos los campos del formulario para insertar
    $name = $journal->getFullName();
    $ISOAbbreviation = $journal->getISOAbbreviation();
    $journalArea = $journal->getJournalAreaId();
    $electronicISSN = $journal->getElectronicISSN();
    $printedISSN = $journal->getPrintedISSN();
    $reference = $journal->getReference();

    $sql = "INSERT INTO Journal 
    (FullName, ISOAbbreviation, JournalAreaID, ElectronicISSN, PrintedISSN, Reference) 
    VALUES ( '$name',              
            " . (isset($ISOAbbreviation) ? "'$ISOAbbreviation'" : "NULL") . ",
            '$journalArea', 
            " . (isset($electronicISSN) ? "'$electronicISSN'" : "NULL") . ",
            " . (isset($printedISSN) ? "'$printedISSN'" : "NULL") . ",
            '$reference'
            )";
    $id = igfae_intranet_save($sql);
}

function getISSNValues($journal)
{
    $electronicISSN = $journal->getElectronicISSN();
    $printedISSN = $journal->getPrintedISSN();

    $where = "";
    $params = [];
    if (isset($electronicISSN) && isset($printedISSN)) {
        $where .= " AND (j.ElectronicISSN LIKE %s OR j.PrintedISSN LIKE %s)";
        $params[] = '%' . $electronicISSN . '%';
        $params[] = '%' . $printedISSN . '%';
       
    }
    if (isset($electronicISSN) && !isset($printedISSN)) {
        $where .= " AND (j.ElectronicISSN LIKE %s)";
        $params[] = '%' . $electronicISSN . '%';
        
       
    }
    if (isset($printedISSN) && !isset($electronicISSN)) {
        $where .= " AND (j.PrintedISSN LIKE %s)";
        $params[] = '%' . $printedISSN . '%';
       
    }

    $sql = "SELECT 
			j.JournalID as 'journalId' , j.FullName as 'fullName'
		FROM 
        Journal j 
        WHERE 1=1
        {$where} 
		ORDER BY 
        j.FullName
			ASC";
    
    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->journalId, "full_name" => $row->fullName);
    }
    return count($resultArray);
}
