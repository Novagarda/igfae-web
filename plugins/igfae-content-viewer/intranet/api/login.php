<?php

/**
 * Valida si el usuario, una vez autenticado dispone de suficientes permisos para 
 * acceder a la Intranet de IGFAE
 */
function igfae_custom_validate_auth($user, $username, $password){
    // Esto solo aplica sobre la Intranet
    if ($user != null && !is_wp_error($user) && in_array('login-intranet', explode('/',wp_get_referer()))) {
        // Roles del usuario
        $roles = get_user_roles_by_user_id($user->ID);
        // Roles de Intranet IGFAE
        $namesRoles    = get_all_roles();
        // Roles generales WP que son admitidos para realizar autenticacion
        $namesRoles["administrator"] = "administrator";
        $namesRoles["editor"] = "editor";
        $namesRoles["author"] = "author";
        $namesRoles["contributor"] = "contributor";
        $namesRoles["subscriber"] = "subscriber";
        $namesRoles["customer"] = "customer";

        $arrayRoles = array();
        if (isset($roles)) {
            // Se verifica si alguno de los roles del usuario permite la autenticacion en la Intranet de IGFAE:
            //  o un rol propio de la Intranet de IGFAE
            //  o el rol "administrator" de Wordpress
            foreach ($roles as $rol) {
                if (isset($namesRoles[$rol])) {
                    $arrayRoles[] = $namesRoles[$rol];
                }
            }
            if (sizeof($arrayRoles) == 0) {
                $user = new WP_Error(
                    IGFAE_INTRANET_LOGIN_ERROR_CODE_NO_IGFAE_ROLES,
                    sprintf(
                        __( 'User has no one IGFAE roles.' )
                    )
                );
            }
            $tieneRolWordpress = in_array("administrator", $arrayRoles) ||
                in_array("editor", $arrayRoles) ||
                in_array("author", $arrayRoles) || 
                in_array("contributor", $arrayRoles) ||
                in_array("subscriber", $arrayRoles) ||
                in_array("customer", $arrayRoles);

            if (!$tieneRolWordpress  && isPersonnelValidated($user->ID) != '1') {
                $user = new WP_Error(
                    IGFAE_INTRANET_LOGIN_ERROR_CODE_NOT_VALIDATED,
                    sprintf(
                        __( 'User is not validated.' )
                    )
                );    
            }
        } else {
            $user = new WP_Error(
                IGFAE_INTRANET_LOGIN_ERROR_CODE_NO_ROLES,
                sprintf(
                    __( 'User has no one roles.' )
                )
            );
        }
    }
    return $user;
}
add_filter('authenticate', 'igfae_custom_validate_auth', 25, 3);

/**
 * Redirecciona al usuario en el caso de autenticacion fallida indicando el motivo para la vista
 */
function my_login_redirect( $redirect_to, $request, $user ) {
    if (is_wp_error($user)) {
        $error_types = array_keys($user->errors);
        // Cogemos el primero de los errores
        if (is_array($error_types) && !empty($error_types)) {
            $error_type = $error_types[0];
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer = $_SERVER['HTTP_REFERER'];    
            if( !empty( $referrer ) && !strstr( $referrer,'wp-login' ) && !strstr( $referrer,'wp-admin' ) ) {
                wp_redirect( site_url('/login-intranet') . "?login=failed&reason=" . $error_type ); 
                exit;
            }
        }
    } else {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer = $_SERVER['HTTP_REFERER'];    
            if( !empty( $referrer ) && !strstr( $referrer,'wp-login' ) && !strstr( $referrer,'wp-admin' ) ) {
                wp_redirect( site_url('/login-intranet') . "?login=failed" ); 
                exit;
            }
        }
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'my_login_redirect', 30, 3 );
