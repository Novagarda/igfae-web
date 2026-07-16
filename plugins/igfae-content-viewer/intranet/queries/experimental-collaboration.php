<?php
function getExport($experimentalCollaboration){


    $result = getSelectExperimentalCollaborations($experimentalCollaboration, 0, null);
    $idsExperiment = [];
    $idsProject = [];
    $prepareParams = [];
    $idsExperimentStr = "";
    $idsProjectStr = "";
    foreach ($result['collaboration'] as $row) {
        if ($row->getConsortium() == 0){
            $idsExperiment[] = $row->getExperimentalCollaborationId();
        } else{
            $idsProject[] = $row->getExperimentalCollaborationId();
        }
        
    }
    //IDS de experimentos
    $idsExperimentStr = implode(',', $idsExperiment);
    $experimentos = "";
    $idsProjectStr = implode(',', $idsProject);
    $projects = "";
    if (count($idsExperiment) > 0 && count($idsProject) > 0){
        $experimentos = " and ec.ExperimentalCollaborationID in ($idsExperimentStr) ";
        $projects = " and p.ProjectID in ($idsProjectStr) ";
    } else if(count($idsExperiment) > 0){
        $experimentos = " and ec.ExperimentalCollaborationID in ($idsExperimentStr) ";
        $projects = " and p.ProjectID in ('') ";
    } else if(count($idsProject) > 0){
        $experimentos = " and ec.ExperimentalCollaborationID in ('') ";
        $projects = " and p.ProjectID in ($idsProjectStr) ";
    } else{
        $experimentos = " and ec.ExperimentalCollaborationID in ('') ";
        $projects = " and p.ProjectID in ('') ";
    }

    $sql = "SELECT 
            a.ID,
            a.Name,
            a.Abbreviation,
            a.Investigator,
            a.MouSignatureDate,
            a.MOULink,
            a.StartDate,
            a.EndDate,
            a.AssociatedUsers,
            a.Website,
            a.Abstract,
            a.ResearchAreas,
            a.ResearchPrograms,
            a.ResearchGroups,
            a.WorkLines,
            a.ResearchProjects,
            a.ProductionProjects,
            a.Facilities,
            a.`External`,
            a.Comment,
            a.Validated,
            a.ValidatedBy,
            a.ValidatedOn,
            a.Published,
            a.PublishedBy,
            a.PublishedOn,
            '0' as Consortium,
            null as ProjectReference,
            null as TitleEn,
            null as TitleEs,
            null as TitleGl,
            null as Ambit,
            null as Description,
            null as ActivityType,
            null as USCActivityType,
            null as USCSubactivity,
            null as USCProjectReference,
            null as Announcement,
            null as FundingAgencies,
            null as AmountWithoutOverheads,
            null as `Granted`,
            null as ConcessionDate,
            null as OfficeCode,
            null as Reference2,
            null as Partners,
            null as FinancingAgencies,
            null as TotalAmount,
            null as AnnualJustification,
            null as Cofinancing ,
            null as CofinancingType,
            null as Resolution,
            null as Extended,
            null as EndDateExtended,
            null as FEDER,
            null as FEDERPercent,
            null as Experiments,
            null as Competitive,
            null as CompetitiveCall,
            null as Keywords,
            null as IndirectCosts,
            null as IGFAELeader,
            null as ProjectType,
            null as BudgetLink,
            null as AnnualGiftedUsc,
            null as AnnualExecuted,
            null as AwardedAllotment,
            null as AnnualJustified,
            null as IPCoordinatesUsers,
            null as IPUsers,
            null as TechnicianUsers,
            null as IPWorkTeamUsersIDs,
            null as IPWorkTeamUsers
        from (
            SELECT 
                ec.ExperimentalCollaborationID as ID,
                ec.Name,
                ec.Abbreviation,
                concat(p1.Name, ' ', p1.FirstSurname, ' ', coalesce(p1.SecondSurname, '')) as Investigator,
                ec.MouSignatureDate,
                group_concat(distinct ml.Link separator ' ; ') as MOULink,
                ec.StartDate,
                ec.EndDate,
                group_concat(distinct concat(p2.Name, ' ', p2.FirstSurname, ' ', coalesce(p2.SecondSurname, '')) separator ' ; ') as AssociatedUsers,
                ec.Website,
                ec.Abstract,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
                group_concat(distinct wl.Name separator ' ; ') as WorkLines,
                group_concat(distinct COALESCE(plen.Title, ples.Title, plgl.Title) separator ' ; ') as ResearchProjects,
                group_concat(distinct COALESCE(pplen.Title, pples.Title, pplgl.Title) separator ' ; ') as ProductionProjects,
                ec.Facilities,
                ec.`External`,
                ec.Comment,
                ec.Validated,
                ec.ValidatedBy,
                ec.ValidatedOn,
                ec.Published,
                ec.PublishedBy,
                ec.PublishedOn,
                '0' as Consortium
            from ExperimentalCollaborationAndProject ecap
            inner join ExperimentalCollaboration ec
                on ecap.Id = ec.ExperimentalCollaborationID 
            left join ExperimentalCollaborationResearchArea ecra
                on ecra.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ResearchArea ra
                on ra.ResearchAreaID = ecra.ResearchAreaID 
            left join ExperimentalCollaborationResearchProgram ecrp
                on ecrp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ResearchProgram rp
                on rp.ResearchProgramID = ecrp.ResearchProgramID 
            left join ExperimentalCollaborationResearchGroup ecrg
                on ecrg.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ResearchGroup rg
                on rg.ResearchGroupID = ecrg.ResearchGroupID 
            left join ExperimentalCollaborationWorkLine ecwl
                on ecwl.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join WorkLine wl
                on wl.WorkLineID = ecwl.WorkLineID 
            left join ExperimentalCollaborationProject ecp
                on ecp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join Project p
                on p.ProjectID = ecp.ProjectID 
            left join ProjectLanguage plen
                on plen.ProjectID = p.ProjectID 
                and plen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
            left join ProjectLanguage ples
                on ples.ProjectID = p.ProjectID 
                and ples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
            left join ProjectLanguage plgl
                on plgl.ProjectID = p.ProjectID 
                and plgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
            left join Personnel p1
                on p1.PersonnelID = ec.IPCoordinadorIgfaeID 
            left join MouLink ml
                on ml.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join PersonnelExperiment pe
                on pe.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join Personnel p2
                on p2.PersonnelID = pe.PersonnelID 
            left join ExperimentalCollaborationProductionProject ecpp
                on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
            left join ProductionProject pp
                on pp.ProductionProjectID = ecpp.ProductionProjectID 
            left join ProductionProjectLanguage pplen
                on pplen.ProductionProjectID = pp.ProductionProjectID 
                and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
            left join ProductionProjectLanguage pples
                on pples.ProductionProjectID = pp.ProductionProjectID 
                and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
            left join ProductionProjectLanguage pplgl
                on pplgl.ProductionProjectID = pp.ProductionProjectID 
                and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
            where 1=1
            and ecap.Consortium = 0
            {$experimentos}
            group by ec.ExperimentalCollaborationID
        ) a
        UNION ALL
        SELECT
            a.ProjectID as ID,
            null as Name,
            null as Abbreviation,
            null as Investigator,
            null as MouSignatureDate,
            null as MOULink,
            a.StartDate,
            a.EndDate,
            b.AssociatedUsers,
            a.Website,
            null as Abstract,
            a.ResearchAreas,
            a.ResearchPrograms,
            a.ResearchGroup,
            a.WorkLine,
            null as ResearchProjects,
            null as ProductionProjects,
            null as Facilities,
            null as `External`,
            a.Comment,
            a.Validated, 
            a.ValidatedBy,
            a.ValidatedOn,
            a.Published,
            a.PublishedBy,
            a.PublishedOn,
            '1' as Consortium,
            a.ProjectReference,
            a.TitleEn,
            a.TitleEs,
            a.TitleGl,
            a.Ambit,
            a.Description,
            a.ActivityType,
            a.USCActivityType,
            a.USCSubactivity,
            a.USCProjectReference,
            a.Announcement,
            a.FundingAgencies,
            a.AmountWithoutOverheads,
            a.`Granted`,
            a.ConcessionDate,
            a.OfficeCode,
            a.Reference2,
            a.Partners,
            a.FinancingAgencies,
            a.TotalAmount,
            a.AnnualJustification,
            a.Cofinancing ,
            a.CofinancingType,
            a.Resolution,
            a.Extended,
            a.EndDateExtended,
            a.FEDER,
            a.FEDERPercent,
            a.Experiments,
            a.Competitive,
            a.CompetitiveCall,
            a.Keywords,
            a.IndirectCosts,
            a.IGFAELeader,
            a.ProjectType,
            a.BudgetLink,
            a.AnnualGiftedUsc,
            a.AnnualExecuted,
            a.AwardedAllotment,
            a.AnnualJustified,
            b.IPCoordinatesUsers,
            b.IPUsers,
            b.TechnicianUsers,
            b.IPWorkTeamUsersIDs,
            b.IPWorkTeamUsers
        from (
            SELECT
                p.ProjectID,
                p.ProjectReference,
                plen.Title as TitleEn,
                ples.Title as TitleEs,
                plgl.Title as TitleGl,
                s.Name as Ambit,
                p.Description,
                p.Website,
                pat.Name as ActivityType,
                concat (uat.Name, ' (', uat.Code, ')') as USCActivityType,
                group_concat( distinct concat (usat.Name, ' (', usat.Code, ')') separator ' ; ') as USCSubactivity,
                p.USCProjectReference,
                p.Announcement,
                group_concat(distinct fa.Name separator ' ; ') as FundingAgencies,
                p.AmountWithoutOverheads,
                p.`Granted`,
                p.ConcessionDate,
                p.OfficeCode,
                p.Reference2,
                group_concat(distinct pp.Name separator ' ; ') as Partners,
                p.StartDate,
                p.EndDate,
                group_concat(distinct fia.AgencyName separator ' ; ') as FinancingAgencies,
                p.TotalAmount,
                p.AnnualJustification,
                p.Cofinancing ,
                p.CofinancingType,
                p.Resolution,
                p.Extended,
                p.EndDateExtended,
                p.FEDER,
                p.FEDERPercent,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct ec.Name separator ' ; ') as Experiments,
                concat(rg.Name, ' (', rg.Acronym, ')') as ResearchGroup,
                wl.Name as WorkLine,
                p.Competitive,
                p.CompetitiveCall,
                group_concat(distinct pk.name separator ' ; ') as Keywords,
                p.IndirectCosts,
                p.IGFAELeader,
                pt.Name as ProjectType,
                p.BudgetLink,
                p.Comment,
                p.Validated, 
                p.ValidatedBy,
                p.ValidatedOn,
                p.Published,
                p.PublishedBy,
                p.PublishedOn,
                group_concat(distinct concat(agu.Year, ' (', format(agu.Amount,4), ')') separator ' ; ') as AnnualGiftedUsc,
                group_concat(distinct concat(ae.Year, ' (', format(ae.Amount,4), ')') separator ' ; ') as AnnualExecuted,
                group_concat(distinct concat(aa.Year, ' (', format(aa.Amount,4), ')') separator ' ; ') as AwardedAllotment,
                group_concat(distinct concat(aj.Year, ' (', format(aj.Amount,4), ')') separator ' ; ') as AnnualJustified
            from Project p
            left join ProjectLanguage plen
                on plen.ProjectID = p.ProjectID 
                and plen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
            left join ProjectLanguage ples
                on ples.ProjectID = p.ProjectID 
                and ples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
            left join ProjectLanguage plgl
                on plgl.ProjectID = p.ProjectID 
                and plgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
            left join `Scope` s
                on s.ScopeID = p.ScopeID 
            left join ProjectActivityType pat
                on pat.ActivityTypeID = p.ActivityTypeID 
            left join USCActivityType uat
                on uat.USCActivityTypeID = p.USCActivityTypeID 
            left join ProjectUSCSubactivity pusa
                on pusa.ProjectID = p.ProjectID 
            left join USCSubactivityType usat
                on usat.USCActivityTypeID = uat.USCActivityTypeID 
                and usat.USCSubactivityTypeID = pusa.USCSubactivityTypeID 
            left join ProjectFundingAgency pfa
                on pfa.ProjectID = p.ProjectID 
            left join FundingAgency fa
                on fa.FundingAgencyID = pfa.FundingAgencyID 
            left join ProjectPartner pp
                on pp.ProjectID = p.ProjectID 
            left join ProjectFinancingAgency pfia
                on pfia.ProjectID = p.ProjectID 
            left join FinancingAgency fia
                on fia.FinancingAgencyID = pfia.FinancingAgencyID 
            left join ProjectResearchArea pra
                on pra.ProjectID = p.ProjectID 
            left join ResearchArea ra
                on ra.ResearchAreaID = pra.ResearchAreaID 
            left join ProjectResearchProgram prp
                on prp.ProjectID = p.ProjectID 
            left join ResearchProgram  rp
                on rp.ResearchProgramID = prp.ResearchProgramID 	
            left join ExperimentalCollaborationProject ecp
                on ecp.ProjectID = p.ProjectID 
            left join ExperimentalCollaboration ec
                on ec.ExperimentalCollaborationID = ecp.ExperimentalCollaborationID 
            left join ResearchGroup rg
                on rg.ResearchGroupID = p.ResearchGroupID 
            left join WorkLine wl
                on wl.WorkLineID = p.WorkLineID 
            left join ProjectKeyword pk
                on pk.ProjectID = p.ProjectID 
            left join ProjectType pt
                on pt.ProjectTypeID = p.ProjectTypeID 
            left join AnnualGiftedUsc agu
                on agu.ProjectID = p.ProjectID 
            left join AnnualExecuted ae
                on ae.ProjectID = p.ProjectID 
            left join AwardedAllotment aa
                on aa.ProjectID = p.ProjectID 
            left join AnnualJustified aj
                on aj.ProjectID = p.ProjectID
            where 1=1
            and (p.Consortium = 1)
            {$projects}
            group by p.ProjectID
        ) a
        INNER JOIN
        (
            SELECT
                p.ProjectID,
                group_concat(distinct concat(peripc.Name, ' ', peripc.FirstSurname, ' ', coalesce(peripc.SecondSurname, '')) separator ' ; ') as IPCoordinatesUsers,
                group_concat(distinct concat(perip.Name, ' ', perip.FirstSurname, ' ', coalesce(perip.SecondSurname, '')) separator ' ; ') as IPUsers,
                group_concat(distinct concat(pera.Name, ' ', pera.FirstSurname, ' ', coalesce(pera.SecondSurname, '')) separator ' ; ') as AssociatedUsers,
                group_concat(distinct concat(pert.Name, ' ', pert.FirstSurname, ' ', coalesce(pert.SecondSurname, '')) separator ' ; ') as TechnicianUsers,
                group_concat(distinct perwt.PersonnelID separator ' ; ') as IPWorkTeamUsersIDs,
                group_concat(distinct concat(perwt.Name, ' ', perwt.FirstSurname, ' ', coalesce(perwt.SecondSurname, '')) separator ' ; ') as IPWorkTeamUsers
            from Project p
            left join ProjectPersonnel pperipc
                on pperipc.ProjectID = p.ProjectID 
                and pperipc.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
            left join Personnel peripc
                on pperipc.PersonnelID = peripc.PersonnelID 
            left join ProjectPersonnel pperip
                on pperip.ProjectID = p.ProjectID 
                and pperip.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IP . "
            left join Personnel perip
                on pperip.PersonnelID = perip.PersonnelID
            left join ProjectPersonnel ppera
                on ppera.ProjectID = p.ProjectID 
                and ppera.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IA . "
            left join Personnel pera
                on ppera.PersonnelID = pera.PersonnelID
            left join ProjectPersonnel ppert
                on ppert.ProjectID = p.ProjectID 
                and ppert.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_TE . "
            left join Personnel pert
                on ppert.PersonnelID = pert.PersonnelID
            left join ProjectPersonnel ppwt
                on ppwt.ProjectID = p.ProjectID 
                and ppwt.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_ET . "
            left join Personnel perwt
                on ppwt.PersonnelID = perwt.PersonnelID
            where 1=1
            and (p.Consortium = 1)
            {$projects}
            group by p.ProjectID
        ) b
        on a.ProjectID = b.ProjectID";

    $result = igfae_intranet_fetch_data($sql);
    return $result['rows'];

}

function getSelectExperimentalCollaborations($experimentalCollaboration, $offset = 0, $amount = 10, $order = null)
{

    $collaboration = [];
    $where = "";
    $prepareParams = [];
    if (isset($experimentalCollaboration)) {
        $queryPrepare = getPrepareSearchExperimentalCollaboration($experimentalCollaboration);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $order = getOrderExperimetnalCollaboration($order);

   $sql = "SELECT
            a.id as experimentalCollaborationID,
            a.Name as nombre,
            group_concat(distinct ecap2.IPCoordinatorIGFAE separator ' ; ') as IPCoordinatorIGFAE,
            a.StartDate as startDate,
            a.EndDate as endDate,
            a.Consortium as consortium,
            a.Validated as validated, 
            a.Published as published
            FROM (
                SELECT DISTINCT 
                    ecap.id,
                    ecap.Name,
                    ecap.StartDate,
                    ecap.EndDate,
                    ecap.Consortium,
                    ecap.Validated, 
                    ecap.Published 
                FROM ExperimentalCollaborationAndProject ecap
                LEFT JOIN PersonnelExperiment pe
                    on pe.ExperimentalCollaborationID = ecap.Id 
                    and ecap.Consortium = " . IGFAE_INTRANET_CONSORTIUM_NO ."
                LEFT JOIN ProjectPersonnel pp
                    on pp.ProjectID = ecap.Id 
                    and ecap.Consortium = " . IGFAE_INTRANET_CONSORTIUM_YES . "
                    WHERE {$where}
                ) a
            INNER JOIN ExperimentalCollaborationAndProject ecap2
                on ecap2.id = a.id
            GROUP by a.id
            ORDER by {$order} ";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoExperimentalCollaboration($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $collaboration[] = new IntranetExperimentalCollaboration(
            $row->experimentalCollaborationID,
            $row->nombre,
            [],
            [],
            $row->IPCoordinatorIGFAE,
            $row->startDate,
            $row->endDate,
            $row->consortium,
            [],
            [],
            [],
            NULL,
            $row->validated,
            $row->published
        );
    }

    $info = [
        'collaboration' => $collaboration,
        'pagination' => $pagination
    ];

    return $info;
}


function getPrepareSearchExperimentalCollaboration($experimentalCollaboration)
{
    $where = "1=1";
    $params = [];


    // Name input
    if ($experimentalCollaboration->getName()) {
        $where .= " AND (ecap.Name like %s)";
        $params[] = '%' . $experimentalCollaboration->getName() . '%';
    }

    // Research area select
     if ($experimentalCollaboration->getResearchArea()) {

        $typesCount = count($experimentalCollaboration->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (ecap.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $experimentalCollaboration->getResearchArea());
    }

    // Coordinator IGFAE select
    if ($experimentalCollaboration->getCoordinatorIgfae()) {
        $typesCount = count($experimentalCollaboration->getCoordinatorIgfae());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersCoordinator = implode(',', $paramsPlaceholders);

        $where .= " AND (ecap.IPCoordinadorIgfaeID IN ({$placeholdersCoordinator}))";
        $params = array_merge($params, $experimentalCollaboration->getCoordinatorIgfae());
    }

    // Research Program
    if ($experimentalCollaboration->getResearchProgram()) {
        $typesCount = count($experimentalCollaboration->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (ecap.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $experimentalCollaboration->getResearchProgram());
    }

    // Start Date and End Date
    if ($experimentalCollaboration->getStartDate() && $experimentalCollaboration->getEndDate()) {
        $startDates =  explode("-", $experimentalCollaboration->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( (ecap.StartDate BETWEEN %s and %s) OR ecap.StartDate <= %s)";
        $params[] = $fecha1;
        $params[] = $fecha2;
        $params[] = $fecha1;

        //Fecha final
        $endDates =  explode("-", $experimentalCollaboration->getEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( (ecap.EndDate BETWEEN %s and %s) OR ecap.EndDate >= %s OR ecap.EndDate is null)";
        $params[] = $fecha1;
        $params[] = $fecha2;
        $params[] = $fecha2;
        
    }  else if ($experimentalCollaboration->getStartDate()) {
        $startDates =  explode("-", $experimentalCollaboration->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( ecap.StartDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    } else if ($experimentalCollaboration->getEndDate()){
        $endDates =  explode("-", $experimentalCollaboration->getEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( ecap.EndDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    }

    //Research Project
    if ($experimentalCollaboration->getResearchProject()) {
        $typesCount = count($experimentalCollaboration->getResearchProject());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProject = implode(',', $paramsPlaceholders);

        $where .= " AND (ecap.ProjectID IN ({$placeholdersResearchProject}))";
        $params = array_merge($params, $experimentalCollaboration->getResearchProject());
    }

    //Research Group
    if ($experimentalCollaboration->getResearchGroup()) {
        $typesCount = count($experimentalCollaboration->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (ecap.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $experimentalCollaboration->getResearchGroup());
    }

    //Work Line
    if ($experimentalCollaboration->getWorkLine()) {
        $typesCount = count($experimentalCollaboration->getWorkLine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersWorkline = implode(',', $paramsPlaceholders);

        $where .= " AND (ecap.WorkLineID IN ({$placeholdersWorkline}))";
        $params = array_merge($params, $experimentalCollaboration->getWorkLine());
    }

    // Facilities
    if ($experimentalCollaboration->getFacilities()) {
        $where .= " AND ecap.Facilities like %s";
        $params[] = '%' . $experimentalCollaboration->getFacilities() . '%';
    }

    // Consortium
    if ($experimentalCollaboration->getConsortium() != null) {
        $where .= " AND (ecap.Consortium  = %d)";
        $params[] = $experimentalCollaboration->getConsortium();
    }

    // Validated
    if ($experimentalCollaboration->getValidated() != null) {
        $where .= " AND (ecap.Validated  = %d)";
        $params[] = $experimentalCollaboration->getValidated();
    }

    // Published
    if ($experimentalCollaboration->getPublished() != null) {
        $where .= " AND (ecap.Published = %d)";
        $params[] = $experimentalCollaboration->getPublished();
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
            $where .= " AND (pe.PersonnelID = %d or ecap.CreatedBy = %d)";
            $params[] =  $igfae_user_id;
            $params[] =  $igfae_user_id;
        }
    } 

    return array(
        "where" => $where,
        "params" => $params
    );
}

function getPaginationInfoExperimentalCollaboration($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];

    return $result;
}


function getOrderExperimetnalCollaboration($order)
{
    if (!isset($order)) {
        return ' a.Name ';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: //nombre
                $sort .= 'nombre ';
                break;
            case 1:  //IPCoordinatorIGFAE
                $sort .= 'IPCoordinatorIGFAE ';
                break;
            case 2: //startDate
                $sort .= 'startDate ';
                break;
            case 3: //endDate
                $sort .= 'endDate ';
                break;
            case 4: //endDate
                $sort .= 'consortium ';
                break;
            case 5: //validated
                $sort .= 'validated ';
                break;
            case 6: //published
                $sort .= 'published ';
                break;
            default:
                $sort .= 'nombre ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}


function getSelectExperimentalCollaboration()
{
    $sql = "SELECT 
    ec.ExperimentalCollaborationID as 'experimentalCollaborationID' , ec.Name as 'name'
    FROM 
    ExperimentalCollaboration ec
    ORDER BY 
    ec.Name
    ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetExperimentalCollaboration(
            $row->experimentalCollaborationID,
            $row->name,
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

    return $result;
}

// Delete experiment
function deleteExperiment($id)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction(); 

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $id);

    deleteRelationsExperiment($id);
     // ExperimentalCollaboration
     $sql = "DELETE FROM ExperimentalCollaboration
     WHERE ExperimentalCollaborationID = '$id'";
     igfae_intranet_save($sql);

    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }
}

// Delete experiment
function deleteRelationsExperiment($id)
{
    //ExperimentalCollaborationResearchArea
    $sql = "DELETE FROM ExperimentalCollaborationResearchArea
        WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationResearchProgram
    $sql = "DELETE FROM ExperimentalCollaborationResearchProgram
          WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationResearchGroup
    $sql = "DELETE FROM ExperimentalCollaborationResearchGroup
          WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationWorkLine
    $sql = "DELETE FROM ExperimentalCollaborationWorkLine
          WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationProject
    $sql = "DELETE FROM ExperimentalCollaborationProject
        WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);

    //ExperimentalCollaborationProductionProject
    $sql = "DELETE FROM ExperimentalCollaborationProductionProject
        WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);

    //PersonnelExperiment
    $sql = "DELETE FROM PersonnelExperiment
        WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);

    //Mou Link
    $sql = "DELETE FROM MouLink
        WHERE ExperimentalCollaborationID = '$id'";
    igfae_intranet_save($sql);
}

function saveExperimentalCollaboration($experimentalCollaboration)
{
    global $current_user;
    
    // Recuperamos los campos del formulario para insertar
    $name = $experimentalCollaboration->getName();
    $abbreviation = $experimentalCollaboration->getAbbreviation();
    $researchArea = $experimentalCollaboration->getResearchArea();
    $researchProgram = $experimentalCollaboration->getResearchProgram();
    $coordinatorIgfae = $experimentalCollaboration->getCoordinatorIgfae();
    $startDateStr = $experimentalCollaboration->getStartDate();
    if (isset($startDateStr)) {
        $time_input = strtotime($startDateStr);
        $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $endDateStr = $experimentalCollaboration->getEndDate();
    $endDate = NULL;
    if (isset($endDateStr) && $endDateStr) {
        $time_input = strtotime($endDateStr);
        $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $researchProject = $experimentalCollaboration->getResearchProject();
    $researchGroup = $experimentalCollaboration->getResearchGroup();
    $workLine = $experimentalCollaboration->getWorkLine();
    $facilities = $experimentalCollaboration->getFacilities();
    $abbreviation = $experimentalCollaboration->getAbbreviation();
    $mouDateStr = $experimentalCollaboration->getMouDate();
    if (isset($mouDateStr)) {
        $time_input = strtotime($mouDateStr);
        $mouDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $mouLink = $experimentalCollaboration->getMouLink();
    $webPage = $experimentalCollaboration->getWebPage();
    $external = $experimentalCollaboration->getExternal();
    $abstract = $experimentalCollaboration->getAbstract();
    $asociatedPublications = $experimentalCollaboration->getAsociatedPublications();
    $asociatedUsers = $experimentalCollaboration->getAsociatedUsers();
    $comments = $experimentalCollaboration->getComments();
    $validated = $experimentalCollaboration-> getValidated();
    $validatedBy = NULL;
    $validatedOn = NULL;
    if (isset($validated) && $validated) {
        $validatedBy = $current_user->user_login;
        $validatedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    }
    $published = $experimentalCollaboration->getPublished();
    $publishedBy = NULL;
    $publishedOn = NULL;
    if (isset($published) && $published) {
        $publishedBy = $current_user->user_login;
        $publishedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    }

    $idExperiment = $_POST['idExperiment'];
    $id = NULL;
    if (isset($idExperiment) && strlen($idExperiment) > 0) {
        //ACTUALIZAR PROYECTO
        $sql = "UPDATE ExperimentalCollaboration
        SET
        Name = '$name',
        Abbreviation = " . (isset($abbreviation) ? "'$abbreviation'" : "NULL") . ",
        MouSignatureDate = '$mouDate',  
        StartDate = '$startDate',   
        EndDate =  " . (isset($endDate) ? "'$endDate'" : "NULL") . ",
        Website = " . (isset($webPage) ? "'$webPage'" : "NULL") . ",
        Facilities = " . (isset($facilities) ? "'$facilities'" : "NULL") . ",         
        Abstract = " . (isset($abstract) ? "'$abstract'" : "NULL") . ",
        IPCoordinadorIgfaeID = '$coordinatorIgfae',
        Comment = " . (isset($comments) ? "'$comments'" : "NULL") . ",
        Validated = '$validated', 
        Published = '$published', 
        ValidatedBy = " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",  
        PublishedBy = " . (isset($publishedBy) ? "'$publishedBy'" : "NULL") . ",             
        ValidatedOn = " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . ", 
        PublishedOn = " . (isset($publishedOn) ? "'$publishedOn'" : "NULL") . ",
        External = " . (isset($external) ? "'$external'" : "NULL") . "
        WHERE ExperimentalCollaborationID = '$idExperiment'";

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        igfae_intranet_save($sql);
        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsExperiment($idExperiment);

        insertResearchAreaExperiment($idExperiment, $researchArea);

        insertResearchProgramExperiment($idExperiment, $researchProgram);

        insertWorkLineExperiment($idExperiment, $workLine);

        insertResearchGroupExperiment($idExperiment, $researchGroup);

        insertResearchProjectExperiment($idExperiment, $researchProject);

        insertResearchProductionProjectExperiment($idExperiment, $asociatedPublications);

        insertMouLink($idExperiment, $mouLink);

        insertPersonnelExperiment($idExperiment, $asociatedUsers, $startDate, $endDate);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $idExperiment);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $idExperiment);
        }
        if (isset($publishedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $idExperiment);
        }

        // Authorization
        $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $idExperiment);
        if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
            closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $idExperiment);
        }   

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
        
    } else {
        global $current_user;
        $sql = "INSERT INTO ExperimentalCollaboration 
            (
            Name,
            Abbreviation,
            MouSignatureDate,
            StartDate,
            EndDate,
            Website,
            Facilities,
            Abstract,
            IPCoordinadorIgfaeID,
            Comment,
            Validated,
            Published,
            ValidatedBy,
            PublishedBy,
            ValidatedOn,
            PublishedOn,
            External,
            CreatedBy) 
            VALUES ( 
            '$name', 
            " . (isset($abbreviation) ? "'$abbreviation'" : "NULL") . ",
            '$mouDate',  
            '$startDate',   
            " . (isset($endDate) ? "'$endDate'" : "NULL") . ",
            " . (isset($webPage) ? "'$webPage'" : "NULL") . ",
            " . (isset($facilities) ? "'$facilities'" : "NULL") . ",         
            " . (isset($abstract) ? "'$abstract'" : "NULL") . ",
            '$coordinatorIgfae',
            " . (isset($comments) ? "'$comments'" : "NULL") . ",
            '$validated', 
            '$published', 
            " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",  
            " . (isset($publishedBy) ? "'$publishedBy'" : "NULL") . ",             
            " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . ", 
            " . (isset($publishedOn) ? "'$publishedOn'" : "NULL") . ",
            " . (isset($external) ? "'$external'" : "NULL") . ",
            " . getUserByWPUserID($current_user->ID)[0]["id"] . "
            )";

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        $id = igfae_intranet_save($sql);

        insertResearchAreaExperiment($id, $researchArea);

        insertResearchProgramExperiment($id, $researchProgram);

        insertWorkLineExperiment($id, $workLine);

        insertResearchGroupExperiment($id, $researchGroup);

        insertResearchProjectExperiment($id, $researchProject);

        insertResearchProductionProjectExperiment($id, $asociatedPublications);

        insertMouLink($id, $mouLink);

        insertPersonnelExperiment($id, $asociatedUsers, $startDate, $endDate);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $id);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $id);
        }
        if (isset($publishedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_EXPERIMENT, $id);
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

function getExperimentalCollaborationById($experimentalCollaborationId)
{
    $experiment = NULL;
    $where = "";
    $prepareParams = [];
    if (isset($experimentalCollaborationId)) {
        $where = " 1=1 and ec.ExperimentalCollaborationID  = %d";
        $prepareParams = $experimentalCollaborationId;
    }

    $sql = "SELECT 
	ec.ExperimentalCollaborationID as Id,
	ec.Name as 'Name',
	ec.Abbreviation as Abbreviation,
	p1.PersonnelID as IPCoordinatorIGFAEID,
	concat(p1.Name, ' ', p1.FirstSurname, ' ', coalesce(p1.SecondSurname, '')) as IPCoordinatorIGFAE,
	ec.MouSignatureDate as MouDate,
	ec.StartDate as StartDate,
	ec.EndDate as EndDate,
    group_concat(distinct pe.PersonnelID  separator ' ; ') as AssociatedUserID,
	group_concat(distinct concat(p2.Name, ' ', p2.FirstSurname, ' ', coalesce(p2.SecondSurname, '')) separator ' ; ') as AssociatedUsers,
	ec.Website as WebSite,
	ec.Abstract as Abstract,
	group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
	group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
	group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
	group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
	group_concat(distinct rg.ResearchGroupID  separator ' ; ') as ResearchGroupsIDs,
	group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
	group_concat(distinct wl.WorkLineID  separator ' ; ') as WorkLinesIDs,
	group_concat(distinct wl.Name separator ' ; ') as WorkLines,
	group_concat(distinct p.ProjectID separator ' ; ') as ResearchProjectsIDs,
	group_concat(distinct COALESCE(plen.Title, ples.Title, plgl.Title) separator ' ; ') as ResearchProjects,
	group_concat(distinct pp.ProductionProjectID separator ' ; ') as ProductionProjectsIDs,
	group_concat(distinct COALESCE(pplen.Title, pples.Title, pplgl.Title) separator ' ; ') as ProductionProjects,
	ec.Facilities as Facilities,
	ec.`External` as 'External',
	ec.Comment as Comment,
	ec.Validated as Validated,
	ec.ValidatedBy as ValidatedBy,
	ec.ValidatedOn as ValidatedOn,
	ec.Published as Published,
	ec.PublishedBy as PublishedBy,
	ec.PublishedOn as PublishedOn 
from ExperimentalCollaboration ec
left join ExperimentalCollaborationResearchArea ecra
	on ecra.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
left join ResearchArea ra
	on ra.ResearchAreaID = ecra.ResearchAreaID 
left join ExperimentalCollaborationResearchProgram ecrp
	on ecrp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
left join ResearchProgram rp
	on rp.ResearchProgramID = ecrp.ResearchProgramID 
left join ExperimentalCollaborationResearchGroup ecrg
	on ecrg.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
left join ResearchGroup rg
	on rg.ResearchGroupID = ecrg.ResearchGroupID 
left join ExperimentalCollaborationWorkLine ecwl
	on ecwl.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
left join WorkLine wl
	on wl.WorkLineID = ecwl.WorkLineID 
left join ExperimentalCollaborationProject ecp
	on ecp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
left join Project p
	on p.ProjectID = ecp.ProjectID 
left join ProjectLanguage plen
	on plen.ProjectID = p.ProjectID 
	and plen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
left join ProjectLanguage ples
	on ples.ProjectID = p.ProjectID 
	and ples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
left join ProjectLanguage plgl
	on plgl.ProjectID = p.ProjectID 
	and plgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
left join Personnel p1
	on p1.PersonnelID = ec.IPCoordinadorIgfaeID 
left join PersonnelExperiment pe
	on pe.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
left join Personnel p2
	on p2.PersonnelID = pe.PersonnelID 
left join ExperimentalCollaborationProductionProject ecpp
	on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
left join ProductionProject pp
	on pp.ProductionProjectID = ecpp.ProductionProjectID 
left join ProductionProjectLanguage pplen
	on pplen.ProductionProjectID = pp.ProductionProjectID 
	and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
left join ProductionProjectLanguage pples
	on pples.ProductionProjectID = pp.ProductionProjectID 
	and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
left join ProductionProjectLanguage pplgl
	on pplgl.ProductionProjectID = pp.ProductionProjectID 
	and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
where {$where}
group by ec.ExperimentalCollaborationID
order by ec.Name";

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $experiment = new IntranetExperimentalCollaboration(
            $row->Id,
            $row->Name,
            NULL,
            NULL,
            NULL,
            $row->StartDate,
            $row->EndDate,
            NULL,
            NULL,
            NULL,
            NULL,
            $row->Facilities,
            $row->Validated,
            $row->Published,
            $row->Abbreviation,
            $row->MouDate,
            NULL,
            $row->WebSite,
            $row->External,
            $row->Abstract,
            NULL,
            NULL,
            $row->Comment,
            $row->PublishedBy,
            $row->PublishedOn,
            $row->ValidatedBy,
            $row->ValidatedOn
        );
    }

    //Personnel
    if($row->IPCoordinatorIGFAEID != null){
        $personnel = [];
        foreach ($rows['rows'] as $row) {
            $personnel[$row->IPCoordinatorIGFAEID] = $row->IPCoordinatorIGFAE;
        }
        $experiment->setCoordinatorIgfae($personnel);
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
        $experiment->setResearchArea($researchAreas);
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
        $experiment->setResearchProgram($researchProgram);
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
        $experiment->setResearchGroup($researchGroup);
    }
    //Associated publications
    if($row->ProductionProjectsIDs != null){
        $associatedPublications = [];
        $ids = explode(" ; ", $row->ProductionProjectsIDs);
        $nombres = explode(" ; ", $row->ProductionProjects); 
        foreach ($ids as $item){  
            foreach ($nombres as $name){
                if(array_search($name, $associatedPublications, true) == ""){
                    $associatedPublications[$item] = $name;
                    break;
                }else{
                    $associatedPublications[$item] = "";
                }
            }  
        }       
        $experiment->setAsociatedPublications($associatedPublications);
    }

    //Research Project
    if($row->ResearchProjectsIDs != null){
        $researchProjects = [];
        $ids = explode(" ; ", $row->ResearchProjectsIDs);
        $nombres = explode(" ; ", $row->ResearchProjects);
        foreach ($ids as $item){
            foreach ($nombres as $name){
                if(array_search($name, $researchProjects, true) == ""){
                    $researchProjects[$item] = $name;
                    break;
                }else{
                    $researchProjects[$item] = "";
                }
            }  
        } 
        $experiment->setResearchProject($researchProjects);
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
        $experiment->setWorkLine($workLines);
    }

    //Associated users
    if($row->AssociatedUserID != null){
        $associatedUsers = [];
        $ids = explode(" ; ", $row->AssociatedUserID);
        $nombres = explode(" ; ", $row->AssociatedUsers); 
        foreach ($ids as $item){  
            foreach ($nombres as $name){
                if(array_search($name, $associatedUsers, true) == ""){
                    $associatedUsers[$item] = $name;
                    break;
                }else{
                    $associatedUsers[$item] = "";
                }
            }  
        } 
        $experiment->setAsociatedUsers($associatedUsers);
    }

    
    //Recuperamos los valores de MOU link
    $experimentId = $experiment->getExperimentalCollaborationId();
    $sql = "SELECT ml.MouLinkID, ml.Link as MouLink
    from MouLink ml
    where 1=1
    and ml.ExperimentalCollaborationID = '$experimentId'"
    ;

    //Mou Link
    $rows = igfae_intranet_fetch_data($sql);
    $mouLinks = [];
    foreach ($rows['rows'] as $row) {
        $mouLinks[] = $row->MouLink;
    }
    $experiment->setMouLink($mouLinks);


    return $experiment;
}

function existsExperimentalCollaboration($experimentalCollaborationId)
{
    $where = "";
    $params = [];

    $where .= " AND ec.ExperimentalCollaborationID = %d";
    $params[] = $experimentalCollaborationId;

    $sql = "SELECT DISTINCT
	ec.ExperimentalCollaborationID as 'id'
    FROM ExperimentalCollaboration ec 
        WHERE 1=1 {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

//Insert ExperimentalCollaborationResearchArea
function insertResearchAreaExperiment($id, $researchArea)
{
    if (isset($id)  && $id > 0 && isset($researchArea)) {
        foreach ($researchArea as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ExperimentalCollaborationResearchArea(ExperimentalCollaborationID,
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
//Insert ExperimentalCollaborationResearchProgram
function insertResearchProgramExperiment($id, $researchProgram)
{
    if (isset($id)  && $id > 0 && isset($researchProgram)) {
        foreach ($researchProgram as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ExperimentalCollaborationResearchProgram(ExperimentalCollaborationID,
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
//Insert ExperimentalCollaborationWorkLine
function insertWorkLineExperiment($id, $workLine)
{
    if (isset($id)  && $id > 0 && isset($workLine)) {
        foreach ($workLine as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ExperimentalCollaborationWorkLine(ExperimentalCollaborationID,
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

//Insert ExperimentalCollaborationResearchGroup
function insertResearchGroupExperiment($id, $researchGroup)
{
    if (isset($id)  && $id > 0 && isset($researchGroup)) {
        foreach ($researchGroup as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ExperimentalCollaborationResearchGroup(ExperimentalCollaborationID,
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

//Insert ExperimentalCollaborationProject
function insertResearchProjectExperiment($id, $researchProject)
{
    if (isset($id)  && $id > 0 && isset($researchProject)) {
        foreach ($researchProject as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ExperimentalCollaborationProject(ExperimentalCollaborationID,
                        ProjectID)
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

//Insert ExperimentalCollaborationProductionProject
function insertResearchProductionProjectExperiment($id, $asociatedPublications)
{
    if (isset($id)  && $id > 0 && isset($asociatedPublications)) {
        foreach ($asociatedPublications as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ExperimentalCollaborationProductionProject(ExperimentalCollaborationID,
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

//Insert MouLink
function insertMouLink($id, $mouLink)
{
    if (isset($id)  && $id > 0 && isset($mouLink)) {
        foreach ($mouLink as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO MouLink(ExperimentalCollaborationID,
                        Link)
                        VALUES
                        ('$id',
                        '$item'                    
                        )";
                igfae_intranet_save($sql);
            }
        }
    }  
}

//Insert PersonnelExperiment
function insertPersonnelExperiment($id, $asociatedUsers, $startDate, $endDate)
{
    if (isset($id)  && $id > 0 && isset($asociatedUsers)) {
        foreach ((array) $asociatedUsers as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO PersonnelExperiment(PersonnelID,
                        ExperimentalCollaborationID,
                        StartDate,
                        EndDate)
                        VALUES
                        ('$item',  
                        '$id',
                        '$startDate',
                        " . (isset($endDate) ? "'$endDate'" : "NULL") . "                 
                        )";
                igfae_intranet_save($sql);
            }
        }
    }  
}

/**
 * Indíca si el usuario está relacionado con el experimento.
 * @param: $user_id: user
 * @param: $id: id del experimento
 * @param: $mode: modo de búsqueda
 * Si modo == 1 solo si el usuario está relacionado. 
 * Si modo == 2 comprueba si el usuario está relacionado o es el creador
 */
function hasRelatedExperiment($user_id, $id, $mode = 2) {
    
    $sqlHasRelation =  "SELECT count(*)
    from ExperimentalCollaboration ec 
    left join PersonnelExperiment pe
	on pe.ExperimentalCollaborationID = ec.ExperimentalCollaborationID ";

    $sqlHasRelation .= " where 1=1 ";

    if ($mode == 1) {
        $sqlHasRelation .= "
            and (pe.PersonnelID = $user_id
            ";
    } else {
        $sqlHasRelation .= "
            and (pe.PersonnelID = $user_id or ec.CreatedBy = $user_id)
        ";
    }   
    
    $sqlHasRelation .= "
        and ec.ExperimentalCollaborationID = $id
    ";
    
    $result = igfae_intranet_get_var($sqlHasRelation);

    if ($result) {
        return true;
    } else {
        return false;
    }
}

function isUserCreatorOrRelatedWithExperiment($user_id){

    $idExperiment = $_GET['idExperiment'];

    if (!isset($idExperiment) || !isset($user_id)) {
        return false;
    }

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedExperiment($id, $idExperiment)) {
            return true;
        }
    }

    return false;
}
//Comprobamos si esta validado el experimento
function isExperimentalCollaborationConsolidated($id) { 
    $sql = "SELECT count(*) 
    from ExperimentalCollaboration ec
    where
    ec.Validated = 1
    and ec.ExperimentalCollaborationID = $id";
    
    $result = igfae_intranet_get_var($sql);
    
    if ($result) {
    return true;
    } else {
    return false;
    }
  }