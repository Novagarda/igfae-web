<?php


function fetchEquipments($equipments, $offset = 0, $amount = 10, $order = null, $getIds = false)
{
    $where = "";
    $prepareParams = [];
    if (isset($equipments)) {
        $queryPrepare = getPrepareSearchEquipments($equipments);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }
    $order = getOrderEquipment($order);
    $pagination = [];
    $limit = "";

    $sql = "Select DISTINCT 
         a.EquipmentID 
       , a.EquipmentName
       , a.EquipmentFamilyName
       , a.EquipmentLaboratoryName
       , a.TechnicalContact
       , a.familyID
       , a.laboratoryID
       , GROUP_CONCAT(fr2.FamilyResponsable separator ' ; ') as FamilyResponsable
       , a.Available
        FROM (
              Select DISTINCT 
                      e.EquipmentID 
                    , e.EquipmentName
                    , f.EquipmentFamilyName
                    , l.EquipmentLaboratoryName
                    , GROUP_CONCAT(distinct CONCAT(COALESCE(etc.TechnicalName,' - '), ' / ', COALESCE(etc.TechnicalEmail,' - '),' / ', COALESCE(etc.TechnicalTelephone,' - ')) separator ';') as TechnicalContact
                    , f.familyID
                    , l.laboratoryID 
                    , av.Available
              FROM Equipment e
              LEFT JOIN EquipmentFamily f 
                    on (f.FamilyId=e.FamilyID)
              LEFT JOIN EquipmentLaboratory l 
                    on (l.LaboratoryID=e.LaboratoryID)
              LEFT JOIN (
                    select efr.FamilyID, p.PersonnelID
                    from EquipmentFamilyResponsable efr 
                    left join Personnel p on efr.PersonnelID=p.PersonnelID 
                    GROUP BY efr.EquipmentFamilyResponsableID 
                    ) fr on e.FamilyID =fr.familyID
              LEFT JOIN EquipmentTechnicalContact etc 
                    on etc.EquipmentTechnicalContactID = e.TechnicalContactID 
              LEFT JOIN ( 
                    ( SELECT e.EquipmentID , '1' as Available FROM Equipment e
                                  WHERE  not exists 
                                        (Select * from  EquipmentForHireHistory ehh 
                                               where ehh.EquipmentID=e.EquipmentID 
                                               and CURDATE() >= ehh.StartDate AND (CURDATE()<= ehh.EndDate  or ehh.EndDate is null))) 
                    UNION ALL
                    ( SELECT e.EquipmentID , '0' as Available FROM Equipment e
                                  WHERE  exists 
                                        (Select * from  EquipmentForHireHistory ehh 
                                               where ehh.EquipmentID=e.EquipmentID 
                                               and CURDATE() >= ehh.StartDate AND (CURDATE()<= ehh.EndDate  or ehh.EndDate is null)))
                                               ) av on av.EquipmentID =e.EquipmentID
              where {$where}
              group by e.EquipmentID, fr.PersonnelID
       ) a 
        LEFT JOIN 
              (select efr.FamilyID, p.PersonnelID, GROUP_CONCAT(CONCAT(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') AS FamilyResponsable 
              from EquipmentFamilyResponsable efr 
              left join Personnel p on efr.PersonnelID=p.PersonnelID 
              GROUP BY efr.EquipmentFamilyResponsableID ) fr2 on fr2.FamilyID= a.familyID
        group by a.equipmentID
        order by {$order}";
    
    if($getIds){
      $idEquipments = [];
      $rows = igfae_intranet_fetch_data($sql, $prepareParams);
      foreach ($rows['rows'] as $row) {
        $idEquipments[] = $row->EquipmentID;
      }
      return $idEquipments;
    }

    if (isset($amount)) {
        $pagination = getPaginationInfoEquipment($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);
    $equipmentsList = [];
    foreach ($rows['rows'] as $row) {
        $equipment = new IntranetEquipment();
        $equipment->setEquipmentID($row->EquipmentID);     
        $equipment->setEquipmentName($row->EquipmentName);
        $equipment->setEquipmentFamily($row->EquipmentFamilyName);        
             
        $equipment->setEquipmentLaboratory($row->EquipmentLaboratoryName);
        $equipment->setEquipmentAvailable($row->Available); 

        if($row->TechnicalContact == ' -  /  -  /  - ' || $row->TechnicalContact == ' /  / '){
            $equipment->setEquipmentTechnicalContact('');
        } else {
            $equipment->setEquipmentTechnicalContact($row->TechnicalContact);
        }     
        $equipment->setEquipmentResponsableFamily($row->FamilyResponsable);
        $equipmentsList[] = $equipment;
    }

    $info = [
        'equipments' => $equipmentsList,
        'pagination' => $pagination
    ];

    return $info;
}

function getPaginationInfoEquipment($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];


    return $result;
}


function getPrepareSearchEquipments($equipments)
{
    $where = "1=1 ";
    $params = [];

    // EquipmentName
    if($equipments->getEquipmentName()){
        $where .= " AND e.EquipmentName like %s ";
        $params[] = '%' . $equipments->getEquipmentName() . '%';
    }
    // TechnicalContact
    if ($equipments->getEquipmentTechnicalContact()) {
        $equipmentTechnicalContactCount = count($equipments->getEquipmentTechnicalContact());
        $paramsPlaceholders = array_fill(0, $equipmentTechnicalContactCount, '%d');
        $placeholdersEquipmentTechnicalContact = implode(',', $paramsPlaceholders);

        $where .= " AND (etc.EquipmentTechnicalContactID IN ({$placeholdersEquipmentTechnicalContact}))";
        $params = array_merge($params, $equipments->getEquipmentTechnicalContact());
    }
    // Laboratory
    if ($equipments->getEquipmentLaboratory()) {
        $equipmentLaboratoryCount = count($equipments->getEquipmentLaboratory());
        $paramsPlaceholders = array_fill(0, $equipmentLaboratoryCount, '%d');
        $placeholdersEquipmentLaboratory = implode(',', $paramsPlaceholders);

        $where .= " AND (l.LaboratoryID IN ({$placeholdersEquipmentLaboratory}))";
        $params = array_merge($params, $equipments->getEquipmentLaboratory());
    }

    // Family
    if ($equipments->getEquipmentFamily()) {
        $equipmentFamilyCount = count($equipments->getEquipmentFamily());
        $paramsPlaceholders = array_fill(0, $equipmentFamilyCount, '%d');
        $placeholdersEquipmentFamily = implode(',', $paramsPlaceholders);

        $where .= " AND (fr.FamilyID IN ({$placeholdersEquipmentFamily}))";
        $params = array_merge($params, $equipments->getEquipmentFamily());
    }

    // Responsable Family
    if ($equipments->getEquipmentResponsableFamily()) {
        $equipmentResponsableFamilyCount = count($equipments->getEquipmentResponsableFamily());
        $paramsPlaceholders = array_fill(0, $equipmentResponsableFamilyCount, '%d');
        $placeholdersEquipmentResponsableFamily = implode(',', $paramsPlaceholders);

        $where .= " AND (fr.PersonnelID IN ({$placeholdersEquipmentResponsableFamily}))";
        $params = array_merge($params, $equipments->getEquipmentResponsableFamily());
    }   

    // Available
    if($equipments->getEquipmentAvailable() != NULL){
        if($equipments->getEquipmentAvailable() == 0) {
            $where .= " AND av.Available in (0)";
        }else if($equipments->getEquipmentAvailable() == 1) {            
            $where .= " AND av.Available in (1)";
        }
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function getOrderEquipment($order)
{
    if (!isset($order)) {
        return ' a.EquipmentName ASC';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0:
                $sort .= ' a.EquipmentName ';
                break;   
            case 1:
                $sort .= ' a.TechnicalContact ';
                break;          
            case 2:
                $sort .= ' a.EquipmentLaboratoryName ';
                break; 
            case 3:
                $sort .= ' a.EquipmentFamilyName ';
                break; 
            case 4:
                $sort .= ' FamilyResponsable ';
                break;     
            case 5:
                $sort .= ' a.Available ';
                break;         
            default:
                $sort .= ' a.EquipmentName ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function fetchEquipmentsExport($equipmentsID){
    $equipments = [];
    $idsEquipmentsStr = "";
    $idsEquipmentsStr = implode(',', $equipmentsID);

    $sql = "Select DISTINCT 
         a.EquipmentID 
       , a.EquipmentName
       , a.EquipmentFamilyName
       , a.EquipmentLaboratoryName
       , a.TechnicalContact
       , GROUP_CONCAT(fr2.FamilyResponsable separator ' ; ') as FamilyResponsable
       , a.Available
        FROM (
              Select DISTINCT 
                      e.EquipmentID 
                    , e.EquipmentName
                    , f.EquipmentFamilyName
                    , l.EquipmentLaboratoryName
                    , GROUP_CONCAT(distinct CONCAT(COALESCE(etc.TechnicalName,' - '), ' / ', COALESCE(etc.TechnicalEmail,' - '),' / ', COALESCE(etc.TechnicalTelephone,' - ')) separator ';') as TechnicalContact
                    , f.familyID
                    , l.laboratoryID 
                    , av.Available
              FROM Equipment e
              LEFT JOIN EquipmentFamily f 
                    on (f.FamilyId=e.FamilyID)
              LEFT JOIN EquipmentLaboratory l 
                    on (l.LaboratoryID=e.LaboratoryID)
              LEFT JOIN (
                    select efr.FamilyID, p.PersonnelID
                    from EquipmentFamilyResponsable efr 
                    left join Personnel p on efr.PersonnelID=p.PersonnelID 
                    GROUP BY efr.EquipmentFamilyResponsableID 
                    ) fr on e.FamilyID =fr.familyID
              LEFT JOIN EquipmentTechnicalContact etc 
                    on etc.EquipmentTechnicalContactID = e.TechnicalContactID 
              LEFT JOIN ( 
                    ( SELECT e.EquipmentID , '1' as Available FROM Equipment e
                                  WHERE  not exists 
                                        (Select * from  EquipmentForHireHistory ehh 
                                               where ehh.EquipmentID=e.EquipmentID 
                                               and CURDATE() >= ehh.StartDate AND (CURDATE()<= ehh.EndDate  or ehh.EndDate is null))) 
                    UNION ALL
                    ( SELECT e.EquipmentID , '0' as Available FROM Equipment e
                                  WHERE  exists 
                                        (Select * from  EquipmentForHireHistory ehh 
                                               where ehh.EquipmentID=e.EquipmentID 
                                               and CURDATE() >= ehh.StartDate AND (CURDATE()<= ehh.EndDate  or ehh.EndDate is null)))
                                               ) av on av.EquipmentID =e.EquipmentID  
            where e.EquipmentID IN ($idsEquipmentsStr)          
            group by e.EquipmentID, fr.PersonnelID
       ) a 
        LEFT JOIN 
              (select efr.FamilyID, p.PersonnelID, GROUP_CONCAT(CONCAT(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') AS FamilyResponsable 
              from EquipmentFamilyResponsable efr 
              left join Personnel p on efr.PersonnelID=p.PersonnelID 
              GROUP BY efr.EquipmentFamilyResponsableID ) fr2 on fr2.FamilyID= a.familyID
        group by a.equipmentID
        ";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];
}

function getForHireHistoryByEquipment($id)
{
    //Buscamos datos en EquipmentForHireHistory
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND e.EquipmentID =  %d";
    $sql = "SELECT e.EquipmentID as 'EquipmentID'
            from EquipmentForHireHistory efhh 
            left join Equipment e
                on efhh.EquipmentID = e.EquipmentID 
            where 1=1 
            {$where} ";

    
    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

// Delete user
function deleteEquipment($id)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_EQUIPMENT, $id);

    $where = array('EquipmentID' => $id);

    igfae_intranet_delete('EquipmentAmortization', $where);
    igfae_intranet_delete('EquipmentMaintenanceHistory', $where);    
    $resultArrayFilesID = getFileIDEquipmentDocumentation($id);
    igfae_intranet_delete('EquipmentDocumentation', $where);
    
    // Borrar documentacion fisica        
    foreach($resultArrayFilesID as $row){
        igfae_intranet_delete_file(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $row['fileName'] .'.' . $row['fileExtension']);
        deleteFileFromBBDD($row['id']);
    }
    igfae_intranet_delete('Equipment', $where);    

    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();        
    } else {
        igfae_intranet_rollback_transaction();
    }
}

function getFileIDEquipmentDocumentation($equipmentID){
    $where = " e.EquipmentID =  %d";
    $params[] = $equipmentID;

    $sql = "SELECT DISTINCT ed.FileID as 'fileID', f.FileName as 'fileName', fe.FileExtension  as 'fileExtension'
            from EquipmentDocumentation ed 
            left join Equipment e on e.EquipmentID = ed.EquipmentID 
            left join File f on f.FileID = ed.FileID 
            left join FileExtension fe on fe.FileExtensionID = f.FilesExtensionID  
            where {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->fileID, "fileName" => $row->fileName, "fileExtension" => $row->fileExtension);
    }
    
    return $resultArray;
}

function isEquipmentConsolidated($id) {    
    $sql = "select count(*) 
    from Equipment e
    where
        e.EquipmentID = $id
    ;";
    
    $result = igfae_intranet_get_var($sql);

    if ($result) {
        return true;
    } else {
        return false;
    }
}

// Save Equipment
function saveEquipment($datosInsert, $equipment)
{   
    $idEquipment = $_POST[IGFAE_INTRANET_PARAM_IDEQUIPMENT];

    if (isset($idEquipment) && strlen($idEquipment) > 0) {

         /** START TRANSACTION */
        igfae_intranet_begin_transaction();
        $equipmentOldInfo = getEquipmentById($idEquipment);

        $where = array('EquipmentID' => $idEquipment);

        // Technical Contact
        $datosInsert['TechnicalContactID'] = checkTechnicalContact($equipmentOldInfo, $equipment, $datosInsert, $where);

        igfae_intranet_update("Equipment", $datosInsert, $where, null, null);

        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsEquipment($idEquipment);

        //Alta datos tablas relacionadas        
        // Amortization
        insertEquipmentAmortization($idEquipment, $equipment->getAmortizations());

        // Maintenance History
        insertEquipmentMaintenanceHistory($idEquipment, $equipment->getMaintenanceHistory());

        // Auditoria        
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_EQUIPMENT, $idEquipment);

        $id = NULL;
        $photoStatus = checkFile($_FILES['photoEquipment'], $idEquipment, $equipment->getDeletePhoto(), $equipmentOldInfo->getEquipmentPhoto(), $equipmentOldInfo->getEquipmentPhotoID(), 1);
        $acceptanceStatus = checkFile($_FILES['acceptanceFile'], $idEquipment, $equipment->getDeleteAcceptance(), $equipmentOldInfo->getEquipmentAcceptance(), $equipmentOldInfo->getEquipmentAcceptanceID(),2);        

        // Documentations
        $listDocuments = $equipment->getDocumentations();
        $listDeleteDocumentations = $equipment->getListDeleteDocumentations();
                    
        if(isset($listDeleteDocumentations)){
            foreach ($listDeleteDocumentations as $item) {
                $title = explode("-", $item);     
                $text = "";    
                if(count($title) > 2){
                    $i = 1;
                    while($i < count($title) - 1){
                        $text .= $title[$i];
                        if($i < count($title) - 2){
                            $text .= '-';
                        }
                        $i++;
                    }
                } else {
                    $text = $title[1];
                }

                $nameFile = $idEquipment .'-3-' . $text;  
                $sql = "SELECT DISTINCT f.FileID, f.FileName , fe.FileExtension 
                            FROM File f
                            left join FileExtension fe on fe.FileExtensionID = f.FilesExtensionID
                            WHERE f.FileName like '$nameFile'";
                $row = igfae_intranet_fetch_data($sql);
                if($row['count'] > 0){
                    foreach ($row['rows'] as $elem) {
                        $nameFileAux = $elem->FileName . "." . $elem->FileExtension;
                        igfae_intranet_delete_file(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $nameFileAux);
                        $IDFile = $elem->FileID;
                        deleteFileFromBBDD($IDFile);
                        $sql = "DELETE FROM EquipmentDocumentation WHERE FileID = $IDFile";
                        igfae_intranet_save($sql);
                    }
                }
            }
        }

        if (isset($listDocuments)) {
            foreach ($listDocuments as $item) {    
                $title = explode("-", $item);     
                $text = "";    
                if(count($title) > 2){
                    $i = 0;
                    while($i < count($title) - 1){
                        $text .= $title[$i];
                        if($i < count($title) - 2){
                            $text .= '-';
                        }
                        $i++;
                    }
                } else {
                    $text = $title[0];
                }

                $item = str_replace(" ", "_", $item);

                saveFileEquipment($idEquipment, $_FILES[$item], 3, $text, $equipment->getListChangeNameDocumentations());
            }
        }

        if (igfae_intranet_is_transaction_ok() && !$photoStatus['fileError'] && !$acceptanceStatus['fileError']) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
            if (!empty($photoStatus['file'])) {
                igfae_intranet_delete_file($photoStatus['file']);
            }
            if (!empty($acceptanceStatus['file'])) {
                igfae_intranet_delete_file($acceptanceStatus['file']);
            }
        }

    } else {
        //NUEVO EQUIPMENT

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();
                
        $idTechnicalContact = insertEquipmentTechnicalContact($equipment->getEquipmentTechnicalContact());
        $datosInsert['TechnicalContactID'] = $idTechnicalContact;

        $id = igfae_intranet_insert("Equipment", $datosInsert, null);
    
        $resultPhoto = saveFileEquipment($id, $_FILES['photoEquipment'], 1, null, null);
        $resultAcceptance = saveFileEquipment($id, $_FILES['acceptanceFile'], 2, null, null);

        // Amortization
        insertEquipmentAmortization($id, $equipment->getAmortizations());

        // Maintenance History
        insertEquipmentMaintenanceHistory($id, $equipment->getMaintenanceHistory());

        // Documentations
        insertEquipmentDocumentations($id, $equipment->getDocumentations());
        
        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_EQUIPMENT, $id);

        if (igfae_intranet_is_transaction_ok() && !$resultPhoto['fileError'] && !$resultAcceptance['fileError']) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
            if (!empty($resultPhoto['file'])) {
                igfae_intranet_delete_file($resultPhoto['file']);
            }
            if(!empty($resultAcceptance['file'])){
                igfae_intranet_delete_file($resultAcceptance['file']);
            }
            $id = null;
        }
    }

    return $id;
}

function checkTechnicalContact($equipmentOldInfo, $equipment, $datosInsert, $where){
    $elem = explode("/", $equipmentOldInfo->getEquipmentTechnicalContact());
    $technicalName = trim($elem[0]);
    $technicalEmail = trim($elem[1]);
    $technicalPhone = trim($elem[2]);
    $sql = "SELECT EquipmentTechnicalContactID FROM EquipmentTechnicalContact WHERE TechnicalName LIKE '$technicalName' AND TechnicalEmail  LIKE '$technicalEmail' AND TechnicalTelephone LIKE '$technicalPhone'";       
        
    $row = igfae_intranet_fetch_data($sql);
    if($row['count'] > 0){       
        $idTechnicalContact = $row['rows'][0]->EquipmentTechnicalContactID;
        $where = array('EquipmentTechnicalContactID' => $idTechnicalContact);
        $elem = explode("/", $equipment->getEquipmentTechnicalContact());
        $data = array('TechnicalName' => $elem[0], 'TechnicalEmail' => $elem[1], 'TechnicalTelephone' => $elem[2]);
        igfae_intranet_update("EquipmentTechnicalContact", $data, $where, null, null);
         
    } else {
        $idTechnicalContact = insertEquipmentTechnicalContact($equipment->getEquipmentTechnicalContact());
    }

    return $idTechnicalContact;
}

function checkFile($fileP, $idEquipment, $equipment, $equipmentOldInfo, $equipmentFileID, $type){

    $fileError = false;
    $file = null;

    if (!empty($fileP) && !empty($fileP['name'])) {

        // Solo viene informado si se ha introducido una nueva imagen

        $filename = $idEquipment . '-' . $type . '-' . substr($fileP['name'], 0, strrpos($fileP['name'], "."));

        $file = igfae_intranet_create_file($fileP, IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $filename, $filename);

        $fileId = null;
        if (!empty($file)) {
            $fileId = saveFile($file, 3);
            $sql = "SELECT * FROM EquipmentDocumentation WHERE EquipmentID = $idEquipment and TypeID = $type";
            $row = igfae_intranet_fetch_data($sql);

            if($row['count'] == 0){
                $data = array('EquipmentID' => $idEquipment, 'FileID' => $fileId, 'TypeID' => $type);
                igfae_intranet_insert('EquipmentDocumentation', $data);
            } else {
                $sql = "UPDATE EquipmentDocumentation SET FileID = $fileId WHERE EquipmentID = $idEquipment and TypeID = $type";
                igfae_intranet_save($sql);
            }

            if ($equipmentOldInfo != null && $equipmentOldInfo != "") {
                deleteFileFromBBDD($equipmentFileID);
                $nameFile = explode("/",$equipmentOldInfo);
                igfae_intranet_delete_file(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $nameFile[4]);
            }
        } else {
            $fileError = true;
        }
    } else if ($equipment != null && $equipment == "true" && $equipmentOldInfo != null && $equipmentOldInfo != "") {

        // Sino viene fichero de entrada pero viene activado el flag de borrado => Borramos la foto existente del usuario
        $sql = "SELECT DISTINCT FileID FROM EquipmentDocumentation WHERE EquipmentID = $idEquipment AND TypeID = $type";
        $row = igfae_intranet_fetch_data($sql);
        $IDFile = $row['rows'][0]->FileID;
        deleteFileFromBBDD($IDFile);
        $sql = "DELETE FROM EquipmentDocumentation WHERE FileID = $IDFile";
        igfae_intranet_save($sql);              
        $nameFile = explode("/",$equipmentOldInfo);
        igfae_intranet_delete_file(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $nameFile[4]);
    }

    return array(
        'fileError'  => $fileError,
        'file' => $file
    );  
}

// Delete relations equipment
function deleteRelationsEquipment($id)
{
   
    $where = array('EquipmentID' => $id);

    //Borramos las tablas relacionadas
    igfae_intranet_delete('EquipmentAmortization', $where);
    igfae_intranet_delete('EquipmentMaintenanceHistory', $where);
}

function insertEquipmentTechnicalContact($elements){
    $idTechnicalContact = NULL;
    if($elements != '')  {
        $elem = explode("/", $elements);
        if($elem[0] != null || $elem[1] != null || $elem[2] != null){
            $data = array('TechnicalName' => $elem[0], 'TechnicalEmail' => $elem[1], 'TechnicalTelephone' => $elem[2]);
            $idTechnicalContact = igfae_intranet_insert('EquipmentTechnicalContact', $data);
        }
    }
    return $idTechnicalContact;
}

function insertEquipmentAmortization($id, $elements){
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $elem) {
            $data = array('EquipmentID' => $id, 'AmortizationCode' => $elem['code'], 'AmortizationYear' => $elem['year'], 'AmortizationPorcentage' => formatNumberToBD($elem['percentage']), 'AmortizationAmount' => formatNumberToBD($elem['money']));
            igfae_intranet_insert('EquipmentAmortization', $data); 
        }
    }
}

function insertEquipmentMaintenanceHistory($id, $elements){
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $elem) {
            $technical = NULL;
            if(trim($elem['technical']) != ''){           
                $technical = str_replace(";", ".-", $elem['technical']);
            }           
            $data = array('EquipmentID' => $id, 'HistoryEvent' => $elem['event'], 'HistoryDate' => $elem['date'], 'HistoryPlace' => $elem['place'], 'HistoryCost' => str_replace(',','.',$elem['cost']), 'TechnicalIntervention' => $technical);
            igfae_intranet_insert('EquipmentMaintenanceHistory', $data); 
        }
    }
}

function insertEquipmentDocumentations($id, $elements){ 
     if (isset($id)  && $id > 0 && isset($elements)) {
            foreach ($elements as $item) {
                $title = explode("-", $item);    
                $text = "";    
                if(count($title) > 2){
                    $i = 0;
                    while($i < count($title) - 1){
                        $text .= $title[$i];
                        if($i < count($title) - 2){
                            $text .= '-';
                        }
                        $i++;
                    }
                } else {
                    $text = $title[0];
                }  
                $item = str_replace(" ", "_", $item);
                saveFileEquipment($id, $_FILES[$item], 3, $text, null);
            }
    }
}

function getEquipmentById($equipmentId){
    $equipment = NULL;
    $where = "";
    $prepareParams = [];
    if (isset($equipmentId)) {
        $where = " e.EquipmentID  = %d";
        $prepareParams = $equipmentId;
    }


    $sql = "Select DISTINCT 
         a.EquipmentID 
       , a.EquipmentName
       , a.TechnicalContact
       , a.familyID
       , a.laboratoryID
       , a.EquipmentFamilyName
       , GROUP_CONCAT(fr2.FamilyResponsableID separator ';') as FamilyResponsableID
       , GROUP_CONCAT(fr2.FamilyResponsable separator ' ; ') as FamilyResponsable
       , a.Available
       , a.SN 
       , a.InventoryNumber 
       , a.InvoiceNumber 
       , a.Location
       , a.FundingCode
       , a.OtherFunding
       , a.TechnicalSpecification 
       , a.Comments 
       , a.AcceptanceDate
       , a.EquipmentLaboratoryName
        FROM (
              Select DISTINCT 
                      e.EquipmentID 
                    , e.EquipmentName
                    , e.SN 
                    , e.InventoryNumber 
                    , e.InvoiceNumber 
                    , e.Location
                    , e.FundingCode
                    , e.OtherFunding
                    , e.TechnicalSpecification
                    , e.Comments 
                    , e.AcceptanceDate 
                    , GROUP_CONCAT(distinct CONCAT(COALESCE(etc.TechnicalName,' - '), ' / ', COALESCE(etc.TechnicalEmail,' - '),' / ', COALESCE(etc.TechnicalTelephone,' - ')) separator ';') as TechnicalContact
                    , f.familyID
                    , f.EquipmentFamilyName
                    , l.laboratoryID 
                    , l.EquipmentLaboratoryName
                    , av.Available
              FROM Equipment e
              LEFT JOIN EquipmentFamily f 
                    on (f.FamilyId=e.FamilyID)
              LEFT JOIN EquipmentLaboratory l 
                    on (l.LaboratoryID=e.LaboratoryID)
              LEFT JOIN (
                    select efr.FamilyID, p.PersonnelID
                    from EquipmentFamilyResponsable efr 
                    left join Personnel p on efr.PersonnelID=p.PersonnelID 
                    GROUP BY efr.EquipmentFamilyResponsableID 
                    ) fr on e.FamilyID =fr.familyID
              LEFT JOIN EquipmentTechnicalContact etc 
                    on etc.EquipmentTechnicalContactID = e.TechnicalContactID 
              LEFT JOIN ( 
                    ( SELECT e.EquipmentID , '1' as Available FROM Equipment e
                                  WHERE  not exists 
                                        (Select * from  EquipmentForHireHistory ehh 
                                               where ehh.EquipmentID=e.EquipmentID 
                                               and CURDATE() >= ehh.StartDate AND (CURDATE()<= ehh.EndDate  or ehh.EndDate is null))) 
                    UNION ALL
                    ( SELECT e.EquipmentID , '0' as Available FROM Equipment e
                                  WHERE  exists 
                                        (Select * from  EquipmentForHireHistory ehh 
                                               where ehh.EquipmentID=e.EquipmentID 
                                               and CURDATE() >= ehh.StartDate AND (CURDATE()<= ehh.EndDate  or ehh.EndDate is null)))
                                               ) av on av.EquipmentID =e.EquipmentID
              where {$where}
              group by e.EquipmentID, fr.PersonnelID
       ) a 
        LEFT JOIN 
               (select efr.FamilyID, p.PersonnelID, GROUP_CONCAT(CONCAT(p.PersonnelID) separator ';') AS FamilyResponsableID, 
               GROUP_CONCAT(CONCAT(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') AS FamilyResponsable  
              from EquipmentFamilyResponsable efr 
              left join Personnel p on efr.PersonnelID=p.PersonnelID 
              GROUP BY efr.EquipmentFamilyResponsableID ) fr2 on fr2.FamilyID= a.familyID
        ";

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $equipment = new IntranetEquipment();
        $equipment->setEquipmentID($row->EquipmentID);     
        $equipment->setEquipmentName($row->EquipmentName);
        $equipment->setEquipmentSN($row->SN);
        $equipment->setEquipmentInventoryNumber($row->InventoryNumber);
        $equipment->setEquipmentInvoiceNumber($row->InvoiceNumber);
        $equipment->setEquipmentLocation($row->Location);
        $equipment->setEquipmentFundingCode($row->FundingCode);
        $equipment->setEquipmentOtherFunding($row->OtherFunding);      
        $equipment->setLaboratoryId($row->laboratoryID);
        $equipment->setFamilyId($row->familyID);
        $equipment->setResponsableFamilyId(createArrayValuesEquipment($row->FamilyResponsableID, $row->FamilyResponsable));
        $equipment->setEquipmentAvailable($row->Available); 
        $equipment->setTechnicalSpecifications($row->TechnicalSpecification);
        $equipment->setComments($row->Comments);
        $equipment->setEquipmentAcceptanceDate($row->AcceptanceDate);
        $equipment->setEquipmentFamily($row->EquipmentFamilyName);
        $equipment->setEquipmentResponsableFamily($row->FamilyResponsable);
        $equipment->setEquipmentLaboratory($row->EquipmentLaboratoryName);

        if($row->TechnicalContact == ' -  /  -  /  - '){
            $equipment->setEquipmentTechnicalContact('');
        } else {
            $equipment->setEquipmentTechnicalContact($row->TechnicalContact);
        }  
    }

    $equipmentId = $equipment->getEquipmentID();

    // GET Photo
    $sql = "select distinct ed.FileID from EquipmentDocumentation ed where ed.TypeID = 1 and ed.EquipmentID in ($equipmentId)";
    $rows = igfae_intranet_fetch_data($sql);
    $filePhoto = NULL;
    $photoID = NULL;
    foreach ($rows['rows'] as $row) {
        if (isset($row->FileID)) {
            $photoID = $row->FileID;
        }
    }
    if ($photoID) {
        $filePhoto = getPathToFile($photoID);
    }
    $equipment->setEquipmentPhoto($filePhoto);
    $equipment->setEquipmentPhotoID($photoID);

    // GET Acceptance
    $sql = "select distinct ed.FileID from EquipmentDocumentation ed where ed.TypeID = 2 and ed.EquipmentID in ($equipmentId)";
    $rows = igfae_intranet_fetch_data($sql);
    $fileAcceptance = NULL;
    $photoID = NULL;
    foreach ($rows['rows'] as $row) {
        if (isset($row->FileID)) {
            $photoID = $row->FileID;
        }
    }
    if ($photoID) {
        $fileAcceptance = getPathToFile($photoID);
    }
    $equipment->setEquipmentAcceptance($fileAcceptance);
    $equipment->setEquipmentAcceptanceID($photoID);

    // GET Amortization
    $sql = "select DISTINCT ea.AmortizationCode, ea.AmortizationYear, ea.AmortizationPorcentage, ea.AmortizationAmount 
             from EquipmentAmortization ea where ea.EquipmentID in ($equipmentId)";
    $rows = igfae_intranet_fetch_data($sql);
    $amortizations = [];
    foreach ($rows['rows'] as $row) {        
        $textPercentage = str_replace('.', ",", $row->AmortizationPorcentage);
        $textAmount = str_replace('.', ",", $row->AmortizationAmount);
        $key = $row->AmortizationCode . ";" . $row->AmortizationYear . ";" . $textPercentage . ";" . $textAmount;
        $amortizations[$key] = $row->AmortizationCode;
    }

    $equipment->setAmortizations($amortizations);

    // GET Maintenance History
    $sql = "select DISTINCT emh.HistoryEvent , emh.HistoryDate , emh.HistoryPlace , emh.HistoryCost, emh.TechnicalIntervention 
             from EquipmentMaintenanceHistory emh where emh.EquipmentID in ($equipmentId) order by emh.HistoryDate DESC ";
    $rows = igfae_intranet_fetch_data($sql);
    $maintananceHistory = [];
    foreach ($rows['rows'] as $row) {  
        $textTechnicalSpecifications = str_replace('"', "'", $row->TechnicalIntervention);
        $textTechnicalSpecifications = str_replace(";", ".-", $textTechnicalSpecifications);
        $textCost = str_replace('.', ",", $row->HistoryCost);
        $key = $row->HistoryEvent . ";" . $row->HistoryDate . ";" . $textCost  . ";" . $row->HistoryPlace. ";" . $textTechnicalSpecifications;
        $maintananceHistory[$key] = $row->HistoryEvent;
    }

    $equipment->setMaintenanceHistory($maintananceHistory);

    // GET DOCUMENTATION
    $sql = "select distinct ed.FileID, f.FileName, fe.FileExtension 
             from EquipmentDocumentation ed 
             left join File f on f.FileID = ed.FileID 
             left join FileExtension fe on fe.FileExtensionID  = f.FilesExtensionID 
             where ed.TypeID = 3 and ed.EquipmentID in ($equipmentId) order by f.FileID  desc";
    $rows = igfae_intranet_fetch_data($sql);
    $documents = [];
    foreach ($rows['rows'] as $row) {
                $title = explode('-',$row->FileName);     
                $name = "";    
                if(count($title) > 2){
                    $i = 0;
                    while($i < count($title)){
                        if($i > 1){
                            $name .= $title[$i];
                        }
                        if($i > 1 && $i < count($title) - 1){
                            $name .= '-';
                        }
                        $i++;
                    }
                } else {
                    $name = $title[2];
                }
        $key = $name . ";" . $row->FileExtension;
        $documents[$key] = $name;
    }
     $equipment->setDocumentations($documents);
    
    
    return $equipment;
}

function createArrayValuesEquipment($identificadores, $valores) {
    $arrayIds = explode(';', $identificadores);
    $arrayValues = explode(' ; ', $valores);
    $resultado = [];

    $posicion = 0;
    foreach ($arrayIds as $id) {
        $resultado[$id] = $arrayValues[$posicion];
        $posicion ++;
    }

    return $resultado;
}

function saveFileEquipment($id, $fileP, $type, $name, $listChangeName){
    $fileError = false;
    $file = null;
    
    if($type == 2){
        $name = $fileP['name'];
    }

    if (isset($fileP) && !empty($fileP) && isset($fileP['name']) && !empty($fileP['name'])) {
        if($name != null){
            $filename = $id . '-' . $type . '-' . $name;
        } else {
            $filename = $id . '-' . $type . '-' . substr($fileP['name'], 0, strrpos($fileP['name'], ".")); 
        }

        $file = igfae_intranet_create_file($fileP, IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $filename, $name);
        $fileId = null;
        if (!empty($file)) {
            $fileId = saveFile($file, IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH_ID);
            $data = array('EquipmentID' => $id, 'FileID' => $fileId, 'TypeID' => $type);
            igfae_intranet_insert('EquipmentDocumentation', $data);
        } else {
            $fileError = true;
        }
    } else {
        // only text change, update DB and file rename
        foreach($listChangeName as $item){
            $fileNewName = explode('.', $item['fileNew']);
            if($name == $fileNewName[0]){
                $fileOld = $id . '-' . $type . '-' . $item['fileOld'];
                $fileNew = $id . '-' . $type . '-' . $item['fileNew'];
                $where = array('FileName' => explode('.', $fileOld)[0]);
                $data = array('FileName' => explode('.', $fileNew)[0]);
                igfae_intranet_update("File", $data, $where, null, null);
                rename(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $fileOld, IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH . $fileNew);      
            } 
        }
    }
    return array(
        'fileError'  => $fileError,
        'file' => $file
    );  
}

function getBookingEquipments($offset = 0, $amount = 10, $order = null, $getIds = false){
    $order = getOrderEquipmentBooking($order);
    $pagination = [];
    $limit = "";
    $prepareParams = [];
    $sql = "select e.EquipmentID, e.EquipmentName, p.EMail, efhh.TemporalLocation, efhh.StartDate, efhh.EndDate 
        from EquipmentForHireHistory efhh 
        left join Equipment e on e.EquipmentID = efhh.EquipmentID  
        left join Personnel p on p.PersonnelID = efhh.PersonnelID 
        order by {$order}";

    if($getIds){
      $equipmentsBookingList = [];
      $rows = igfae_intranet_fetch_data($sql, $prepareParams);
      foreach ($rows['rows'] as $row) {
        $idEquipments[] = $row->EquipmentID;
      }
      return $equipmentsBookingList;
    }

    if (isset($amount)) {
        $pagination = getPaginationInfoEquipment($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $equipmentsBookingList = [];
    foreach ($rows['rows'] as $row) {
        $equipmentBooking = new IntranetEquipmentBookingHistory();
        $equipmentBooking->setEquipmentID($row->EquipmentID);     
        $equipmentBooking->setEquipmentName($row->EquipmentName);     
        $equipmentBooking->setPersonnel($row->EMail);
        $equipmentBooking->setTemporalLocation($row->TemporalLocation);
        $equipmentBooking->setStartDate($row->StartDate);
        $equipmentBooking->setEndDate($row->EndDate);
        $equipmentsBookingList[] = $equipmentBooking;
    }

    $info = [
        'equipmentsBooking' => $equipmentsBookingList,
        'pagination' => $pagination
    ];

    return $info;   
}

function getOrderEquipmentBooking($order)
{
    if (!isset($order)) {
        return ' e.EquipmentName ASC';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0:
                $sort .= ' e.EquipmentName ';
                break;   
            case 1:
                $sort .= ' p.EMail ';
                break;          
            case 2:
                $sort .= ' efhh.TemporalLocation ';
                break; 
            case 3:
                $sort .= ' efhh.StartDate ';
                break;    
            case 4:
                $sort .= ' efhh.EndDate ';
                break;
            default:
                $sort .= ' e.EquipmentName ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function fetchEquipmentsBookingExport(){
    $order = getOrderEquipmentBooking($order);

    $sql = "select e.EquipmentName, p.EMail, efhh.TemporalLocation, efhh.StartDate, efhh.EndDate 
        from EquipmentForHireHistory efhh 
        left join Equipment e on e.EquipmentID = efhh.EquipmentID  
        left join Personnel p on p.PersonnelID = efhh.PersonnelID 
        order by {$order}";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];
}

function fetchEquipmentsHireExport($equipmentID){

    $sql = "select e.EquipmentName, p.EMail, efhh.TemporalLocation, efhh.StartDate, efhh.EndDate 
        from EquipmentForHireHistory efhh 
        left join Equipment e on e.EquipmentID = efhh.EquipmentID  
        left join Personnel p on p.PersonnelID = efhh.PersonnelID 
        where e.EquipmentID in ({$equipmentID})";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];
}

function fetchDetailEquipmentExport($equipmentId){
    $sql = "select 
       e.EquipmentName
       , e.SN
       , e.InventoryNumber
       , e.InvoiceNumber
       , e.FundingCode
       , e.OtherFunding
       , e.AcceptanceDate
       , e.Location
    from Equipment e 
    where e.EquipmentID in ({$equipmentId})";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];
}

function fetchEquipmentAmortization($equipmentId){
    $sql = "select 
        ea.AmortizationCode 
       , ea.AmortizationYear 
       , CONCAT(REPLACE(CAST(ea.AmortizationPorcentage AS CHAR), '.', ','),'%') as Porcentage
       , ea.AmortizationAmount 
        from EquipmentAmortization ea
        left join Equipment e on e.EquipmentID = ea.EquipmentID 
        where ea.EquipmentID in ({$equipmentId})";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];
}

function fetchDetailTechnicalEquipmentExport($equipmentId){
    $sql = "select 
       e.EquipmentName
       , e.SN
       , e.InvoiceNumber 
       , e.TechnicalSpecification
       , ef.EquipmentFamilyName 
       , GROUP_CONCAT(distinct fr.FamilyResponsable separator ' ; ') as FamilyResponsable
       , el.EquipmentLaboratoryName
       , GROUP_CONCAT(distinct CONCAT(COALESCE(etc.TechnicalName,''), ' / ', COALESCE(etc.TechnicalEmail,''),' / ', COALESCE(etc.TechnicalTelephone,'')) separator ';') as TechnicalContact
       , fr2.FileName as AcceptanceName
       , CONCAT(fr2.FileName , '.', fr2.FileExtension)  as AcceptanceFile       
        from Equipment e 
        left join EquipmentTechnicalContact etc on etc.EquipmentTechnicalContactID = e.TechnicalContactID 
        left join EquipmentFamily ef on ef.FamilyID =e.FamilyID 
        left join ( SELECT efr.FamilyID, p.PersonnelID, GROUP_CONCAT(CONCAT(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') AS FamilyResponsable 
                            FROM EquipmentFamilyResponsable efr 
                            LEFT JOIN Personnel p ON efr.PersonnelID=p.PersonnelID 
                            GROUP BY efr.EquipmentFamilyResponsableID 
                            ) fr ON e.FamilyID = fr.familyID
        left join EquipmentLaboratory el on el.LaboratoryID = e.LaboratoryID 
        left join EquipmentDocumentation ed on ed.EquipmentID = e.EquipmentID 
        left join ( SELECT f.FileID, f.FileName, fe.FileExtension 
                    FROM File f 
                    left join FileExtension fe on fe.FileExtensionID = f.FilesExtensionID  
                    GROUP BY f.FileID 
                     ) fr2 ON ed.FileID = fr2.FileID 
        where e.EquipmentID in ({$equipmentId})";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];
}

function fetchEquipmentTechnical($equipmentId){
    $sql = "select 
        emh.HistoryEvent 
        , emh.HistoryDate 
        , emh.HistoryCost 
        , emh.HistoryPlace 
        , Replace(emh.TechnicalIntervention, '.-', ';') as TechnicalIntervention
    from EquipmentMaintenanceHistory emh 
    left join Equipment e on e.EquipmentID =emh.EquipmentID 
    where emh.EquipmentID in ({$equipmentId})";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];
}

function getHireEquipments($equipmentId, $offset = 0, $amount = 10, $order = null, $getIds = false){
    // $order = getOrderEquipmentBooking($order);
    $pagination = [];
    $limit = "";
    $prepareParams = [];
    $sql = "select efhh.EquipmentReservationID, e.EquipmentID, e.EquipmentName, p.EMail, efhh.TemporalLocation, efhh.StartDate, efhh.EndDate 
        from EquipmentForHireHistory efhh 
        left join Equipment e on e.EquipmentID = efhh.EquipmentID  
        left join Personnel p on p.PersonnelID = efhh.PersonnelID 
        where efhh.EquipmentID in ({$equipmentId})
        ";

    if($getIds){
      $equipmentsBookingList = [];
      $rows = igfae_intranet_fetch_data($sql, $prepareParams);
      foreach ($rows['rows'] as $row) {
        $idEquipments[] = $row->EquipmentID;
      }
      return $equipmentsBookingList;
    }

    if (isset($amount)) {
        $pagination = getPaginationInfoEquipment($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $equipmentsBookingList = [];
    foreach ($rows['rows'] as $row) {
        $equipmentBooking = new IntranetEquipmentBookingHistory();
        $equipmentBooking->setEquipmentReservationID($row->EquipmentReservationID);
        $equipmentBooking->setEquipmentName($row->EquipmentName);
        $equipmentBooking->setPersonnel($row->EMail);
        $equipmentBooking->setTemporalLocation($row->TemporalLocation);
        $equipmentBooking->setStartDate($row->StartDate);
        $equipmentBooking->setEndDate($row->EndDate);
        $equipmentsBookingList[] = $equipmentBooking;
    }

    $info = [
        'equipmentsBooking' => $equipmentsBookingList,
        'pagination' => $pagination
    ];

    return $info;   
}

function checkUserEmail($userEmail){
    $sql = "select *
        from Personnel p
        where p.EMail = '{$userEmail}'";

    $rows = igfae_intranet_fetch_data($sql, []);

    return array(
        'error'  => count($rows['rows']) === 1 ? true : false,
    );  
}

function getPersonnelByEmail($userEmail){
    $sql = "select p.PersonnelID, p.Name, p.FirstSurname, p.SecondSurname, p.Email
        from Personnel p
        where p.EMail = '{$userEmail}'";

    $rows = igfae_intranet_fetch_data($sql, []);

    return $rows['rows'];
}

function getReservationHistoryIDByReservationID($reservationID){
    $sql = "select r.ReservationHistoryID
        from EquipmentReservationHistory r
        where r.EquipmentReservationID = {$reservationID} order by r.ReservationHistoryID DESC";

    $rows = igfae_intranet_fetch_data($sql, []);

    return $rows['rows'];
}

function saveHireHistory($idEquipment, $equipmentHireHistory, $equipmentHireRemove){
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();  

    $where = array('EquipmentID' => $idEquipment);
    //Borramos las tablas relacionadas
    //igfae_intranet_delete('EquipmentForHireHistory', $where);

    $user_id = get_current_user_id();
    $igfaeUserId = getUserByWPUserID($user_id)[0]["id"];
    if (isset($idEquipment) && $idEquipment > 0) {
        foreach ($equipmentHireHistory as $elem) {

            $where = array('EquipmentReservationID' => $elem['_equipmentReservationID']);            
            igfae_intranet_delete('EquipmentForHireHistory', $where);

            $personnelID = getPersonnelByEmail($elem['_personnel'])[0]->PersonnelID;
            $data = array('EquipmentID' => $idEquipment, 'PersonnelID' => $personnelID, 'TemporalLocation' => $elem['_temporal_location'], 'StartDate' => $elem['_start_date'], 'EndDate' => $elem['_end_date'], 'CreationDate' => date('Y-m-d H:i:s'), 'CreatedBy' => $igfaeUserId);
            $reservationID = igfae_intranet_insert('EquipmentForHireHistory', $data);
            
            if($reservationID > 0){
                $where = array('EquipmentReservationID' => $elem['_equipmentReservationID']);
                $data = array('EquipmentReservationID' => $reservationID);
                igfae_intranet_update("EquipmentReservationHistory", $data, $where, null, null);
            }            

            if($elem['_equipmentReservationID'] == 0){
                saveReservation($reservationID, $idEquipment, $personnelID, $elem['_temporal_location'], $elem['_start_date'], $elem['_end_date'], $igfaeUserId, IGFAE_INTRANET_AUDIT_TYPE_ADD, NULL);
            } else if(isset($elem['_hasEdit']) && $elem['_hasEdit']){      
                $reservationHistoryID = getReservationHistoryIDByReservationID($elem['_equipmentReservationID'])[0]->ReservationHistoryID;
                saveReservation($reservationID, $idEquipment, $personnelID, $elem['_temporal_location'], $elem['_start_date'], $elem['_end_date'], $igfaeUserId, IGFAE_INTRANET_AUDIT_TYPE_MODIFY, $reservationHistoryID);
            }            
        }
        if (isset($equipmentHireRemove) && count($equipmentHireRemove) > 0) {
            foreach ($equipmentHireRemove as $elem) {
                
                $where = array('EquipmentReservationID' => $elem['_equipmentReservationID']);            
                igfae_intranet_delete('EquipmentForHireHistory', $where);

                $personnelID = getPersonnelByEmail($elem['_personnel'])[0]->PersonnelID;
                $reservationHistoryID = getReservationHistoryIDByReservationID($elem['_equipmentReservationID'])[0]->ReservationHistoryID;
                if(isset($reservationHistoryID) && $reservationHistoryID > 0){
                    saveReservation(NULL, $idEquipment, $personnelID, $elem['_temporal_location'], $elem['_start_date'], $elem['_end_date'], $igfaeUserId, IGFAE_INTRANET_AUDIT_TYPE_DELETE, $reservationHistoryID);
                }
            }
        }
    }

    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
        return true;
    } else {
        igfae_intranet_rollback_transaction();        
        return false;
    }
}

function saveReservation($reservationID, $idEquipment, $personnelID, $temporalLocation, $starDate, $endDate, $igfaeUserId, $typeAudit, $rHPrevious){
    $dataReservation = array('EquipmentReservationID' => $reservationID, 'EquipmentReservationHistoryIDPrevious' => $rHPrevious, 'EquipmentID' => $idEquipment, 'PersonnelID' => $personnelID, 'TemporalLocation' => $temporalLocation, 'StartDate' => $starDate, 'EndDate' => $endDate, 'CreationDate' => date('Y-m-d H:i:s'), 'CreatedBy' => $igfaeUserId, 'AuditTypeID' => $typeAudit);
    igfae_intranet_insert('EquipmentReservationHistory', $dataReservation);
}
