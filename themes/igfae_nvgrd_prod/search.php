<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$templates = array( 'search.twig', 'archive.twig', 'index.twig' );
$context = Timber::get_context();

$search_query = get_search_query();

$posts_default = Timber::get_posts(); // o pasar una WP_Query con argumentos

if($search_query){

    // PERSOAL
    require_once WP_PLUGIN_DIR . '/igfae-content-viewer/models/personal.php';
    $persoal_results = IgfaePersonal::fetch($search_query);

    // Xeramos os posts de persoal virtuales que veñen da BD de IGFAE
    foreach ($persoal_results as $key => $persoa) { 
/*
        $logInfo .= "[".date("d/m/Y - h:i:s")."] Events Busqueda: ".print_r($persoa, true)."\n";
        file_put_contents(get_stylesheet_directory().'/custom.log', $logInfo, FILE_APPEND);
*/
        $post_data = new stdClass();
        $post_data->post_title = $persoa->getFullName();
        $post_data->post_type = 'persoal';
        $post_data->guid = get_permalink(get_field('paxina_de_persoa', 'option')) . sanitize_title_for_query(sanitize_file_name($persoa->getFullName())) . "/" . $persoa->getId();
        $post_data->categoria = $persoa->getCategory();
        $post_data->email = $persoa->getEmail();
        $phone = trim((string) $persoa->getPhone());
        $post_data->phone = $phone !== '' ? '(+34) 8818' . $phone : '';

        $fake_persoal_post = new WP_Post($post_data);
        $fake_persoal_timber_post = new Timber\Post($fake_persoal_post);

        array_unshift($posts_default, $fake_persoal_timber_post);
    }

    // AXENDA
    require_once WP_PLUGIN_DIR . '/igfae-content-viewer/models/event.php';
    require_once WP_PLUGIN_DIR . '/igfae-content-viewer/models/event_unified.php';
    $events_results = IgfaeEvent::fetch($search_query, NULL, 0, 10);

    // Xeramos os posts de axenda virtuales que veñen da BD de IGFAE
    foreach ($events_results['rows'] as $key => $event) { 

        if($event != NULL){
            $event_data = new stdClass();
            $event_data->post_title = $event->getTitle();
            $event_data->post_type = 'axenda';
            $event_data->categoria = $event->getType();
            $event_data->guid = $event->getUrl();
            $event_data->place = $event->getPlace();

            $fake_event_post = new WP_Post($event_data);
            $fake_event_timber_post = new Timber\Post($fake_event_post);

            array_unshift($posts_default, $fake_event_timber_post);            
        }
    }
}

$context['title'] = 'Search results for '. get_search_query();
$context['posts_timber'] = $posts_default;

Timber::render( $templates, $context );
