<?php

/**
 * Get types activities
 */
function getTypesActivities()
{
    global $wp_query;
    $activity = "";
    $typesActivities = getSelectTypesActivities();
    $typesActivitiesSelect = NULL;
    $typeActivitySelected = NULL;

    if (isset($wp_query->query_vars['typeActivity'])) {
        $activity = sanitize_title(urldecode($wp_query->query_vars['typeActivity']));
    }
    foreach ($typesActivities as $key => $item) {
        $typesActivitiesSelect[$key] = $item;
        if ($activity == sanitize_title($item->getActivityType())) {
            $typeActivitySelected[$key] = $item;
        }
    }
    $typesActivitiesSelect['items'] = $typesActivitiesSelect;
    $typesActivitiesSelect['selected'] = $typeActivitySelected;
    return $typesActivitiesSelect;
}

/**
 * Get research areas
 */
function getResearchAreas()
{
    global $wp_query;
    // $researchAreasSelect = ['all' => __('All', 'ifgae-content-viewer')];
    $researchAreas = getSelectResearchAreas();
    $researchArea = "";
    $researchAreaSelected = NULL;
    $researchAreasSelect = NULL;

    if (isset($wp_query->query_vars['researchArea'])) {
        $researchArea = sanitize_title(urldecode($wp_query->query_vars['researchArea']));
    }
    foreach ($researchAreas as $key => $item) {
        $researchAreasSelect[$key] = $item;
        if ($researchArea == sanitize_title($item->getResearchAreaName())) {
            $researchAreaSelected[$key] = $item;
        }
    }
    $researchAreasSelect['items'] = $researchAreasSelect;
    $researchAreasSelect['selected'] = $researchAreaSelected;
    return $researchAreasSelect;
}
/**
 * Get thesis types
 */
function getThesisTypes()
{
    global $wp_query;
    // $thesisTypesSelect = ['all' => __('All', 'ifgae-content-viewer')];
    $thesisTypes = getSelectThesisTypes();
    $thesisType = "";
    $thesisTypesSelect = NULL;
    $thesisTypeSelected = NULL;


    if (isset($wp_query->query_vars['thesisType'])) {
        $thesisType = sanitize_title(urldecode($wp_query->query_vars['thesisType']));
    }
    foreach ($thesisTypes as $key => $item) {
        $thesisTypesSelect[$key] = $item;
        if ($thesisType == sanitize_title($item->getThesisType())) {
            $thesisTypeSelected[$key] = $item;
        }
    }
    $thesisTypesSelect['items'] = $thesisTypesSelect;
    $thesisTypesSelect['selected'] = $thesisTypeSelected;
    return $thesisTypesSelect;
}
/**
 * Get research programs
 */
function getResearchProgram()
{
    global $wp_query;
    // $researchProgramSelect = ['all' => __('All', 'ifgae-content-viewer')];
    $researchPrograms = getSelectResearchPrograms();
    $researchProgram = "";
    $researchProgramSelect = NULL;
    $researchProgramSelected = NULL;


    if (isset($wp_query->query_vars['researchProgram'])) {
        $researchProgram = sanitize_title(urldecode($wp_query->query_vars['researchProgram']));
    }
    foreach ($researchPrograms as $key => $item) {
        $researchProgramSelect[$key] = $item;
        if ($researchProgram == sanitize_title($item->getResearchProgramName())) {
            $researchProgramSelected[$key] = $item;
        }
    }
    $researchProgramSelect['items'] = $researchProgramSelect;
    $researchProgramSelect['selected'] = $researchProgramSelected;
    return $researchProgramSelect;
}

/**
 * Get research groups
 */
function getResearchGroup()
{
    global $wp_query;
    // $researchGroupSelect = ['all' => __('All', 'ifgae-content-viewer')];
    $researchGroups = getSelectResearchGroups();
    $researchGroup = "";
    $researchGroupSelected = NULL;
    $researchGroupSelect = NULL;

    if (isset($wp_query->query_vars['researchGroup'])) {
        $researchGroup = sanitize_title(urldecode($wp_query->query_vars['researchGroup']));
    }
    foreach ($researchGroups as $key => $item) {
        $researchGroupSelect[$key] = $item;
        if ($researchGroup == sanitize_title($item->getResearchGroupName())) {
            $researchGroupSelected[$key] = $item;
        }
    }
    $researchGroupSelect['items'] = $researchGroupSelect;
    $researchGroupSelect['selected'] = $researchGroupSelected;
    return $researchGroupSelect;
}
/**
 * Get experimentals collaboration
 */
function getExperimentalCollaborations()
{
    global $wp_query;
    $experimentalCollaborations = getSelectExperimentalCollaboration();
    $experimentalCollaboration = "";
    $experimentalCollaborationSelected = NULL;
    $experimentalCollaborationSelect = NULL;

    if (isset($wp_query->query_vars['collaborationExperiment'])) {
        $experimentalCollaboration = sanitize_title(urldecode($wp_query->query_vars['collaborationExperiment']));
    }
    foreach ($experimentalCollaborations as $key => $item) {
        $experimentalCollaborationSelect[$key] = $item;
        if ($experimentalCollaboration == sanitize_title($item->getName())) {
            $experimentalCollaborationSelected[$key] = $item;
        }
    }
    $experimentalCollaborationSelect['items'] = $experimentalCollaborationSelect;
    $experimentalCollaborationSelect['selected'] = $experimentalCollaborationSelected;
    return $experimentalCollaborationSelect;
}



/**
 * Get personnels
 */
function getPersonnel()
{
    global $wp_query;
    $directors = getSelectPersonnel();
    $director = "";
    $directorSelected = NULL;
    $directorSelect = NULL;

    if (isset($wp_query->query_vars['thesisDirector'])) {
        $director = sanitize_title(urldecode($wp_query->query_vars['thesisDirector']));
    }
    foreach ($directors as $key => $item) {
        $directorSelect[$key] = $item;
        if ($director == sanitize_title($item->getName())) {
            $directorSelected[$key] = $item;
        }
    }
    $directorSelect['items'] = $directorSelect;
    $directorSelect['selected'] = $directorSelected;
    return $directorSelect;
}



/**
 * Get externals editorials
 */
function getExternalDirectors()
{
    global $wp_query;
    $externalDirectors = getSelectExternalDirectors();
    $externalDirector = "";
    $externalDirectorSelected = NULL;
    $externalDirectorSelect = NULL;

    if (isset($wp_query->query_vars['externalThesisDirector'])) {
        $externalDirector = sanitize_title(urldecode($wp_query->query_vars['externalThesisDirector']));
    }
    $externalDirectorSelect = [];
    $externalDirectorSelected = [];
    foreach ($externalDirectors as $key => $item) {
        $externalDirectorSelect[$key] = $item;
        if ($externalDirector == sanitize_title($item->getName())) {
            $externalDirectorSelected[$key] = $item;
        }
    }
    $externalDirectorSelect['items'] = $externalDirectorSelect;
    $externalDirectorSelect['selected'] = $externalDirectorSelected;
    return $externalDirectorSelect;
}


// Select external authors
add_action("wp_ajax_search_external_authors", "api_search_external_authors");
add_action("wp_ajax_nopriv_search_external_authors", "api_search_external_authors");
function api_search_external_authors()
{

    // Search External authors
    $q = $_GET["q"];
    $page = NULL;
    if (isset($_GET["page"]) && $_GET["page"] != null) {
        $page = $_GET["page"];
    }
    $data = getSelectExternalAuthors($q, $page);
    echo json_encode($data);

    exit();
}

// Select journals
add_action("wp_ajax_search_productionProject_journals", "api_search_productionProject_journals");
add_action("wp_ajax_nopriv_search_productionProject_journals", "api_search_productionProject_journals");
function api_search_productionProject_journals()
{
    $q = $_GET["q"];
    $page = NULL;
    if (isset($_GET["page"]) && $_GET["page"] != null) {
        $page = $_GET["page"];
    }
    // Search Journals    
    $data = getSelectJournals($q, $page);
    echo json_encode($data);

    exit();
}

// Select journal
add_action("wp_ajax_api_get_journal", "api_get_journal");
add_action("wp_ajax_nopriv_api_get_journal", "api_get_journal");
function api_get_journal()
{
    $id = $_GET["id"];
    // Search Journal   
    $data = getJournal($id);
    echo json_encode($data);

    exit();
}

// Select keywords
add_action("wp_ajax_search_productionProject_keywords", "api_search_productionProject_keywords");
add_action("wp_ajax_nopriv_search_productionProject_keywords", "api_search_productionProject_keywords");
function api_search_productionProject_keywords()
{

    $q = $_GET["q"];
    $page = NULL;
    if (isset($_GET["page"]) && $_GET["page"] != null) {
        $page = $_GET["page"];
    }
    // Search keywords 
    $data = getSelectKeywords($q, $page);
    echo json_encode($data);

    exit();
}

// Select financing agencies
add_action("wp_ajax_search_productionProject_financingAgencies", "api_search_productionProject_financingAgencies");
add_action("wp_ajax_nopriv_search_productionProject_financingAgencies", "api_search_productionProject_financingAgencies");
function api_search_productionProject_financingAgencies()
{

    $q = $_GET["q"];
    $page = NULL;
    if (isset($_GET["page"]) && $_GET["page"] != null) {
        $page = $_GET["page"];
    }
    // Search keywords 
    $data = getSelectSearchFinancingAgencies($q, $page);
    echo json_encode($data);

    exit();
}

// Select Research project
add_action("wp_ajax_search_productionProject_researchProject", "api_search_productionProject_researchProject");
add_action("wp_ajax_nopriv_search_productionProject_researchProject", "api_search_productionProject_researchProject");
function api_search_productionProject_researchProject()
{
    $q = $_GET["q"];
    $page = NULL;
    if (isset($_GET["page"]) && $_GET["page"] != null) {
        $page = $_GET["page"];
    }
    // Search researchProject    
    $data = getSelectResearchProjects($q, $page);
    echo json_encode($data);

    exit();
}

/**
 * Get worklines
 */
function getWorkLines()
{
    global $wp_query;
    $worklines = getSelectWorkline();
    $workline = "";
    $workLineSelected = NULL;
    $workLineSelect = NULL;

    if (isset($wp_query->query_vars['workline'])) {
        $workline = sanitize_title(urldecode($wp_query->query_vars['workline']));
    }
    foreach ($worklines as $workLi => $item) {
        $workLineSelect[$workLi] = $item;
        if ($workline == sanitize_title($item->getWorklineName())) {
            $workLineSelected[$workLi] = $item;
        }
    }
    $workLineSelect['items'] = $workLineSelect;
    $workLineSelect['selected'] = $workLineSelected;
    return $workLineSelect;
}

// Select Link to production project project
add_action("wp_ajax_search_link_productionProject", "api_search_link_productionProject");
add_action("wp_ajax_nopriv_search_link_productionProject", "api_search_link_productionProject");
function api_search_link_productionProject()
{
    $q = $_GET["q"];
    $id = isset($_GET["idProject"]) ? $_GET["idProject"] : NULL;
    $page = NULL;
    if (isset($_GET["page"]) && $_GET["page"] != null) {
        $page = $_GET["page"];
    }
    // Search vinculations to another production project    
    $data = getSelectVinculations($id, $q, $page);
    echo json_encode($data);

    exit();
}
function getListYears()
{
    $actualYear = date(IGFAE_INTRANET_DATE_FORMAT_Y);
    $result = [];
    $position = 0;
    $result[$position] = $actualYear;
    $position ++;
    $maxYearsPast = IGFAE_INTRANET_NUMBER_ELEMENTS_SELECT_YEARS;
    $maxYearsFuture = IGFAE_INTRANET_NUMBER_ELEMENTS_SELECT_YEARS_FUTURE;
    for ($id = 1; $id <= $maxYearsPast; $id++) {
        $result[$position] = $actualYear - $id;
        $position++;
    }

    for ($id = 1; $id <= $maxYearsFuture; $id++) {
        $result[$position] = $actualYear + $id;
        $position++;
    }

    return $result;
}

function getYearsSelect()
{
    $listYears = getListYears();
    $annualGifted = "";
    $annualGiftedSelected = NULL;
    $annualGiftedsSelect = NULL;

    if (isset($wp_query->query_vars['annualGifted'])) {
        $annualGifted = sanitize_title(urldecode($wp_query->query_vars['annualGifted']));
    }
    foreach ($listYears as $key => $item) {
        $annualGiftedsSelect[$key] = $item;
        if ($annualGifted == sanitize_title($item)) {
            $annualGiftedSelected[$key] = $item;
        }
    }
    $annualGiftedsSelect['selected'] = $annualGiftedSelected;
    return $annualGiftedsSelect;
}

/**
 * Get prototypes states
 */
function getPrototypesStates()
{
    global $wp_query;
    $state = "";
    $prototypeStatesSelect = NULL;
    $prototypeStatesSelected = NULL;
    $prototypeStates = getSelectPrototypeState();

    if (isset($wp_query->query_vars['prototypeStates'])) {
        $state = sanitize_title(urldecode($wp_query->query_vars['prototypeStates']));
    }
    foreach ($prototypeStates as $key => $item) {
        $prototypeStatesSelect[$key] = $item;
        if ($state == sanitize_title($item->getPrototypeStateName())) {
            $prototypeStatesSelected[$key] = $item;
        }
    }
    $prototypeStatesSelect['items'] = $prototypeStatesSelect;
    $prototypeStatesSelect['selected'] = $prototypeStatesSelected;
    return $prototypeStatesSelect;
}

/**
 * Get funding agencies
 */
function getFundingAgencies()
{
    global $wp_query;
    $fundingAgency = "";
    $fundingAgencies = getSelectFundingAgencies();
    $fundingAgenciesSelect = NULL;
    $fundingAgencySelected = NULL;

    if (isset($wp_query->query_vars['fundingAgency'])) {
        $fundingAgency = sanitize_title(urldecode($wp_query->query_vars['fundingAgency']));
    }
    foreach ($fundingAgencies as $key => $item) {
        $fundingAgenciesSelect[$key] = $item;
        if ($fundingAgency == sanitize_title($item->getFundingAgency())) {
            $fundingAgencySelected[$key] = $item;
        }
    }
    $fundingAgenciesSelect['items'] = $fundingAgenciesSelect;
    $fundingAgenciesSelect['selected'] = $fundingAgencySelected;
    return $fundingAgenciesSelect;
}

/**
 * Formatea las fechas MM/DD/YYYY
 */
function formatDate($date)
{
    if (isset($date) && $date != IGFAE_INTRANET_DATE_INIT  && $date != IGFAE_INTRANET_DATE_INIT_H_I_S) {
        $fecha = date_create_from_format(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $date);
        if (!$fecha) {
            //Comprobamos el otro formato
            $fecha = date_create_from_format(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S, $date);
        }
        return date_format($fecha, IGFAE_INTRANET_DATE_FORMAT_M_D_Y);
    }
}

/**
 * Formatea las fechas MM/DD/YYYY HH:MM
 */
function formatDateWithHour($date)
{
    if (isset($date) && $date != IGFAE_INTRANET_DATE_INIT && $date != IGFAE_INTRANET_DATE_INIT_H_I_S) {
        $fecha = date_create_from_format(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S, $date);
        return date_format($fecha, IGFAE_INTRANET_DATE_FORMAT_M_D_Y_H_M);
    }
}
/**
 * Formatea las fechas HH:MM
 */
function formatHour($date)
{
    if (isset($date) && $date != IGFAE_INTRANET_DATE_INIT && $date != IGFAE_INTRANET_DATE_INIT_H_I_S) {
        $fecha = date_create_from_format(IGFAE_INTRANET_DATE_FORMAT_H_I_S, $date);
        return date_format($fecha, IGFAE_INTRANET_DATE_FORMAT_H_I);
    }
}

/**
 * Formatea un valor numerico con 4 decimales y sin separacion de miles
 */
function formatNumber($number)
{
    $number = str_replace(",", "", $number);

    return number_format($number, IGFAE_INTRANET_NUMBER_FORMAT_DECIMAL_4, '.', '');
}

/**
 * Formatea un valor numerico con 4 decimales y con separacion de miles
 */
function formatNumberMiles($number)
{
    $number = str_replace(",", "", $number);

    return number_format($number, IGFAE_INTRANET_NUMBER_FORMAT_DECIMAL_4, ',', '.');
}

/**
 * Formatea un valor numerico con 4 decimales y con separacion de miles
 */
function formatNumber2Miles($number)
{
    $number = str_replace(",", "", $number);

    return number_format($number, IGFAE_INTRANET_NUMBER_FORMAT_DECIMAL_2, ',', '.');
}

/**
 * Formatea un valor numerico con separacion de miles
 */
function formatNumberOnlyMiles($number)
{
    $number = str_replace(",", "", $number);

    return number_format($number, IGFAE_INTRANET_NUMBER_ZERO, '', '.');
}

function notFormatNumber($number)
{
    $number = str_replace(".", "", $number);

    return $number;
}

/**
 * Formatea un valor numerico con 4 decimales y con separacion de miles a sin separador de miles
 */
function formatNumberToBD($number)
{    
    $number = str_replace(".", "", $number);
    $number = str_replace(",", ".", $number);

    return $number;
}

/**
 * Get institutions
 */
function getInstitutions()
{
    global $wp_query;
    $institutions = getSelectInstitutions();
    $institution = "";
    $institutionSelect = NULL;
    $institutionSelected = NULL;


    if (isset($wp_query->query_vars['institution'])) {
        $institution = sanitize_title(urldecode($wp_query->query_vars['institution']));
    }
    foreach ($institutions as $key => $item) {
        $institutionSelect[$key] = $item;
        if ($institution == sanitize_title($item->getInstitutionName())) {
            $institutionSelected[$key] = $item;
        }
    }
    $institutionSelect['items'] = $institutionSelect;
    $institutionSelect['selected'] = $institutionSelected;
    return $institutionSelect;
}

/**
 * Get transfer type
 */
function getTransferType()
{
    global $wp_query;
    $transferTypes = getSelectTransferType();
    $transferType = "";
    $transferTypeSelect = NULL;
    $transferTypeSelected = NULL;


    if (isset($wp_query->query_vars['transferType'])) {
        $transferType = sanitize_title(urldecode($wp_query->query_vars['transferType']));
    }
    foreach ($transferTypes as $key => $item) {
        $transferTypeSelect[$key] = $item;
        if ($transferType == sanitize_title($item->getTransferName())) {
            $transferTypeSelected[$key] = $item;
        }
    }
    $transferTypeSelect['items'] = $transferTypeSelect;
    $transferTypeSelect['selected'] = $transferTypeSelected;
    return $transferTypeSelect;
}

/**
 * Get TRL type
 */
function getTRLType()
{
    global $wp_query;
    $trlTypes = getSelectTRLType();
    $trlType = "";
    $trlTypeSelect = NULL;
    $trlTypeSelected = NULL;


    if (isset($wp_query->query_vars['trlType'])) {
        $trlType = sanitize_title(urldecode($wp_query->query_vars['trlType']));
    }
    foreach ($trlTypes as $key => $item) {
        $trlTypeSelect[$key] = $item;
        if ($trlType == sanitize_title($item->getTrlName())) {
            $trlTypeSelected[$key] = $item;
        }
    }
    $trlTypeSelect['items'] = $trlTypeSelect;
    $trlTypeSelect['selected'] = $trlTypeSelected;
    return $trlTypeSelect;
}


function getTransferSubtype($transferType)
{
    global $wp_query;
    $subTransfers = getSelectTransferSubtype($transferType);
    $subTransfer = "";
    $subTransfersSelect = NULL;
    $subTransferSelect = NULL;

    if (isset($wp_query->query_vars['transferSubType'])) {
        $subTransfer = sanitize_title(urldecode($wp_query->query_vars['transferSubType']));
    }

    foreach ($subTransfers as $key => $item) {
        $subTransfersSelect[$key] = $item;
        if ($subTransfer == sanitize_title($item->getTransferSubTypeName())) {
            $subTransferSelect[$key] = $item;
        }
    }
    $subTransfersSelect['items'] = $subTransfersSelect;
    $subTransfersSelect['selected'] = $subTransferSelect;

    return $subTransfersSelect;
}

// Change value new Transfer type
add_action("wp_ajax_load_dates_subType", "api_getSubType");
add_action("wp_ajax_nopriv_load_dates_subType", "api_getSubType");
function api_getSubType()
{
    $transferType = $_POST['transferType'];
    $transferSubTypeSelect = getTransferSubtype($transferType[0]);
    wp_send_json_success($transferSubTypeSelect);
    exit();
}

function getTransferSubTypeEdit($transferType)
{

    $transferSubTypeSelect = getTransferSubtype($transferType);
    return $transferSubTypeSelect;
}

//Funcion que comprueba si existen keywords
function existKeywords($keywords)
{
    $where = "";
    $params = [];
    foreach ($keywords as $item) {
        $where = "";
        $params = [];
        $where .= " AND ( UPPER(TRIM(Name)) =  %s)";
        $params[] =  strtoupper(trim($item));

        $sql = "SELECT Name as 'name'
        FROM Keyword 
            WHERE 1=1 
            {$where} ";
        $rows = igfae_intranet_fetch_data($sql, $params);
        $resultArray = array();

        foreach ($rows['rows'] as $row) {
            $resultArray[] = array("name" => $row->name);
        }
        if (count($resultArray) == 0) {
            // Insertamos la nueva keyword
            $sql = "INSERT INTO Keyword(
            Name)
            VALUES
            (            
            '$item'                    
            )";
            igfae_intranet_save($sql);
        }
    }
}

//Funcion que comprueba si existen worklines
function existWorkLines($item)
{
    $where = "";
    $params = [];
    $where .= " AND WorkLineID =  %d ";
    $params[] =  $item;

    $sql = "SELECT Name as 'name'
        FROM WorkLine 
            WHERE 1=1 
            {$where} ";

    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("name" => $row->name);
    }

    return $resultArray;
}

function insertWorkLines($item, $id)
{

    $data = array('WorkLineID' => $id, 'Name' => $item);
    igfae_intranet_insert('WorkLine', $data);
}


/**
 * Get sources
 */
function getExternalSources()
{
    global $wp_query;
    $sources = getSources();

    foreach ($sources as $key => $item) {
        $sourcesSelect[$key] = $item;
    }
    $sourcesSelect['items'] = $sources;
    return $sourcesSelect;
}

function verificarValidarInformacion($validated, $published) {
    return ($published == 1 && $validated == 1) || $published == 0;
}

// Select keywords
add_action("wp_ajax_api_get_keyword", "api_get_keyword");
add_action("wp_ajax_nopriv_api_get_keyword", "api_get_keyword");
function api_get_keyword()
{
    $q = $_GET["id"];
    // Search keyword   
    $data = getKeyword($q);
    echo json_encode($data);

    exit();
}

add_action("wp_ajax_api_get_ExternalAuthor", "api_get_ExternalAuthor");
add_action("wp_ajax_nopriv_api_get_ExternalAuthor", "api_get_ExternalAuthor");
function api_get_ExternalAuthor()
{
    $id = $_GET["id"]; 
    $data = getExternalAuthor($id);
    echo json_encode($data);

    exit();
}

add_action("wp_ajax_api_get_ResearchProject", "api_get_ResearchProject");
add_action("wp_ajax_nopriv_api_get_ResearchProject", "api_get_ResearchProject");
function api_get_ResearchProject()
{
    $id = $_GET["id"]; 
    $data = getResearchProjectAsinc($id);
    echo json_encode($data);

    exit();
}

/**
 * Get equipments family
 */
function getEquipmentFamily()
{
    global $wp_query;
    $equipmentFamilies = getSelectEquipmentFamily();
    $equipmentFamily = "";
    $equipmentFamilySelect = NULL;
    $equipmentFamilySelected = NULL;


    if (isset($wp_query->query_vars['equipmentFamily'])) {
        $equipmentFamily = sanitize_title(urldecode($wp_query->query_vars['equipmentFamily']));
    }
    foreach ($equipmentFamilies as $key => $item) {
        $equipmentFamilySelect[$key] = $item;
        if ($equipmentFamily == sanitize_title($item->getEquipmentFamilyName())) {
            $equipmentFamilySelected[$key] = $item;
        }
    }
    $equipmentFamilySelect['items'] = $equipmentFamilySelect;
    $equipmentFamilySelect['selected'] = $equipmentFamilySelected;
    return $equipmentFamilySelect;

}


/**
 * Get equipments laboratory
 */
function getEquipmentLaboratory()
{
    global $wp_query;
    $equipmentLaboratories = getSelectEquipmentLaboratory();
    $equipmentLaboratory = "";
    $equipmentLaboratorySelect = NULL;
    $equipmentLaboratorySelected = NULL;


    if (isset($wp_query->query_vars['equipmentLaboratory'])) {
        $equipmentLaboratory = sanitize_title(urldecode($wp_query->query_vars['equipmentLaboratory']));
    }
    foreach ($equipmentLaboratories as $key => $item) {
        $equipmentLaboratorySelect[$key] = $item;
        if ($equipmentLaboratory == sanitize_title($item->getEquipmentLaboratoryName())) {
            $equipmentLaboratorySelected[$key] = $item;
        }
    }
    $equipmentLaboratorySelect['items'] = $equipmentLaboratorySelect;
    $equipmentLaboratorySelect['selected'] = $equipmentLaboratorySelected;
    return $equipmentLaboratorySelect;

}


/**
 * Get equipments technical contact
 */
function getEquipmentTechnicalContact()
{
    global $wp_query;
    $equipmentTechnicalContacts = getSelectEquipmentTechnicalContact();
    $equipmentTechnicalContact = "";
    $equipmentTechnicalContactSelect = NULL;
    $equipmentTechnicalContactSelected = NULL;


    if (isset($wp_query->query_vars['equipmentTechnicalContact'])) {
        $equipmentTechnicalContact = sanitize_title(urldecode($wp_query->query_vars['equipmentTechnicalContact']));
    }
    foreach ($equipmentTechnicalContacts as $key => $item) {
        $equipmentTechnicalContactSelect[$key] = $item;
        if ($equipmentTechnicalContact == sanitize_title($item->getEquipmentTechnicalContactName())) {
            $equipmentTechnicalContactSelected[$key] = $item;
        }
    }
    $equipmentTechnicalContactSelect['items'] = $equipmentTechnicalContactSelect;
    $equipmentTechnicalContactSelect['selected'] = $equipmentTechnicalContactSelected;
    return $equipmentTechnicalContactSelect;

}


/**
 * Get equipments responsable of family
 */
function getEquipmentResponsableFamily()
{
    global $wp_query;
    $equipmentResponsableFamilies = getSelectEquipmentResponsableFamily();
    $equipmentResponsableFamily = "";
    $equipmentResponsableFamilySelect = NULL;
    $equipmentResponsableFamilySelected = NULL;

    if (isset($wp_query->query_vars['equipmentResponsableFamily'])) {
        $equipmentResponsableFamily = sanitize_title(urldecode($wp_query->query_vars['equipmentResponsableFamily']));
    }
    foreach ($equipmentResponsableFamilies as $key => $item) {
        $equipmentResponsableFamilySelect[$key] = $item;
        if ($equipmentResponsableFamily == sanitize_title($item->getEquipmentResponsableFamilyName())) {
            $equipmentResponsableFamilySelected[$key] = $item;
        }
    }

    $equipmentResponsableFamilySelect['items'] = $equipmentResponsableFamilySelect;
    $equipmentResponsableFamilySelect['selected'] = $equipmentResponsableFamilySelected;
    return $equipmentResponsableFamilySelect;

}


