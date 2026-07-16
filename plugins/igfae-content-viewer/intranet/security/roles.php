<?php 

$constantPrefix = 'IGFAE_INTRANET_';
defined( $constantPrefix . 'ROLE_INVESTIGADOR_GRUPO' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR_GRUPO', 'investigador_principal_grupo' );
defined( $constantPrefix . 'ROLE_INVESTIGADOR_PROYECTO' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR_PROYECTO', 'investigador_principal_proyecto' );
defined( $constantPrefix . 'ROLE_INVESTIGADOR_REFERENCIA' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR_REFERENCIA', 'investigador_referencia' );
defined( $constantPrefix . 'ROLE_INVESTIGADOR' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR', 'investigador' );
defined( $constantPrefix . 'ROLE_GESTION' ) or define( $constantPrefix . 'ROLE_GESTION', 'gestion' );
defined( $constantPrefix . 'ROLE_COLABORADOR' ) or define( $constantPrefix . 'ROLE_COLABORADOR', 'colaborador' );
defined( $constantPrefix . 'ROLE_VISITANTE_TEMPORAL' ) or define( $constantPrefix . 'ROLE_VISITANTE_TEMPORAL', 'visitante_temporal' );
defined( $constantPrefix . 'ROLE_EDITOR_DNI' ) or define( $constantPrefix . 'ROLE_EDITOR_DNI', 'editor_dni' );
defined( $constantPrefix . 'ROLE_MONITOR' ) or define( $constantPrefix . 'ROLE_MONITOR', 'monitor' );
defined( $constantPrefix . 'ROLE_VALIDADOR' ) or define( $constantPrefix . 'ROLE_VALIDADOR', 'validador' );
defined( $constantPrefix . 'ROLE_SUBSCRIBER' ) or define( $constantPrefix . 'ROLE_SUBSCRIBER', 'subscriber' );

defined( $constantPrefix . 'ROLE_INVESTIGADOR_GRUPO_LABEL' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR_GRUPO_LABEL', 'Group principal researcher' );
defined( $constantPrefix . 'ROLE_INVESTIGADOR_PROYECTO_LABEL' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR_PROYECTO_LABEL', 'Project principal researcher' );
defined( $constantPrefix . 'ROLE_INVESTIGADOR_REFERENCIA_LABEL' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR_REFERENCIA_LABEL', 'Reference researcher' );
defined( $constantPrefix . 'ROLE_INVESTIGADOR_LABEL' ) or define( $constantPrefix . 'ROLE_INVESTIGADOR_LABEL', 'Researcher' );
defined( $constantPrefix . 'ROLE_GESTION_LABEL' ) or define( $constantPrefix . 'ROLE_GESTION_LABEL', 'Management' );
defined( $constantPrefix . 'ROLE_COLABORADOR_LABEL' ) or define( $constantPrefix . 'ROLE_COLABORADOR_LABEL', 'Partner' );
defined( $constantPrefix . 'ROLE_VISITANTE_TEMPORAL_LABEL' ) or define( $constantPrefix . 'ROLE_VISITANTE_TEMPORAL_LABEL', 'Time visitor' );
defined( $constantPrefix . 'ROLE_EDITOR_DNI_LABEL' ) or define( $constantPrefix . 'ROLE_EDITOR_DNI_LABEL', 'DNI editor' );
defined( $constantPrefix . 'ROLE_MONITOR_LABEL' ) or define( $constantPrefix . 'ROLE_MONITOR_LABEL', 'Monitor' );
defined( $constantPrefix . 'ROLE_VALIDADOR_LABEL' ) or define( $constantPrefix . 'ROLE_VALIDADOR_LABEL', 'Validator' );


function create_roles(){
    //hacemos comprobacion para no escribir siempre en bbdd
    $role_version = get_option( 'igfae_roles_version' );
    if ( isset($role_version) && $role_version < 14 ) {

        remove_role(IGFAE_INTRANET_ROLE_EDITOR_DNI);
        add_role( IGFAE_INTRANET_ROLE_EDITOR_DNI, __(IGFAE_INTRANET_ROLE_EDITOR_DNI_LABEL), EDITOR_DNI_CAPACIBILITIES);      
    
        remove_role(IGFAE_INTRANET_ROLE_MONITOR);
        add_role( IGFAE_INTRANET_ROLE_MONITOR, __(IGFAE_INTRANET_ROLE_MONITOR_LABEL), MONITOR_CAPACIBILITIES);
    
        remove_role(IGFAE_INTRANET_ROLE_VALIDADOR);
        add_role(IGFAE_INTRANET_ROLE_VALIDADOR, __(IGFAE_INTRANET_ROLE_VALIDADOR_LABEL), VALIDADOR_CAPACIBILITIES);  
    
        remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO);
        add_role( IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO, __(IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO_LABEL), IP_GRUPO_CAPACIBILITIES);
        
        remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO);
        add_role( IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO, __(IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO_LABEL), IP_PROYECTO_CAPACIBILITIES);	
        
        remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA);
        add_role( IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA, __(IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA_LABEL), INVESTIGADOR_REFERENCIA_CAPACIBILITIES);
        
        remove_role(IGFAE_INTRANET_ROLE_INVESTIGADOR);
        add_role( IGFAE_INTRANET_ROLE_INVESTIGADOR, __(IGFAE_INTRANET_ROLE_INVESTIGADOR_LABEL), INVESTIGADOR_CAPACIBILITIES);	    
        
        remove_role(IGFAE_INTRANET_ROLE_GESTION);
        add_role( IGFAE_INTRANET_ROLE_GESTION, __(IGFAE_INTRANET_ROLE_GESTION_LABEL), GESTION_CAPACIBILITIES);
        
        remove_role(IGFAE_INTRANET_ROLE_COLABORADOR);
        add_role( IGFAE_INTRANET_ROLE_COLABORADOR, __(IGFAE_INTRANET_ROLE_COLABORADOR_LABEL), COLABORADOR_CAPACIBILITIES);	    
        
        remove_role(IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL);
        add_role( IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL, __(IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL_LABEL), VISITANTE_TEMPORAL_CAPACIBILITIES);    
    
        update_option( 'igfae_roles_version', 14 );
    }
}

add_action('init', 'create_roles');