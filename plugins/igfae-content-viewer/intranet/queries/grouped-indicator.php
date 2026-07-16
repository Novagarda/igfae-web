<?php

function fetchGroupedIndicators($dataRequest, $offset = 0, $amount = 10, $order = null)
{
    $indicatorsResult = [];
    $order = getOrderGoupedIndicators($order);
    $data = NULL;
    
    switch($dataRequest -> getIndicator()) {
        case IGFAE_INTRANET_INDICATOR_INTERNATIONAL_AUTHORSHIP :             
            $data = fetchGroupedIndicatorsInternational($dataRequest, $offset, $amount, $order);           
            break;

        case IGFAE_INTRANET_INDICATOR_TESIS :            
            $data = fetchGroupedIndicatorsTesis($dataRequest, $offset, $amount, $order);           
            break;
        
        case IGFAE_INTRANET_INDICATOR_ARTICLE_ON_SCOPUS :            
            $data = fetchGroupedIndicatorsTotalsArticles($dataRequest, $offset, $amount, $order, $dataRequest -> getIndicator());           
            break;

        case IGFAE_INTRANET_INDICATOR_ARTICLE_ON_WOS :             
            $data = fetchGroupedIndicatorsTotalsArticles($dataRequest, $offset, $amount, $order, $dataRequest -> getIndicator());           
            break;

        case IGFAE_INTRANET_INDICATOR_ARTICLE_ON_INSPIRE :            
            $data = fetchGroupedIndicatorsTotalsArticles($dataRequest, $offset, $amount, $order, $dataRequest -> getIndicator());            
            break;

        default:
            $data = fetchGroupedIndicatorsDefault($dataRequest, $offset, $amount, $order);           
            break;

    }   

    $indicatorsResult = setGroupedIndicatorsValues($data['rows']);

    $info = [
        'groupedIndicators' => $indicatorsResult,
        'pagination' => $data['pagination']
    ];

    return $info;
}

function fetchGroupedIndicatorsDefault($dataRequest, $offset = 0, $amount = 10, $order='') {
    
    $prepareParams = [];
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchGroupedIndicators($dataRequest);
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
                group_concat(distinct ec.Name separator ' ; ') as Experiments,
                a.`Date`
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    if (pp.ActivityTypeID in (2,4), pp.`Date`, ppt.ReadDate) as `Date`
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
                left join ProductionProjectThesis ppt
                    on ppt.ProductionProjectID = pp.ProductionProjectID 
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
                left join Journal j
                    on pp.JournalID = j.JournalID 
                where 1=1               
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_THESIS.", ".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",
                                         ".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
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

function fetchGroupedIndicatorsInternational($dataRequest, $offset = 0, $amount = 10, $order='') {

    $prepareParams = [];
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchGroupedIndicators($dataRequest);
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
                group_concat(distinct ec.Name separator ' ; ') as Experiments,
                a.`Date`
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    if (pp.ActivityTypeID in (2,4), pp.`Date`, ppt.ReadDate) as `Date`
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
                left join ProductionProjectThesis ppt
                    on ppt.ProductionProjectID = pp.ProductionProjectID 
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
                left join Journal j
                    on pp.JournalID = j.JournalID 
                where 1=1                
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_THESIS.", ".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.",
                                        ".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
                and ppt.InternationalMention = ".IGFAE_INTRANET_NUMBER_ONE."
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

function fetchGroupedIndicatorsTesis($dataRequest, $offset = 0, $amount = 10, $order='') {

    $prepareParams = [];
    $where = "";
    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchGroupedIndicators($dataRequest, true);
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
                group_concat(distinct ec.Name separator ' ; ') as Experiments,
                a.`Date`
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    ppt.ReadDate as `Date`
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
                left join ProductionProjectThesis ppt
                    on ppt.ProductionProjectID = pp.ProductionProjectID 
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
                left join Journal j
                    on pp.JournalID = j.JournalID 
                where 1=1                
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_THESIS.")               
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

function fetchGroupedIndicatorsTotalsArticles($dataRequest, $offset = 0, $amount = 10, $order='', $indicator='') {

    $prepareParams = [];
    $where = "";
    $sourceName = "";

    if ($indicator == IGFAE_INTRANET_INDICATOR_ARTICLE_ON_SCOPUS) {
        $sourceName = " and (s.SourceName = '".IGFAE_INTRANET_INDICATOR_SOURCENAME_SCOPUS."')";
    } else if($indicator == IGFAE_INTRANET_INDICATOR_ARTICLE_ON_WOS) {
        $sourceName = " and (s.SourceName = '".IGFAE_INTRANET_INDICATOR_SOURCENAME_WOS."')";
    } else {
        $sourceName = " and (s.SourceName = '".IGFAE_INTRANET_INDICATOR_SOURCENAME_INSPIRE."')";
    }


    
    if (isset($dataRequest)) {
        
        $queryPrepare = getPrepareSearchGroupedIndicators($dataRequest, false);
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
                group_concat(distinct ec.Name separator ' ; ') as Experiments,
                a.`Date`
            from (
                select distinct
                    pp.ProductionProjectID,
                    COALESCE(pplen.Title, pples.Title, pplgl.Title) as Title,
                    pp.DOI,
                    pp.`Date` as `Date`
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
                left join ProductionProjectThesis ppt
                    on ppt.ProductionProjectID = pp.ProductionProjectID 
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
                left join Journal j
                    on pp.JournalID = j.JournalID 
                inner join ProductionProjectExternalReference pper
                    on pper.ProductionProjectID = pp.ProductionProjectID 
                inner join Source s
                    on pper.SourceID = s.SourceID 
                where 1=1
                and pp.ActivityTypeID in (".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE.", ".IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_REFEREES.")
                {$sourceName}
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

function getOrderGoupedIndicators($order)
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
                $sort .= 'ResearchAreas ';
                break;
            case 2: 
                $sort .= 'ResearchPrograms ';
                break;
            case 3: 
                $sort .= 'Experiments ';
                break;
            case 4: 
                $sort .= 'a.`Date` ';
                break;
            default:
                $sort .= 'a.Title ';
                break;                                                                                   
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}


function getPrepareSearchGroupedIndicators($indicatorsRequest, $tesis = false)
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
    if ($indicatorsRequest->getInternationalConsortium()) {
        $typesCount = count($indicatorsRequest->getInternationalConsortium());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ecpp.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $indicatorsRequest->getInternationalConsortium());
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
  

    // date
    if ($indicatorsRequest->getDate()) {
        $startDates =  explode("-", $indicatorsRequest->getDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0],  $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        if ($tesis) {
            $where .= " AND (ppt.ReadDate BETWEEN %s and %s) ";
            $params[] = $fecha1;
            $params[] = $fecha2;
        } else {
            $where .= " AND ( (pp.ActivityTypeID in (2,4) and pp.`Date` BETWEEN %s and %s ) or 
                          (pp.ActivityTypeID in (1)  and ppt.ReadDate BETWEEN %s and %s) )";

            $params[] = $fecha1;
            $params[] = $fecha2;
            $params[] = $fecha1;
            $params[] = $fecha2;
        }
        
    }

 
    return array(
        "where" => $where,
        "params" => $params
    );
}

function setGroupedIndicatorsValues($rows) {
    $groupedIndicators = [];
    
    foreach ($rows as $row) {
        $groupedIndicator = new IntranetGroupedIndicator();
        $groupedIndicator -> setTitle($row->Title);        
        $groupedIndicator -> setResearchArea($row->ResearchAreas);
        $groupedIndicator -> setResearchProgram($row->ResearchPrograms);
        $groupedIndicator -> setInternationalConsortium($row->Experiments);
        $groupedIndicator -> setDate($row->Date);

        $groupedIndicators[] = $groupedIndicator;
    }
    
    
    return $groupedIndicators;
}


