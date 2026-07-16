<?php
function getExportTransfer($transfer)
{


    $resultIds = getSelectTransfer($transfer, 0, NULL);
    $result = [];
    $idstransfer = [];
    $idsTrStr = "";
    foreach ($resultIds['transfers'] as $row) {
        $idstransfer[] = $row->getTransferID();
    }
    $idsTrStr = implode(',', $idstransfer);

    if (count($idstransfer) > 0) {
        $sql = "SELECT DISTINCT
                t.TransferID as ID,
                tt.Name as TransferTypeName,
                ts.Name as TransferSubtypeName,
                tlen.Title as TitleEn,
                tles.Title as TitleEs,
                tlgl.Title as TitleGl,
                concat(pIPCoordinator.Name, ' ', pIPCoordinator.FirstSurname, COALESCE(pIPCoordinator.SecondSurname, '')) as IPCoordinatorName,
                group_concat(distinct concat(pAssociated.Name, ' ', pAssociated.FirstSurname, COALESCE(pAssociated.SecondSurname, '')) separator ' ; ') as AssociatedPersonnel,
                t.Reference,
                trlType.Name as TRLTypeName,
                group_concat(distinct tp.Name separator ' ; ') as partners,
                t.Link,
                t.`National`,
                t.Description,
                t.TTAType,
                t.TTAStartDate,
                t.TTAEndDate,
                t.ReportLink,
                t.TTACompanyTarget,
                group_concat(distinct tk.Name separator ' ; ') as keywords,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
                group_concat(distinct ec.Name separator ' ; ') as Experiments,
                group_concat(distinct wl.Name separator ' ; ') as WorkLines,
                group_concat(distinct COALESCE (pplen.Title, pples.Title, pplgl.Title) separator ' ; ') as ProductionProjects,
                group_concat(distinct trl.RequestLink  separator ' ; ') as TransferLinks,
                t.AnnualIncome,
                t.AnnualCost,
                t.Comments,
                t.Validated,
                t.ValidatedBy,
                t.ValidatedOn,
                t.Published,
                t.PublishedBy,
                t.PublishedOn  
            from Transfer t 
            inner join TransferType tt
                on tt.TransferTypeID = t.TransferTypeID 
            left join TransferSubtype ts
                on ts.TransferTypeID = t.TransferTypeID 
                and ts.TransferSubtypeID = t.TransferSubtypeID 
            left join TransferLanguage tlen
                on tlen.TransferID = t.TransferID 
                and tlen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
            left join TransferLanguage tles
                on tles.TransferID = t.TransferID 
                and tles.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
            left join TransferLanguage tlgl
                on tlgl.TransferID = t.TransferID 
                and tlgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
            left join TransferPersonnel tpIPCoordinator
                on tpIPCoordinator.TransferID = t.TransferID 
                and tpIPCoordinator.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
            left join Personnel pIPCoordinator
                on pIPCoordinator.PersonnelID = tpIPCoordinator.PersonnelID 
            left join TransferPersonnel tpAssociated
                on tpAssociated.TransferID = t.TransferID 
                and tpAssociated.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_II . "
            left join Personnel pAssociated
                on pAssociated.PersonnelID = tpAssociated.PersonnelID 
            left join TransferResearchArea tra
                on tra.TransferID = t.TransferID 
            left join ResearchArea ra
                on ra.ResearchAreaID = tra.ResearchAreaID 
            left join TransferResearchProgram trp
                on trp.TransferID = t.TransferID 
            left join ResearchProgram rp
                on rp.ResearchProgramID = trp.ResearchProgramID 
            left join TransferResearchGroup trg
                on trg.TransferID = t.TransferID 
            left join ResearchGroup rg
                on rg.ResearchGroupID = trg.ResearchGroupID 
            left join TransferWorkline twl
                on twl.TransferID = t.TransferID 
            left join WorkLine wl
                on wl.WorkLineID = twl.WorkLineID 
            left join TransferKeyword tk
                on tk.TransferID = t.TransferID 
            left join TRLType trlType
                on t.TRLTypeID = trlType.TRLTypeID 
            left join TransferExperiment te
                on te.TransferID = t.TransferID 
            left join ExperimentalCollaboration ec
                on ec.ExperimentalCollaborationID = te.ExperimentalCollaborationID 
            left join TransferProductionProject tpp
                on tpp.TransferID = t.TransferID 
            left join ProductionProject pp
                on tpp.ProductionProjectID = pp.ProductionProjectID 
            left join ProductionProjectLanguage pplen
                on pplen.ProductionProjectID = tpp.ProductionProjectID 
                and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
            left join ProductionProjectLanguage pples
                on pples.ProductionProjectID = tpp.ProductionProjectID 
                and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
            left join ProductionProjectLanguage pplgl
                on pplgl.ProductionProjectID = tpp.ProductionProjectID 
                and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
            left join TransferRequestLink trl
                on trl.TransferID = t.TransferID 
            left join TransferPartner tp
                on tp.TransferID = t.TransferID 
            where 1=1
            and t.TransferID in ($idsTrStr)
            group by t.TransferID
            order by t.TransferID desc";

        $result = igfae_intranet_fetch_data($sql);
        return $result['rows'];
    } else {
        return [];
    }
    
}

function getSelectTransfer($trs, $offset = 0, $amount = 10, $order = null)
{

    $transfers = [];
    $where = "";
    $prepareParams = [];
    if (isset($trs)) {
        $queryPrepare = getPrepareSearchTransfer($trs);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $order = getOrderTransfer($order);

    $sql = "SELECT distinct
            t.TransferID as TransferID,
            t.TransferTypeID,
            tt.Name as TransferTypeName,
            ts.TransferSubtypeID,
            ts.Name as TransferSubtypeName,
            COALESCE (tlen.Title, tles.Title, tlgl.Title) as Title,
            pIPCoordinator.PersonnelID IPCoordinatorID,
            concat(pIPCoordinator.Name, ' ', pIPCoordinator.FirstSurname, ' ', COALESCE(pIPCoordinator.SecondSurname, '')) as IPCoordinatorName,
            t.TTAType as TTAType,
            t.TTAStartDate as TTAStartDate,
            t.TTAEndDate,
            t.Validated as Validated,
            t.Published as Published
        from Transfer t 
        inner join TransferType tt
            on tt.TransferTypeID = t.TransferTypeID 
        left join TransferSubtype ts
            on ts.TransferTypeID = t.TransferTypeID 
            and ts.TransferSubtypeID = t.TransferSubtypeID 
        left join TransferLanguage tlen
            on tlen.TransferID = t.TransferID 
            and tlen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        left join TransferLanguage tles
            on tles.TransferID = t.TransferID 
            and tles.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
        left join TransferLanguage tlgl
            on tlgl.TransferID = t.TransferID 
            and tlgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
        left join TransferPersonnel tpIPCoordinator
            on tpIPCoordinator.TransferID = t.TransferID 
            and tpIPCoordinator.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
        left join Personnel pIPCoordinator
            on pIPCoordinator.PersonnelID = tpIPCoordinator.PersonnelID 
        left join TransferPersonnel tpAssociated
            on tpAssociated.TransferID = t.TransferID 
            and tpAssociated.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_II . "
        left join TransferResearchArea tra
            on tra.TransferID = t.TransferID 
        left join TransferResearchProgram trp
            on trp.TransferID = t.TransferID 
        left join TransferResearchGroup trg
            on trg.TransferID = t.TransferID 
        left join TransferWorkline twl
            on twl.TransferID = t.TransferID 
        left join TransferKeyword tk
            on tk.TransferID = t.TransferID 
        left join TransferPersonnel tp
            on tp.TransferID = t.TransferID 
        where {$where}
        order by {$order} ";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoTransfer($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $transfers[] = new IntranetTransfer(
            $row->TransferID,
            $row->Title,
            NULL,
            NULL,
            NULL,
            $row->TransferTypeName,
            $row->TransferSubtypeName,
            NULL,
            NULL,
            NULL,
            NULL,
            $row->TTAType,
            $row->TTAStartDate,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            $row->IPCoordinatorName,
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
            $row->Validated,
            $row->Published,
            NULL,
            NULL,
            NULL,
            NULL
        );
    }

    $info = [
        'transfers' => $transfers,
        'pagination' => $pagination
    ];

    return $info;
}


function getPrepareSearchTransfer($tranfer)
{
    $where = "1=1";
    $params = [];

    // ID
    if ($tranfer->getTransferID()) {
        $where .= " AND (t.TransferID  = %d)";
        $params[] = $tranfer->getTransferID();
    }

    // Title
    if ($tranfer->getTitle()) {
        $where .= " AND ((tlen.Title) like %s or (tles.Title) like %s or (tlgl.Title) like %s)";
        $params[] = '%' . $tranfer->getTitle() . '%';
        $params[] = '%' . $tranfer->getTitle() . '%';
        $params[] = '%' . $tranfer->getTitle() . '%';
    }

    // TTA type
    if ($tranfer->getTTAType()) {
        $where .= " AND (t.TTAType like %s)";
        $params[] = '%' . $tranfer->getTTAType() . '%';
    }

    // TRL type
    if ($tranfer->getTRLTypeID()) {
        $typesCount = count($tranfer->getTRLTypeID());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersTRL = implode(',', $paramsPlaceholders);

        $where .= " AND (t.TRLTypeID IN ({$placeholdersTRL}))";
        $params = array_merge($params, $tranfer->getTRLTypeID());
    }

    // Coordinator IGFAE select
    if ($tranfer->getCoordinatorIgfae()) {
        $typesCount = count($tranfer->getCoordinatorIgfae());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersCoordinator = implode(',', $paramsPlaceholders);

        $where .= " AND (tpIPCoordinator.PersonnelID IN ({$placeholdersCoordinator}))";
        $params = array_merge($params, $tranfer->getCoordinatorIgfae());
    }

    // Transfer personnel
    if ($tranfer->getTransferPersonnel()) {
        $typesCount = count($tranfer->getTransferPersonnel());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersPersonnel = implode(',', $paramsPlaceholders);

        $where .= " AND (tpAssociated.PersonnelID IN ({$placeholdersPersonnel}))";
        $params = array_merge($params, $tranfer->getTransferPersonnel());
    }

    // Transfer type 
    if ($tranfer->getTransferType()) {
        $typesCount = count($tranfer->getTransferType());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersTransferType = implode(',', $paramsPlaceholders);

        $where .= " AND (t.TransferTypeID IN ({$placeholdersTransferType}))";
        $params = array_merge($params, $tranfer->getTransferType());
    }


    // TTA Start Date and End Date
    if ($tranfer->getTTAStartDate() && $tranfer->getTTAEndDate()) {
        $startDates =  explode("-", $tranfer->getTTAStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( (t.TTAStartDate BETWEEN %s and %s) OR t.TTAStartDate <= %s)";
        $params[] = $fecha1;
        $params[] = $fecha2;
        $params[] = $fecha1;

        //Fecha final
        $endDates =  explode("-", $tranfer->getTTAEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( (t.TTAEndDate BETWEEN %s and %s) OR t.TTAEndDate >= %s OR t.TTAEndDate is null)";
        $params[] = $fecha1;
        $params[] = $fecha2;
        $params[] = $fecha2;
        
    }  else if ($tranfer->getTTAStartDate()) {
        $startDates =  explode("-", $tranfer->getTTAStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( t.TTAStartDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    } else if ($tranfer->getTTAEndDate()){
        $endDates =  explode("-", $tranfer->getTTAEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( t.TTAEndDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    }

    // Transfer subtype 
    if ($tranfer->getTransferSubtype()) {
        $typesCount = count($tranfer->getTransferSubtype());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersTransferType = implode(',', $paramsPlaceholders);

        $where .= " AND (t.TransferSubtypeID  IN ({$placeholdersTransferType}))";
        $params = array_merge($params, $tranfer->getTransferSubtype());
    }

    // Keywords
    if ($tranfer->getKeywords()) {
        $typesCount = count($tranfer->getKeywords());
        $paramsPlaceholders = array_fill(0, $typesCount, '%s');
        $placeholdersKeywords = implode(',', $paramsPlaceholders);

        $where .= " AND (tk.Name IN ({$placeholdersKeywords}))";
        $params = array_merge($params, $tranfer->getKeywords());
    }

    // Research area 
    if ($tranfer->getResearchArea()) {

        $typesCount = count($tranfer->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (tra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $tranfer->getResearchArea());
    }

    // Research Program
    if ($tranfer->getResearchProgram()) {
        $typesCount = count($tranfer->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (trp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $tranfer->getResearchProgram());
    }

    //Research Group
    if ($tranfer->getResearchGroup()) {
        $typesCount = count($tranfer->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (trg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $tranfer->getResearchGroup());
    }

    //Work Line
    if ($tranfer->getWorkLine()) {
        $typesCount = count($tranfer->getWorkLine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersWorkline = implode(',', $paramsPlaceholders);

        $where .= " AND (twl.WorkLineID IN ({$placeholdersWorkline}))";
        $params = array_merge($params, $tranfer->getWorkLine());
    }

    // National
    if ($tranfer->getNational()) {
        $typesCount = count($tranfer->getNational());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersNat = implode(',', $paramsPlaceholders);

        $where .= " AND (t.`National` IN ({$placeholdersNat}))";
        $params = array_merge($params, $tranfer->getNational());
    }

    // Published
    if ($tranfer->getPublished() != null) {
        $where .= " AND (t.Published = %d)";
        $params[] = $tranfer->getPublished();
    }

    // Validated
    if ($tranfer->getValidated() != null) {
        $where .= " AND (t.Validated  = %d)";
        $params[] = $tranfer->getValidated();
    }
    //permisos
    $user_id = get_current_user_id();

    if (
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_GESTION) &&
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL) &&
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_COLABORADOR) &&
        (is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO) ||
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO) ||
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA) ||
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR))
    ) {

        $igfaeUserId = getUserByWPUserID($user_id);
        if (!empty($igfaeUserId)) {
            $igfae_user_id = $igfaeUserId[0]["id"];
            $where .= " AND (tp.PersonnelID = %d or t.CreatedBy = %d)";
            $params[] =  $igfae_user_id;
            $params[] =  $igfae_user_id;
        }
    }
    return array(
        "where" => $where,
        "params" => $params
    );
}

function getPaginationInfoTransfer($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];

    return $result;
}


function getOrderTransfer($order)
{
    if (!isset($order)) {
        return ' t.TransferID ';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: //TransferTypeName
                $sort .= 'TransferTypeName ';
                break;
            case 1:  //transferSubtypeName
                $sort .= 'TransferSubtypeName ';
                break;
            case 2: //Title
                $sort .= 'Title ';
                break;
            case 3: //TTAType
                $sort .= 'TTAType ';
                break;
            case 4: //TTAStartDate
                $sort .= 'TTAStartDate ';
                break;
            case 5: //IPCoordinatorName
                $sort .= 'IPCoordinatorName ';
                break;
            case 6: //Validated
                $sort .= 'Validated ';
                break;
            case 7: //Published
                $sort .= 'Published ';
                break;
            default:
                $sort .= 'TransferTypeName ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

// Delete transfer
function deleteTransfer($id)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $id);

    deleteRelationsTransfer($id);
    // ExperimentalCollaboration
    $sql = "DELETE FROM `Transfer`
     WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }
}

// Delete experiment
function deleteRelationsTransfer($id)
{
    //TransferExperiment
    $sql = "DELETE FROM TransferExperiment
        WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferKeyword
    $sql = "DELETE FROM TransferKeyword
          WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferLanguage
    $sql = "DELETE FROM TransferLanguage
          WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferPartner
    $sql = "DELETE FROM TransferPartner
          WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationProject
    $sql = "DELETE FROM TransferPersonnel
        WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferProductionProject
    $sql = "DELETE FROM TransferProductionProject
        WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferRequestLink
    $sql = "DELETE FROM TransferRequestLink
        WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferResearchArea
    $sql = "DELETE FROM TransferResearchArea
        WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferResearchGroup
    $sql = "DELETE FROM TransferResearchGroup
        WHERE TransferID = '$id'";
    igfae_intranet_save($sql);

    //TransferResearchProgram
    $sql = "DELETE FROM TransferResearchProgram
        WHERE TransferID = '$id'";

    igfae_intranet_save($sql);

    //TransferWorkline
    $sql = "DELETE FROM TransferWorkline
        WHERE TransferID = '$id'";
    igfae_intranet_save($sql);
}

function saveTransfer($transfer)
{
    global $current_user;

    // Recuperamos los campos del formulario para insertar
    $transferType = $transfer->getTransferType();
    $transferSubType = $transfer->getTransferSubtype();
    $titleES = $transfer->getTitleES();
    $titleEN = $transfer->getTitleEN();
    $titleGL = $transfer->getTitleGL();
    $reference = $transfer->getReference();
    $reportLink = $transfer->getReportLink();
    $tRLType = $transfer->getTRLTypeID();
    $researchArea = $transfer->getResearchArea();
    $researchProgram = $transfer->getResearchProgram();
    $coordinatorIgfae = $transfer->getCoordinatorIgfae();
    $keywords = $transfer->getKeywords();
    $tTAStartDateeStr = $transfer->getTTAStartDate();
    if (isset($tTAStartDateeStr)) {
        $time_input = strtotime($tTAStartDateeStr);
        $tTAStartDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $tTAEndDateStr = $transfer->getTTAEndDate();
    $tTAEndDate = NULL;
    if (isset($tTAEndDateStr) && $tTAEndDateStr) {
        $time_input = strtotime($tTAEndDateStr);
        $tTAEndDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $researchGroup = $transfer->getResearchGroup();
    $workLine = $transfer->getWorkLine();
    $link = $transfer->getLink();
    $description = $transfer->getDescription();
    $national = $transfer->getNational();
    $annualIncome = $transfer->getAnnualIncome();
    $annualCost = $transfer->getAnnualCost();
    $tTaType = $transfer->getTTAType();
    $tTACompanyTarget = $transfer->getTTACompanyTarget();
    $asociatedPublications = $transfer->getAsociatedPublications();
    $experimentalCollaboration = $transfer->getExperimentalCollaboration();
    $partner = $transfer->getPartners();
    $requestLink = $transfer->getRequestLink();
    $researcherInvolved = $transfer->getTransferPersonnel();
    $comments = $transfer->getComments();
    $validated = $transfer->getValidated();
    $validatedBy = NULL;
    $validatedOn = NULL;
    if (isset($validated) && $validated) {
        $validatedBy = $current_user->user_login;
        $validatedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    }
    $published = $transfer->getPublished();
    $publishedBy = NULL;
    $publishedOn = NULL;
    if (isset($published) && $published) {
        $publishedBy = $current_user->user_login;
        $publishedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    }

    $idTransfer = $_POST['idTransfer'];
    $id = NULL;
    if (isset($idTransfer) && strlen($idTransfer) > 0) {
        //ACTUALIZAR PROYECTO
        $sql = "UPDATE Transfer
        SET
        TransferTypeID = '$transferType',
        TransferSubtypeID = " . (isset($transferSubType) ? "'$transferSubType'" : "NULL") . ",
        Reference = " . (isset($reference) ? "'$reference'" : "NULL") . ",
        Link = " . (isset($link) ? "'$link'" : "NULL") . ",
        National = " . (isset($national) ? "'$national'" : "NULL") . ",        
        Description = " . (isset($description) ? "'$description'" : "NULL") . ",
        TTAType = '$tTaType',
        TTAStartDate = '$tTAStartDate',
        TTACompanyTarget = " . (isset($tTACompanyTarget) ? "'$tTACompanyTarget'" : "NULL") . ",
        ReportLink = " . (isset($reportLink) ? "'$reportLink'" : "NULL") . ",
        TRLTypeID =  " . (isset($tRLType) ? "'$tRLType'" : "NULL") . ",
        AnnualIncome = " . (isset($annualIncome) ? "'$annualIncome'" : "NULL") . ", 
        AnnualCost = " . (isset($annualCost) ? "'$annualCost'" : "NULL") . ", 
        Comments = " . (isset($comments) ? "'$comments'" : "NULL") . ",
        Validated = '$validated', 
        Published = '$published', 
        ValidatedBy = " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",  
        PublishedBy = " . (isset($publishedBy) ? "'$publishedBy'" : "NULL") . ", 
        ValidatedOn = " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . ", 
        PublishedOn = " . (isset($publishedOn) ? "'$publishedOn'" : "NULL") . ",
        TTAEndDate = " . (isset($tTAEndDate) ? "'$tTAEndDate'" : "NULL") . "
        WHERE TransferID = '$idTransfer'";

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        igfae_intranet_save($sql);
        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsTransfer($idTransfer);

        insertResearchAreaTransfer($idTransfer, $researchArea);

        insertResearchProgramTransfer($idTransfer, $researchProgram);

        insertWorkLineTransfer($idTransfer, $workLine);

        insertResearchGroupTransfer($idTransfer, $researchGroup);

        insertTransferProductionProject($idTransfer, $asociatedPublications);

        insertTransferPersonnel($idTransfer, $researcherInvolved);

        insertTransferCoordinatorPersonnel($idTransfer, $coordinatorIgfae);

        insertTransferExperiment($idTransfer, $experimentalCollaboration);

        insertTransferRequestLink($idTransfer, $requestLink);

        insertTransferPartner($idTransfer, $partner);

        insertTransferLanguage($idTransfer, $titleEN, $titleES, $titleGL);

        insertTransferKeywords($idTransfer, $keywords);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $idTransfer);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $idTransfer);
        }
        if (isset($publishedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $idTransfer);
        }

        // Authorization
        $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $idTransfer);
        if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
            closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $idTransfer);
        }    

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    } else {
        global $current_user;

        $sql = "INSERT INTO Transfer 
            (
            TransferTypeID,
            TransferSubtypeID,
            Reference,
            Link,
            National,
            Description,
            TTAType,
            TTAStartDate,
            TTACompanyTarget,
            ReportLink,
            TRLTypeID,
            AnnualIncome,
            AnnualCost,
            Comments,
            Validated,
            Published,
            ValidatedBy,
            PublishedBy,
            ValidatedOn,
            PublishedOn,
            TTAEndDate,
            CreatedBy) 
            VALUES ( 
            '$transferType', 
            " . (isset($transferSubType) ? "'$transferSubType'" : "NULL") . ",
            " . (isset($reference) ? "'$reference'" : "NULL") . ",
            " . (isset($link) ? "'$link'" : "NULL") . ",
            '$national',
            " . (isset($description) ? "'$description'" : "NULL") . ",
            '$tTaType',
            '$tTAStartDate',
            " . (isset($tTACompanyTarget) ? "'$tTACompanyTarget'" : "NULL") . ",
            " . (isset($reportLink) ? "'$reportLink'" : "NULL") . ",
            " . (isset($tRLType) ? "'$tRLType'" : "NULL") . ",
            " . (isset($annualIncome) ? "'$annualIncome'" : "NULL") . ",
            " . (isset($annualCost) ? "'$annualCost'" : "NULL") . ", 
            " . (isset($comments) ? "'$comments'" : "NULL") . ",
            '$validated', 
            '$published', 
            " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",  
            " . (isset($publishedBy) ? "'$publishedBy'" : "NULL") . ",             
            " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . ", 
            " . (isset($publishedOn) ? "'$publishedOn'" : "NULL") . ",
            " . (isset($tTAEndDate) ? "'$tTAEndDate'" : "NULL") . ",
            " . getUserByWPUserID($current_user->ID)[0]["id"] . "
            )";

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        $id = igfae_intranet_save($sql);

        insertResearchAreaTransfer($id, $researchArea);

        insertResearchProgramTransfer($id, $researchProgram);

        insertWorkLineTransfer($id, $workLine);

        insertResearchGroupTransfer($id, $researchGroup);

        insertTransferProductionProject($id, $asociatedPublications);

        insertTransferPersonnel($id, $researcherInvolved);

        insertTransferCoordinatorPersonnel($id, $coordinatorIgfae);

        insertTransferExperiment($id, $experimentalCollaboration);

        insertTransferRequestLink($id, $requestLink);

        insertTransferPartner($id, $partner);

        insertTransferLanguage($id, $titleEN, $titleES, $titleGL);

        insertTransferKeywords($id, $keywords);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $id);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $id);
        }
        if (isset($publishedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_TRANSFER, $id);
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

//Insert TransferResearchArea
function insertResearchAreaTransfer($id, $researchArea)
{
    if (isset($id)  && $id > 0 && isset($researchArea)) {
        foreach ($researchArea as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferResearchArea(TransferID,
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
//Insert TransferResearchProgram
function insertResearchProgramTransfer($id, $researchProgram)
{
    if (isset($id)  && $id > 0 && isset($researchProgram)) {
        foreach ($researchProgram as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferResearchProgram(TransferID,
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
//Insert TransferWorkLine
function insertWorkLineTransfer($id, $workLine)
{
    if (isset($id)  && $id > 0 && isset($workLine)) {
        foreach ($workLine as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferWorkline(TransferID,
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

//Insert TransferResearchGroup
function insertResearchGroupTransfer($id, $researchGroup)
{
    if (isset($id)  && $id > 0 && isset($researchGroup)) {
        foreach ($researchGroup as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferResearchGroup(TransferID,
                        ResearchGroupID)
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
//Insert TransferLanguage
function insertTransferLanguage($id, $titleEN, $titleES, $titleGL)
{
    if (isset($id) && $id > 0) {
        $language = IGFAE_INTRANET_LANGUAGE_EN;
        $title = "";
        if (isset($titleEN) && strlen($titleEN) > 0) {
            $language = IGFAE_INTRANET_LANGUAGE_EN;
            $title = $titleEN;

            $sql = "INSERT INTO TransferLanguage(TransferID,
            LanguageID,
            Title)
            VALUES
            (
            '$id',
            '$language',
            '$title'
            )";
            igfae_intranet_save($sql);
        }
        if (isset($titleES) && strlen($titleES) > 0) {
            $language = IGFAE_INTRANET_LANGUAGE_ES;
            $title = $titleES;

            $sql = "INSERT INTO TransferLanguage(TransferID,
            LanguageID,
            Title)
            VALUES
            (
            '$id',
            '$language',
            '$title'
            )";
            igfae_intranet_save($sql);
        }
        if (isset($titleGL) && strlen($titleGL) > 0) {
            $language = IGFAE_INTRANET_LANGUAGE_GL;
            $title = $titleGL;

            $sql = "INSERT INTO TransferLanguage(TransferID,
            LanguageID,
            Title)
            VALUES
            (
            '$id',
            '$language',
            '$title'
            )";
            igfae_intranet_save($sql);
        }
    }
}
//Insert TransferKeyword
function insertTransferKeywords($id, $keywords)
{
    // Insertamos en la tabla TransferKeyword
    if (isset($id)  && $id > 0 && isset($keywords)) {
        if (isset($id)  && $id > 0 && isset($keywords)) {
            existKeywords($keywords);
            foreach ($keywords as $item) {
                $data = array('Name' => trim($item), 'TransferID' => $id);
                igfae_intranet_insert('TransferKeyword', $data);
            }
        }
    }
}
//Insert TransferProductionProject
function insertTransferProductionProject($id, $asociatedPublications)
{
    if (isset($id)  && $id > 0 && isset($asociatedPublications)) {
        foreach ($asociatedPublications as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferProductionProject(TransferID,
                        ProductionProjectID)
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
//Insert TransferExperiment
function insertTransferExperiment($id, $experimentalCollaboration)
{
    if (isset($id)  && $id > 0 && isset($experimentalCollaboration)) {
        $sql = "INSERT INTO TransferExperiment(TransferID,
                ExperimentalCollaborationID)
                VALUES
                (
                '$id',
                '$experimentalCollaboration'                    
                )";
        igfae_intranet_save($sql);
    }
}
//Insert TransferPartner
function insertTransferPartner($id, $partner)
{
    if (isset($id)  && $id > 0 && isset($partner)) {
        foreach ($partner as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferPartner(TransferID,
                        Name)
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
//Insert TransferPersonnel
function insertTransferPersonnel($id, $researcherInvolved)
{
    if (isset($id)  && $id > 0 && isset($researcherInvolved)) {
        $involved = IGFAE_INTRANET_PERSONNEL_TYPE_II;
        foreach ($researcherInvolved as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferPersonnel(TransferID,
                        PersonnelTypeID,
                        PersonnelID)
                        VALUES
                        (
                        '$id',
                        '$involved',
                        '$item'                    
                        )";
                igfae_intranet_save($sql);
            }
        }
    }
}
//Insert TransferPersonnel
function insertTransferCoordinatorPersonnel($id, $coordinator)
{
    if (isset($id)  && $id > 0 && isset($coordinator)) {
        $investigator = IGFAE_INTRANET_PERSONNEL_TYPE_IPCP;
        $sql = "INSERT INTO TransferPersonnel(TransferID,
                PersonnelTypeID,
                PersonnelID)
                VALUES
                (
                '$id',
                '$investigator',
                '$coordinator'                    
                )";
        igfae_intranet_save($sql);
    }
}
//Insert TransferRequestLink
function insertTransferRequestLink($id, $requestLink)
{
    if (isset($id)  && $id > 0 && isset($requestLink)) {
        foreach ($requestLink as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO TransferRequestLink(TransferID,
                        RequestLink)
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

function getTransferById($transferId)
{
    $transfer = NULL;
    $where = "";
    $prepareParams = [];
    if (isset($transferId)) {
        $where = " 1=1 and t.TransferID = %d";
        $prepareParams = $transferId;
    }

    $sql = "SELECT DISTINCT
            t.TransferID as transferID,
            t.TransferTypeID,
            tt.Name as TransferTypeName,
            ts.TransferSubtypeID,
            ts.Name as TransferSubtypeName,
            tlen.Title as TitleEn,
            tles.Title as TitleEs,
            tlgl.Title as TitleGl,
            pIPCoordinator.PersonnelID as IPCoordinatorID,
            concat(pIPCoordinator.Name, ' ', pIPCoordinator.FirstSurname,' ', COALESCE(pIPCoordinator.SecondSurname, ' ')) as IPCoordinatorName,
            group_concat(distinct tpAssociated.PersonnelID separator ' ; ') as AssociatedPersonnelIDs,
            group_concat(distinct concat(pAssociated.Name, ' ', pAssociated.FirstSurname,' ', COALESCE(pAssociated.SecondSurname, '')) separator ' ; ') as AssociatedPersonnel,
            t.Reference,
            trlType.TRLTypeID,
            trlType.Name as TRLTypeName,
            group_concat(distinct tp.Name separator ' ; ') as partners,
            t.Link,
            t.`National`,
            t.Description,
            t.TTAType,
            t.TTAStartDate,
            t.TTAEndDate,
            t.ReportLink,
            t.TTACompanyTarget,
            group_concat(distinct tk.Name separator ' ; ') as keywords,
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
            group_concat(distinct tpp.ProductionProjectID  separator ' ; ') as ProductionProjectsIDs,
            group_concat(distinct COALESCE (pplen.Title, pples.Title, pplgl.Title) separator ' ; ') as ProductionProjects,
            t.AnnualIncome,
            t.AnnualCost,
            t.Comments,
            t.Validated,
            t.ValidatedBy,
            t.ValidatedOn,
            t.Published,
            t.PublishedBy,
            t.PublishedOn  
        from Transfer t 
        inner join TransferType tt
            on tt.TransferTypeID = t.TransferTypeID 
        left join TransferSubtype ts
            on ts.TransferTypeID = t.TransferTypeID 
            and ts.TransferSubtypeID = t.TransferSubtypeID 
        left join TransferLanguage tlen
            on tlen.TransferID = t.TransferID 
            and tlen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        left join TransferLanguage tles
            on tles.TransferID = t.TransferID 
            and tles.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
        left join TransferLanguage tlgl
            on tlgl.TransferID = t.TransferID 
            and tlgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
        left join TransferPersonnel tpIPCoordinator
            on tpIPCoordinator.TransferID = t.TransferID 
            and tpIPCoordinator.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
        left join Personnel pIPCoordinator
            on pIPCoordinator.PersonnelID = tpIPCoordinator.PersonnelID 
        left join TransferPersonnel tpAssociated
            on tpAssociated.TransferID = t.TransferID 
            and tpAssociated.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_II . "
        left join Personnel pAssociated
            on pAssociated.PersonnelID = tpAssociated.PersonnelID 
        left join TransferResearchArea tra
            on tra.TransferID = t.TransferID 
        left join ResearchArea ra
            on ra.ResearchAreaID = tra.ResearchAreaID 
        left join TransferResearchProgram trp
            on trp.TransferID = t.TransferID 
        left join ResearchProgram rp
            on rp.ResearchProgramID = trp.ResearchProgramID 
        left join TransferResearchGroup trg
            on trg.TransferID = t.TransferID 
        left join ResearchGroup rg
            on rg.ResearchGroupID = trg.ResearchGroupID 
        left join TransferWorkline twl
            on twl.TransferID = t.TransferID 
        left join WorkLine wl
            on wl.WorkLineID = twl.WorkLineID 
        left join TransferKeyword tk
            on tk.TransferID = t.TransferID 
        left join TRLType trlType
            on t.TRLTypeID = trlType.TRLTypeID 
        left join TransferExperiment te
            on te.TransferID = t.TransferID 
        left join ExperimentalCollaboration ec
            on ec.ExperimentalCollaborationID = te.ExperimentalCollaborationID 
        left join TransferProductionProject tpp
            on tpp.TransferID = t.TransferID 
        left join ProductionProject pp
            on tpp.ProductionProjectID = pp.ProductionProjectID 
        left join ProductionProjectLanguage pplen
            on pplen.ProductionProjectID = tpp.ProductionProjectID 
            and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        left join ProductionProjectLanguage pples
            on pples.ProductionProjectID = tpp.ProductionProjectID 
            and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
        left join ProductionProjectLanguage pplgl
            on pplgl.ProductionProjectID = tpp.ProductionProjectID 
            and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
        left join TransferPartner tp
            on tp.TransferID = t.TransferID 
        where {$where}
        group by t.TransferID
        order by t.TransferID desc";

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $transfer = new IntranetTransfer(
            $row->transferID,
            NULL,
            $row->TitleEs,
            $row->TitleEn,
            $row->TitleGl,
            NULL,
            NULL,
            $row->Reference,
            $row->Link,
            $row->National,
            $row->Description,
            $row->TTAType,
            $row->TTAStartDate,
            $row->TTACompanyTarget,
            $row->ReportLink,
            NULL,
            NULL,
            $row->AnnualIncome,
            $row->AnnualCost,
            $row->TTAEndDate,
            $row->IPCoordinatorName,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            NULL,
            $row->partners,
            NULL,
            $row->keywords,
            $row->Comments,
            $row->Validated,
            $row->Published,
            $row->ValidatedBy,
            $row->PublishedBy,
            $row->ValidatedOn,
            $row->PublishedOn
        );
        $transfer->setTransferType($row->TransferTypeID . ";" . $row->TransferTypeName);
        $transfer->setTransferSubtype($row->TransferSubtypeID . ";" . $row->TransferSubtypeName);
    }

    //IP coordinator
    if ($row->IPCoordinatorID != null) {
        $personnel = [];
        foreach ($rows['rows'] as $row) {
            $personnel[$row->IPCoordinatorID] = $row->IPCoordinatorName;
        }
        $transfer->setCoordinatorIgfae($personnel);
    }

    // TRL type
    if ($row->TRLTypeID != null) {
        $trl = [];
        foreach ($rows['rows'] as $row) {
            $trl[$row->TRLTypeID] = $row->TRLTypeName;
        }
        $transfer->setTRLTypeID($trl);
    }

    //Experiemntal collaboration
    if ($row->ExperimentsIDs != null) {
        $experiment = [];
        $ids = explode(" ; ", $row->ExperimentsIDs);
        $nombres = explode(" ; ", $row->Experiments);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $experiment, true) == "") {
                    $experiment[$item] = $name;
                    break;
                } else {
                    $experiment[$item] = "";
                }
            }
        }
        $transfer->setExperimentalCollaboration($experiment);
    }

    //Associated publications
    if ($row->ProductionProjectsIDs != null) {
        $associated = [];
        $ids = explode(" ; ", $row->ProductionProjectsIDs);
        $nombres = explode(" ; ", $row->ProductionProjects);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $associated, true) == "") {
                    $associated[$item] = $name;
                    break;
                } else {
                    $associated[$item] = "";
                }
            }
        }
        $transfer->setAsociatedPublications($associated);
    }

    //Research Area
    if ($row->ResearchAreasIDs != null) {
        $researchAreas = [];
        $ids = explode(" ; ", $row->ResearchAreasIDs);
        $nombres = explode(" ; ", $row->ResearchAreas);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $researchAreas, true) == "") {
                    $researchAreas[$item] = $name;
                    break;
                } else {
                    $researchAreas[$item] = "";
                }
            }
        }
        $transfer->setResearchArea($researchAreas);
    }
    //Research Program
    if ($row->ResearchProgramsIDs != null) {
        $researchProgram = [];
        $ids = explode(" ; ", $row->ResearchProgramsIDs);
        $nombres = explode(" ; ", $row->ResearchPrograms);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $researchProgram, true) == "") {
                    $researchProgram[$item] = $name;
                    break;
                } else {
                    $researchProgram[$item] = "";
                }
            }
        }
        $transfer->setResearchProgram($researchProgram);
    }
    //Research Group
    if ($row->ResearchGroupsIDs != null) {
        $researchGroup = [];
        $ids = explode(" ; ", $row->ResearchGroupsIDs);
        $nombres = explode(" ; ", $row->ResearchGroups);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $researchGroup, true) == "") {
                    $researchGroup[$item] = $name;
                    break;
                } else {
                    $researchGroup[$item] = "";
                }
            }
        }
        $transfer->setResearchGroup($researchGroup);
    }
    //Associated publications
    if ($row->ProductionProjectsIDs != null) {
        $associatedPublications = [];
        $ids = explode(" ; ", $row->ProductionProjectsIDs);
        $nombres = explode(" ; ", $row->ProductionProjects);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $associatedPublications, true) == "") {
                    $associatedPublications[$item] = $name;
                    break;
                } else {
                    $associatedPublications[$item] = "";
                }
            }
        }
        $transfer->setAsociatedPublications($associatedPublications);
    }

    //Work Line
    if ($row->WorkLinesIDs != null) {
        $workLines = [];
        $ids = explode(" ; ", $row->WorkLinesIDs);
        $nombres = explode(" ; ", $row->WorkLines);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $workLines, true) == "") {
                    $workLines[$item] = $name;
                    break;
                } else {
                    $workLines[$item] = "";
                }
            }
        }
        $transfer->setWorkLine($workLines);
    }

    //Transfer personnel
    if ($row->AssociatedPersonnelIDs != null) {
        $associatedUsers = [];
        $ids = explode(" ; ", $row->AssociatedPersonnelIDs);
        $nombres = explode(" ; ", $row->AssociatedPersonnel);
        foreach ($ids as $item) {
            foreach ($nombres as $name) {
                if (array_search($name, $associatedUsers, true) == "") {
                    $associatedUsers[$item] = $name;
                    break;
                } else {
                    $associatedUsers[$item] = "";
                }
            }
        }
        $transfer->setTransferPersonnel($associatedUsers);
    }


    //Recuperamos los valores de request link
    $transferId = $transfer->getTransferID();
    $sql = "SELECT trl.TransferRequestLinkID, trl.RequestLink as RequestLink
    from TransferRequestLink trl
    where 1=1 and trl.TransferID = '$transferId'";

    //Request link
    $rows = igfae_intranet_fetch_data($sql);
    $requestLinks = [];
    foreach ($rows['rows'] as $row) {
        $requestLinks[] = $row->RequestLink;
    }
    $transfer->setRequestLink($requestLinks);


    return $transfer;
}

/**
 * Indíca si el usuario está relacionado con el prototype.
 * @param: $user_id: user
 * @param: $id: id del prototype
 * @param: $mode: modo de búsqueda
 * Si modo == 1 solo si el usuario está relacionado. 
 * Si modo == 2 comprueba si el usuario está relacionado o es el creador
 */
function hasRelatedTransfer($user_id, $id, $mode = 2) {
    
    $sqlHasRelation = "select count(*) 
    from Transfer t
    inner join TransferPersonnel tp
        on tp.TransferID = t.TransferID ";
    
  
    $sqlHasRelation .= " where 1=1 ";
  
    if ($mode == 1) {
        $sqlHasRelation .= "
        and tp.PersonnelID = $user_id
        ";
    } else {
        $sqlHasRelation .= "
        and (tp.PersonnelID = $user_id or t.CreatedBy = $user_id)
        ";
    }
  
    $sqlHasRelation .= "
        and t.TransferID = $id
    ;";
  
    $result = igfae_intranet_get_var($sqlHasRelation);
  
    if ($result) {
        return true;
    } else {
        return false;
    }
  }
  
  function isUserCreatorOrRelatedWithTransfer($user_id){
  
    $idPro = $_GET['idTransfer'];
  
    if (!isset($idPro) || !isset($user_id)) {
        return false;
    }
  
    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedTransfer($id, $idPro)) {
            return true;
        }
    }
  
    return false;
  }
//Comprobamos si esta validada la transferencia
function isTechnologyTransferConsolidated($id) { 
    $sql = "SELECT count(*) 
    from `Transfer` t
    where
    t.Validated = 1
    and t.TransferID = $id";

    $result = igfae_intranet_get_var($sql);

    if ($result) {
    return true;
    } else {
    return false;
    }
}

function existsTransfer($workPlacementId)
{
    $where = "";
    $params = [];

    $where .= " AND t.TransferID= %d";
    $params[] = $workPlacementId;

    $sql = "select t.TransferID as 'id'
            from Transfer t
            WHERE 1=1 {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}