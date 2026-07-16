<?php
function getSelectIndicatorTypes($type)
{

    $sql = "SELECT 
        i.IndicatorID as 'indicatorId' , i.Name as 'indicatorName'
    FROM 
    Indicator i
    WHERE i.IndicatorTypeID = $type
    ORDER BY i.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetIndicatorType(
            $row->indicatorId,
            $row->indicatorName
        );        
        
    }
   
    return $result;
}

function fetchIndicators($dataRequest, $offset = 0, $amount = 10, $order = null)
{
    $indicatorsResult = [];
    $orderGarants = getOrderIndicatorsGarants($order);
    $order = getOrderIndicators($order);
    $data = NULL;

    switch($dataRequest -> getIndicator()) {
        case IGFAE_INTRANET_INDICATOR_ARTICLES_CITIES :             
            $data = fetchIndicatorsArticle($dataRequest, $offset, $amount, $order);
            $indicatorsResult = setIndicatorsValues($data['rows'], false);
            break;

        case IGFAE_INTRANET_INDICATOR_ARTICLES_FIRST_DECILE :            
            $data = fetchIndicatorsFirstDecile($dataRequest, $offset, $amount, $order);
            $indicatorsResult = setIndicatorsValues($data['rows'], false);
            break;
        
        case IGFAE_INTRANET_INDICATOR_ARTICLES_FIRST_QUARTILE :            
            $data = fetchIndicatorsFirstQuartile($dataRequest, $offset, $amount, $order);
            $indicatorsResult = setIndicatorsValues($data['rows'], false);
            break;

        case IGFAE_INTRANET_INDICATOR_ARTICLES_FIVE_PERCENT :             
            $data = fetchIndicatorsFivePercent($dataRequest, $offset, $amount, $order);
            $indicatorsResult = setIndicatorsValues($data['rows'], false);
            break;

        case IGFAE_INTRANET_INDICATOR_ARTICLES_ONE_PERCENT :            
            $data = fetchIndicatorsFirstPercent($dataRequest, $offset, $amount, $order);
            $indicatorsResult = setIndicatorsValues($data['rows'], false);
            break;

        case IGFAE_INTRANET_INDICATOR_INDEX_H :           
            $data = fetchIndicatorsIndexH($dataRequest, $offset, $amount);
            $indicatorsResult = setIndicatorsValues($data['rows'], true);
            break;
        
        case IGFAE_INTRANET_INDICATOR_GARANTS :           
             $data = fetchIndicatorsGarants($dataRequest, $offset, $amount, $orderGarants);
             $indicatorsResult = setIndicatorsValues($data['rows'], false, true);
             break;   

        default:
            $data = fetchIndicatorsDefault($dataRequest, $offset, $amount, $order);
            $indicatorsResult = setIndicatorsValues($data['rows'], false);
            break;

    }   

    $info = [
        'indicators' => $indicatorsResult,
        'pagination' => $data['pagination']
    ];

    return $info;
}


function fetchIndicatorsDefault($indicatorRequest, $offset = 0, $amount = 10, $order="") {
    
    $prepareParams = [];
    $where = "";
    
    if (isset($indicatorRequest)) {
        
        $queryPrepare = getPrepareSearchIndicatorsDefault($indicatorRequest);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $sql = "Select 
        a.ProductionProjectID,
        a.Title,
        a.DOI,
        group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
        group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
        group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
        group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
        group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
        group_concat(distinct concat(ec.Name, '(',a.ExternalExperiment,')') separator ' ; ') as Experiments,
        group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupIDd,
        group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
        group_concat(distinct p.PersonnelID separator ' ; ') as AuthorsIDs,
        group_concat(distinct concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') as Authors,
        a.PublishDate,
        -- Fecha mas reciente de actualizacion
        a.UpdateDate
    from (
        select distinct
            pp.ProductionProjectID,
            COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
            pp.DOI,
            CASE 
                WHEN ec.`External`= 1 THEN 'Yes'
                ELSE 'No'
            END as ExternalExperiment,
            pp.`Date` as publishDate,
            -- Fecha mas reciente de actualizacion
            max(md.MeasurementDate) as updateDate
        from ProductionProject pp
        left join ProductionProjectLanguage pplen
            on pplen.ProductionProjectID = pp.ProductionProjectID 
            and pplen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
        left join ProductionProjectLanguage pples
            on pples.ProductionProjectID = pp.ProductionProjectID 
            and pples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
        left join ProductionProjectLanguage pplgl
            on pplgl.ProductionProjectID = pp.ProductionProjectID 
            and pplgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
        left join Journal j
            on pp.JournalID = j.JournalID 
        where 1=1
        -- Filtro obligatorio        
        and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
        {$where}
        group by pp.ProductionProjectID
    ) a
    -- Extraccion datos salida que al mismo tiempo son filtros, es necesario reprocesarlos
    left join ProductionProjectResearchArea ppra
        on ppra.ProductionProjectID = a.ProductionProjectID 
    left join ResearchArea ra
        on ppra.ResearchAreaID = ra.ResearchAreaID 
    left join ProductionProjectResearchProgram pprp
        on pprp.ProductionProjectID = a.ProductionProjectID 
    left join ResearchProgram rp
        on pprp.ResearchProgramID = rp.ResearchProgramID 
    left join ExperimentalCollaborationProductionProject ecpp
        on ecpp.ProductionProjectID = a.ProductionProjectID 
    left join ExperimentalCollaboration ec
        on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
    left join ProductionProjectResearchGroup pprg
        on pprg.ProductionProjectID = a.ProductionProjectID 
    left join ResearchGroup rg
        on pprg.ResearchGroupID = rg.ResearchGroupID 
    left join ProductionProjectAuthor ppa
        on ppa.ProductionProjectID = a.ProductionProjectID
    left join Personnel p
        on ppa.PersonnelID = p.PersonnelID 
    group by a.ProductionProjectID
    order by {$order}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndicators($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];

    return $data;
    
}

function getPrepareSearchIndicatorsDefault($indicatorsRequest)
{
    $where = " ";
    $params = [];
    
    // Research area input
    if ($indicatorsRequest->getResearchArea()) {
        $typesCount = count($indicatorsRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (ppra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $indicatorsRequest->getResearchArea());
    }

    // Research program input
    if ($indicatorsRequest->getResearchProgram()) {
        $typesCount = count($indicatorsRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (pprp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $indicatorsRequest->getResearchProgram());
    }

    // Collaboration input
    if ($indicatorsRequest->getCollaboration()) {
        $typesCount = count($indicatorsRequest->getCollaboration());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ecpp.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $indicatorsRequest->getCollaboration());
    }

    // External Collaboration input   
    if ($indicatorsRequest->getExternalCollaboration() != NULL && count($indicatorsRequest->getExternalCollaboration()) > 0
        && $indicatorsRequest->getExternalCollaboration()[0] != "") {
        $typesCount = count($indicatorsRequest->getExternalCollaboration());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ec.`External` IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $indicatorsRequest->getExternalCollaboration());
    }

    // Research group input
    if ($indicatorsRequest->getResearchGroup()) {
        $typesCount = count($indicatorsRequest->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (pprg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $indicatorsRequest->getResearchGroup());
    }

    // Authors input
    if ($indicatorsRequest->getAuthors()) {
        $typesCount = count($indicatorsRequest->getAuthors());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (ppa.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $indicatorsRequest->getAuthors());
    }
  

    // publication date input
    if ($indicatorsRequest->getPublicationDate()) {
        $startDates =  explode("-", $indicatorsRequest->getPublicationDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0],  $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (pp.`Date` BETWEEN %s and %s )";

        $params[] = $fecha1;
        $params[] = $fecha2;
    }

    // update date input
    if ($indicatorsRequest->getUpdateDate()) {
        $startDates =  explode("-", $indicatorsRequest->getUpdateDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (md.MeasurementDate BETWEEN %s and %s )";

        $params[] = $fecha1;
        $params[] = $fecha2;
    }    

    // Magazine input
    if ($indicatorsRequest->getMagazine()) {
        $typesCount = count($indicatorsRequest->getMagazine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersMagazine = implode(',', $paramsPlaceholders);

        $where .= " AND (pp.JournalID IN ({$placeholdersMagazine}))";
        $params = array_merge($params, $indicatorsRequest->getMagazine());
    }

    // Source
    if ($indicatorsRequest->getSource()) {
        $where .= " AND (ppc.SourceID = %d)";
        $params[] = $indicatorsRequest->getSource();
    }

 
    return array(
        "where" => $where,
        "params" => $params
    );
}

function fetchIndicatorsArticle($dataRequest, $offset = 0, $amount = 10, $order='') {
    
    $prepareParams = [];
    $whereDefault = "";
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepareDefault = getPrepareSearchIndicatorsDefault($dataRequest);
        $whereDefault = $queryPrepareDefault['where'];

        $queryPrepare = getPrepareSearchIndicatorsArticle($dataRequest);
        $where = $queryPrepare['where'];

        $prepareParams = array_merge($queryPrepareDefault['params'], $queryPrepare['params']);
    }

    $sql = "select 
                a.ProductionProjectID,
                a.Title,
                a.DOI,
                ppc.Cites as Cites,
                group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
                group_concat(distinct concat(ec.Name, '(',a.ExternalExperiment,')') separator ' ; ') as Experiments,
                group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupIDd,
                group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
                group_concat(distinct p.PersonnelID separator ' ; ') as AuthorsIDs,
                group_concat(distinct concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') as Authors,
                a.PublishDate,
                -- Fecha mas reciente de actualizacion
                a.UpdateDate
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    CASE 
                        WHEN ec.`External`= 1 THEN 'Yes'
                        ELSE 'No'
                    END as ExternalExperiment,
                    pp.`Date` as publishDate,
                    -- Fecha mas reciente de actualizacion
                    max(md.MeasurementDate) as updateDate
                from ProductionProject pp
                left join ProductionProjectLanguage pplen
                    on pplen.ProductionProjectID = pp.ProductionProjectID 
                    and pplen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
                left join ProductionProjectLanguage pples
                    on pples.ProductionProjectID = pp.ProductionProjectID 
                    and pples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
                left join ProductionProjectLanguage pplgl
                    on pplgl.ProductionProjectID = pp.ProductionProjectID 
                    and pplgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
                left join Journal j
                    on pp.JournalID = j.JournalID 
                where 1=1
                -- Filtro obligatorio
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
                {$whereDefault}
                group by pp.ProductionProjectID
            ) a
            -- Extraccion datos salida que al mismo tiempo son filtros, es necesario reprocesarlos
            left join ProductionProjectResearchArea ppra
                on ppra.ProductionProjectID = a.ProductionProjectID 
            left join ResearchArea ra
                on ppra.ResearchAreaID = ra.ResearchAreaID 
            left join ProductionProjectResearchProgram pprp
                on pprp.ProductionProjectID = a.ProductionProjectID 
            left join ResearchProgram rp
                on pprp.ResearchProgramID = rp.ResearchProgramID 
            left join ExperimentalCollaborationProductionProject ecpp
                on ecpp.ProductionProjectID = a.ProductionProjectID 
            left join ExperimentalCollaboration ec
                on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ProductionProjectResearchGroup pprg
                on pprg.ProductionProjectID = a.ProductionProjectID 
            left join ResearchGroup rg
                on pprg.ResearchGroupID = rg.ResearchGroupID 
            left join ProductionProjectAuthor ppa
                on ppa.ProductionProjectID = a.ProductionProjectID
            left join Personnel p
                on ppa.PersonnelID = p.PersonnelID 
            -- Extraemos las citas para la fecha mas reciente de actualizacion del articulo
            inner join ProductionProjectCites ppc
	            on ppc.ProductionProjectID = a.ProductionProjectID
            inner join MeasurementDate md
                on md.MeasurementDate = a.updateDate
                and md.MeasurementDateID = ppc.MeasurementDateID
            where 1=1
            {$where}
            group by a.ProductionProjectID
            order by {$order}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndicators($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];

    return $data;
    
}

function getPrepareSearchIndicatorsArticle($dataRequest) {

    $where = " ";
    $params = [];
    $minValue = NULL;
    $maxValue = NULL;

    if ($dataRequest->getRangeArticle()) {
        $idInterval = $dataRequest->getRangeArticle();  
        $interval = getIntervalsById($idInterval);  

        if ($interval != NULL) {
            $minValue = $interval[0] -> getMinValue();
            $maxValue = $interval[0] -> getMaxValue();
        }
    }

    //Preparamos los parametros de consulta
     if ($minValue != NULL and $maxValue != NULL) {
        $where .= " AND (ppc.Cites BETWEEN %d and %d )";
        $params[] = $minValue;
        $params[] = $maxValue;
     } else if ($minValue != NULL) {
        $where .= " AND (ppc.Cites >= %d )";
        $params[] = $minValue;
     } else if ($maxValue != NULL) {
        $where .= " AND (ppc.Cites <= %d )";
        $params[] = $maxValue;
     }

     return array(
        "where" => $where,
        "params" => $params
    );
}

function fetchIndicatorsFirstDecile($dataRequest, $offset = 0, $amount = 10, $order='') {
    
    $prepareParams = [];
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchIndicatorsDefault($dataRequest);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $sql = "select 
                a.ProductionProjectID,
                a.Title,
                a.DOI,
                group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
                group_concat(distinct concat(ec.Name, '(',a.ExternalExperiment,')') separator ' ; ') as Experiments,
                group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupIDd,
                group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
                group_concat(distinct p.PersonnelID separator ' ; ') as AuthorsIDs,
                group_concat(distinct concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') as Authors,
                a.PublishDate,
                -- Fecha mas reciente de actualizacion
                a.UpdateDate
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    CASE 
                        WHEN ec.`External`= 1 THEN 'Yes'
                        ELSE 'No'
                    END as ExternalExperiment,
                    pp.`Date` as publishDate,
                    -- Fecha mas reciente de actualizacion
                    max(md.MeasurementDate) as updateDate
                from ProductionProject pp
                left join ProductionProjectLanguage pplen
                    on pplen.ProductionProjectID = pp.ProductionProjectID 
                    and pplen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
                left join ProductionProjectLanguage pples
                    on pples.ProductionProjectID = pp.ProductionProjectID 
                    and pples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
                left join ProductionProjectLanguage pplgl
                    on pplgl.ProductionProjectID = pp.ProductionProjectID 
                    and pplgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
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
                {$where}                
                group by pp.ProductionProjectID
            ) a
            -- Extraccion datos salida que al mismo tiempo son filtros, es necesario reprocesarlos
            left join ProductionProjectResearchArea ppra
                on ppra.ProductionProjectID = a.ProductionProjectID 
            left join ResearchArea ra
                on ppra.ResearchAreaID = ra.ResearchAreaID 
            left join ProductionProjectResearchProgram pprp
                on pprp.ProductionProjectID = a.ProductionProjectID 
            left join ResearchProgram rp
                on pprp.ResearchProgramID = rp.ResearchProgramID 
            left join ExperimentalCollaborationProductionProject ecpp
                on ecpp.ProductionProjectID = a.ProductionProjectID 
            left join ExperimentalCollaboration ec
                on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ProductionProjectResearchGroup pprg
                on pprg.ProductionProjectID = a.ProductionProjectID 
            left join ResearchGroup rg
                on pprg.ResearchGroupID = rg.ResearchGroupID 
            left join ProductionProjectAuthor ppa
                on ppa.ProductionProjectID = a.ProductionProjectID
            left join Personnel p
                on ppa.PersonnelID = p.PersonnelID 
            -- Extraemos las citas para la fecha mas reciente de actualizacion del articulo
            left join ProductionProjectCites ppc
                on ppc.ProductionProjectID = a.ProductionProjectID
            left join MeasurementDate md
                on md.MeasurementDateID = ppc.MeasurementDateID 
                and md.MeasurementDate = a.updateDate
            where 1=1
            group by a.ProductionProjectID
            order by {$order}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndicators($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];

    return $data;
    
}

function fetchIndicatorsFirstQuartile($dataRequest, $offset = 0, $amount = 10, $order='') {
    
    $prepareParams = [];
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchIndicatorsDefault($dataRequest);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $sql = "select 
                a.ProductionProjectID,
                a.Title,
                a.DOI,
                group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
                group_concat(distinct concat(ec.Name, '(',a.ExternalExperiment,')') separator ' ; ') as Experiments,
                group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupIDd,
                group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
                group_concat(distinct p.PersonnelID separator ' ; ') as AuthorsIDs,
                group_concat(distinct concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') as Authors,
                a.PublishDate,
                -- Fecha mas reciente de actualizacion
                a.UpdateDate
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    CASE 
                        WHEN ec.`External`= 1 THEN 'Yes'
                        ELSE 'No'
                    END as ExternalExperiment,
                    pp.`Date` as publishDate,
                    -- Fecha mas reciente de actualizacion
                    max(md.MeasurementDate) as updateDate
                from ProductionProject pp
                left join ProductionProjectLanguage pplen
                    on pplen.ProductionProjectID = pp.ProductionProjectID 
                    and pplen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
                left join ProductionProjectLanguage pples
                    on pples.ProductionProjectID = pp.ProductionProjectID 
                    and pples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
                left join ProductionProjectLanguage pplgl
                    on pplgl.ProductionProjectID = pp.ProductionProjectID 
                    and pplgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
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
                {$where}               
                group by pp.ProductionProjectID
            ) a
            -- Extraccion datos salida que al mismo tiempo son filtros, es necesario reprocesarlos
            left join ProductionProjectResearchArea ppra
                on ppra.ProductionProjectID = a.ProductionProjectID 
            left join ResearchArea ra
                on ppra.ResearchAreaID = ra.ResearchAreaID 
            left join ProductionProjectResearchProgram pprp
                on pprp.ProductionProjectID = a.ProductionProjectID 
            left join ResearchProgram rp
                on pprp.ResearchProgramID = rp.ResearchProgramID 
            left join ExperimentalCollaborationProductionProject ecpp
                on ecpp.ProductionProjectID = a.ProductionProjectID 
            left join ExperimentalCollaboration ec
                on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ProductionProjectResearchGroup pprg
                on pprg.ProductionProjectID = a.ProductionProjectID 
            left join ResearchGroup rg
                on pprg.ResearchGroupID = rg.ResearchGroupID 
            left join ProductionProjectAuthor ppa
                on ppa.ProductionProjectID = a.ProductionProjectID
            left join Personnel p
                on ppa.PersonnelID = p.PersonnelID 
            -- Extraemos las citas para la fecha mas reciente de actualizacion del articulo
            left join ProductionProjectCites ppc
                on ppc.ProductionProjectID = a.ProductionProjectID
            left join MeasurementDate md
                on md.MeasurementDateID = ppc.MeasurementDateID 
                and md.MeasurementDate = a.updateDate
            where 1=1
            group by a.ProductionProjectID
            order by {$order}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndicators($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];

    return $data;
    
}

function fetchIndicatorsFivePercent($dataRequest, $offset = 0, $amount = 10, $order='') {
    
    $prepareParams = [];
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchIndicatorsDefault($dataRequest);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $sql = "select 
                a.ProductionProjectID,
                a.Title,
                a.DOI,
                group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
                group_concat(distinct concat(ec.Name, '(',a.ExternalExperiment,')') separator ' ; ') as Experiments,
                group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupIDd,
                group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
                group_concat(distinct p.PersonnelID separator ' ; ') as AuthorsIDs,
                group_concat(distinct concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') as Authors,
                a.PublishDate,
                -- Fecha mas reciente de actualizacion
                a.UpdateDate
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    CASE 
                        WHEN ec.`External`= 1 THEN 'Yes'
                        ELSE 'No'
                    END as ExternalExperiment,
                    pp.`Date` as publishDate,
                    -- Fecha mas reciente de actualizacion
                    max(md.MeasurementDate) as updateDate
                from ProductionProject pp
                left join ProductionProjectLanguage pplen
                    on pplen.ProductionProjectID = pp.ProductionProjectID 
                    and pplen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
                left join ProductionProjectLanguage pples
                    on pples.ProductionProjectID = pp.ProductionProjectID 
                    and pples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
                left join ProductionProjectLanguage pplgl
                    on pplgl.ProductionProjectID = pp.ProductionProjectID 
                    and pplgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
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
                ) > 95
                {$where}               
                group by pp.ProductionProjectID
            ) a
            -- Extraccion datos salida que al mismo tiempo son filtros, es necesario reprocesarlos
            left join ProductionProjectResearchArea ppra
                on ppra.ProductionProjectID = a.ProductionProjectID 
            left join ResearchArea ra
                on ppra.ResearchAreaID = ra.ResearchAreaID 
            left join ProductionProjectResearchProgram pprp
                on pprp.ProductionProjectID = a.ProductionProjectID 
            left join ResearchProgram rp
                on pprp.ResearchProgramID = rp.ResearchProgramID 
            left join ExperimentalCollaborationProductionProject ecpp
                on ecpp.ProductionProjectID = a.ProductionProjectID 
            left join ExperimentalCollaboration ec
                on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ProductionProjectResearchGroup pprg
                on pprg.ProductionProjectID = a.ProductionProjectID 
            left join ResearchGroup rg
                on pprg.ResearchGroupID = rg.ResearchGroupID 
            left join ProductionProjectAuthor ppa
                on ppa.ProductionProjectID = a.ProductionProjectID
            left join Personnel p
                on ppa.PersonnelID = p.PersonnelID 
            -- Extraemos las citas para la fecha mas reciente de actualizacion del articulo
            left join ProductionProjectCites ppc
                on ppc.ProductionProjectID = a.ProductionProjectID
            left join MeasurementDate md
                on md.MeasurementDateID = ppc.MeasurementDateID 
                and md.MeasurementDate = a.updateDate
            where 1=1
            group by a.ProductionProjectID
            order by {$order}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndicators($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];

    return $data;
    
}

function fetchIndicatorsFirstPercent($dataRequest, $offset = 0, $amount = 10, $order='') {
    
    $prepareParams = [];
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchIndicatorsDefault($dataRequest);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $sql = "select 
                a.ProductionProjectID,
                a.Title,
                a.DOI,
                group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
                group_concat(distinct concat(ec.Name, '(',a.ExternalExperiment,')') separator ' ; ') as Experiments,
                group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupIDd,
                group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
                group_concat(distinct p.PersonnelID separator ' ; ') as AuthorsIDs,
                group_concat(distinct concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) separator ' ; ') as Authors,
                a.PublishDate,
                -- Fecha mas reciente de actualizacion
                a.UpdateDate
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    CASE 
                        WHEN ec.`External`= 1 THEN 'Yes'
                        ELSE 'No'
                    END as ExternalExperiment,
                    pp.`Date` as publishDate,
                    -- Fecha mas reciente de actualizacion
                    max(md.MeasurementDate) as updateDate
                from ProductionProject pp
                left join ProductionProjectLanguage pplen
                    on pplen.ProductionProjectID = pp.ProductionProjectID 
                    and pplen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
                left join ProductionProjectLanguage pples
                    on pples.ProductionProjectID = pp.ProductionProjectID 
                    and pples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
                left join ProductionProjectLanguage pplgl
                    on pplgl.ProductionProjectID = pp.ProductionProjectID 
                    and pplgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
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
                ) >= 99
                {$where}               
                group by pp.ProductionProjectID
            ) a
            -- Extraccion datos salida que al mismo tiempo son filtros, es necesario reprocesarlos
            left join ProductionProjectResearchArea ppra
                on ppra.ProductionProjectID = a.ProductionProjectID 
            left join ResearchArea ra
                on ppra.ResearchAreaID = ra.ResearchAreaID 
            left join ProductionProjectResearchProgram pprp
                on pprp.ProductionProjectID = a.ProductionProjectID 
            left join ResearchProgram rp
                on pprp.ResearchProgramID = rp.ResearchProgramID 
            left join ExperimentalCollaborationProductionProject ecpp
                on ecpp.ProductionProjectID = a.ProductionProjectID 
            left join ExperimentalCollaboration ec
                on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ProductionProjectResearchGroup pprg
                on pprg.ProductionProjectID = a.ProductionProjectID 
            left join ResearchGroup rg
                on pprg.ResearchGroupID = rg.ResearchGroupID 
            left join ProductionProjectAuthor ppa
                on ppa.ProductionProjectID = a.ProductionProjectID
            left join Personnel p
                on ppa.PersonnelID = p.PersonnelID 
            -- Extraemos las citas para la fecha mas reciente de actualizacion del articulo
            left join ProductionProjectCites ppc
                on ppc.ProductionProjectID = a.ProductionProjectID
            left join MeasurementDate md
                on md.MeasurementDateID = ppc.MeasurementDateID 
                and md.MeasurementDate = a.updateDate
            where 1=1
            group by a.ProductionProjectID
            order by {$order}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndicators($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];

    return $data;
    
}

function getOrderIndicators($order)
{
    if (!isset($order)) {
        return ' a.ProductionProjectID desc';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: 
                $sort .= 'a.Title ';
                break;
            case 1:  
                $sort .= 'a.DOI ';
                break;
            case 2:  
                $sort .= 'Cites ';
                break;
            case 3: 
                $sort .= 'ResearchAreas ';
                break;
            case 4: 
                $sort .= 'ResearchPrograms ';
                break;
            case 5: 
                $sort .= 'Experiments ';
                break;
            case 6: 
                $sort .= 'ResearchGroups ';
                break;
            case 7: 
                $sort .= 'Authors ';
                break;
            case 8: 
                $sort .= 'a.PublishDate ';
                break;
            case 9: 
                $sort .= 'a.UpdateDate ';
                break;
            default:
                $sort .= 'a.Title ';
                break;                                                                                   
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function getPaginationInfoIndicators($offset, $amount, $sql, $params = [])
{

    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset/$amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];

    return $result;
}

function setIndicatorsValues($rows, $indexH = false, $garants = false) {
    $indicators = [];
    
    if ($indexH) {
        foreach ($rows as $row) {
            $indicator = new IntranetIndicator();
            $indicator -> setIndexH($row->HIndex);
            if (property_exists($row, "Journal") != null) {
                $indicator -> setMagazine($row->Journal);
            }
            if (property_exists($row, "Personnel") != null) {
                $indicator -> setAuthors($row->Personnel);           
            }
    
            $indicators[] = $indicator;
        }
    } else if ($garants) {
        foreach ($rows as $row) {
            $indicator = new IntranetIndicator();
            $indicator -> setGindex($row->GIndex);
            $indicator -> setPersonnelID($row->PersonnelID);
            $indicator -> setName($row->Name);  
            $indicator -> setExternalSourceID($row->ExternalSourceID);         
    
            $indicators[] = $indicator;
        }
    }else {
        foreach ($rows as $row) {
            $indicator = new IntranetIndicator();
            $indicator -> setTitle($row->Title);
            $indicator -> setDoi($row->DOI);
            $indicator -> setCites($row->Cites);
            $indicator -> setResearchArea($row->ResearchAreas);
            $indicator -> setResearchProgram($row->ResearchPrograms);
            $indicator -> setCollaboration($row->Experiments);
            $indicator -> setResearchGroup($row->ResearchGroups);
            $indicator -> setAuthors($row->Authors);
            $indicator -> setPublicationDate($row->PublishDate);
            $indicator -> setUpdateDate($row->UpdateDate);
    
            $indicators[] = $indicator;
        }
    }
    
    return $indicators;
}

function fetchIndicatorsIndexH($dataRequest, $offset = 0, $amount = 10) {

    //Almacenamos las diferentes sentencias sql en un array
    $sqlArray = [];
    $sqlParams = [];

    //Borrado tabla temporal
    $sqlArray[] = "DROP TEMPORARY TABLE IF EXISTS HIndexData";
    $sqlParams[] = [];

    //Creamos la tabla temporal
    $sqlArray[] = "CREATE TEMPORARY TABLE HIndexData(
                    RowNumber INT NOT NULL,
                    ProductionProjectID INT NOT NULL,
                    Cites INT NOT NULL,
                    UpdateDate date NOT NULL,
                    PersonnelID INT NOT NULL,
                    Personnel VARCHAR(100) NOT NULL,
                    JournalID INT NOT NULL,
                    Journal VARCHAR(100) NOT NULL
                );";
    $sqlParams[] = [];

    $sqlArray[] = "SET @row_number:=0";
    $sqlParams[] = [];
    $sqlArray[] = "SET @autoNumKey:=''";
    $sqlParams[] = [];

    //Realizamos inserccion de datos en la tabla temporal
    $data = insertIntoHIndexData($dataRequest);
    $sqlArray[] = $data['sql'];
    $sqlParams[] = $data['params'];

    $data = getSelectIndexH($dataRequest);
    $sqlArray[] = $data['sql'];
    $sqlParams[] = $data['params'];

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndexH($offset, $amount, $sqlArray, $sqlParams);
    }
    $rows = igfae_intranet_execute_script($sqlArray, $sqlParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];

    return $data;

}

function insertIntoHIndexData($dataRequest) {

    $rowNumber = "";
    $order = "";
    $sql = "";
    $prepareParams = [];

    if ($dataRequest) {

        if ($dataRequest->getAuthors()) {
            $rowNumber = "@row_number:=CASE 
                            WHEN @autoNumKey=a.PersonnelID THEN @row_number+1 
                            ELSE 1 
                          END AS RowNumber,
                          @autoNumKey:=a.PersonnelID AS autoNumKey,";

            $order = "order by ppa.PersonnelID, ppc.Cites desc";
        } else {
            $rowNumber = "@row_number:=CASE 
                            WHEN @autoNumKey=a.JournalID THEN @row_number+1 
                            ELSE 1 
                        END AS RowNumber,
                        @autoNumKey:=a.JournalID AS autoNumKey,";

            $order = "order by pp.JournalID, ppc.Cites desc";
        }
        
    $queryPrepare = getPrepareSearchIndicatorsDefault($dataRequest);
    $where = $queryPrepare['where'];
    $prepareParams = $queryPrepare['params'];

    $sql = "insert into HIndexData
            select
                b.RowNumber,
                b.ProductionProjectID,
                b.Cites,
                b.UpdateDate,
                b.PersonnelID,
                b.Personnel,
                b.JournalID,
                b.Journal
            from (
                select 
                   {$rowNumber}
                    pp.ProductionProjectID, 
                    a.Cites, 
                    a.UpdateDate,
                    a.PersonnelID,
                    a.Personnel,
                    pp.JournalID,
                    a.Journal
                from 
                (
                    select 
                        pp.ProductionProjectID, 
                        ppc.Cites,
                        -- Fecha mas reciente de actualizacion
                        max(md.MeasurementDate) as UpdateDate,
                        ppa.PersonnelID,
                        concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, ''))  as Personnel,
                        pp.JournalID,
                        j.FullName as Journal
                    from ProductionProject pp
                    inner join ProductionProjectCites ppc
                    on ppc.ProductionProjectID = pp.ProductionProjectID 
                    inner join MeasurementDate md
                    on md.MeasurementDateID = ppc.MeasurementDateID 
                    inner join ProductionProjectAuthor ppa
                    on ppa.ProductionProjectID = pp.ProductionProjectID
                    left join Journal j 
                        on j.JournalID = pp.JournalID
                    left join Personnel p
                        on ppa.PersonnelID = p.PersonnelID
                    left join ProductionProjectResearchArea ppra
                        on ppra.ProductionProjectID = pp.ProductionProjectID 
                    left join ResearchArea ra
                        on ppra.ResearchAreaID = ra.ResearchAreaID 
                    left join ProductionProjectResearchProgram pprp
                        on pprp.ProductionProjectID = pp.ProductionProjectID 
                    left join ResearchProgram rp
                        on pprp.ResearchProgramID = rp.ResearchProgramID 
                    left join ExperimentalCollaborationProductionProject ecpp
                        on ecpp.ProductionProjectID = pp.ProductionProjectID 
                    left join ExperimentalCollaboration ec
                        on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
                    left join ProductionProjectResearchGroup pprg
                        on pprg.ProductionProjectID = pp.ProductionProjectID 
                    left join ResearchGroup rg
                        on pprg.ResearchGroupID = rg.ResearchGroupID 
                    where 1=1
                    {$where}
                    group by pp.ProductionProjectID 
                    {$order}
                ) a
                inner join ProductionProject pp
                on a.ProductionProjectID = pp.ProductionProjectID
            ) b
            where 1=1
            and b.RowNumber <= b.Cites";

    }    

    $data = [
        'sql' => $sql,
        'params' => $prepareParams
    ];

    return $data;

}


function getSelectIndexH($dataRequest) {
    $sql = "";
    $params = [];

    $select = "";
    $select2 = "";
    $where = "";
    $groupBy ="";

    if ($dataRequest -> getAuthors()) {
        $select = "a.Personnel";
        $select2 = "h.Personnel";
        $groupBy = "group by h.PersonnelID";
    }else {
        $select = "a.Journal";
        $select2 = "h.Journal";
        $groupBy = "group by h.JournalID";
    }

    if ($dataRequest -> getRangeIndexH()) {

        $queryPrepare = getPrepareSearchIndexH($dataRequest);
        $where = $queryPrepare['where'];
        $params = $queryPrepare['params'];

    }


    $sql = "select 
                a.HIndex,
                {$select}
            from (
                select 
                    max(h.RowNumber) as HIndex,
                    {$select2}
                from HIndexData h
                {$groupBy}
            ) a
            where 1=1
            {$where} ";

    $data = [
        'sql' => $sql,
        'params' => $params
    ];

    return $data;

}

function getPrepareSearchIndexH($dataRequest) {

    $where = " ";
    $params = [];
    $minValue = NULL;
    $maxValue = NULL;

    if ($dataRequest->getRangeIndexH()) {
        $idInterval = $dataRequest->getRangeIndexH();  
        $interval = getIntervalsById($idInterval);  

        if ($interval != NULL) {
            $minValue = $interval[0] -> getMinValue();
            $maxValue = $interval[0] -> getMaxValue();
        }
    }

    //Preparamos los parametros de consulta
     if ($minValue != NULL and $maxValue != NULL) {
        $where .= " AND (HIndex  BETWEEN %d and %d )";
        $params[] = $minValue;
        $params[] = $maxValue;
     } else if ($minValue != NULL) {
        $where .= " AND (HIndex  >= %d )";
        $params[] = $minValue;
     } else if ($maxValue != NULL) {
        $where .= " AND (HIndex  <= %d )";
        $params[] = $maxValue;
     }

     return array(
        "where" => $where,
        "params" => $params
    );
}

function getPaginationInfoIndexH($offset, $amount, $sql, $params = [[]])
{

    $rows = igfae_intranet_execute_script($sql, $params);
    $result = [
        'draw'    => ($offset/$amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];

    return $result;
}


function getPrepareSearchIndicatorsGarants($indicatorsRequest)
{
    $where = " ";
    $params = [];
   
    // Source
    if ($indicatorsRequest->getSource()) {
        $where .= " AND (pes.SourceID = %d)";
        $params[] = $indicatorsRequest->getSource();
        }
  
    return array(
        "where" => $where,
        "params" => $params
    );
   
}

function getOrderIndicatorsGarants($order)
{
    if (!isset($order)) {
        return ' Name';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: 
                $sort .= 'GIndex ';
                break;
            case 1:  
                $sort .= 'Name ';
                break;
            default:
                $sort .= 'Name ';
                break;                                                                                   
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}


function fetchIndicatorsGarants($dataRequest, $offset = 0, $amount = 10, $order2='') {
    
    $prepareParams = [];
    $whereDefault = "";
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepareDefault = getPrepareSearchIndicatorsGarantsDefault($dataRequest);
        $whereDefault = $queryPrepareDefault['where'];
        
        $queryPrepare = getPrepareSearchIndicatorsGarants($dataRequest);
        $where = $queryPrepare['where'];

        $prepareParams = array_merge($queryPrepareDefault['params'], $queryPrepare['params']);
    
    }
   

    $sql = "select 
                sum((a.Index * a.TArticles)) / sum(a.TArticles) AS GIndex,
                a.PersonnelID,
                concat(a.Name, ' ', a.Surname) AS Name,
                a.ExternalSourceID as ExternalSourceID
            from ( 
                select
                    a.PersonnelID AS PersonnelID,
                    a.`Year` AS `Year`,
                    a.TCites / (a.TArticles * a.FWCIndex) AS `Index`,
                    a.TArticles AS TArticles,
                    a.Name AS Name,
                    a.Surname AS Surname,
                    a.ExternalSourceID AS ExternalSourceID
                from (
                    select
                        p.PersonnelID ,
                        year(a.`Date`) AS `Year`,
                        sum(a.Cites) AS TCites,
                        count(a.ProductionProjectID) AS TArticles,
                        group_concat(distinct 
                            if(FWCI1.`Year` = year(a.`Date`),
                                FWCI1.Value,
                                ''
                            ) separator ',') AS FWCIndex,
                        p.Name AS Name,
                        concat(p.FirstSurname, ' ', COALESCE (p.SecondSurname, '')) AS Surname,
                        group_concat(distinct pes.Code separator ' ; ') AS ExternalSourceID,
                        group_concat(distinct pes.SourceID separator ' ; ') as SourceID
                    from (
                        select
                            pp.ProductionProjectID AS ProductionProjectID,
                            pp.ActivityTypeID AS ActivityTypeID,
                            j.JournalID AS JournalID,
                            pp.DOI AS DOI,
                            pp.`Date` AS `Date`,
                            pper.Reference AS ArxivRef,
                            if((spp.SourceName is not null), spp.SourceName, 'None') AS SourceName,
                            if((ppc.Cites is not null), ppc.Cites, 0) AS Cites,
                            if((s.SourceName is not null), s.SourceName, 'None') AS CitesSourceName,
                            if((md.MeasurementDate is not null), md.MeasurementDate, 'None') AS MeasurementDate,
                            s.SourceID as SourceID
                        from Personnel p 
                        inner join ProductionProjectAuthor ppa
                            on p.PersonnelID = ppa.PersonnelID 
                        inner join ProductionProject pp
                            on pp.ProductionProjectID = ppa.ProductionProjectID 
                        inner join Journal j
                            on j.JournalID = pp.JournalID 
                        left join JournalArea ja
                            on ja.JournalAreaID = j.JournalAreaID 
                        inner join PersonnelContract pc
                            on pc.PersonnelID = p.PersonnelID 
                        inner join ProductionProjectExternalReference pper
                            on pper.ProductionProjectID = pp.ProductionProjectID 
                        left join ProductionProjectCites ppc
                            on ppc.ProductionProjectID = pp.ProductionProjectID 
                        left join Source s
                            on s.SourceID = ppc.SourceID
                        left join MeasurementDate md
                            on md.MeasurementDateID = ppc.MeasurementDateID 
                        left join ProductionProjectResearchArea ppra
                            on ppra.ProductionProjectID = pp.ProductionProjectID 
                        left join ProductionProjectResearchProgram pprp
                            on pprp.ProductionProjectID = pp.ProductionProjectID 
                        left join ExperimentalCollaborationProductionProject ecpp
                            on ecpp.ProductionProjectID = pp.ProductionProjectID 
                        left join Source spp
                            on spp.SourceID = pper.SourceID 
                            and pper.OriginalSource = " . IGFAE_INTRANET_SOURCE_SCOPUS . "
                        where 1=1
                        {$whereDefault}
                            and (
                                (
                                    pp.`Date` between pc.StartDate 
                                    and (if(pc.EndDate is not null, pc.EndDate, curdate()) + interval 1 year)
                                )
                                or 
                                (
                                    (
                                        pp.`Date` not between pc.StartDate 
                                        and if (pc.EndDate is not null, pc.EndDate + interval 6 month, curdate() + interval 1 year)
                                    )
                                    and pp.ProductionProjectID not in 
                                        (
                                            select distinct pp1.ProductionProjectID 
                                            from Personnel p1
                                            inner join ProductionProjectAuthor ppa1
                                                on ppa1.PersonnelID = p1.PersonnelID 
                                            inner join ProductionProject pp1
                                                on pp1.ProductionProjectID = ppa1.ProductionProjectID 
                                            inner join PersonnelContract pc1
                                                on pc1.PersonnelID = p1.PersonnelID 
                                            where
                                                pp1.`Date` between pc1.StartDate and (if(pc1.EndDate is not null, pc1.EndDate, curdate()) + interval 1 year)
                                        )
                                )
                            )
                        group by
                            pp.ProductionProjectID 
                    ) a
                    inner join ProductionProjectAuthor ppa
                        on ppa.ProductionProjectID = a.ProductionProjectID
                    inner join Personnel p
                        on p.PersonnelID = ppa.PersonnelID 
                    left join PersonnelExternalSource pes
                        on pes.PersonnelID = p.PersonnelID 
                    inner join FieldWeightedCitationImpact FWCI1
                        on (
                            year(a.`Date`) = FWCI1.`Year`
                            or (FWCI1.lastYear = 1
                                and year(a.`Date`) > FWCI1.`Year`
                            )
                        ) and FWCI1.SourceID = a.SourceID
                    inner join FieldWeightedCitationImpact FWCI2
                        on FWCI1.MeasurementDateID > FWCI2.MeasurementDateID
                    where 1=1
                    {$where}
                    and a.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
                    group by concat(p.PersonnelID, year(a.`Date`))
                ) a
            ) a
            group by a.PersonnelID
            having GIndex > 1.5
            order by {$order2}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoIndicators($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    $data = [
        'rows' => $rows['rows'],
        'pagination' => $pagination
    ];
   
    return $data;
    
}

function getPrepareSearchIndicatorsGarantsDefault($indicatorsRequest)
{
    $where = " ";
    $params = [];

    // Source
    if ($indicatorsRequest->getSource()) {
        $where .= " AND (pper.SourceID = %d)";
        $params[] = $indicatorsRequest->getSource();
    }
    
    // Research area input
    if ($indicatorsRequest->getResearchArea()) {
        $typesCount = count($indicatorsRequest->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (ppra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $indicatorsRequest->getResearchArea());
    }

    // Research program input
    if ($indicatorsRequest->getResearchProgram()) {
        $typesCount = count($indicatorsRequest->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (pprp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $indicatorsRequest->getResearchProgram());
    }

    // Collaboration input
    if ($indicatorsRequest->getCollaboration()) {
        $typesCount = count($indicatorsRequest->getCollaboration());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ecpp.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $indicatorsRequest->getCollaboration());
    }

    // Authors input
    if ($indicatorsRequest->getAuthors()) {
        $typesCount = count($indicatorsRequest->getAuthors());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);

        $where .= " AND (p.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $indicatorsRequest->getAuthors());
    }
  

    // publication date input
    if ($indicatorsRequest->getPublicationDate()) {
        $startDates =  explode("-", $indicatorsRequest->getPublicationDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0],  $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (pp.`Date` BETWEEN %s and %s )";

        $params[] = $fecha1;
        $params[] = $fecha2;
    }

    // update date input
    if ($indicatorsRequest->getUpdateDate()) {
        $startDates =  explode("-", $indicatorsRequest->getUpdateDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (md.MeasurementDate BETWEEN %s and %s )";

        $params[] = $fecha1;
        $params[] = $fecha2;
    }    

    // Magazine input
    if ($indicatorsRequest->getMagazine()) {
        $typesCount = count($indicatorsRequest->getMagazine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersMagazine = implode(',', $paramsPlaceholders);

        $where .= " AND (j.JournalID IN ({$placeholdersMagazine}))";
        $params = array_merge($params, $indicatorsRequest->getMagazine());
    }
 
    return array(
        "where" => $where,
        "params" => $params
    );
}



