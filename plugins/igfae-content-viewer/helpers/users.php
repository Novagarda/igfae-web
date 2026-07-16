<?php

/**
 * Obtiene los roles que tiene un usuario
 * @param: $user_id: id del usuario
 * @return: array()
 */
function get_user_roles_by_user_id($user_id)
{
    $user = get_userdata($user_id);
    return empty($user) ? array() : $user->roles;
}

/**
 * Comprueba si el usuario pertenece a un rol
 * @param: $user_id: id del usuario
 * @param: $user_id: id del usuario
 * @return: boolean
 */
function is_user_in_role( $user_id, $role  ) {
    return in_array( $role, get_user_roles_by_user_id( $user_id ) );
}

/**
 * Determina si un usuario puede ejecutar una acción.
 * Ejemplo:
 * 	if (igfae_user_can( 25, IGFAE_INTRANET_CAPACIBILITY_USERS_DELETE )) {
 *	    	error_log('Puede borrar: ');
 *     } else {
 *		    error_log('No Puede borrar: ');
 *	    }
 * @param: $user_id: id del usuario,
 * @param: $capability: nombre de la acción
 * @return: boolean
 */
function igfae_user_can($user_id, $capability)
{
    return user_can($user_id, $capability);
}


function igfae_user_has_permissions($callbacks)
{
    $result = true;
    foreach ($callbacks as $callback) {
        $result = $result && call_user_func_array(...$callback);        
    }
    return $result;  
}

function igfae_user_has_authorization_for_resource($type, $id)
{
    $user_id = get_current_user_id();
    $igfaeUser = getUserByWPUserID($user_id);
    $igfaeUserId = $igfaeUser[0]["id"];    
    $result = hasAuthorizedAuthorization($igfaeUserId, $type, $id);
    //error_log("Tiene autorizacion? ". $result);
    return $result;  
}

function check_permissions($callbacks, $redirect = NULL) {
    if (!igfae_user_has_permissions($callbacks)) {
        
        if (!isset($redirect)) {
            $redirect = site_url("/home");
        }

        intranet_get_template_part('commons', '404', '', array( 
            "error" =>  __("You are not allowed to access this page. Please contact the IGFAE Intranet administrator"),
            "redirect" => $redirect
        ));        
        exit();
    }
}

function check_authorization($type, $id , $redirect = NULL) {
    $user_id = get_current_user_id();
    if (!isset($redirect)) {
        $redirect = site_url("/home");
    } 

    if (            
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_VALIDADOR) && 
        (
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR) ||
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_GESTION) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_COLABORADOR) || 
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL) 
        )
    ){
        if (isset($_GET['f'])) {
            $forceAuthorizationDialog = $_GET['f'];
        }
                
        $hasAuthorization = igfae_user_has_authorization_for_resource($type, $id);
        
        if (!$hasAuthorization) {

            //obtenemos autorizacion para ver que hacemos en funcion del estado
            $authorization = getLastAuthorizationForResource($type, $id);

            if (empty($authorization)) { //no existe autorización. Mostramos popup de solicitud
                showAuthorizationDialog($type, $id, $redirect);
            } else if (!empty($authorization)) {
                $user_id = get_current_user_id();
                $igfaeUser = getUserByWPUserID($user_id);
                $igfaeUserId = $igfaeUser[0]["id"];
                if ($authorization->getRequesterID() != $igfaeUserId && 
                    $authorization->getAuthorizationStatusID() != IGFAE_INTRANET_AUTHORIZATION_STATUS_REJECTED && 
                    $authorization->getAuthorizationStatusID() != IGFAE_INTRANET_AUTHORIZATION_STATUS_CLOSED
                ) { //si el usuario no es el mismo y el estado es distinto de rechazada o cerrada mostrar error
                    intranet_get_template_part('commons', '404', '', array( 
                        "error" =>  __("There's already an active editing authorization for another user"),
                        "redirect" => $redirect
                    )); 
                    exit();
                } else if (
                    $authorization->getRequesterID() != $igfaeUserId && 
                    (
                        $authorization->getAuthorizationStatusID() == IGFAE_INTRANET_AUTHORIZATION_STATUS_REJECTED ||
                        $authorization->getAuthorizationStatusID() == IGFAE_INTRANET_AUTHORIZATION_STATUS_CLOSED
                    )
                ) { // existe autorización rechazada o cerrada de otro usuario
                    showAuthorizationDialog($type, $id, $redirect); 
                } else if (
                    $authorization->getRequesterID() == $igfaeUserId && 
                    $authorization->getAuthorizationStatusID() == IGFAE_INTRANET_AUTHORIZATION_STATUS_PENDING
                ) { // solicitud pendiente. Mostrar mensaje pendiente y boton volver página                    
                    intranet_get_template_part('commons', 'pending-authorization', '', array( 
                        "redirect" => $redirect
                    ));
                    exit();                   
                } else if (
                    $authorization->getRequesterID() == $igfaeUserId && 
                        ((
                            $authorization->getAuthorizationStatusID() == IGFAE_INTRANET_AUTHORIZATION_STATUS_REJECTED && 
                            isset($forceAuthorizationDialog) && 
                            $forceAuthorizationDialog == 1
                        ) || 
                        $authorization->getAuthorizationStatusID() == IGFAE_INTRANET_AUTHORIZATION_STATUS_CLOSED)
                    ) { // solicitud rechazada y forceDialog = 1 o solicitud anterior cerrada. Venimos de pulsar el botón aceptar del último caso
                    showAuthorizationDialog($type, $id, $redirect);
                } else if (
                    $authorization->getRequesterID() == $igfaeUserId &&
                    $authorization->getAuthorizationStatusID() == IGFAE_INTRANET_AUTHORIZATION_STATUS_REJECTED
                ) { // solicitud rechazada. Mostrar mensaje y boton que abre dialogo
                    intranet_get_template_part('commons', 'rejected-authorization', '', array( 
                        "entityNameID" =>  $type,
                        "entityID" => $id,
                        "comments" => $authorization->getAuthorizationComment(),               
                        "redirect" => $redirect
                    ));
                    exit();                      
                }
            }
        }
    }
}

function showAuthorizationDialog($entityNameID, $entityID, $redirect) {

    intranet_get_template_part('commons', 'authorization', '', array( 
        "entityNameID" =>  $entityNameID,
        "entityID" => $entityID,
        "redirect" => $redirect
    )); 
    exit(); 
}


/**
 * Obtiene todos los roles disponibles 
 * esGestion: Indicador de si el usuario tiene el rol de gestion en cuyo caso se devuelve una lista mas amplia de roles. Esto
 *    permite en el buscador de usuarios restringir los valores del combo (solo en este caso concreto)
 * @return: array()
 */
function get_all_roles($esGestion = true)
{
    $available_roles = array();
    $available_roles[IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO] = IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO_LABEL;
    $available_roles[IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO] = IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO_LABEL;
    $available_roles[IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA] = IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA_LABEL;
    $available_roles[IGFAE_INTRANET_ROLE_INVESTIGADOR] = IGFAE_INTRANET_ROLE_INVESTIGADOR_LABEL;
    $available_roles[IGFAE_INTRANET_ROLE_GESTION] = IGFAE_INTRANET_ROLE_GESTION_LABEL;
    $available_roles[IGFAE_INTRANET_ROLE_COLABORADOR] = IGFAE_INTRANET_ROLE_COLABORADOR_LABEL;
    $available_roles[IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL] = IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL_LABEL;
    if ($esGestion == true) {
        $available_roles[IGFAE_INTRANET_ROLE_EDITOR_DNI] = IGFAE_INTRANET_ROLE_EDITOR_DNI_LABEL;
        $available_roles[IGFAE_INTRANET_ROLE_MONITOR] = IGFAE_INTRANET_ROLE_MONITOR_LABEL;
        $available_roles[IGFAE_INTRANET_ROLE_VALIDADOR] = IGFAE_INTRANET_ROLE_VALIDADOR_LABEL;
    }
    return $available_roles;
}

/**
 * Envía un correo de reseteo de password al usuario
 * @param: $user_id: id del usuario
 */

function igfae_send_password_reset_mail($user_id){

    $user = get_user_by('id', $user_id);
    $firstname = $user->first_name;
    $email = $user->user_email;
    $rp_key = get_password_reset_key( $user );
    $user_login = $user->user_login;
	$rp_link = '<a href="' . network_site_url("wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode($user_login), 'login') . '">' . network_site_url("wp-login.php?action=rp&key=$rp_key&login=" . rawurlencode($user_login), 'login') . '</a>';

    $message = "Dear ".$firstname.",<br>";
    $message .= "A new account has been created on the IGFAE's intranet associated to the following email address: ".$email."<br><br>";
    $message .= "Please follow the link below to set the password of your new account: <br>";
    $message .= $rp_link.'<br><br>';
    $message .= "Should you experience any issue with your account please contact us at it.support@igfae.usc.es <mailto:it.support@igfae.usc.es>";


	$subject = __("Please set a password for your IGFAE's intranet account");
    $headers = array('From: IGFAE <noreply@igfae.usc.es>');

   	add_filter( 'wp_mail_content_type', function( $content_type ) {return 'text/html';});
   	wp_mail( $email, $subject, $message, $headers);

   	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
}

/**
 * Obtenemos el listado de todos los usuarios con sus roles
 */
function get_all_user_all_roles()
{
    $roles = array(IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO, IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO, IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA,
    IGFAE_INTRANET_ROLE_INVESTIGADOR, IGFAE_INTRANET_ROLE_GESTION, IGFAE_INTRANET_ROLE_COLABORADOR, IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL,
    IGFAE_INTRANET_ROLE_EDITOR_DNI, IGFAE_INTRANET_ROLE_MONITOR, IGFAE_INTRANET_ROLE_VALIDADOR);
    $users = array();
    foreach ($roles as $role) {
        $args = array(
            'role' => $role
        );
        $current_role_users = get_users($args);
        $users = array_merge($current_role_users, $users);
    }

    return $users;
}

