<?php
function getExportWorkPlacements($workPlacements){


    $result = getSelectWorkPlacements($workPlacements, 0, null);
    $idsWorkPlacement = [];
    $idsWorkStr = "";
    $prepareParams = [];
    foreach ($result['workPlacement'] as $row) {
        $idsWorkPlacement[] = $row->getWorkPlacementID();
    }
    $idsWorkStr = implode(',', $idsWorkPlacement);

    if (count($idsWorkPlacement) > 0) {
        $sql = "SELECT
            wp.WorkPlacementID as ID,
            concat(displaced.Name, ' ', displaced.FirstSurname, ' ', coalesce(displaced.SecondSurname, '')) as DisplacedUser,
            concat(tutor.Name, ' ', tutor.FirstSurname, ' ', coalesce(tutor.SecondSurname, '')) as TutorUser,
            wp.ExternalTutor,
            wp.StartDate,
            wp.EndDate,
            wp.Financiation,
            wp.FinancedBy,
            i.InstitutionName,
            wp.Cost,
            wp.AsociatedContract,
            wp.ContractLink,
            group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
            group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
            group_concat(distinct ec.Name separator ' ; ') as Experiments,
            group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
            group_concat(distinct wl.Name separator ' ; ') as WorkLines,
            wp.Comment,
            wp.Validated,
            wp.ValidatedBy,
            wp.ValidatedOn 
        from WorkPlacement wp
        inner join Personnel displaced
            on displaced.PersonnelID = wp.DisplacedResearcherID 
        left join Personnel tutor
            on tutor.PersonnelID = wp.TutorID 
        left join Institution i
            on i.InstitutionID = wp.InstitutionID 
        left join WorkPlacementResearchArea wpra
            on wpra.WorkPlacementID = wp.WorkPlacementID 
        left join ResearchArea ra
            on ra.ResearchAreaID = wpra.ResearchAreaID 
        left join WorkPlacementResearchProgram wprp
            on wprp.WorkPlacementID = wp.WorkPlacementID 
        left join ResearchProgram rp
            on rp.ResearchProgramID = wprp.ResearchProgramID 
        left join WorkPlacementExperiment wpe
            on wpe.WorkPlacementID = wp.WorkPlacementID 
        left join ExperimentalCollaboration ec
            on ec.ExperimentalCollaborationID = wpe.ExperimentalCollaborationID 
        left join WorkplacementResearchGroup wprg
            on wprg.WorkPlacementID = wp.WorkPlacementID 
        left join ResearchGroup rg
            on rg.ResearchGroupID = wprg.ResearchGroupID 
        left join WorkPlacementWorkLine wpwl
            on wpwl.WorkPlacementID = wp.WorkPlacementID 
        left join WorkLine wl
            on wl.WorkLineID = wpwl.WorkLineID 
        where 1=1
        and wp.WorkPlacementID in ($idsWorkStr)
        group by wp.WorkPlacementID";

    $result = igfae_intranet_fetch_data($sql, $prepareParams);

    return $result['rows'];
    } else {
        return [];
    }
}

function getSelectWorkPlacements($workPlacements, $offset = 0, $amount = 10, $order = null)
{

    $workPlacement = [];
    $where = "";
    $prepareParams = [];
    if (isset($workPlacements)) {
        $queryPrepare = getPrepareSearchWorkPlacements($workPlacements);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $order = getOrderWorkPlacements($order);

    $sql = "SELECT 
            a.WorkPlacementID as workPlacementID,
            a.DisplacedUser as displacedUser,
            a.TutorUser as tutor,
            a.ExternalTutor as externalTutor,
            a.StartDate as startDate,
            a.EndDate as endDate,
            a.InstitutionName as institution,
            group_concat(distinct ec.Name separator ' ; ') as Experiments,
            a.Validated as validated
        from (select 
                wp.WorkPlacementID as WorkPlacementID,
                concat(displaced.Name, ' ', displaced.FirstSurname, ' ', coalesce(displaced.SecondSurname, '')) as DisplacedUser,
                concat(tutor.Name, ' ', tutor.FirstSurname, ' ', coalesce(tutor.SecondSurname, '')) as TutorUser,
                wp.ExternalTutor as ExternalTutor,
                wp.StartDate as StartDate,
                wp.EndDate as EndDate,
                i.InstitutionName as InstitutionName,
                wp.Validated as Validated
            from WorkPlacement wp
            inner join Personnel displaced
                on displaced.PersonnelID = wp.DisplacedResearcherID 
            left join Personnel tutor
                on tutor.PersonnelID = wp.TutorID 
            left join Institution i
                on i.InstitutionID = wp.InstitutionID 
            left join WorkPlacementResearchArea wpra
                on wpra.WorkPlacementID = wp.WorkPlacementID 
            left join WorkPlacementResearchProgram wprp
                on wprp.WorkPlacementID = wp.WorkPlacementID 
            left join WorkPlacementExperiment wpe
                on wpe.WorkPlacementID = wp.WorkPlacementID 
            left join ExperimentalCollaboration ec
                on ec.ExperimentalCollaborationID = wpe.ExperimentalCollaborationID 
            left join WorkplacementResearchGroup wprg
                on wprg.WorkPlacementID = wp.WorkPlacementID 
            left join WorkPlacementWorkLine wpwl
                on wpwl.WorkPlacementID = wp.WorkPlacementID 
            where {$where}
            group by wp.WorkPlacementID
            order by wp.WorkPlacementID ) a
            left join WorkPlacementExperiment wpe
                on wpe.WorkPlacementID = a.WorkPlacementID 
            left join ExperimentalCollaboration ec
                on ec.ExperimentalCollaborationID = wpe.ExperimentalCollaborationID 
            group by a.WorkPlacementID
            order by {$order} ";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoWorkPlacements($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $workPlacement[] = new IntranetWorkPlacements(
            $row->workPlacementID,
            $row->displacedUser,
            $row->tutor,
            NULL, 
            NULL,
            $row->institution,
            $row->externalTutor,
            NULL,
            NULL,
            NULL,
            [],
            [],
            $row->startDate,
            $row->endDate,
            [],
            [],
            $row->Experiments,
            NULL,
            $row->validated
        );
    }

    $info = [
        'workPlacement' => $workPlacement,
        'pagination' => $pagination
    ];

    return $info;
}


function getPrepareSearchWorkPlacements($workPlacements)
{
    $where = "1=1";
    $params = [];

    // ID
    if ($workPlacements->getWorkPlacementID()) {
        $where .= " AND (wp.WorkPlacementID  = %d)";
        $params[] = $workPlacements->getWorkPlacementID();
    }

    // Contract link
    if ($workPlacements->getContractLink()) {
        $where .= " AND (wp.ContractLink like %s)";
        $params[] = '%' . $workPlacements->getContractLink() . '%';
    }

    // Institution
    if ($workPlacements->getInstitution()) {
        $where .= " AND (i.InstitutionName like %s)";
        $params[] = '%' . $workPlacements->getInstitution() . '%';
    }

    // External tutor
    if ($workPlacements->getExternalTutor()) {
        $where .= " AND (wp.ExternalTutor like %s)";
        $params[] = '%' . $workPlacements->getExternalTutor() . '%';
    }

    // Displaced Researcher
    if ($workPlacements->getDisplacedResearcher()) {
        $typesCount = count($workPlacements->getDisplacedResearcher());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersDisplaced = implode(',', $paramsPlaceholders);
        
        $where .= " AND (wp.DisplacedResearcherID IN ({$placeholdersDisplaced}))";
        $params = array_merge($params, $workPlacements->getDisplacedResearcher());
    }

    // Tutor
    if ($workPlacements->getTutor()) {
        $typesCount = count($workPlacements->getTutor());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersTutor = implode(',', $paramsPlaceholders);
        
        $where .= " AND (wp.TutorID IN ({$placeholdersTutor}))";
        $params = array_merge($params, $workPlacements->getTutor());
    }

    // Research area select
     if ($workPlacements->getResearchArea()) {

        $typesCount = count($workPlacements->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (wpra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $workPlacements->getResearchArea());
    }

    // Research Program
    if ($workPlacements->getResearchProgram()) {
        $typesCount = count($workPlacements->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (wprp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $workPlacements->getResearchProgram());
    }

    // Start Date and End Date
    if ($workPlacements->getStartDate() && $workPlacements->getEndDate()) {
        $startDates =  explode("-", $workPlacements->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( (wp.StartDate BETWEEN %s and %s) OR wp.StartDate <= %s)";
        $params[] = $fecha1;
        $params[] = $fecha2;
        $params[] = $fecha1;

        //Fecha final
        $endDates =  explode("-", $workPlacements->getEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( (wp.EndDate BETWEEN %s and %s) OR wp.EndDate >= %s OR wp.EndDate is null)";
        $params[] = $fecha1;
        $params[] = $fecha2;
        $params[] = $fecha2;
        
    }  else if ($workPlacements->getStartDate()) {
        $startDates =  explode("-", $workPlacements->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( wp.StartDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    } else if ($workPlacements->getEndDate()){
        $endDates =  explode("-", $workPlacements->getEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( wp.EndDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    }



    //Research Group
    if ($workPlacements->getResearchGroup()) {
        $typesCount = count($workPlacements->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (wprg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $workPlacements->getResearchGroup());
    }

    //Work Line
    if ($workPlacements->getWorkLine()) {
        $typesCount = count($workPlacements->getWorkLine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersWorkline = implode(',', $paramsPlaceholders);

        $where .= " AND (wpwl.WorkLineID IN ({$placeholdersWorkline}))";
        $params = array_merge($params, $workPlacements->getWorkLine());
    }

    //Experimental collaboration
    if ($workPlacements->getExperimentalCollaboration()) {
        $typesCount = count($workPlacements->getExperimentalCollaboration());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiments = implode(',', $paramsPlaceholders);

        $where .= " AND (wpe.ExperimentalCollaborationID  IN ({$placeholdersExperiments}))";
        $params = array_merge($params, $workPlacements->getExperimentalCollaboration());
    }
    
    // Associated contract
    if ($workPlacements->getAssociatedContract()) {
        $typesCount = count($workPlacements->getAssociatedContract());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAssociated = implode(',', $paramsPlaceholders);

        $where .= " AND (wp.AsociatedContract IN ({$placeholdersAssociated}))";
        $params = array_merge($params, $workPlacements->getAssociatedContract());
    }

    // Validated
    if ($workPlacements->getValidated() != null) {
        $where .= " AND (wp.Validated  = %d)";
        $params[] = $workPlacements->getValidated();
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
            $where .= " AND (wp.DisplacedResearcherID = %d or wp.TutorID = %d or wp.CreatedBy = %d) ";
            $params[] =  $igfae_user_id;
            $params[] =  $igfae_user_id;
            $params[] =  $igfae_user_id;
        }
    }    

    return array(
        "where" => $where,
        "params" => $params
    );
}

function getPaginationInfoWorkPlacements($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];

    return $result;
}


function getOrderWorkPlacements($order)
{
    if (!isset($order)) {
        return ' a.WorkPlacementID ';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: //displacedUser
                $sort .= 'displacedUser ';
                break;
            case 1:  //tutor
                $sort .= 'tutor ';
                break;
            case 2: //externalTutor
                $sort .= 'externalTutor ';
                break;
            case 3: //startDate
                $sort .= 'startDate ';
                break;
            case 4: //endDate
                $sort .= 'endDate ';
                break;
            case 5: //institution
                $sort .= 'institution ';
                break;
            case 6: //Experiments
                $sort .= 'Experiments ';
                break;
            case 7: //validated
                $sort .= 'validated ';
                break;    
            default:
                $sort .= 'displacedUser ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

// Delete experiment
function deleteWorkPlacements($id)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction(); 

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_WORK_PLACEMENT, $id);

    deleteRelationsWorkPlacements($id);
     // ExperimentalCollaboration
     $sql = "DELETE FROM WorkPlacement
     WHERE WorkPlacementID = '$id'";
     igfae_intranet_save($sql);

    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }
}

// Delete experiment
function deleteRelationsWorkPlacements($id)
{
    //ExperimentalCollaborationResearchArea
    $sql = "DELETE FROM WorkPlacementResearchArea
        WHERE WorkPlacementID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationResearchProgram
    $sql = "DELETE FROM WorkPlacementResearchProgram
          WHERE WorkPlacementID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationResearchGroup
    $sql = "DELETE FROM WorkPlacementExperiment
          WHERE WorkPlacementID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationWorkLine
    $sql = "DELETE FROM WorkplacementResearchGroup
          WHERE WorkPlacementID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationProject
    $sql = "DELETE FROM WorkPlacementWorkLine
        WHERE WorkPlacementID = '$id'";
    igfae_intranet_save($sql);
}

function existsWorkPlacement($workPlacementId)
{
    $where = "";
    $params = [];

    $where .= " AND wp.WorkPlacementID = %d";
    $params[] = $workPlacementId;

    $sql = "SELECT DISTINCT
	wp.WorkPlacementID as 'id'
    FROM WorkPlacement wp 
        WHERE 1=1 {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

function saveWorkPlacement($workPlacement)
{
    global $current_user;

    // Recuperamos los campos del formulario para insertar
    $displacedResearcher= $workPlacement->getDisplacedResearcher();
    $tutor= $workPlacement->getTutor();
    $externalTutor= $workPlacement->getExternalTutor();
    $institution = $workPlacement->getInstitution();
    $cost = $workPlacement->getCost();
    $financiation = $workPlacement->getFinanciation();
    $financiatedBy= $workPlacement->getFinanciatedBy();
    $associatedContract= $workPlacement->getAssociatedContract();
    $contractLink= $workPlacement->getContractLink();
    $experimentalCollaboration = $workPlacement->getExperimentalCollaboration();
    $startDateStr = $workPlacement->getStartDate();
    if (isset($startDateStr)) {
        $time_input = strtotime($startDateStr);
        $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $endDateStr = $workPlacement->getEndDate();
    if (isset($endDateStr) && $endDateStr) {
        $time_input = strtotime($endDateStr);
        $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $researchArea = $workPlacement->getResearchArea();
    $researchProgram = $workPlacement->getResearchProgram();
    $researchGroup = $workPlacement->getResearchGroup();
    $workLine = $workPlacement->getWorkLine();
    $comment = $workPlacement->getComment();
    $validated = $workPlacement-> getValidated();
    $validatedBy = NULL;
    $validatedOn = NULL;
    if (isset($validated) && $validated) {
        $validatedBy = $current_user->user_login;
        $validatedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    }

    $idWorkPlacement = $_POST['idWorkPlacement'];
    $id = NULL;
    if (isset($idWorkPlacement) && strlen($idWorkPlacement) > 0) {
        //ACTUALIZAR PROYECTO
        $sql = "UPDATE WorkPlacement
        SET
        StartDate = '$startDate',
        EndDate = '$endDate',
        DisplacedResearcherID = '$displacedResearcher',  
        TutorID = '$tutor',   
        ExternalTutor =  " . (isset($externalTutor) ? "'$externalTutor'" : "NULL") . ",
        Financiation = " . (isset($financiation) ? "'$financiation'" : "NULL") . ",         
        FinancedBy = '$financiatedBy',
        Cost = " . (isset($cost) ? "'$cost'" : "NULL") . ",
        AsociatedContract = " . (isset($associatedContract) ? "'$associatedContract'" : "NULL") . ",
        ContractLink = " . (isset($contractLink) ? "'$contractLink'" : "NULL") . ", 
        InstitutionID = " . (isset($institution) ? "'$institution'" : "NULL") . ", 
        Comment = " . (isset($comment) ? "'$comment'" : "NULL") . ",              
        Validated = " . (isset($validated) ? "'$validated'" : "NULL") . ", 
        ValidatedBy = " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",
        ValidatedOn = " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . "
        WHERE WorkPlacementID = '$idWorkPlacement'";

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        igfae_intranet_save($sql);
        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsWorkPlacements($idWorkPlacement);

        insertResearchAreaWorkPlacement($idWorkPlacement, $researchArea);

        insertResearchProgramWorkPlacement($idWorkPlacement, $researchProgram);

        insertWorkLineWorkPlacement($idWorkPlacement, $workLine);

        insertResearchGroupWorkPlacement($idWorkPlacement, $researchGroup);

        insertExperimentWorkPlacement($idWorkPlacement, $experimentalCollaboration);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_WORK_PLACEMENT, $idWorkPlacement);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_WORK_PLACEMENT, $idWorkPlacement);
        }

         // Authorization
         $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_WORK_PLACEMENT, $idWorkPlacement);
         if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
             closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_WORK_PLACEMENT, $idWorkPlacement);
         }    

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
        
    } else {
        global $current_user;

        $sql = "INSERT INTO WorkPlacement 
            (
            StartDate,
            EndDate,
            DisplacedResearcherID,
            TutorID,
            ExternalTutor,
            Financiation,
            FinancedBy,
            Cost,
            AsociatedContract,
            ContractLink,
            InstitutionID,
            Comment,
            Validated,
            ValidatedBy,
            ValidatedOn,
            CreatedBy) 
            VALUES (  
            '$startDate',   
            '$endDate',
            '$displacedResearcher',
            '$tutor',
            " . (isset($externalTutor) ? "'$externalTutor'" : "NULL") . ",         
            " . (isset($financiation) ? "'$financiation'" : "NULL") . ",
            '$financiatedBy',
            " . (isset($cost) ? "'$cost'" : "NULL") . ",         
            " . (isset($associatedContract) ? "'$associatedContract'" : "NULL") . ",
            " . (isset($contractLink) ? "'$contractLink'" : "NULL") . ",
            " . (isset($institution) ? "'$institution'" : "NULL") . ",
            " . (isset($comment) ? "'$comment'" : "NULL") . ",
            " . (isset($validated) ? "'$validated'" : "NULL") . ", 
            " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",             
            " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . ",
            " . getUserByWPUserID($current_user->ID)[0]["id"] . "
            )";

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        $id = igfae_intranet_save($sql);

        insertResearchAreaWorkPlacement($id, $researchArea);

        insertResearchProgramWorkPlacement($id, $researchProgram);

        insertWorkLineWorkPlacement($id, $workLine);

        insertResearchGroupWorkPlacement($id, $researchGroup);

        insertExperimentWorkPlacement($id, $experimentalCollaboration);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_WORK_PLACEMENT, $id);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_WORK_PLACEMENT, $id);
        }

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    }   
    return $id;
}

//Insert WorkPlacementResearchArea
function insertResearchAreaWorkPlacement($id, $researchArea)
{
    if (isset($id)  && $id > 0 && isset($researchArea)) {
        foreach ($researchArea as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO WorkPlacementResearchArea(WorkPlacementID,
                        ResearchAreaID)
                        VALUES
                        (
                        '$id',
                        '$item'                    
                        )";
                igfae_intranet_save($sql);
            }
        }
    }   
}
//Insert WorkPlacementResearchProgram
function insertResearchProgramWorkPlacement($id, $researchProgram)
{
    if (isset($id)  && $id > 0 && isset($researchProgram)) {
        foreach ($researchProgram as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO WorkPlacementResearchProgram(WorkPlacementID,
                        ResearchProgramID)
                        VALUES
                        (
                        '$id',
                        '$item'                    
                        )";
                igfae_intranet_save($sql);
            }
        }
    }   
}
//Insert WorkPlacementWorkLine
function insertWorkLineWorkPlacement($id, $workLine)
{
    if (isset($id)  && $id > 0 && isset($workLine)) {
        foreach ($workLine as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO WorkPlacementWorkLine(WorkPlacementID,
                        WorkLineID)
                        VALUES
                        (
                        '$id',
                        '$item'                    
                        )";
                igfae_intranet_save($sql);
            }
        }
    }   
}
//Insert WorkplacementResearchGroup
function insertResearchGroupWorkPlacement($id, $researchGroup)
{
    if (isset($id)  && $id > 0 && isset($researchGroup)) {
        $sql = "INSERT INTO WorkplacementResearchGroup(WorkPlacementID,
                ResearchGroupID)
                VALUES
                (
                '$id',
                '$researchGroup'                    
                )";
        igfae_intranet_save($sql);
    }   
}

//Insert WorkPlacementExperiment
function insertExperimentWorkPlacement($id, $experimentalCollaboration)
{
    if (isset($id)  && $id > 0 && isset($experimentalCollaboration)) {
        $sql = "INSERT INTO WorkPlacementExperiment(WorkPlacementID,
                ExperimentalCollaborationID)
                VALUES
                (
                '$id',
                '$experimentalCollaboration'                    
                )";
        igfae_intranet_save($sql);
    }   
}

function getWorkPlacementsById($workPlacementId)
{
    $workPL = NULL;
    $where = "";
    $prepareParams = [];
    if (isset($workPlacementId)) {
        $where = " 1=1 and wp.WorkPlacementID  = %d";
        $prepareParams = $workPlacementId;
    }

    $sql = "SELECT
            wp.WorkPlacementID as idWorkPlacement,
            concat(displaced.PersonnelID) as DisplacedUserIDs,
            concat(displaced.Name, ' ', displaced.FirstSurname, ' ', coalesce(displaced.SecondSurname, '')) as DisplacedUser,
            concat(tutor.PersonnelID) as TutorUserIDs,
            concat(tutor.Name, ' ', tutor.FirstSurname, ' ', coalesce(tutor.SecondSurname, '')) as TutorUser,
            wp.ExternalTutor as ExternalTutor,
            wp.StartDate as StartDate,
            wp.EndDate as EndDate,
            wp.Financiation as Financiation,
            wp.FinancedBy as FinancedBy,
            wp.InstitutionID as InstitutionID,
            i.InstitutionName as InstitutionName,
            wp.Cost as Cost,
            wp.AsociatedContract as AsociatedContract,
            wp.ContractLink as ContractLink,
            group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
            group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
            group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
            group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
            group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
            group_concat(distinct ec.Name separator ' ; ') as Experiments,
            group_concat(distinct rg.ResearchGroupID  separator ' ; ') as ResearchGroupsIDs,
            group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
            group_concat(distinct wl.WorkLineID  separator ' ; ') as WorkLinesIDs,
            group_concat(distinct wl.Name separator ' ; ') as WorkLines,
            wp.Comment as Comment,
            wp.Validated as Validated,
            wp.ValidatedBy as ValidatedBy,
            wp.ValidatedOn as ValidatedOn
        from WorkPlacement wp
        inner join Personnel displaced
            on displaced.PersonnelID = wp.DisplacedResearcherID 
        left join Personnel tutor
            on tutor.PersonnelID = wp.TutorID 
        left join Institution i
            on i.InstitutionID = wp.InstitutionID 
        left join WorkPlacementResearchArea wpra
            on wpra.WorkPlacementID = wp.WorkPlacementID 
        left join ResearchArea ra
            on ra.ResearchAreaID = wpra.ResearchAreaID 
        left join WorkPlacementResearchProgram wprp
            on wprp.WorkPlacementID = wp.WorkPlacementID 
        left join ResearchProgram rp
            on rp.ResearchProgramID = wprp.ResearchProgramID 
        left join WorkPlacementExperiment wpe
            on wpe.WorkPlacementID = wp.WorkPlacementID 
        left join ExperimentalCollaboration ec
            on ec.ExperimentalCollaborationID = wpe.ExperimentalCollaborationID 
        left join WorkplacementResearchGroup wprg
            on wprg.WorkPlacementID = wp.WorkPlacementID 
        left join ResearchGroup rg
            on rg.ResearchGroupID = wprg.ResearchGroupID 
        left join WorkPlacementWorkLine wpwl
            on wpwl.WorkPlacementID = wp.WorkPlacementID 
        left join WorkLine wl
            on wl.WorkLineID = wpwl.WorkLineID 
        where {$where}
        group by wp.WorkPlacementID";

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $workPL = new IntranetWorkPlacements(
            $row->idWorkPlacement,
            NULL,
            NULL,
            $row->AsociatedContract,
            $row->ContractLink,
            NULL,
            $row->ExternalTutor,
            $row->Financiation,
            $row->FinancedBy,
            $row->Cost,
            NULL,
            NULL,
            $row->StartDate,
            $row->EndDate,
            NULL,
            NULL,
            NULL,
            $row->Comment,
            $row->Validated,
            $row->ValidatedBy,
            $row->ValidatedOn
        );
    }

    //Displaced users
    if($row->DisplacedUserIDs != null){
        $personnel = [];
        foreach ($rows['rows'] as $row) {
            $personnel[$row->DisplacedUserIDs] = $row->DisplacedUser;
        }
        $workPL->setDisplacedResearcher($personnel);
    }

    //Tutor
    if($row->TutorUserIDs != null){
        $tutor = [];
        foreach ($rows['rows'] as $row) {
            $tutor[$row->TutorUserIDs] = $row->TutorUser;
        }
        $workPL->setTutor($tutor);
    }

    //Insitution
    if($row->InstitutionID != null){
        $institution = [];
        foreach ($rows['rows'] as $row) {
            $institution[$row->InstitutionID] = $row->InstitutionName;
        }
        $workPL->setInstitution($institution);
    }

    //Research Area
    if($row->ResearchAreasIDs != null){
        $researchAreas = [];
        $ids = explode(" ; ", $row->ResearchAreasIDs);
        $nombres = explode(" ; ", $row->ResearchAreas); 
        foreach ($ids as $item){  
            foreach ($nombres as $name){
                if(array_search($name, $researchAreas, true) == ""){
                    $researchAreas[$item] = $name;
                    break;
                }else{
                    $researchAreas[$item] = "";
                }
            }  
        }    
        $workPL->setResearchArea($researchAreas);
    }

    //Research Program
    if($row->ResearchProgramsIDs != null){
        $researchProgram = [];
        $ids = explode(" ; ", $row->ResearchProgramsIDs);
        $nombres = explode(" ; ", $row->ResearchPrograms); 
        foreach ($ids as $item){  
            foreach ($nombres as $name){
                if(array_search($name, $researchProgram, true) == ""){
                    $researchProgram[$item] = $name;
                    break;
                }else{
                    $researchProgram[$item] = "";
                }
            }  
        } 
        $workPL->setResearchProgram($researchProgram);
    }

    //Research Group
    if($row->ResearchGroupsIDs != null){
        $researchGroup = [];
        $ids = explode(" ; ", $row->ResearchGroupsIDs);
        $nombres = explode(" ; ", $row->ResearchGroups); 
        foreach ($ids as $item){  
            foreach ($nombres as $name){
                if(array_search($name, $researchGroup, true) == ""){
                    $researchGroup[$item] = $name;
                    break;
                }else{
                    $researchGroup[$item] = "";
                }
            }  
        } 
        $workPL->setResearchGroup($researchGroup);
    }

    //External Collaboration
    if($row->ExperimentsIDs != null){
        $experimentalCollaboration = [];
        $ids = explode(" ; ", $row->ExperimentsIDs);
        $nombres = explode(" ; ", $row->Experiments); 
        foreach ($ids as $item){  
            foreach ($nombres as $name){
                if(array_search($name, $experimentalCollaboration, true) == ""){
                    $experimentalCollaboration[$item] = $name;
                    break;
                }else{
                    $experimentalCollaboration[$item] = "";
                }
            }  
        } 
        $workPL->setExperimentalCollaboration($experimentalCollaboration);
    }

    //Work Line
    if($row->WorkLinesIDs != null){
        $workLines = [];
        $ids = explode(" ; ", $row->WorkLinesIDs);
        $nombres = explode(" ; ", $row->WorkLines); 
        foreach ($ids as $item){  
            foreach ($nombres as $name){
                if(array_search($name, $workLines, true) == ""){
                    $workLines[$item] = $name;
                    break;
                }else{
                    $workLines[$item] = "";
                }
            }  
        } 
        $workPL->setWorkLine($workLines);
    }


    return $workPL;
}

/**
 * Indíca si el usuario está relacionado con el workplacement.
 * @param: $user_id: user
 * @param: $id: id del workplacement
 * @param: $mode: modo de búsqueda
 * Si modo == 1 solo si el usuario está relacionado. 
 * Si modo == 2 comprueba si el usuario está relacionado o es el creador
 */
function hasRelatedWorkPlacement($user_id, $id, $mode = 2) {
    
    $sqlHasRelation =  "SELECT count(*)
    from WorkPlacement wp ";

    $sqlHasRelation .= " where 1=1 ";

    if ($mode == 1) {
        $sqlHasRelation .= "
            and (wp.DisplacedResearcherID = $user_id or wp.TutorID = $user_id
            ";
    } else {
        $sqlHasRelation .= "
        and (wp.DisplacedResearcherID = $user_id or wp.TutorID = $user_id or wp.CreatedBy = $user_id)
        ";
    }   
    
    $sqlHasRelation .= "
        and wp.WorkPlacementID = $id
    ";
    
    $result = igfae_intranet_get_var($sqlHasRelation);

    if ($result) {
        return true;
    } else {
        return false;
    }
}

function isUserCreatorOrRelatedWithWorPlacement($user_id){

    $idWPlacement = $_GET['idWorkPlacement'];

    if (!isset($idWPlacement) || !isset($user_id)) {
        return false;
    }

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedWorkPlacement($id, $idWPlacement)) {
            return true;
        }
    }

    return false;
}
//Comprobamos si esta validada la estancia
function isWorkPlacementConsolidated($id) { 
    $sql = "SELECT count(*) 
    from WorkPlacement wp
    where
    wp.Validated = 1
    and wp.WorkPlacementID = $id";
    
    $result = igfae_intranet_get_var($sql);
    
    if ($result) {
    return true;
    } else {
    return false;
    }
}