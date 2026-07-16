<?php
function getSelectAuthorizationStatus()
{
    $params = [];

    $sql = "SELECT 
                AuthorizationStatusID as 'id', 
                Status as 'status' 
            FROM  AuthorizationStatus
            ORDER BY  Status ASC";

    $rows = igfae_intranet_fetch_data($sql, $params);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $result[] = new IntranetAuthorizationStatus(
            $row->id,
            $row->status
        );
    }

    return $result;
}

function fetchAuthorizationsExport($authorizationRequest, $offset = 0, $amount = 10, $order = null)
{
    
    $where = "";
    $prepareParams = [];
    if (isset($authorizationRequest)) {
        $queryPrepare = getPrepareSearchAuthorizations($authorizationRequest);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }
    $order = getOrderAuthorizations($order);
    

    $sql = "select 
                a.AuthorizationID as ID,
                concat(requester.Name,  ' ', requester.FirstSurname, ' ', coalesce(requester.SecondSurname,'')) as RequesterName,
                en.Name as EntityName,
                a.EntityID,
                a.RequestDate,
                a.AuthorizationDate,
                concat(authorizer.Name,  ' ', authorizer.FirstSurname, ' ', coalesce(authorizer.SecondSurname,'')) as AuthorizerBy,
                ast.Status as StatusName,
                a.RequestComment,
                a.AuthorizationComment,
                a.ApplyDate
            from `Authorization` a
            inner join Personnel requester
                on requester.PersonnelID = a.RequesterID 
            left join Personnel authorizer
                on authorizer.PersonnelID = a.AuthorizerID 
            inner join EntityName en
                on en.EntityNameID = a.EntityNameID 
            inner join AuthorizationStatus ast
                on ast.AuthorizationStatusID = a.AuthorizationStatusID 
            WHERE     
                {$where} 
            ORDER BY {$order}";
    
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    return $rows['rows'];
}
function fetchAuthorizations($authorizationRequest, $offset = 0, $amount = 10, $order = null)
{
    $authorizations = [];
    $where = "";
    $prepareParams = [];
    if (isset($authorizationRequest)) {
        $queryPrepare = getPrepareSearchAuthorizations($authorizationRequest);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }
    $order = getOrderAuthorizations($order);
    

    $sql = "select 
                a.AuthorizationID,
                a.RequesterID,
                concat(requester.Name,  ' ', requester.FirstSurname, ' ', coalesce(requester.SecondSurname,'')) as RequesterName,
                en.EntityNameID,
                en.Name as EntityName,
                a.EntityID,
                a.RequestDate,
                a.AuthorizationDate,
                a.AuthorizerID,
                concat(authorizer.Name,  ' ', authorizer.FirstSurname, ' ', coalesce(authorizer.SecondSurname,'')) as AuthorizerBy,
                a.AuthorizationStatusID,
                ast.Status as StatusName,
                a.RequestComment,
                a.AuthorizationComment,
                a.ApplyDate
            from `Authorization` a
            inner join Personnel requester
                on requester.PersonnelID = a.RequesterID 
            left join Personnel authorizer
                on authorizer.PersonnelID = a.AuthorizerID 
            inner join EntityName en
                on en.EntityNameID = a.EntityNameID 
            inner join AuthorizationStatus ast
                on ast.AuthorizationStatusID = a.AuthorizationStatusID 
            WHERE     
                {$where} 
            ORDER BY {$order}";
    $pagination = [];
    if (isset($amount)) {
        $pagination = getPaginationInfoAuthorizations($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }
    
    $rows = igfae_intranet_fetch_data($sql, $prepareParams);

    foreach ($rows['rows'] as $row) {
        $authorizations[] = new IntranetAuthorization(
            $row->AuthorizationID,
            $row->RequesterID,
            $row->RequesterName,
            $row->EntityNameID,
            $row->EntityName,
            $row->EntityID,
            $row->RequestDate,
            $row->AuthorizationDate,
            $row->AuthorizerID,
            $row->AuthorizerBy,
            $row->AuthorizationStatusID,
            $row->StatusName,
            $row->RequestComment,
            $row->AuthorizationComment,
            $row->ApplyDate
        );
    }

    $info = [
        'authorizations' => $authorizations,
        'pagination' => $pagination
    ];

    return $info;
}

function getPrepareSearchAuthorizations($authorizationRequest)
{
    $where = "1=1 ";
    $params = [];
   
    if (!getCapacibilityValidationUser()) {
        $user_id = get_current_user_id();
        $igfaeUserId = getUserByWPUserID($user_id);
        if (!empty($igfaeUserId)) {
            $where .= " AND (a.RequesterID  = %d)";
            $params[] =  $igfaeUserId[0]["id"];
        }
    }
    // User select multiple
    if ($authorizationRequest->getUser()) {
        $typesCount = count($authorizationRequest->getUser());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAux = implode(',', $paramsPlaceholders);

        $where .= " AND (a.RequesterID IN ({$placeholdersAux}))";
        $params = array_merge($params, $authorizationRequest->getUser());
    }

    // Entity select multiple
    if ($authorizationRequest->getEntity()) {
        $typesCount = count($authorizationRequest->getEntity());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAux = implode(',', $paramsPlaceholders);

        $where .= " AND (a.EntityNameID IN ({$placeholdersAux}))";
        $params = array_merge($params, $authorizationRequest->getEntity());
    }

    // requestDate date input
    if ($authorizationRequest->getRequestDate()) {
        $startDates =  explode("-", $authorizationRequest->getRequestDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (a.RequestDate BETWEEN %s and %s )";

        $params[] = $fecha1;
        $params[] = $fecha2;
    }

    // authorizationDate date input
    if ($authorizationRequest->getAuthorizationDate()) {
        $startDates =  explode("-", $authorizationRequest->getAuthorizationDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (a.AuthorizationDate BETWEEN %s and %s )";

        $params[] = $fecha1;
        $params[] = $fecha2;
    }

    // Status select multiple
    if ($authorizationRequest->getStatus()) {
        $typesCount = count($authorizationRequest->getStatus());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersAux = implode(',', $paramsPlaceholders);

        $where .= " AND (a.AuthorizationStatusID IN ({$placeholdersAux}))";
        $params = array_merge($params, $authorizationRequest->getStatus());
    }

    return array(
        "where" => $where,
        "params" => $params
    );
}

function getOrderAuthorizations($order)
{
    
    if (!isset($order)) { 
        return ' a.RequestDate DESC';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0: //RequestDate
                $sort .= 'a.RequestDate ';
                break;
            case 1:  //User
                $sort .= 'RequesterName ';
                break;
            case 2: //Entity
                $sort .= 'EntityName ';
                break;
            case 4: //Request date
                $sort .= 'RequestDate ';
                break;
            case 5: //Authorized by
                $sort .= 'AuthorizerBy ';
                break;
            case 6: //Status
                $sort .= 'StatusName ';
                break;
            case 7: //Request comment
                $sort .= 'RequestComment ';
                break;
            case 8: //Auhtorized comment
                $sort .= 'AuthorizationComment ';
                break;
            default:
                $sort .= 'a.RequestDate ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}


function getPaginationInfoAuthorizations($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];


    return $result;
}

function changeStatusAuthorization($authorizationList)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();

    foreach ($authorizationList as $item) {
        $user_id = get_current_user_id();
        $igfaeUserId = getUserByWPUserID($user_id);

        $where = array('AuthorizationID' => $item->getAuthorizationID());

        $datosInsert = array(
            'AuthorizationStatusID' => $item->getAuthorizationStatusID(),
            'AuthorizationComment' => $item->getAuthorizationComment(),            
            'AuthorizationDate' => date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S)
        );
        if (!empty($igfaeUserId)) {
            $datosInsert['AuthorizerID'] = $igfaeUserId[0]["id"];
        }

        igfae_intranet_update("Authorization", $datosInsert, $where, null, null);
    }
    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }
}


function saveAuthorization($authorization)
{
    /** START TRANSACTION */
    igfae_intranet_begin_transaction();
    
    igfae_intranet_insert("Authorization", $authorization, null);

    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
        return true;
    } else {
        igfae_intranet_rollback_transaction();
        return false;
    }
}



function hasAuthorizedAuthorization($user_id, $entityNameId, $entity_id) {

    $sqlHasRelation = "select a.AuthorizationID 
        from `Authorization` a
        where 1=1
        and a.AuthorizationStatusID = 2 -- Autorizada
        and a.EntityNameID = $entityNameId
        and a.RequesterID = $user_id
        and a.EntityID = $entity_id
    ;";       

    
    $result = igfae_intranet_fetch_data($sqlHasRelation);

    if ($result['count']) {
        return true;
    } else {
        return false;
    }
}

function closeAuthorizedAuthorizationForResource($entityNameId, $entity_id) {
    $user_id = get_current_user_id();
    $igfaeUserId = getUserByWPUserID($user_id)[0]["id"];

    $where = array(
        'AuthorizationStatusID' => IGFAE_INTRANET_AUTHORIZATION_STATUS_AUTHORIZED,
        'EntityNameID' => $entityNameId,
        'RequesterID' => $igfaeUserId,
        'EntityID' =>  $entity_id
    );

    $datosInsert = array(
        'AuthorizationStatusID' => IGFAE_INTRANET_AUTHORIZATION_STATUS_CLOSED,
        'ApplyDate' => date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S)
    );

    igfae_intranet_update("Authorization", $datosInsert, $where, null, null);    
}

function getLastAuthorizationForResource($entityNameId, $entity_id) {

    $sqlHasRelation = "select * 
        from `Authorization` a
        where 1=1
        and a.EntityNameID = $entityNameId
        and a.EntityID = $entity_id
        order by a.RequestDate DESC
        limit 1
    ;";       
    
    $result = igfae_intranet_fetch_data($sqlHasRelation);

    if ($result['count']) {
        $rows = $result['rows'];
        $authorization = new IntranetAuthorization(
            $rows[0]->AuthorizationID,
            $rows[0]->RequesterID,
            NULL,
            $rows[0]->EntityNameID,
            NULL,
            $rows[0]->EntityID,
            $rows[0]->RequestDate,
            $rows[0]->AuthorizationDate,
            $rows[0]->AuthorizerID,
            NULL,
            $rows[0]->AuthorizationStatusID,
            NULL,
            $rows[0]->RequestComment,
            $rows[0]->AuthorizationComment
        );

        return $authorization;
              
    } else {
        return null;
    }
}
