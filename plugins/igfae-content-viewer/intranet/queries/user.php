<?php
function fetchUsers($user, $offset = 0, $amount = 10, $order = null)
{

    //Almacenamos las diferentes sentencias sql en un array
    $sqlArray = [];
    $sqlParams = [];

    //Borrado tabla temporal
    $sqlArray[] = "DROP TEMPORARY TABLE IF EXISTS PersonnelRoles";
    $sqlParams[] = [];
    //Borrado tabla temporal
    $sqlArray[] = "DROP TEMPORARY TABLE IF EXISTS PersonnelRolesCopy";
    $sqlParams[] = [];

    //Creamos la tabla temporal
    $sqlArray[] = "CREATE TEMPORARY TABLE PersonnelRoles(
    WPUserID bigint(20) unsigned,
   `Role` VARCHAR(100) NOT NULL);";
    $sqlParams[] = [];

    //Creamos el índice
    $sqlArray[] = "CREATE INDEX IX_PersonnelRoles USING BTREE ON PersonnelRoles (WPUserID);";
    $sqlParams[] = [];

    //Creamos la tabla temporal
    $sqlArray[] = "CREATE TEMPORARY TABLE PersonnelRolesCopy(
        WPUserID bigint(20) unsigned,
        Role VARCHAR(100) NOT NULL
        );";
    $sqlParams[] = [];

    //Creamos el índice
    $sqlArray[] = "CREATE INDEX IX_PersonnelCopyRoles USING BTREE ON PersonnelRoles (WPUserID);";
    $sqlParams[] = [];

    //Insertamos en la tabla
    $params = array();
    $wp_roles = new WP_Roles;
    $namesRoles    = $wp_roles->get_names();

    $users = get_all_user_all_roles();
    foreach ($users as $userItem) {
        foreach ($userItem->roles as $role) {
            if (isset($namesRoles[$role])) {
                $params[] = "('{$userItem->ID}', '{$namesRoles[$role]}')";
            }
        }
    }
    if (count($params) > 0) {
        $params  = implode(",", $params);
        $sqlArray[] = "INSERT INTO PersonnelRoles VALUES {$params};";
        $sqlParams[] = $params;

        //Insertamos en la tabla de copia
        $sqlArray[] = "INSERT INTO PersonnelRolesCopy SELECT * FROM PersonnelRoles;";
        $sqlParams[] = [];
    }


    $data = getSelectUsers($user, $order, false, $offset, $amount);
    $sqlArray[] = $data['sql'];
    $sqlParams[] = $data['params'];

    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoUser($offset, $amount, $sqlArray, $sqlParams);


        $data = getSelectUsers($user, $order, true, $offset, $amount);
        $sqlArray[] = $data['sql'];
        $sqlParams[] = $data['params'];
    }


    $rows = igfae_intranet_execute_script($sqlArray, $sqlParams);

    $usersList = [];
    foreach ($rows['rows'] as $row) {
        $userInfo = new IntranetUser();
        $userInfo->setPersonnelId($row->id);
        $userInfo->setName($row->name);
        $userInfo->setFirstSurname($row->firstSurname);
        $userInfo->setSecondSurname($row->secondSurname);
        $userInfo->setResearchArea($row->researchArea);
        $userInfo->setResearchProgram($row->researchProgram);
        $userInfo->setWorkLine($row->workLine);
        $userInfo->setCollaborationExperiment($row->experiments);
        $userInfo->setIgfaeDepartment($row->departments);
        $userInfo->setValidated($row->validated);
        $userInfo->setPublished($row->published);
        $userInfo->setRol($row->rol);

        $usersList[] = $userInfo;
    }

    $info = [
        'users' => $usersList,
        'pagination' => $pagination
    ];

    return $info;
}

function getSelectUsers($user, $order, $isPagination, $offset, $amount)
{
    $where = "";
    $prepareParams = [];
    if (isset($user)) {
        $queryPrepare = getPrepareSearchUser($user);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }
    $order = getOrderUser($order);


    $sql = "select distinct
    a.PersonnelID as 'id',
    a.WPUserID as 'WPUserID',
    a.Name as 'name',
    a.FirstSurname as 'firstSurname',
    a.SecondSurname as 'secondSurname',       
    group_concat(distinct d.Name separator ' ; ') as departments, 
    group_concat(distinct ra.Name separator ' ; ') as 'researchArea',
    group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as 'researchProgram',
    group_concat(distinct wl.Name separator ' ; ') as 'workLine',
    group_concat(distinct ec.Name separator ' ; ') as 'experiments',    
    group_concat(distinct pr.`Role` separator ' ; ') as 'rol',
    a.Validated as 'validated',
    a.Published as 'published'
    from (
        select distinct
            p.PersonnelID,
            p.WPUserID,
            p.Name,
            p.FirstSurname,
            p.SecondSurname,
            group_concat(distinct cigfae.Name) as Igfaecategory,
            group_concat(distinct cxunta.Name) as Xuntacategory,
            group_concat(distinct cue.Name) as Uecategory,
            pra.ResearchAreaID,
            prp.ResearchProgramID ,
            pwl.WorkLineID,
            p.Validated,
            p.Published 
        from Personnel p 
        left join PersonnelResearchArea pra
            on pra.PersonnelID = p.PersonnelID 
            and (pra.EndDate is null or pra.EndDate >= CURRENT_DATE()) 
        left join ResearchArea ra
            on ra.ResearchAreaID = pra.ResearchAreaID 
        left join PersonnelResearchProgram prp
            on prp.PersonnelID = p.PersonnelID 
            and (prp.EndDate is null or prp.EndDate >= CURRENT_DATE())
        left join PersonnelWorkLine pwl
            on pwl.PersonnelID = p.PersonnelID 
            and (pwl.EndDate is null or pwl.EndDate >= CURRENT_DATE())
        left join PersonnelResearchGroup prg
            on prg.PersonnelID = p.PersonnelID 
            and (prg.EndDate is null or prg.EndDate >= CURRENT_DATE())
        left join PersonnelExperiment pe
            on pe.PersonnelID = p.PersonnelID 
            and (pe.EndDate is null or pe.EndDate >= CURRENT_DATE()) 
        left join ExperimentalCollaboration ec
            on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
        left join PersonnelCategory pcigfae
            on pcigfae.PersonnelID = p.PersonnelID
            and (pcigfae.EndDate is null or pcigfae.EndDate >= CURRENT_DATE())
        left join Category cigfae
            on cigfae.CategoryID = pcigfae.CategoryID 
            and cigfae.CategoryTypeID = " . IGFAE_INTRANET_CATEGORY_TYPE_IGFAE . "
        left join PersonnelCategory pcxunta
            on pcxunta.PersonnelID = p.PersonnelID
            and (pcxunta.EndDate is null or pcxunta.EndDate >= CURRENT_DATE())
        left join Category cxunta
            on cxunta.CategoryID = pcxunta.CategoryID 
            and cxunta.CategoryTypeID = " . IGFAE_INTRANET_CATEGORY_TYPE_XUNTA . "
        left join PersonnelCategory pcue
            on pcue.PersonnelID = p.PersonnelID
            and (pcue.EndDate is null or pcue.EndDate >= CURRENT_DATE())
        left join Category cue
            on cue.CategoryID = pcue.CategoryID 
            and cue.CategoryTypeID = " . IGFAE_INTRANET_CATEGORY_TYPE_UE . "
        left join PersonnelDepartment pd 
            on pd.PersonnelID = p.PersonnelID 
        left join TeamReference tr
            on tr.PersonnelID = p.PersonnelID 
        left join PersonnelContract pco
            on pco.PersonnelID = p.PersonnelID            
        left join Contract co
            on co.ContractID = pco.ContractID       
        left join PersonnelRoles pr
            on pr.WPUserID = p.WPUserID
        left join PersonnelReference pref
		    on pref.PersonnelID = p.PersonnelID 
    WHERE     
        {$where} 
        group by p.PersonnelID         
    ) a
    left join PersonnelResearchArea pra
	on pra.PersonnelID = a.PersonnelID 
	and (pra.EndDate is null or pra.EndDate >= CURRENT_DATE()) 
    left join ResearchArea ra
        on ra.ResearchAreaID = pra.ResearchAreaID 
    left join PersonnelResearchProgram prp
        on prp.PersonnelID = a.PersonnelID 
        and (prp.EndDate is null or prp.EndDate >= CURRENT_DATE())
    left join ResearchProgram rp
        on rp.ResearchProgramID = prp.ResearchProgramID 
    left join PersonnelWorkLine pwl
        on pwl.PersonnelID = a.PersonnelID 
        and (pwl.EndDate is null or pwl.EndDate >= CURRENT_DATE())
    left join WorkLine wl
        on wl.WorkLineID = pwl.WorkLineID 
    left join PersonnelExperiment pe
        on pe.PersonnelID = a.PersonnelID 
        and (pe.EndDate is null or pe.EndDate >= CURRENT_DATE()) 
    left join ExperimentalCollaboration ec
        on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID
    left join PersonnelRolesCopy pr
        on pr.WPUserID = a.WPUserID
    left join PersonnelDepartment pd 
        on pd.PersonnelID = a.PersonnelID 
    left join Department d
        on d.DepartmentID = pd.DepartmentID 
    left join PersonnelReference pref
        on pref.PersonnelID = a.PersonnelID           
    group by a.PersonnelID           
    ORDER BY {$order}";

    if ($isPagination) {
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    $data = [
        'sql' => $sql,
        'params' => $prepareParams
    ];

    return $data;
}
function getOrderUser($order)
{
    if (!isset($order)) {
        return ' name ASC';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0:
                $sort .= 'name ';
                break;
            case 1:
                $sort .= 'firstSurname ';
                break;
            case 2:
                $sort .= 'secondSurname ';
                break;
            case 3:
                $sort .= 'departments ';
                break;
            case 4:
                $sort .= 'researchArea ';
                break;
            case 5:
                $sort .= 'researchProgram ';
                break;
            case 6:
                $sort .= 'workLine ';
                break;
            case 7:
                $sort .= 'experiments ';
                break;
            case 8:
                $sort .= 'rol ';
                break;
            case 9:
                $sort .= 'validated ';
                break;
            case 10:
                $sort .= 'published ';
                break;
            default:
                $sort .= 'name ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function getPaginationInfoUser($offset, $amount, $sql, $params = [[]])
{
    $rows = igfae_intranet_execute_script($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];


    return $result;
}

function getPrepareSearchUser($user)
{
    $where = "1=1 ";
    $params = [];
    // personnel id
    if ($user->getPersonnelId()) {
        $where .= " AND p.PersonnelID  = %d";
        $params[] = $user->getPersonnelId();
    }
    // Name input
    if ($user->getName()) {
        $where .= " AND p.Name like %s ";
        $params[] = '%' . $user->getName() . '%';
    }
    // FirstSurname input
    if ($user->getFirstSurname()) {
        $where .= " AND p.FirstSurname like %s ";
        $params[] = '%' . $user->getFirstSurname() . '%';
    }
    // SecondSurname input
    if ($user->getSecondSurname()) {
        $where .= " AND p.SecondSurname like %s ";
        $params[] = '%' . $user->getSecondSurname() . '%';
    }
    // Gender
    if ($user->getGender() != null) {
        $where .= " AND (p.Gender  =  %d)";
        $params[] = $user->getGender();
    }
    // Research area select multiple
    if ($user->getResearchArea()) {
        $typesCount = count($user->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (pra.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $user->getResearchArea());
    }
    // Research program select multiple
    if ($user->getResearchProgram()) {
        $typesCount = count($user->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (prp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $user->getResearchProgram());
    }
    // Work line select multiple
    if ($user->getWorkLine()) {
        $typesCount = count($user->getWorkLine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersWorkLine = implode(',', $paramsPlaceholders);

        $where .= " AND (pwl.WorkLineID IN ({$placeholdersWorkLine}))";
        $params = array_merge($params, $user->getWorkLine());
    }
    // Collaboration experiment select multiple
    if ($user->getCollaborationExperiment()) {
        $typesCount = count($user->getCollaborationExperiment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersCollaborationExperiment = implode(',', $paramsPlaceholders);
        $where .= " AND (pe.ExperimentalCollaborationID IN ({$placeholdersCollaborationExperiment}))";
        $params = array_merge($params, $user->getCollaborationExperiment());
    }
    // Research group select multiple
    if ($user->getResearchGroup()) {
        $typesCount = count($user->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);
        $where .= " AND (prg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $user->getResearchGroup());
    }
    // xuntaCategory select multiple
    if ($user->getXuntaCategory()) {
        $typesCount = count($user->getXuntaCategory());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersCategory = implode(',', $paramsPlaceholders);
        $where .= " AND (pcxunta.CategoryID IN ({$placeholdersCategory}))";
        $params = array_merge($params, $user->getXuntaCategory());
    }
    // igfae category select multiple
    if ($user->getIgfaeCategory()) {
        $typesCount = count($user->getIgfaeCategory());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersCategory = implode(',', $paramsPlaceholders);
        $where .= " AND (pcigfae.CategoryID IN ({$placeholdersCategory}))";
        $params = array_merge($params, $user->getIgfaeCategory());
    }
    // ue category select multiple
    if ($user->getUeCategory()) {
        $typesCount = count($user->getUeCategory());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersCategory = implode(',', $paramsPlaceholders);
        $where .= " AND (pcue.CategoryID IN ({$placeholdersCategory}))";
        $params = array_merge($params, $user->getUeCategory());
    }
    // igfae department select multiple
    if ($user->getIgfaeDepartment()) {
        $typesCount = count($user->getIgfaeDepartment());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersDepartment = implode(',', $paramsPlaceholders);
        $where .= " AND (pd.DepartmentID IN ({$placeholdersDepartment}))";
        $params = array_merge($params, $user->getIgfaeDepartment());
    }
    // team reference select multiple
    if ($user->getTeamReference()) {
        $typesCount = count($user->getTeamReference());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersReference = implode(',', $paramsPlaceholders);
        $where .= " AND (tr.TeamReferenceID IN ({$placeholdersReference}))";
        $params = array_merge($params, $user->getTeamReference());
    }
    // contract select multiple
    if ($user->getContractType()) {
        $typesCount = count($user->getContractType());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersContractType = implode(',', $paramsPlaceholders);
        $where .= " AND (co.ContractID IN ({$placeholdersContractType}))";
        $params = array_merge($params, $user->getContractType());
    }
    // user reference select multiple
    if ($user->getUserReference()) {
        $typesCount = count($user->getUserReference());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersUserReference = implode(',', $paramsPlaceholders);
        $where .= " AND (pref.SupervisorID IN ({$placeholdersUserReference}))";
        $params = array_merge($params, $user->getUserReference());
    }
    // Validated
    if ($user->getValidated() != null) {
        $where .= " AND (p.Validated  =  %d)";
        $params[] = $user->getValidated();
    }
    // Published
    if ($user->getPublished() != null) {
        $where .= " AND (p.Published  =  %d)";
        $params[] = $user->getPublished();
    }
    // Foreign    
    if ($user->getForeign() !== null && $user->getForeign() != "") {
        $where .= " AND (p.`Foreign`  =  %d)";
        $params[] = $user->getForeign();
    }
    // Roles select multiple
    if ($user->getRol()) {
        $typesCount = count($user->getRol());
        $paramsPlaceholders = array_fill(0, $typesCount, '%s');
        $placeholdersRole = implode(',', $paramsPlaceholders);
        $where .= " AND (pr.`Role` IN ({$placeholdersRole}))";
        $params = array_merge($params, $user->getRol());
    }
    
    // Start Date and End Date
    if ($user->getStartDate() && $user->getEndDate()) {
        $startDates =  explode("-", $user->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (pco.StartDate BETWEEN %s and %s) ";
        $params[] = $fecha1;
        $params[] = $fecha2;

        //Fecha final
        $endDates =  explode("-", $user->getEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (pco.EndDate BETWEEN %s and %s) ";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    }  else if ($user->getStartDate()) {
        $startDates =  explode("-", $user->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( pco.StartDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    } else if ($user->getEndDate()){
        $endDates =  explode("-", $user->getEndDate());
        $endDate1 = explode("/", trim($endDates[0]));
        $fechaFormateada1 = array($endDate1[2], $endDate1[0], $endDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $endDate2 = explode("/", trim($endDates[1]));
        $fechaFormateada2 = array($endDate2[2], $endDate2[0], $endDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND ( pco.EndDate BETWEEN %s and %s )";
        $params[] = $fecha1;
        $params[] = $fecha2;
        
    }
    
    if ($user->getCurrentContratEndDate() && $user->getCurrentContratStartDate()) {
        
        $startContractDate = explode("/", $user->getCurrentContratStartDate());
        $fechaFormateada1 = array($startContractDate[2], $startContractDate[0], $startContractDate[1]);
        $fecha1 = implode("-", $fechaFormateada1);

        $endContractDate = explode("/", $user->getCurrentContratEndDate());
        $fechaFormateada2 = array($endContractDate[2], $endContractDate[0], $endContractDate[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (pco.StartDate <= %s) and (pco.EndDate >= %s or pco.EndDate IS NULL)";
        //$where .= " AND ( pco.StartDate BETWEEN %s and %s )";
        $params[] = $fecha2;
        $params[] = $fecha1;
        
        //Fecha final
        //$where .= " AND ( pco.EndDate BETWEEN %s and %s )";
        //$params[] = $fecha1;
        //$params[] = $fecha2;
        
    }  else if ($user->getCurrentContratStartDate()) {
        $startDate = explode("/", $user->getCurrentContratStartDate());
        $fechaFormateada1 = array($startDate[2], $startDate[0], $startDate[1]);
        $fecha3 = implode("-", $fechaFormateada1);
        $where .= " AND (pco.StartDate >= %s )";
        //$where .= " AND (pco.StartDate >= %s ) AND ((pco.EndDate >= %s ) OR pco.EndDate is null)";
        $params[] = $fecha3;
        //$params[] = $fecha3;

    } else if ($user->getCurrentContratEndDate()){
        $endDate = explode("/", $user->getCurrentContratEndDate());
        $fechaFormateada2 = array($endDate[2], $endDate[0], $endDate[1]);
        $fecha4 = implode("-", $fechaFormateada2);
        $where .= " AND (pco.EndDate <= %s ) ";
        //$where .= " AND ((pco.StartDate <= %s ) OR pco.StartDate is null) AND (pco.EndDate <= %s ) ";
        //$params[] = $fecha4;
        $params[] = $fecha4;
        
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
            $where .= " and (pref.SupervisorID in (%d) or p.PersonnelID = %d or p.CreatedBy = %d)";
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

function fetchUserExport($user)
{
    $result = fetchUsers($user, 0, NULL);
    $idsUser = [];
    $listRoles = [];
    $idsUserStr = "";
    foreach ($result['users'] as $row) {
        if ($row->getRol() !== null) {
            $listRoles[$row->getPersonnelId()] = $row->getRol();
            $idsUser[] = $row->getPersonnelId();
        } else {
            $idsUser[] = $row->getPersonnelId();
        }
    }
    $idsUserStr = implode(',', $idsUser);
    if (count($idsUser) > 0) {
        $sql = "select distinct
        p.PersonnelID as 'id',
        concat(p.Name, ' ', p.FirstSurname, ' ',coalesce(p.SecondSurname, '')) as Name,
        p.BirthDate,    
        p.IDCard as NIF,
        p.Phone,
        p.Mobile,
        building.Building as Building,
        location.Room as Location,
        p.EMail,
        group_concat(distinct inspire.Code separator ' ; ') as Arxiv,
        group_concat(distinct pesorcid.Code separator ' ; ') as Orcid,
        group_concat(distinct wosid.Code separator ' ; ') as Wosid,
        group_concat(distinct scopusid.Code separator ' ; ') as Scopusid,
        group_concat(distinct sf.Firm separator ' ; ') as ScientificFirms,
        p.Website,
        p.Gender,
        country.Name as CountryName,
        p.UE,
        p.IGFAAEID,	
        group_concat(distinct d.Name separator ' ; ') as Departments,
        maplocation.Name as 'nameLocation',
        p.Foreign,
        p.Biography,
        group_concat(distinct tr.Name separator ' ; ') as Teamreference,
        group_concat(distinct concat(pReference.Name, ' ', pReference.FirstSurname, ' ', coalesce(pReference.SecondSurname, '')) separator ' ; ') as ReferenceName,
        group_concat(distinct ra.name separator ' ; ') as ResearchAreas,
        group_concat(distinct (concat(rp.Name, ' (', rp.Acronym, ')')) separator ' ; ') as ResearchPrograms,
        group_concat(distinct wl.name separator ' ; ') as WorkLines,
        group_concat(distinct ec.name separator ' ; ') as Experiments,
        p.Comment,
        group_concat(distinct concat (rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
        group_concat(distinct co.Name separator ' ; ') as Contracts,
        p.Validated,
        p.ValidatedBy,
        p.ValidatedOn,
        p.Published,
        p.PublishedBy,
        p.PublishedOn
        from Personnel p 
        left join PersonnelResearchArea pra
            on pra.PersonnelID = p.PersonnelID        
        left join ResearchArea ra
            on ra.ResearchAreaID = pra.ResearchAreaID 
        left join PersonnelResearchProgram prp
            on prp.PersonnelID = p.PersonnelID        
        left join ResearchProgram rp
            on rp.ResearchProgramID = prp.ResearchProgramID 
        left join PersonnelWorkLine pwl
            on pwl.PersonnelID = p.PersonnelID        
        left join WorkLine wl
            on wl.WorkLineID = pwl.WorkLineID 
        left join PersonnelResearchGroup prg
            on prg.PersonnelID = p.PersonnelID        
        left join ResearchGroup rg
            on rg.ResearchGroupID = prg.ResearchGroupID
        left join PersonnelExperiment pe
            on pe.PersonnelID = p.PersonnelID       
        left join ExperimentalCollaboration ec
            on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
        left join PersonnelDepartment pd 
            on pd.PersonnelID = p.PersonnelID 
        left join Department d
            on d.DepartmentID = pd.DepartmentID 
        left join TeamReference tr
            on tr.PersonnelID = p.PersonnelID 
        left join PersonnelContract pco
            on pco.PersonnelID = p.PersonnelID        
        left join Contract co
            on co.ContractID = pco.ContractID 
        left join Country country
            on country.Code = p.CountryCode
        left join Location location
            on location.LocationID = p.LocationID 
        left join Building building
            on building.BuildingID = location.BuildingID 
        left join MapLocation maplocation
            on maplocation.MapLocationID = p.MapLocationID         
        left join ScientificFirm sf
            on sf.PersonnelID = p.PersonnelID 
        left join PersonnelExternalSource inspire
            on inspire.PersonnelID = p.PersonnelID 
            and inspire.SourceID = " . IGFAE_INTRANET_SOURCE_INSPIRE . "        
        left join PersonnelExternalSource pesorcid
            on pesorcid.PersonnelID = p.PersonnelID 
            and pesorcid.SourceID = " . IGFAE_INTRANET_SOURCE_ORCID . "
        left join PersonnelExternalSource wosid
            on wosid.PersonnelID = p.PersonnelID 
            and wosid.SourceID = " . IGFAE_INTRANET_SOURCE_WOS . "
        left join PersonnelExternalSource scopusid
            on scopusid.PersonnelID = p.PersonnelID 
            and scopusid.SourceID = " . IGFAE_INTRANET_SOURCE_SCOPUS . "
        left join PersonnelReference pr
            on pr.PersonnelID = p.PersonnelID 
        left join Personnel pReference
            on pReference.PersonnelID = pr.SupervisorID 
        where 1=1
        AND p.PersonnelID in ($idsUserStr)
        group by p.PersonnelID ";

        $result = igfae_intranet_fetch_data($sql);

        //Redes sociales
        $sql = "select psn.PersonnelID as 'id', sn.Name as 'name', psn.SocialID as 'socialID'
        from PersonnelSocialNetwork psn
        left join SocialNetwork sn
            on sn.SocialNetworkID = psn.SocialNetworkID
        where 1=1
        AND psn.PersonnelID in ($idsUserStr)";


        $resultNetworks = igfae_intranet_fetch_data($sql);

        //Proyectos de investigación científica
        $sql = "select pp.PersonnelID as 'id', group_concat(distinct COALESCE(projectLanguage.Title, pro.Request) separator ' ; ') as 'name'
        from Project pro 
        left join ProjectPersonnel pp
            on pro.ProjectID = pp.ProjectID 
        left join ProjectLanguage projectLanguage
            on projectLanguage.ProjectID = pp.ProjectID 
            and projectLanguage.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        where 1=1
        AND pp.PersonnelID in ($idsUserStr)
        group by pp.PersonnelID";


        $resultProjects = igfae_intranet_fetch_data($sql);

        //Proyectos de producción científica
        $sql = "select ppa.PersonnelID as 'id', group_concat(distinct ppl.Title separator ' ; ') as 'name'
        from ProductionProject pp
        left join ProductionProjectAuthor ppa
            on pp.ProductionProjectID = ppa.ProductionProjectID 
        left join ProductionProjectLanguage ppl
            on ppl.ProductionProjectID = ppa.ProductionProjectID 
            and ppl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        where 1=1
        AND ppa.PersonnelID in ($idsUserStr)
        group by ppa.PersonnelID";


        $resultProductionProject = igfae_intranet_fetch_data($sql);

        //Eventos   
        $sql = "select ep.PersonnelID as 'id', group_concat(distinct el.Title separator ' ; ') as 'name'
        from Event e
        left join EventPersonnel ep
            on e.EventID = ep.EventID 
        left join EventLanguage el
            on el.EventID = ep.EventID 
        where 1=1
        AND ep.PersonnelID in ($idsUserStr)
        group by ep.PersonnelID";


        $resultEvents = igfae_intranet_fetch_data($sql);

        //Estancias
        $sql = "select p.PersonnelID as 'id', group_concat(distinct concat (wpDisplaced.FinancedBy, ' (', wpDisplaced.StartDate, ' - ', wpDisplaced.EndDate, ')' ) separator ' ; ') as 'name'
        from Personnel p 
        inner join WorkPlacement wpDisplaced
            on wpDisplaced.DisplacedResearcherID = p.PersonnelID
        where 1=1
        AND p.PersonnelID in ($idsUserStr)
        group by p.PersonnelID";


        $resultWorkPlacement = igfae_intranet_fetch_data($sql);

        //Estancias
        $sql = "select p.PersonnelID as 'id', group_concat(distinct concat (wpTutor.FinancedBy, ' (', wpTutor.StartDate, ' - ', wpTutor.EndDate, ')' ) separator ' ; ') as 'name'
        from Personnel p 
        inner join WorkPlacement wpTutor
            on wpTutor.TutorID = p.PersonnelID
        where 1=1
        AND p.PersonnelID in ($idsUserStr)
        group by p.PersonnelID";


        $resultWorkPlacementTutor = igfae_intranet_fetch_data($sql);

        //Transferencias
        $sql = "select tp.PersonnelID as 'id', group_concat(tl.Title separator ' ; ') as 'name'
        from Transfer t 
        left join TransferPersonnel tp
            on t.TransferID = tp.TransferID 
        left join TransferLanguage tl
            on tl.TransferID = tp.TransferID 
            and tl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        where 1=1
        AND tp.PersonnelID in ($idsUserStr)
        group by tp.PersonnelID";


        $resultTransfers = igfae_intranet_fetch_data($sql);

        //Categorías
        $sql = "select pc.PersonnelID as 'id', c.CategoryTypeID as 'typeId', pc.CategoryID as 'categoryId', c.Name as 'name', pc.StartDate as 'startDate',
        pc.EndDate as 'endDate'
        from PersonnelCategory pc 
        inner join Category c
        on c.CategoryID = pc.CategoryID 
        inner join CategoryType ct
        on ct.CategoryTypeID = c.CategoryTypeID 	 
        where 1=1
        AND pc.PersonnelID in ($idsUserStr)";


        $resultCategories = igfae_intranet_fetch_data($sql);
        $user_id = get_current_user_id();
        foreach ($result['rows'] as $row) {
            //Comprobamos si el usuario tiene el rol Editor DNI para mostrar el DNI
            if (!is_user_in_role($user_id, IGFAE_INTRANET_ROLE_EDITOR_DNI)) {
                
                $igfaeUserId = getUserByWPUserID($user_id);
                if (!empty($igfaeUserId)) {
                    $id = $igfaeUserId[0]["id"];
                    if ($row->id != $id) {
                        $row->NIF = "";
                    } 
                } else {
                    $row->NIF = "";
                }
            
            }
            // Redes sociales
            $networksStr = "";
            $networks = [];
            foreach ($resultNetworks['rows'] as $rowNetworks) {
                if ($row->id == $rowNetworks->id) {
                    $networks[] = $rowNetworks->name . " - " . $rowNetworks->socialID;
                }
            }
            if (count($networks) > 0) {
                $networksStr = implode(' ; ', $networks);
            }
            // Proyectos de investigación científica
            $namesProjectsStr = "";
            $namesProjects = [];
            foreach ($resultProjects['rows'] as $rowProjects) {
                if ($row->id == $rowProjects->id) {
                    $namesProjects[] = $rowProjects->name;
                }
            }
            if (count($namesProjects) > 0) {
                $namesProjectsStr = implode(' ; ', $namesProjects);
            }
            // Proyectos de producción científica
            $namesProductionProjectStr = "";
            $namesProductionProject = [];
            foreach ($resultProductionProject['rows'] as $rowProductionProject) {
                if ($row->id == $rowProductionProject->id) {
                    $namesProductionProject[] = $rowProductionProject->name;
                }
            }
            if (count($namesProductionProject) > 0) {
                $namesProductionProjectStr = implode(' ; ', $namesProductionProject);
            }
            // Eventos
            $namesEventStr = "";
            $namesEvent = [];
            foreach ($resultEvents['rows'] as $rowEvent) {
                if ($row->id == $rowEvent->id) {
                    $namesEvent[] = $rowEvent->name;
                }
            }
            if (count($namesEvent) > 0) {
                $namesEventStr = implode(' ; ', $namesEvent);
            }
            // Estancias
            $namesWorkPlacementStr = "";
            $namesWorkPlacements = [];
            foreach ($resultWorkPlacement['rows'] as $rowWorkPlacement) {
                if ($row->id == $rowWorkPlacement->id) {
                    $namesWorkPlacements[] = $rowWorkPlacement->name;
                }
            }
            if (count($namesWorkPlacements) > 0) {
                $namesWorkPlacementStr = implode(' ; ', $namesWorkPlacements);
            }
            // Estancias tutor
            $namesWorkPlacementTutorStr = "";
            $namesWorkPlacementsTutor = [];
            foreach ($resultWorkPlacementTutor['rows'] as $rowWorkPlacementTutor) {
                if ($row->id == $rowWorkPlacementTutor->id) {
                    $namesWorkPlacementsTutor[] = $rowWorkPlacementTutor->name;
                }
            }
            if (count($namesWorkPlacementsTutor) > 0) {
                $namesWorkPlacementTutorStr = implode(' ; ', $namesWorkPlacementsTutor);
            }
            // Transferencias
            $namesTransfersStr = "";
            $namesTransfers = [];
            foreach ($resultTransfers['rows'] as $rowTransfer) {
                if ($row->id == $rowTransfer->id) {
                    $namesTransfers[] = $rowTransfer->name;
                }
            }
            if (count($namesTransfers) > 0) {
                $namesTransfersStr = implode(' ; ', $namesTransfers);
            }
            // Categorías
            $namesCategoriesStr = "";
            $namesCategories = [];
            foreach ($resultCategories['rows'] as $rowCategory) {
                if ($row->id == $rowCategory->id) {
                    $namesCategories[] = $rowCategory->name;
                }
            }
            if (count($namesCategories) > 0) {
                $namesCategoriesStr = implode(' ; ', $namesCategories);
            }

            $row->networks = $networksStr;
            $row->projects = $namesProjectsStr;
            $row->productionProjects = $namesProductionProjectStr;
            $row->events = $namesEventStr;
            $row->workPlacement = $namesWorkPlacementStr;
            $row->workPlacementTutor = $namesWorkPlacementTutorStr;
            $row->transfers = $namesTransfersStr;
            $row->category = $namesCategoriesStr;
            // Roles
            foreach ($listRoles as $keyRol => $rowRol) {
                if ($row->id == $keyRol) {
                    $row->rol = $rowRol;
                }
            }
        }
        return $result['rows'];
    } else {
        return [];
    }
}

function getReferencePersonnel($id)
{
    //Buscamos datos en Personnel
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND p.PersonnelID=  %d";
    $sql = "SELECT DISTINCT *
             FROM Personnel p          
             left join PersonnelReference pr on pr.SupervisorID = p.PersonnelID 
    where 1=1 
    {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);

    return $rows;
}


function getTransferPersonnel($id)
{
    //Buscamos datos en TransferPersonnel
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND tp.PersonnelID =  %d";
    $sql = "SELECT tl.TransferID as 'id', tl.Title as 'name'
    from Transfer t 
    left join TransferPersonnel tp
        on t.TransferID = tp.TransferID 
    left join TransferLanguage tl
        on tl.TransferID = tp.TransferID 
        and tl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    where 1=1 
    {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}

function getWorkPlacementDisplacedResearcher($id)
{
    //Buscamos datos en WorkPlacement-DisplacedResearcherID
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND p.PersonnelID =  %d";

    $sql = "SELECT distinct wpDisplaced.WorkPlacementID as 'id', concat (wpDisplaced.FinancedBy, ' (', wpDisplaced.StartDate, ' - ', wpDisplaced.EndDate, ')') as 'name'
     from Personnel p 
     left join WorkPlacement wpDisplaced
     on wpDisplaced.DisplacedResearcherID = p.PersonnelID
     where 1=1 
          {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}
function getWorkPlacementTutor($id)
{
    //Buscamos datos en WorkPlacement-TutorID
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND p.PersonnelID =  %d";

    $sql = "SELECT distinct wpTutor.WorkPlacementID as 'id', concat (wpTutor.FinancedBy, ' (', wpTutor.StartDate, ' - ', wpTutor.EndDate, ')' ) as 'name'
    from Personnel p 
    left join WorkPlacement wpTutor
        on wpTutor.TutorID = p.PersonnelID
    where 1=1 
              {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}
function getEventsPersonnel($id)
{
    //Buscamos datos en EventPersonnel
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND ep.PersonnelID =  %d";

    $sql = "SELECT distinct ep.EventID as 'id', e.EventTypeID as 'eventType', e.EventSubTypeID as 'eventSubType', el.Title as 'name'
    from Event e
    left join EventPersonnel ep
        on e.EventID = ep.EventID 
    left join EventLanguage el
        on el.EventID = ep.EventID
    where 1=1
          {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}
function getProductionProjectsAuthor($id)
{
    //Buscamos datos en ProductionProjectAuthor
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND ppa.PersonnelID =  %d";

    $sql = "SELECT distinct ppl.ProductionProjectID as 'id',  ppl.Title  as 'name'
    from ProductionProject pp
    left join ProductionProjectAuthor ppa
        on pp.ProductionProjectID = ppa.ProductionProjectID 
    left join ProductionProjectLanguage ppl
        on ppl.ProductionProjectID = ppa.ProductionProjectID 
        and ppl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    where 1=1
       {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}

function getProjectPersonnel($id)
{
    //Buscamos datos en ProjectPersonnel
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND pp.PersonnelID =  %d";

    $sql = "SELECT distinct 
	pro.ProjectID as 'id', 
	COALESCE(projectLanguage.Title, pro.Request) as 'name', 
	group_concat(concat(pcoord.Name, ' ', pcoord.FirstSurname, ' ', coalesce(pcoord.SecondSurname, '' )) separator ' ; ') as IPCoordinator,
	group_concat(concat(pip.Name, ' ', pip.FirstSurname, ' ', coalesce(pip.SecondSurname, '' )) separator ' ; ') as IP,
	group_concat(concat(pipas.Name, ' ', pipas.FirstSurname, ' ', coalesce(pipas.SecondSurname, '' )) separator ' ; ') as IPAsociated,
	group_concat(concat(ptech.Name, ' ', ptech.FirstSurname, ' ', coalesce(ptech.SecondSurname, '' )) separator ' ; ') as Technician
        from Project pro 
        left join ProjectPersonnel pp
            on pro.ProjectID = pp.ProjectID 
        left join ProjectLanguage projectLanguage
            on projectLanguage.ProjectID = pp.ProjectID 
            and projectLanguage.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
        left join ProjectPersonnel ppcoord  
            on ppcoord.ProjectID = pp.ProjectID 
            and ppcoord.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
        left join Personnel pcoord
            on pcoord.PersonnelID = ppcoord.PersonnelID 
        left join ProjectPersonnel ppip
            on ppip.ProjectID = pp.ProjectID 
            and ppip.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IP . "
        left join Personnel pip
            on pip.PersonnelID = ppip.PersonnelID
        left join ProjectPersonnel ppipas
            on ppipas.ProjectID = pp.ProjectID 
            and ppipas.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IA . "
        left join Personnel pipas
            on pipas.PersonnelID = ppipas.PersonnelID
        left join ProjectPersonnel pptech
            on pptech.ProjectID = pp.ProjectID 
            and pptech.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_TE . "
        left join Personnel ptech
            on ptech.PersonnelID = pptech.PersonnelID
        where 1=1
       {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}

function getPrototypePersonnel($id)
{
    //Buscamos datos en PrototypePersonnel
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND pp.PersonnelID =  %d";

    $sql = "SELECT distinct pp.PrototypeID as 'id'
    from Personnel p
    left join PrototypePersonnel pp
        on pp.PersonnelID = p.PersonnelID 
    where 1=1
       {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}
function getPersonnelExperiments($id)
{
    //Buscamos datos en PrototypePersonnel
    $where = "";
    $params = [];
    $params[] = $id;
    $where .= " AND pe.PersonnelID =  %d";

    $sql = "SELECT pe.PersonnelExperimentID as 'id', ec.Name as 'name'
    from  PersonnelExperiment pe
    left join ExperimentalCollaboration ec
        on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID 
        and (pe.EndDate is null or pe.EndDate >= CURRENT_DATE())
    where 1=1
       {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    return $rows;
}
function getRelationsPersonnel($id)
{

    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getReferencePersonnel($id);

    foreach ($rows['rows'] as $row) {
        if (isset($row->PersonnelID)) {
            $resultArray[] = array("id" => $row->PersonnelID);
        }
    }
    
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because he/she is a reference user");
        return $result;
    }

    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getTransferPersonnel($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of transfers assigned");
        return $result;
    }


    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getWorkPlacementDisplacedResearcher($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of workplacements assigned");
        return $result;
    }


    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getWorkPlacementTutor($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of workplacements assigned");
        return $result;
    }


    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getEventsPersonnel($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of events assigned");
        return $result;
    }


    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getProductionProjectsAuthor($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of production projects assigned");
        return $result;
    }


    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getProjectPersonnel($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of projects assigned");
        return $result;
    }

    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getPrototypePersonnel($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of prototypes assigned");
        return $result;
    }

    $resultArray = array();
    $result = [];
    $result['count'] = 0;
    $rows = getPersonnelExperiments($id);
    foreach ($rows['rows'] as $row) {
        if (isset($row->id)) {
            $resultArray[] = array("id" => $row->id);
        }
    }
    if (count($resultArray) > 0) {
        $result['count'] = count($resultArray);
        $result['message'] = __("User cannot be removed because of experiments assigned");
        return $result;
    }

    return $result;
}
// Delete user
function deleteUser($id)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $id);

    $where = array('PersonnelID' => $id);

    igfae_intranet_delete('PersonnelWorkLine', $where);
    igfae_intranet_delete('PersonnelResearchArea', $where);
    igfae_intranet_delete('PersonnelResearchGroup', $where);
    igfae_intranet_delete('PersonnelResearchProgram', $where);
    igfae_intranet_delete('PersonnelExperiment', $where);
    igfae_intranet_delete('PersonnelExternalSource', $where);
    igfae_intranet_delete('PersonnelReference', $where);
    igfae_intranet_delete('PersonnelContract', $where);
    igfae_intranet_delete('TeamReference', $where);
    igfae_intranet_delete('ScientificFirm', $where);
    igfae_intranet_delete('PersonnelSocialNetwork', $where);
    igfae_intranet_delete('PersonnelCategory', $where);
    igfae_intranet_delete('PersonnelDepartment', $where);

    igfae_intranet_delete('Personnel', $where);

    //Eliminamos el usuario de WP
    //Obtenemos el valor de WPUserID
    $userInfo = getUserById($id);
    if ($userInfo->getWpUserId() !== null && $userInfo->getWpUserId() !== "") {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        wp_delete_user($userInfo->getWpUserId());
    }

    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }
}


function getUserById($userId)
{
    $user = NULL;
    $where = "";
    $prepareParams = [];
    if (isset($userId)) {
        $where = " AND p.PersonnelID  = %d";
        $prepareParams = $userId;
    }


    $sql = "SELECT distinct
	p.PersonnelID,
    p.WPUserID,
	p.Name,
	p.FirstSurname,
	p.SecondSurname,
	p.BirthDate,
	p.IDCard as NIF,
    p.PhotoID,
	p.Phone,
	p.Mobile,
	building.Building as Building,
    building.BuildingID as BuildingID,
	location.Room as Location,
    location.LocationID as LocationID,
	p.EMail,
    group_concat(distinct inspire.Code separator ' ; ') as 'Arxiv',
    inspire.PersonnelExternalSourcesID as 'ArxivId',
	group_concat(distinct pesorcid.Code separator ' ; ') as 'Orcid',
    pesorcid.PersonnelExternalSourcesID as 'OrcidId',
	group_concat(distinct wosid.Code separator ' ; ') as 'Wosid',
    wosid.PersonnelExternalSourcesID as 'WosidId',
	group_concat(distinct scopusid.Code separator ' ; ') as 'Scopusid',
    scopusid.PersonnelExternalSourcesID as 'ScopusidId',
	group_concat(distinct sf.Firm separator ' ; ') as 'ScientificFirms',
    sf.ScientificFirmID as ScientificFirmID,
	p.Website,
	p.Gender,
	country.Name as 'CountryName',
    country.Code as 'CountryCode',
	p.UE,
	p.IGFAAEID,	
	group_concat(distinct d.DepartmentID  separator ';') as DepartmentsIDs,
	group_concat(distinct d.Name separator ' ; ') as Departments,
	maplocation.MapLocationID,
    maplocation.Name as MapLocationName,
	p.Biography,
	group_concat(distinct tr.Name separator ' ; ') as 'Teamreference',
    tr.TeamReferenceID as 'TeamReferenceID',
	p.Comment,
	p.Validated,
	p.ValidatedBy,
	p.ValidatedOn,
	p.Published,
	p.PublishedBy,
	p.PublishedOn,
    p.Foreign
    from Personnel p    
    left join PersonnelDepartment pd 
	    on pd.PersonnelID = p.PersonnelID 
    left join Department d
        on d.DepartmentID = pd.DepartmentID 
    left join TeamReference tr
        on tr.PersonnelID = p.PersonnelID 
    left join Country country
        on country.Code = p.CountryCode
    left join Location location
        on location.LocationID = p.LocationID 
    left join MapLocation maplocation
        on maplocation.MapLocationID = p.MapLocationID 
    left join Building building
        on building.BuildingID = location.BuildingID 
    left join ScientificFirm sf
        on sf.PersonnelID = p.PersonnelID 
    left join PersonnelExternalSource inspire
        on inspire.PersonnelID = p.PersonnelID 
        and inspire.SourceID = " . IGFAE_INTRANET_SOURCE_INSPIRE . "        
    left join PersonnelExternalSource pesorcid
        on pesorcid.PersonnelID = p.PersonnelID 
        and pesorcid.SourceID = " . IGFAE_INTRANET_SOURCE_ORCID . "
    left join PersonnelExternalSource wosid
        on wosid.PersonnelID = p.PersonnelID 
        and wosid.SourceID = " . IGFAE_INTRANET_SOURCE_WOS . "
    left join PersonnelExternalSource scopusid
        on scopusid.PersonnelID = p.PersonnelID 
        and scopusid.SourceID = " . IGFAE_INTRANET_SOURCE_SCOPUS . "
    where 1=1
    {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    //Obtenemos los nombres de los roles
    $wp_roles = new WP_Roles;
    $namesRoles    = $wp_roles->get_names();
    foreach ($rows['rows'] as $row) {
        $user = new IntranetUser();
        $user->setPersonnelId($row->PersonnelID);
        $user->setName($row->Name);
        $user->setFirstSurname($row->FirstSurname);
        $user->setSecondSurname($row->SecondSurname);
        $user->setGender($row->Gender);
        $user->setIgfaeDepartmentIDs(explode(";",$row->DepartmentsIDs));
        $user->setIgfaeDepartment($row->Departments);
        $user->setValidated($row->Validated);
        $user->setPublished($row->Published);
        $user->setValidatedBy($row->ValidatedBy);
        $user->setPublishedBy($row->PublishedBy);
        $user->setValidatedOn($row->ValidatedOn);
        $user->setPublishedOn($row->PublishedOn);
        $user->setBirthDate($row->BirthDate);
        $user->setIdCard($row->NIF);
        $user->setPhotoID($row->PhotoID);
        $user->setPhone($row->Phone);
        $user->setMobile($row->Mobile);
        $user->setBuilding($row->BuildingID . ";" . $row->Building);
        $user->setLocation($row->LocationID . ";" . $row->Location);
        $user->setEmail($row->EMail);
        $user->setWebSite($row->Website);
        $user->setCountry($row->CountryCode . ";" . $row->CountryName);
        $user->setUe($row->UE);
        $user->setIgfaeID($row->IGFAAEID);
        $user->setBiography($row->Biography);
        $user->setMapLocation($row->MapLocationID . ";" . $row->MapLocationName);
        $user->setComments($row->Comment);
        $user->setForeign($row->Foreign);
        $user->setWpUserId($row->WPUserID);
        $photoID = $row->PhotoID;
        $filePhoto = NULL;
        if ($photoID) {
            $filePhoto = getPathToFile($photoID);
        }
        $user->setPhoto($filePhoto);
        if (isset($row->WPUserID)) {
            $rolUser = get_user_roles_by_user_id($row->WPUserID);
            if (isset($rolUser)) {
                $arrayRoles = array();
                foreach ($rolUser as $rol) {
                    if (isset($namesRoles[$rol])) {
                        $arrayRoles[] = $namesRoles[$rol];
                    }
                }
                $user->setRol($arrayRoles);
            }
        }
        $users[] = $user;
    }
    $userId = $user->getPersonnelId();
    // Recuperamos los valores del personal de referencia
    $sql = "select distinct pr.SupervisorID as 'id', concat(p.Name, ' ', p.FirstSurname, COALESCE(concat(' ', p.SecondSurname), '')) as 'name'
    from PersonnelReference pr 
    inner join Personnel p
        on pr.SupervisorID = p.PersonnelID    
    where 1=1
    AND pr.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $userReference = [];
    foreach ($rows['rows'] as $row) {
        $userReference[$row->id] = $row->name;
    }

    $user->setUserReference($userReference);

    // Recuperamos los valores de a quien supervisa el usuario
    $sql = "select distinct pr.PersonnelID as 'id', concat(p.Name, ' ', p.FirstSurname, COALESCE(concat(' ', p.SecondSurname), '')) as 'name'
    from PersonnelReference pr 
    inner join Personnel p
        on pr.PersonnelID = p.PersonnelID    
    where 1=1
    AND pr.SupervisorID in ($userId)
    ORDER BY name;";

    $rows = igfae_intranet_fetch_data($sql);
    $userSupervisor = [];
    foreach ($rows['rows'] as $row) {
        $userSupervisor[$row->id] = $row->name;
    }

    $user->setUserSupervisor($userSupervisor);

    // Recuperamos los valores de los equipos de referencia
    $sql = "select distinct tr.TeamReferenceID as 'id', tr.Name as 'name'
    from TeamReference tr
    left join Personnel p
        on tr.PersonnelID = p.PersonnelID    
    where 1=1
    AND tr.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $teams = [];
    foreach ($rows['rows'] as $row) {
        $teams[$row->id] = $row->name;
    }

    $user->setTeamReference($teams);
    // Recuperamos los valores de orcid
    $sql = "select distinct pesorcid.Code as 'name',
    pesorcid.PersonnelExternalSourcesID as 'id'
    from PersonnelExternalSource pesorcid
    left join Personnel p
        on pesorcid.PersonnelID = p.PersonnelID    
    where 1=1
    AND pesorcid.SourceID = " . IGFAE_INTRANET_SOURCE_ORCID . "
    AND pesorcid.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $externalResources = [];
    foreach ($rows['rows'] as $row) {
        $externalResources[$row->id] = $row->name;
    }

    $user->setOrcid($externalResources);

    // Recuperamos los valores de research
    $sql = "select distinct pesorcid.Code as 'name',
    pesorcid.PersonnelExternalSourcesID as 'id'
    from PersonnelExternalSource pesorcid
    left join Personnel p
        on pesorcid.PersonnelID = p.PersonnelID    
    where 1=1
    AND pesorcid.SourceID = " . IGFAE_INTRANET_SOURCE_WOS . "
    AND pesorcid.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $externalResources = [];
    foreach ($rows['rows'] as $row) {
        $externalResources[$row->id] = $row->name;
    }

    $user->setResearchId($externalResources);
    // Recuperamos los valores de scopus
    $sql = "select distinct pesorcid.Code as 'name',
    pesorcid.PersonnelExternalSourcesID as 'id'
    from PersonnelExternalSource pesorcid
    left join Personnel p
        on pesorcid.PersonnelID = p.PersonnelID    
    where 1=1
    AND pesorcid.SourceID = " . IGFAE_INTRANET_SOURCE_SCOPUS . "
    AND pesorcid.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $externalResources = [];
    foreach ($rows['rows'] as $row) {
        $externalResources[$row->id] = $row->name;
    }

    $user->setScopus($externalResources);

    // Recuperamos los valores de inspire
    $sql = "select distinct pesorcid.Code as 'name',
        pesorcid.PersonnelExternalSourcesID as 'id'
        from PersonnelExternalSource pesorcid
        left join Personnel p
            on pesorcid.PersonnelID = p.PersonnelID    
        where 1=1
        AND pesorcid.SourceID = " . IGFAE_INTRANET_SOURCE_INSPIRE . "
        AND pesorcid.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $externalResources = [];
    foreach ($rows['rows'] as $row) {
        $externalResources[$row->id] = $row->name;
    }

    $user->setInspireRef($externalResources);

    // Recuperamos los valores de scientificFirm
    $sql = "select distinct sc.Firm as 'name',
        sc.ScientificFirmID as 'id'
        from ScientificFirm sc
        left join Personnel p
            on sc.PersonnelID = p.PersonnelID    
        where 1=1
        AND sc.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $scientificFirms = [];
    foreach ($rows['rows'] as $row) {
        $scientificFirms[$row->id] = $row->name;
    }

    $user->setScientificFirm($scientificFirms);

    // Recuperamos los valores de las areas de investigacion
    $sql = "select distinct ra.ResearchAreaID as 'id', ra.Name as 'name', pra.StartDate as 'startDate',
     pra.EndDate as 'endDate'
    from PersonnelResearchArea pra
    left join ResearchArea ra
        on ra.ResearchAreaID = pra.ResearchAreaID       
    where 1=1
    AND pra.PersonnelID in ($userId)
    order by pra.StartDate desc;";

    $rows = igfae_intranet_fetch_data($sql);
    $researchAreas = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $researchAreas[$row->id  . ";" . formatDate($row->startDate)  . ";" . formatDate($row->endDate)] = $row->name;
        }
    }
    $user->setResearchArea($researchAreas);

    // Recuperamos los valores de los grupos de investigación    
    $sql = "select distinct rg.ResearchGroupID as 'id', concat(rg.Name, ' (', rg.Acronym, ')') as 'name', prg.StartDate as 'startDate',
    prg.EndDate as 'endDate',
    rg.Acronym as 'USC',
    rg.Website as 'web'
    from PersonnelResearchGroup prg
    left join ResearchGroup rg
	on rg.ResearchGroupID = prg.ResearchGroupID	 
    where 1=1
    AND prg.PersonnelID in ($userId)
    order by prg.StartDate desc;";

    $rows = igfae_intranet_fetch_data($sql);
    $researchGroups = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $researchGroups[$row->id  . ";" . formatDate($row->startDate)  . ";" . formatDate($row->endDate)] = $row->name . ";" . $row->USC . ";" . $row->web;
        }
    }
    $user->setResearchGroup($researchGroups);
    // Recuperamos los valores de los programas de investigación    
    $sql = "select distinct rp.ResearchProgramID as 'id', concat(rp.Name, ' (', rp.Acronym, ')') as 'name', prp.StartDate as 'startDate',
    prp.EndDate as 'endDate'
    from PersonnelResearchProgram prp
    left join ResearchProgram rp
	on rp.ResearchProgramID = prp.ResearchProgramID	
    where 1=1
    AND prp.PersonnelID in ($userId)
    order by prp.StartDate desc;";

    $rows = igfae_intranet_fetch_data($sql);
    $researchPrograms = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $researchPrograms[$row->id  . ";" . formatDate($row->startDate)  . ";" . formatDate($row->endDate)] = $row->name;
        }
    }
    $user->setResearchProgram($researchPrograms);
    // Recuperamos las líneas de trabajo
    $sql = "select distinct wl.WorkLineID as 'id', wl.Name as 'name', pwl.StartDate as 'startDate',
    pwl.EndDate as 'endDate'
    from PersonnelWorkLine pwl
    left join WorkLine wl
	on wl.WorkLineID = pwl.WorkLineID	
    where 1=1
    AND pwl.PersonnelID in ($userId)
    order by pwl.StartDate desc;";

    $rows = igfae_intranet_fetch_data($sql);
    $workLines = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $workLines[$row->id  . ";" . formatDate($row->startDate)  . ";" . formatDate($row->endDate)] = $row->name;
        }
    }
    $user->setWorkLine($workLines);

    // Recuperamos los contratos
    $sql = "select distinct co.ContractID as 'id', pco.PersonnelContractID as 'personnelContractID', p.PersonnelID as 'supervisorID', p.Name as 'supervisorName', p.FirstSurname as 'firstSurname',  
            IF(ISNULL(p.SecondSurname)=1, ' ', p.SecondSurname) as 'secondSurname', pco.FileCode as 'fileCode', pco.Extended as 'extended', co.Name as 'name',
            pco.StartDate as 'startDate', pco.EndDate as 'endDate' 
    from PersonnelContract pco
    left join Contract co on co.ContractID = pco.ContractID	
    left join Personnel p on p.PersonnelID = pco.ContractSupervisorID 
    where 1=1
    AND pco.PersonnelID in ($userId)
    order by pco.StartDate desc;";

    $rows = igfae_intranet_fetch_data($sql);
    $contracts = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            // obtener nombre del supervisor
            $supervisor = $row->supervisorID . "-" . $row->supervisorName . " " . $row->firstSurname . " " . $row->secondSurname;
            
            // Recuperamos los codigos Economic Activity
            $sqlE = "select ceac.PersonnelContractID as 'personnelContractID', 
                ceac.EconomicActivityCode  as 'economicActivityCode'
                from ContractEconomicActivityCode ceac 
                left join PersonnelContract pc on pc.PersonnelContractID  = ceac.PersonnelContractID 
                where 1=1 and ceac.PersonnelContractID in ($row->personnelContractID);";

            $rowsList = igfae_intranet_fetch_data($sqlE);
            $listEconomicActivityCode = array();
            foreach($rowsList['rows'] as $rowE){
                array_push($listEconomicActivityCode, $rowE->economicActivityCode);
            }         
            $listEconomicActivityCode=implode(",",$listEconomicActivityCode);
            $contracts[$row->id  . ";" . $supervisor . ";" . $listEconomicActivityCode . ";" . $row->fileCode . ";" . $row->extended . ";" . formatDate($row->startDate)  . ";" . formatDate($row->endDate)] = $row->name;
        }
    }
    $user->setContractType($contracts);

    // Redes sociales
    $sql = "select psn.SocialNetworkID as 'id', sn.Name as 'name', psn.SocialID as 'socialID'
    from PersonnelSocialNetwork psn
    left join SocialNetwork sn
	on sn.SocialNetworkID = psn.SocialNetworkID 
    where 1=1
    AND psn.PersonnelID in ($userId);";

    $rows = igfae_intranet_fetch_data($sql);
    $socialNetworks = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $socialNetworks[$row->id  . ";" . $row->socialID] = $row->name;
        }
    }
    $user->setSocialNetwork($socialNetworks);


    // Recuperamos los experimentos
    $sql = "select ec.ExperimentalCollaborationID as 'id', ec.Name as 'name', pe.StartDate as 'startDate',
    pe.EndDate as 'endDate'
    from  PersonnelExperiment pe
    left join ExperimentalCollaboration ec
	on ec.ExperimentalCollaborationID = pe.ExperimentalCollaborationID	
    where 1=1
    AND pe.PersonnelID in ($userId)
    order by ec.Name;";

    $rows = igfae_intranet_fetch_data($sql);
    $experiments = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $experiments[$row->id  . ";" . formatDate($row->startDate)  . ";" . formatDate($row->endDate)] = $row->name;
        }
    }
    $user->setCollaborationExperiment($experiments);

    // Recuperamos las categorías
    $sql = "select c.CategoryTypeID as 'typeId', pc.CategoryID as 'id', c.Name as 'name', pc.StartDate as 'startDate',
    pc.EndDate as 'endDate'
    from PersonnelCategory pc 
    inner join Category c
    on c.CategoryID = pc.CategoryID 
    inner join CategoryType ct
    on ct.CategoryTypeID = c.CategoryTypeID 	 
    where 1=1
    AND pc.PersonnelID in ($userId)
    order by pc.StartDate desc;";

    $rows = igfae_intranet_fetch_data($sql);
    $categories = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $categories[$row->typeId  . ";" . $row->id  . ";" . formatDate($row->startDate)  . ";" . formatDate($row->endDate)] = $row->name;
        }
    }
    $user->setCategory($categories);

    // Recuperamos las transferencias
    $sql = "select t.TransferID as 'id', COALESCE (tlEn.Title, tlEs.Title, tlGl.Title) as 'name'
    from Transfer t 
    left join TransferPersonnel tp
        on t.TransferID = tp.TransferID 
    left join TransferLanguage tlEn
        on tlEn.TransferID = tp.TransferID 
        and tlEn.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join TransferLanguage tlEs
        on tlEs.TransferID = tp.TransferID 
        and tlEs.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join TransferLanguage tlGl
        on tlGl.TransferID = tp.TransferID 
        and tlGl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    where 1=1
    AND tp.PersonnelID in ($userId)
    order by 2;";

    $rows = igfae_intranet_fetch_data($sql);
    $transfers = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $transfers[$row->id] = $row->name;
        }
    }
    $user->setTransfers($transfers);

    // Recuperamos las estancias del tutor
    $sql = "select distinct wpTutor.WorkPlacementID as 'id', wpTutor.FinancedBy as 'name', wpTutor.StartDate as 'startDate',wpTutor.EndDate as 'endDate'
    from Personnel p 
    left join WorkPlacement wpTutor
        on wpTutor.TutorID = p.PersonnelID
    where 1=1
    AND p.PersonnelID in ($userId)
    order by wpTutor.StartDate;";

    $rows = igfae_intranet_fetch_data($sql);
    $wp = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $wp[$row->id] = $row->name . ' (' . formatDate($row->startDate) . ' - ' . formatDate($row->endDate) .  ')';
        }
    }
    $user->setTutorWorkplacements($wp);
    // Recuperamos las estancias
    $sql = "select distinct wpDisplaced.WorkPlacementID as 'id', wpDisplaced.FinancedBy as 'name', wpDisplaced.StartDate as 'startDate', wpDisplaced.EndDate as 'endDate'
    from Personnel p 
    left join WorkPlacement wpDisplaced
        on wpDisplaced.DisplacedResearcherID = p.PersonnelID
    where 1=1
    AND p.PersonnelID in ($userId)
    order by wpDisplaced.StartDate;";

    $rows = igfae_intranet_fetch_data($sql);
    $wp = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $wp[$row->id] = $row->name . ' (' . formatDate($row->startDate) . ' - ' . formatDate($row->endDate) .  ')';
        }
    }
    $user->setWorkplacements($wp);
    // Recuperamos los eventos
    $sql = "select distinct ep.EventID as 'id', e.EventTypeID, e.EventSubTypeID , COALESCE (elEn.Title, elEs.Title, elGl.Title) as 'name'
    from Event e
    left join EventPersonnel ep
        on e.EventID = ep.EventID 
    left join EventLanguage elEn
        on elEn.EventID = ep.EventID
        and elEn.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join EventLanguage elEs
        on elEs.EventID = ep.EventID
        and elEs.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join EventLanguage elGl
        on elGl.EventID = ep.EventID
        and elGl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    where 1=1
    AND ep.PersonnelID in ($userId)
    order by 4;";

    $rows = igfae_intranet_fetch_data($sql);
    $events = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $events[$row->id] = $row->name;
        }
    }
    $user->setEvents($events);

    // Recuperamos proyectos de producción científica
    $sql = "select distinct pp.ProductionProjectID as 'id', COALESCE(pplEn.Title, pplEs.Title, pplGl.Title) as 'name'
    from ProductionProject pp
    left join ProductionProjectAuthor ppa
        on pp.ProductionProjectID = ppa.ProductionProjectID 
    left join ProductionProjectLanguage pplEn
        on pplEn.ProductionProjectID = ppa.ProductionProjectID 
        and pplEn.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProductionProjectLanguage pplEs
        on pplEs.ProductionProjectID = ppa.ProductionProjectID 
        and pplEs.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProductionProjectLanguage pplGl
        on pplGl.ProductionProjectID = ppa.ProductionProjectID 
        and pplGl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    where 1=1
    AND ppa.PersonnelID in ($userId)
    order by 2
    LIMIT 100;";

    $rows = igfae_intranet_fetch_data($sql);
    $projects = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $projects[$row->id] = $row->name;
        }
    }
    $user->setProductionProject($projects);
    // Recuperamos proyectos de investigación científica
    $sql = "select distinct 
	pro.ProjectID as 'id', 
	COALESCE(plEn.Title, plEs.Title, plGl.Title) as 'name', 
	group_concat(concat(pcoord.Name, ' ', pcoord.FirstSurname, ' ', coalesce(pcoord.SecondSurname, '' )) separator ' ; ') as IPCoordinator,
	group_concat(concat(pip.Name, ' ', pip.FirstSurname, ' ', coalesce(pip.SecondSurname, '' )) separator ' ; ') as IP,
	group_concat(concat(pipas.Name, ' ', pipas.FirstSurname, ' ', coalesce(pipas.SecondSurname, '' )) separator ' ; ') as IPAsociated,
	group_concat(concat(ptech.Name, ' ', ptech.FirstSurname, ' ', coalesce(ptech.SecondSurname, '' )) separator ' ; ') as Technician
    from Project pro 
    left join ProjectPersonnel pp
        on pro.ProjectID = pp.ProjectID 
    left join ProjectLanguage plEn
        on plEn.ProjectID = pp.ProjectID 
        and plEn.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProjectLanguage plEs
        on plEs.ProjectID = pp.ProjectID 
        and plEs.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProjectLanguage plGl
        on plGl.ProjectID = pp.ProjectID 
        and plGl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    left join ProjectPersonnel ppcoord
        on ppcoord.ProjectID = pp.ProjectID 
        and ppcoord.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IPCP . "
    left join Personnel pcoord
        on pcoord.PersonnelID = ppcoord.PersonnelID 
    left join ProjectPersonnel ppip
        on ppip.ProjectID = pp.ProjectID 
        and ppip.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IP . "
    left join Personnel pip
        on pip.PersonnelID = ppip.PersonnelID
    left join ProjectPersonnel ppipas
        on ppipas.ProjectID = pp.ProjectID 
        and ppipas.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_IA . "
    left join Personnel pipas
        on pipas.PersonnelID = ppipas.PersonnelID
    left join ProjectPersonnel pptech
        on pptech.ProjectID = pp.ProjectID 
        and pptech.PersonnelTypeID =  " . IGFAE_INTRANET_PERSONNEL_TYPE_TE . "
    left join Personnel ptech
        on ptech.PersonnelID = pptech.PersonnelID
    where 1=1
    AND pp.PersonnelID in ($userId)
    group by pro.ProjectID
    order by 2;";

    $rows = igfae_intranet_fetch_data($sql);
    $projects = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $projects[$row->id] = $row->name;
        }
    }
    $user->setResearchProject($projects);

    // Recuperamos los prototipos
    $sql = "select p.PrototypeID as 'id', COALESCE (plEn.Name, plEs.Name, plGl.Name) as 'name'
    from Prototype p 
    left join PrototypePersonnel pp
        on pp.PrototypeID = p.PrototypeID 
    left join PrototypeLanguage plEn
        on plEn.PrototypeID = p.PrototypeID 
        and plEn.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join PrototypeLanguage plEs
        on plEs.PrototypeID = p.PrototypeID 
        and plEs.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join PrototypeLanguage plGl
        on plGl.PrototypeID = p.PrototypeID 
        and plGl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    where 1=1
    AND pp.PersonnelID in ($userId)
    order by 2";

    $rows = igfae_intranet_fetch_data($sql);
    $prototypes = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $prototypes[$row->id] = $row->name;
        }
    }
    $user->setPrototypes($prototypes);
    return $user;
}


function saveUser($datosInsert, $user)
{
    $idUser = $_POST[IGFAE_INTRANET_PARAM_IDUSER];
    file_put_contents(ABSPATH."/usercreation.log","saveuser: 'user' variable contents: ".print_r($user,true)."\n",FILE_APPEND);
    file_put_contents(ABSPATH."/usercreation.log","saveuser: 'user' variable contents: ".print_r($datosInsert,true)."\n",FILE_APPEND);


    if (isset($idUser) && strlen($idUser) > 0) {

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();
        $userOldInfo = getUserById($idUser);

        //Comprobamos si se ha modificado el NIF para añadir auditoría
        if ($userOldInfo->getIdCard() != $user->getIdCard()) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DNI, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $idUser);
        }

        //Comprobamos si es un edificio y despacho nuevo
        $buildingId = insertBuilding($user->getBuilding());
        $locationId = insertRoom($buildingId, $user->getLocation());
        $datosInsert['LocationID'] = $locationId;

        $where = array('PersonnelID' => $idUser);

        igfae_intranet_update("Personnel", $datosInsert, $where, null, null);

        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsUser($idUser);

        //Usuario en wordpress, edición roles
        if ($idUser !== null && $user->getWpUserId() != null){
            insertUserWP($idUser, $user);
        }
        //Alta datos tablas relacionadas
        insertPersonnelResearchGroups($idUser, $user->getResearchGroup());
        insertPersonnelResearchAreas($idUser, $user->getResearchArea());
        insertPersonnelResearchPrograms($idUser, $user->getResearchProgram());
        insertPersonnelWorkLines($idUser, $user->getWorkLine());
        insertPersonnelContracts($idUser, $user->getContractType());
        insertPersonnelSocialNetworks($idUser, $user->getSocialNetwork());
        insertPersonnelExternalSource($idUser, $user->getScopus(), IGFAE_INTRANET_SOURCE_SCOPUS);
        insertPersonnelExternalSource($idUser, $user->getOrcid(), IGFAE_INTRANET_SOURCE_ORCID);
        insertPersonnelExternalSource($idUser, $user->getResearchId(), IGFAE_INTRANET_SOURCE_WOS);
        insertPersonnelExternalSource($idUser, $user->getInspireRef(), IGFAE_INTRANET_SOURCE_INSPIRE);
        insertPersonnelExperimental($idUser, $user->getCollaborationExperiment());
        insertTeamReference($idUser, $user->getTeamReference());
        insertPersonnelReference($idUser, $user->getUserReference());
        insertScientificFirm($idUser, $user->getScientificFirm());
        insertPersonnelCategory($idUser, $user->getCategory());
        insertPersonnelDepartments($idUser, $user->getIgfaeDepartment());
        // Auditoria        
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $idUser);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $idUser);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $idUser);
        }

        // Authorization
        $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $idUser);
        if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
            closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $idUser);
        }    

        $photoError = false;
        $photo = null;
        $id = NULL;
        if (!empty($_FILES['photoUser']) && !empty($_FILES['photoUser']['name'])) {
            // Solo viene informado si se ha introducido una nueva imagen
            if ($userOldInfo->getPhoto() != null) {
                igfae_intranet_delete_file(get_home_path() . $userOldInfo->getPhoto());
            }
            $filename = IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PREFIX . $idUser;
            $photo = igfae_intranet_create_file($_FILES['photoUser'], IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PATH . $filename);
            $fileId = null;
            if (!empty($photo)) {
                $fileId = saveFile($photo, IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PATH_ID);
                $sql = "UPDATE Personnel
                        SET
                        PhotoID = $fileId
                        WHERE PersonnelID = $idUser";

                igfae_intranet_save($sql);
                if ($userOldInfo->getPhotoID() != null && $userOldInfo->getPhotoID() != "") {
                    deleteFileFromBBDD($userOldInfo->getPhotoID());
                }
            } else {
                $photoError = true;
            }
        } else if ($user->getDeleteImage() != null && $user->getDeleteImage() == "true" && $userOldInfo->getPhotoID() != null && $userOldInfo->getPhotoID() != "") {
            // Sino viene fichero de entrada pero viene activado el flag de borrado => Borramos la foto existente del usuario
            igfae_intranet_update("Personnel", array('PhotoID' => NULL), $where, null, null);
            igfae_intranet_delete_file(get_home_path() . $userOldInfo->getPhoto());
            deleteFileFromBBDD($userOldInfo->getPhotoID());
        }
        if (igfae_intranet_is_transaction_ok() && !$photoError) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
            if (!empty($photo)) {
                igfae_intranet_delete_file($photo);
            }
        }
    } else {
        //NUEVO USUARIO
        global $current_user;
        file_put_contents(ABSPATH."/usercreation.log","saveuser: 'user' variable contents: ".print_r($user,true)."\n",FILE_APPEND);
        file_put_contents(ABSPATH."/usercreation.log","saveuser: 'user' variable contents: ".print_r($current_user,true)."\n",FILE_APPEND);
        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        //Comprobamos si es un edificio y despacho nuevo
        $buildingId = insertBuilding($user->getBuilding());
        $locationId = insertRoom($buildingId, $user->getLocation());
        $datosInsert['LocationID'] = $locationId;
        $datosInsert['CreatedBy'] = getUserByWPUserID($current_user->ID)[0]["id"];

        $id = igfae_intranet_insert("Personnel", $datosInsert, null);


        $photoError = false;
        //Alta imagen
        $photo = null;
        if (isset($_FILES['photoUser']) && !empty($_FILES['photoUser']['name'])) {
            $filename = IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PREFIX . $id;
            $photo = igfae_intranet_create_file($_FILES['photoUser'], IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PATH . $filename);
            $fileId = null;
            if (!empty($photo)) {
                $fileId = saveFile($photo, IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PATH_ID);

                $sql = "UPDATE Personnel
                SET
                PhotoID = $fileId
                WHERE PersonnelID = $id";

                igfae_intranet_save($sql);
            } else {
                $photoError = true;
            }
        }

        //Alta datos tablas relacionadas
        file_put_contents(ABSPATH."/usercreation.log","saveuser: 'user' variable contents: ".print_r($user,true)."\n",FILE_APPEND);
        insertPersonnelResearchGroups($id, $user->getResearchGroup());
        insertPersonnelResearchAreas($id, $user->getResearchArea());
        insertPersonnelResearchPrograms($id, $user->getResearchProgram());
        insertPersonnelWorkLines($id, $user->getWorkLine());
        insertPersonnelContracts($id, $user->getContractType());
        insertPersonnelSocialNetworks($id, $user->getSocialNetwork());
        insertPersonnelExternalSource($id, $user->getScopus(), IGFAE_INTRANET_SOURCE_SCOPUS);
        insertPersonnelExternalSource($id, $user->getOrcid(), IGFAE_INTRANET_SOURCE_ORCID);
        insertPersonnelExternalSource($id, $user->getResearchId(), IGFAE_INTRANET_SOURCE_WOS);
        insertPersonnelExternalSource($id, $user->getInspireRef(), IGFAE_INTRANET_SOURCE_INSPIRE);
        insertPersonnelExperimental($id, $user->getCollaborationExperiment());
        insertTeamReference($id, $user->getTeamReference());
        insertPersonnelReference($id, $user->getUserReference());
        insertScientificFirm($id, $user->getScientificFirm());
        insertPersonnelCategory($id, $user->getCategory());
        insertPersonnelDepartments($id, $user->getIgfaeDepartment());

        //Alta de usuario en wordpress
	file_put_contents(ABSPATH."/usercreation.log","saveuser: 'user' variable contents: ".print_r($user,true)."\n",FILE_APPEND);
        $wpUserInsertWP = insertUserWP($id, $user);

        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $id);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $id);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_PERSONNEL, $id);
        }

        if (igfae_intranet_is_transaction_ok() && !$photoError) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
            // Eliminamos el usuario de WP
            require_once(ABSPATH . 'wp-admin/includes/user.php');
            wp_delete_user($wpUserInsertWP);
            if (!empty($photo)) {
                igfae_intranet_delete_file($photo);
            }
        }
    }

    return $id;
}

function insertPersonnelResearchGroups($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $key => $item) {
            $time_input = strtotime($item['startDate']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $endDate = NULL;
            if (isset($item['endDate']) && $item['endDate'] != "") {
                $time_input = strtotime($item['endDate']);
                $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            }
            $data = array('ResearchGroupID' => $item['firstColumn'], 'StartDate' => $startDate, 'EndDate' => $endDate, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelResearchGroup', $data);
        }
    }
}
function insertPersonnelResearchAreas($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $time_input = strtotime($item['startDate']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $endDate = NULL;
            if (isset($item['endDate'])  && $item['endDate'] != "") {
                $time_input = strtotime($item['endDate']);
                $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            }
            $data = array('ResearchAreaID' => $item['firstColumn'], 'StartDate' =>  $startDate, 'EndDate' => $endDate, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelResearchArea', $data);
        }
    }
}
function insertPersonnelResearchPrograms($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $time_input = strtotime($item['startDate']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $endDate = NULL;
            if (isset($item['endDate'])  && $item['endDate'] != "") {
                $time_input = strtotime($item['endDate']);
                $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            }
            $data = array('ResearchProgramID' => $item['firstColumn'], 'StartDate' => $startDate, 'EndDate' => $endDate, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelResearchProgram', $data);
        }
    }
}
function insertPersonnelWorkLines($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $time_input = strtotime($item['startDate']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $endDate = NULL;
            if (isset($item['endDate'])  && $item['endDate'] != "") {
                $time_input = strtotime($item['endDate']);
                $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            }
            $data = array('WorkLineID' => $item['firstColumn'], 'StartDate' => $startDate, 'EndDate' => $endDate, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelWorkLine', $data);
        }
    }
}
function insertPersonnelContracts($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $time_input = strtotime($item['startDate']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $endDate = NULL;
            if (isset($item['endDate'])  && $item['endDate'] != "") {
                $time_input = strtotime($item['endDate']);
                $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            }

            $contractSupervisor = NULL;
            $rr = array('<span style="display:none">', '</span>');
            $contractSuperAll = str_replace($rr, "", $item['contractSupervisor']);
            if($contractSuperAll != "-"){
                $contractSupervisor = $contractSuperAll;
            }
            
            $fileCode = NULL;
            if($item['fileCode'] != ""){
                $fileCode = $item['fileCode'];
            } 

            $extended = $item['extended'];
            if($item['extended'] === ""){
                $extended = NULL;
            }

            $data = array('ContractID' => $item['firstColumn'], 'StartDate' => $startDate, 'EndDate' => $endDate, 'PersonnelID' => $id, 'ContractSupervisorID' => $contractSupervisor, 'FileCode' => $fileCode, 'Extended' => $extended);
            $personnelContractID = igfae_intranet_insert('PersonnelContract', $data);

            if($personnelContractID > 0){

                $economicActivityCodes = explode(" ; ", $item['economicActivityCode']);
                $where = array('PersonnelContractID' => $personnelContractID);

                // Insertamos en la tabla relacionada
                foreach ($economicActivityCodes as $item) {
                    if($item != ""){
                        $data = array('PersonnelContractID' => $personnelContractID, 'economicActivityCode' => $item);
                        igfae_intranet_insert('ContractEconomicActivityCode', $data);
                    }
                }  
            }          
        }
    }
}
function insertPersonnelSocialNetworks($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('SocialNetworkID' => $item['socialNetwork'], 'SocialID' => $item['link'], 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelSocialNetwork', $data);
        }
    }
}
function insertPersonnelExperimental($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $time_input = strtotime($item['startDate']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $endDate = NULL;
            if (isset($item['endDate']) && $item['endDate'] != "") {
                $time_input = strtotime($item['endDate']);
                $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            }
            $data = array('ExperimentalCollaborationID' => $item['firstColumn'], 'StartDate' => $startDate, 'EndDate' => $endDate, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelExperiment', $data);
        }
    }
}
function insertPersonnelExternalSource($id, $elements, $sourceID)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('Code' => $item, 'PersonnelID' => $id, 'SourceID' => $sourceID);
            igfae_intranet_insert('PersonnelExternalSource', $data);
        }
    }
}
function insertTeamReference($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('Name' => $item, 'PersonnelID' => $id);
            igfae_intranet_insert('TeamReference', $data);
        }
    }
}
function insertPersonnelReference($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('SupervisorID' => $item, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelReference', $data);
        }
    }
}
function insertScientificFirm($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('Firm' => $item, 'PersonnelID' => $id);
            igfae_intranet_insert('ScientificFirm', $data);
        }
    }
}

function insertPersonnelCategory($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $time_input = strtotime($item['startDate']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $endDate = NULL;
            if (isset($item['endDate']) && $item['endDate'] != "") {
                $time_input = strtotime($item['endDate']);
                $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            }
            $data = array('CategoryID' => $item['firstColumn'], 'StartDate' => $startDate, 'EndDate' => $endDate, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelCategory', $data);
        }
    }
}

function insertPersonnelDepartments($id, $departmentID)
{
    if (isset($id)  && $id > 0 && isset($departmentID)) {
        foreach ($departmentID as $item) {
            $data = array('DepartmentID' => $item, 'PersonnelID' => $id);
            igfae_intranet_insert('PersonnelDepartment', $data);
        }
    }
}

function insertUserWP($id, $user)
{
    $wpUserID = "";
    $editUser = false;
    $user_id = get_current_user_id();
    if ($user->getWpUserId() !== null && $user->getWpUserId() != "") {
        // Edición de usuario
        $wpUserID = $user->getWpUserId();
        $editUser = true;
        // Eliminamos los roles que tuviese y añadimos los nuevos, 
        // solo para el rol Gestion
        
        if (igfae_user_can($user_id, IGFAE_INTRANET_CAPACIBILITY_USERS_UPDATE_ROLES)) {
            $userWP = new WP_User($wpUserID);
            //Por defecto al crear el usuario sin rol establecido añade subscriber
            if (!$editUser) {
                $userWP->remove_role(IGFAE_INTRANET_ROLE_SUBSCRIBER);
            }
            $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_GESTION);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_COLABORADOR);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_EDITOR_DNI);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_MONITOR);
            $userWP->remove_role(IGFAE_INTRANET_ROLE_VALIDADOR);
            if ($user->getRol() != null) {
                //añadimos los que se han seleccionado        
                foreach ($user->getRol() as $item) {
                    $userWP->add_role($item);
                }
            }
        }
    } else {
        //Usuario nuevo        
        file_put_contents(ABSPATH."/usercreation.log","insertUserwP: user data: ".print_r($user,true)."\n",FILE_APPEND);
        $WP_array = array(
            'user_login'    =>  explode("@", $user->getEmail())[0],
            'user_email'    =>  $user->getEmail(),
            'first_name'    =>  $user->getName(),
            'user_pass'     =>  wp_generate_password(20, true)

        );

        file_put_contents(ABSPATH."/usercreation.log","insertUserwP: wordpress array data: ".print_r($WP_array,true)."\n",FILE_APPEND);

        $wpUserID = wp_insert_user($WP_array);
        file_put_contents(ABSPATH."/usercreation.log","insertUserwP: userID: ".print_r($wpUserID,true)."\n",FILE_APPEND);

        //Enviamos mail para resetear la contraseña
        igfae_send_password_reset_mail($wpUserID);
        if (isset($wpUserID)) {
            // Actualizamos el valor de WPUserID del usuario
            $datosInsert['WPUserID'] = $wpUserID;

            $where = array('PersonnelID' => $id);

            igfae_intranet_update("Personnel", $datosInsert, $where, null, null);
        }
        // Eliminamos los roles que tuviese y añadimos los nuevos,
        $userWP = new WP_User($wpUserID);
        
        //Por defecto al crear el usuario sin rol establecido añade subscriber
        if (!$editUser) {
            $userWP->remove_role(IGFAE_INTRANET_ROLE_SUBSCRIBER);
        }
        $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_GESTION);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_COLABORADOR);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_EDITOR_DNI);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_MONITOR);
        $userWP->remove_role(IGFAE_INTRANET_ROLE_VALIDADOR);
        if ($user->getRol() != null) {
            //añadimos los que se han seleccionado        
            foreach ($user->getRol() as $item) {
                $userWP->add_role($item);
            }
        }
    }

    return $wpUserID;
}
function insertBuilding($item)
{
    if (isset($item)) {
        $where = "";
        $params = [];

        if ($item) {
            $where .= " AND b.BuildingID =  %s";
            $params[] = $item;
        }
        $sql = "SELECT b.BuildingID as 'id' 
        FROM Building b
        WHERE 1=1 
        {$where} ";

        $rows = igfae_intranet_fetch_data($sql, $params);
        $resultArray = array();

        foreach ($rows['rows'] as $row) {
            $resultArray[] = array("id" => $row->id);
        }
        if (count($resultArray) == 0) {
            // Insertamos el nuevo edificio
            $data = array('Building' => $item);
            $idBuilding = igfae_intranet_insert('Building', $data);
        } else {
            $idBuilding = $item;
        }
        return $idBuilding;
    }
}
function insertRoom($buildingId, $item)
{

    $idLocation = NULL;
    if (isset($item) && isset($buildingId)) {
        $where = "";
        $params = [];

        if ($item) {
            $where .= " AND l.LocationID =  %s";
            $params[] = $item;
        }
        $sql = "SELECT l.LocationID as 'id' 
        FROM Location l
        WHERE 1=1 
        {$where} ";

        $rows = igfae_intranet_fetch_data($sql, $params);
        $resultArray = array();

        foreach ($rows['rows'] as $row) {
            $resultArray[] = array("id" => $row->id);
        }
        $idLocation = NULL;
        if (count($resultArray) == 0) {
            //Insertamos el nuevo despacho
            $data = array('Room' => $item, 'BuildingID' => $buildingId);
            $idLocation = igfae_intranet_insert('Location', $data);
        } else {
            $idLocation = $item;
        }
    }
    return $idLocation;
}
// Delete relations user
function deleteRelationsUser($id)
{
    $where = array('PersonnelID' => $id);

    //Borramos las tablas relacionadas
    igfae_intranet_delete('PersonnelWorkLine', $where);
    igfae_intranet_delete('PersonnelResearchArea', $where);
    igfae_intranet_delete('PersonnelResearchGroup', $where);
    igfae_intranet_delete('PersonnelResearchProgram', $where);
    igfae_intranet_delete('PersonnelExperiment', $where);
    igfae_intranet_delete('PersonnelExternalSource', $where);
    igfae_intranet_delete('PersonnelReference', $where);
    igfae_intranet_delete('PersonnelContract', $where);
    igfae_intranet_delete('TeamReference', $where);
    igfae_intranet_delete('ScientificFirm', $where);
    igfae_intranet_delete('PersonnelSocialNetwork', $where);
    igfae_intranet_delete('PersonnelCategory', $where);
    igfae_intranet_delete('PersonnelDepartment', $where);
}

function getUserByIDCard($user)
{
    $where = "";
    $params = [];

    if ($user->getIdCard()) {
        $where .= " AND p.IDCard =  %s";
        $params[] = $user->getIdCard();
    }
    if ($user->getPersonnelId()) {
        $where .= " AND p.PersonnelID NOT IN (%d)";
        $params[] = $user->getPersonnelId();
    }
    $sql = "SELECT distinct
	p.PersonnelID as 'id'
    FROM Personnel p 
        WHERE 1=1 
        {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

function getUserByWPUserID($user_id)
{
    $where = "";
    $params = [];

    if ($user_id) {
        $where .= " AND p.	WPUserID =  %s";
        $params[] = $user_id;
    }

    $sql = "SELECT distinct
	p.PersonnelID as 'id'
    FROM Personnel p 
        WHERE 1=1 
        {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return $resultArray;
}

function getUserByEmail($user)
{
    $where = "";
    $params = [];

    if ($user->getEmail()) {
        $where .= " AND p.EMail =  %s";
        $params[] = $user->getEmail();
    }
    if ($user->getPersonnelId()) {
        $where .= " AND p.PersonnelID NOT IN (%d)";
        $params[] = $user->getPersonnelId();
    }
    $sql = "SELECT distinct
	p.PersonnelID as 'id'
    FROM Personnel p 
        WHERE 1=1 
        {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

function existsUser($userId)
{
    $where = "";
    $params = [];

    $where .= " AND p.PersonnelID = %d";
    $params[] = $userId;

    $sql = "SELECT distinct
	p.PersonnelID as 'id'
    FROM Personnel p 
        WHERE 1=1 {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}


function isPersonnelValidated($user_id)
{
    $params[] = $user_id;

    $sql = "SELECT p.Validated
            FROM Personnel p 
            WHERE p.WPUserID = %s";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = '0';

    foreach ($rows['rows'] as $row) {
        $result = $row->Validated;
    }
    return $result;
}

/**
 * Indica si el usuario está relacionado con el usuario.
 * @param: $user_id: user
 * @param: $id: id del usuario
 * @param: $mode: modo de búsqueda
 * Si modo == 1 solo si el usuario está relacionado. 
 * Si modo == 2 comprueba si el usuario está relacionado o es el creador
 */
function hasRelatedUser($user_id, $id, $mode = 2)
{

    $sqlHasRelation = "select count(*)
        from Personnel p 
        left join PersonnelReference pr
        on pr.PersonnelID = p.PersonnelID ";


    $sqlHasRelation .= " where 1=1 ";

    if ($mode == 1) {
        $sqlHasRelation .=
            "and (pr.SupervisorID in ($user_id) or p.PersonnelID = $user_id)  ";
    } else {
        $sqlHasRelation .= "
        and (pr.SupervisorID in ($user_id) or p.PersonnelID = $user_id or p.CreatedBy = $user_id) ";
    }

    $sqlHasRelation .= "
     and p.PersonnelID =  $id
    ;";


    $result = igfae_intranet_get_var($sqlHasRelation);

    if ($result) {
        return true;
    } else {
        return false;
    }
}

function isUserCreatorOrRelatedWithUser($user_id)
{

    $idUser = $_GET['idUser'];

    if (!isset($idUser) || !isset($user_id)) {
        return false;
    }

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedUser($id, $idUser)) {
            return true;
        }
    }

    return false;
}

function isUserInfo($user_id)
{
    $idUser = $_GET['idUser'];

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if ($id == $idUser) {
            return true;
        }
    }

    return false;
}
//Comprobamos si esta validado el usuario
function isUserConsolidated($id) { 
    $sql = "SELECT count(*) 
    from Personnel p
    where
    p.Validated = 1
    and p.PersonnelID = $id";
    
    $result = igfae_intranet_get_var($sql);
    
    if ($result) {
    return true;
    } else {
    return false;
    }
}

function getSelectEconomicActivityCode(){
    $sql = "select DISTINCT USCProjectReference as 'uscProjectReference'
            from Project 
            where 1=1
            order by USCProjectReference desc;";
    
    $rows = igfae_intranet_fetch_data($sql);
    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] =  $row->uscProjectReference;
    }

    return $result;
}
