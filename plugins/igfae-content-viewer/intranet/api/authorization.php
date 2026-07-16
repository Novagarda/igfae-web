<?php

function getEntities()
{
    global $wp_query;
    $entities = getSelectEntities();
    $entity = "";
    $entitySelected = NULL;
    $entitySelect = NULL;

    if (isset($wp_query->query_vars['entity'])) {
        $entity = sanitize_title(urldecode($wp_query->query_vars['entity']));
    }
    foreach ($entities as $key => $item) {
        $entitySelect[$key] = $item;
        if ($entity == sanitize_title($item->getName())) {
            $entitySelected[$key] = $item;
        }
    }
    $entitySelect['items'] = $entitySelect;
    $entitySelect['selected'] = $entitySelected;
    return $entitySelect;
}

function getAuthorizationStatus()
{
    global $wp_query;
    $authorizations = getSelectAuthorizationStatus();
    $authorization = "";
    $authorizationSelected = NULL;
    $authorizationSelect = NULL;

    if (isset($wp_query->query_vars['authorizationStatusId'])) {
        $authorization = sanitize_title(urldecode($wp_query->query_vars['authorizationStatusId']));
    }
    foreach ($authorizations as $key => $item) {
        $authorizationSelect[$key] = $item;
        if ($authorization == sanitize_title($item->getStatus())) {
            $authorizationSelected[$key] = $item;
        }
    }
    $authorizationSelect['items'] = $authorizationSelect;
    $authorizationSelect['selected'] = $authorizationSelected;
    return $authorizationSelect;
}

add_action("wp_ajax_search_authorization_submit", "api_search_authorization_submit");
add_action("wp_ajax_nopriv_search_authorization_submit", "api_search_authorization_submit");
function api_search_authorization_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $authorizationRequest = getSearchAuthorizationRequestModel();

    $info = fetchAuthorizations($authorizationRequest, $offset, $length, $order);

    $data = [
        "data" => $info['authorizations'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

function getSearchAuthorizationRequestModel()
{
    //User select multiple
    $user         = [];
    if (isset($_POST['user'])) {
        foreach ($_POST['user'] as $item) {
            $user[] = $item;
        }
    }

    //Entity select multiple
    $entity         = [];
    if (isset($_POST['entity'])) {
        foreach ($_POST['entity'] as $item) {
            $entity[] = $item;
        }
    }

    //requestDate
    $requestDate = NULL;
    if (isset($_POST['requestDate'])) {
        $requestDate = trim(filter_var($_POST['requestDate'], FILTER_SANITIZE_STRING));
    }

    //authorizationDate
    $authorizationDate = NULL;
    if (isset($_POST['authorizationDate'])) {
        $authorizationDate = trim(filter_var($_POST['authorizationDate'], FILTER_SANITIZE_STRING));
    }

    //Status select multiple
    $status         = [];
    if (isset($_POST['status'])) {
        foreach ($_POST['status'] as $item) {            
            $status[] = $item;
        }
    }

    $authorizationRequest = new IntranetAuthorizationRequest(
        $user,
        $entity,
        $requestDate,
        $authorizationDate,
        $status
    );

    return $authorizationRequest;
}

// Change status
add_action("wp_ajax_change_status_submit", "api_change_status_submit");
add_action("wp_ajax_nopriv_change_status_submit", "api_change_status_submit");
function api_change_status_submit()
{

    // Comment  
    $comment         = NULL;
    if (isset($_POST['comment']) && !empty($_POST['comment'])) {
        $comment      = trim(filter_var($_POST['comment'], FILTER_SANITIZE_STRING));
    }

    //Status
    $status         = NULL;
    if (isset($_POST['status']) && !empty($_POST['status'])) {
        $status      = trim(filter_var($_POST['status'], FILTER_SANITIZE_STRING));
    }

    //Filas del datatable
    $filasSeleccionadas         = [];
    if (isset($_POST['filasSeleccionadas']) && !empty($_POST['filasSeleccionadas'])) {
        $filasSeleccionadas      = json_decode(stripcslashes($_POST['filasSeleccionadas']), true);
    }


    $authorizationList = [];
    foreach ($filasSeleccionadas as $item) {
        $authorization = new IntranetAuthorization(
            $item['authorizationID'],  //authorizationID
            NULL,  //requesterID
            NULL,  //requesterName
            NULL,  //entityNameID
            NULL,  //entityName
            NULL,  //entityID
            NULL,  //requestDate
            NULL,  //authorizationDate
            NULL,  //authorizerID
            NULL,  //authorizerBy
            $status,  //authorizationStatusID
            NULL,  //statusName
            NULL,  //requestComment
            $comment  //authorizationCommet 
        );
        $authorizationList[] = $authorization;
    }



    // Guardamos la autorización
    changeStatusAuthorization($authorizationList);
    wp_send_json_success();

    exit();
}


// New Authorization
add_action("wp_ajax_new_authorization_submit", "api_new_authorization_submit");
add_action("wp_ajax_new_authorization_submit", "api_new_authorization_submit");
function api_new_authorization_submit()
{
    // Comment  
    $comment         = NULL;
    if (isset($_POST['comments']) && !empty($_POST['comments'])) {
        $comment      = trim(filter_var($_POST['comments'], FILTER_SANITIZE_STRING));
    }

    // entityNameID  
    $entityNameID    = NULL;
    if (isset($_POST['entityNameID']) && !empty($_POST['entityNameID'])) {
        $entityNameID = trim(filter_var($_POST['entityNameID'], FILTER_SANITIZE_STRING));
    }

    // entityID  
    $entityID       = NULL;
    if (isset($_POST['entityID']) && !empty($_POST['entityID'])) {
        $entityID = trim(filter_var($_POST['entityID'], FILTER_SANITIZE_STRING));
    }   
    
    $requestDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);

    $user_id = get_current_user_id();
    $igfaeUser = getUserByWPUserID($user_id);
    $igfaeUserId = $igfaeUser[0]["id"];

    $data = array(
        'RequesterID' =>   $igfaeUserId,
        'EntityNameID' =>  $entityNameID,
        'EntityID' =>      $entityID,        
        'AuthorizationStatusID' =>  IGFAE_INTRANET_AUTHORIZATION_STATUS_PENDING,
        'RequestComment' =>  $comment,
        'RequestDate' => date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S)
    );

    // Guardamos la autorización
    saveAuthorization($data);
    wp_send_json_success();

    exit();
}

/** EXPORT */
add_action('admin_action_export_authorizations', 'export_authorizations');
function export_authorizations()
{
    $headers = [
        __("ID"), __("User"),   __("Entity name"), __("Entity ID"),
        __("Request date"), __("Authorization date"),  __("Authorizer by"),
        __("Status"), __("Request comment"), __("Authorization comment"),
        __("Apply date")

    ];

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $authorization = getSearchAuthorizationRequestModel();

    $authorizations = fetchAuthorizationsExport($authorization);
    

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($authorizations, function (&$v, $k) {
        $v = get_object_vars($v);
        if (!empty($v)) {
            $v['RequestDate'] = isset($v['RequestDate']) ? formatDateWithHour($v['RequestDate']) : "";
            $v['AuthorizationDate'] = isset($v['AuthorizationDate']) ? formatDateWithHour($v['AuthorizationDate']) : "";
            $v['ApplyDate'] = isset($v['ApplyDate']) ? formatDateWithHour($v['ApplyDate']) : "";
        }
    });
    if (!isset($authorizations)) {
        $authorizations = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($authorizations, $headers);

    //llamamos a la función común para exportar
    igfae_export($authorizations, __('Authorizations'));
}
