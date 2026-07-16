<?php

/**
 * Get ambit o scopes
 */
function getScopesProject()
{
    global $wp_query;
    $scopesProject = getSelectScopesProject();
    $scopeProject = "";
    $scopeProjectSelect = NULL;
    $scopesProjectSelect = NULL;

    if (isset($wp_query->query_vars['scopeName'])) {
        $scopeProject = sanitize_title(urldecode($wp_query->query_vars['scopeName']));
    }
    foreach ($scopesProject as $key => $item) {
        $scopesProjectSelect[$key] = $item;
        if ($scopeProject == sanitize_title($item)) {
            $scopeProjectSelect[$key] = $item;
        }
    }
    $scopesProjectSelect['items'] = $scopesProjectSelect;
    $scopesProjectSelect['selected'] = $scopeProjectSelect;
    return $scopesProjectSelect;
}


/**
 * Get project types
 */
function getProjectTypes()
{
    global $wp_query;
    $projectsTypes = getSelectProjectTypes();
    $projectType = "";
    $projectTypeSelect = NULL;
    $projectsTypesSelect = NULL;

    if (isset($wp_query->query_vars['projectType'])) {
        $projectType = sanitize_title(urldecode($wp_query->query_vars['projectType']));
    }
    foreach ($projectsTypes as $key => $item) {
        $projectsTypesSelect[$key] = $item;
        if ($projectType == sanitize_title($item->getProjectTypeName())) {
            $projectTypeSelect[$key] = $item;
        }
    }
    $projectsTypesSelect['items'] = $projectsTypesSelect;
    $projectsTypesSelect['selected'] = $projectTypeSelect;
    return $projectsTypesSelect;
}

/**
 * Get types activities
 */
function getResearchTypesActivities()
{
    global $wp_query;
    $activity = "";
    $typesActivities = getSelectResearchTypesActivities();
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
 * Get project types
 */
function getFinancingAgencies()
{
    global $wp_query;
    $financingAgencies = getSelectFinancingAgencies();
    $financingAgency = "";
    $financingAgenciesSelect = NULL;
    $financingAgencySelect = NULL;

    if (isset($wp_query->query_vars['financingAgency'])) {
        $financingAgency = sanitize_title(urldecode($wp_query->query_vars['financingAgency']));
    }
    foreach ($financingAgencies as $key => $item) {
        $financingAgenciesSelect[$key] = $item;
        if ($financingAgency == sanitize_title($item->getFinancingAgencyName())) {
            $financingAgencySelect[$key] = $item;
        }
    }
    $financingAgenciesSelect['items'] = $financingAgenciesSelect;
    $financingAgenciesSelect['selected'] = $financingAgencySelect;
    return $financingAgenciesSelect;
}

/**
 * Get types activities USC
 */
function getResearchTypesActivitiesUSC()
{
    global $wp_query;
    $activity = "";
    $typesActivities = getSelectResearchTypesActivitiesUSC();
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

function getSubactivitiesTypeEdit($activityUSC)
{

    $subActivitiesSelect = getSubactivitiesUsc($activityUSC);
    return $subActivitiesSelect;
}

// Change value new Activity type
add_action("wp_ajax_load_dates_subactivityType", "api_getSubactivityTypeUSC");
add_action("wp_ajax_nopriv_load_dates_subactivityType", "api_getSubactivityTypeUSC");
function api_getSubactivityTypeUSC()
{
    $activityUSC = $_POST['activityUSC'];
    $subActivitiesSelect = getSubactivitiesUsc($activityUSC);
    /*
    global $wp_query;    
    $subActivities = getSelectResearchTypesSubActivitiesUSC($activityUSC);
    $subActivity = "";
    $subActivitiesSelect = NULL;
    $subActivitySelect = NULL;

    if (isset($wp_query->query_vars['subactivity'])) {
        $subActivity = sanitize_title(urldecode($wp_query->query_vars['subactivity']));
    }
    foreach ($subActivities as $key => $item) {
        $subActivitiesSelect[$key] = $item;
        if ($subActivity == sanitize_title($item->getActivityType())) {
            $subActivitySelect[$key] = $item;
        }
    }
    $subActivitiesSelect['items'] = $subActivitiesSelect;
    $subActivitiesSelect['selected'] = $subActivitySelect;*/
    wp_send_json_success($subActivitiesSelect);
    exit();
}

function getSubactivitiesUsc()
{
    global $wp_query;
    $subActivities = getSelectResearchTypesSubActivitiesUSC();
    $subActivity = "";
    $subActivitiesSelect = NULL;
    $subActivitySelect = NULL;

    if (isset($wp_query->query_vars['subactivity'])) {
        $subActivity = sanitize_title(urldecode($wp_query->query_vars['subactivity']));
    }
    foreach ($subActivities as $key => $item) {
        $subActivitiesSelect[$key] = $item;
        if ($subActivity == sanitize_title($item->getActivitySubType())) {
            $subActivitySelect[$key] = $item;
        }
    }
    $subActivitiesSelect['items'] = $subActivitiesSelect;
    $subActivitiesSelect['selected'] = $subActivitySelect;

    return $subActivitiesSelect;
}


add_action("wp_ajax_search_research_projects_submit", "api_search_research_projects_submit");
add_action("wp_ajax_nopriv_search_research_projects_submit", "api_search_research_projects_submit");

function api_search_research_projects_submit()
{

    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $researchProject = getResearchRequestModel();

    $info = fetchResearchProject($researchProject, $offset, $length, $order);

    $data = [
        "data" => $info['projects'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

/** EXPORT */
add_action('admin_action_export_research_projects', 'export_research_projects');
function export_research_projects()
{
    //definir con internacionalizacion
    $headers = [
        __("ID"), __("Project request"), __("Project reference"), __("Title EN"), __("Title ES"), __("Title GL"), __("Scope"), __("Description"), __("Web project"), __("Activity type"), __("USC Activity type"), __("USC Subactivity type"), __("USC Project reference"),
        __("Call"), __("Management center"), __("USC total amount"), __("Granted"), __("Concession date"), __("Office code"), __("Amount without overhead"),
        __("Partners"), __("Start date"), __("End date"), __("Financing agencies"), __("Total amount"), __("Annual justification"), __("Cofinancing"), __("Cofinancing type"),
        __("Resolution"), __("Extended"), __("End date extended"), __("FEDER"), __("FEDER %"), __("Research area"), __("Research program"), __("International consortium"), __("Research group"), __("Activity"),
        __("Competitive"), __("Competitive call"), __("Keywords"), __("Indirect costs"), __("IGFAE leader"), __("Project type"), __("Budget link"), __("Comments"), __("Validated"), __("Validated by"),
        __("Validated on"), __("Published"), __("Published by"), __("Published on"), __("Annual gifted USC"), __("Annual executed"), __("Awarded allotment"), __("Annual justified"),
        __("IP Ref. USC"), __("Principal researcher"), __("Asocciate researchers"), __("Technician"), __("Work team"),
    ];

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $researchProject = getResearchRequestModel();

    //Obtenemos todos los id de proyecto a exportar
    $idProjects = fetchIdProjectExport($researchProject);

    //Se realiza la busqueda de los datos a exportar 
    $projects = fetchResearchProjectExport($idProjects);

    //funcion de mapeo.
    array_walk($projects, function (&$v, $k) {
        $v = get_object_vars($v);
        if (!empty($v)) {
            unset($v['ProjectID']);

            if (isset($v['Granted'])) {
                $v['Granted'] = $v['Granted'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['AnnualJustification'])) {
                $v['AnnualJustification'] = $v['AnnualJustification'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['Cofinancing'])) {
                $v['Cofinancing'] = $v['Cofinancing'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['Extended'])) {
                $v['Extended'] = $v['Extended'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['FEDER'])) {
                $v['FEDER'] = $v['FEDER'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['Competitive'])) {
                $v['Competitive'] = $v['Competitive'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['CompetitiveCall'])) {
                $v['CompetitiveCall'] = $v['CompetitiveCall'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['IGFAELeader'])) {
                $v['IGFAELeader'] = $v['IGFAELeader'] == 1 ? __("Yes") : __("No"); 
            }
                                                                                          
            $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
            $v['Published'] = $v['Published'] == 1 ? __("Yes") : __("No");
            $v['ConcessionDate'] = isset($v['ConcessionDate']) ? formatDate($v['ConcessionDate']) : "";
            $v['StartDate'] = isset($v['StartDate']) ? formatDate($v['StartDate']) : "";
            $v['EndDate'] = isset($v['EndDate']) ? formatDate($v['EndDate']) : "";
            $v['EndDateExtended'] = isset($v['EndDateExtended']) ? formatDate($v['EndDateExtended']) : "";
            $v['ValidatedOn'] = isset($v['ValidatedOn']) ? formatDateWithHour($v['ValidatedOn']) : "";
            $v['PublishedOn'] = isset($v['PublishedOn']) ? formatDateWithHour($v['PublishedOn']) : "";

            //Formateo numerico      
            $v['AmountWithoutOverheads'] = isset($v['AmountWithoutOverheads']) ? formatNumber($v['AmountWithoutOverheads']) : "";      
            $v['TotalAmount'] = isset($v['TotalAmount']) ? formatNumber($v['TotalAmount']) : "";
            $v['FEDERPercent'] = isset($v['FEDERPercent']) ? formatNumber($v['FEDERPercent']) : "";
            $v['IndirectCosts'] = isset($v['IndirectCosts']) ? formatNumber($v['IndirectCosts']) : "";
        }
    });
    //al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($projects, $headers);

    //llamamos a la función común para exportar
    igfae_export($projects, __('ResearchProjects'));
}

function getResearchRequestModel()
{
    // Title
    $title = trim(filter_var($_POST['title'], FILTER_SANITIZE_STRING));

    //Scope select multiple
    $scope = [];
    if (isset($_POST['ambit'])) {
        foreach ($_POST['ambit'] as $item) {
            $scope[] = $item;
        }
    }

    //Type activity select multiple
    $typeActivity = [];
    if (isset($_POST['typeActivity'])) {
        foreach ($_POST['typeActivity'] as $item) {
            $typeActivity[] = $item;
        }
    }

    //Research Area select multiple
    $researchArea = [];
    if (isset($_POST['researchArea'])) {
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }

    //Research Program select multiple
    $researchProgram = [];
    if (isset($_POST['researchProgram'])) {
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }

    //collaboration/Experiment select multiple
    $collaborationExperiment = [];
    if (isset($_POST['collaborationExperiment'])) {
        foreach ($_POST['collaborationExperiment'] as $item) {
            $collaborationExperiment[] = $item;
        }
    }

    //Research Group select
    $researchGroup = [];
    if (isset($_POST['researchGroup'])) {
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }

    //Work line select multiple
    $workLine = [];
    if (isset($_POST['workLine'])) {
        foreach ($_POST['workLine'] as $item) {
            $workLine[] = $item;
        }
    }

    //Announcement
    $announcement = trim(filter_var($_POST['announcement'], FILTER_SANITIZE_STRING));

    //annualGifteDateFrom
    $annualGifteDateFrom = NULL;
    if (isset($_POST['annualGifteDateFrom'])) {
        $annualGifteDateFrom = trim(filter_var($_POST['annualGifteDateFrom'], FILTER_SANITIZE_STRING));
    }

    // annualGifteDateUntil
    $annualGifteDateUntil = NULL;
    if (isset($_POST['annualGifteDateUntil'])) {
        $annualGifteDateUntil = trim(filter_var($_POST['annualGifteDateUntil'], FILTER_SANITIZE_STRING));
    }

    //amountGiftedFrom
    $annualGifteAmountFrom = isset($_POST['amountGiftedFrom']) ? trim(filter_var($_POST['amountGiftedFrom'], FILTER_SANITIZE_STRING)) : NULL;

    //amountGiftedUntil
    $annualGifteAmountUntil = isset($_POST['amountGiftedUntil']) ? trim(filter_var($_POST['amountGiftedUntil'], FILTER_SANITIZE_STRING)) : NULL;

    //Start date
    $startDate = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));

    //End date
    $endDate = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));    

    //Concession date
    $concessionDate = trim(filter_var($_POST['concessionDate'], FILTER_SANITIZE_STRING));

    //Keywords select multiple
    $keywords = [];
    if (isset($_POST['keywords'])) {
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = $item;
        }
    }

    //Financing Agencies select multiple
    $financingAgencies = [];
    if (isset($_POST['financingAgency'])) {
        foreach ($_POST['financingAgency'] as $item) {
            $financingAgencies[] = $item;
        }
    }

    //Reference
    $reference = trim(filter_var($_POST['reference'], FILTER_SANITIZE_STRING));

    //Amount without overhead from
    $amountOverheadFrom = formatNumberToBD(trim(filter_var($_POST['amountOverheadFrom'], FILTER_SANITIZE_STRING)));

    //Amount without overhead until
    $amountOverheadUntil = formatNumberToBD(trim(filter_var($_POST['amountOverheadUntil'], FILTER_SANITIZE_STRING)));

    //FEDER
    $feder = [];
    if (isset($_POST['feder'])) {
        foreach ($_POST['feder'] as $item) {
            $feder[] = $item;
        }
    }

    //Annual justification select
    $justification = [];
    if (isset($_POST['justification'])) {
        foreach ($_POST['justification'] as $item) {
            $justification[] = $item;
        }
    }

    //Extended select
    $extended = [];
    if (isset($_POST['extended'])) {
        foreach ($_POST['extended'] as $item) {
            $extended[] = $item;
        }
    }

    //Indirect costs from
    $indirectCostsFrom = isset($_POST['indirectCostsFrom']) ? trim(filter_var($_POST['indirectCostsFrom'], FILTER_SANITIZE_STRING)) : NULL;

    //Indirect costs until
    $indirectCostsUntil = isset($_POST['indirectCostsUntil']) ? trim(filter_var($_POST['indirectCostsUntil'], FILTER_SANITIZE_STRING)) : NULL;

    //projectCoordinator
    $projectCoordinator = [];
    if (isset($_POST['projectCoordinator'])) {
        foreach ($_POST['projectCoordinator'] as $item) {
            $projectCoordinator[] = $item;
        }
    }

    //principalResearcher
    $principalResearcher = [];
    if (isset($_POST['principalResearcher'])) {
        foreach ($_POST['principalResearcher'] as $item) {
            $principalResearcher[] = $item;
        }
    }

    //associateResearchers
    $associateResearchers = [];
    if (isset($_POST['associateResearchers'])) {
        foreach ($_POST['associateResearchers'] as $item) {
            $associateResearchers[] = $item;
        }
    }

    //technical
    $technical = [];
    if (isset($_POST['technical'])) {
        foreach ($_POST['technical'] as $item) {            
            $technical[] = $item;
        }
    }

    //Projects type select
    $typeProject = [];
    if (isset($_POST['projectType'])) {
        foreach ($_POST['projectType'] as $item) {
            $typeProject[] = $item;
        }
    }

    //Budget link
    $budgetLink = trim(filter_var($_POST['budgetLink'], FILTER_SANITIZE_STRING));

    //Competitive select
    $competitive = NULL;
    if (isset($_POST['competitive'])) {
        $competitive = trim(filter_var($_POST['competitive'], FILTER_SANITIZE_STRING));
    }

    //IGFAE Leader select
    $igfaeLeader = NULL;
    if (isset($_POST['igfaeLeader'])) {
        $igfaeLeader     = trim(filter_var($_POST['igfaeLeader'], FILTER_SANITIZE_STRING));
    }

    //Reference
    $reference2 = trim(filter_var($_POST['reference2'], FILTER_SANITIZE_STRING));

    //Validated select
    $validated = NULL;
    if (isset($_POST['validated'])) {
        $validated     = trim(filter_var($_POST['validated'], FILTER_SANITIZE_STRING));
    }

    //Published select
    $published = NULL;
    if (isset($_POST['published'])) {
        $published     = trim(filter_var($_POST['published'], FILTER_SANITIZE_STRING));
    }

    $researchProject = new IntranetResearchProject(
        NULL,
        $title,
        $scope,
        $typeActivity,
        $researchArea,
        $researchProgram,
        $collaborationExperiment,
        $researchGroup,
        $workLine,
        $announcement,
        $annualGifteDateFrom,
        $annualGifteDateUntil,
        $annualGifteAmountFrom,
        $annualGifteAmountUntil,
        NULL,
        $startDate,
        $concessionDate,
        $keywords,
        $financingAgencies,
        $reference,
        $amountOverheadFrom,
        $amountOverheadUntil,
        NULL,
        $feder,
        $justification,
        $extended,
        $indirectCostsFrom,
        $indirectCostsUntil,
        NULL,
        NULL,
        $typeProject,
        $budgetLink,
        $competitive,
        $igfaeLeader,
        $reference2,
        $validated,
        $published,
        $projectCoordinator,
        $principalResearcher,
        $associateResearchers,
        $endDate
    );

    $researchProject->setTechnical($technical);
    $researchProject->setEndDate($endDate);

    return $researchProject;
}

add_action("wp_ajax_new_research_projects_submit", "api_new_research_projects_submit");
add_action("wp_ajax_nopriv_new_research_projects_submit", "api_new_research_projects_submit");
function api_new_research_projects_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();

    global $current_user;

    // Id Project
    $idProject = trim(filter_var($_POST['idProject'], FILTER_SANITIZE_STRING));

    //project request 
    $projectRequest = trim(filter_var($_POST['request'], FILTER_SANITIZE_STRING));

    // Title
    $titleES = trim(filter_var($_POST['title_es'], FILTER_SANITIZE_STRING));

    $titleEN = trim(filter_var($_POST['title_en'], FILTER_SANITIZE_STRING));

    $titleGL = trim(filter_var($_POST['title_gl'], FILTER_SANITIZE_STRING));

    // Scope
    $scope = trim(filter_var($_POST['scope'], FILTER_SANITIZE_STRING));

    // Description textarea
    $description         = NULL;
    if (isset($_POST['description']) && !empty($_POST['description']) && !empty(strip_tags($_POST['description']))) {
        $textDescription = str_replace('\"', '"', $_POST["description"]); 
        $description = $purifier->purify(trim($textDescription)); 
    }

    //Web project
    $webProject = NULL;
    if (isset($_POST['webProject']) && !empty($_POST['webProject'])) {
        $webProject = trim(filter_var($_POST['webProject'], FILTER_SANITIZE_STRING));
    }

    //Type activity select
    $typeActivity = NULL;
    if (isset($_POST['typeActivity']) && !empty($_POST['typeActivity'])) {
        $typeActivity = trim(filter_var($_POST['typeActivity'], FILTER_SANITIZE_STRING));
    }

    //Subtype activity Usc select
    $subTypeActivity    = NULL;
    if (isset($_POST['subactivity']) && !empty($_POST['subactivity'])) {
        $subTypeActivity         = [];
        foreach ($_POST['subactivity'] as $item) {
            $subTypeActivity[] = $item;
        }
    }

    //Reference
    $reference = NULL;
    if (isset($_POST['reference']) && !empty($_POST['reference'])) {
        $reference = trim(filter_var($_POST['reference'], FILTER_SANITIZE_STRING));
    }

    //Reference usc
    $referenceUSC = NULL;
    if (isset($_POST['referenceUSC']) && !empty($_POST['referenceUSC'])) {
        $referenceUSC = trim(filter_var($_POST['referenceUSC'], FILTER_SANITIZE_STRING));
    }

    //Announcement
    $announcement = NULL;
    if (isset($_POST['announcement']) && !empty($_POST['announcement'])) {
        $announcement = trim(filter_var($_POST['announcement'], FILTER_SANITIZE_STRING));
    }

    //Funding agency (Management center)
    $fundingAgency    = NULL;
    if (isset($_POST['fundingAgency']) && !empty($_POST['fundingAgency'])) {
        $fundingAgency         = [];
        foreach ($_POST['fundingAgency'] as $item) {
            $fundingAgency[] = $item;
        }
    }

    //Amount without overhead
    $amountOverhead = NULL;
    if (isset($_POST['amountOverhead']) && !empty($_POST['amountOverhead'])) {
        $amountOverhead = formatNumberToBD(trim(filter_var($_POST['amountOverhead'], FILTER_SANITIZE_STRING)));
    }


    //Granted
    $granted = null;
    if (isset($_POST['granted']) && !empty($_POST['granted'])) {
        if ($_POST['granted'] == __("Yes")) {
            $granted = 1;
        } else {
            $granted = 0;
        }
    }

    //Concession date    
    $newConcessionDate = NULL;
    if (isset($_POST['concessionDate']) && !empty($_POST['concessionDate'])) {
        $newConcessionDate = trim(filter_var($_POST['concessionDate'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($newConcessionDate);
        $newConcessionDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    //Office code    
    $officeCode = NULL;
    if (isset($_POST['officeCode']) && !empty($_POST['officeCode'])) {
        $officeCode = trim(filter_var($_POST['officeCode'], FILTER_SANITIZE_STRING));
    }

    //Reference 2    
    $reference2 = NULL;
    if (isset($_POST['reference2']) && !empty($_POST['reference2'])) {
        $reference2 = trim(filter_var($_POST['reference2'], FILTER_SANITIZE_STRING));
    }

    //partners    
    $partners = NULL;
    if (isset($_POST['partners']) && !empty($_POST['partners'])) {
        $partners         = [];
        foreach ($_POST['partners'] as $item) {
            $partners[] = $item;
        }
    }

    //Coordinators project
    $projectCoordinator    = NULL;
    if (isset($_POST['projectCoordinator']) && !empty($_POST['projectCoordinator'])) {
        $projectCoordinator         = [];
        foreach ($_POST['projectCoordinator'] as $item) {
            $projectCoordinator[] = $item;
        }
    }

    //pincipal Researcher   
    $pincipalResearcher = NULL;
    if (isset($_POST['pincipalResearcher']) && !empty($_POST['pincipalResearcher'])) {
        $pincipalResearcher = [];
        foreach ($_POST['pincipalResearcher'] as $item) {
            $pincipalResearcher[] = $item;
        }
    }

    //Associate Researchers
    $associateResearchers    = NULL;
    if (isset($_POST['associateResearchers']) && !empty($_POST['associateResearchers'])) {
        $associateResearchers         = [];
        foreach ($_POST['associateResearchers'] as $item) {
            $associateResearchers[] = $item;
        }
    }

    //technical
    $technical    = NULL;
    if (isset($_POST['technical']) && !empty($_POST['technical'])) {
        $technical         = [];
        foreach ($_POST['technical'] as $item) {
            $technical[] = $item;
        }
    }

    //Start date
    $startDate = NULL;
    if (isset($_POST['startDate']) && !empty($_POST['startDate'])) {
        $startDate    = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($startDate);
        $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    //End date
    $endDate = NULL;
    if (isset($_POST['endDate']) && !empty($_POST['endDate'])) {
        $endDate    = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($endDate);
        $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    //Organismo financiador
    $financingAgency    = NULL;
    if (isset($_POST['financingAgency']) && !empty($_POST['financingAgency'])) {
        $financingAgency         = [];
        foreach ($_POST['financingAgency'] as $item) {
            $financingAgency[] = $item;
        }
    }

    //total Amount 
    $totalAmount = NULL;
    if (isset($_POST['totalAmount']) && !empty($_POST['totalAmount'])) {
        $totalAmount = formatNumberToBD(trim(filter_var($_POST['totalAmount'], FILTER_SANITIZE_STRING)));
    }

    //Annual justification
    $annualJustification = null;
    if (isset($_POST['annualJustification']) && !empty($_POST['annualJustification'])) {
        if ($_POST['annualJustification'] == __("Yes")) {
            $annualJustification = 1;
        } else {
            $annualJustification = 0;
        }
    }

    //Awarded Allotment
    $awardedAllotment = NULL;
    if (isset($_POST['tableAwardedAllotment']) && !empty($_POST['tableAwardedAllotment'])) {
        $awardedAllotment = json_decode(stripcslashes($_POST['tableAwardedAllotment']), true);
    }

    //Annual gifted Usc
    $annualGiftedUsc = NULL;
    if (isset($_POST['tableGiftedUsc']) && !empty($_POST['tableGiftedUsc'])) {
        $annualGiftedUsc = json_decode(stripcslashes($_POST['tableGiftedUsc']), true);
    }
    //Annual executed
    $anualExecuted = NULL;
    if (isset($_POST['tableAnnualExecuted']) && !empty($_POST['tableAnnualExecuted'])) {
        $anualExecuted = json_decode(stripcslashes($_POST['tableAnnualExecuted']), true);
    }
    //Annual justification
    $annualJustificationTable = NULL;
    if (isset($_POST['tableAnnualJustification']) && !empty($_POST['tableAnnualJustification'])) {
        $annualJustificationTable = json_decode(stripcslashes($_POST['tableAnnualJustification']), true);
    }

    //Cofinancing
    $cofinancing = null;
    if (isset($_POST['cofinancing']) && !empty($_POST['cofinancing'])) {
        if ($_POST['cofinancing'] == __("Yes")) {
            $cofinancing = 1;
        } else {
            $cofinancing = 0;
        }
    }

    //Cofinancing type    
    $cofinancingType = NULL;
    if (isset($_POST['cofinancingType']) && !empty($_POST['cofinancingType'])) {
        $cofinancingType = trim(filter_var($_POST['cofinancingType'], FILTER_SANITIZE_STRING));
    }

    //Resolution     
    $resolution = NULL;
    if (isset($_POST['resolution']) && !empty($_POST['resolution'])) {
        $resolution = trim(filter_var($_POST['resolution'], FILTER_SANITIZE_STRING));
    }

    //Extended
    $extended = null;
    if (isset($_POST['extended']) && !empty($_POST['extended'])) {
        if ($_POST['extended'] == __("Yes")) {
            $extended = 1;
        } else {
            $extended = 0;
        }
    }

    //End date extended
    $extendedDate = NULL;
    if (isset($_POST['extendedDate']) && !empty($_POST['extendedDate'])) {
        $extendedDate    = trim(filter_var($_POST['extendedDate'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($extendedDate);
        $extendedDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    //Feder
    $feder = null;
    if (isset($_POST['feder']) && !empty($_POST['feder'])) {
        if ($_POST['feder'] == __("Yes")) {
            $feder = 1;
        } else {
            $feder = 0;
        }
    }

    //Feder percent    
    $federPercent = NULL;
    if (isset($_POST['federPercent']) && !empty($_POST['federPercent'])) {
        $federPercent = formatNumberToBD(trim(filter_var($_POST['federPercent'], FILTER_SANITIZE_STRING)));
    }

    //Research area
    $researchArea    = NULL;
    if (isset($_POST['researchArea']) && !empty($_POST['researchArea'])) {
        $researchArea         = [];
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }

    //Research program
    $researchProgram    = NULL;
    if (isset($_POST['researchProgram']) && !empty($_POST['researchProgram'])) {
        $researchProgram         = [];
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }

    //Collaboration / experiment   
    $collaborationExperiment = NULL;
    if (isset($_POST['collaborationExperiment']) && !empty($_POST['collaborationExperiment'])) {
        $collaborationExperiment = trim(filter_var($_POST['collaborationExperiment'], FILTER_SANITIZE_STRING));
    }

    //Research group
    $researchGroup = NULL;
    if (isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])) {
        $researchGroup = trim(filter_var($_POST['researchGroup'], FILTER_SANITIZE_STRING));
    }

    //Work line
    $workLine = NULL;
    if (isset($_POST['workLine']) && !empty($_POST['workLine'])) {
        $workLine = trim(filter_var($_POST['workLine'], FILTER_SANITIZE_STRING));
    }

    //Competitve call
    $competitiveCall = null;
    if (isset($_POST['competitiveCall']) && !empty($_POST['competitiveCall'])) {
        if ($_POST['competitiveCall'] == __("Yes")) {
            $competitiveCall = 1;
        } else {
            $competitiveCall = 0;
        }
    }

    //Keywords
    $keywords    = NULL;
    if (isset($_POST['keywords']) && !empty($_POST['keywords'])) {
        $keywords         = [];
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = trim($item);
        }
    }

    //Indirect costs
    $indirectCosts = NULL;
    if (isset($_POST['indirectCosts']) && !empty($_POST['indirectCosts'])) {
        $indirectCosts = formatNumberToBD(trim(filter_var($_POST['indirectCosts'], FILTER_SANITIZE_STRING)));
    }

    //Competitve
    $competitive = null;
    if (isset($_POST['competitive']) && !empty($_POST['competitive'])) {
        if ($_POST['competitive'] == __("Yes")) {
            $competitive = 1;
        } else {
            $competitive = 0;
        }
    }

    //IGFAE leader
    $igfaeLeader = null;
    if (isset($_POST['igfaeLeader']) && !empty($_POST['igfaeLeader'])) {
        if ($_POST['igfaeLeader'] == __("Yes")) {
            $igfaeLeader = 1;
        } else {
            $igfaeLeader = 0;
        }
    }

    //Project type
    $projectType = NULL;
    if (isset($_POST['projectType']) && !empty($_POST['projectType'])) {
        $projectType = trim(filter_var($_POST['projectType'], FILTER_SANITIZE_STRING));
    }

    //Budget Link   
    $budgetLink = NULL;
    if (isset($_POST['budgetLink']) && !empty($_POST['budgetLink'])) {
        $budgetLink = trim(filter_var($_POST['budgetLink'], FILTER_SANITIZE_STRING));
    }

    //workTeam
    $workTeam = NULL;
    if (isset($_POST['workTeam']) && !empty($_POST['workTeam'])) {
        $workTeam         = [];
        foreach ($_POST['workTeam'] as $item) {
            $workTeam[] = $item;
        }
    }

    //consortium
    $consortium = null;
    if (isset($_POST['consortium']) && !empty($_POST['consortium'])) {
        if ($_POST['consortium'] == __("Yes")) {
            $consortium = 1;
        } else {
            $consortium = 0;
        }
    }

    // Comments textarea
    $comments         = NULL;
    if (isset($_POST['comments']) && !empty($_POST['comments']) && !empty(strip_tags($_POST['comments']))) {
        $textComments = str_replace('\"', '"', $_POST["comments"]); 
        $comments = $purifier->purify(trim($textComments)); 
    }

    //Validated select
    $validated = NULL;
    $validatedOn = NULL;
    $validatedBy = NULL;
    if (isset($_POST['validated']) && !empty($_POST['validated'])) {
        $validated     = 1;
        $validatedOn   = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
        $validatedBy = $current_user->user_login;
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede validar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de validar
        $validated = 0;
    }
    //Published select
    $published         = NULL;
    $publishedOn       = NULL;
    $publishedBy       = NULL;
    if (isset($_POST['published']) && !empty($_POST['published'])) {
        $published     = 1;
        $publishedOn   = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
        $publishedBy = $current_user->user_login;
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede publicar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de publicar
        $published = 0;
    }

    $researchProject = new IntranetResearchProject();
    $researchProject->setAwardedAllotment($awardedAllotment);
    $researchProject->setAnnualGifted($annualGiftedUsc);
    $researchProject->setAnnualJustified($annualJustificationTable);
    $researchProject->setAnnualExecuted($anualExecuted);
    $researchProject->setTitleEn($titleEN);
    $researchProject->setTitleEs($titleES);
    $researchProject->setTitleGl($titleGL);
    $researchProject->set_collaborationExperiment($collaborationExperiment);
    $researchProject->setFundingAgencies($fundingAgency);
    $researchProject->set_keywords($keywords);
    $researchProject->setPartners($partners);
    $researchProject->set_researchArea($researchArea);
    $researchProject->set_researchProgram($researchProgram);
    $researchProject->set_financingAgencies($financingAgency);
    $researchProject->setUscSubActivityType($subTypeActivity);
    $researchProject->setIpUsers($pincipalResearcher);
    $researchProject->setIpCoordinatesUsers($projectCoordinator);
    $researchProject->setAssociatedUsers($associateResearchers);
    $researchProject->setTechnicianUsers($technical);
    $researchProject->setWorkTeam($workTeam);

    $sizeSubtype = 0;
    $typeActivityUsc = getUSCSubtypeByID($researchProject);
    if ($typeActivityUsc != null && $typeActivityUsc != []) {
        $sizeSubtype = count($typeActivityUsc);
    }

    $typeT = NULL;
    if ($sizeSubtype == 1) {
        foreach ($typeActivityUsc as $i) {
            $typeT  = $i;
        }
    }

    //Construimos el array con las columnas y valores para el insert
    $table = "Project";
    $data = array(
        'Request' => $projectRequest,
        'ProjectReference' => $reference,
        'USCProjectReference' => $referenceUSC,
        'StartDate' => $startDate,
        'EndDate' => $endDate,
        'TotalAmount' => $totalAmount,
        'AmountWithoutOverheads' => $amountOverhead,
        'ConcessionDate' => $newConcessionDate,
        'Announcement' => $announcement,
        'Description' => $description,
        'ScopeID' => $scope, //not null
        'Website' => $webProject,
        'ActivityTypeID' => $typeActivity,
        'USCActivityTypeID' => $typeT,
        'OfficeCode' => $officeCode,
        'Reference2' => $reference2,
        'CofinancingType' => $cofinancingType,
        'FEDER' => $feder,
        'FEDERPercent' => $federPercent,
        'Granted' => $granted,
        'AnnualJustification' => $annualJustification,
        'Cofinancing' => $cofinancing,
        'Extended' => $extended,
        'CompetitiveCall' => $competitiveCall,
        'Competitive' => $competitive,
        'IGFAELeader' => $igfaeLeader,
        'IndirectCosts' => $indirectCosts,
        'EndDateExtended' => $extendedDate,
        'ResearchGroupID' => $researchGroup,
        'WorkLineID' => $workLine,
        'ProjectTypeID' => $projectType,
        'BudgetLink' => $budgetLink,
        'Resolution' => $resolution,
        'Consortium' => $consortium,
        'Comment' => $comments,
        'Validated' => $validated,
        'Published' => $published,
        'ValidatedBy' => $validatedBy,
        'PublishedBy' => $publishedBy,
        'ValidatedOn' => $validatedOn,
        'PublishedOn' => $publishedOn
    );



    // COMPROBAMOS QUE EL ID USC SUBTYPE SEA ÚNICO
    if ($sizeSubtype > 1) {
        wp_send_json_error(new WP_Error('-1', __('USC subactivities must have same USC activity type')));
    } else if (!verificarValidarInformacion($validated, $published)) {
        // No se permite publicar sin haber validado previamente
        wp_send_json_error(new WP_Error('-1', __('In order to publish, the information must first be validated')));
    } else {
        // $idUser-> id del usuario a editar, $idUserSave-> id del nuevo usuario
        $idProjectSave = saveResearchProject($data, $researchProject);
        $return = array(
            'idProject'         => $idProject,
            'idProjectSave'     => $idProjectSave
        );
        wp_send_json_success($return);
    }

    exit();
}

// Botón Delete proyect
add_action("wp_ajax_delete_research_projects_submit", "api_delete_research_projects_submit");
add_action("wp_ajax_nopriv_delete_research_projects_submit", "api_delete_research_projects_submit");
function api_delete_research_projects_submit()
{
    $id = $_POST['id'];

    $deleted = deleteResearchProject($id);

    if ($deleted == false) {
        wp_send_json_error(new WP_Error('-1', __('The research project cannot be removed')));
    } else {
        wp_send_json_success();
    }

    exit();
}
