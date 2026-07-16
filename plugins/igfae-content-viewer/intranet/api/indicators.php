<?php

/**
 * Get indicators types
 */
function getIndicatorTypes($type)
{
    global $wp_query;    
    $indicators = getSelectIndicatorTypes($type);
    $indicator = "";
    $indicatorSelect = NULL;
    $indicatorsSelect = NULL;

    if (isset($wp_query->query_vars['indicator'])) {
        $indicator = sanitize_title(urldecode($wp_query->query_vars['indicator']));
    }
    foreach ($indicators as $key => $item) {
        $indicatorsSelect[$key] = $item;
        if ($indicator == sanitize_title($item->getIndicatorName())) {
            $indicatorSelect[$key] = $item;
        }
    }
    $indicatorsSelect['items'] = $indicatorsSelect;
    $indicatorsSelect['selected'] = $indicatorSelect;
    return $indicatorsSelect;
}

/**
 * Get intervals types
 */
function getIntervalsByType($typeIndicator)
{
    global $wp_query;    
    $intervals = getSelectIntervalsByType($typeIndicator);
    $interval = "";
    $intervalSelect = NULL;
    $intervalsSelect = NULL;

    if (isset($wp_query->query_vars['interval'])) {
        $interval = sanitize_title(urldecode($wp_query->query_vars['interval']));
    }
    foreach ($intervals as $key => $item) {
        $intervalsSelect[$key] = $item;
        if ($interval == sanitize_title($item->getIntervalName())) {
            $intervalSelect[$key] = $item;
        }
    }
    $intervalsSelect['items'] = $intervalsSelect;
    $intervalsSelect['selected'] = $intervalSelect;
    return $intervalsSelect;
}


add_action("wp_ajax_search_indicators_submit", "api_search_indicators_submit");
add_action("wp_ajax_nopriv_search_indicators_submit", "api_search_indicators_submit");

function api_search_indicators_submit() {

    $draw = isset($_POST['draw'])? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)):0;
    $length = isset($_POST['length'])? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)): 10;
    $offset = isset($_POST['start'])? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)): 0;
    $order = isset($_POST['order'])? $_POST['order']: null;

    $indicatorsRequest = getIndicatorsRequestModel();
    
    $indicators = fetchIndicators($indicatorsRequest, $offset, $length, $order);    
   
    $data = [
        "data" => $indicators['indicators'],
        "draw" => $draw,
        "recordsTotal"  =>  $indicators['pagination']['recordsTotal'],
        "recordsFiltered"  => $indicators['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

function getIndicatorsRequestModel() {
    $indicatorsRequest = new IntranetIndicator();


    if (isset($_POST['indicator'])) {       
        $indicatorsRequest->setIndicator(trim(filter_var($_POST['indicator'], FILTER_SANITIZE_STRING)));
    }

    if (isset($_POST['citationsArticle'])) {       
        $indicatorsRequest->setRangeArticle(trim(filter_var($_POST['citationsArticle'], FILTER_SANITIZE_STRING)));
    }

    if (isset($_POST['indexH'])) {       
        $indicatorsRequest->setRangeIndexH(trim(filter_var($_POST['indexH'], FILTER_SANITIZE_STRING)));
    }

    //Research Area select multiple
    $researchArea = [];
    if (isset($_POST['researchArea'])) {
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }
    $indicatorsRequest->setResearchArea($researchArea);

    //Research Program select multiple
    $researchProgram = [];
    if (isset($_POST['researchProgram'])) {
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }
    $indicatorsRequest->setResearchProgram($researchProgram);

    //collaboration/Experiment select multiple
    $collaborationExperiment = [];
    if (isset($_POST['collaborationExperiment'])) {
        foreach ($_POST['collaborationExperiment'] as $item) {
            $collaborationExperiment[] = $item;
        }
    }
    $indicatorsRequest->setCollaboration($collaborationExperiment);

    //collaboration/Experiment select multiple
    $externalCollaboration = [];
    if (isset($_POST['externalCollaboration'])) {
        foreach ($_POST['externalCollaboration'] as $item) {           
            $externalCollaboration[] = $item;
        }
    }
    $indicatorsRequest->setExternalCollaboration($externalCollaboration);

    //Work line select multiple
    $workLine = [];
    if (isset($_POST['workLine'])) {
        foreach ($_POST['workLine'] as $item) {
            $workLine[] = $item;
        }
    }
    $indicatorsRequest->setWorkLine($workLine);

    //Work line select multiple
    $authors = [];
    if (isset($_POST['author'])) {
        foreach ($_POST['author'] as $item) {
            $authors[] = $item;
        }
    }
    $indicatorsRequest->setAuthors($authors);


    //Publication date
    $indicatorsRequest->setPublicationDate(trim(filter_var($_POST['publicationDate'], FILTER_SANITIZE_STRING)));

    //Update date
    $indicatorsRequest->setUpdateDate(trim(filter_var($_POST['updateDate'], FILTER_SANITIZE_STRING)));

    //Magazine select multiple
    $magazine = [];
    if (isset($_POST['magazine'])) {
        foreach ($_POST['magazine'] as $item) {
            $magazine[] = $item;
        }
    }
    $indicatorsRequest->setMagazine($magazine);

    if (isset($_POST['source'])) {       
        $indicatorsRequest->setSource(trim(filter_var($_POST['source'], FILTER_SANITIZE_STRING)));
    }

    return $indicatorsRequest;
}

/** EXPORT */
add_action('admin_action_export_indicators', 'export_indicators');
function export_indicators()
{
    $header;
    

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $indicatorsRequest = getIndicatorsRequestModel();

    if ($indicatorsRequest -> getIndicator() == IGFAE_INTRANET_INDICATOR_INDEX_H ) {
        if ($indicatorsRequest -> getMagazine()) {
            $headers = [__("H Index"), __("Journal")];
        }else {
            $headers = [__("H Index"), __("Personnel")];
        }        
    } else if ($indicatorsRequest -> getIndicator() == IGFAE_INTRANET_INDICATOR_GARANTS) {
        $headers = [__("Value"), __("Personnel"),__("External source")];
        
    } else {
        $headers = [
            __("Title"), __("Doi"), __("Cites"), __("Research area"), __("Research program"), __("International consortium"), __("Research group"), __("Activity"),
            __("Authors igfae"), __("Publication date"), __("Update date")
        ];
    }

    $indicators = fetchIndicators($indicatorsRequest, 0, NULL)['indicators']; 

    if ($indicatorsRequest -> getIndicator() == IGFAE_INTRANET_INDICATOR_INDEX_H ) {
        if ($indicatorsRequest -> getMagazine()) {
            array_walk($indicators, function (&$v, $k) {
                $v = get_object_vars($v); 
                if (!empty($v)){                 
                    unset($v['gindex']);
                    unset($v['personnelID']);
                    unset($v['name']);
                    unset($v['externalSourceID']); 
                    unset($v['source']); 
                    $v = array($v['indexH'] , $v['magazine']);
                }  
            });
        }else {
            array_walk($indicators, function (&$v, $k) {
                $v = get_object_vars($v); 
                if (!empty($v)){
                    unset($v['gindex']);
                    unset($v['personnelID']);
                    unset($v['name']);
                    unset($v['externalSourceID']);
                    unset($v['source']);
                    $v = array($v['indexH'] , $v['authors']);
                }
            });

        }
    } else if ($indicatorsRequest -> getIndicator() == IGFAE_INTRANET_INDICATOR_GARANTS ) {
        //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
        array_walk($indicators, function (&$v, $k) {
            $v = get_object_vars($v); 
            if (!empty($v)){
                unset($v['indicator']);
                unset($v['rangeArticle']);
                unset($v['rangeIndexH']);
                unset($v['externalCollaboration']);
                unset($v['magazine']);
                unset($v['source']);
                unset($v['title']);
                unset($v['doi']);
                unset($v['cites']);
                unset($v['researchArea']);
                unset($v['researchProgram']);
                unset($v['collaboration']);
                unset($v['researchGroup']);
                unset($v['workLine']);
                unset($v['authors']);
                unset($v['publicationDate']);
                unset($v['updateDate']);
                unset($v['indexH']);
                unset($v['personnelID']);
            }  
        });
    } else {
        //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
        array_walk($indicators, function (&$v, $k) {
            $v = get_object_vars($v); 
            if (!empty($v)){
                unset($v['indicator']);
                unset($v['rangeArticle']);
                unset($v['rangeIndexH']);
                unset($v['externalCollaboration']);       
                unset($v['magazine']);
                unset($v['indexH']);
                unset($v['gindex']);
                unset($v['personnelID']);
                unset($v['name']);
                unset($v['externalSourceID']);
                unset($v['source']);
                $v['publicationDate'] = isset($v['publicationDate']) ? formatDate($v['publicationDate']) : "";
                $v['updateDate'] = isset($v['updateDate']) ? formatDate($v['updateDate']) : "";
            }  
        });
    }
    

    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($indicators, $headers);

    //llamamos a la función común para exportar
    igfae_export($indicators, __('Indicators'));
}