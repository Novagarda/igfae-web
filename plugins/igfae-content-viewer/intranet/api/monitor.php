<?php

function getListQuarter() {
    global $wp_query;
    
    $quarters = getSelectQuarters();
    $quarter = "";
    $quarterSelected = NULL;
    $quartersSelect = NULL;

    if (isset($wp_query->query_vars['startDate'])) {
        $quarter = sanitize_title(urldecode($wp_query->query_vars['startDate']));
    }
    foreach ($quarters as $key => $item) {
        $quartersSelect[$key] = $item;
        if ($quarter == sanitize_title($item->getQuarter())) {
            $quarterSelected[$key] = $item;
        }
    }
    $quartersSelect['items'] = $quartersSelect;
    $quartersSelect['selected'] = $quarterSelected;
    return $quartersSelect;
}

function getListMonitorIndicator() {
    global $wp_query;
    
    $monitorsIndicators = getSelectMonitorsIndicators();
    $monitorIndicator = "";
    $monitorIndicatorSelected = NULL;
    $monitorsIndicatorsSelect = NULL;

    if (isset($wp_query->query_vars['monitorIndicator'])) {
        $monitorIndicator = sanitize_title(urldecode($wp_query->query_vars['monitorIndicator']));
    }
    foreach ($monitorsIndicators as $key => $item) {
        $monitorsIndicatorsSelect[$key] = $item;
        if ($monitorIndicator == sanitize_title($item->getIndicator())) {
            $monitorIndicatorSelected[$key] = $item;
        }
    }
    $monitorsIndicatorsSelect['items'] = $monitorsIndicatorsSelect;
    $monitorsIndicatorsSelect['selected'] = $monitorIndicatorSelected;
    return $monitorsIndicatorsSelect;
}

add_action("wp_ajax_list_monitor_indicator", "api_get_list_monitor_indicator");
add_action("wp_ajax_nopriv_list_monitor_indicator", "api_get_list_monitor_indicator");
function api_get_list_monitor_indicator(){
    $datos = getListMonitorIndicator();

    wp_send_json_success($datos);

    exit();
}

function getListMonitorType() {    
    global $wp_query;
    
    $monitorsTypes = getSelectMonitorsTypes();
    $monitorType = "";
    $monitorTypeSelected = NULL;
    $monitorsTypesSelect = NULL;

    if (isset($wp_query->query_vars['monitorType'])) {
        $monitorType = sanitize_title(urldecode($wp_query->query_vars['monitorType']));
    }
    foreach ($monitorsTypes as $key => $item) {
        $monitorsTypesSelect[$key] = $item;
        if ($monitorType == sanitize_title($item->getDescription())) {
            $monitorTypeSelected[$key] = $item;
        }
    }
    $monitorsTypesSelect['items'] = $monitorsTypesSelect;
    $monitorsTypesSelect['selected'] = $monitorTypeSelected;    
    return $monitorsTypesSelect;
}


add_action("wp_ajax_search_monitor_submit", "api_search_monitor_submit");
add_action("wp_ajax_nopriv_search_monitor_submit", "api_search_monitor_submit");
function api_search_monitor_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;
    $monitorFilter = getMonitorRequestModel();
    $info = fetchMonitor($monitorFilter, $order, false);    

    $data = [
        "data" => $info['rows'],
        "draw" => $draw,
        "recordsTotal"  =>  sizeof($info['rows']),
        "recordsFiltered"  => 0
    ];
    wp_send_json($data);
    exit();
}

add_action("wp_ajax_validate_search_monitor_submit", "api_validate_search_monitor_submit");
add_action("wp_ajax_nopriv_validate_search_monitor_submit", "api_validate_search_monitor_submit");
function api_validate_search_monitor_submit()
{
    $monitorFilter = getMonitorRequestModel();

    // Comprobamos la obligatoriedad del Source en funcion de los indicadores seleccionados
    if ($monitorFilter->getObjectives() != null && $monitorFilter->getSource() == null && indicadorNeedsSource($monitorFilter->getObjectives()) > 0) {
        wp_send_json_error(new WP_Error('-1', __('Selected objectives needs a selected source')));
    } else if ($monitorFilter->getObjectives() != null && objectivesUniqueTypes($monitorFilter->getObjectives()) > 0){
        wp_send_json_error(new WP_Error('-1', __('There is more than one type of objective selected')));
    } else {
        wp_send_json([
            "success" => true
        ]);
    }
    exit();
}

add_action("wp_ajax_search_objectives", "search_objectives");
add_action("wp_ajax_search_objectives", "search_objectives");
function search_objectives()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;   

    $info = fetchObjectives($offset, $length, $order);

    $data = [
        "data" => $info['objectives'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

add_action("wp_ajax_add_objectives_submit", "add_objectives_submit");
add_action("wp_ajax_add_objectives_submit", "add_objectives_submit");
function add_objectives_submit()
{
    global $current_user;

    // Id Project
    $idObjective = trim(filter_var($_POST['idObjective'], FILTER_SANITIZE_STRING));

    //Indicator name 
    $indicatorName = trim(filter_var($_POST['indicatorName'], FILTER_SANITIZE_STRING));

    //Type indicator
    $typeIndicator = NULL;
    if (isset($_POST['typeIndicator']) && !empty($_POST['typeIndicator'])) {
        $typeIndicator = trim(filter_var($_POST['typeIndicator'], FILTER_SANITIZE_STRING));
    }

    //Objective name 
    $objectiveName = trim(filter_var($_POST['objectiveName'], FILTER_SANITIZE_STRING));

    //date    
    $dateObjective = NULL;
    if (isset($_POST['dateObjective']) && !empty($_POST['dateObjective'])) {        
        $dateObjective = trim(filter_var($_POST['dateObjective'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($dateObjective);
        $dateObjective = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    //Amount without overhead
    $value = NULL;
    if (isset($_POST['objectiveValue']) && !empty($_POST['objectiveValue'])) {
        $value = trim(filter_var($_POST['objectiveValue'], FILTER_SANITIZE_STRING));
    }

    //Indicator name 
    $expectations = trim(filter_var($_POST['expectations'], FILTER_SANITIZE_STRING));

    //Validated select
    $validated = NULL;
    $validatedBy = NULL;
    if (isset($_POST['validated']) && !empty($_POST['validated'])) {                      
        $validated = 1;
        $validatedBy = $current_user->user_login;
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede validar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de validar
        $validated = 0;
    }
    
     //Published select
     $published         = NULL;
     $publishedBy       = NULL;
     if (isset($_POST['published']) && !empty($_POST['published'])) {        
        $published = 1;
        $publishedBy = $current_user->user_login;
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede publicar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de publicar
        $published = 0;
    }

    // Se valida la obligatoriedad del campo Value
    if ($typeIndicator != null && $value == null && indicadorNeedsvalue(array($typeIndicator)) > 0) {
        wp_send_json_error(new WP_Error('-1', __('Selected type indicador needs a selected value')));
    } else if (!verificarValidarInformacion($validated, $published)) {
        // No se permite publicar sin haber validado previamente
        wp_send_json_error(new WP_Error('-1', __('In order to publish, the information must first be validated')));
    } else {
        $value = isset($value) ? formatNumberToBD($value) : $value;
        $data = array (        
            'MonitorTypeID' => $typeIndicator,
            'Indicator' =>$indicatorName,
            'Objective' => $objectiveName,
            'Date' => $dateObjective,
            'Expectance' => $expectations,
            'Value' => $value,
            'Validated' => $validated,
            'Published' => $published,
            'ValidatedBy' => $validatedBy, 
            'PublishedBy' => $publishedBy
            );
    
        $idObjectiveSave = saveObjective($data);
    
        $return = array(
            'idObjective' => $idObjective,
            'idObjectiveSave' => $idObjectiveSave
        );
    
        wp_send_json_success($return);
    }

    exit();
}

// Botón delete objective
add_action("wp_ajax_delete_objective", "api_delete_objective");
add_action("wp_ajax_nopriv_delete_objective", "api_delete_objective");
function api_delete_objective()
{
    $id = $_POST['id'];

    $deleted = deleteMonitorIndicator($id);

    if ($deleted == false) {
        wp_send_json_error(new WP_Error('-1', __('The objective cannot be removed')));
    } else {
        wp_send_json_success();
    }

    exit();
}

function getMonitorRequestModel() {

    //researchArea select multiple
    $researchArea = [];
    if (isset($_POST['researchArea'])) {
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;           
        }
    }

    //researchProgram select multiple
    $researchProgram = [];
    if (isset($_POST['researchProgram'])) {
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }

    //collaborationExperiment
    $collaborationExperiment = [];
    if (isset($_POST['collaborationExperiment'])) {
        foreach ($_POST['collaborationExperiment'] as $item) {
            $collaborationExperiment[] = $item;
        }
    }

    //researchGroup select multiple
    $researchGroup = [];
    if (isset($_POST['researchGroup'])) {
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }

    //researchs select multiple
    $researchs = [];
    if (isset($_POST['researchs'])) {
        foreach ($_POST['researchs'] as $item) {
            $researchs[] = $item;
        }
    }

    //startDate
    $startDate = NULL;
    if (isset($_POST['startDate'])) {
        $startDate = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
    }

    // endDate
    $endDate = NULL;
    if (isset($_POST['endDate'])) {
        $endDate = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));
    }

    //objectives select multiple
    $objectives = [];
    if (isset($_POST['objectives'])) {
        foreach ($_POST['objectives'] as $item) {
            $objectives[] = $item;
        }
    }

    // source
    $source = NULL;
    if (isset($_POST['source'])) {
        $source = trim(filter_var($_POST['source'], FILTER_SANITIZE_STRING));
    }

    $monitorRequest = new IntranetMonitorRequest(
        $researchArea,
        $researchProgram,
        $collaborationExperiment,
        $researchGroup,
        $researchs,
        $startDate,
        $endDate,
        $objectives,
        $source
    );

    return $monitorRequest;
}

add_action( 'admin_action_export_monitor', 'export_monitor' );
function export_monitor()
{
    // Lanzamos la busqueda de monitor
    $monitorFilter = getMonitorRequestModel();
    // Obtenemos los quarters seleccionados por el usuario
    $quarters = fetchAllSelectedQuarters($monitorFilter->getStartDate(), $monitorFilter->getEndDate());
    // Obtenemos fecha inicial y final de los Quarters seleccionados por el usuario
    $quartersDates = fetchStartDateAndEndDateFromQuarters($monitorFilter->getStartDate(), $monitorFilter->getEndDate());
    // Obtenemos los datos de monitor
    $info = fetchMonitor($monitorFilter, NULL, true);
    $datos = $info["rows"];    

    // Construccion de headers en base a la lista de datos
    $headers = [];
    $headers[] = __("Nº");
    $headers[] = __("Indicator");
    $headers[] = __("Base line"). " ". date("Y", strtotime($quartersDates["StartDate"])) . "-" . date("Y", strtotime($quartersDates["EndDate"]));
    foreach ($quarters as $quarter) {
        $headers[] = substr($quarter, 1, 4) . "-Q" . substr($quarter, 6, 1);
    }
    $headers[] = __("Tendence");
    $headers[] = __("To be fullfilled");
    $headers[] = __("Expected outcomes (at the end ") . date("Y", strtotime($quartersDates["EndDate"])) . ")";

    //funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($datos, function (&$v, $k) {
        $v = get_object_vars($v);
        /*
        $monitorFilter = getMonitorRequestModel();
        // Obtenemos los quarters seleccionados por el usuario
        $quarters = fetchAllSelectedQuarters($monitorFilter->getStartDate(), $monitorFilter->getEndDate());
        
        //Eliminamos el formato que nos llega, interpretado como decimal
        foreach ($quarters as $quarter) {
            $nombreQuarter = "Y" . substr($quarter, 1, 4) . "Q" . substr($quarter, 6, 1);                   
            $v[$nombreQuarter] = notFormatNumber($v[$nombreQuarter]);            
        }

        $v['Tendencia'] = isset($v['Tendencia']) ? notFormatNumber($v['Tendencia']) : "";
        $v['Pendient'] = isset($v['Pendiente']) ? notFormatNumber($v['Pendiente']) : "";*/
    }); 

    array_unshift($datos, $headers);

    //llamamos a la función común para exportar
    igfae_export($datos, __('Monitor'));
}

add_action("wp_ajax_check_autorization_monitor", "api_check_autorization_monitor");
add_action("wp_ajax_nopriv_check_autorization_monitor", "api_check_autorization_monitor");
function api_check_autorization_monitor() {
    $idMonitor = $_POST['id'];
    $typeAuthorization = 0;
    

    $consolidated = isMonitorConsolidated($idMonitor);
   
        if ( $consolidated ){
            //$redirect = "location:javascript://history.go(-1)";   
            $redirect = get_permalink(get_page_by_path('search-monitor'));            
            $typeAuthorization = check_authorization(IGFAE_INTRANET_ENTITY_NAME_MONITOR_INDICATOR, $idMonitor, $redirect);    
        } 
    
    wp_send_json_success($typeAuthorization);
    exit();
}