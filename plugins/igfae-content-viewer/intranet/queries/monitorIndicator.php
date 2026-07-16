<?php
function getSelectMonitorsIndicators()
{       
    $sql = "SELECT mi.MonitorIndicatorID,
                   mi.`Indicator` as indicator 
            from MonitorIndicator  mi
            where mi.Validated = 1
            order BY mi.`Indicator` ASC";


    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $monitorIndicator = new IntranetMonitorIndicator();
        $monitorIndicator -> setMonitorIndicatorId($row->MonitorIndicatorID);
        $monitorIndicator -> setIndicator($row->indicator);    
        
        $result[] = $monitorIndicator;
    }

    return $result;
}


function fetchObjectives($offset = 0, $amount = 10, $order = null)
{
    $result = [];

    $order = getOrderObjectives($order);

    $sql = "SELECT 
            mi.MonitorIndicatorID,
            mi.`Indicator` as indicator,
            mi.MonitorTypeID as IndicatorType,
            mt.Description as IndicatorTypeDescription,
            mi.Objective,
            mi.`Date`,
            mi.Value,
            mi.Expectance,
            mi.Published,
            mi.Validated 
        from MonitorIndicator mi 
        inner join MonitorType mt
            on mt.MonitorTypeID = mi.MonitorTypeID 
            order BY {$order}";

    $pagination = getPaginationInfoObjectives($offset, $amount, $sql);

    $sql .= " LIMIT {$offset}, {$amount}";
    $rows = igfae_intranet_fetch_data($sql);

    foreach ($rows['rows'] as $row) {
        $monitorIndicator = new IntranetMonitorIndicator();
        $monitorIndicator -> setMonitorIndicatorId($row->MonitorIndicatorID);
        $monitorIndicator -> setIndicator($row->indicator);
        $monitorIndicator -> setMonitorTypeId($row->IndicatorType);
        $monitorIndicator -> setMonitorTypeDescription($row->IndicatorTypeDescription);
        $monitorIndicator -> setObjective($row->Objective);
        $monitorIndicator -> setDate($row->Date);
        if(isset($row->Value)) {
            $monitorIndicator -> setValue(formatNumberMiles($row->Value));
        }        
        $monitorIndicator -> setExpectance($row->Expectance);
        $monitorIndicator -> setPublished($row->Published);
        $monitorIndicator -> setValidated($row->Validated);    
        
        $result[] = $monitorIndicator;
    }
   
    $info = [
        'objectives' => $result,
        'pagination' => $pagination
    ];

    return $info;
}

function getOrderObjectives($order)
{
    if (!isset($order)) {
        return ' mi.`Indicator` ASC ';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: 
                $sort .= 'mi.`Indicator` ';
                break;
            case 1:  
                $sort .= 'mt.Description ';
                break;
            case 2: 
                $sort .= 'mi.Objective ';
                break;
            case 3: 
                $sort .= 'mi.`Date` ';
                break;
            case 4: 
                $sort .= 'mi.Value ';
                break;
            case 5: 
                $sort .= 'mi.Expectance ';
                break;
            case 6: 
                $sort .= 'mi.Validated ';
                break;
            case 7: 
                $sort .= 'mi.Published ';
                break;            
            default:
                $sort .= 'mi.`Indicator` ';
                break;                                                                                   
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function getPaginationInfoObjectives($offset, $amount, $sql)
{

    $rows = igfae_intranet_fetch_data($sql);
    $result = [
        'draw'    => ($offset/$amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];

    return $result;
}

function saveObjective($datosInsert) {

    $idObjective = $_POST['idObjective'];
    $idNew = null;

    if (isset($idObjective) && strlen($idObjective) > 0) {
         /** START TRANSACTION */
         igfae_intranet_begin_transaction();

         $where = array('MonitorIndicatorID' => $idObjective);
 
         igfae_intranet_update("MonitorIndicator", $datosInsert, $where, null, null);

         // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idObjective);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idObjective);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idObjective);
        }

        // Authorization
        $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idObjective);
        if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
            closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idObjective);
        }

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    } else {
        global $current_user;
        
        $datosInsert['CreatedBy'] = getUserByWPUserID($current_user->ID)[0]["id"];
        
        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        $idNew = igfae_intranet_insert("MonitorIndicator", $datosInsert, null);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idNew);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idNew);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idNew);
        }

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    }

    return $idNew;
     
}

function deleteMonitorIndicator($idMonitorIndicator) {
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();   

    $where = array('MonitorIndicatorID' => $idMonitorIndicator);

    //Eliminamos el proyecto
    $deleted = igfae_intranet_delete("MonitorIndicator", $where);

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idMonitorIndicator);

     /** END TRANSACTION */
     if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }

    return $deleted;
}

function fetchMonitor($monitorRequest, $order = null, $isExport = false)
{
    $offset = 0;
    $amount = 10;
    $monitorResults = [];

    // Obtenemos la lista de tipos de indicador seleccionados por el usuario
    $userMonitorTypesSelected = fetchSelectedIndicatorsTypes($monitorRequest->getObjectives());
    
    // Obtenemos los quarters seleccionados por el usuario
    $quarters = fetchAllSelectedQuarters($monitorRequest->getStartDate(), $monitorRequest->getEndDate());

    // Obtenemos fecha inicial y final de los Quarters seleccionados por el usuario
    $quartersDates = fetchStartDateAndEndDateFromQuarters($monitorRequest->getStartDate(), $monitorRequest->getEndDate());

    //Almacenamos las diferentes sentencias sql en un array
    $sqlArray = [];
    $sqlParams = [];

    //Borrado tabla temporal
    $sqlArray[] = "DROP TEMPORARY TABLE IF EXISTS MonitorCalculatedIndicators";
    $sqlParams[] = [];

    //Borrado tabla temporal
    $sqlArray[] = "DROP TEMPORARY TABLE IF EXISTS MonitorCalculatedValues";
    $sqlParams[] = [];

    // Tabla para los indicadores que se van a calcular
    $sqlArray[] = "CREATE TEMPORARY TABLE MonitorCalculatedIndicators(
        MonitorIndicatorID INT PRIMARY KEY,
        Name VARCHAR(45) NOT NULL
      )";
    $sqlParams[] = [];

    // Tabla con los indicadores calculados
    $sqlArray[] = "CREATE TEMPORARY TABLE MonitorCalculatedValues(
        MonitorIndicatorID INT,
        `Year` SMALLINT NOT NULL,
        `Quarter` TINYINT NOT NULL,
        IndicatorCalculated BIGINT NOT NULL,
        MeasurementDate DATE NULL
     )";
    $sqlParams[] = [];
    
    $sqlArray[] = "CREATE INDEX IX_MonitorCalculatedValues USING BTREE ON MonitorCalculatedValues (MonitorIndicatorID, `Year`, `Quarter`)";
    $sqlParams[] = [];

    // Introducimos los indicadores que se van a calcular en base a la seleccion del usuario (solamente indicadores)
    $paramsPlaceholders = array_fill(0, count($monitorRequest->getObjectives()), '%d');
    $sqlArray[] = "insert into MonitorCalculatedIndicators 
        select 
            mi.MonitorIndicatorID, 
            mt.Name 
        from MonitorIndicator mi
        inner join MonitorType mt
            on mt.MonitorTypeID = mi.MonitorTypeID 
        where 1=1
        -- Indicadores seleccionados por el usuario
        and mi.MonitorIndicatorID IN (" . implode(',', $paramsPlaceholders). " )";
    $sqlParams[] = $monitorRequest->getObjectives();

    // Se calculan los diferentes indicadores con los filtros correspondientes y se insertan
    // "MonitorType.Name=publication.if"
    addQueryMonitorPublicationIf($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=publication.cities"
    addQueryMonitorPublicationCities($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=publication.d1"
    addQueryMonitorPublicationD1($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=publication.q1"
    addQueryMonitorPublicationQ1($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=project.coordinate"
    addQueryMonitorProjectCoordinateOrParticipate($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, "1", IGFAE_INTRANET_MONITOR_TYPE_PROJECT_COORDINATE);
    // "MonitorType.Name=project.participate"
    addQueryMonitorProjectCoordinateOrParticipate($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, "0", IGFAE_INTRANET_MONITOR_TYPE_PROJECT_PARTICIPATE);
    // "MonitorType.Name=students.phd.trained"
    addQueryMonitorStudentsTrained($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=students.phd.active"
    addQueryMonitorStudentsOrPostdocsOrJuniorActive($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, IGFAE_INTRANET_CATEGORY_IGFAE_STUDENTS, IGFAE_INTRANET_MONITOR_TYPE_STUDENTS_PHD_ACTIVE);
    // "MonitorType.Name=postdocs.igfae"
    addQueryMonitorPostdocsIgfae($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=postdocs.active"
    addQueryMonitorStudentsOrPostdocsOrJuniorActive($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, IGFAE_INTRANET_CATEGORY_IGFAE_POSTDOCS, IGFAE_INTRANET_MONITOR_TYPE_POSTDOCS_ACTIVE);
    // "MonitorType.Name=junior.group.leader"
    addQueryMonitorStudentsOrPostdocsOrJuniorActive($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, IGFAE_INTRANET_CATEGORY_IGFAE_NEW_JUNIOR_GROUP_LEADER, IGFAE_INTRANET_MONITOR_TYPE_JUNIOR_GROUP_LEADER);
    // "MonitorType.Name=professionals"
    addQueryMonitorProfessionals($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=professionals.administrative"
    addQueryMonitorProfessionalsAdministrativeOrTechnician($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, false, IGFAE_INTRANET_MONITOR_TYPE_PROFESSIONALS_ADMINISTRATIVE);
    // "MonitorType.Name=professionals.technician"
    addQueryMonitorProfessionalsAdministrativeOrTechnician($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, true, IGFAE_INTRANET_MONITOR_TYPE_PROFESSIONALS_TECHNICIAN);
    // "MonitorType.Name=students.mobility"
    addQueryMonitorStudentsMobility($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=patents.granted"
    addQueryMonitorPatentsGranted($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=transfer.money"
    addQueryMonitorTransferMoney($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=financial.sources.money"
    addQueryMonitorFinancialSourcesMoney($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=international.partners"
    addQueryMonitorInternationalPartners($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=international.igfae"
    addQueryMonitorInternationalIgfae($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);
    // "MonitorType.Name=symposium.attendants"
    addQueryMonitorSymposiumOrOutreachAttendats($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, true, IGFAE_INTRANET_MONITOR_TYPE_SYMPOSIUM_ATTENDANTS);
    // "MonitorType.Name=outreach.attendants"
    addQueryMonitorSymposiumOrOutreachAttendats($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams, false, IGFAE_INTRANET_MONITOR_TYPE_OUTREACH_ATTENDANTS);
    // "MonitorType.Name=outreach.activities"
    addQueryMonitorOutreachActivities($monitorRequest, $userMonitorTypesSelected, $quartersDates, $sqlArray, $sqlParams);

    // Obtencion de los datos finales
    addQueryMonitorFinal($quarters, $sqlArray, $sqlParams, $order);

    // Order de la ultima query
    $rows = igfae_intranet_execute_script($sqlArray, $sqlParams);  
    
    $arrayRows = json_decode(json_encode($rows['rows']), true);  

    //Solo se formatea si se trata de la consulta de busqueda y no para la exportacion
    if(!$isExport) {
        for ($i = 0; $i< count($arrayRows); $i++ ){
            $formatear = false;
            foreach($arrayRows[$i] as $index => $valor) {        
                if ($index == 'Objective'){
                    $formatear = true;
                    continue;
                }
                if ($index == 'Tendencia'){
                    $formatear = false;
                    if (isset($valor)){
                        $arrayRows[$i][$index] = formatNumberOnlyMiles($valor);
                    }            
                    continue;
                }
    
                if ($index == 'Pendiente' && isset($valor)){            
                    $arrayRows[$i][$index] = formatNumberOnlyMiles($valor);
                    continue;
                }
    
                if($formatear) {           
                    $arrayRows[$i][$index] = formatNumberOnlyMiles($valor);
                   
                }
            }
        }
    }

    
    $rows = json_decode(json_encode($arrayRows));

    $data = [
        'rows' => $rows
    ];
    
    return $data;
}

function fetchAllSelectedQuarters($quarterIdStart, $quarterIdEnd)
{
    $result = [];

    $sql = "select concat('Y', q.`Year`, 'Q', q.Quarter) as `Date`
        from Quarter q
        where 1=1 
        and q.QuarterID >= %d
        and q.QuarterID <= %d
        order by q.`Year`, q.Quarter";
    $params[] = $quarterIdStart;
    $params[] = $quarterIdEnd;

    $rows = igfae_intranet_fetch_data($sql, $params);

    foreach ($rows['rows'] as $row) {
        $result[] = $row->Date;
    }
   
    return $result;
}

function fetchStartDateAndEndDateFromQuarters($quarterIdStart, $quarterIdEnd)
{
    $result = [];

    $sql = "select 
	        min(q.StartDate) as StartDate, 
	        max(q.EndDate) as EndDate
        from Quarter q 
        where q.QuarterID in (%d, %d)";
    $params[] = $quarterIdStart;
    $params[] = $quarterIdEnd;

    $rows = igfae_intranet_fetch_data($sql, $params);

    foreach ($rows['rows'] as $row) {
        $result["StartDate"] = $row->StartDate;
        $result["EndDate"] = $row->EndDate;
    }
   
    return $result;
}

function fetchSelectedIndicatorsTypes($monitorIndicatorIds) {
    $result = [];

    $paramsPlaceholders = array_fill(0, count($monitorIndicatorIds), '%d');
    $sql = "select mt.Name 
        from MonitorIndicator mi 
        inner join MonitorType mt
        on mi.MonitorTypeID = mt.MonitorTypeID 
        where mi.MonitorIndicatorID IN (" . implode(',', $paramsPlaceholders). " )";
    $rows = igfae_intranet_fetch_data($sql, $monitorIndicatorIds);

    foreach ($rows['rows'] as $row) {
        $result[] = $row->Name;
    }
   
    return $result;   
}

function addQueryMonitorPublicationIf($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_IF, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorGenericFilter($monitorRequest);
        $where = $queryPrepare['where'];

        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_IF ."') as MonitorIndicatorID,
                    year(pp.`Date`) as `Year`,
                    quarter(pp.`Date`) as `Quarter`,
                    -- IF > 5
                    sum(if((ifa.ImpactFactor > 5), 1, 0)) AS IndicatorCalculated,
                    max(pp.UpdateDate) as UpdateDate
                from JournalArea ja 
                inner join Classification c
                    on c.JournalAreaID = ja.JournalAreaID
                inner join ImpactFactorInClassification ific
                    on ific.ClassificationID = c.ClassificationID 
                inner join ImpactFactor ifa
                    on ifa.ImpactFactorID = ific.ImpactFactorID 
                inner join Journal j
                    on j.JournalID = ifa.JournalID 
                inner join (
                    select distinct 
                        pp.ProductionProjectID, 
                        pp.`Date`, 
                        pp.JournalID,
                        -- Fecha mas reciente de actualizacion
                        max(md.MeasurementDate) as UpdateDate
                    from ProductionProject pp
                    left join ProductionProjectResearchArea ppra
                        on ppra.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectResearchProgram pprp
                        on pprp.ProductionProjectID = pp.ProductionProjectID 
                    left join ExperimentalCollaborationProductionProject ecpp
                        on ecpp.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectResearchGroup pprg
                        on pprg.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectAuthor ppa
                        on ppa.ProductionProjectID = pp.ProductionProjectID
                    left join ProductionProjectCites ppc
                        on ppc.ProductionProjectID = pp.ProductionProjectID 
                    left join MeasurementDate md
                        on md.MeasurementDateID = ppc.MeasurementDateID
                    where 1=1
                    -- Filtros estaticos
                    and pp.ActivityTypeID = " . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE . "
                    -- Filtros variables
                    {$where}
                    group by pp.ProductionProjectID
                ) pp
                    on pp.JournalID = j.JournalID 
                where 1=1
                -- Filtros estaticos
                and ja.SourceID = {$monitorRequest->getSource()}
                and (
                    year(pp.`Date`) = c.`Year`
                    or (
                        c.LastYear = 1 and (year(pp.`Date`) > c.`Year`)
                    )
                )
                -- Filtros variables
                -- Filtro de fechas (trimestres)
                and pp.`Date` >= '{$quartersDates["StartDate"]}' -- Fecha inicio del trimestre
                and pp.`Date` <= '{$quartersDates["EndDate"]}' -- Fecha fin del trimestre
                group by concat(year(pp.`Date`), quarter(pp.`Date`))";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function getPrepareQueryMonitorGenericFilter($monitorRequest) {
    $where = " ";
    $params = [];

    // Research area input
    if ($monitorRequest->getResearchArea()) {
        $typesCount = count($monitorRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (ppra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $monitorRequest->getResearchArea());
    }

    // Research program input
    if ($monitorRequest->getResearchProgram()) {
        $typesCount = count($monitorRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (pprp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $monitorRequest->getResearchProgram());
    }

    // Collaboration input
    if ($monitorRequest->getCollaborationExperiment()) {
        $typesCount = count($monitorRequest->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ecpp.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $monitorRequest->getCollaborationExperiment());
    }

    // Research group input
    if ($monitorRequest->getResearchGroup()) {
        $typesCount = count($monitorRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (pprg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $monitorRequest->getResearchGroup());
    }

    // Authors input
    if ($monitorRequest->getResearchs()) {
        $typesCount = count($monitorRequest->getResearchs());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (ppa.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $monitorRequest->getResearchs());
    }

    // Source
    if ($monitorRequest->getSource()) {
        $where .= " AND (ppc.SourceID = %d)";
        $params[] = $monitorRequest->getSource();
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function indicadorNeedsValue($monitorIndicatorIds) {
    $result = 0;

    $paramsPlaceholders = array_fill(0, count($monitorIndicatorIds), '%d');
    $sql = " select count(*) as cantidad
            from MonitorIndicator mi
            inner join MonitorType mt
                on mi.MonitorTypeID = mt.MonitorTypeID 
            where mi.MonitorIndicatorID IN (" . implode(',', $paramsPlaceholders). " )
            and mt.WithValue = 1";
    $rows = igfae_intranet_fetch_data($sql, $monitorIndicatorIds);

    foreach ($rows['rows'] as $row) {
        $result = $row->cantidad;
    }
   
    return $result;   
}


function indicadorNeedsSource($monitorIndicatorIds) {
    $result = 0;

    $paramsPlaceholders = array_fill(0, count($monitorIndicatorIds), '%d');
    $sql = " select count(*) as cantidad
            from MonitorIndicator mi
            inner join MonitorType mt
                on mi.MonitorTypeID = mt.MonitorTypeID 
            where mi.MonitorIndicatorID IN (" . implode(',', $paramsPlaceholders). " )
            and mt.WithSource = 1";
    $rows = igfae_intranet_fetch_data($sql, $monitorIndicatorIds);

    foreach ($rows['rows'] as $row) {
        $result = $row->cantidad;
    }
   
    return $result;   
}

function addQueryMonitorFinal($quarters, &$sqlArray, &$sqlParams, $order = NULL) {
    $order = getOrderMonitorFinal($order);
    $sql = "select 
        @n := @n + 1 AS NumberCount,
        b.`Indicator`,
        b.Objective,";
    // Es necesario introducir esta parte del SELECT de forma dinamica en PHP con los diferentes intervalos seleccionados por el usuario
    foreach ($quarters as $quarter) {
    $sql .= "b." . $quarter . ",";
    }
    $sql .= "   b.Tendencia,
        if (b.Value - b.Tendencia < 0, 0, b.Value - b.Tendencia) as Pendiente,
        b.Expectance ,
        b.MeasurementDate
    from 
    (
        select 
            mi.`Indicator`,
            mi.Objective,
            a.MonitorIndicatorID,
            a.MeasurementDate,";
    // Es necesario introducir esta parte del SELECT de forma dinamica en PHP con los diferentes intervalos seleccionados por el usuario
    foreach ($quarters as $quarter) {
    $sql .= "coalesce(sum(a." . $quarter . "),0) as " . $quarter . ",";
    }
    $sql .= "       (";
    // Es necesario introducir esta parte del SELECT de forma dinamica en PHP con los diferentes intervalos seleccionados por el usuario
    foreach ($quarters as $quarter) {
    $sql .= "coalesce(sum(a." . $quarter . "),0)+";
    }
    // Eliminamos la ultima coma
    $sql = substr($sql, 0, -1);
    $sql .= "       ) as Tendencia,
            mi.Value,
            mi.Expectance 
        from MonitorCalculatedIndicators mci
        inner join MonitorIndicator mi
            on mi.MonitorIndicatorID = mci.MonitorIndicatorID 
        left join (
            select
                mcv.MonitorIndicatorID,
                mcv.`Year`,
                mcv.`Quarter`,
                mcv.MeasurementDate,";
    // Es necesario introducir esta parte del SELECT de forma dinamica en PHP con los diferentes intervalos seleccionados por el usuario
    foreach ($quarters as $quarter) {
    $sql .= "case when `Year`=" . substr($quarter, 1, 4) . " and `Quarter`=" . substr($quarter, 6, 1) . " then mcv.IndicatorCalculated end as " . $quarter . ",";
    }
    // Eliminamos la ultima coma
    $sql = substr($sql, 0, -1);
    $sql .= "        from MonitorCalculatedValues mcv
        ) a
        on mci.MonitorIndicatorID = a.MonitorIndicatorID
        group by mi.MonitorIndicatorID
    ) b,
    ( SELECT  @n := 0) as parameter
    ORDER BY {$order}";
    $sqlArray[] = $sql;
    $sqlParams[] = [];
}

function getOrderMonitorFinal($order) {
    if (!isset($order)) {
        return ' 1 ASC';
    } else {
        return " " . $order[0]["column"] + 1 . " " . $order[0]["dir"];
    }
}

function addQueryMonitorPublicationCities($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_CITIES, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorGenericFilter($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_CITIES ."') as MonitorIndicatorID,
                    year(pp.`Date`) as `Year`,
                    quarter(pp.`Date`) as `Quarter`,
                    -- Cities > 10
                    sum(if((ppc.Cites > 10), 1, 0)) AS IndicatorCalculated,
                    max(ppAux.UpdateDate) as UpdateDate
                from (
                    select distinct 
                        pp.ProductionProjectID, 
                        pp.`Date`, 
                        -- Fecha mas reciente de actualizacion
                        max(md.MeasurementDate) as UpdateDate
                    from ProductionProject pp
                    inner join ProductionProjectCites ppc
                        on ppc.ProductionProjectID = pp.ProductionProjectID 
                    inner join MeasurementDate md
                        on md.MeasurementDateID = ppc.MeasurementDateID
                    left join ProductionProjectResearchArea ppra
                        on ppra.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectResearchProgram pprp
                        on pprp.ProductionProjectID = pp.ProductionProjectID 
                    left join ExperimentalCollaborationProductionProject ecpp
                        on ecpp.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectResearchGroup pprg
                        on pprg.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectAuthor ppa
                        on ppa.ProductionProjectID = pp.ProductionProjectID
                    where 1=1
                    -- Filtros estaticos
                    and pp.ActivityTypeID = " . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE . "
                    -- Filtros variables
                    {$where}
                    group by pp.ProductionProjectID
                ) ppAux
                inner join ProductionProject pp
                    on pp.ProductionProjectID = ppAux.ProductionProjectID
                inner join ProductionProjectCites ppc
                    on ppc.ProductionProjectID = pp.ProductionProjectID 
                inner join MeasurementDate md
                    on md.MeasurementDateID = ppc.MeasurementDateID 
                    and md.MeasurementDate = ppAux.UpdateDate
                where 1=1
                -- Filtros variables
                -- Filtro de fechas (trimestres)
                and pp.`Date` >= '{$quartersDates["StartDate"]}' -- Fecha inicio del trimestre
                and pp.`Date` <= '{$quartersDates["EndDate"]}' -- Fecha fin del trimestre
                group by concat(year(pp.`Date`), quarter(pp.`Date`))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function addQueryMonitorPublicationD1($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_D1, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorGenericFilter($monitorRequest);
        $where = $queryPrepare['where'];
        $sql ="insert into MonitorCalculatedValues
            select 
                (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_D1 ."') as MonitorIndicatorID,
                year(b.PublishDate) as `Year`,
                quarter(b.PublishDate) as `Quarter`,
                count(*) AS IndicatorCalculated,
                max(b.UpdateDate) as UpdateDate
            from (
                select distinct
                    a.ProductionProjectID,
                    a.PublishDate,
                    -- Fecha mas reciente de actualizacion
                    a.UpdateDate
                from (
                    select distinct
                        pp.ProductionProjectID,
                        COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                        pp.DOI,
                        pp.`Date` as publishDate,
                        -- Fecha mas reciente de actualizacion
                        max(md.MeasurementDate) as UpdateDate
                    from ProductionProject pp
                    left join ProductionProjectLanguage pplen
                        on pplen.ProductionProjectID = pp.ProductionProjectID 
                        and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
                    left join ProductionProjectLanguage pples
                        on pples.ProductionProjectID = pp.ProductionProjectID 
                        and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
                    left join ProductionProjectLanguage pplgl
                        on pplgl.ProductionProjectID = pp.ProductionProjectID 
                        and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
                    left join ProductionProjectResearchArea ppra
                        on ppra.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectResearchProgram pprp
                        on pprp.ProductionProjectID = pp.ProductionProjectID 
                    left join ExperimentalCollaborationProductionProject ecpp
                        on ecpp.ProductionProjectID = pp.ProductionProjectID 
                    left join ExperimentalCollaboration ec
                        on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID
                    left join ProductionProjectResearchGroup pprg
                        on pprg.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectAuthor ppa
                        on ppa.ProductionProjectID = pp.ProductionProjectID
                    left join ProductionProjectCites ppc
                        on ppc.ProductionProjectID = pp.ProductionProjectID 
                    left join MeasurementDate md
                        on md.MeasurementDateID = ppc.MeasurementDateID 
                    inner join Journal j
                        on pp.JournalID = j.JournalID 
                    inner join JournalArea ja
                        on ja.JournalAreaID = j.JournalAreaID 
                    inner join Classification c
                        on c.JournalAreaID = ja.JournalAreaID 
                    inner join ImpactFactorInClassification ific
                        on ific.ClassificationID = c.ClassificationID 
                    inner join ImpactFactor ifa
                        on ifa.ImpactFactorID = ific.ImpactFactorID 
                        and ifa.JournalID = j.JournalID 
                    where 1=1
                    -- Filtros obligatorios
                    and pp.ActivityTypeID in (" . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE . "," . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES . ")
                    and (
                        if(
                            (c.`Year` = year(pp.`Date` )),
                            1,
                            if(
                                (
                                    (c.LastYear = 1)
                                    and (
                                        year(pp.`Date`) > c.`Year`
                                    )
                                ),
                                1,
                                0
                            )
                        ) = 1
                    )
                    and (
                        (1 - 
                            (ifa.`Order` / c.NumJournalsInArea)
                        ) * 100
                    ) > 90
                    -- Filtros opcionales
                    {$where}
                    and pp.`Date` >= '{$quartersDates["StartDate"]}' -- Fecha inicio del trimestre
                    and pp.`Date` <= '{$quartersDates["EndDate"]}' -- Fecha fin del trimestre
                    group by pp.ProductionProjectID
                ) a
            ) b
            group by concat(year(b.PublishDate), quarter(b.PublishDate))
            order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function addQueryMonitorPublicationQ1($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_Q1, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorGenericFilter($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
            select 
                (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_PUBLICATION_Q1 ."') as MonitorIndicatorID,
                year(b.PublishDate) as `Year`,
                quarter(b.PublishDate) as `Quarter`,
                count(*) AS IndicatorCalculated,
                max(b.UpdateDate) as UpdateDate
            from (
                select distinct
                    a.ProductionProjectID,
                    a.PublishDate,
                    -- Fecha mas reciente de actualizacion
                    a.UpdateDate
                from (
                    select distinct
                        pp.ProductionProjectID,
                        COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                        pp.DOI,
                        pp.`Date` as publishDate,
                        -- Fecha mas reciente de actualizacion
                        max(md.MeasurementDate) as updateDate
                    from ProductionProject pp
                    left join ProductionProjectLanguage pplen
                        on pplen.ProductionProjectID = pp.ProductionProjectID 
                        and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
                    left join ProductionProjectLanguage pples
                        on pples.ProductionProjectID = pp.ProductionProjectID 
                        and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
                    left join ProductionProjectLanguage pplgl
                        on pplgl.ProductionProjectID = pp.ProductionProjectID 
                        and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
                    left join ProductionProjectResearchArea ppra
                        on ppra.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectResearchProgram pprp
                        on pprp.ProductionProjectID = pp.ProductionProjectID 
                    left join ExperimentalCollaborationProductionProject ecpp
                        on ecpp.ProductionProjectID = pp.ProductionProjectID 
                    left join ExperimentalCollaboration ec
                        on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID
                    left join ProductionProjectResearchGroup pprg
                        on pprg.ProductionProjectID = pp.ProductionProjectID 
                    left join ProductionProjectAuthor ppa
                        on ppa.ProductionProjectID = pp.ProductionProjectID
                    left join ProductionProjectCites ppc
                        on ppc.ProductionProjectID = pp.ProductionProjectID 
                    left join MeasurementDate md
                        on md.MeasurementDateID = ppc.MeasurementDateID 
                    inner join Journal j
                        on pp.JournalID = j.JournalID 
                    inner join JournalArea ja
                        on ja.JournalAreaID = j.JournalAreaID 
                    inner join Classification c
                        on c.JournalAreaID = ja.JournalAreaID 
                    inner join ImpactFactorInClassification ific
                        on ific.ClassificationID = c.ClassificationID 
                    inner join ImpactFactor ifa
                        on ifa.ImpactFactorID = ific.ImpactFactorID 
                        and ifa.JournalID = j.JournalID 
                    where 1=1
                    -- Filtros obligatorios
                    and pp.ActivityTypeID in (" . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE . "," . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES . ")
                    and (
                        if(
                            (c.`Year` = year(pp.`Date` )),
                            1,
                            if(
                                (
                                    (c.LastYear = 1)
                                    and (
                                        year(pp.`Date`) > c.`Year`
                                    )
                                ),
                                1,
                                0
                            )
                        ) = 1
                    )
                    and (
                        (1 - 
                            (ifa.`Order` / c.NumJournalsInArea)
                        ) * 100
                    ) > 75
                    -- Filtros opcionales
                    {$where}
                    and pp.`Date` >= '{$quartersDates["StartDate"]}' -- Fecha inicio del trimestre
                    and pp.`Date` <= '{$quartersDates["EndDate"]}' -- Fecha fin del trimestre
                    group by pp.ProductionProjectID
                ) a
            ) b
            group by concat(year(b.PublishDate), quarter(b.PublishDate))
        order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function addQueryMonitorProjectCoordinateOrParticipate($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams, $coordinate, $type) {
    if (in_array($type, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorResearchProject($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='{$type}') as MonitorIndicatorID,
                    year(p.ConcessionDate) as `Year`,
                    quarter(p.ConcessionDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from Project p
                left join ProjectResearchArea pra
                    on pra.ProjectID = p.ProjectID 
                left join ProjectResearchProgram prp
                    on prp.ProjectID = p.ProjectID 
                left join ExperimentalCollaborationProject ecp
                    on ecp.ProjectID = p.ProjectID 
                left join ProjectPersonnel pp
                    on pp.ProjectID = p.ProjectID 
                where 1=1
                -- Filtros estaticos
                and p.ScopeID = " . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE . "
                and p.IGFAELeader = " . $coordinate . "
                and p.ConcessionDate is not null
                -- Filtro dinamicos
                {$where}
                and p.ConcessionDate >= '{$quartersDates["StartDate"]}' -- Fecha inicio del trimestre
                and p.ConcessionDate <= '{$quartersDates["EndDate"]}' -- Fecha fin del trimestre
                group by concat(year(p.ConcessionDate), quarter(p.ConcessionDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function getPrepareQueryMonitorResearchProject($monitorRequest) {
    $where = " ";
    $params = [];

    // Research area input
    if ($monitorRequest->getResearchArea()) {
        $typesCount = count($monitorRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (pra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $monitorRequest->getResearchArea());
    }

    // Research program input
    if ($monitorRequest->getResearchProgram()) {
        $typesCount = count($monitorRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (prp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $monitorRequest->getResearchProgram());
    }

    // Collaboration input
    if ($monitorRequest->getCollaborationExperiment()) {
        $typesCount = count($monitorRequest->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ecp.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $monitorRequest->getCollaborationExperiment());
    }

    // Research group input
    if ($monitorRequest->getResearchGroup()) {
        $typesCount = count($monitorRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (p.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $monitorRequest->getResearchGroup());
    }

    // Authors input
    if ($monitorRequest->getResearchs()) {
        $typesCount = count($monitorRequest->getResearchs());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (pp.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $monitorRequest->getResearchs());
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function addQueryMonitorStudentsTrained($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_STUDENTS_PHD_TRAINED, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorWorkPlacement($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_STUDENTS_PHD_TRAINED ."') as MonitorIndicatorID,
                    year(wp.StartDate) as `Year`,
                    quarter(wp.StartDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from WorkPlacement wp
                inner join Personnel displaced
                    on displaced.PersonnelID = wp.DisplacedResearcherID 
                inner join PersonnelCategory pc
                    on pc.PersonnelID = displaced.PersonnelID 
                left join Personnel tutor
                    on tutor.PersonnelID = wp.TutorID 
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
                where 1=1
                -- Filtros estaticos
                and pc.CategoryID = " . IGFAE_INTRANET_CATEGORY_IGFAE_STUDENTS . " -- Students
                -- Filtros dinamicos
                {$where}
                and wp.StartDate >= '{$quartersDates["StartDate"]}' -- Fecha inicio del trimestre
                and wp.StartDate <= '{$quartersDates["EndDate"]}' -- Fecha fin del trimestre
                group by concat(year(wp.StartDate), quarter(wp.StartDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}


function getPrepareQueryMonitorWorkPlacement($monitorRequest) {
    $where = " ";
    $params = [];

    // Research area input
    if ($monitorRequest->getResearchArea()) {
        $typesCount = count($monitorRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (wpra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $monitorRequest->getResearchArea());
    }

    // Research program input
    if ($monitorRequest->getResearchProgram()) {
        $typesCount = count($monitorRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (wprp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $monitorRequest->getResearchProgram());
    }

    // Collaboration input
    if ($monitorRequest->getCollaborationExperiment()) {
        $typesCount = count($monitorRequest->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (wpe.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $monitorRequest->getCollaborationExperiment());
    }

    // Research group input
    if ($monitorRequest->getResearchGroup()) {
        $typesCount = count($monitorRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (wprg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $monitorRequest->getResearchGroup());
    }

    // Authors input
    if ($monitorRequest->getResearchs()) {
        $typesCount = count($monitorRequest->getResearchs());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (wp.TutorID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $monitorRequest->getResearchs());
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function addQueryMonitorStudentsOrPostdocsOrJuniorActive($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams, $category, $type) {
    if (in_array($type, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorPersonnel($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='{$type}') as MonitorIndicatorID,
                    q.`Year`,
                    q.Quarter,
                    count(*) AS IndicatorCalculated,
                    NULL
                from Personnel p 
                left join PersonnelResearchArea pra
                    on pra.PersonnelID = p.PersonnelID 
                left join ResearchArea ra
                    on ra.ResearchAreaID = pra.ResearchAreaID 
                left join PersonnelResearchProgram prp
                    on prp.PersonnelID = p.PersonnelID 
                left join PersonnelWorkLine pwl
                    on pwl.PersonnelID = p.PersonnelID 
                left join PersonnelResearchGroup prg
                    on prg.PersonnelID = p.PersonnelID 
                left join PersonnelExperiment pe
                    on pe.PersonnelID = p.PersonnelID 
                left join ExperimentalCollaboration ec
                    on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
                left join PersonnelCategory pcigfae
                    on pcigfae.PersonnelID = p.PersonnelID
                -- 	and (pcigfae.EndDate is null or pcigfae.EndDate >= CURRENT_DATE())
                inner join PersonnelContract pco
                    on pco.PersonnelID = p.PersonnelID 
                -- 	and (pco.EndDate is null or pco.EndDate >= CURRENT_DATE()) 
                , Quarter q
                where 1=1
                -- Filtros estaticos
                and pcigfae.CategoryID = {$category}
                and (
                    (q.StartDate BETWEEN pco.StartDate and pco.EndDate) or 
                    (pco.EndDate is null and pco.StartDate <= q.EndDate)
                )
                -- Filtros dinamicos
                {$where}
                and pco.StartDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
	            and pco.EndDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(q.`Year`, q.Quarter)
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function getPrepareQueryMonitorPersonnel($monitorRequest) {
    $where = " ";
    $params = [];

    // Research area input
    if ($monitorRequest->getResearchArea()) {
        $typesCount = count($monitorRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (pra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $monitorRequest->getResearchArea());
    }

    // Research program input
    if ($monitorRequest->getResearchProgram()) {
        $typesCount = count($monitorRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (prp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $monitorRequest->getResearchProgram());
    }

    // Collaboration input
    if ($monitorRequest->getCollaborationExperiment()) {
        $typesCount = count($monitorRequest->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (pe.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $monitorRequest->getCollaborationExperiment());
    }

    // Research group input
    if ($monitorRequest->getResearchGroup()) {
        $typesCount = count($monitorRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (prg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $monitorRequest->getResearchGroup());
    }

    // Authors input
    if ($monitorRequest->getResearchs()) {
        $typesCount = count($monitorRequest->getResearchs());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (p.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $monitorRequest->getResearchs());
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function addQueryMonitorPostdocsIgfae($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_POSTDOCS_IGFAE, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorPersonnel($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_POSTDOCS_IGFAE ."') as MonitorIndicatorID,
                    q.`Year`,
                    q.Quarter,
                    count(*) AS IndicatorCalculated,
                    NULL
                from Personnel p 
                inner join EventPersonnel ep
                    on ep.PersonnelID = p.PersonnelID 
                    and ep.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_AS . " -- Asistente
                left join PersonnelResearchArea pra
                    on pra.PersonnelID = p.PersonnelID 
                left join ResearchArea ra
                    on ra.ResearchAreaID = pra.ResearchAreaID 
                left join PersonnelResearchProgram prp
                    on prp.PersonnelID = p.PersonnelID 
                left join PersonnelWorkLine pwl
                    on pwl.PersonnelID = p.PersonnelID 
                left join PersonnelResearchGroup prg
                    on prg.PersonnelID = p.PersonnelID 
                left join PersonnelExperiment pe
                    on pe.PersonnelID = p.PersonnelID 
                left join ExperimentalCollaboration ec
                    on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
                left join PersonnelCategory pcigfae
                    on pcigfae.PersonnelID = p.PersonnelID
                -- 	and (pcigfae.EndDate is null or pcigfae.EndDate >= CURRENT_DATE())
                inner join PersonnelContract pco
                    on pco.PersonnelID = p.PersonnelID 
                -- 	and (pco.EndDate is null or pco.EndDate >= CURRENT_DATE()) 
                , Quarter q
                where 1=1
                -- Filtros estaticos
                and pcigfae.CategoryID = " . IGFAE_INTRANET_CATEGORY_IGFAE_POSTDOCS . " -- Postdocs
                and (
                    (q.StartDate BETWEEN pco.StartDate and pco.EndDate) or 
                    (pco.EndDate is null and pco.StartDate <= q.EndDate)
                )
                -- Filtros dinamicos
                {$where}
                and pco.StartDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                and pco.EndDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(q.`Year`, q.Quarter)
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

/**
 * Verifica que el conjunto de indicadores de entrada tengan todos diferentes tipos
 * Devuelve > 0 si hay mas de un indicador con el mismo tipo
 */
function objectivesUniqueTypes($monitorIndicatorIds) {
    $result = 0;

    $paramsPlaceholders = array_fill(0, count($monitorIndicatorIds), '%d');
    $sql = "select count(*) as cantidad
            from (
                select distinct MonitorTypeID, count(*)
                from MonitorIndicator mi 
                where mi.MonitorIndicatorID IN (" . implode(',', $paramsPlaceholders). " )
                group by MonitorTypeID 
                having count(*)>1
            ) a";
    $rows = igfae_intranet_fetch_data($sql, $monitorIndicatorIds);

    foreach ($rows['rows'] as $row) {
        $result = $row->cantidad;
    }
   
    return $result;   
}

function addQueryMonitorProfessionals($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_PROFESSIONALS, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorPersonnel($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_PROFESSIONALS ."') as MonitorIndicatorID,
                    year(pco.StartDate) as `Year`,
                    quarter(pco.StartDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from Personnel p 
                left join PersonnelResearchArea pra
                    on pra.PersonnelID = p.PersonnelID 
                left join ResearchArea ra
                    on ra.ResearchAreaID = pra.ResearchAreaID 
                left join PersonnelResearchProgram prp
                    on prp.PersonnelID = p.PersonnelID 
                left join PersonnelWorkLine pwl
                    on pwl.PersonnelID = p.PersonnelID 
                left join PersonnelResearchGroup prg
                    on prg.PersonnelID = p.PersonnelID 
                left join PersonnelExperiment pe
                    on pe.PersonnelID = p.PersonnelID 
                left join ExperimentalCollaboration ec
                    on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
                inner join PersonnelContract pco
                    on pco.PersonnelID = p.PersonnelID 
                -- 	and (pco.EndDate is null or pco.EndDate >= CURRENT_DATE()) 
                where 1=1
                -- Filtros dinamicos
                {$where}
                and pco.StartDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                and pco.EndDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(year(pco.StartDate), quarter(pco.StartDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function addQueryMonitorProfessionalsAdministrativeOrTechnician($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams, $technician, $type) {
    if (in_array($type, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorPersonnel($monitorRequest);
        $where = $queryPrepare['where'];
        $categoriasFilter = NULL;
        if (!$technician) {
            $categoriasFilter = array(IGFAE_INTRANET_CATEGORY_XUNTA_MANAGEMENT_DIRECTION, IGFAE_INTRANET_CATEGORY_XUNTA_MANAGEMENT_TECHNICIAN, IGFAE_INTRANET_CATEGORY_XUNTA_MANAGEMENT_ADMINISTRATION);
        } else {
            $categoriasFilter = array(IGFAE_INTRANET_CATEGORY_XUNTA_TECHNICIAN_LICENCED, IGFAE_INTRANET_CATEGORY_XUNTA_TECHNICIAN_GRADUATED, IGFAE_INTRANET_CATEGORY_XUNTA_TECHNICIAN_FP);
        }
        $paramsPlaceholders = array_fill(0, count($categoriasFilter), '%d');

        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='{$type}') as MonitorIndicatorID,
                    year(pco.StartDate) as `Year`,
                    quarter(pco.StartDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from Personnel p 
                left join PersonnelResearchArea pra
                    on pra.PersonnelID = p.PersonnelID 
                left join ResearchArea ra
                    on ra.ResearchAreaID = pra.ResearchAreaID 
                left join PersonnelResearchProgram prp
                    on prp.PersonnelID = p.PersonnelID 
                left join PersonnelWorkLine pwl
                    on pwl.PersonnelID = p.PersonnelID 
                left join PersonnelResearchGroup prg
                    on prg.PersonnelID = p.PersonnelID 
                left join PersonnelExperiment pe
                    on pe.PersonnelID = p.PersonnelID 
                left join ExperimentalCollaboration ec
                    on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
                left join PersonnelCategory pcxunta
                    on pcxunta.PersonnelID = p.PersonnelID
                -- 	and (pcxunta.EndDate is null or pcxunta.EndDate >= CURRENT_DATE())
                left join Category cxunta
                    on cxunta.CategoryID = pcxunta.CategoryID 
                    and cxunta.CategoryTypeID = " . IGFAE_INTRANET_CATEGORY_TYPE_XUNTA . "
                inner join PersonnelContract pco
                    on pco.PersonnelID = p.PersonnelID 
                -- 	and (pco.EndDate is null or pco.EndDate >= CURRENT_DATE()) 
                where 1=1
                -- Filtros estaticos
                and cxunta.CategoryID IN (" . implode(',', $paramsPlaceholders). " )
                {$where}
                and pco.StartDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                and pco.EndDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(year(pco.StartDate), quarter(pco.StartDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = array_merge($queryPrepare['params'], $categoriasFilter);
    }
}

function addQueryMonitorStudentsMobility($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_STUDENTS_MOBILITY, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorWorkPlacement($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_STUDENTS_MOBILITY ."') as MonitorIndicatorID,
                    year(wp.StartDate) as `Year`,
                    quarter(wp.StartDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from WorkPlacement wp
                inner join Personnel displaced
                    on displaced.PersonnelID = wp.DisplacedResearcherID 
                left join Personnel tutor
                    on tutor.PersonnelID = wp.TutorID 
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
                where 1=1
                {$where}
                and wp.StartDate >= '{$quartersDates["StartDate"]}' -- Fecha inicio del trimestre
                and  wp.StartDate <= '{$quartersDates["EndDate"]}' -- Fecha fin del trimestre
                group by concat(year(wp.StartDate), quarter(wp.StartDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function addQueryMonitorPatentsGranted($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_PATENTS_GRANTED, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorTransfer($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_PATENTS_GRANTED ."') as MonitorIndicatorID,
                    year(t.TTAStartDate) as `Year`,
                    quarter(t.TTAStartDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from Transfer t 
                inner join TransferType tt
                    on tt.TransferTypeID = t.TransferTypeID 
                left join TransferSubtype ts
                    on ts.TransferTypeID = t.TransferTypeID 
                    and ts.TransferSubtypeID = t.TransferSubtypeID 
                left join TransferPersonnel tpIPCoordinator
                    on tpIPCoordinator.TransferID = t.TransferID 
                    and tpIPCoordinator.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . " -- IPCoordinador
                left join Personnel pIPCoordinator
                    on pIPCoordinator.PersonnelID = tpIPCoordinator.PersonnelID 
                left join TransferPersonnel tpAssociated
                    on tpAssociated.TransferID = t.TransferID 
                    and tpAssociated.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_II . " -- Investigador implicado
                left join TransferResearchArea tra
                    on tra.TransferID = t.TransferID 
                left join TransferResearchProgram trp
                    on trp.TransferID = t.TransferID 
                left join TransferResearchGroup trg
                    on trg.TransferID = t.TransferID 
                left join TransferWorkline twl
                    on twl.TransferID = t.TransferID 
                left join TransferExperiment te
                    on te.TransferID = t.TransferID 
                where 1=1
                {$where}
                and t.TTAStartDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(year(t.TTAStartDate), quarter(t.TTAStartDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function getPrepareQueryMonitorTransfer($monitorRequest) {
    $where = " ";
    $params = [];

    // Research area input
    if ($monitorRequest->getResearchArea()) {
        $typesCount = count($monitorRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (tra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $monitorRequest->getResearchArea());
    }

    // Research program input
    if ($monitorRequest->getResearchProgram()) {
        $typesCount = count($monitorRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (trp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $monitorRequest->getResearchProgram());
    }

    // Collaboration input
    if ($monitorRequest->getCollaborationExperiment()) {
        $typesCount = count($monitorRequest->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (te.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $monitorRequest->getCollaborationExperiment());
    }

    // Research group input
    if ($monitorRequest->getResearchGroup()) {
        $typesCount = count($monitorRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (trg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $monitorRequest->getResearchGroup());
    }

    // Authors input
    if ($monitorRequest->getResearchs()) {
        $typesCount = count($monitorRequest->getResearchs());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (tpIPCoordinator.PersonnelID IN ({$placeholdersAuthors}) OR tpAssociated.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $monitorRequest->getResearchs());
        $params = array_merge($params, $monitorRequest->getResearchs());
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function addQueryMonitorTransferMoney($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_TRANSFER_MONEY, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorTransfer($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_TRANSFER_MONEY ."') as MonitorIndicatorID,
                    year(t.TTAStartDate) as `Year`,
                    quarter(t.TTAStartDate) as `Quarter`,
                    sum(t.AnnualIncome) AS IndicatorCalculated,
                    NULL
                from Transfer t 
                inner join TransferType tt
                    on tt.TransferTypeID = t.TransferTypeID 
                left join TransferSubtype ts
                    on ts.TransferTypeID = t.TransferTypeID 
                    and ts.TransferSubtypeID = t.TransferSubtypeID 
                left join TransferPersonnel tpIPCoordinator
                    on tpIPCoordinator.TransferID = t.TransferID 
                    and tpIPCoordinator.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . " -- IPCoordinador
                left join Personnel pIPCoordinator
                    on pIPCoordinator.PersonnelID = tpIPCoordinator.PersonnelID 
                left join TransferPersonnel tpAssociated
                    on tpAssociated.TransferID = t.TransferID 
                    and tpAssociated.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_II . " -- Investigador implicado
                left join TransferResearchArea tra
                    on tra.TransferID = t.TransferID 
                left join TransferResearchProgram trp
                    on trp.TransferID = t.TransferID 
                left join TransferResearchGroup trg
                    on trg.TransferID = t.TransferID 
                left join TransferWorkline twl
                    on twl.TransferID = t.TransferID 
                left join TransferExperiment te
                    on te.TransferID = t.TransferID 
                where 1=1
                -- Filtro estatico
                and	t.AnnualIncome is not null
                -- Filtro dinamica
                {$where}
                and t.TTAStartDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(year(t.TTAStartDate), quarter(t.TTAStartDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function addQueryMonitorFinancialSourcesMoney($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_FINANCIAL_SOURCES_MONEY, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorResearchProject($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_FINANCIAL_SOURCES_MONEY ."') as MonitorIndicatorID,
                    year(p.ConcessionDate) as `Year`,
                    quarter(p.ConcessionDate) as `Quarter`,
                    sum(p.TotalAmount) AS IndicatorCalculated,
                    NULL
                from Project p
                left join ProjectResearchArea pra
                    on pra.ProjectID = p.ProjectID 
                left join ProjectResearchProgram prp
                    on prp.ProjectID = p.ProjectID 
                left join ExperimentalCollaborationProject ecp
                    on ecp.ProjectID = p.ProjectID 
                left join ProjectPersonnel pp
                    on pp.ProjectID = p.ProjectID 
                where 1=1
                -- Filtros estaticos
                and p.`Granted` = 1
                and p.CompetitiveCall = 1
                and p.ConcessionDate is not null
                and p.TotalAmount is not null
                -- Filtro dinamicos
                {$where}
                and p.ConcessionDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(year(p.ConcessionDate), quarter(p.ConcessionDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function addQueryMonitorInternationalPartners($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_INTERNATIONAL_PARTNERS, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorExperiment($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_INTERNATIONAL_PARTNERS ."') as MonitorIndicatorID,
                    year(ec.StartDate) as `Year`,
                    quarter(ec.StartDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from ExperimentalCollaboration ec
                left join ExperimentalCollaborationResearchArea ecra
                    on ecra.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
                left join ExperimentalCollaborationResearchProgram ecrp
                    on ecrp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
                left join ExperimentalCollaborationResearchGroup ecrg
                    on ecrg.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
                left join ExperimentalCollaborationWorkLine ecwl
                    on ecwl.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
                left join ExperimentalCollaborationProject ecp
                    on ecp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
                left join Personnel p
                    on p.PersonnelID = ec.IPCoordinadorIgfaeID 
                where 1=1
                -- Filtros estaticos
                and ec.StartDate  is not null
                -- Filtros dinamicos
                {$where}
                and ec.StartDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                and ec.EndDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(year(ec.StartDate), quarter(ec.StartDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function getPrepareQueryMonitorExperiment($monitorRequest) {
    $where = " ";
    $params = [];

    // Research area input
    if ($monitorRequest->getResearchArea()) {
        $typesCount = count($monitorRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (ecra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $monitorRequest->getResearchArea());
    }

    // Research program input
    if ($monitorRequest->getResearchProgram()) {
        $typesCount = count($monitorRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (ecrp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $monitorRequest->getResearchProgram());
    }

    // Collaboration input
    if ($monitorRequest->getCollaborationExperiment()) {
        $typesCount = count($monitorRequest->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ec.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $monitorRequest->getCollaborationExperiment());
    }

    // Research group input
    if ($monitorRequest->getResearchGroup()) {
        $typesCount = count($monitorRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (ecrg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $monitorRequest->getResearchGroup());
    }

    // Authors input
    if ($monitorRequest->getResearchs()) {
        $typesCount = count($monitorRequest->getResearchs());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (ec.IPCoordinadorIgfaeID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $monitorRequest->getResearchs());
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function addQueryMonitorInternationalIgfae($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_INTERNATIONAL_IGFAE, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorResearchProject($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_INTERNATIONAL_IGFAE ."') as MonitorIndicatorID,
                    year(p.ConcessionDate) as `Year`,
                    quarter(p.ConcessionDate) as `Quarter`,
                    count(*) AS IndicatorCalculated,
                    NULL
                from Project p
                left join ProjectResearchArea pra
                    on pra.ProjectID = p.ProjectID 
                left join ProjectResearchProgram prp
                    on prp.ProjectID = p.ProjectID 
                left join ExperimentalCollaborationProject ecp
                    on ecp.ProjectID = p.ProjectID 
                left join ProjectPersonnel pp
                    on pp.ProjectID = p.ProjectID 
                where 1=1
                -- Filtros estaticos
                and (p.Announcement = 'Redes' or p.Announcement = 'Network')
                and p.ConcessionDate is not null
                -- Filtro dinamicos
                {$where}
                and p.ConcessionDate BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                group by concat(year(p.ConcessionDate), quarter(p.ConcessionDate))
                order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}


function addQueryMonitorSymposiumOrOutreachAttendats($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams, $symposium, $type) {
    if (in_array($type, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorEvent($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
                select 
                    (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='{$type}') as MonitorIndicatorID,
                    e.`Year`,
                    e.`Quarter`,
                    sum(e.NumberOfParticipants) AS IndicatorCalculated,
                    NULL
                from (
                    -- Un mismo evento tiene N fechas y por lo tanto N (year + quarter)
                    -- Por ello primero sacamos los diferentes eventos por anho/quarter ya filtrados y luego se agrupan
                    -- Aqui tambien se calcula el numero completo de asistentes (valor manual + inscripciones)
                    select
                        e.EventID,
                        year(ed.`Date`) as `year`,
                        quarter(ed.`Date`) as `quarter`,
                        e.Participants + sum(if(ei.EventInscriptionID is not null, 1, 0)) as NumberOfParticipants
                    from Event e 
                    inner join EventType et
                        on et.EventTypeID = e.EventTypeID 
                    left join EventResearchArea era
                        on era.EventID = e.EventID 
                    left join EventResearchProgram erp
                        on erp.EventID = e.EventID 
                    left join EventResearchGroup erg
                        on erg.EventID = e.EventID 
                    left join EventWorkLine ewl
                        on ewl.EventID = e.EventID 
                    left join EventPersonnel epspeaker
                        on epspeaker.EventID = e.EventID 
                        and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_SP . " -- Speaker
                    left join Personnel p1 
                        on p1.PersonnelID = epspeaker.PersonnelID
                    inner join EventDate ed
                        on ed.EventID = e.EventID 
                    left join EventInscription ei
                        on ei.EventID = e.EventID 
                    left join EventPersonnel eporgan
                        on eporgan.EventID = e.EventID 
                        and eporgan.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_UO . " -- Usuario organizador
                    left join Personnel p2
                        on p2.PersonnelID = eporgan.PersonnelID
                    left join EventExperiment ee
                        on ee.EventID = e.EventID 
                    where 1=1
                    -- Filtros estaticos";
        if ($symposium) {
            $sql .= "and e.EventTypeID = " . IGFAE_INTRANET_EVENT_TYPE_OUTREACH . " -- Outreach
                     and e.EventSubTypeID = " . IGFAE_INTRANET_EVENT_SUBTYPE_SYMPOSIUM . " -- Symposium";
        } else {
            $sql .= "and e.EventTypeID = " . IGFAE_INTRANET_EVENT_TYPE_OUTREACH . " -- Outreach";
        }
            $sql .= "-- Filtros dinamicos
                    {$where}
                    and ed.`Date` BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
                    group by e.EventID, year(ed.`Date`), quarter(ed.`Date`)
                ) e
                group by concat(e.`Year`, e.`Quarter`)
            order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}

function getPrepareQueryMonitorEvent($monitorRequest) {
    $where = " ";
    $params = [];

    // Research area input
    if ($monitorRequest->getResearchArea()) {
        $typesCount = count($monitorRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (era.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $monitorRequest->getResearchArea());
    }

    // Research program input
    if ($monitorRequest->getResearchProgram()) {
        $typesCount = count($monitorRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (erp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $monitorRequest->getResearchProgram());
    }

    // Collaboration input
    if ($monitorRequest->getCollaborationExperiment()) {
        $typesCount = count($monitorRequest->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ee.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $monitorRequest->getCollaborationExperiment());
    }

    // Research group input
    if ($monitorRequest->getResearchGroup()) {
        $typesCount = count($monitorRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (erg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $monitorRequest->getResearchGroup());
    }

    // Authors input
    if ($monitorRequest->getResearchs()) {
        $typesCount = count($monitorRequest->getResearchs());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (epspeaker.PersonnelID IN ({$placeholdersAuthors}) or eporgan.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $monitorRequest->getResearchs());
        $params = array_merge($params, $monitorRequest->getResearchs());
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function addQueryMonitorOutreachActivities($monitorRequest, $userMonitorTypesSelected, $quartersDates, &$sqlArray, &$sqlParams) {
    if (in_array(IGFAE_INTRANET_MONITOR_TYPE_OUTREACH_ACTIVITIES, $userMonitorTypesSelected)) {
        $queryPrepare = getPrepareQueryMonitorEvent($monitorRequest);
        $where = $queryPrepare['where'];
        $sql = "insert into MonitorCalculatedValues
        select 
            (select mci.MonitorIndicatorID from MonitorCalculatedIndicators mci where mci.Name='" . IGFAE_INTRANET_MONITOR_TYPE_OUTREACH_ACTIVITIES ."') as MonitorIndicatorID,
             e.`Year`,
             e.`Quarter`,
               count(*) AS IndicatorCalculated,
              NULL
         from (
            -- Un mismo evento tiene N fechas y por lo tanto N (year + quarter)
            -- Por ello primero sacamos los diferentes eventos por anho/quarter ya filtrados y luego se agrupan
            -- Aqui tambien se calcula el numero completo de asistentes (valor manual + inscripciones)
            select
                e.EventID,
                year(ed.`Date`) as `year`,
                quarter(ed.`Date`) as `quarter`
            from Event e 
            inner join EventType et
                on et.EventTypeID = e.EventTypeID 
            left join EventSubType est
                on est.EventTypeID = et.EventTypeID 
                and est.EventSubTypeID = e.EventSubTypeID
            left join EventResearchArea era
                on era.EventID = e.EventID 
            left join EventResearchProgram erp
                on erp.EventID = e.EventID 
            left join EventResearchGroup erg
                on erg.EventID = e.EventID 
            left join EventWorkLine ewl
                on ewl.EventID = e.EventID 
            left join EventPersonnel epspeaker
                on epspeaker.EventID = e.EventID 
                and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_SP . " -- Speaker
            left join Personnel p1 
                on p1.PersonnelID = epspeaker.PersonnelID
            inner join EventDate ed
                on ed.EventID = e.EventID 
            left join EventPersonnel eporgan
                on eporgan.EventID = e.EventID 
                and eporgan.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_UO . " -- Usuario organizador
            left join Personnel p2
                on p2.PersonnelID = eporgan.PersonnelID
            left join EventExperiment ee
                on ee.EventID = e.EventID 
            where 1=1
            -- Filtros estaticos
            and e.EventTypeID = " . IGFAE_INTRANET_EVENT_TYPE_OUTREACH . " -- Outreach
            -- Filtros dinamicos
            {$where}
            and ed.`Date` BETWEEN '{$quartersDates["StartDate"]}' and '{$quartersDates["EndDate"]}' -- Inicio y fin del intervalo
            group by e.EventID, year(ed.`Date`), quarter(ed.`Date`)
        ) e
        group by concat(e.`Year`, e.`Quarter`)
        order by 1,2";
        $sqlArray[] = $sql;
        $sqlParams[] = $queryPrepare['params'];
    }
}


//Comprobamos si esta validada la estancia
function isMonitorConsolidated($id) { 
    $sql = "SELECT count(*) 
    from MonitorIndicator m
    where
    m.Validated = 1
    and m.MonitorIndicatorID = $id";
    
    $result = igfae_intranet_get_var($sql);
    
    if ($result) {
    return true;
    } else {
    return false;
    }
}