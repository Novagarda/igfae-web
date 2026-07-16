<?php

function fetchProductionProjectExport($productionProject)
{
    $resultIds = fetchProductionProject($productionProject, 0, NULL);
    $result = [];
    $idsProyect = [];
    $idsProyectStr = "";
    foreach ($resultIds['projects'] as $row) {
        $idsProyect[] = $row->getId();
    }
    $idsProyectStr = implode(',', $idsProyect);

    if (count($idsProyect) > 0) {
        $sql = "SELECT 	
    pp.ProductionProjectID as 'id',
	ppat.Name as ActivityType,
	pplen.Title as TitleEn,
	pples.Title as TitleEs,
	pplgl.Title as TitleGl,
	j.FullName as JournalName,
	pperScopus.Reference as ScopusRef,
	pperWos.Reference as WosRef,
	pperInspire.Reference as InspireRef,
	pp.ArticleNumber,
	pp.AcceptanceDate,
	pp.Date,
	ppt.ReadDate as ThesisReadDate,
	tt.Name as ThesisType,
	ppt.ReadLocation as ThesisReadLocation,
	q.Name as ThesisQualification,
	ppt.InternationalMention,
	ppt.Link as ThesisLink,
	pp.DOI,
	pp.ISBN,
	group_concat(distinct ppk.Name separator ' ; ') as Keywords,
	concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) as ThesisDirector,
	ed.Name as ThesisExternalDirectorName,
	je.Name as JournalEditorial,
	pp.Citation,
	pp.Volume,
	pp.ChapterBook,
	pp.StartingPage,
	pp.EndingPage,
	pp.NumberOfPages,
	pp.NumAuthors,
	COALESCE (pplen1.Title, pples1.Title, pplgl1.Title) as ReferenceProjectName,
	pp.Link,
	pp.SpiIndex,
	pp.InternationalCoAuthorship,
	pp.Abstract,
	pp.Comment,
	pp.Validated,
	pp.ValidatedBy,
	pp.ValidatedOn,
	pp.Published,
	pp.PublishedBy,
	pp.PublishedOn 
    from ProductionProject pp
    inner join ProductionProjectActivityType ppat
        on ppat.ActivityTypeID = pp.ActivityTypeID 
    left join ProductionProjectLanguage pplen
        on pplen.ProductionProjectID = pp.ProductionProjectID 
        and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProductionProjectLanguage pples
        on pples.ProductionProjectID = pp.ProductionProjectID 
        and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProductionProjectLanguage pplgl
        on pplgl.ProductionProjectID = pp.ProductionProjectID 
        and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "                       
    left join Journal j
        on j.JournalID = pp.JournalID
    left join ProductionProjectExternalReference pperScopus
        on pperScopus.ProductionProjectID = pp.ProductionProjectID 
        and pperScopus.SourceID = " . IGFAE_INTRANET_SOURCE_SCOPUS . "
    left join ProductionProjectExternalReference pperWos
        on pperWos.ProductionProjectID = pp.ProductionProjectID 
        and pperWos.SourceID = " . IGFAE_INTRANET_SOURCE_WOS . "
    left join ProductionProjectExternalReference pperInspire
        on pperInspire.ProductionProjectID = pp.ProductionProjectID 
        and pperInspire.SourceID = " . IGFAE_INTRANET_SOURCE_INSPIRE . "
    left join ProductionProjectThesis ppt
        on ppt.ProductionProjectID = pp.ProductionProjectID
    left join ThesisType tt
        on tt.ThesisTypeID = ppt.ThesisTypeID 
    left join ProductionProjectThesisQualification pptq
        on pptq.ProductionProjectID = pp.ProductionProjectID 
    left join Qualification q
        on q.QualificationID = pptq.QualificationID 
    left join ProductionProjectThesisDirector pptd
        on pptd.ProductionProjectID = pp.ProductionProjectID 
    left join Personnel p1
        on p1.PersonnelID = pptd.DirectorID 
    left join ProductionProjectExternalDirector ppex
        on ppex.ProductionProjectID = pp.ProductionProjectID 
    left join ExternalDirector ed
        on ppex.ExternalDirectorID = ed.ExternalDirectorID             
    left join ProductionProjectEditorial ppe
        on ppe.ProductionProjectID = pp.ProductionProjectID
    left join JournalEditorial je
        on ppe.JournalEditorialID = je.JournalEditorialID
    left join ProductionProject pp2
        on pp.LinkProductionProjectID = pp2.ProductionProjectID 
    left join ProductionProjectLanguage pplen1
        on pplen1.ProductionProjectID = pp2.ProductionProjectID 
        and pplen1.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProductionProjectLanguage pples1
        on pples1.ProductionProjectID = pp2.ProductionProjectID 
        and pples1.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProductionProjectLanguage pplgl1
        on pplgl1.ProductionProjectID = pp2.ProductionProjectID 
        and pplgl1.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    left join ProductionProjectKeyword ppk
	on ppk.ProductionProjectID = pp.ProductionProjectID
    WHERE     
        pp.ProductionProjectID in ($idsProyectStr)
        group by pp.ProductionProjectID ";

        $result = igfae_intranet_fetch_data($sql);

        //Recuperamos los autores locales
        $sql = "select pp.ProductionProjectID as 'id', group_concat(concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) separator ' ; ') as 'name'
        from ProductionProject pp
        inner join ProductionProjectAuthor ppa
            on ppa.ProductionProjectID = pp.ProductionProjectID 
        inner join Personnel p1
            on p1.PersonnelID = ppa.PersonnelID 
        where pp.ProductionProjectID in ($idsProyectStr)
        group by pp.ProductionProjectID";

        $resultAuthors = igfae_intranet_fetch_data($sql);

        //Recuperamos los autores externos
        $sql = "select pp.ProductionProjectID as 'id', group_concat(distinct concat(ea.Name, ' ', ea.Surname ) separator ' ; ') as 'name'
        from ProductionProject pp
        inner join ProductionProjectExternalAuthor ppea
            on pp.ProductionProjectID = ppea.ProductionProjectID 
        inner join ExternalAuthor ea
            on ppea.ExternalAuthorID = ea.ExternalAuthorID 
        where pp.ProductionProjectID in ($idsProyectStr)
        group by pp.ProductionProjectID";
 
        $resultExternalAuthors = igfae_intranet_fetch_data($sql);

        //Grupos de investigación
        $sql = "select pp.ProductionProjectID as 'id', group_concat(distinct concat (rg.Name, ' (', rg.Acronym, ')')  separator ' ; ') as 'name'
        from ProductionProject pp
        inner join ProductionProjectResearchGroup pprg
            on pprg.ProductionProjectID = pp.ProductionProjectID 
        inner join ResearchGroup rg
            on pprg.ResearchGroupID = rg.ResearchGroupID 
        where pp.ProductionProjectID in ($idsProyectStr)
        group by pp.ProductionProjectID";

        $resultResearchGroup = igfae_intranet_fetch_data($sql);

        //Areas de investigación   
        $sql = "select pp.ProductionProjectID as 'id', group_concat(distinct ra.Name separator ' ; ') as 'name'
        from ProductionProject pp
        inner join ProductionProjectResearchArea ppra
            on ppra.ProductionProjectID = pp.ProductionProjectID 
        inner join ResearchArea ra
            on ppra.ResearchAreaID = ra.ResearchAreaID 
        where pp.ProductionProjectID in ($idsProyectStr)    
        group by pp.ProductionProjectID";

        $resultResearchArea = igfae_intranet_fetch_data($sql);

        //Programas de investigación
        $sql = "select pp.ProductionProjectID as 'id', group_concat(distinct concat (rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as 'name'
        from ProductionProject pp
        inner join ProductionProjectResearchProgram pprp
            on pprp.ProductionProjectID = pp.ProductionProjectID 
        inner join ResearchProgram rp
            on pprp.ResearchProgramID = rp.ResearchProgramID 
        where pp.ProductionProjectID in ($idsProyectStr)
        group by pp.ProductionProjectID";

        $resultResearchProgram = igfae_intranet_fetch_data($sql);

        //Proyectos de investigación
        $sql = "select pp.ProductionProjectID as 'id', group_concat(COALESCE (plen.Title, ples.Title, plgl.Title)  separator ' ; ') as 'name'
        from ProductionProject pp
        inner join ProductionProjectProject ppp
            on ppp.ProductionProjectID = pp.ProductionProjectID 
        inner join Project p
            on ppp.ProjectID = p.ProjectID 
        left join ProjectLanguage plen
            on plen.ProjectID = p.ProjectID 
            and plen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        left join ProjectLanguage ples
            on ples.ProjectID = p.ProjectID 
            and ples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
        left join ProjectLanguage plgl
            on plgl.ProjectID = p.ProjectID 
            and plgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
        where pp.ProductionProjectID in ($idsProyectStr)
        group by pp.ProductionProjectID";

        $resultResearchProyect = igfae_intranet_fetch_data($sql);

        //Experimentos
        $sql = "select pp.ProductionProjectID as 'id', group_concat(distinct ec.Name  separator ' ; ') as 'name'
        from ProductionProject pp
        inner join ExperimentalCollaborationProductionProject ecpp
            on ecpp.ProductionProjectID = pp.ProductionProjectID
        inner join ExperimentalCollaboration ec
            on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
        where pp.ProductionProjectID in ($idsProyectStr)
        group by pp.ProductionProjectID";

        $resultExperiments = igfae_intranet_fetch_data($sql);

        foreach ($result['rows'] as $row) {
            // Authores
            $namesAuthorsStr = "";
            $namesAuthors = [];
            foreach ($resultAuthors['rows'] as $rowAuthors) {
                if ($row->id == $rowAuthors->id) {
                    $namesAuthors[] = $rowAuthors->name;
                }
            }
            if (count($namesAuthors) > 0) {
                $namesAuthorsStr = implode(' ; ', $namesAuthors);
            }
            // External authors
            $namesExternalAuthorsStr = "";
            $namesExternalAuthors = [];
            foreach ($resultExternalAuthors['rows'] as $rowExternalAuthors) {
                if ($row->id == $rowExternalAuthors->id) {
                    $namesExternalAuthors[] = $rowExternalAuthors->name;
                }
            }
            if (count($namesExternalAuthors) > 0) {
                $namesExternalAuthorsStr = implode(' ; ', $namesExternalAuthors);
            }
            // Research groups
            $namesResearchGroupStr = "";
            $namesResearchGroups = [];
            foreach ($resultResearchGroup['rows'] as $rowReserachGroup) {
                if ($row->id == $rowReserachGroup->id) {
                    $namesResearchGroups[] = $rowReserachGroup->name;
                }
            }
            if (count($namesResearchGroups) > 0) {
                $namesResearchGroupStr = implode(' ; ', $namesResearchGroups);
            }
            // Research areas
            $namesResearchAreaStr = "";
            $namesResearchAreas = [];
            foreach ($resultResearchArea['rows'] as $rowReserachArea) {
                if ($row->id == $rowReserachArea->id) {
                    $namesResearchAreas[] = $rowReserachArea->name;
                }
            }
            if (count($namesResearchAreas) > 0) {
                $namesResearchAreaStr = implode(' ; ', $namesResearchAreas);
            }
            // Research programs
            $namesResearchProgramStr = "";
            $namesResearchPrograms = [];
            foreach ($resultResearchProgram['rows'] as $rowReserachProgram) {
                if ($row->id == $rowReserachProgram->id) {
                    $namesResearchPrograms[] = $rowReserachProgram->name;
                }
            }
            if (count($namesResearchPrograms) > 0) {
                $namesResearchProgramStr = implode(' ; ', $namesResearchPrograms);
            }
            // Research projects
            $namesResearchProjectStr = "";
            $namesResearchProjects = [];
            foreach ($resultResearchProyect['rows'] as $rowReserachProject) {
                if ($row->id == $rowReserachProject->id) {
                    $namesResearchProjects[] = $rowReserachProject->name;
                }
            }
            if (count($namesResearchProjects) > 0) {
                $namesResearchProjectStr = implode(' ; ', $namesResearchProjects);
            }

            // Experiments
            $namesExperimentsStr = "";
            $namesExperiments = [];
            foreach ($resultExperiments['rows'] as $rowExperiment) {
                if ($row->id == $rowExperiment->id) {
                    $namesExperiments[] = $rowExperiment->name;
                }
            }
            if (count($namesExperiments) > 0) {
                $namesExperimentsStr = implode(' ; ', $namesExperiments);
            }
            $row->nameAuthors = $namesAuthorsStr;
            $row->nameExternalAuthors = $namesExternalAuthorsStr;
            $row->researchGroup = $namesResearchGroupStr;
            $row->researchArea = $namesResearchAreaStr;
            $row->researchProgram = $namesResearchProgramStr;
            $row->researchProject = $namesResearchProjectStr;
            $row->experiment = $namesExperimentsStr;
        }
        return $result['rows'];
    } else {
        return [];
    }
    
}

function fetchProductionProject($productionProject, $offset = 0, $amount = 10, $order = null)
{
    $projects = [];
    $where = "";
    $prepareParams = [];
    if (isset($productionProject)) {
        $queryPrepare = getPrepareSearchProductionProject($productionProject);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }
    $order = getOrderProductionProject($order);

    $sql = "SELECT distinct
	pp.ProductionProjectID as 'id', 
	COALESCE(pplen.Title, pples.Title, pplgl.Title) as 'title',
	j.FullName  as 'journalName',
	pp.Citation as 'citation',
	ppt.InternationalMention as 'internationalMention',
	pp.Validated as 'validated',
	pp.Published as 'published' 
    FROM ProductionProject pp
    left join ProductionProjectLanguage pplen
        on pplen.ProductionProjectID = pp.ProductionProjectID 
        and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProductionProjectLanguage pples
        on pples.ProductionProjectID = pp.ProductionProjectID 
        and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProductionProjectLanguage pplgl
        on pplgl.ProductionProjectID = pp.ProductionProjectID 
        and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    left join Journal j
        on j.JournalID = pp.JournalID 
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
    left join ProductionProjectKeyword ppk
        on ppk.ProductionProjectID = pp.ProductionProjectID 
    left join ProductionProjectAuthor ppa
        on ppa.ProductionProjectID = pp.ProductionProjectID
    left join PersonnelExternalSource pes
        on pes.PersonnelID = ppa.PersonnelID 
    left join Source s
        on s.SourceID = pes.SourceID 
        and s.SourceID = " . IGFAE_INTRANET_SOURCE_ORCID . "
    left join ProductionProjectThesisDirector pptd
        on pptd.ProductionProjectID = ppt.ProductionProjectID 
    left join ProductionProjectEditorial ppe
        on ppe.ProductionProjectID = pp.ProductionProjectID         
    left join ProductionProjectExternalAuthor ppea
        on ppea.ProductionProjectID = pp.ProductionProjectID 
    left join ProductionProjectExternalDirector pped
        on pped.ProductionProjectID = ppt.ProductionProjectID
    ";

    $sql .= "           
    WHERE     
        {$where} 
    ORDER BY {$order}";

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoProductionProject($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);
    foreach ($rows['rows'] as $row) {
        $projects[] = new IntranetProductionProject(
            $row->id,
            [],
            $row->title,
            NULL,
            NULL,
            NULL,
            [],
            [],
            [],
            [],
            $row->journalName,
            NULL,
            NULL,
            NULL,
            [],
            $row->internationalMention,
            [],
            [],
            [],
            [],
            [],
            [],
            (strlen(strip_tags(ltrim($row->citation))) <= 25) ? strip_tags(ltrim($row->citation)) : (substr(strip_tags($row->citation), 0, 25) . "..."),
            $row->validated,
            $row->published,
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
        'projects' => $projects,
        'pagination' => $pagination
    ];

    return $info;
}

function getPrepareSearchProductionProject($productionProject)
{
    $where = "1=1 ";
    $params = [];
    // Title input
    if ($productionProject->getId()) {
        $where .= " AND (pp.ProductionProjectID  = %d)";
        $params[] = $productionProject->getId();
    }
    // Title input
    if ($productionProject->getTitle()) {
        $where .= " AND ((pplen.Title) like %s or (pples.Title) like %s or (pplgl.Title) like %s)";
        $params[] = '%' . $productionProject->getTitle() . '%';
        $params[] = '%' . $productionProject->getTitle() . '%';
        $params[] = '%' . $productionProject->getTitle() . '%';
    }
    // Type activity select multiple
    if ($productionProject->getTypeActivity()) {
        $typesCount = count($productionProject->getTypeActivity());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersTypeActivity = implode(',', $paramsPlaceholders);
        $where .= " AND (pp.ActivityTypeID IN ({$placeholdersTypeActivity}))";
        $params = array_merge($params, $productionProject->getTypeActivity());
    }
    // Research area select multiple
    if ($productionProject->getResearchArea()) {
        $typesCount = count($productionProject->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (ppra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $productionProject->getResearchArea());
    }
    // Research program select multiple
    if ($productionProject->getResearchProgram()) {
        $typesCount = count($productionProject->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (pprp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $productionProject->getResearchProgram());
    }
    // Collaboration experiment select multiple
    if ($productionProject->getCollaborationExperiment()) {
        $typesCount = count($productionProject->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersCollaborationExperiment = implode(',', $paramsPlaceholders);
        $where .= " AND (ecpp.ExperimentalCollaborationID IN ({$placeholdersCollaborationExperiment}))";
        $params = array_merge($params, $productionProject->getCollaborationExperiment());
    }
    // Research group select multiple
    if ($productionProject->getResearchGroup()) {
        $typesCount = count($productionProject->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);
        $where .= " AND (pprg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $productionProject->getResearchGroup());
    }
    // journal select multiple
    if ($productionProject->getJournal()) {
        $typesCount = count($productionProject->getJournal());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersJournal = implode(',', $paramsPlaceholders);
        $where .= " AND (j.JournalID IN ({$placeholdersJournal}))";
        $params = array_merge($params, $productionProject->getJournal());
    }
    // Article number input
    if ($productionProject->getArticleNumber()) {
        $where .= " AND (pp.ArticleNumber LIKE  %s)";
        $params[] = '%' . $productionProject->getArticleNumber() . '%';
    }
    // Thesis type select
    if ($productionProject->getThesisType()) {
        $typesCount = count($productionProject->getThesisType());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersThesisType = implode(',', $paramsPlaceholders);
        $where .= " AND (ppt.ThesisTypeID IN ({$placeholdersThesisType}))";
        $params = array_merge($params, $productionProject->getThesisType());
    }
    // International menthion select
    if ($productionProject->getInternationalMention() != null) {
        $where .= " AND (ppt.InternationalMention =  %d)";
        $params[] = $productionProject->getInternationalMention();
    }
    // DOI input
    if ($productionProject->getDoi()) {
        $where .= " AND ((pp.DOI) LIKE  %s)";
        $params[] = '%' . $productionProject->getDoi() . '%';
    }
    // Keywords select multiple
    if ($productionProject->getKeywords()) {
        $typesCount = count($productionProject->getKeywords());
        $paramsPlaceholders = array_fill(0, $typesCount, '%s');
        $placeholdersKeywords = implode(',', $paramsPlaceholders);
        $where .= " AND (ppk.Name IN ({$placeholdersKeywords}))";
        $params = array_merge($params, $productionProject->getKeywords());
    }
    // Authors select multiple
    if ($productionProject->getAuthors()) {
        $typesCount = count($productionProject->getAuthors());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAuthors = implode(',', $paramsPlaceholders);
        $where .= " AND (ppa.PersonnelID IN ({$placeholdersAuthors}))";
        $params = array_merge($params, $productionProject->getAuthors());
    }
    // Thesis Director select multiple
    if ($productionProject->getThesisDirector()) {
        $typesCount = count($productionProject->getThesisDirector());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersThesisDirector = implode(',', $paramsPlaceholders);
        $where .= " AND (pptd.DirectorID IN ({$placeholdersThesisDirector}))";
        $params = array_merge($params, $productionProject->getThesisDirector());
    }
    // Citation input
    if ($productionProject->getCitation()) {
        $where .= " AND (pp.Citation LIKE  %s)";
        $params[] = '%' . $productionProject->getCitation() . '%';
    }
    // SPI Indexing input
    if ($productionProject->getSpiIndexing()) {
        $where .= " AND (pp.SpiIndex LIKE  %s)";
        $params[] = '%' . $productionProject->getSpiIndexing() . '%';
    }
    // External Author select multiple
    if ($productionProject->getExternalAuthor()) {
        $typesCount = count($productionProject->getExternalAuthor());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExternalAuthor = implode(',', $paramsPlaceholders);
        $where .= " AND (ppea.ExternalAuthorID IN ({$placeholdersExternalAuthor}))";
        $params = array_merge($params, $productionProject->getExternalAuthor());
    }
    // External thesis director select multiple
    if ($productionProject->getExternalThesisDirector()) {
        $typesCount = count($productionProject->getExternalThesisDirector());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersExternalThesis = implode(',', $paramsPlaceholders);
        $where .= " AND (pped.ExternalDirectorID IN ({$placeholdersExternalThesis}))";
        $params = array_merge($params, $productionProject->getExternalThesisDirector());
    }
    // journal editorial select
    if ($productionProject->getJournalEditorial()) {
        $typesCount = count($productionProject->getJournalEditorial());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersJournalEditorial = implode(',', $paramsPlaceholders);
        $where .= " AND (ppe.JournalEditorialID IN ({$placeholdersJournalEditorial}))";
        $params = array_merge($params, $productionProject->getJournalEditorial());
    }
    // Validated
    if ($productionProject->getValidated() != null) {
        $where .= " AND (pp.Validated  =  %d)";
        $params[] = $productionProject->getValidated();
    }
    // Published
    if ($productionProject->getPublished() != null) {
        $where .= " AND (pp.Published  =  %d)";
        $params[] = $productionProject->getPublished();
    }
    // Orcid
    if ($productionProject->get_orcid() != null) {
        $where .= " AND pes.Code LIKE %s ";
        $params[] = '%' . $productionProject->get_orcid() . '%';
    }
    // International Coauthorship
    if ($productionProject->getInternationalCoAuthorship()) {
        $typesCount = count($productionProject->getInternationalCoAuthorship());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersInternationalCoauthorship = implode(',', $paramsPlaceholders);

        $where .= " AND (pp.InternationalCoAuthorship IN ({$placeholdersInternationalCoauthorship}))";
        $params = array_merge($params, $productionProject->getInternationalCoAuthorship());
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
            $where .= " AND (ppa.PersonnelID = %d or ppe.PersonnelID = %d or pptd.DirectorID = %d or pp.CreatedBy  =  %d)";
            $params[] =  $igfae_user_id;
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

function getOrderProductionProject($order)
{
    if (!isset($order)) {
        return ' title ASC';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: //title
                $sort .= 'title ';
                break;
            case 1:  //journalName
                $sort .= 'journalName ';
                break;
            case 2: //citation
                $sort .= 'citation ';
                break;
            case 3: //internationalMention
                $sort .= 'internationalMention ';
                break;
            case 4: //validated
                $sort .= 'validated ';
                break;
            case 5: //published
                $sort .= 'published ';
                break;
            default:
                $sort .= 'title ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function getPaginationInfoProductionProject($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];


    return $result;
}

function getProductionProjectById($productionProjectId)
{
    $project = NULL;
    $where = "";
    $prepareParams = [];
    if (isset($productionProjectId)) {
        $where = " pp.ProductionProjectID  = %d";
        $prepareParams = $productionProjectId;
    }


    $sql = "SELECT
    pp.ProductionProjectID as 'Id',
	ppat.ActivityTypeID as 'ActivityTypeId',
	ppat.Name as 'ActivityType',
    COALESCE(pplen.Title, pples.Title, pplgl.Title) as 'Title',
	pplen.Title as 'TitleEn',
	pples.Title as 'TitleEs',
	pplgl.Title as 'TitleGl',
	j.JournalID as 'JournalId',
	j.FullName as JournalName,
	pperScopus.Reference as 'ScopusRef',
	pperWos.Reference as 'WosRef',
	pperInspire.Reference as 'InspireRef',
	pp.ArticleNumber as 'ArticleNumber',
	pp.AcceptanceDate as 'AcceptanceDate',
    pp.Date as 'PublicationDate',
	pp.PublishedOn as 'PublishedOn',
	ppt.ReadDate as 'ThesisReadDate',
	tt.ThesisTypeID as 'ThesisTypeId',
	tt.Name as 'ThesisType',
	ppt.ReadLocation as 'ThesisReadLocation',
	q.Name as 'ThesisQualification',
	ppt.InternationalMention as 'InternationalMention',
	ppt.Link as 'ThesisLink',
	pp.DOI as 'doi',
	pp.ISBN as 'isbn',
	group_concat(distinct ppk.Name separator ' ; ') as 'Keywords',
	pp.SpiIndex as 'spiIndex',
	p1.PersonnelID as 'ThesisDirectorID',
	concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) as 'ThesisDirector',
	ed.Name as 'ThesisExternalDirectorName',
	je.Name as 'JournalEditorial',
	pp.Citation as 'Citation',
	pp.Link as 'Link',	
	pp.Volume as 'Volume',
	pp.ChapterBook as 'ChapterBook',
	pp.StartingPage as 'StartingPage',
	pp.EndingPage as 'EndingPage',
	pp.NumberOfPages as 'NumberOfPages',
	pp.NumAuthors as 'NumAuthors',
	COALESCE (pplen1.Title, pples1.Title, pplgl1.Title) as 'ReferenceProjectName',
    pp.InternationalCoAuthorship as 'internationalCoAuthorship',
	pp.Abstract as 'Abstract',
	pp.Comment as 'Comment',
    pp.LinkProductionProjectID as 'LinkProductionProjectID',
    COALESCE(pplen1.Title, pples1.Title, pplgl1.Title) as 'TitleLink',
	pp.Validated as 'Validated',
	pp.ValidatedBy as 'ValidatedBy',
	pp.ValidatedOn as 'ValidatedOn',
	pp.Published as 'Published',
	pp.PublishedBy as 'PublishedBy'
from ProductionProject pp
inner join ProductionProjectActivityType ppat
	on ppat.ActivityTypeID = pp.ActivityTypeID 
left join ProductionProjectLanguage pplen
	on pplen.ProductionProjectID = pp.ProductionProjectID 
	and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
left join ProductionProjectLanguage pples
	on pples.ProductionProjectID = pp.ProductionProjectID 
	and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
left join ProductionProjectLanguage pplgl
	on pplgl.ProductionProjectID = pp.ProductionProjectID 
	and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
left join Journal j
	on j.JournalID = pp.JournalID
left join ProductionProjectExternalReference pperScopus
	on pperScopus.ProductionProjectID = pp.ProductionProjectID 
	and pperScopus.SourceID = " . IGFAE_INTRANET_SOURCE_SCOPUS . "
left join ProductionProjectExternalReference pperWos
	on pperWos.ProductionProjectID = pp.ProductionProjectID 
	and pperWos.SourceID = " . IGFAE_INTRANET_SOURCE_WOS . "
left join ProductionProjectExternalReference pperInspire
	on pperInspire.ProductionProjectID = pp.ProductionProjectID 
	and pperInspire.SourceID = " . IGFAE_INTRANET_SOURCE_INSPIRE . "
left join ProductionProjectThesis ppt
	on ppt.ProductionProjectID = pp.ProductionProjectID
left join ThesisType tt
	on tt.ThesisTypeID = ppt.ThesisTypeID 
left join ProductionProjectThesisQualification pptq
	on pptq.ProductionProjectID = pp.ProductionProjectID 
left join Qualification q
	on q.QualificationID = pptq.QualificationID 
left join ProductionProjectThesisDirector pptd
	on pptd.ProductionProjectID = pp.ProductionProjectID 
left join Personnel p1
	on p1.PersonnelID = pptd.DirectorID 
left join ProductionProjectExternalDirector ppex
	on ppex.ProductionProjectID = pp.ProductionProjectID 
left join ExternalDirector ed
	on ppex.ExternalDirectorID = ed.ExternalDirectorID 
left join ProductionProjectEditorial ppe
	on ppe.ProductionProjectID = pp.ProductionProjectID
left join JournalEditorial je
	on ppe.JournalEditorialID = je.JournalEditorialID
left join ProductionProject pp2
	on pp.LinkProductionProjectID = pp2.ProductionProjectID 
left join ProductionProjectLanguage pplen1
	on pplen1.ProductionProjectID = pp2.ProductionProjectID 
	and pplen1.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
left join ProductionProjectLanguage pples1
	on pples1.ProductionProjectID = pp2.ProductionProjectID 
	and pples1.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
left join ProductionProjectLanguage pplgl1
	on pplgl1.ProductionProjectID = pp2.ProductionProjectID 
	and pplgl1.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
left join ProductionProjectKeyword ppk
	on ppk.ProductionProjectID = pp.ProductionProjectID    
    WHERE {$where}
    ORDER BY pp.ProductionProjectID DESC";

    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $project = new IntranetProductionProject(
            $row->Id,
            ($row->ActivityTypeId . ";" . $row->ActivityType),
            $row->Title,
            $row->TitleEs,
            $row->TitleEn,
            $row->TitleGl,
            [], //Research ARea
            [], // Research Program
            [], // collaboration experiment
            [], // Research group
            ($row->JournalId . ";" . $row->JournalName),
            $row->ArticleNumber,
            $row->spiIndex,
            $row->doi,
            ($row->ThesisTypeId . ";" . $row->ThesisType),
            $row->InternationalMention,
            $row->Keywords, // keywords
            [], // authors
            [], // directors
            [], // externalAuthor 
            [], // exthernalThesisDirector 
            [], // journalEditorial 
            $row->Citation,
            $row->Validated,
            $row->Published,
            $row->InspireRef,
            $row->AcceptanceDate, // acceptanceDate 
            $row->PublicationDate, // publicationDate
            $row->ThesisReadDate, // thesisReadingDate 
            $row->ThesisReadLocation, // thesisReadingPlace 
            NULL, // qualification
            $row->isbn,
            $row->Link, // linkArticle
            $row->ThesisLink, // linkThesisCertificate
            $row->Volume, // volume
            $row->ChapterBook, // chapterbook
            $row->StartingPage, // homepage
            $row->EndingPage, // finalPages
            $row->NumberOfPages, // numberPages
            NULL, // numberAuthors
            NULL, // numberAuthorsIgfae 
            $row->Abstract,
            ($row->LinkProductionProjectID . ";" . $row->TitleLink), // linkProductionProject 
            $row->Comment,
            NULL, // researchProject,
            $row->PublishedBy, //publishedBy
            $row->PublishedOn, //publishedOn
            $row->ValidatedBy, //validatedBy
            $row->ValidatedOn, //validatedOn
            $row->internationalCoAuthorship, //internationalCoAuthorship
            NULL, //external sources
            NULL
        );
    }

    // Recuperamos los valores de los autores locales
    $projectId = $project->getId();
    $sql = "select p1.PersonnelID as 'id',  
    concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) as 'name'
    from ProductionProject pp
    inner join ProductionProjectAuthor ppa
        on ppa.ProductionProjectID = pp.ProductionProjectID 
    inner join Personnel p1
        on p1.PersonnelID = ppa.PersonnelID 
    where pp.ProductionProjectID = '$projectId'";
    
    $rows = igfae_intranet_fetch_data($sql);
    $authors = [];
    foreach ($rows['rows'] as $key => $row) {
        $authors[$row->id] = $row->name;
    }
    $project->setAuthors($authors);
    //Recuperamos los valores de los directores de thesis
    $sql = "select distinct p1.PersonnelID as 'id',  
    concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) as 'name'
    from ProductionProject pp
    inner join ProductionProjectThesisDirector pptd
        on pptd.ProductionProjectID = pp.ProductionProjectID
    inner join Personnel p1
        on p1.PersonnelID = pptd.DirectorID 
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $thesisDirector = [];
    foreach ($rows['rows'] as $row) {
        $thesisDirector[$row->id] = $row->name;
    }
    $project->setThesisDirector($thesisDirector);

    // Recuperamos los valores de los authores externos
    $sql = "select distinct concat(ea.Name, ' ', COALESCE(ea.Surname, ' ')) as 'Name', 
    ea.ExternalAuthorID as 'id'
    from ProductionProject pp
    inner join ProductionProjectExternalAuthor ppea
        on pp.ProductionProjectID = ppea.ProductionProjectID 
    inner join ExternalAuthor ea
        on ppea.ExternalAuthorID = ea.ExternalAuthorID 
    where pp.ProductionProjectID ='$projectId'
    order by ppea.`Order` asc";

    
    $rows = igfae_intranet_fetch_data($sql);
    $extAuthors = [];
    foreach ($rows['rows'] as $row) {
        $extAuthors[$row->id] = $row->Name;
    }
    $project->setExtAuthors($extAuthors);

    // Recuperamos los valores de los directores externos
    $sql = "select distinct ea.Name as 'Name', 
        ea.ExternalDirectorID as 'id'
        from ProductionProject pp
        inner join ProductionProjectExternalDirector ppea
            on pp.ProductionProjectID = ppea.ProductionProjectID 
        inner join ExternalDirector ea
            on ppea.ExternalDirectorID = ea.ExternalDirectorID 
        where pp.ProductionProjectID ='$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $extDirectors = [];
    foreach ($rows['rows'] as $row) {
        $extDirectors[$row->id] = $row->Name;
    }
    $project->setExtDirectors($extDirectors);

    //Recuperamos los valores de los grupos de investigación
    $sql = "select distinct concat (rg.Name, ' (', rg.Acronym, ')') as 'Name', rg.ResearchGroupID as 'id'
    from ProductionProject pp
    inner join ProductionProjectResearchGroup pprg
        on pprg.ProductionProjectID = pp.ProductionProjectID 
    inner join ResearchGroup rg
        on pprg.ResearchGroupID = rg.ResearchGroupID 
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $researchGroups = [];
    foreach ($rows['rows'] as $row) {
        $researchGroups[$row->id] = $row->Name;
    }
    $project->setResearchGroup($researchGroups);

    //Recuperamos los valores de las áreas de investigación
    $sql = "select distinct ra.Name as 'Name', ra.ResearchAreaID as 'id'
    from ProductionProject pp
    inner join ProductionProjectResearchArea ppra
        on ppra.ProductionProjectID = pp.ProductionProjectID 
    inner join ResearchArea ra
        on ppra.ResearchAreaID = ra.ResearchAreaID 
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $researchAreas = [];
    foreach ($rows['rows'] as $row) {
        $researchAreas[$row->id] = $row->Name;
    }
    $project->setResearchAreas($researchAreas);

    //Recuperamos los valores de los programas de investigación
    $sql = "select distinct concat (rp.Name, ' (', rp.Acronym, ')') as 'Name', rp.ResearchProgramID as 'id'
    from ProductionProject pp
    inner join ProductionProjectResearchProgram pprp
        on pprp.ProductionProjectID = pp.ProductionProjectID 
    inner join ResearchProgram rp
        on pprp.ResearchProgramID = rp.ResearchProgramID 
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $researchPrograms = [];
    foreach ($rows['rows'] as $row) {
        $researchPrograms[$row->id] = $row->Name;
    }
    $project->setResearchPrograms($researchPrograms);

    //Recuperamos los valores de los proyectos de investigación
    $sql = "select p.ProjectID as 'id', COALESCE (plen.Title, ples.Title, plgl.Title) as 'Title'
    from ProductionProject pp
    inner join ProductionProjectProject ppp
	on ppp.ProductionProjectID = pp.ProductionProjectID 
    inner join Project p
        on ppp.ProjectID = p.ProjectID 
    left join ProjectLanguage plen
        on plen.ProjectID = p.ProjectID 
        and plen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProjectLanguage ples
        on ples.ProjectID = p.ProjectID 
        and ples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProjectLanguage plgl
        on plgl.ProjectID = p.ProjectID 
        and plgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "   
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $researchProjects = [];
    foreach ($rows['rows'] as $row) {
        $researchProjects[$row->id] = $row->Title;
    }
    $project->setResearchProjects($researchProjects);

    //Recuperamos los valores de los experimentos
    $sql = "select distinct ec.ExperimentalCollaborationID as 'id', ec.Name as 'name'
    from ProductionProject pp
    inner join ExperimentalCollaborationProductionProject ecpp
        on ecpp.ProductionProjectID = pp.ProductionProjectID
    inner join ExperimentalCollaboration ec
        on ecpp.ExperimentalCollaborationID = ec.ExperimentalCollaborationID 
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $experiments = [];
    foreach ($rows['rows'] as $row) {
        $experiments[$row->id] = $row->name;
    }
    $project->setCollaborationExperiments($experiments);

    //Recuperamos los valores de las editoriales
    $sql = "select distinct ppe.PersonnelID as 'personelID', 
    ppe.JournalEditorialID as 'journalEditorialID',
    je.Name as 'journalName',
    concat(p1.Name, ' ', p1.FirstSurname, ' ' ,coalesce(p1.SecondSurname, '')) as 'personalName'
    from ProductionProject pp
    inner join ProductionProjectEditorial ppe
        on ppe.ProductionProjectID = pp.ProductionProjectID  
    inner join JournalEditorial je
        on je.JournalEditorialID = ppe.JournalEditorialID   
    inner join Personnel p1
        on p1.PersonnelID = ppe.PersonnelID         
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $editorials = [];
    foreach ($rows['rows'] as $key => $row) {
        $editorials[$row->personelID . "-" . $row->journalEditorialID] = $row->personalName . ";" . $row->journalName;
    }
    $project->setJournalsEditorials($editorials);

    //Recuperamos los valores de las calificaciones
    $sql = "select distinct pptq.QualificationID as 'id', q.Name as 'name'
    from ProductionProject pp
    inner join ProductionProjectThesisQualification pptq
        on pptq.ProductionProjectID = pp.ProductionProjectID
    inner join Qualification q
        on q.QualificationID = pptq.QualificationID 
    where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $qualifications = [];
    foreach ($rows['rows'] as $key => $row) {
        $qualifications[$row->id] = $row->name;
    }
    $project->setQualifications($qualifications);

    //Recuperamos los valores de las fuentes externas
    $sql = "select distinct source.SourceName as 'name', pper.Link as 'link'
     from ProductionProject pp
     inner join ProductionProjectExternalReference pper
         on pper.ProductionProjectID = pp.ProductionProjectID   
    inner join Source source
         on source.SourceID = pper.SourceID     
     where pp.ProductionProjectID = '$projectId'";

    
    $rows = igfae_intranet_fetch_data($sql);
    $externalReferences = [];
    foreach ($rows['rows'] as $row) {
        if ($row->link != null && $row->link != "" && $row->link != IGFAE_INTRANET_NONE) {
            $externalReferences[$row->name] = $row->link;           
        }
    }
    $project->setExternalSources($externalReferences);
    return $project;
}

function saveProductionProject($productionProject)
{
    // Recuperamos los campos del formulario para insertar
    $typeActivity = $productionProject->getTypeActivity();
    $titleES = $productionProject->getTitleES();
    $titleEN = $productionProject->getTitleEN();
    $titleGL = $productionProject->getTitleGL();
    $journal = $productionProject->getJournal();
    $researchInspire = $productionProject->getResearchInspire();
    $articleNumber = $productionProject->getArticleNumber();
    $acceptanceDateStr = $productionProject->getAcceptanceDate();
    if (isset($acceptanceDateStr)) {
        $time_input = strtotime($acceptanceDateStr);
        $acceptanceDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $publicationDateStr = $productionProject->getPublicationDate();
    $publicationDate = NULL;
    if (isset($publicationDateStr)) {
        $time_input = strtotime($publicationDateStr);
        $publicationDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $thesisReadingDateStr = $productionProject->getThesisReadingDate();
    $thesisReadingDate = NULL;
    if (isset($thesisReadingDateStr)) {
        $time_input = strtotime($thesisReadingDateStr);
        $thesisReadingDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    $thesisType = $productionProject->getThesisType();
    $thesisReadingPlace = $productionProject->getThesisReadingPlace();
    $qualification = $productionProject->getQualification();
    $internationalMenthion = $productionProject->getInternationalMention();
    $doi = $productionProject->getDoi();    
    $isbn = $productionProject->getIsbn();
    $authors = $productionProject->getAuthors();
    $thesisDirector = $productionProject->getThesisDirector();
    $keywords = $productionProject->getKeywords();
    $extAuthors = $productionProject->getExternalAuthor();
    $extThesisDirector = $productionProject->getExternalThesisDirector();
    $journalEditorialTable = $productionProject->getJournalEditorial();
    $spiIndexing = $productionProject->getSpiIndexing();
    $citation = $productionProject->getCitation();
    $linkToArticle = $productionProject->getLinkArticle();
    $linksThesisCertificate = $productionProject->getLinkThesisCertificate();
    $volume = $productionProject->getVolume();
    $chapterBook = $productionProject->getChapterBook();
    $homepage = $productionProject->getHomePage();
    $finalPage = $productionProject->getFinalPage();
    $numberPages = $productionProject->getNumberPages();
    $researchArea = $productionProject->getResearchArea();
    $researchProject = $productionProject->getResearchProject();
    $researchProgram = $productionProject->getResearchProgram();
    $researchGroup = $productionProject->getResearchGroup();
    $collaborationExperiment = $productionProject->getCollaborationExperiment();
    $linkProductionProject = $productionProject->getLinkProductionProject();
    $abstract = $productionProject->getAbstract();
    $comment = $productionProject->getComments();
    $validated = $productionProject->getValidated();
    $validatedBy = $productionProject->getValidatedBy();
    $validatedOn = $productionProject->getValidatedOn();
    $published = $productionProject->getPublished();
    $publishedBy = $productionProject->getPublishedBy();
    $publishedOn = $productionProject->getPublishedOn();
    $internationalCoAuthorship = $productionProject->getInternationalCoAuthorship();
    $idProject = $_POST['idProject'];
    $id = NULL;
    if (isset($idProject) && strlen($idProject) > 0) {
        //ACTUALIZAR PROYECTO
        $sql = "UPDATE ProductionProject
        SET
        ActivityTypeID = '$typeActivity',
        DOI = " . (isset($doi) ? "'$doi'" : "NULL") . ",
        ISBN = " . (isset($isbn) ? "'$isbn'" : "NULL") . ",       
        JournalID = " . (isset($journal) ? "'$journal'" : "NULL") . ",
        Volume = " . (isset($volume) ? "'$volume'" : "NULL") . ",
        Date = " . (isset($publicationDate) ? "'$publicationDate'" : "NULL") . ",
        ChapterBook = " . (isset($chapterBook) ? "'$chapterBook'" : "NULL") . ",
        StartingPage = " . (isset($homepage) ? "'$homepage'" : "NULL") . ",
        EndingPage = " . (isset($finalPage) ? "'$finalPage'" : "NULL") . ",
        NumberOfPages =  " . (isset($numberPages) ? "'$numberPages'" : "NULL") . ",
        Citation = " . (isset($citation) ? "'$citation'" : "NULL") . ",
        Link = " . (isset($linkToArticle) ? "'$linkToArticle'" : "NULL") . ",
        ArticleNumber = " . (isset($articleNumber) ? "'$articleNumber'" : "NULL") . ",
        AcceptanceDate = " . (isset($acceptanceDate) ? "'$acceptanceDate'" : "NULL") . ",
        LinkProductionProjectID = " . (isset($linkProductionProject) ? "'$linkProductionProject'" : "NULL") . ",
        SpiIndex =  " . (isset($spiIndexing) ? "'$spiIndexing'" : "NULL") . ",
        Abstract = " . (isset($abstract) ? "'$abstract'" : "NULL") . ",
        Comment =  " . (isset($comment) ? "'$comment'" : "NULL") . ",
        Validated = '$validated',
        Published = '$published',
        ValidatedBy = " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",
        PublishedBy = " . (isset($publishedBy) ? "'$publishedBy'" : "NULL") . ",
        ValidatedOn = " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . ",
        PublishedOn = " . (isset($publishedOn) ? "'$publishedOn'" : "NULL") . ",
        InternationalCoAuthorship = " . (isset($internationalCoAuthorship) ? "'$internationalCoAuthorship'" : "NULL") . "
        WHERE ProductionProjectId = '$idProject'";


        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        igfae_intranet_save($sql);
        
        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsProductionProject($idProject);
        // Insertamos en la tabla ProductionProjectLanguage
        insertLanguage($idProject, $titleEN, $titleES, $titleGL);

        // Insertamos en la tabla keywords
        insertKeywords($idProject, $keywords);

        // Insertamos en la tabla ProductionProjectResearchProgram
        insertResearchProgram($idProject, $researchProgram);

        // Insertamos en la tabla ProductionProjectResearchArea
        insertResearchArea($idProject, $researchArea);

        // Insertamos en la tabla ProductionProjectResearchGroup
        insertResearchGroup($idProject, $researchGroup);

        // Insertamos en la tabla ProductionProjectProject
        insertResearchProject($idProject, $researchProject);

        // Insertamos en la tabla ExperimentalCollaborationProductionProject
        insertCollaborationExperiment($idProject, $collaborationExperiment);

        // Insertamos en la tabla ProductionProjectExternalReference, para Inspire (source = 3)
        insertExternalReference($idProject, $researchInspire);

        // Insertamos en la tabla ProductionProjectExternalAuthor
        insertExternalAuthor($idProject, $extAuthors);

        // Insertamos en la tabla ProductionProjectAuthor
        insertAuthors($idProject, $authors);

        // Insertamos en la tabla ProductionProjectThesis
        insertThesis($idProject, $thesisType, $thesisReadingPlace, $linksThesisCertificate, $thesisReadingDate, $internationalMenthion);

        // Insertamos en la tabla ProductionProjectThesisQualification
        insertQualification($idProject, $qualification);

        // Insertamos en la tabla ProductionProjectExternalDirector
        insertExternalDirector($idProject, $extThesisDirector);

        // Insertamos en la tabla ProductionProjectThesisDirector
        insertThesisDirector($idProject, $thesisDirector);

        // Insertamos en la tabla ProductionProjectEditorial
        insertJournalEditorial($idProject, $journalEditorialTable);

        // Obtenemos el valor de la columna AllAuthors de ProductionProject
        updateAllAuthors($idProject);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $idProject);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $idProject);
        }
        if (isset($publishedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $idProject);
        }

        // Authorization
        $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $idProject);
        if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
            closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $idProject);
        }

        /** END TRANSACTION */
        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    } else {
        global $current_user;

        //NUEVO PROYECTO
        $sql = "INSERT INTO ProductionProject 
            (
            ActivityTypeID, 
            DOI,
            ISBN,
            AllAuthors, 
            NumAuthors,
            JournalID, 
            Volume, 
            Date, 
            ChapterBook,
            StartingPage, 
            EndingPage, 
            NumberOfPages,
            Citation, 
            Link, 
            ArticleNumber,
            AcceptanceDate, 
            LinkProductionProjectID, 
            SpiIndex,
            Abstract, 
            Comment, 
            Validated, 
            Published,
            ValidatedBy, 
            PublishedBy, 
            ValidatedOn,
            PublishedOn,
            InternationalCoAuthorship,
            CreatedBy) 
            VALUES ( 
            '$typeActivity', 
            " . (isset($isbn) ? "'$doi'" : "NULL") . ",
            " . (isset($isbn) ? "'$isbn'" : "NULL") . ",
            NULL,
            NULL, 
            " . (isset($journal) ? "'$journal'" : "NULL") . ", 
            " . (isset($volume) ? "'$volume'" : "NULL") . ", 
            " . (isset($publicationDate) ? "'$publicationDate'" : "NULL") . ",
            " . (isset($chapterBook) ? "'$chapterBook'" : "NULL") . ",
            " . (isset($homepage) ? "'$homepage'" : "NULL") . ",
            " . (isset($finalPage) ? "'$finalPage'" : "NULL") . ",
            " . (isset($numberPages) ? "'$numberPages'" : "NULL") . ", 
            " . (isset($citation) ? "'$citation'" : "NULL") . ", 
            " . (isset($linkToArticle) ? "'$linkToArticle'" : "NULL") . ",
            " . (isset($articleNumber) ? "'$articleNumber'" : "NULL") . ",
            " . (isset($acceptanceDate) ? "'$acceptanceDate'" : "NULL") . ",
            " . (isset($linkProductionProject) ? "'$linkProductionProject'" : "NULL") . ", 
            " . (isset($spiIndexing) ? "'$spiIndexing'" : "NULL") . ",
            " . (isset($abstract) ? "'$abstract'" : "NULL") . ",
            " . (isset($comment) ? "'$comment'" : "NULL") . ",
            '$validated', 
            '$published', 
            " . (isset($validatedBy) ? "'$validatedBy'" : "NULL") . ",  
            " . (isset($publishedBy) ? "'$publishedBy'" : "NULL") . ",             
            " . (isset($validatedOn) ? "'$validatedOn'" : "NULL") . ", 
            " . (isset($publishedOn) ? "'$publishedOn'" : "NULL") . ",
            " . (isset($internationalCoAuthorship) ? "'$internationalCoAuthorship'" : "NULL") . ",
            " . getUserByWPUserID($current_user->ID)[0]["id"] . "
            )";

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        
        $id = igfae_intranet_save($sql);

        // Insertamos en la tabla ProductionProjectLanguage
        insertLanguage($id, $titleEN, $titleES, $titleGL);

        // Insertamos en la tabla keywords
        insertKeywords($id, $keywords);

        // Insertamos en la tabla ProductionProjectResearchProgram
        insertResearchProgram($id, $researchProgram);

        // Insertamos en la tabla ProductionProjectResearchArea
        insertResearchArea($id, $researchArea);

        // Insertamos en la tabla ProductionProjectResearchGroup
        insertResearchGroup($id, $researchGroup);

        // Insertamos en la tabla ProductionProjectProject
        insertResearchProject($id, $researchProject);

        // Insertamos en la tabla ExperimentalCollaborationProductionProject
        insertCollaborationExperiment($id, $collaborationExperiment);

        // Insertamos en la tabla ProductionProjectExternalReference, para Inspire (source = 3)
        insertExternalReference($id, $researchInspire);

        // Insertamos en la tabla ProductionProjectExternalAuthor
        insertExternalAuthor($id, $extAuthors);

        // Insertamos en la tabla ProductionProjectAuthor
        insertAuthors($id, $authors);

        // Insertamos en la tabla ProductionProjectThesis
        insertThesis($id, $thesisType, $thesisReadingPlace, $linksThesisCertificate, $thesisReadingDate, $internationalMenthion);

        // Insertamos en la tabla ProductionProjectThesisQualification
        insertQualification($id, $qualification);

        // Insertamos en la tabla ProductionProjectExternalDirector
        insertExternalDirector($id, $extThesisDirector);

        // Insertamos en la tabla ProductionProjectThesisDirector
        insertThesisDirector($id, $thesisDirector);

        // Insertamos en la tabla ProductionProjectEditorial
        insertJournalEditorial($id, $journalEditorialTable);

        // Obtenemos el valor de la columna AllAuthors de ProductionProject
        updateAllAuthors($id);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $id);
        if (isset($validatedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $id);
        }
        if (isset($publishedBy)) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $id);
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
// Delete project
function deleteProductionProject($id)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_PRODUCTION_PROJECT, $id);

    deleteRelationsProductionProject($id);
    // ProductionProject
    $sql = "DELETE FROM ProductionProject
    WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }
}
// Delete project
function deleteRelationsProductionProject($id)
{
    //ProductionProjectEditorial
    $sql = "DELETE FROM ProductionProjectEditorial
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectAuthor
    $sql = "DELETE FROM ProductionProjectAuthor
          WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ExperimentalCollaborationProductionProject
    $sql = "DELETE FROM ExperimentalCollaborationProductionProject
          WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectProject
    $sql = "DELETE FROM ProductionProjectProject
          WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectResearchGroup
    $sql = "DELETE FROM ProductionProjectResearchGroup
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectResearchArea
    $sql = "DELETE FROM ProductionProjectResearchArea
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectResearchProgram
    $sql = "DELETE FROM ProductionProjectResearchProgram
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);
    //ProductionProjectKeyword
    $sql = "DELETE FROM ProductionProjectKeyword
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectLanguage
    $sql = "DELETE FROM ProductionProjectLanguage
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectExternalDirector
    $sql = "DELETE FROM ProductionProjectExternalDirector
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectExternalAuthor
    $sql = "DELETE FROM ProductionProjectExternalAuthor
            WHERE ProductionProjectID = '$id'";
        
    igfae_intranet_save($sql);

    //ProductionProjectCites
    $sql = "DELETE FROM ProductionProjectCites
            WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectExternalReference
    $sql = "DELETE FROM ProductionProjectExternalReference
        WHERE ProductionProjectID = '$id'
        AND SourceID = " . IGFAE_INTRANET_SOURCE_INSPIRE;
    
    igfae_intranet_save($sql);

    //ProductionProjectThesisDirector
    $sql = "DELETE FROM ProductionProjectThesisDirector
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectThesisQualification
    $sql = "DELETE FROM ProductionProjectThesisQualification
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);

    //ProductionProjectThesis
    $sql = "DELETE FROM ProductionProjectThesis
        WHERE ProductionProjectID = '$id'";
    
    igfae_intranet_save($sql);
}

function getSelectVinculations($id, $texto, $page)
{

    $where = "";
    $params = [];
    if (isset($texto)) {
        $where .= " AND ( pplen.Title like %s or pples.Title like %s or pplgl.Title like %s)";
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
    }
    if (isset($id)) {
        $where .= " AND pp.ProductionProjectID NOT IN (%d) ";
        $params[] = '$id';
    }
    // Añadimos los params para la segunda clausula where
    if (isset($texto)) {
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
        $params[] = '%' . $texto . '%';
    }
    if (isset($id)) {
        $params[] = '$id';
    }

    $sql = "SELECT x.totalCounts as 'totalCounts',
        pp.ProductionProjectID AS 'productionProjectId',
        COALESCE(pplen.Title, pples.Title, pplgl.Title) as 'Title'
    FROM ProductionProject pp
    left join ProductionProjectLanguage pplen
        on pplen.ProductionProjectID = pp.ProductionProjectID 
        and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProductionProjectLanguage pples
        on pples.ProductionProjectID = pp.ProductionProjectID 
        and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProductionProjectLanguage pplgl
	on pplgl.ProductionProjectID = pp.ProductionProjectID 
	and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . ",
    (select count(*) as totalCounts 
        FROM ProductionProject pp
        left join ProductionProjectLanguage pplen
        on pplen.ProductionProjectID = pp.ProductionProjectID 
	    and pplen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        left join ProductionProjectLanguage pples
	    on pples.ProductionProjectID = pp.ProductionProjectID 
	    and pples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
        left join ProductionProjectLanguage pplgl
	    on pplgl.ProductionProjectID = pp.ProductionProjectID 
	    and pplgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
         where 1=1 {$where}) as x 
        WHERE 1=1
        {$where}     
    ORDER BY 
    COALESCE(pplen.Title, pples.Title, pplgl.Title)
    ASC";

    // Obtenemos los resultados en grupos de 30    
    $amount = IGFAE_INTRANET_NUMBER_ELEMENTS_SELECT_ASYNC;
    if (is_null($page)) {
        $page = 1;
    }
    $offset = (($page - 1) * $amount);
    $sql .= " LIMIT {$offset}, {$amount}";
    $rows = igfae_intranet_fetch_data($sql, $params);
    
    $result = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->productionProjectId, "full_name" => $row->Title);
    }
    $result['items']  = $resultArray;
    $result['total_count'] = $row->totalCounts;

    return $result;
}


function existsProductionProject($productionProjectId)
{
    $where = "";
    $params = [];

    $where .= " AND pp.ProductionProjectID = %d";
    $params[] = $productionProjectId;

    $sql = "SELECT distinct
	pp.ProductionProjectID as 'id'
    FROM ProductionProject pp 
        WHERE 1=1 {$where} ";

    
    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}
function getProductionProjectByDOI($productionProject)
{
    $where = "";
    $params = [];

    if ($productionProject->getDoi()) {
        $where .= " AND pp.DOI =  %s";
        $params[] = $productionProject->getDoi();
    }
    if ($productionProject->getId()) {
        $where .= " AND pp.ProductionProjectID NOT IN (%d)";
        $params[] = $productionProject->getId();
    }
    $sql = "SELECT distinct
	pp.ProductionProjectID as 'id'
    FROM ProductionProject pp 
        WHERE 1=1 AND pp.ActivityTypeID = " . IGFAE_INTRANET_PRODUCTION_PROJECT_ACTIVITY_TYPE_ARTICLE . "
        {$where} ";
    
    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

function getLinkProductionProject($id)
{
    $where = "";
    $params = [];


    $where .= " AND pp.LinkProductionProjectId =  %s";
    $params[] = $id;

    $sql = "SELECT distinct
	pp.ProductionProjectID as 'id'
    FROM ProductionProject pp 
        WHERE 1=1 
        {$where} ";

    
    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}
/**
 * Comprobamos si existe y sino la insertamos en la tabla ExternalAuthor
 */
function existExtAuthors($extAuthors)
{
    $where = "";
    $params = [];
    $nameSurname = [];
    $idsExtAuthors = [];
    $typesCount = 0;
    $where = "";
    $params = [];
    $nameSurname = [];

    //Obtenemos el listado de extAuthors que existan
    $typesCount = count($extAuthors);
    $paramsPlaceholders = array_fill(0, $typesCount, '%s');
    $placeholdersExtAuthors = implode(',', $paramsPlaceholders);
    $where .= " AND (ExternalAuthorID IN ({$placeholdersExtAuthors}))";
    $params = array_merge($params, $extAuthors);

    $sql = "SELECT ExternalAuthorID as 'id'
    FROM ExternalAuthor 
        WHERE 1=1 
        {$where} ";
    
    $rows = igfae_intranet_fetch_data($sql, $params);

    $resultArray = array();
    foreach ($rows['rows'] as $row) {
        $resultArray[] = $row->id;
    }

    foreach ($extAuthors as $key => $item) {
        $existeAuthor = false;
        foreach ($resultArray as $itemExiste) {
            if ($item == $itemExiste) {
                $existeAuthor = true;
            }
        }
        if ($existeAuthor) {
            $idsExtAuthors[$key] = $item;
        } else {
            //Separamos nombre y apellidos
            $nameSurname = explode(" ", $item);
            // Insertamos en ExternalAuthor
            if (count($nameSurname) == 1) {
                $sql = "INSERT INTO ExternalAuthor(
                   Name, Surname, Firm)
                   VALUES
                   (            
                   '$nameSurname[0]',
                   NULL,
                   NULL                    
                   )";
                
                $idsExtAuthors[$key] = igfae_intranet_save($sql);
            } else if (count($nameSurname) == 2) {
                $sql = "INSERT INTO ExternalAuthor(
                   Name, Surname, Firm)
                   VALUES
                   (            
                   '$nameSurname[0]',
                   '$nameSurname[1]',
                   NULL                    
                   )";
                
                $idsExtAuthors[$key] = igfae_intranet_save($sql);
            }
        }
    }
    return $idsExtAuthors;
}
/**
 * Comprobamos si existe y sino la insertamos en la tabla ExternalDirector
 */
function existExtThesisDirector($extDirectors)
{
    $where = "";
    $params = [];
    $idsExtDirectors = [];
    // Si existe nos viene el ID, sino Nombre
    foreach ($extDirectors as $key => $item) {
        $where = "";
        $params = [];
        $where .= " AND (ExternalDirectorID =  %d)";
        $params[] =  $item;

        $sql = "SELECT Name as 'name'
        FROM ExternalDirector 
            WHERE 1=1 
            {$where} ";
        
        $rows = igfae_intranet_fetch_data($sql, $params);
        $resultArray = array();

        foreach ($rows['rows'] as $row) {
            $resultArray[] = array("name" => $row->name);
        }
        if (count($resultArray) == 0) {
            $sql = "INSERT INTO ExternalDirector(
                    Name)
                    VALUES
                    (            
                    '$item'                   
                    )";
            
            $idsExtDirectors[$key] = igfae_intranet_save($sql);
        } else {
            $idsExtDirectors[$key] = $item;
        }
    }
    return $idsExtDirectors;
}
function insertLanguage($id, $titleEN, $titleES, $titleGL)
{
    if (isset($id) && $id > 0) {
        $language = IGFAE_INTRANET_LANGUAGE_EN;
        $title = "";
        if (isset($titleEN) && strlen($titleEN) > 0) {
            $language = IGFAE_INTRANET_LANGUAGE_EN;
            $title = $titleEN;

            $sql = "INSERT INTO ProductionProjectLanguage(ProductionProjectID,
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

            $sql = "INSERT INTO ProductionProjectLanguage(ProductionProjectID,
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

            $sql = "INSERT INTO ProductionProjectLanguage(ProductionProjectID,
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
function insertKeywords($id, $keywords)
{
    // Insertamos en la tabla ProductionProjectKeywords
    if (isset($id)  && $id > 0 && isset($keywords)) {
        //Comprobamos si son keywords nuevas para insertar en la tabla maestra
        existKeywords($keywords);
        foreach ($keywords as $item) {
            $sql = "INSERT INTO ProductionProjectKeyword(ProductionProjectID,
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

function insertResearchProgram($id, $researchProgram)
{
    if (isset($id)  && $id > 0 && isset($researchProgram)) {
        foreach ($researchProgram as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ProductionProjectResearchProgram(ProductionProjectID,
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

function insertResearchArea($id, $researchArea)
{
    if (isset($id)  && $id > 0 && isset($researchArea)) {
        foreach ($researchArea as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ProductionProjectResearchArea(ProductionProjectID,
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
function insertResearchGroup($id, $researchGroup)
{
    if (isset($id)  && $id > 0 && isset($researchGroup)) {
        foreach ($researchGroup as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ProductionProjectResearchGroup(ProductionProjectID,
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

function insertResearchProject($id, $researchProject)
{
    if (isset($id)  && $id > 0 && isset($researchProject)) {
        foreach ($researchProject as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ProductionProjectProject(ProductionProjectID,
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

function insertCollaborationExperiment($id, $collaborationExperiment)
{
    if (isset($id)  && $id > 0 && isset($collaborationExperiment)) {
        $sql = "INSERT INTO ExperimentalCollaborationProductionProject(ExperimentalCollaborationID,
                        ProductionProjectID)
                        VALUES
                        (                            
                        '$collaborationExperiment',
                        '$id'                   
                        )";
        
        igfae_intranet_save($sql);
    }
}

function insertExternalReference($id, $researchInspire)
{
    if (isset($id)  && $id > 0 && isset($researchInspire)) {
        $sql = "INSERT INTO ProductionProjectExternalReference(
                    ProductionProjectID,
                    SourceID,
                    Reference,
                    OriginalSource,
                    Link)
                    VALUES
                    (                            
                    '$id',
                    3,
                    '$researchInspire',
                    1,
                    NULL                 
                    )";
        
        igfae_intranet_save($sql);
    }
}

function insertExternalAuthor($id, $extAuthors)
{

    if (isset($id)  && $id > 0 && isset($extAuthors)) {
        $idsExtAuthors = [];
        //Comprobamos si son autores ext. nuevos para insertar en la tabla maestra
        $idsExtAuthors = existExtAuthors($extAuthors);
        if (count($idsExtAuthors) > 0) {
            // Identificadores de los nuevos 
            $extAuthors = $idsExtAuthors;
        }
        $order = 1;
        $params = array();
        foreach ($extAuthors as $item) {
            $params[] = '(' . $id . ',' . $item . ',' . $order . ')';
            $order++;
        }
        $params = implode(",", $params);
        $sql = "INSERT INTO ProductionProjectExternalAuthor VALUES {$params} ";
        
        igfae_intranet_save($sql, $params);
    }
}

function insertAuthors($id, $authors)
{
    if (isset($id)  && $id > 0 && isset($authors)) {
        foreach ($authors as $item) {
            $sql = "INSERT INTO ProductionProjectAuthor(ProductionProjectID,
                    PersonnelID)
                    VALUES
                    (
                    '$id',
                    '$item')";
            
            igfae_intranet_save($sql);
        }
    }
}

function insertThesis($id, $thesisType, $thesisReadingPlace, $linksThesisCertificate, $thesisReadingDate, $internationalMenthion)
{
    if (isset($id)  && $id > 0 && isset($thesisType)) {

        $sql = "INSERT INTO ProductionProjectThesis(ProductionProjectID,
                            ThesisTypeID,
                            ReadLocation,
                            Link,
                            ReadDate,
                            InternationalMention)
                            VALUES
                            (
                            '$id',
                            '$thesisType',
                            " . (isset($thesisReadingPlace) ? "'$thesisReadingPlace'" : "NULL") . ", 
                            " . (isset($linksThesisCertificate) ? "'$linksThesisCertificate'" : "NULL") . ", 
                            " . (isset($thesisReadingDate) ? "'$thesisReadingDate'" : "NULL") . ", 
                            " . (isset($internationalMenthion) ? "'$internationalMenthion'" : "NULL") . "
                            )";
        
        igfae_intranet_save($sql);
    }
}

function insertQualification($id, $qualification)
{
    if (isset($id)  && $id > 0 && isset($qualification)) {
        foreach ($qualification as $item) {
            $sql = "INSERT INTO ProductionProjectThesisQualification(ProductionProjectID,
                    QualificationID)
                    VALUES
                    (
                    '$id',
                    '$item')";
            
            igfae_intranet_save($sql);
        }
    }
}

function insertExternalDirector($id, $extThesisDirector)
{
    if (isset($id)  && $id > 0 && isset($extThesisDirector)) {
        $idsExtDirectors = [];
        //Comprobamos si son directores ext. nuevos para insertar en la tabla maestra
        $idsExtDirectors = existExtThesisDirector($extThesisDirector);
        if (count($idsExtDirectors) > 0) {
            // Identificadores de los nuevos 
            $extThesisDirector = $idsExtDirectors;
        }
        foreach ($extThesisDirector as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ProductionProjectExternalDirector(ProductionProjectID,
                    ExternalDirectorID)
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
function insertThesisDirector($id, $thesisDirector)
{
    if (isset($id)  && $id > 0 && isset($thesisDirector)) {
        foreach ($thesisDirector as $item) {
            if (isset($item)) {
                $sql = "INSERT INTO ProductionProjectThesisDirector(ProductionProjectID,
                    DirectorID)
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

function insertJournalEditorial($id, $journalEditorialTable)
{
    if (isset($id)  && $id > 0 && isset($journalEditorialTable)) {
        foreach ($journalEditorialTable as $item) {
            if (isset($item)) {
                $items = [];
                $items = explode("-", $item);
                if (count($items) > 1) {
                    $sql = "INSERT INTO ProductionProjectEditorial(ProductionProjectID,
                    PersonnelID,
                    JournalEditorialID)
                    VALUES
                    (
                    '$id',
                    '$items[0]',
                    '$items[1]'                    
                    )";
                    
                    igfae_intranet_save($sql);
                }
            }
        }
    }
}

function updateAllAuthors($id)
{
    if (isset($id)  && $id > 0) {

        $sql = "SET SESSION group_concat_max_len = 1000000";
        igfae_intranet_setSession($sql);

        $sqlSelectAuthors = "select group_concat(a.firm separator ', ') as 'allAuthors'
        from (
            select replace(sf.Firm, ',','') as firm, 0 as 'Order'
            from ProductionProjectAuthor ppa 
            inner join ScientificFirm sf
                on sf.PersonnelID = ppa.PersonnelID 
            where ppa.ProductionProjectID = '$id'
            union all
            select concat(ea.Surname, ' ', ea.Name) as firm, ppea.Order as 'Order'
            from ProductionProjectExternalAuthor ppea
            inner join ExternalAuthor ea
                on ppea.ExternalAuthorID = ea.ExternalAuthorID 
            where ppea.ProductionProjectID = '$id'
            order by 2 asc
        ) a
        order by a.Order";

        $rows = igfae_intranet_fetch_data($sqlSelectAuthors);
        $allAuthors = "";
        $numAuthors = 0;

        foreach ($rows['rows'] as $row) {
            $allAuthors =
                $row->allAuthors;
        }
        if (isset($allAuthors) && strlen($allAuthors) > 0) {
            // Obtenemos el número de autores
            $numAuthors = count(explode(",", $allAuthors));
            $allAuthors = str_replace("'", "\"", $allAuthors);
            // Actualizamos la columna allAuthors
            $sqlUpdate = "UPDATE ProductionProject SET AllAuthors = '$allAuthors', NumAuthors = '$numAuthors' WHERE ProductionProjectID='$id'";
            igfae_intranet_save($sqlUpdate);
        }
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
function hasRelatedProductionProject($user_id, $id, $mode = 2) {
    
    $sqlHasRelation = "select count(*) 
    from ProductionProject pp
    left join ProductionProjectAuthor ppa
        on ppa.ProductionProjectID = pp.ProductionProjectID
    left join ProductionProjectThesisDirector pptd
        on pptd.ProductionProjectID = pp.ProductionProjectID 
    left join ProductionProjectEditorial ppe
        on ppe.ProductionProjectID = pp.ProductionProjectID";       

    $sqlHasRelation .= " where 1=1 ";

    if ($mode == 1) {
        $sqlHasRelation .= "
            and (ppa.PersonnelID = $user_id or ppe.PersonnelID = $user_id or pptd.DirectorID = $user_id)
            ";
    } else {
        $sqlHasRelation .= "
            and (ppa.PersonnelID = $user_id or ppe.PersonnelID = $user_id or pptd.DirectorID = $user_id or pp.CreatedBy = $user_id)
        ";
    }

    $sqlHasRelation .= "
        and pp.ProductionProjectID = '$id'
    ;";

    
    $result = igfae_intranet_get_var($sqlHasRelation);

    if ($result) {
        return true;
    } else {
        return false;
    }
}

function isUserCreatorOrRelatedWithProductionProject($user_id){

    $idProject = $_GET['idProject'];

    if (!isset($idProject) || !isset($user_id)) {
        return false;
    }

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedProductionProject($id, $idProject)) {
            return true;
        }
    }

    return false;
}


function isProductionProjectConsolidated($id) {    
    $sql = "select count(*) 
    from ProductionProject pp
    where
        pp.Validated = 1
        and pp.ProductionProjectID = $id
    ;";
    
    $result = igfae_intranet_get_var($sql);

    if ($result) {
        return true;
    } else {
        return false;
    }
}