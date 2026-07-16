<?php

add_action('admin_menu', 'igfae_add_menu_page_viewer_settings', 10);    
function igfae_add_menu_page_viewer_settings() {   
	add_menu_page(
        'IGFAE Viewer', 
        'IGFAE Viewer', 
        'edit_published_posts',
        'igfae_settings',
        'igfae_viewer_settings_content'
    );
}

function igfae_viewer_settings_content() {    
    igfae_get_template_part('admin-menu', 'settings');
} 

function igfae_get_db_name() {
	return get_option('igfae_db_name');    
}

function igfae_get_db_pass() {
	return get_option('igfae_db_pass');    
}

function igfae_get_db_user() {
	return get_option('igfae_db_user');    
}

function igfae_get_db_host() {
	$value = get_option('igfae_db_host'); 
	return  ($value == '') ? 'localhost' : $value;   
}
  
function igfae_get_db_intranet_name() {
	return get_option('igfae_db_intranet_name');    
}

function igfae_get_db_intranet_pass() {
	return get_option('igfae_db_intranet_pass');    
}

function igfae_get_db_intranet_user() {
	return get_option('igfae_db_intranet_user');    
}

function igfae_get_db_intranet_host() {
	$value = get_option('igfae_db_intranet_host'); 
	return  ($value == '') ? 'localhost' : $value;   
}
  

function igfae_get_intranet_cron_recurrence() {
	$value = get_option('igfae_intranet_cron_recurrence'); 
	return  ($value == '') ? 'daily' : $value;   
}

function igfae_get_intranet_cron_start() {
	$value = get_option('igfae_intranet_cron_start'); 
	$fecha = new DateTime();
	$fecha->setTimestamp(time());
	return  ($value == '') ? $fecha->format('Y-m-d H:i') : $value;   
}

function igfae_get_intranet_cron_state() {
	$value = get_option('igfae_intranet_cron_state'); 
	return  ($value == '') ? '0' : $value;   
}

function igfae_get_events_cache_ttl_metadata() {
	$value = get_option('igfae_events_cache_ttl_metadata');
	return ($value === '' || $value === false) ? '600' : $value;
}

function igfae_get_events_cache_ttl_fetch() {
	$value = get_option('igfae_events_cache_ttl_fetch');
	return ($value === '' || $value === false) ? '120' : $value;
}

function igfae_get_events_cache_ttl_single() {
	$value = get_option('igfae_events_cache_ttl_single');
	return ($value === '' || $value === false) ? '300' : $value;
}


