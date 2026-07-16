<?php


function fetchResearchProject($researchProject, $offset = 0, $amount = 10, $order = null)
{
    $projects = [];
    $prepareParams = [];
    $where = "";

    if (isset($researchProject)) {
        
        $queryPrepare = getPrepareSearchResearchProject($researchProject);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $order = getOrderResearchProject($order);

    $sql = "select 
	a.ProjectID,
	a.Title,
	a.Ambit,
	a.ActivityType,
	group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
	group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
	group_concat(distinct ec.Name separator ' ; ') as Experiments,
	a.ResearchGroup,
	a.WorkLine,
	a.Announcement,
	group_concat(distinct concat(agu.Year, ' (', format(agu.Amount,4,'de_DE'), ')') separator ' ; ') as AnnualGiftedAmountUsc,
	a.StartDate,
	a.ConcessionDate,
	group_concat(distinct pk.name separator ' ; ') as Keywords,
	group_concat(distinct fa.AgencyName separator ' ; ') as FundingAgencies,
	a.ProjectReference,
	a.AmountWithoutOverheads,
	a.FEDER,	
	a.AnnualJustification,
	a.Extended,
	a.IndirectCosts,
	group_concat(distinct concat(p1.Name,  ' ', p1.FirstSurname, ' ', coalesce(p1.SecondSurname,'') ) separator ' ; ') as members,
	a.ProjectType,
	a.BudgetLink,
	a.CompetitiveCall,
	a.IGFAELeader,
	a.Validated,
	a.Published 
    from (
        select distinct
            p.ProjectID,
            COALESCE(plen.Title, ples.Title, plgl.Title) as Title,
            s.Name as Ambit,
            pat.Name as ActivityType,
            concat(rg.Name, ' (', rg.Acronym, ')') as ResearchGroup,
            wl.Name as WorkLine,
            p.Announcement,
            p.StartDate,
            p.ConcessionDate,
            group_concat(distinct pk.name separator ' ; ') as Keywords,
            p.ProjectReference,
            p.AmountWithoutOverheads,
            p.FEDER,           
            p.AnnualJustification,
            p.Extended,
            p.IndirectCosts,
            pt.Name as ProjectType,
            p.BudgetLink,
            p.CompetitiveCall,
            p.IGFAELeader,
            p.Validated,
            p.Published 
        FROM Project p
        left join ProjectLanguage plen
            on plen.ProjectID = p.ProjectID 
            and plen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
        left join ProjectLanguage ples
            on ples.ProjectID = p.ProjectID 
            and ples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
        left join ProjectLanguage plgl
            on plgl.ProjectID = p.ProjectID 
            and plgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
        left join `Scope` s
            on s.ScopeID = p.ScopeID 
        left join ProjectActivityType pat
            on pat.ActivityTypeID = p.ActivityTypeID 
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
        left join AnnualGiftedUsc agu
            on agu.ProjectID = p.ProjectID
        left join ProjectKeyword pk
            on pk.ProjectID = p.ProjectID 
        left join ProjectFinancingAgency pfa
            on pfa.ProjectID = p.ProjectID 
        left join FinancingAgency fa
            on fa.FinancingAgencyID = pfa.FinancingAgencyID 
        left join ProjectType pt
            on pt.ProjectTypeID = p.ProjectTypeID 
       	left join ProjectPersonnel ppIpRefUSC
            on ppIpRefUSC.ProjectID = p.ProjectID 
            and ppIpRefUSC.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
        left join ProjectPersonnel ppIp
            on ppIp.ProjectID = p.ProjectID 
            and ppIp.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IP . "
        left join ProjectPersonnel ppAss
            on ppAss.ProjectID = p.ProjectID 
            and ppAss.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IA . "
        left join ProjectPersonnel ppTech
            on ppTech.ProjectID = p.ProjectID 
            and ppTech.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_TE . "         
        left join ProjectPersonnel pp
            on pp.ProjectID = p.ProjectID     
        where 1=1
            and (p.Consortium is null or p.Consortium = 0)
            {$where}
        group by p.ProjectID
        order by p.ProjectID desc        
    ) a
    -- Es necesario realizar los joins para elementos multiples que sean visibles en los resultados para no perder informacion en en una columna de una tupla. La tupla puede salir pero con meno info debido a los filtros
    left join ProjectResearchArea pra
        on pra.ProjectID = a.ProjectID 
    left join ResearchArea ra
        on ra.ResearchAreaID = pra.ResearchAreaID 
    left join ProjectResearchProgram prp
        on prp.ProjectID = a.ProjectID 
    left join ResearchProgram  rp
        on rp.ResearchProgramID = prp.ResearchProgramID 
    left join ExperimentalCollaborationProject ecp
        on ecp.ProjectID = a.ProjectID 
    left join ExperimentalCollaboration ec
        on ec.ExperimentalCollaborationID = ecp.ExperimentalCollaborationID 
    left join AnnualGiftedUsc agu
        on agu.ProjectID = a.ProjectID
    left join ProjectKeyword pk
        on pk.ProjectID = a.ProjectID 
    left join ProjectFinancingAgency pfa
        on pfa.ProjectID = a.ProjectID 
    left join FinancingAgency fa
        on fa.FinancingAgencyID = pfa.FinancingAgencyID
    left join ProjectPersonnel pp
        on pp.ProjectID = a.ProjectID 
    left join Personnel p1
        on p1.PersonnelID = pp.PersonnelID 
    group by a.ProjectID
    ORDER BY {$order}";   

    $pagination = getPaginationInfoResearchProject($offset, $amount, $sql, $prepareParams);

    $sql .= " LIMIT {$offset}, {$amount}";
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $amountOverheads = isset($row->AmountWithoutOverheads) ? formatNumberMiles($row->AmountWithoutOverheads) : $row->AmountWithoutOverheads;
        $indirectCosts = isset($row->IndirectCosts) ? formatNumberMiles($row->IndirectCosts) : $row->IndirectCosts;
        $projects[] = new IntranetResearchProject(
            $row->ProjectID,            
            $row->Title,
            $row->Ambit,
            $row->ActivityType,
            $row->ResearchAreas,
            $row->ResearchPrograms,
            $row->Experiments,
            [],
            $row->WorkLine,
            $row->Announcement,
            NULL,
            NULL,
            NULL,
            NULL,         
            $row->AnnualGiftedAmountUsc,            
            $row->StartDate,
            $row->ConcessionDate,
            $row->Keywords,
            $row->FundingAgencies,            
            $row->ProjectReference,
            NULL,
            NULL,
            $amountOverheads,
            $row->FEDER,                       
            $row->AnnualJustification,
            $row->Extended,
            NULL,
            NULL,
            $indirectCosts,            
            [],
            $row->ProjectType,
            $row->BudgetLink,
            [],
            $row->IGFAELeader,
            NULL,                     
            $row->Validated,
            $row->Published,
            NULL,
            NULL,
            NULL
        );
    }

    $info = [
        'projects' => $projects,
        'pagination' => $pagination
    ];

    return $info;
}

function fetchIdProjectExport($researchProject) { 
    $idProjects = [];
    $where = "";
    $prepareParams = [];

    if (isset($researchProject)) {
        $queryPrepare = getPrepareSearchResearchProject($researchProject);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }

    $sql = "select distinct a.ProjectID
    from (
        select distinct
            p.ProjectID,
            COALESCE(plen.Title, ples.Title, plgl.Title) as Title,
            s.Name as Ambit,
            pat.Name as ActivityType,
            concat(rg.Name, ' (', rg.Acronym, ')') as ResearchGroup,
            wl.Name as WorkLine,
            p.Announcement,
            p.StartDate,
            p.ConcessionDate,
            group_concat(distinct pk.name separator ' ; ') as Keywords,
            p.ProjectReference,
            p.AmountWithoutOverheads,
            p.FEDER,           
            p.AnnualJustification,
            p.Extended,
            p.IndirectCosts,
            pt.Name as ProjectType,
            p.BudgetLink,
            p.CompetitiveCall,
            p.IGFAELeader,
            p.Validated,
            p.Published 
        FROM Project p
        left join ProjectLanguage plen
            on plen.ProjectID = p.ProjectID 
            and plen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
        left join ProjectLanguage ples
            on ples.ProjectID = p.ProjectID 
            and ples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
        left join ProjectLanguage plgl
            on plgl.ProjectID = p.ProjectID 
            and plgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
        left join `Scope` s
            on s.ScopeID = p.ScopeID 
        left join ProjectActivityType pat
            on pat.ActivityTypeID = p.ActivityTypeID 
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
        left join AnnualGiftedUsc agu
            on agu.ProjectID = p.ProjectID
        left join ProjectKeyword pk
            on pk.ProjectID = p.ProjectID 
        left join ProjectFinancingAgency pfa
            on pfa.ProjectID = p.ProjectID 
        left join FinancingAgency fa
            on fa.FinancingAgencyID = pfa.FinancingAgencyID 
        left join ProjectType pt
            on pt.ProjectTypeID = p.ProjectTypeID 
        left join ProjectPersonnel pp
            on pp.ProjectID = p.ProjectID 
        left join Personnel p1
            on p1.PersonnelID = pp.PersonnelID 
        left join ProjectPersonnel ppIpRefUSC
            on ppIpRefUSC.ProjectID = p.ProjectID 
            and ppIpRefUSC.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
        left join ProjectPersonnel ppIp
            on ppIp.ProjectID = p.ProjectID 
            and ppIp.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IP . "
        left join ProjectPersonnel ppAss
            on ppAss.ProjectID = p.ProjectID 
            and ppAss.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IA . "
        left join ProjectPersonnel ppTech
            on ppTech.ProjectID = p.ProjectID 
            and ppTech.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_TE . "
        where 1=1 
            and (p.Consortium is null or p.Consortium = 0)
            {$where}
        group by p.ProjectID
        order by p.ProjectID desc        
    ) a
    -- Es necesario realizar los joins para elementos multiples que sean visibles en los resultados para no perder informacion en en una columna de una tupla. La tupla puede salir pero con meno info debido a los filtros
    left join ProjectResearchArea pra
        on pra.ProjectID = a.ProjectID 
    left join ResearchArea ra
        on ra.ResearchAreaID = pra.ResearchAreaID 
    left join ProjectResearchProgram prp
        on prp.ProjectID = a.ProjectID 
    left join ResearchProgram  rp
        on rp.ResearchProgramID = prp.ResearchProgramID 
    left join ExperimentalCollaborationProject ecp
        on ecp.ProjectID = a.ProjectID 
    left join ExperimentalCollaboration ec
        on ec.ExperimentalCollaborationID = ecp.ExperimentalCollaborationID 
    left join AnnualGiftedUsc agu
        on agu.ProjectID = a.ProjectID
    left join ProjectKeyword pk
        on pk.ProjectID = a.ProjectID 
    left join ProjectFinancingAgency pfa
        on pfa.ProjectID = a.ProjectID 
    left join FinancingAgency fa
        on fa.FinancingAgencyID = pfa.FinancingAgencyID
    left join ProjectPersonnel pp
        on pp.ProjectID = a.ProjectID 
    left join Personnel p1
        on p1.PersonnelID = pp.PersonnelID 
    group by a.ProjectID";

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $idProjects[] = $row->ProjectID;
    }
    
    return $idProjects;

}

function fetchResearchProjectExport($idProjects) {
    
    $result = [];
    $where = "";
    $prepareParams = [];
    
    if (isset($idProjects) && !empty($idProjects)) {
        $typesCount = count($idProjects);
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersScope = implode(',', $paramsPlaceholders);

        $where .= " p.ProjectID IN ({$placeholdersScope})";
        $prepareParams = array_merge($idProjects, $idProjects);
    

        $sql = "select a.*, b.*
        from (
            select
                p.ProjectID as ID,
                p.Request,
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
                group_concat(distinct concat(agu.Year, ' (', format(agu.Amount,4,'de_DE'), ')') separator ' ; ') as AnnualGiftedUsc,
                group_concat(distinct concat(ae.Year, ' (', format(ae.Amount,4,'de_DE'), ')') separator ' ; ') as AnnualExecuted,
                group_concat(distinct concat(aa.Year, ' (', format(aa.Amount,4,'de_DE'), ')') separator ' ; ') as AwardedAllotment,
                group_concat(distinct concat(aj.Year, ' (', format(aj.Amount,4,'de_DE'), ')') separator ' ; ') as AnnualJustified
            from Project p
            left join ProjectLanguage plen
                on plen.ProjectID = p.ProjectID 
                and plen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
            left join ProjectLanguage ples
                on ples.ProjectID = p.ProjectID 
                and ples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
            left join ProjectLanguage plgl
                on plgl.ProjectID = p.ProjectID 
                and plgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
            where {$where}
            group by p.ProjectID
        ) a
        inner join 
        (
            -- Usuarios relacionados, esta en 2 porque esta ultima tiene pesimo rendimiento
            select
                p.ProjectID as ID,
                group_concat(distinct concat(peripc.Name, ' ', peripc.FirstSurname, ' ', coalesce(peripc.SecondSurname, '')) separator ' ; ') as IPCoordinatesUsers,
                group_concat(distinct concat(perip.Name, ' ', perip.FirstSurname, ' ', coalesce(perip.SecondSurname, '')) separator ' ; ') as IPUsers,
                group_concat(distinct concat(pera.Name, ' ', pera.FirstSurname, ' ', coalesce(pera.SecondSurname, '')) separator ' ; ') as AssociatedUsers,
                group_concat(distinct concat(pert.Name, ' ', pert.FirstSurname, ' ', coalesce(pert.SecondSurname, '')) separator ' ; ') as TechnicianUsers,               
		        group_concat(distinct concat(perwt.Name, ' ', perwt.FirstSurname, ' ', coalesce(perwt.SecondSurname, '')) separator ' ; ') as IPWorkTeamUsers
            from Project p
            left join ProjectPersonnel pperipc
                on pperipc.ProjectID = p.ProjectID 
                and pperipc.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IPCP."
            left join Personnel peripc
                on pperipc.PersonnelID = peripc.PersonnelID 
            left join ProjectPersonnel pperip
                on pperip.ProjectID = p.ProjectID 
                and pperip.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IP."
            left join Personnel perip
                on pperip.PersonnelID = perip.PersonnelID
            left join ProjectPersonnel ppera
                on ppera.ProjectID = p.ProjectID 
                and ppera.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IA."
            left join Personnel pera
                on ppera.PersonnelID = pera.PersonnelID
            left join ProjectPersonnel ppert
                on ppert.ProjectID = p.ProjectID 
                and ppert.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_TE."
            left join Personnel pert
                on ppert.PersonnelID = pert.PersonnelID
            left join ProjectPersonnel ppwt
                on ppwt.ProjectID = p.ProjectID 
                and ppwt.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_ET."
            left join Personnel perwt
                on ppwt.PersonnelID = perwt.PersonnelID

            where {$where}
            group by p.ProjectID
        ) b
        on a.ID = b.ID";
        
        $result = igfae_intranet_fetch_data($sql, $prepareParams)['rows'];
    }

    return $result;
}

function getPrepareSearchResearchProject($researchProject)
{
    $where = " ";
    $params = [];
    
    // Identificador
    if ($researchProject->getId()) {
        $where .= " AND (p.ProjectID = %d)";
        $params[] = $researchProject->getId();
    }
    // Title input
    if ($researchProject->getTitle()) {
        $where .= " AND (plen.Title LIKE %s  or ples.Title like %s or plgl.Title like %s)";       
        $params[] = '%' . $researchProject->getTitle() . '%';
        $params[] = '%' . $researchProject->getTitle() . '%';
        $params[] = '%' . $researchProject->getTitle() . '%';
    }

    // Scope input
    if ($researchProject->getScope()) {
        $typesCount = count($researchProject->getScope());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersScope = implode(',', $paramsPlaceholders);

        $where .= " AND (s.ScopeID IN ({$placeholdersScope}))";
        $params = array_merge($params, $researchProject->getScope());
    }

    // Activity type input
    if ($researchProject->getTypeActivity()) {
        $typesCount = count($researchProject->getTypeActivity());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersTypeActivity = implode(',', $paramsPlaceholders);

        $where .= " AND (p.ActivityTypeID IN ({$placeholdersTypeActivity}))";
        $params = array_merge($params, $researchProject->getTypeActivity());
    }

    // Research area input
    if ($researchProject->getResearchArea()) {
        $typesCount = count($researchProject->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (pra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $researchProject->getResearchArea());
    }

    // Research program input
    if ($researchProject->getResearchProgram()) {
        $typesCount = count($researchProject->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (prp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $researchProject->getResearchProgram());
    }

    // Collaboration input
    if ($researchProject->getCollaborationExperiment()) {
        $typesCount = count($researchProject->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExperiment = implode(',', $paramsPlaceholders);

        $where .= " AND (ecp.ExperimentalCollaborationID IN ({$placeholdersExperiment}))";
        $params = array_merge($params, $researchProject->getCollaborationExperiment());
    }

    // Research group input
    if ($researchProject->getResearchGroup()) {
        $typesCount = count($researchProject->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (p.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $researchProject->getResearchGroup());
    }

    // Work line input
    if ($researchProject->getWorkLine()) {
        $typesCount = count($researchProject->getWorkLine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersWorkLine = implode(',', $paramsPlaceholders);

        $where .= " AND (p.WorkLineID IN ({$placeholdersWorkLine}))";
        $params = array_merge($params, $researchProject->getWorkLine());
    }

    // Announcement input
    if ($researchProject->getAnnouncement()) {
        $where .= " AND p.Announcement like %s ";       
        $params[] = '%' . $researchProject->getAnnouncement() . '%';
    }
        
    // annualGifteDateFrom input
    if ($researchProject->getAnnualGifteDateFrom()) {
        $where .= " AND agu.`Year` >= %d ";       
        $params[] = $researchProject->getAnnualGifteDateFrom();
    }

    // annualGifteDateUntil input
    if ($researchProject->getAnnualGifteDateUntil()) {
        $where .= " AND agu.`Year` <= %d ";       
        $params[] = $researchProject->getAnnualGifteDateUntil();
    }

    // annualGifteAmountFrom input
    if ($researchProject->getAnnualGifteAmountFrom()) {
        $where .= " AND agu.Amount >= %f ";       
        $params[] = $researchProject->getAnnualGifteAmountFrom();
    }

    // annualGifteAmountUntil input
    if ($researchProject->getAnnualGifteAmountUntil()) {
        $where .= " AND agu.Amount <= %f ";       
        $params[] = $researchProject->getAnnualGifteAmountUntil();
    }

    // Start Date and End Date
    if ($researchProject->getStartDate() && $researchProject->getEndDate()) {
        $startDates =  explode("-", $researchProject->getStartDate());
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
        $endDates =  explode("-", $researchProject->getEndDate());
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
        
    }  else if ($researchProject->getStartDate()) {
        $startDates =  explode("-", $researchProject->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( p.StartDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    } else if ($researchProject->getEndDate()){
        $endDates =  explode("-", $researchProject->getEndDate());
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

    // concession date input
    if ($researchProject->getConcessionDate()) {
        $startDates =  explode("-", $researchProject->getConcessionDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (p.ConcessionDate BETWEEN %s and %s )";

        $params[] = $fecha1;
        $params[] = $fecha2;
    }    

    //keywords input
    if ($researchProject->getKeywords()) {        
        $typesCount = count($researchProject->getKeywords());
        $paramsPlaceholders = array_fill(0, $typesCount, '%s');
        $placeholdersKeywords = implode(',', $paramsPlaceholders);        

        $where .= " AND (pk.Name IN ({$placeholdersKeywords}))";
        $params = array_merge($params, $researchProject->getKeywords());
    }

    // agencies input
    if ($researchProject->getFinancingAgencies()) {
        $typesCount = count($researchProject->getFinancingAgencies());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAgencies = implode(',', $paramsPlaceholders);

        $where .= " AND (pfa.FinancingAgencyID IN ({$placeholdersAgencies}))";
        $params = array_merge($params, $researchProject->getFinancingAgencies());
    }

    // Reference input
    if ($researchProject->getReference()) {
        $where .= " AND p.ProjectReference like %s ";       
        $params[] = '%' . $researchProject->getReference() . '%';
    }

    // AmountOverheadFrom input
    if ($researchProject->getAmountOverheadFrom()) {    
        $where .= " AND p.AmountWithoutOverheads >= %f ";       
        $params[] = $researchProject->getAmountOverheadFrom();
    }

    // AmountOverheadUntil input
    if ($researchProject->getAmountOverheadUntil()) {
        $where .= " AND p.AmountWithoutOverheads <= %f ";       
        $params[] = $researchProject->getAmountOverheadUntil();
    }

    // FEDER input
    if ($researchProject->getFeder() != null && count($researchProject->getFeder()) > 0
    && $researchProject->getFeder()[0] != "") {
        $typesCount = count($researchProject->getFeder());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersFeder = implode(',', $paramsPlaceholders);

        $where .= " AND (p.FEDER IN ({$placeholdersFeder}))";
        $params = array_merge($params, $researchProject->getFeder());
    }   

    // Annual Justification input
    if ($researchProject->getJustification() != null && count($researchProject->getJustification()) > 0
    && $researchProject->getJustification()[0] != "") {
        $typesCount = count($researchProject->getJustification());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersJustification = implode(',', $paramsPlaceholders);

        $where .= " AND (p.AnnualJustification IN ({$placeholdersJustification}))";
        $params = array_merge($params, $researchProject->getJustification());
    }

    // Extended input
    if ($researchProject->getExtended() != null && count($researchProject->getExtended()) > 0
        && $researchProject->getExtended()[0] != "") {        
        $typesCount = count($researchProject->getExtended());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExtended = implode(',', $paramsPlaceholders);

        $where .= " AND (p.Extended IN ({$placeholdersExtended}))";
        $params = array_merge($params, $researchProject->getExtended());
    }

    // Indirect cost from
    if ($researchProject->getIndirectCostsFrom()) {
        $where .= " AND p.IndirectCosts >= %f ";       
        $params[] = $researchProject->getIndirectCostsFrom();
    }

    // Indirect cost until
    if ($researchProject->getIndirectCostsUntil()) {
        $where .= " AND p.IndirectCosts <= %f ";       
        $params[] = $researchProject->getIndirectCostsUntil();
    }

    // projectCoordinator
    if ($researchProject->getProjectCoordinator()) {
        $typesCount = count($researchProject->getProjectCoordinator());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersMembers = implode(',', $paramsPlaceholders);

        $where .= " AND (ppIpRefUSC.PersonnelID IN ({$placeholdersMembers}))";
        $params = array_merge($params, $researchProject->getProjectCoordinator());
    }

    // principalResearcher
    if ($researchProject->getPrincipalResearcher()) {
        $typesCount = count($researchProject->getPrincipalResearcher());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersMembers = implode(',', $paramsPlaceholders);

        $where .= " AND (ppIp.PersonnelID IN ({$placeholdersMembers}))";
        $params = array_merge($params, $researchProject->getPrincipalResearcher());
    }
     // Technical
     if ($researchProject->getTechnical()) {        
        $typesCount = count($researchProject->getTechnical());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersMembers = implode(',', $paramsPlaceholders);

        $where .= " AND (ppTech.PersonnelID IN ({$placeholdersMembers}))";
        $params = array_merge($params, $researchProject->getTechnical());
    }

    // associateResearchers
    if ($researchProject->getAssociateResearchers()) {
        $typesCount = count($researchProject->getAssociateResearchers());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersMembers = implode(',', $paramsPlaceholders);

        $where .= " AND (ppAss.PersonnelID IN ({$placeholdersMembers}))";
        $params = array_merge($params, $researchProject->getAssociateResearchers());
    }

    // Project type input
    if ($researchProject->getTypeProject()) {
        $typesCount = count($researchProject->getTypeProject());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersProjectType = implode(',', $paramsPlaceholders);

        $where .= " AND (p.ProjectTypeID IN ({$placeholdersProjectType}))";
        $params = array_merge($params, $researchProject->getTypeProject());
    }

    // Budget link input
    if ($researchProject->getBudgetLink()) {
        $where .= " AND p.BudgetLink like %s ";       
        $params[] = '%' . $researchProject->getBudgetLink() . '%';
    }

    // Competitive input
    if ($researchProject->getCompetitive() != null) {
        $where .= " AND p.CompetitiveCall = %d ";       
        $params[] = $researchProject->getCompetitive();
    }

    // IGFAELeader input
    if ($researchProject->getIgfaeLeader() != null) {
        $where .= " AND p.IGFAELeader = %d ";       
        $params[] = $researchProject->getIgfaeLeader();
    }

    // Reference2 input
    if ($researchProject->getReference2()) {
        $where .= " AND p.Reference2 like %s ";       
        $params[] = '%' . $researchProject->getReference2() . '%';
    }

    // Validated input
    if ($researchProject->getValidated() != null) {     
        $where .= " AND p.Validated = %d ";       
        $params[] = $researchProject->getValidated();
    }

    // Published input
    if ($researchProject->getPublished() != null) {
        $where .= " AND p.Published = %d ";       
        $params[] = $researchProject->getPublished();
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

function getOrderResearchProject($order)
{
    if (!isset($order)) {
        return ' a.ProjectID desc';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: //title
                $sort .= 'a.Title ';
                break;
            case 1:  //Ambit
                $sort .= 'a.Ambit ';
                break;
            case 2: //ActivityType
                $sort .= 'a.ActivityType ';
                break;
            case 3: //ResearchAreas
                $sort .= 'ResearchAreas ';
                break;
            case 4: //ResearchPrograms
                $sort .= 'ResearchPrograms ';
                break;
            case 5: //Experiments
                $sort .= 'Experiments ';
                break;
            case 6: //WorkLine
                $sort .= 'a.WorkLine ';
                break;
            case 7: //Announcement
                $sort .= 'a.Announcement ';
                break;
            case 8: //AnnualGiftedAmountUsc
                $sort .= 'AnnualGiftedAmountUsc ';
                break;
            case 9: //StartDate
                $sort .= 'a.StartDate ';
                break;
            case 10: //ConcessionDate
                $sort .= 'a.ConcessionDate ';
                break;
            case 11: //Keywords
                $sort .= 'Keywords ';
                break;
            case 12: //FundingAgencies
                $sort .= 'FundingAgencies ';
                break;
            case 13: //ProjectReference
                $sort .= 'a.ProjectReference ';
                break;
            case 14: //AmountWithoutOverheads
                $sort .= 'a.AmountWithoutOverheads ';
                break;
            case 15: //FEDER
                $sort .= 'a.FEDER ';
                break;           
            case 16: //AnnualJustification
                $sort .= 'a.AnnualJustification ';
                break;
            case 17: //Extended
                $sort .= 'a.Extended ';
                break;
            case 18: //IndirectCosts
                $sort .= 'a.IndirectCosts ';
                break;
            case 19: //ProjectType
                $sort .= 'a.ProjectType ';
                break;
            case 20: //BudgetLink
                $sort .= 'a.BudgetLink ';
                break;
            case 21: //IGFAELeader
                $sort .= 'a.IGFAELeader ';
                break;
            case 22: //Validated
                $sort .= 'a.Validated ';
                break;
            case 23: //published
                $sort .= 'a.Published ';
                break;
            default:
                $sort .= 'a.Title ';
                break;                                                                                   
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function getPaginationInfoResearchProject($offset, $amount, $sql, $params = [])
{

    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset/$amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];

    return $result;
}

function getSelectResearchProjects($texto, $page)
{
     
    $where = "";   
    $params = []; 
    if (isset($texto)) {
        $where .= " AND ( plen.Title like %s or ples.Title like %s or plgl.Title like %s)";
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        //Añadimos los params para la segunda clausula where
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';        
    }   

    $sql = "SELECT x.totalCounts as 'totalCounts',
    p.ProjectID as 'projectId',	
    COALESCE (plen.Title, ples.Title, plgl.Title) as 'title'				
		FROM                  
        Project p
        left join ProjectLanguage plen
	    on plen.ProjectID = p.ProjectID 
	    and plen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
        left join ProjectLanguage ples
	    on ples.ProjectID = p.ProjectID 
	    and ples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
        left join ProjectLanguage plgl
	    on plgl.ProjectID = p.ProjectID 
	    and plgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL.",
        (select count(*) as totalCounts 
        FROM Project p
        left join ProjectLanguage plen
        on plen.ProjectID = p.ProjectID 
	    and plen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
        left join ProjectLanguage ples
	    on ples.ProjectID = p.ProjectID 
	    and ples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
        left join ProjectLanguage plgl
	    on plgl.ProjectID = p.ProjectID 
	    and plgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
         where 1=1 {$where}) as x 
        WHERE 1=1
        {$where} 
		ORDER BY 
        COALESCE (plen.Title, ples.Title, plgl.Title)
		ASC";


    // Obtenemos los resultados en grupos
    $amount = IGFAE_INTRANET_NUMBER_ELEMENTS_SELECT_ASYNC;
    if (is_null($page)) {
        $page = 1;
    }
    $offset = (($page-1) * $amount);
    $sql .= " LIMIT {$offset}, {$amount}";
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->projectId, "full_name" => $row->title);
    }
    $result['items']  = $resultArray;
    $result['total_count'] = $row->totalCounts;
   
    return $result;
}

function getResearchProjectById($projectId)
{
    $researchProject = NULL;
    $where = "";
    $prepareParams = [];

    if (isset($projectId)) {
        $where = " p.ProjectID  = %d";
        $prepareParams = $projectId;
    }


    $sql = "select
                p.ProjectID,
                p.Request,
                p.ProjectReference,
                plen.Title as TitleEn,
                ples.Title as TitleEs,
                plgl.Title as TitleGl,
                s.Name as Ambit,
                s.ScopeID as AmbitId,
                p.Description,
                p.Website,
                pat.ActivityTypeID,
                pat.Name as ActivityType,
                uat.USCActivityTypeID,
                concat (uat.Name, ' (', uat.Code, ')') as USCActivityType,
                group_concat( distinct usat.USCSubactivityTypeID separator ' ; ') as USCSubactivityIDs,
                group_concat( distinct concat (usat.Name, ' (', usat.Code, ')') separator ' ; ') as USCSubactivity,
                p.USCProjectReference,
                p.Announcement,
                group_concat(distinct fa.FundingAgencyID separator ' ; ') as FundingAgenciesIDs,
                group_concat(distinct fa.Name separator ' ; ') as FundingAgencies,
                p.AmountWithoutOverheads,
                p.`Granted`,
                p.ConcessionDate,
                p.OfficeCode,
                p.Reference2,                
                group_concat(distinct peripc.PersonnelID separator ' ; ') as IPCoordinatesUsersIDs,
                group_concat(distinct concat(peripc.Name, ' ', peripc.FirstSurname, ' ', coalesce(peripc.SecondSurname, '')) separator ' ; ') as IPCoordinatesUsers,
                group_concat(distinct perip.PersonnelID separator ' ; ') as IPUsersIDs,
                group_concat(distinct concat(perip.Name, ' ', perip.FirstSurname, ' ', coalesce(perip.SecondSurname, '')) separator ' ; ') as IPUsers,
                group_concat(distinct pera.PersonnelID separator ' ; ') as AssociatedUsersIDs,
                group_concat(distinct concat(pera.Name, ' ', pera.FirstSurname, ' ', coalesce(pera.SecondSurname, '')) separator ' ; ') as AssociatedUsers,
                group_concat(distinct pert.PersonnelID separator ' ; ') as TechnicianUsersIDs,
                group_concat(distinct concat(pert.Name, ' ', pert.FirstSurname, ' ', coalesce(pert.SecondSurname, '')) separator ' ; ') as TechnicianUsers,
                group_concat(distinct perwt.PersonnelID separator ' ; ') as IPWorkTeamUsersIDs,
	            group_concat(distinct concat(perwt.Name, ' ', perwt.FirstSurname, ' ', coalesce(perwt.SecondSurname, '')) separator ' ; ') as IPWorkTeamUsers,
                p.StartDate,
                p.EndDate,
                group_concat(distinct fia.FinancingAgencyID separator ' ; ') as FinancingAgenciesIDs,
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
                group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
                group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
                group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
                group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
                group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
                group_concat(distinct ec.Name separator ' ; ') as Experiments,
                p.ResearchGroupID,
                concat(rg.Name, ' (', rg.Acronym, ')') as ResearchGroup,
                wl.WorkLineID,
                wl.Name as WorkLine,
                p.Competitive,
                group_concat(distinct pk.name separator ' ; ') as Keywords,
                p.IndirectCosts,
                p.CompetitiveCall,
                p.IGFAELeader,
                pt.Name as ProjectType,
                pt.ProjectTypeID as ProjectTypeId,
                p.BudgetLink,
                p.Consortium,
                p.Comment,
                p.Validated, 
                p.ValidatedBy,
                p.ValidatedOn,
                p.Published,
                p.PublishedBy,
                p.PublishedOn 
            from Project p
            left join ProjectLanguage plen
                on plen.ProjectID = p.ProjectID 
                and plen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
            left join ProjectLanguage ples
                on ples.ProjectID = p.ProjectID 
                and ples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
            left join ProjectLanguage plgl
                on plgl.ProjectID = p.ProjectID 
                and plgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
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
            left join ProjectPersonnel pperipc
                on pperipc.ProjectID = p.ProjectID 
                and pperipc.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IPCP."
            left join Personnel peripc
                on pperipc.PersonnelID = peripc.PersonnelID 
            left join ProjectPersonnel pperip
                on pperip.ProjectID = p.ProjectID 
                and pperip.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IP."
            left join Personnel perip
                on pperip.PersonnelID = perip.PersonnelID
            left join ProjectPersonnel ppera
                on ppera.ProjectID = p.ProjectID 
                and ppera.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IA."
            left join Personnel pera
                on ppera.PersonnelID = pera.PersonnelID
            left join ProjectPersonnel ppert
                on ppert.ProjectID = p.ProjectID 
                and ppert.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_TE."
            left join Personnel pert
                on ppert.PersonnelID = pert.PersonnelID
                left join ProjectPersonnel ppwt
                on ppwt.ProjectID = p.ProjectID 
                and ppwt.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_ET."
            left join Personnel perwt
                on ppwt.PersonnelID = perwt.PersonnelID
            where {$where}
            group by p.ProjectID";


    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $researchProject = new IntranetResearchProject();
        $researchProject->setIdProject($row->ProjectID);
        $researchProject->set_reference($row->ProjectReference);
        $researchProject->setTitleEn($row->TitleEn);
        $researchProject->setTitleEs($row->TitleEs);
        $researchProject->setTitleGl($row->TitleGl);
        $researchProject->set_scope(createArrayValues($row->AmbitId, $row->Ambit));
        $researchProject->setDescription($row->Description);
        $researchProject->setWebsite($row->Website);
        $researchProject->setActivityType(createArrayValues($row->ActivityTypeID, $row->ActivityType));
        $researchProject->setUscActivityType(createArrayValues($row->USCActivityTypeID, $row->USCActivityType));
        $researchProject->setUscSubActivityType(createArrayValues($row->USCSubactivityIDs, $row->USCSubactivity));
        $researchProject->setUscProjectReference($row->USCProjectReference);
        $researchProject->set_announcement($row->Announcement);
        $researchProject->setFundingAgencies(createArrayValues($row->FundingAgenciesIDs, $row->FundingAgencies));
        $researchProject->set_amountWithoutOverheads($row->AmountWithoutOverheads);
        $researchProject->setGranted($row->Granted);
        $researchProject->set_concessionDate($row->ConcessionDate);
        $researchProject->setOfficeCode($row->OfficeCode);
        $researchProject->setReference2($row->Reference2);        
        $researchProject->setIpCoordinatesUsers(createArrayValues($row->IPCoordinatesUsersIDs, $row->IPCoordinatesUsers));
        $researchProject->setIpUsers(createArrayValues($row->IPUsersIDs, $row->IPUsers));
        $researchProject->setAssociatedUsers(createArrayValues($row->AssociatedUsersIDs, $row->AssociatedUsers));
        $researchProject->setTechnicianUsers(createArrayValues($row->TechnicianUsersIDs, $row->TechnicianUsers));
        $researchProject->setWorkTeam(createArrayValues($row->IPWorkTeamUsersIDs, $row->IPWorkTeamUsers));
        $researchProject->set_startDate($row->StartDate);
        $researchProject->setEndDate($row->EndDate);
        $researchProject->set_financingAgencies(createArrayValues($row->FinancingAgenciesIDs, $row->FinancingAgencies));
        $researchProject->setTotalAmount($row->TotalAmount);
        $researchProject->setAnnualjustification($row->AnnualJustification);
        $researchProject->setCofinancing($row->Cofinancing);
        $researchProject->setCofinancingType($row->CofinancingType);
        $researchProject->setResolution($row->Resolution);
        $researchProject->set_extended($row->Extended);
        $researchProject->setEndDateExtended($row->EndDateExtended);
        $researchProject->set_feder($row->FEDER);
        $researchProject->setFederPercent($row->FEDERPercent);
        $researchProject->set_researchArea(createArrayValues($row->ResearchAreasIDs, $row->ResearchAreas));
        $researchProject->set_researchProgram(createArrayValues($row->ResearchProgramsIDs, $row->ResearchPrograms));
        $researchProject->set_collaborationExperiment(createArrayValues($row->ExperimentsIDs, $row->Experiments));
        $researchProject->set_researchGroup(createArrayValues($row->ResearchGroupID, $row->ResearchGroup));
        $researchProject->set_workLine(createArrayValues($row->WorkLineID, $row->WorkLine));
        $researchProject->set_competitive($row->Competitive);
        $researchProject->set_keywords($row->Keywords);
        $researchProject->set_indirectCosts($row->IndirectCosts);
        $researchProject->setCompetitiveCall($row->CompetitiveCall);
        $researchProject->set_igfaeLeader($row->IGFAELeader);
        $researchProject->set_typeProject(createArrayValues($row->ProjectTypeId,$row->ProjectType));
        $researchProject->set_budgetLink($row->BudgetLink);
        $researchProject->setComment($row->Comment);
        $researchProject->setConsortium($row->Consortium);
        $researchProject->set_validated($row->Validated);
        $researchProject->setValidatedBy($row->ValidatedBy);
        $researchProject->setValidatedOn($row->ValidatedOn);
        $researchProject->set_published($row->Published);
        $researchProject->setPublishedBy($row->PublishedBy);
        $researchProject->setPublishedOn($row->PublishedOn);
        $researchProject->setProjectRequest($row->Request);
    }


    // Recuperamos los valores de los datos economicos
    $projectId = $researchProject->getId();
    $sql = "select aa.AwardedAllotmentID, aa.`Year`, format(aa.Amount, ".IGFAE_INTRANET_NUMBER_FORMAT_DECIMAL_4.") as Amount, 'AA' as `Type`
            from AwardedAllotment aa
            where aa.ProjectID = '$projectId'
            union ALL
            -- Dotado por anualidad USC
            select agu.AnnualGiftedUscID, agu.`Year`, format(agu.Amount, ".IGFAE_INTRANET_NUMBER_FORMAT_DECIMAL_4.") as Amount, 'AG' as `Type`
            from AnnualGiftedUsc agu
            where agu.ProjectID = '$projectId'
            union ALL
            -- Ejecutado por anualidad
            select ae.AnnualExecutedID , ae.`Year`, format(ae.Amount, ".IGFAE_INTRANET_NUMBER_FORMAT_DECIMAL_4.") as Amount, 'AE' as `Type`
            from AnnualExecuted ae 
            where ae.ProjectID = '$projectId'
            union ALL 
            -- Justificado por anualidad
            select aj.AnnualJustifiedID, aj.`Year`, format(aj.Amount, ".IGFAE_INTRANET_NUMBER_FORMAT_DECIMAL_4.") as Amount, 'AJ' as `Type`
            from AnnualJustified aj 
            where aj.ProjectID = '$projectId'";
    
    $rows = igfae_intranet_fetch_data($sql);
    $awardedAllotmen = [];
    $annualGifted = [];
    $annualExecuted = [];
    $annualJustified = [];

    foreach ($rows['rows'] as $key => $row) {
        if ($row->Type == IGFAE_INTRANET_AWARDED_ALLOTMENT_TYPE) {
            $awardedAllotmen[$row->Year] = $row->Amount;
        }
        if ($row->Type == IGFAE_INTRANET_ANNUAL_GIFTED_TYPE) {
            $annualGifted[$row->Year] =$row->Amount;
        }
        if ($row->Type == IGFAE_INTRANET_ANNUAL_EXECUTED_TYPE) {
            $annualExecuted[$row->Year] = $row->Amount;
        }
        if ($row->Type == IGFAE_INTRANET_ANNUAL_JUSTIFIED_TYPE) {
            $annualJustified[$row->Year] = $row->Amount;
        }

    }
    $researchProject->setAwardedAllotment($awardedAllotmen);
    $researchProject->setAnnualGifted($annualGifted);
    $researchProject->setAnnualExecuted($annualExecuted);
    $researchProject->setAnnualJustified($annualJustified);

    // Recuperamos los partners del proyecto
    $sql = "select pp.ProjectPartnerID, pp.Name 
            from ProjectPartner pp
            where pp.ProjectID = '$projectId'";

    $rows = igfae_intranet_fetch_data($sql);

    $partners = [];
    foreach ($rows['rows'] as $key => $row) {
        $partners[$row->ProjectPartnerID] = $row->Name;
    }
    $researchProject->setPartners($partners);


    return $researchProject;

   
}

function createArrayValues($identificadores, $valores) {
    $arrayIds = explode(' ; ', $identificadores);
    $arrayValues = explode(' ; ', $valores);
    $resultado = [];

    $posicion = 0;
    foreach ($arrayIds as $id) {
        $resultado[$id] = $arrayValues[$posicion];
        $posicion ++;
    }

    return $resultado;
}

function saveResearchProject($datosInsert, $researchProject){
    $idProject = $_POST['idProject'];
    $id = NULL;

    if (isset($idProject) && strlen($idProject) > 0) {
        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        $where = array('ProjectID' => $idProject);

        igfae_intranet_update("Project", $datosInsert, $where, null, null);

        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsResearchProject($idProject);

        //Alta datos tablas relacionadas
        insertResearchLanguage($idProject, $researchProject->getTitleEn(), $researchProject->getTitleEs(), $researchProject->getTitleGl());
        insertAnnualAmounts($idProject, $researchProject->getAwardedAllotment(), 'AwardedAllotment');
        insertAnnualAmounts($idProject, $researchProject->getAnnualGifted(), 'AnnualGiftedUsc');
        insertAnnualAmounts($idProject, $researchProject->getAnnualJustified(), 'AnnualJustified');
        insertAnnualAmounts($idProject, $researchProject->getAnnualExecuted(), 'AnnualExecuted');
        insertResearchCollaboration($idProject, $researchProject->getCollaborationExperiment());
        insertResearchFundingAgency($idProject, $researchProject->getFundingAgencies());
        insertResearchKeywords($idProject, $researchProject->getKeywords());
        insertResearchPartners($idProject, $researchProject->getPartners());
        insertResearchPersonnel($idProject, $researchProject->getIpUsers(), $researchProject->getIpCoordinatesUsers(),
             $researchProject->getAssociatedUsers(), $researchProject->getTechnicianUsers(), $researchProject->getWorkTeam());
        insertResearchAreas($idProject, $researchProject->getResearchArea());
        insertResearchPrograms($idProject, $researchProject->getResearchProgram());
        insertResearchFiancingAgencies($idProject, $researchProject->getFinancingAgencies());
        insertUSCSubactivity($idProject, $researchProject->getUscSubActivityType());

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_PROJECT, $idProject);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PROJECT, $idProject);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_PROJECT, $idProject);
        }

        // Authorization
        $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_PROJECT, $idProject);
        if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
            closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_PROJECT, $idProject);
        }        

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    } else {
        //NUEVO PROYECTO
        global $current_user;

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        $datosInsert['CreatedBy'] = getUserByWPUserID($current_user->ID)[0]["id"];
        
        $id = igfae_intranet_insert("Project", $datosInsert, null);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_PROJECT, $id);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PROJECT, $id);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_PROJECT, $id);
        }

        //Alta datos tablas relacionadas
        insertResearchLanguage($id, $researchProject->getTitleEn(), $researchProject->getTitleEs(), $researchProject->getTitleGl());
        insertAnnualAmounts($id, $researchProject->getAwardedAllotment(), 'AwardedAllotment');
        insertAnnualAmounts($id, $researchProject->getAnnualGifted(), 'AnnualGiftedUsc');
        insertAnnualAmounts($id, $researchProject->getAnnualJustified(), 'AnnualJustified');
        insertAnnualAmounts($id, $researchProject->getAnnualExecuted(), 'AnnualExecuted');
        insertResearchCollaboration($id, $researchProject->getCollaborationExperiment());
        insertResearchFundingAgency($id, $researchProject->getFundingAgencies());
        insertResearchKeywords($id, $researchProject->getKeywords());
        insertResearchPartners($id, $researchProject->getPartners());
        insertResearchPersonnel($id,$researchProject->getIpUsers(), $researchProject->getIpCoordinatesUsers(),
            $researchProject->getAssociatedUsers(), $researchProject->getTechnicianUsers(), $researchProject->getWorkTeam());
        insertResearchAreas($id, $researchProject->getResearchArea());
        insertResearchPrograms($id, $researchProject->getResearchProgram());
        insertResearchFiancingAgencies($id, $researchProject->getFinancingAgencies());
        insertUSCSubactivity($id, $researchProject->getUscSubActivityType());

         /** END TRANSACTION */
         if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    }

    return $id;
}

// Delete project
function deleteRelationsResearchProject($id) {
    $where = array('ProjectID' => $id);

    //Borramos las tablas relacionadas
    igfae_intranet_delete('ProjectLanguage', $where);
    igfae_intranet_delete('AwardedAllotment', $where);
    igfae_intranet_delete('AnnualGiftedUsc', $where);
    igfae_intranet_delete('AnnualJustified', $where);
    igfae_intranet_delete('AnnualExecuted', $where);
    igfae_intranet_delete('ExperimentalCollaborationProject', $where);
    igfae_intranet_delete('ProjectFundingAgency', $where);
    igfae_intranet_delete('ProjectKeyword', $where);
    igfae_intranet_delete('ProjectPartner', $where);
    igfae_intranet_delete('ProjectPersonnel', $where);
    igfae_intranet_delete('ProjectResearchArea', $where);
    igfae_intranet_delete('ProjectResearchProgram', $where);
    igfae_intranet_delete('ProjectFinancingAgency', $where);
    igfae_intranet_delete('ProjectUSCSubactivity', $where);
}

function insertResearchLanguage($id, $titleEN, $titleES, $titleGL){    
    if (isset($id) && $id > 0) {
        $table = "ProjectLanguage";
        if (isset($titleEN) && strlen($titleEN) > 0) {
            $data = array('ProjectID' => $id, 'LanguageID' => IGFAE_INTRANET_LANGUAGE_EN, 'Title' => $titleEN );
            igfae_intranet_insert($table, $data, null);
        }
        if (isset($titleES) && strlen($titleES) > 0) {           
            $data = array('ProjectID' => $id, 'LanguageID' => IGFAE_INTRANET_LANGUAGE_ES, 'Title' => $titleES );
            igfae_intranet_insert($table, $data, null);
        }
        if (isset($titleGL) && strlen($titleGL) > 0) {
            $data = array('ProjectID' => $id, 'LanguageID' => IGFAE_INTRANET_LANGUAGE_GL, 'Title' => $titleGL );
            igfae_intranet_insert($table, $data, null);
        }
    }
}

function insertAnnualAmounts($id, $datosTabla, $nombreTabla) {
    if (isset($id)  && $id > 0 && isset($datosTabla)) {        
        foreach ($datosTabla as $item) {
            $data = array('Year' => $item['year'], 'Amount' => formatNumberToBD($item['amount']), 'ProjectID' => $id );
            igfae_intranet_insert($nombreTabla, $data);            
        }
    }
}

function insertResearchCollaboration($id, $collaborationExperiment)
{
    if (isset($id)  && $id > 0 && isset($collaborationExperiment)) {
        $data = array('ExperimentalCollaborationID' => $collaborationExperiment, 'ProjectID' => $id );
            igfae_intranet_insert('ExperimentalCollaborationProject', $data);
    }
}

function insertResearchFundingAgency($id, $fundingAgency)
{
    if (isset($id)  && $id > 0 && isset($fundingAgency)) {       
        foreach ($fundingAgency as $agency) {
            $data = array('FundingAgencyID' => $agency, 'ProjectID' => $id );
            igfae_intranet_insert('ProjectFundingAgency', $data);
            
        }
    }
}

function insertResearchKeywords($id, $keywords) {
    if (isset($id)  && $id > 0 && isset($keywords)) {
        existKeywords($keywords);
        foreach ($keywords as $keyword) {
            $data = array('ProjectID' => $id, 'Name' => $keyword );
            igfae_intranet_insert('ProjectKeyword', $data);
            
        }
    }
}

function insertResearchPersonnel($id,$pincipalResearcher, $projectCoordinator, $associateResearchers, $technical, $workTeam){
    if (isset($id)  && $id > 0) {
        $tabla = 'ProjectPersonnel';

        if (isset($pincipalResearcher)) {
            foreach ($pincipalResearcher as $item) {
                $data= array('ProjectID' => $id, 'PersonnelID' => $item, 'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_IP);
                igfae_intranet_insert($tabla, $data);                
            }
        }

        if (isset($projectCoordinator)) {
            foreach ($projectCoordinator as $coordinator) {
                $data= array('ProjectID' => $id, 'PersonnelID' => $coordinator, 'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_IPCP);
                igfae_intranet_insert($tabla, $data);                
            }            
        }

        if (isset($associateResearchers)) {
            foreach ($associateResearchers as $associate) {
                $data= array('ProjectID' => $id, 'PersonnelID' => $associate, 'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_IA);
                igfae_intranet_insert($tabla, $data);                
            }            
        }

        if (isset($technical)) {
            foreach ($technical as $item) {
                $data= array('ProjectID' => $id, 'PersonnelID' => $item, 'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_TE);
                igfae_intranet_insert($tabla, $data);                
            }            
        }

        if (isset($workTeam)) {
            foreach ($workTeam as $item) {
                $data= array('ProjectID' => $id, 'PersonnelID' => $item, 'PersonnelTypeID' => IGFAE_INTRANET_PERSONNEL_TYPE_ET);
                igfae_intranet_insert($tabla, $data);                
            }            
        }
    }
}

function insertResearchAreas($id, $researchArea) {
    if (isset($id)  && $id > 0 && isset($researchArea)) {
        foreach ($researchArea as $area) {
            $data = array('ResearchAreaID' => $area, 'ProjectID' => $id );
            igfae_intranet_insert('ProjectResearchArea', $data);
        }
        
    }
}

function insertResearchPrograms($id, $researchProgram) {
    if (isset($id)  && $id > 0 && isset($researchProgram)) {
        foreach ($researchProgram as $program) {
            $data = array('ResearchProgramID' => $program, 'ProjectID' => $id );
            igfae_intranet_insert('ProjectResearchProgram', $data);
        }
    }
}

function insertResearchFiancingAgencies($id, $financingAgencies) {
    if (isset($id)  && $id > 0 && isset($financingAgencies)) { 
        foreach ($financingAgencies as $financingAgency) {
            $data = array('ProjectID' => $id, 'FinancingAgencyID' => $financingAgency );
            igfae_intranet_insert('ProjectFinancingAgency', $data);
            
        }
    }
}

function insertUSCSubactivity($id, $subTypeActivities) {
    if (isset($id)  && $id > 0 && isset($subTypeActivities)) {      
        foreach ($subTypeActivities as $subTypeActivity) {
            $data = array('ProjectID' => $id, 'USCSubactivityTypeID' => $subTypeActivity );
            igfae_intranet_insert('ProjectUSCSubactivity', $data);
            
        }
    }
}
function insertResearchPartners($id, $partners) {
    
    if (isset($id)  && $id > 0 && isset($partners)) {    
        foreach ($partners as $partner) {
            $data = array('ProjectID' => $id, 'Name' => $partner );
            igfae_intranet_insert('ProjectPartner', $data);
            
        }
    }
}

function deleteResearchProject($idResearchProject) {

    /** START TRANSACTION */
    igfae_intranet_begin_transaction();

    deleteRelationsResearchProject($idResearchProject);

    $where = array('ProjectID' => $idResearchProject);

    //Eliminamos el proyecto
    $deleted = igfae_intranet_delete("Project", $where);

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_PROJECT, $idResearchProject);

     /** END TRANSACTION */
     if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }

    return $deleted;

}

function existsResearchProject($researchProjectId)
{
    $where = "";
    $params = [];

    $where .= " AND p.ProjectID = %d";
    $params[] = $researchProjectId;

    $sql = "SELECT distinct
	p.ProjectID as 'id'
    FROM Project p 
        WHERE 1=1 {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

function getUSCSubtypeByID($project)
{
    if($project->getUscSubActivityType()){

        $where = "";
        $params = [];

        if ($project->getUscSubActivityType()) {
            $typesCount = count($project->getUscSubActivityType());
            $paramsPlaceholders = array_fill(0, $typesCount, '%d');
            $placeholdersTypeActivity = implode(',', $paramsPlaceholders);
            $where .= " AND (ut.USCSubactivityTypeID in ({$placeholdersTypeActivity}))";
            $params = array_merge($params, $project->getUscSubActivityType());
        }

        $sql = "SELECT distinct ut.USCActivityTypeID as 'id'
                from USCSubactivityType ut
                WHERE 1=1 
                {$where} ";

        $rows = igfae_intranet_fetch_data($sql, $params);
        
        $resultArray = [];

        foreach ($rows['rows'] as $row) {
            $resultArray[] = $row->id;
        }
        return $resultArray;
    }else{
        return null;
    }
}

/**
 * Indíca si el usuario está relacionado con el proyecto.
 * @param: $user_id: user
 * @param: $id: id del proyecto
 * @param: $mode: modo de búsqueda
 * Si modo == 1 solo si el usuario está relacionado. 
 * Si modo == 2 comprueba si el usuario está relacionado o es el creador
 */
function hasRelatedResearchProject($user_id, $id, $mode = 2) {
    

    $sqlHasRelation =  "select count(*) 
    from Project p
    inner join ProjectPersonnel pp
        on pp.ProjectID = p.ProjectID ";

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
        and p.ProjectID = $id
    ";
    
    $result = igfae_intranet_get_var($sqlHasRelation);

    if ($result) {
        return true;
    } else {
        return false;
    }
}

function isUserCreatorOrRelatedWithResearchProject($user_id){

    $idProject = $_GET['idProject'];

    if (!isset($idProject) || !isset($user_id)) {
        return false;
    }

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedResearchProject($id, $idProject)) {
            return true;
        }
    }

    return false;
}
//Comprobamos si esta validado el proyecto de investigacion
function isResearchProjectConsolidated($id) { 
    $sql = "SELECT count(*) 
    from Project p
    where
    p.Validated = 1
    and p.ProjectID = $id";
    
    $result = igfae_intranet_get_var($sql);
    
    if ($result) {
    return true;
    } else {
    return false;
    }
}

function getResearchProjectAsinc($id)
{
    $where = "";
    $params = [];
    $where .= " AND (p.ProjectID = %d)";
    $params[] = $id;
    
    $sql = "SELECT p.ProjectID as 'projectId',	
    COALESCE (plen.Title, ples.Title, plgl.Title) as 'title'				
		FROM                  
        Project p
        left join ProjectLanguage plen
	    on plen.ProjectID = p.ProjectID 
	    and plen.LanguageID = ".IGFAE_INTRANET_LANGUAGE_EN."
        left join ProjectLanguage ples
	    on ples.ProjectID = p.ProjectID 
	    and ples.LanguageID = ".IGFAE_INTRANET_LANGUAGE_ES."
        left join ProjectLanguage plgl
	    on plgl.ProjectID = p.ProjectID 
	    and plgl.LanguageID = ".IGFAE_INTRANET_LANGUAGE_GL."
        WHERE 1=1
        {$where};";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->projectId, "full_name" => $row->title);
    }

    return $resultArray[0];
    
}