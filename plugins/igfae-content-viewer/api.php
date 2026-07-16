<?php
add_action("wp_ajax_igfae_save_settings", "api_igfae_save_admin_settings");	
function api_igfae_save_admin_settings() 
{	
    $parameters = $_POST;
	$callback = strval($parameters["callback"]);
    $flushEventsCache = isset($parameters['igfae_flush_events_cache']) && (string) $parameters['igfae_flush_events_cache'] === '1';
	    	
    update_option('igfae_db_name', $parameters['igfae_db_name']);
    update_option('igfae_db_user', $parameters['igfae_db_user']);
    update_option('igfae_db_pass', $parameters['igfae_db_pass']);
    update_option('igfae_db_host', $parameters['igfae_db_host']);

    update_option('igfae_db_intranet_name', $parameters['igfae_db_intranet_name']);
    update_option('igfae_db_intranet_user', $parameters['igfae_db_intranet_user']);
    update_option('igfae_db_intranet_pass', $parameters['igfae_db_intranet_pass']);
    update_option('igfae_db_intranet_host', $parameters['igfae_db_intranet_host']);
    update_option('igfae_events_cache_ttl_fetch', max(0, intval($parameters['igfae_events_cache_ttl_fetch'] ?? 120)));
    update_option('igfae_events_cache_ttl_single', max(0, intval($parameters['igfae_events_cache_ttl_single'] ?? 300)));
    update_option('igfae_events_cache_ttl_metadata', max(0, intval($parameters['igfae_events_cache_ttl_metadata'] ?? 600)));


    $currentCronStart = get_option('igfae_intranet_cron_start');
    $currentCronState = get_option('igfae_intranet_cron_state');
    $currentCronRecurrence = get_option('igfae_intranet_cron_recurrence');


    update_option('igfae_intranet_cron_recurrence', $parameters['igfae_intranet_cron_recurrence']);
    update_option('igfae_intranet_cron_start', $parameters['igfae_intranet_cron_start']);
    update_option('igfae_intranet_cron_state', $parameters['igfae_intranet_cron_state']);
    
    if ($parameters['igfae_intranet_cron_state'] == '0') {
        igfae_cron_unschedule();
    } elseif ( //si el estado de la tarea es activado y algun parametro ha cambiado la reprogramamos
        $parameters['igfae_intranet_cron_state'] == '1' &&
        (
            $parameters['igfae_intranet_cron_state'] != $currentCronState ||
            $parameters['igfae_intranet_cron_start'] != $currentCronStart ||
            $parameters['igfae_intranet_cron_recurrence'] != $currentCronRecurrence
        )
    ) {
        igfae_cron_unschedule();
        igfae_cron_schedule($parameters['igfae_intranet_cron_start'], $parameters['igfae_intranet_cron_recurrence']);
    }

    $url = add_query_arg('settings-updated', 'true', $callback);
    if ( $flushEventsCache && class_exists('IgfaeUnifiedEvent') ) {
        IgfaeUnifiedEvent::clearCache();
        $url = add_query_arg('events-cache-cleared', 'true', $url);
    }
    wp_redirect($url);
    exit;
} 

add_action("wp_ajax_igfae_filter_publications", "api_igfae_filter_publications");	
add_action("wp_ajax_nopriv_igfae_filter_publications", "api_igfae_filter_publications");	
function api_igfae_filter_publications() 
{	

    $keywords 	= $_POST['igfae-keywords'];
    $type 		= NULL;
    $year		= NULL;
    $rp		    = NULL;
    $page 		= 0;

    if( $_POST['igfae-type'] != 'all') {
    	$type 	= $_POST['igfae-type'];
    }

    if( $_POST['igfae-type2'] != 'all') {
    	$year 	= $_POST['igfae-type2'];
    }

    if( $_POST['igfae-type3'][0] != 'ALL') {
        $rp 	= implode(', ', $_POST['igfae-type3']);
    }
/*
    $logInfo .= "[".date("d/m/Y - h:i:s")."] Fetch: ".print_r($_POST['igfae-type3'], true)."\n";
    //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
    if ( isset( $_POST['igfae-page'] ) ) {
    	$page = $_POST['igfae-page'];
    }

    switch($type) {
    	case 'article':

	    	if ($keywords == '') {
	    		$publications = IgfaePublication::all('article', $year, $page, 10, $rp);
	    	} else {
			    $publications = IgfaePublication::search($keywords, 'article', $year, $page, 10, $rp);
	    	}
			
		break;
		case 'thesis':

	    	if ($keywords == '') {
			    $publications = IgfaePublication::all('thesis', $year, $page, 10, $rp);
	    	} else {
			    $publications = IgfaePublication::search($keywords, 'thesis', $year, $page, 10, $rp);
	    	}
			
		break;
		default:

	    	if ($keywords == '') {
			    $publications = IgfaePublication::all($type, $year, $page, 10, $rp);
	    	} else {
			    $publications = IgfaePublication::search($keywords, 'all', $year, $page, 10, $rp);
	    	}

		break;
    }

    igfae_get_template_part('publications', 'view-table', $publications);
	exit();
} 

add_action("wp_ajax_igfae_filter_events", "api_igfae_filter_events");	
add_action("wp_ajax_nopriv_igfae_filter_events", "api_igfae_filter_events");	
function api_igfae_filter_events() 
{	
	/*
	$logInfo .= "[".date("d/m/Y - h:i:s")."] Events: ".print_r($_POST, true)."\n";
    //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
	*/
    $keywords 	= $_POST['igfae-keywords'];
    $type 		= NULL;
    $year		= NULL;
    $page 		= 0;

    if( $_POST['igfae-type'] != 'all') {
    	$type 	= $_POST['igfae-type'];
    }

    if( $_POST['igfae-type2'] != 'all') {
    	$year 	= $_POST['igfae-type2'];
    }

    if ( isset( $_POST['igfae-page'] ) ) {
    	$page = $_POST['igfae-page'];
    }

    if ( $type ) {
		$events = IgfaeEvent::fetch( $keywords, $year, $page, 20, $type );
    } else {
		$events = IgfaeEvent::fetch( $keywords, $year, $page, 20, 'all' );
    }
    /*
    $logInfo .= "[".date("d/m/Y - h:i:s")."] Events ".print_r($events, true)."\n";
    //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
    */
    igfae_get_template_part('events', 'view-table', $events);
	exit();
} 

add_action("wp_ajax_igfae_filter_personal", "api_igfae_filter_personal");	
add_action("wp_ajax_nopriv_igfae_filter_personal", "api_igfae_filter_personal");	
function api_igfae_filter_personal()
{
    $keywords 	= $_POST['igfae-keywords'];

    $type 			= NULL;
    $researchLine	= NULL;

    if( $_POST['igfae-type'] != 'ALL') {
    	$type = $_POST['igfae-type'];
    }

    if( $_POST['igfae-type2'][0] != 'ALL') {
    	$researchLine 	= implode(', ', $_POST['igfae-type2']);
    }
	
	/*
	$logInfo .= "[".date("d/m/Y - h:i:s")."] Personal ".print_r($_POST, true)."\n";
    //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
	*/
    igfae_get_template_part('personal', 'table3-view', [
        'personal' => IgfaePersonal::fetch( $keywords, $type, $researchLine )
    ]);

	exit();
} 

add_action("wp_ajax_igfae_filter_projects", "api_igfae_filter_projects");	
add_action("wp_ajax_nopriv_igfae_filter_projects", "api_igfae_filter_projects");	
function api_igfae_filter_projects()
{
    $keywords 	= trim( filter_var( $_POST['igfae-keywords'], FILTER_SANITIZE_STRING ) );
    $type 		= NULL;
    $year		= NULL;
    $page 		= 0;

    if( $_POST['igfae-type'] != 'all' && $_POST['igfae-type'] != 'ALL' && $_POST['igfae-type'][0] != 'ALL') {
//    	$type 	= trim( filter_var( $_POST['igfae-type'], FILTER_SANITIZE_STRING ) );
    	$type 	= implode(', ', $_POST['igfae-type']);
    }

    if( $_POST['igfae-type2'] != 'all') {
    	$year 	= intval( filter_var( $_POST['igfae-type2'], FILTER_SANITIZE_NUMBER_INT ) );
    }

    if ( isset( $_POST['igfae-page'] ) ) {
    	$page = intval( filter_var( $_POST['igfae-page'], FILTER_SANITIZE_NUMBER_INT ) );
    }
    
	$info = IgfaeProject::fetch( $keywords, $type, $year, $page, 10 );

    igfae_get_template_part('project', 'view-table', [
        'projects' 		=> $info['projects'],
	    'pagination' 	=> $info['pagination']
    ]);

	exit();
}

add_action("wp_ajax_igfae_filter_prensas", "api_igfae_filter_prensas");	
add_action("wp_ajax_nopriv_igfae_filter_prensas", "api_igfae_filter_prensas");	
function api_igfae_filter_prensas()
{

    $keywords 	= trim( filter_var( $_POST['igfae-keywords'], FILTER_SANITIZE_STRING ) );
    $type 		= NULL;
    $year		= NULL;
    $page 		= 0;

    if( $_POST['igfae-type'] != 'all') {
    	$type 	= trim( filter_var( $_POST['igfae-type'], FILTER_SANITIZE_STRING ) );
    }

    if( $_POST['igfae-type2'] != 'all') {
    	$year 	= intval( filter_var( $_POST['igfae-type2'], FILTER_SANITIZE_NUMBER_INT ) );
    }

    if ( isset( $_POST['igfae-page'] ) ) {
    	$page = intval( filter_var( $_POST['igfae-page'], FILTER_SANITIZE_NUMBER_INT ) );
    }
    
	$info = IgfaePrensa::fetch( $keywords, $type, $year, $page, 10 );
/*
	$logInfo .= "[".date("d/m/Y - h:i:s")."] filter: ".print_r('\''.$keywords.'\'', true)."\n";
    //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
    igfae_get_template_part('prensa', 'view-table', [
        'projects' 		=> $info['projects'],
	    'pagination' 	=> $info['pagination']
    ]);

	exit();
}

add_action("wp_ajax_igfae_events_calendar", "api_igfae_events_calendar");	
add_action("wp_ajax_nopriv_igfae_events_calendar", "api_igfae_events_calendar");	
function api_igfae_events_calendar()
{
    $start 	= $_POST['start'];
    $end 	= $_POST['end'];
    
    //$events = IgfaeEventCalendar::all();

	$events = IgfaeEvent::fetchJSON();
	/*
	$event = new IgfaeEventCalendar( 
		count($events)+1,
		'Prueba de calendario', 
		'2018-04-19 12:00:00',
		'2018-04-21 14:00:00',
		'Adrián Fernández',
		'Calle Real nº 24'
	);
	$events[] = $event;    //evento de prueba
	*/
    
    api_response($events);
}

function api_response($response, $callback = null, $parameters = null)
{
	if ($callback) {
        api_redirect($callback, $response);    
	} else {
    	header('Content-Type: application/json');
    	echo json_encode($response);
	}

	exit();
}
