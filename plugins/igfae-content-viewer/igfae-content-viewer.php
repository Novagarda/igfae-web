<?php
/*
Plugin Name: Visor contenido IGFAE _ NVGRD
Description: Visor de contenido de la base de datos de IGFAE
Version: 1.2.3
Author: novagarda
Author URI: http://www.novagarda.gal
Text Domain: igfae-content-viewer
License: GPLv2 or later
*/

define( 'IGFAE_PATH', plugin_dir_path( __FILE__ ) );
define( 'IGFAE_URL', plugin_dir_url( __FILE__ ) );
define( 'TRANS_DOMAIN', 'ifgae-content-viewer');

//Includes generales
include_once("helpers/import_template.php");
include_once("helpers/users.php");
include_once("helpers/events_date.php");
include_once("admin_menu.php");
include_once("models/publication.php");
include_once("models/article.php");
include_once("models/thesis.php");
include_once("models/event.php");
include_once("models/event_unified.php");
include_once("models/personal.php");
include_once("models/project.php");
include_once("models/prensa.php");
include_once("models/calendar_event.php");



include_once("shortcodes.php");
include_once("api.php");
include_once('routes.php');

// Includes Intranet
// Models
include_once("intranet/constants.php");
include_once("intranet/models/production_project.php");
include_once("intranet/models/activity_type.php");
include_once("intranet/models/prototype_state.php");
include_once("intranet/models/research_area.php");
include_once("intranet/models/thesis_type.php");
include_once("intranet/models/research_program.php");
include_once("intranet/models/research_group.php");
include_once("intranet/models/experimental_collaboration.php");
include_once("intranet/models/scope.php");
include_once("intranet/models/journal.php");
include_once("intranet/models/keyword.php");
include_once("intranet/models/personnel.php");
include_once("intranet/models/external_author.php");
include_once("intranet/models/workline.php");
include_once("intranet/models/journal_editorial.php");
include_once("intranet/models/external_director.php");
include_once("intranet/models/research_project.php");
include_once("intranet/models/qualification.php");
include_once("intranet/models/project_type.php");
include_once("intranet/models/financing_agency.php");
include_once("intranet/models/journal_area.php");
include_once("intranet/models/prototype.php");
include_once("intranet/models/funding_agency.php");
include_once("intranet/models/category.php");
include_once("intranet/models/team_reference.php");
include_once("intranet/models/department.php");
include_once("intranet/models/contract.php");
include_once("intranet/models/work_placements.php");
include_once("intranet/models/building.php");
include_once("intranet/models/location.php");
include_once("intranet/models/social_network.php");
include_once("intranet/models/scientific_firm.php");
include_once("intranet/models/country.php");
include_once("intranet/models/map_location.php");
include_once("intranet/models/indicator_type.php");
include_once("intranet/models/interval.php");
include_once("intranet/models/indicator.php");
include_once("intranet/models/grouped_indicator.php");
include_once("intranet/models/institution.php");
include_once("intranet/models/transfer.php");
include_once("intranet/models/transfer_sub_type.php");
include_once("intranet/models/transfer_type.php");
include_once("intranet/models/event_type.php");
include_once("intranet/models/event_subtype.php");
include_once("intranet/models/events.php");
include_once("intranet/models/quarter.php");
include_once("intranet/models/monitorIndicator.php");
include_once("intranet/models/monitor_type.php");
include_once("intranet/models/trl_type.php");
include_once("intranet/models/event-language.php");
include_once("intranet/models/source.php");
include_once("intranet/models/entity.php");
include_once("intranet/models/authorization.php");
include_once("intranet/models/authorizationStatus.php");
include_once("intranet/models/authorizationRequest.php");
include_once("intranet/models/activity_subtype.php");
include_once("intranet/models/monitor_request.php");

include_once("intranet/models/equipment_family.php");
include_once("intranet/models/equipment_laboratory.php");
include_once("intranet/models/equipment_technical-contact.php");
include_once("intranet/models/equipment_responsable-family.php");
include_once("intranet/models/equipment_booking-history.php");
include_once("intranet/models/equipments.php");

// Api
include_once("intranet/api/commons.php");
include_once("intranet/api/export.php");
include_once("intranet/api/login.php");
include_once("intranet/api/production-project.php");
include_once("intranet/api/research-project.php");
include_once("intranet/api/experimental-collaboration.php");
include_once("intranet/api/journal.php");
include_once("intranet/api/prototypes.php");
include_once("intranet/api/users.php");
include_once("intranet/api/work-placements.php");
include_once("intranet/api/indicators.php");
include_once("intranet/api/grouped-indicators.php");
include_once("intranet/api/events.php");
include_once("intranet/api/transfer.php");
include_once("intranet/api/monitor.php");
include_once("intranet/api/authorization.php");
include_once("intranet/api/locations.php");

include_once("intranet/api/equipments.php");

//Security
include_once("intranet/security/capacibilities.php");
include_once("intranet/security/roles.php");

//Queries
include_once("intranet/queries/audit.php");
include_once("intranet/queries/production-project.php");
include_once("intranet/queries/production-project-activity-type.php");
include_once("intranet/queries/research-area.php");
include_once("intranet/queries/research-group.php");
include_once("intranet/queries/research-program.php");
include_once("intranet/queries/thesis-type.php");
include_once("intranet/queries/experimental-collaboration.php");
include_once("intranet/queries/scope-project.php");
include_once("intranet/queries/journal.php");
include_once("intranet/queries/keyword.php");
include_once("intranet/queries/personnel.php");
include_once("intranet/queries/external-author.php");
include_once("intranet/queries/workline.php");
include_once("intranet/queries/journal-editorial.php");
include_once("intranet/queries/external-director.php");
include_once("intranet/queries/research-project.php");
include_once("intranet/queries/qualification.php");
include_once("intranet/queries/project-type.php");
include_once("intranet/queries/financing-agency.php");
include_once("intranet/queries/journal-area.php");
include_once("intranet/queries/research-project-activity-type.php");
include_once("intranet/queries/prototype-state.php");
include_once("intranet/queries/prototype-researcher-igfae.php");
include_once("intranet/queries/prototype-researcher-ip-coordinator.php");
include_once("intranet/queries/prototypes.php");
include_once("intranet/queries/research-project-activity-type-usc.php");
include_once("intranet/queries/research-project-subactivity-type-usc.php");
include_once("intranet/queries/funding-agency.php");
include_once("intranet/queries/category.php");
include_once("intranet/queries/team-reference.php");
include_once("intranet/queries/department.php");
include_once("intranet/queries/contract.php");
include_once("intranet/queries/user.php");
include_once("intranet/queries/work-placements.php");
include_once("intranet/queries/building.php");
include_once("intranet/queries/location.php");
include_once("intranet/queries/social-network.php");
include_once("intranet/queries/scientific-firm.php");
include_once("intranet/queries/country.php");
include_once("intranet/queries/map-location.php");
include_once("intranet/queries/indicator.php");
include_once("intranet/queries/grouped-indicator.php");
include_once("intranet/queries/interval.php");
include_once("intranet/queries/file.php");
include_once("intranet/queries/institution.php");
include_once("intranet/queries/transfer.php");
include_once("intranet/queries/transfer_sub_type.php");
include_once("intranet/queries/transfer_type.php");
include_once("intranet/queries/quarter.php");
include_once("intranet/queries/event-type.php");
include_once("intranet/queries/event-subtype.php");
include_once("intranet/queries/events.php");
include_once("intranet/queries/event-language.php");
include_once("intranet/queries/monitorIndicator.php");
include_once("intranet/queries/monitor-type.php");
include_once("intranet/queries/trl_type.php");
include_once("intranet/queries/sources.php");
include_once("intranet/queries/entity.php");
include_once("intranet/queries/authorization.php");
include_once("intranet/queries/locations.php");

include_once("intranet/queries/equipment-family.php");
include_once("intranet/queries/equipment-laboratory.php");
include_once("intranet/queries/equipment-technical-contact.php");
include_once("intranet/queries/equipment-responsable-family.php");
include_once("intranet/queries/equipment.php");

add_action('plugins_loaded', 'igfae_load_textdomain');
function igfae_load_textdomain() {
	load_plugin_textdomain( 'igfae-content-viewer', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

/**

Ajax function

**/
add_action('wp_head', 'igfae_load_scripts');
function igfae_load_scripts(){
	// embed the javascript file that makes the AJAX request
	wp_enqueue_script( 'igfae-content-viewer', IGFAE_URL .'resources/js/igfae-content-viewer.js', array( 'jquery' ) );
	 
	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	
	wp_enqueue_style( 'igfae-content-viewer', IGFAE_URL . 'resources/styles/igfae-content-viewer.css' );
	wp_register_style( 'igfae-events-calendar', plugins_url( 'resources/styles/events-calendar.css', __FILE__ ), []);
	
	wp_register_style( 'igfae-fullcalendar', plugins_url( 'resources/foundation/fullcalendar/fullcalendar.min.css', __FILE__ ), []);
	wp_register_style( 'igfae-fullcalendar-print', plugins_url( 'resources/foundation/fullcalendar/fullcalendar.print.min.css', __FILE__ ), [], null, "print");
	
	wp_register_script( 'igfae-moment', plugins_url( 'resources/foundation/fullcalendar/lib/moment.min.js', __FILE__ ), ['jquery']);
	wp_register_script( 'fullcalendar', plugins_url( 'resources/foundation/fullcalendar/fullcalendar.min.js', __FILE__ ), ['jquery', 'igfae-moment']);

	$locale = 'en';
	wp_register_script( 'fullcalendar-locale', plugins_url( 'resources/foundation/fullcalendar/locale-all.js', __FILE__ ), ['fullcalendar']);
	if ( defined ( 'ICL_LANGUAGE_CODE' ) ) {
		$locale = ICL_LANGUAGE_CODE;
	}

	wp_register_script( 'igfae-events-calendar', plugins_url( 'resources/js/events-calendar.js', __FILE__ ), [
		'jquery', 
		'fullcalendar', 
		'fullcalendar-locale', 
		'igfae-moment'
	]);

	wp_localize_script( 'igfae-events-calendar', 'IGFAE', [
		'params' =>[ 
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'locale' 	=> $locale
		],
		'translation' =>[
			'from' 	=> __( 'From', 'ifgae-content-viewer' ), 
			'to'	=> __( 'To', 'ifgae-content-viewer' ),
			'place' => __( 'Place', 'ifgae-content-viewer' ),
			'speaker' => __( 'Speaker', 'ifgae-content-viewer' ),
		]
	]);
}

/**
* Python script automatizated recuperation of articles 
**/

 //Creamos la action que ejecutara el script
function igfae_cron_exec(){

  $logRoute = WP_CONTENT_DIR . '/';
  $logRoute = wp_normalize_path($logRoute);
  error_log('Ejecutando tarea cron, ¿se ha producido un error? :');
 

  $path = get_home_path() ."python-automatizated-rec-articles/procesoArticulos.py";
  $path = wp_normalize_path ($path);

  $userBd =  igfae_get_db_intranet_user();
  $passBd =  igfae_get_db_intranet_pass();
  $nameBd =  igfae_get_db_intranet_name();
  $hostBd =  igfae_get_db_intranet_host();
  $wosUrlBase = WOS_URL_BASE;
  $wosUser = WOS_USER;
  $wosPassword = WOS_PASSWORD;
  $wosMetricsUrl = WOS_URL_BASE_METRICS;
  $inspireSourceActivated = INSPIRE_SOURCE_ACTIVATED;
  $wosSourceActivated = WOS_SOURCE_ACTIVATED;
  $scopusSourceActivated = SCOPUS_SOURCE_ACTIVATED;

  $cmdLineOrder = "python ". $path . " " . $userBd . " " . $passBd . " " . $nameBd . " " . $hostBd . " " . $logRoute . " " . $wosUrlBase . " " . $wosUser . " " . $wosPassword . " " . $wosMetricsUrl . " " . $inspireSourceActivated . " " . $wosSourceActivated . " " . $scopusSourceActivated;

  $output;
  $cmdLineOrder = escapeshellcmd($cmdLineOrder);
  $cmdLineOrder = $cmdLineOrder . " 2>&1";
  exec($cmdLineOrder, $output);
  error_log(print_r($output, TRUE));
}
add_action( 'igfae_cron_hook', 'igfae_cron_exec' );


//registramos la tarea
function igfae_cron_schedule($start, $recurrence) {

	$dtime = DateTime::createFromFormat("Y-m-d G:i", $start, new DateTimeZone('Europe/Madrid'));

	$now = new DateTime();
	while ($now > $dtime) {
		if ($recurrence === 'hourly') { 
			$dtime->add(new DateInterval('PT1H'));
		} elseif ($recurrence === 'twicedaily') { 
			$dtime->add(new DateInterval('PT12H'));
		} elseif ($recurrence === 'daily') {
			$dtime->add(new DateInterval('P1D'));
		} elseif ($recurrence === 'weekly') {
			$dtime->add(new DateInterval('P7D'));
		} elseif ($recurrence === 'monthly') {
			$dtime->add(new DateInterval('P1M'));
		} elseif ($recurrence === 'quarterly') {
			$dtime->add(new DateInterval('P3M'));
		} elseif ($recurrence === 'biannual') {
			$dtime->add(new DateInterval('P6M'));
		}
	}

	$timestamp = $dtime->getTimestamp();
    wp_schedule_event( $timestamp, $recurrence, 'igfae_cron_hook' );
}


function igfae_cron_unschedule() {

	$timestamp = wp_next_scheduled( 'igfae_cron_hook' );

	while ($timestamp) {
		wp_unschedule_event( $timestamp, 'igfae_cron_hook' );
		$timestamp = wp_next_scheduled( 'igfae_cron_hook' );
	}
}

//registramos nuevas recurrences
add_filter( 'cron_schedules', 'add_cron_interval' );
function add_cron_interval( $schedules ) { 
    $schedules['monthly'] = array(
        'interval' => 2592000,
        'display'  => esc_html__( 'Monthly' ), );
    return $schedules;
}


//inicializamos los folders necesarios para los uploads
function igfae_intranet_init() {
    if ( ! is_dir(IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PATH)) {
        mkdir(IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PATH);
        chmod(IGFAE_INTRANET_UPLOADS_USERS_PHOTOS_PATH, 0775);
	}
    if ( ! is_dir(IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PATH)) {
        mkdir(IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PATH);
        chmod(IGFAE_INTRANET_UPLOADS_PROTOTYPES_PHOTOS_PATH, 0775);
    }	
	if ( ! is_dir(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH)) {
        mkdir(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH);
        chmod(IGFAE_INTRANET_UPLOADS_EQUIPMENTS_FILES_PATH, 0775);
    }	
}
add_action( 'init', 'igfae_intranet_init' );
  
