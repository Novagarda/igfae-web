<?php

function fetchPrototypes($prototype, $offset = 0, $amount = 10, $order = null, $getIds = false)
{
    $prototypes = [];
    $where = "";
    $prepareParams = [];
    if (isset($prototype)) {
        $queryPrepare = getPrepareSearchPrototype($prototype);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }
    $order = getOrderPrototype($order);

    $sql = "select 
    a.PrototypeID,
    a.StartDate,
    a.EndDate,
    a.StatusName,
    a.Name,
    a.PromotionalDescription,
    a.PublicationDate,
    group_concat(distinct pt.Name separator ' ; ') as `Type`,
    group_concat(distinct concat(personnel.Name, ' ', personnel.FirstSurname, ' ', coalesce(personnel.SecondSurname, '')) separator ' ; ') as ResearchPersonnel,
    a.Validated 
  from (
    select 
      p.PrototypeID,
      p.StartDate,
      p.EndDate,
      ps.Name as StatusName,
      COALESCE(plEn.Name, plEs.Name, plGl.Name) as Name,
      COALESCE(plEn.PromotionalDescription, plEs.PromotionalDescription, plGl.PromotionalDescription) as PromotionalDescription,
      p.PublicationDate,
      p.Validated 
    from Prototype p 
    left join PrototypeState ps
      on ps.PrototypeStateID = p.PrototypeStateID 
    left join PrototypeLanguage plEn
      on plEn.PrototypeID = p.PrototypeID 
      and plEn.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
    left join PrototypeLanguage plEs
      on plEs.PrototypeID = p.PrototypeID 
      and plEs.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
    left join PrototypeLanguage plGl
      on plGl.PrototypeID = p.PrototypeID 
      and plGl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
    left join PrototypeType pt
      on pt.PrototypeID = p.PrototypeID 
    left join PrototypePersonnel pp
      on pp.PrototypeID = p.PrototypeID 
    left join Personnel personnel
      on personnel.PersonnelID = pp.PersonnelID 
    left join PrototypeResearchArea pra
      on pra.PrototypeID = p.PrototypeID 
    left join PrototypeResearchProgram prp
      on prp.PrototypeID = p.PrototypeID 
    left join PrototypeResearchGroup prg
      on prg.PrototypeID = p.PrototypeID 
    left join PrototypeExperiment pe
      on pe.PrototypeID = p.PrototypeID 
    left join PrototypeWorkline pwl
      on pwl.PrototypeID = p.PrototypeID 
    WHERE     
        {$where} 
    group by p.PrototypeID 
  ) a
  left join PrototypeType pt
    on pt.PrototypeID = a.PrototypeID 
  left join PrototypePersonnel pp
    on pp.PrototypeID = a.PrototypeID 
  left join Personnel personnel
    on personnel.PersonnelID = pp.PersonnelID 
  group by a.PrototypeID 
  ORDER BY {$order}";

    if($getIds){
      $idPrototypes = [];
      $rows = igfae_intranet_fetch_data($sql, $prepareParams);
      foreach ($rows['rows'] as $row) {
        $idPrototypes[] = $row->PrototypeID;
      }
      return $idPrototypes;
    }

    $pagination = getPaginationInfoPrototype($offset, $amount, $sql, $prepareParams);

    $sql .= " LIMIT {$offset}, {$amount}";
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);
    foreach ($rows['rows'] as $row) {

        $prototypes[] = new IntranetPrototype(
          $row->PrototypeID,
          $row->StartDate,
          $row->EndDate,
          $row->StatusName,
          $row->Name,
          NULL,
          NULL,
          NULL,
          $row->PublicationDate,
          $row->Type,
          $row->ResearchPersonnel,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          $row->Validated,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          $row->PromotionalDescription,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL,
          NULL
        );
    }

    $info = [
        'prototypes' => $prototypes,
        'pagination' => $pagination
    ];

    return $info;
}

function getPrepareSearchPrototype($prototype)
{
    $where = "1=1 ";
    $params = [];

    $existIGFAEResearchAndIPCoordinator = false;

    if($prototype->getIGFAEResearchers() && $prototype->getIPCoordinator()){
      $existIGFAEResearchAndIPCoordinator = true;
    }

    // Id
    if ($prototype->getId()) {
        $where .= " AND (p.PrototypeID  = %d)";
        $params[] = $prototype->getId();
    }
    
    // Start Date and End Date
    if ($prototype->getStartDate() && $prototype->getEndDate()) {
      $startDates =  explode("-", $prototype->getStartDate());
      $startDate1 = explode("/", trim($startDates[0]));
      $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
      $fecha1 = implode("-", $fechaFormateada1);
      $startDate2 = explode("/", trim($startDates[1]));
      $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
      $fecha2 = implode("-", $fechaFormateada2);
      $where .= " AND ( (p.StartDate BETWEEN %s and %s) OR p.StartDate <= %s)";
      $params[] = $fecha1;
      $params[] = $fecha2;
      $params[] = $fecha1;

      //Fecha final
      $endDates =  explode("-", $prototype->getEndDate());
      $endDate1 = explode("/", trim($endDates[0]));
      $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
      $fecha1 = implode("-", $fechaFormateada1);
      $endDate2 = explode("/", trim($endDates[1]));
      $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
      $fecha2 = implode("-", $fechaFormateada2);
      $where .= " AND ( (p.EndDate BETWEEN %s and %s) OR p.EndDate >= %s OR p.EndDate is null)";
      $params[] = $fecha1;
      $params[] = $fecha2;
      $params[] = $fecha2;
      
    }  else if ($prototype->getStartDate()) {
        $startDates =  explode("-", $prototype->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( p.StartDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    } else if ($prototype->getEndDate()){
        $endDates =  explode("-", $prototype->getEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( p.EndDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    }

      // State select multiple
    if ($prototype->getState()) {
        $statesCount = count($prototype->getState());
        $paramsPlaceholders = array_fill(0, $statesCount, '%d');
        $placeholdersState = implode(',', $paramsPlaceholders);
        $where .= " AND (p.PrototypeStateID IN ({$placeholdersState}))";
        $params = array_merge($params, $prototype->getState());
    }

    // Name input
    if ($prototype->getName()) {
        $where .= " AND (plEn.Name like %s or plEs.Name like %s or plGl.Name like %s)";
        $params[] = '%' . $prototype->getName() . '%';
        $params[] = '%' . $prototype->getName() . '%';
        $params[] = '%' . $prototype->getName() . '%';
    }

    // Publication Date
    if ($prototype->getPublicationDate()) {
      $publicationDates =  explode("-", $prototype->getPublicationDate());
      $publicationDate1 = explode("/", trim($publicationDates[0]));
      $fechaFormateada1 = array($publicationDate1[2], $publicationDate1[0], $publicationDate1[1]);
      $fecha1 = implode("-", $fechaFormateada1);
      $publicationDate2 = explode("/", trim($publicationDates[1]));
      $fechaFormateada2 = array($publicationDate2[2], $publicationDate2[0], $publicationDate2[1]);
      $fecha2 = implode("-", $fechaFormateada2);
      $where .= " AND (p.PublicationDate BETWEEN %s and %s)";
      $params[] = $fecha1;
      $params[] = $fecha2;
    }

    // Type input
    if ($prototype->getType()) {
      $where .= " AND ( pt.Name like %s)";
      $params[] = '%' . $prototype->getType() . '%';
    }
    if($existIGFAEResearchAndIPCoordinator){
      $typesCountIGFAEResearchers = count($prototype->getIGFAEResearchers());
      $paramsPlaceholdersIGFAEResearchers = array_fill(0, $typesCountIGFAEResearchers, '%d');
      $placeholdersIGFAEResearchers = implode(',', $paramsPlaceholdersIGFAEResearchers);

      $typesCountIPCoordinator = count($prototype->getIPCoordinator());
      $paramsPlaceholdersIPCoordinator = array_fill(0, $typesCountIPCoordinator, '%d');
      $placeholdersIPCoordinator = implode(',', $paramsPlaceholdersIPCoordinator);

      $where .= " AND (pp.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IG . " and personnel.PersonnelID IN ({$placeholdersIGFAEResearchers}) OR pp.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . " and personnel.PersonnelID IN ({$placeholdersIPCoordinator}))";
      $params = array_merge($params, $prototype->getIGFAEResearchers());
      $params = array_merge($params, $prototype->getIPCoordinator());

    } else {
        // IGFAEResearchers select multiple
        if ($prototype->getIGFAEResearchers()) {
          $typesCount = count($prototype->getIGFAEResearchers());
          $paramsPlaceholders = array_fill(0, $typesCount, '%d');
          $placeholdersIGFAEResearchers = implode(',', $paramsPlaceholders);
          $where .= " AND (pp.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IG . " and personnel.PersonnelID IN ({$placeholdersIGFAEResearchers}))";
          $params = array_merge($params, $prototype->getIGFAEResearchers());
        }
        
        // IPCoordinator select multiple
        if ($prototype->getIPCoordinator()) {
          $typesCount = count($prototype->getIPCoordinator());
          $paramsPlaceholders = array_fill(0, $typesCount, '%d');
          $placeholdersIPCoordinator = implode(',', $paramsPlaceholders);
          $where .= " AND (pp.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . " and personnel.PersonnelID IN ({$placeholdersIPCoordinator}))";
          $params = array_merge($params, $prototype->getIPCoordinator());
        }  
    }
    // ResearchProgram select multiple
    if ($prototype->getResearchProgram()) {
      $typesCount = count($prototype->getResearchProgram());
      $paramsPlaceholders = array_fill(0, $typesCount, '%d');
      $placeholdersResearchProgram = implode(',', $paramsPlaceholders);
      $where .= " AND (prp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
      $params = array_merge($params, $prototype->getResearchProgram());
    }

    // ResearchArea select multiple
    if ($prototype->getResearchArea()) {
      $typesCount = count($prototype->getResearchArea());
      $paramsPlaceholders = array_fill(0, $typesCount, '%d');
      $placeholdersResearchArea = implode(',', $paramsPlaceholders);
      $where .= " AND (pra.ResearchAreaID IN ({$placeholdersResearchArea}))";
      $params = array_merge($params, $prototype->getResearchArea());
    }

    // ExperimentalCollaboration select multiple
    if ($prototype->getExperimentalCollaboration()) {
      $typesCount = count($prototype->getExperimentalCollaboration());
      $paramsPlaceholders = array_fill(0, $typesCount, '%d');
      $placeholdersExperimentalCollaboration = implode(',', $paramsPlaceholders);
      $where .= " AND (pe.ExperimentalCollaborationID IN ({$placeholdersExperimentalCollaboration}))";
      $params = array_merge($params, $prototype->getExperimentalCollaboration());
    }

    // ResearchGroup select multiple
    if ($prototype->getResearchGroup()) {
      $typesCount = count($prototype->getResearchGroup());
      $paramsPlaceholders = array_fill(0, $typesCount, '%d');
      $placeholdersResearchGroup = implode(',', $paramsPlaceholders);
      $where .= " AND (prg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
      $params = array_merge($params, $prototype->getResearchGroup());
    }

    // Workline select multiple
    if ($prototype->getWorkLine()) {
      $typesCount = count($prototype->getWorkLine());
      $paramsPlaceholders = array_fill(0, $typesCount, '%d');
      $placeholdersWorkLine = implode(',', $paramsPlaceholders);
      $where .= " AND (pwl.WorkLineID IN ({$placeholdersWorkLine}))";
      $params = array_merge($params, $prototype->getWorkLine());
    }

    // Validated
    if ($prototype->getValidated() != null) {
      $where .= " AND (p.Validated  =  %d)";
      $params[] = $prototype->getValidated();
    }

    //permisos
    $user_id = get_current_user_id();

    if  (
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_GESTION) && 
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL) && 
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_COLABORADOR) && 
        (
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR)
        )
    ) {
    
        $igfaeUserId = getUserByWPUserID($user_id);
        if (!empty($igfaeUserId)) {
            $igfae_user_id = $igfaeUserId[0]["id"];
            $where .= " AND (pp.PersonnelID = %d or p.CreatedBy = %d)";
            $params[] =  $igfae_user_id;
            $params[] =  $igfae_user_id;
        }
    }       
    
    return array(
        "where" => $where,
        "params" => $params
    );
}

function getOrderPrototype($order)
{
    if (!isset($order)) {
        return ' StartDate DESC';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: //START DATE
                $sort .= 'StartDate ';
                break;
            case 1:  //END DATE
                $sort .= 'EndDate ';
                break;
            case 2: //STATE
                $sort .= 'StatusName ';
                break;
            case 3: //NAME
                $sort .= 'Name ';
                break;
            case 4: //DESCRIPTION
                $sort .= 'PromotionalDescription ';
                break;
            case 5: //PUBLICATION DATE
                $sort .= 'PublicationDate ';
                break;
            case 6: //TYPE
                $sort .= 'Type ';
                break;
            case 7: //IGFAE RESEARCHERS
                $sort .= 'ResearchPersonnel ';
                break;
            case 8: //VALIDATED
                $sort .= 'Validated ';
                break;
            default:
                $sort .= 'StartDate ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function getPaginationInfoPrototype($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];


    return $result;
}

// Delete prototype
function deletePrototype($id)
{
  igfae_intranet_begin_transaction();

  // Auditoria
  saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_PROTOTYPE, $id);

  deletePrototypeRelationships($id);

  //Prototype
  $sql = "DELETE FROM Prototype
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  if (igfae_intranet_is_transaction_ok()) {
    igfae_intranet_commit_transaction();
  } else {
      igfae_intranet_rollback_transaction();
  }

}

function deletePrototypeRelationships($id){

  //PrototypeLanguage
  $sql = "DELETE FROM PrototypeLanguage
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeResearchGroup
  $sql = "DELETE FROM PrototypeResearchGroup
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);
  
  //PrototypeResearchProgram
  $sql = "DELETE FROM PrototypeResearchProgram
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeResearchArea
  $sql = "DELETE FROM PrototypeResearchArea
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeWorkline
  $sql = "DELETE FROM PrototypeWorkline
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeExperiment
  $sql = "DELETE FROM PrototypeExperiment
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypePersonnel
  $sql = "DELETE FROM PrototypePersonnel
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeDeliverable
  $sql = "DELETE FROM PrototypeDeliverable
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //OtherResearchLinked
  $sql = "DELETE FROM OtherResearchLinked
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeProject
  $sql = "DELETE FROM PrototypeProject
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeType
  $sql = "DELETE FROM PrototypeType
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

  //PrototypeOrganizationLinked
  $sql = "DELETE FROM PrototypeOrganizationLinked
  WHERE PrototypeID = '$id'";
  igfae_intranet_save($sql);

}

function editPrototype($prototype){
  $ids = [$prototype->getId()];

  igfae_intranet_begin_transaction();

  $prototypeOldInfo = getPrototypeById($prototype->getId());

  // Auditoria
  saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_PROTOTYPE, $prototype->getId());

  updatePrototype($prototype);

  deletePrototypeRelationships($prototype->getId());

  $ids[] = savePrototypeRelationships($prototype);

  $photoError = false;
  $oldPrototypeId = $prototype->getId();

  
  $photo = null;

  if (!empty($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
      $oldFileId = $prototypeOldInfo->getFileId();
      if(isset($oldFileId)){
        igfae_intranet_delete_file(get_home_path().$prototypeOldInfo->getFile());
      }
      
      $filename = IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PREFIX . $oldPrototypeId;
      $photo = igfae_intranet_create_file($_FILES['photo'], IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PATH . $filename);
      $fileId = null;
      if (!empty($photo)) {
          $fileId = saveFile($photo, IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PATH_ID);
          $sql = "UPDATE Prototype
          SET
          FileID = $fileId
          WHERE PrototypeID = $oldPrototypeId";

          igfae_intranet_save($sql);

          if(isset($oldFileId)){
            deleteFileFromBBDD($oldFileId);
          }

      } else {
          $photoError = true;
      }
  } else if ($prototype->getDeleteImage() != null && $prototype->getDeleteImage() == "true" && $prototypeOldInfo->getFile() != null && $prototypeOldInfo->getFile() != "") {
      // Sino viene fichero de entrada pero viene activado el flag de borrado => Borramos la foto existente del prototipo
      igfae_intranet_update("Prototype", array('FileID' => NULL), array('PrototypeID' => $oldPrototypeId), null, null);
      igfae_intranet_delete_file(get_home_path() . $prototypeOldInfo->getFile());
      deleteFileFromBBDD($prototypeOldInfo->getFileId());
  }

  if (igfae_intranet_is_transaction_ok() && !$photoError) {
    igfae_intranet_commit_transaction();
  } else {
      igfae_intranet_rollback_transaction();
      if (!empty($photo)) {
          igfae_intranet_delete_file($photo);
      }
  }

  return [$prototype->getId()];
}

function updatePrototype($prototype){

  $startDateFormatted = "'".$prototype->getStartDate()."'";
  $endDate = empty($prototype->getEndDate()) ? "NULL" : "'".$prototype->getEndDate()."'" ;
  $slug = empty($prototype->getSlug()) ? "NULL" :  "'".$prototype->getSlug()."'" ;
  $publicationDate = empty($prototype->getPublicationDate())? "NULL" : "'".$prototype->getPublicationDate()."'" ;
  $website = empty($prototype->getWebsite()) ? "NULL" : "'".$prototype->getWebsite()."'" ;
  $access = empty($prototype->getAccess()) ? "NULL" : "'".$prototype->getAccess()."'" ;
  $documentationAccess = empty($prototype->getDocumentationAccess()) ? "NULL" : "'".$prototype->getDocumentationAccess()."'" ;
  $prototypeStateID = empty($prototype->getState()) ? "NULL" : $prototype->getState();
  $comments = empty($prototype->getComments()) ? "NULL" : "'".$prototype->getComments()."'" ;
  $validated = (empty($prototype->getValidated()) ? 0 : $prototype->getValidated());
  $validatedBy = (empty($prototype->getValidatedBy()) ? "NULL" : "'".$prototype->getValidatedBy()."'");
  $validatedOn = (empty($prototype->getValidatedOn()) ? "NULL" : "'".$prototype->getValidatedOn()."'");
  $prototypeId = $prototype->getId();


  //Query de updatePrototype
  $sql = "UPDATE Prototype
    SET
    StartDate = $startDateFormatted, EndDate = $endDate
    , Slug = $slug
    , PublicationDate = $publicationDate
    , Website = $website
    , Access = $access
    , DocumentationAccess = $documentationAccess
    , PrototypeStateID = $prototypeStateID
    , Comments = $comments
    , Validated = $validated
    , ValidatedBy = $validatedBy
    , ValidatedOn = $validatedOn
    WHERE PrototypeID = $prototypeId";

  igfae_intranet_save($sql);

  if (!empty($prototype->getValidated())) {
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PROTOTYPE, $prototypeId);
  }

   // Authorization
   $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_PROTOTYPE, $prototypeId);
   if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
       closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_PROTOTYPE, $prototypeId);
   }    
}

function saveNewPrototype($prototype){
    global $current_user;
    $ids = [];
    $table = "Prototype";
    $data = array(
      'StartDate' => $prototype->getStartDate(),
      'EndDate' => $prototype->getEndDate(),
      'Slug' => $prototype->getSlug(),
      'PublicationDate' => $prototype->getPublicationDate(),
      'Website' => $prototype->getWebsite(),
      'Access' => $prototype->getAccess(),
      'DocumentationAccess' => $prototype->getDocumentationAccess(),
      'PrototypeStateID' => empty($prototype->getState()) ? NULL : $prototype->getState(),
      'Comments' => $prototype->getComments()   ,
      'CreatedBy' =>  getUserByWPUserID($current_user->ID)[0]["id"]
    );
  
    if($prototype->getValidated() != NULL){
      $data['Validated'] = $prototype->getValidated();
      $data['ValidatedBy'] = $prototype->getValidatedBy();
      $data['ValidatedOn'] = $prototype->getValidatedOn();
    }

    igfae_intranet_begin_transaction();
  
    $prototypeID = saveDataUnique($table, $data);
    $ids[] = $prototypeID;

    $prototype->setIdPrototype($prototypeID);
  
    $ids[] = savePrototypeRelationships($prototype);

    $photoError = false;
    //Alta imagen
    $photo = null;
    if (!empty($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
        $filename = IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PREFIX . $prototypeID;
        $photo = igfae_intranet_create_file($_FILES['photo'], IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PATH . $filename);
        $fileId = null;
        if (!empty($photo)) {
            $fileId = saveFile($photo, IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PATH_ID);

            $sql = "UPDATE Prototype
            SET
            FileID = $fileId
            WHERE PrototypeID = $prototypeID";

            igfae_intranet_save($sql);
        } else {
            $photoError = true;
        }
    }
    
    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_PROTOTYPE, $prototypeID);
    if($prototype->getValidated() != NULL){
      saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PROTOTYPE, $prototypeID);
    }

    if (igfae_intranet_is_transaction_ok() && !$photoError) {
      igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
        if (!empty($photo)) {
            igfae_intranet_delete_file($photo);
        }
    }
    
    //Cuando es un alta se espera que se devuelva el ID del prototipo
    return $prototypeID;
}

function savePrototypeRelationships($prototype){
  $ids = [];
  $prototypeID = $prototype->getId();

  if($prototype->getOrganizationsLinked() != NULL){
  
    $dataList = array_map(function($organizationLinked) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'Name' => $organizationLinked
      );
    },$prototype->getOrganizationsLinked());

    $ids[] = saveDataList("PrototypeOrganizationLinked", $dataList);
  }

  if($prototype->getType() != NULL){

    $dataList = array_map(function($prototypeType) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'Name' => $prototypeType
      );
    },$prototype->getType());

    $ids[] = saveDataList("PrototypeType", $dataList);
  }

  if($prototype->getProjects() != NULL){

    $dataList = array_map(function($prototypeProject) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'Name' => $prototypeProject
      );
    },$prototype->getProjects());

    $ids[] = saveDataList("PrototypeProject", $dataList);
  }

  if($prototype->getDeliverables() != NULL){

    $dataList = array_map(function($prototypeDeliverable) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'Name' => $prototypeDeliverable
      );
    },$prototype->getDeliverables());

    $ids[] = saveDataList("PrototypeDeliverable", $dataList);
  }

  if($prototype->getIGFAEResearchers() != NULL || $prototype->getIPCoordinator() != NULL || $prototype->getOtherResearchers() != NULL){
    $ids[] = savePrototypePersonnel($prototypeID, $prototype->getIGFAEResearchers(), $prototype->getIPCoordinator(), $prototype->getOtherResearchers());
  }

  if($prototype->getExperimentalCollaboration() != NULL){

    $data = array(
      'PrototypeID' => $prototypeID,
      'ExperimentalCollaborationID' => $prototype->getExperimentalCollaboration(),
    );

    $ids[] = saveDataUnique("PrototypeExperiment", $data);
  }

  if($prototype->getWorkLine() != NULL){

    $dataList = array_map(function($prototypeWorkline) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'WorklineID' => $prototypeWorkline
      );
    },$prototype->getWorkLine());

    $ids[] = saveDataList("PrototypeWorkline", $dataList);
  }

  if($prototype->getResearchArea() != NULL){

    $dataList = array_map(function($ResearchArea) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'ResearchAreaID' => $ResearchArea
      );
    },$prototype->getResearchArea());

    $ids[] = saveDataList("PrototypeResearchArea", $dataList);
  }
  
  if($prototype->getResearchProgram() != NULL){

    $dataList = array_map(function($ResearchProgram) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'ResearchProgramID' => $ResearchProgram
      );
    },$prototype->getResearchProgram());

    $ids[] = saveDataList("PrototypeResearchProgram", $dataList);
  }

  if($prototype->getResearchGroup() != NULL){

    $dataList = array_map(function($ResearchGroup) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'ResearchGroupID' => $ResearchGroup
      );
    },$prototype->getResearchGroup());

    $ids[] = saveDataList("PrototypeResearchGroup", $dataList);
  }

  $ids[] = savePrototypeLanguage($prototypeID, $prototype->getNameES(), $prototype->getNameEN(), $prototype->getNameGL(), $prototype->getDescriptionES(), $prototype->getDescriptionEN(), $prototype->getDescriptionGL(), $prototype->getPromotionalDescriptionES(), $prototype->getPromotionalDescriptionEN(), $prototype->getPromotionalDescriptionGL());


  return $ids;
}

function saveDataUnique($table, $data){
  return saveDataList($table, [$data])[0];
}

function saveDataList($table, $dataList){
  $ids = [];
  foreach($dataList as $data){
    $ids[] = igfae_intranet_insert($table, $data, null);
  }
  return $ids;
}

function savePrototypePersonnel($prototypeID, $IGFAEResearchers, $iPCoordinatorID, $otherResearchers){
  $ids = [];
  $table = "PrototypePersonnel";
  if($IGFAEResearchers != NULL){

    $dataList = array_map(function($id) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'PersonnelID' => $id,
        'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_IG
      );
    },$IGFAEResearchers);

    $ids[] = saveDataList($table, $dataList);
  }

  if($iPCoordinatorID != NULL){
    
    $data = array(
      'PrototypeID' => $prototypeID,
      'PersonnelID' => $iPCoordinatorID,
      'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_IPCP   
    );

    $ids[] = saveDataUnique($table, $data);
  }

  if($otherResearchers != NULL){

    $dataList = array_map(function($id) use ($prototypeID){
      return array(
        'PrototypeID' => $prototypeID,
        'PersonnelID' => $id,
        'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_IV
      );
    },$otherResearchers);

    $ids[] = saveDataList($table, $dataList);
  }

  return $ids;
}

function savePrototypeLanguage($prototypeID, $nameES, $nameEN, $nameGL, $descriptionES, $descriptionEN, $descriptionGL, $promotionalDescriptionES, $promotionalDescriptionEN, $promotionalDescriptionGL){
  $ids = [];
  $table = "PrototypeLanguage";

  if($nameES != NULL || $descriptionES != NULL || $promotionalDescriptionES != NULL){
    $data = array(
      'PrototypeID' => $prototypeID,
      'LanguageID' => IGFAE_INTRANET_LANGUAGE_ES,
    );

    if($nameES != NULL){
      $data['Name'] = $nameES;
    }
    if($descriptionES != NULL){
      $data['Description'] = $descriptionES;
    }
    if($promotionalDescriptionES != NULL){
      $data['PromotionalDescription'] = $promotionalDescriptionES;
    }

    $ids[] = saveDataUnique($table, $data);
  }

  if($nameEN != NULL || $descriptionEN != NULL || $promotionalDescriptionEN != NULL){
    $data = array(
      'PrototypeID' => $prototypeID,
      'LanguageID' => IGFAE_INTRANET_LANGUAGE_EN,
    );

    if($nameEN != NULL){
      $data['Name'] = $nameEN;
    }
    if($descriptionEN != NULL){
      $data['Description'] = $descriptionEN;
    }
    if($promotionalDescriptionEN != NULL){
      $data['PromotionalDescription'] = $promotionalDescriptionEN;
    }

    $ids[] = saveDataUnique($table, $data);
  }

  if($nameGL != NULL || $descriptionGL != NULL || $promotionalDescriptionGL != NULL){

    $data = array(
      'PrototypeID' => $prototypeID,
      'LanguageID' => IGFAE_INTRANET_LANGUAGE_GL,
    );

    if($nameGL != NULL){
      $data['Name'] = $nameGL;
    }
    if($descriptionGL != NULL){
      $data['Description'] = $descriptionGL;
    }
    if($promotionalDescriptionGL != NULL){
      $data['PromotionalDescription'] = $promotionalDescriptionGL;
    }

    $ids[] = saveDataUnique($table, $data);
  }

  return $ids;
}

function getPrototypeById($prototypeId){
  $prototype = NULL;
  $sql = getByIdSelect($prototypeId);

  $rows = igfae_intranet_fetch_data($sql, [$prototypeId]);

  foreach ($rows['rows'] as $row) {

    $types = extractIdsNamesFromRowByParametersName($row, "TypeIDs", "Type");
    $igfaeResearchers = extractIdsNamesFromRowByParametersName($row, "IgfaeResearchersIDs", "IgfaeResearchers");
    $researchPrograms = extractIdsNamesFromRowByParametersName($row, "ResearchProgramsIDs", "ResearchPrograms");
    $researchGroups = extractIdsNamesFromRowByParametersName($row, "ResearchGroupsIDs", "ResearchGroups");
    $researchAreas = extractIdsNamesFromRowByParametersName($row, "ResearchAreasIDs", "ResearchAreas");
    $workLines = extractIdsNamesFromRowByParametersName($row, "WorkLinesIDs", "WorkLines");
    $ipCoordinator = extractIdNameFromRowByParameterName($row, "PrincipalResearchersIDs", "PrincipalResearchers");
    $otherResearchers = extractIdsNamesFromRowByParametersName($row, "OtherResearchersIDs", "OtherResearchers");
    $deliverables = extractIdsNamesFromRowByParametersName($row, "DeliverablesIDs", "Deliverables");
    $projects = extractIdsNamesFromRowByParametersName($row, "ProjectsIDs", "Projects");
    $organizationsLinked = extractIdsNamesFromRowByParametersName($row, "OrganizationLinkedIDs", "OrganizationLinked");
    $experimentalCollaboration = extractIdNameFromRowByParameterName($row, "ExperimentsIDs", "Experiments") ;
    $state = extractIdNameFromRowByParameterName($row, "StatusID", "StatusName") ;
    $fileId = $row->FileID;
    $file = NULL;
    if($fileId){
      $file = getPathToFile($fileId);
    }

    $prototype = new IntranetPrototype(
        $row->PrototypeID,
        $row->StartDate,
        $row->EndDate,
        $state,
        NULL,
        $row->NameEs,
        $row->NameEn,
        $row->NameGl,
        $row->PublicationDate,
        $types,
        $igfaeResearchers,
        $researchPrograms,
        $researchGroups,
        $researchAreas,
        $experimentalCollaboration,
        $workLines,
        $ipCoordinator,
        $row->Validated,
        $row->ValidatedBy,
        $row->ValidatedOn,
        NULL,
        $row->DescriptionEs,
        $row->DescriptionEn,
        $row->DescriptionGl,
        NULL,
        $row->PromotionalDescriptionEs,
        $row->PromotionalDescriptionEn,
        $row->PromotionalDescriptionGl,
        $row->Slug,
        $row->Website,
        $row->Access,
        $row->DocumentationAccess,
        $file,
        $fileId,
        $otherResearchers,
        $deliverables,
        $projects,
        $organizationsLinked,
        $row->Comments
    );
  }
  return $prototype;
}

function getByIdSelect($prototypeId){
  $where = "";

  if (isset($prototypeId)) {
      $where = "WHERE p.PrototypeID  = %d";
  }

  $sql = "SELECT 
    p.PrototypeID,
    p.StartDate,
    p.EndDate,
    group_concat(distinct pd.PrototypeDeliverableID separator ' ; ') as DeliverablesIDs,
    group_concat(distinct pd.Name separator ' ; ') as Deliverables,
    ps.PrototypeStateID as StatusID,
    ps.Name as StatusName,
    plEn.Name as NameEn,
    plEs.Name as NameEs,
    plGl.Name as NameGl,
    p.Slug,
    plEn.Description as DescriptionEn, 
    plEs.Description as DescriptionEs,
    plGl.Description as DescriptionGl,
    plEn.PromotionalDescription as PromotionalDescriptionEn,
    plEs.PromotionalDescription as PromotionalDescriptionEs,
    plGl.PromotionalDescription as PromotionalDescriptionGl,
    p.PublicationDate,
    group_concat(distinct pt.PrototypeTypeID  separator ' ; ') as TypeIDs,
    group_concat(distinct pt.Name separator ' ; ') as `Type`,
    p.Website,
    p.Access,
    p.FileID,
    p.DocumentationAccess,
    group_concat(distinct concat(personnelInv.PersonnelID) separator ' ; ') as IgfaeResearchersIDs,
    group_concat(distinct concat(personnelInv.Name, ' ', personnelInv.FirstSurname, ' ', coalesce(personnelInv.SecondSurname, '')) separator ' ; ') as IgfaeResearchers,
    group_concat(distinct concat(personnelInvOther.PersonnelID) separator ' ; ') as OtherResearchersIDs,
    group_concat(distinct concat(personnelInvOther.Name, ' ', personnelInvOther.FirstSurname, ' ', coalesce(personnelInvOther.SecondSurname, '')) separator ' ; ') as OtherResearchers,
    group_concat(distinct concat(pp.PrototypeProjectID ) separator ' ; ') as ProjectsIDs,
    group_concat(distinct concat(pp.Name ) separator ' ; ') as Projects,
    group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
    group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
    group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
    group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
    group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupsIDs,
    group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
    group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
    group_concat(distinct ec.Name separator ' ; ') as Experiments,
    group_concat(distinct wl.WorkLineID separator ' ; ') as WorkLinesIDs,
    group_concat(distinct wl.Name separator ' ; ') as WorkLines,
    group_concat(distinct concat(pol.PrototypeOrganizationLinkedID ) separator ' ; ') as OrganizationLinkedIDs,
    group_concat(distinct concat(pol.Name ) separator ' ; ') as OrganizationLinked,
    group_concat(distinct concat(personnelInvPrincipal.PersonnelID) separator ' ; ') as PrincipalResearchersIDs,
    group_concat(distinct concat(personnelInvPrincipal.Name, ' ', personnelInvPrincipal.FirstSurname, ' ', coalesce(personnelInvPrincipal.SecondSurname, '')) separator ' ; ') as PrincipalResearchers,
    p.Comments,
    p.Validated,
    p.ValidatedBy,
    p.ValidatedOn  
  from Prototype p 
  left join PrototypeState ps
    on ps.PrototypeStateID = p.PrototypeStateID 
  left join PrototypeLanguage plEn
    on plEn.PrototypeID = p.PrototypeID 
    and plEn.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
  left join PrototypeLanguage plEs
    on plEs.PrototypeID = p.PrototypeID 
    and plEs.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
  left join PrototypeLanguage plGl
    on plGl.PrototypeID = p.PrototypeID 
    and plGl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
  left join PrototypeType pt
    on pt.PrototypeID = p.PrototypeID 
  left join PrototypePersonnel ppInv
    on ppInv.PrototypeID = p.PrototypeID 
    and ppInv.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IG."
  left join Personnel personnelInv
    on personnelInv.PersonnelID = ppInv.PersonnelID 
  left join PrototypePersonnel ppInvOther
    on ppInvOther.PrototypeID = p.PrototypeID 
    and ppInvOther.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IV."
  left join Personnel personnelInvOther
    on personnelInvOther.PersonnelID = ppInvOther.PersonnelID
  left join PrototypePersonnel ppInvPrincipal
    on ppInvPrincipal.PrototypeID = p.PrototypeID 
    and ppInvPrincipal.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IPCP."
  left join Personnel personnelInvPrincipal
    on ppInvPrincipal.PersonnelID = personnelInvPrincipal.PersonnelID
  left join PrototypeResearchArea pra
    on pra.PrototypeID = p.PrototypeID
  left join ResearchArea ra
    on ra.ResearchAreaID = pra.ResearchAreaID 
  left join PrototypeResearchProgram prp
    on prp.PrototypeID = p.PrototypeID 
  left join ResearchProgram rp
    on rp.ResearchProgramID = prp.ResearchProgramID 
  left join PrototypeResearchGroup prg
    on prg.PrototypeID = p.PrototypeID 
  left join ResearchGroup rg
    on rg.ResearchGroupID = prg.ResearchGroupID 
  left join PrototypeExperiment pe
    on pe.PrototypeID = p.PrototypeID 
  left join ExperimentalCollaboration ec
    on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
  left join PrototypeWorkline pwl
    on pwl.PrototypeID = p.PrototypeID 
  left join WorkLine wl
    on wl.WorkLineID = pwl.WorkLineID 
  left join PrototypeDeliverable pd
    on pd.PrototypeID = p.PrototypeID 
  left join PrototypeProject pp
    on pp.PrototypeID = p.PrototypeID 
  left join PrototypeOrganizationLinked pol
    on pol.PrototypeID = p.PrototypeID 
  {$where}
  GROUP by p.PrototypeID;";

  return $sql;
}

function extractIdsNamesFromRowByParametersName($row, $idsProperty, $NameProperty){

  $result = [];
  $bdIds = explode(";", $row->$idsProperty);
  $bdNames = explode(";", $row->$NameProperty);

  foreach ( $bdIds as $key => $item ) {
    if(!empty($item)){
      $result[trim($item)] = $bdNames[$key];
    }
  }

  return $result;
}

function extractIdNameFromRowByParameterName($row, $idProperty, $NameProperty){

  $result = null;

  if(!empty($row->$idProperty) && !empty($row->$NameProperty)){
    $result=$row->$idProperty . ";" .$row->$NameProperty;
  }

  return $result;
}

function existsPrototype($prototypeId)
{
    $where = "";
    $params = [];

    $where .= " AND p.PrototypeID = %d";
    $params[] = $prototypeId;

    $sql = "SELECT distinct
	  p.PrototypeID as 'id'
    FROM Prototype p 
        WHERE 1=1 {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

function fetchPrototypesExport($idPrototypes){
  $prototypes = [];
  $idsPrototypesStr = "";
  $idsPrototypesStr = implode(',', $idPrototypes);

  $sql ="select 
      p.PrototypeID as ID,
      p.StartDate,
      p.EndDate,
      group_concat(distinct pd.Name separator ' ; ') as Deliverables,
      ps.Name as StatusName,
      plEn.Name as NameEn,
      plEs.Name as NameEs,
      plGl.Name as NameGl,
      p.Slug,
      plEn.Description as DescriptionEn, 
      plEs.Description as DescriptionEs,
      plGl.Description as DescriptionGl,
      plEn.PromotionalDescription as PromotionalDescriptionEn,
      plEs.PromotionalDescription as PromotionalDescriptionEs,
      plGl.PromotionalDescription as PromotionalDescriptionGl,
      p.PublicationDate,
      group_concat(distinct pt.Name separator ' ; ') as `Type`,
      p.Website,
      p.Access,
      p.FileID,
      p.DocumentationAccess,
      group_concat(distinct concat(personnelInv.Name, ' ', personnelInv.FirstSurname, ' ', coalesce(personnelInv.SecondSurname, '')) separator ' ; ') as IgfaeResearchers,
      group_concat(distinct concat(personnelInvOther.Name, ' ', personnelInvOther.FirstSurname, ' ', coalesce(personnelInvOther.SecondSurname, '')) separator ' ; ') as OtherResearchers,
      group_concat(distinct concat(pp.Name ) separator ' ; ') as Projects,
      group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
      group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
      group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
      group_concat(distinct ec.Name separator ' ; ') as Experiments,
      group_concat(distinct wl.Name separator ' ; ') as WorkLines,
      group_concat(distinct concat(pol.Name ) separator ' ; ') as OrganizationLinked,
      group_concat(distinct concat(personnelInvPrincipal.Name, ' ', personnelInvPrincipal.FirstSurname, ' ', coalesce(personnelInvPrincipal.SecondSurname, '')) separator ' ; ') as PrincipalResearchers,
      p.Comments,
      p.Validated,
      p.ValidatedBy,
      p.ValidatedOn  
    from Prototype p 
    left join PrototypeState ps
      on ps.PrototypeStateID = p.PrototypeStateID 
    left join PrototypeLanguage plEn
      on plEn.PrototypeID = p.PrototypeID 
      and plEn.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
    left join PrototypeLanguage plEs
      on plEs.PrototypeID = p.PrototypeID 
      and plEs.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
    left join PrototypeLanguage plGl
      on plGl.PrototypeID = p.PrototypeID 
      and plGl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
    left join PrototypeType pt
      on pt.PrototypeID = p.PrototypeID 
    left join PrototypePersonnel ppInv
      on ppInv.PrototypeID = p.PrototypeID 
      and ppInv.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IG."
    left join Personnel personnelInv
      on personnelInv.PersonnelID = ppInv.PersonnelID 
    left join PrototypePersonnel ppInvOther
      on ppInvOther.PrototypeID = p.PrototypeID 
      and ppInvOther.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IV."
    left join Personnel personnelInvOther
      on personnelInvOther.PersonnelID = ppInvOther.PersonnelID
    left join PrototypePersonnel ppInvPrincipal
      on ppInvPrincipal.PrototypeID = p.PrototypeID 
      and ppInvPrincipal.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IPCP."
    left join Personnel personnelInvPrincipal
      on ppInvPrincipal.PersonnelID = personnelInvPrincipal.PersonnelID
    left join PrototypeResearchArea pra
      on pra.PrototypeID = p.PrototypeID
    left join ResearchArea ra
      on ra.ResearchAreaID = pra.ResearchAreaID 
    left join PrototypeResearchProgram prp
      on prp.PrototypeID = p.PrototypeID 
    left join ResearchProgram rp
      on rp.ResearchProgramID = prp.ResearchProgramID 
    left join PrototypeResearchGroup prg
      on prg.PrototypeID = p.PrototypeID 
    left join ResearchGroup rg
      on rg.ResearchGroupID = prg.ResearchGroupID 
    left join PrototypeExperiment pe
      on pe.PrototypeID = p.PrototypeID 
    left join ExperimentalCollaboration ec
      on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
    left join PrototypeWorkline pwl
      on pwl.PrototypeID = p.PrototypeID 
    left join WorkLine wl
      on wl.WorkLineID = pwl.WorkLineID 
    left join PrototypeDeliverable pd
      on pd.PrototypeID = p.PrototypeID 
    left join PrototypeProject pp
      on pp.PrototypeID = p.PrototypeID 
    left join PrototypeOrganizationLinked pol
      on pol.PrototypeID = p.PrototypeID 
    where p.PrototypeID IN ($idsPrototypesStr)
    group by p.PrototypeID 
    ORDER BY p.StartDate DESC;
    ";

    $result = igfae_intranet_fetch_data($sql);

    return $result['rows'];

}

/**
 * Indíca si el usuario está relacionado con el prototype.
 * @param: $user_id: user
 * @param: $id: id del prototype
 * @param: $mode: modo de búsqueda
 * Si modo == 1 solo si el usuario está relacionado. 
 * Si modo == 2 comprueba si el usuario está relacionado o es el creador
 */
function hasRelatedPrototype($user_id, $id, $mode = 2) {
    
  $sqlHasRelation = "select count(*) 
    from Prototype p 
    left join PrototypePersonnel pp
      on pp.PrototypeID = p.PrototypeID ";
  

  $sqlHasRelation .= " where 1=1 ";

  if ($mode == 1) {
      $sqlHasRelation .= "
        and pp.PersonnelID = $user_id
      ";
  } else {
      $sqlHasRelation .= "
        and (pp.PersonnelID = $user_id or p.CreatedBy = $user_id)
      ";
  }

  $sqlHasRelation .= "
    and p.PrototypeID = $id
  ;";


  $result = igfae_intranet_get_var($sqlHasRelation);

  if ($result) {
      return true;
  } else {
      return false;
  }
}

function isUserCreatorOrRelatedWithPrototype($user_id){

  $idPro = $_GET['idPrototype'];

  if (!isset($idPro) || !isset($user_id)) {
      return false;
  }

  //obtener user a partir del user de wp
  $igfaeUserId = getUserByWPUserID($user_id);
  if (!empty($igfaeUserId)) {
      $id = $igfaeUserId[0]["id"];
      if (hasRelatedPrototype($id, $idPro)) {
          return true;
      }
  }

  return false;
}
//Comprobamos si esta validado el prototipo
function isPrototypeConsolidated($id) { 
  $sql = "SELECT count(*) 
  from Prototype p
  where
  p.Validated = 1
  and p.PrototypeID = $id";
  
  $result = igfae_intranet_get_var($sql);
  
  if ($result) {
  return true;
  } else {
  return false;
  }
}