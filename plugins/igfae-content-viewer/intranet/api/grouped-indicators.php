<?php

add_action("wp_ajax_search_grouped_indicators_submit", "api_search_grouped_indicators_submit");
add_action("wp_ajax_nopriv_search_grouped_indicators_submit", "api_search_grouped_indicators_submit");

function api_search_grouped_indicators_submit() {

    $draw = isset($_POST['draw'])? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)):0;
    $length = isset($_POST['length'])? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)): 10;
    $offset = isset($_POST['start'])? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)): 0;
    $order = isset($_POST['order'])? $_POST['order']: null;

    $groupedIndicatorsRequest = getGroupedIndicatorsRequestModel();
    
    $groupedIndicators = fetchGroupedIndicators($groupedIndicatorsRequest, $offset, $length, $order);    
   
    $data = [
        "data" => $groupedIndicators['groupedIndicators'],
        "draw" => $draw,
        "recordsTotal"  =>  $groupedIndicators['pagination']['recordsTotal'],
        "recordsFiltered"  => $groupedIndicators['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

function getGroupedIndicatorsRequestModel() {
    $groupedIndicator = new IntranetGroupedIndicator();


    if (isset($_POST['indicator'])) {       
        $groupedIndicator->setIndicator(trim(filter_var($_POST['indicator'], FILTER_SANITIZE_STRING)));
    }


    //Research Area select multiple
    $researchArea = [];
    if (isset($_POST['researchArea'])) {
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }
    $groupedIndicator->setResearchArea($researchArea);

    //Research Program select multiple
    $researchProgram = [];
    if (isset($_POST['researchProgram'])) {
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }
    $groupedIndicator->setResearchProgram($researchProgram);

    //collaboration/Experiment select multiple
    $collaborationExperiment = [];
    if (isset($_POST['collaborationExperiment'])) {
        foreach ($_POST['collaborationExperiment'] as $item) {
            $collaborationExperiment[] = $item;
        }
    }
    $groupedIndicator->setInternationalConsortium($collaborationExperiment);

    //Research Program select multiple
    $researchGroup = [];
    if (isset($_POST['researchGroup'])) {
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }
    $groupedIndicator->setResearchGroup($researchGroup);

    //Work line select multiple
    $workLine = [];
    if (isset($_POST['workLine'])) {
        foreach ($_POST['workLine'] as $item) {
            $workLine[] = $item;
        }
    }
    $groupedIndicator->setWorkLine($workLine);

    $authors = [];
    if (isset($_POST['author'])) {
        foreach ($_POST['author'] as $item) {
            $authors[] = $item;
        }
    }
    $groupedIndicator->setAuthors($authors);

    //Publication date
    $groupedIndicator->setDate(trim(filter_var($_POST['date'], FILTER_SANITIZE_STRING)));


    return $groupedIndicator;
}

/** EXPORT */
add_action('admin_action_export_grouped_indicators', 'export_grouped_indicators');
function export_grouped_indicators()
{
    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $groupedIndicatorsRequest = getGroupedIndicatorsRequestModel();

   
    $headers = [__("Title"), __("Research area"), __("Research program"), __("International consortium"), __("Date")];
    

    $groupedIndicators = fetchGroupedIndicators($groupedIndicatorsRequest, 0, NULL)['groupedIndicators']; 

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($groupedIndicators, function (&$v, $k) {
        $v = get_object_vars($v); 
        if (!empty($v)){
            unset($v['ProductionProjectID']);
            unset($v['DOI']);
            unset($v['ResearchAreasIDs']);
            unset($v['ResearchProgramsIDs']);       
            unset($v['ExperimentsIDs']);            
            $v['Date'] = isset($v['Date']) ? formatDate($v['Date']) : "";            
        }  
    });
    
    

    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($groupedIndicators, $headers);

    //llamamos a la función común para exportar
    igfae_export($groupedIndicators, __('Grouped indicators'));
}