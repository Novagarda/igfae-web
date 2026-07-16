<?php
add_shortcode('igfae_publications', 'shortcode_publications');
function shortcode_publications($atts, $content = "", $shortcodename = "igfae_publications") {
    $atts = shortcode_atts(array(), $atts, $shortcodename);
    ob_start();

    igfae_get_template_part("publications", 'main', $atts);

    return ob_get_clean();
}

add_shortcode('igfae_events', 'shortcode_events');
function shortcode_events($atts, $content = "", $shortcodename = "igfae_events") {

    $atts = shortcode_atts([
        'limit'      => 20,
        'paginar'    => true,
        'destacados' => false,
        'order'      => 'DESC',
        'upcoming'   => false,
    ], $atts, $shortcodename);

    ob_start();

    igfae_get_template_part("events", 'main', $atts);

    return ob_get_clean();
}

add_shortcode('igfae_projects', 'shortcode_projects');
function shortcode_projects($atts, $content = "", $shortcodename = "igfae_projects") {
    $atts = shortcode_atts(array(), $atts, $shortcodename);
    ob_start();

    igfae_get_template_part("project", 'main', $atts);

    return ob_get_clean();
}

add_shortcode('igfae_prensas', 'shortcode_prensas');
function shortcode_prensas($atts, $content = "", $shortcodename = "igfae_prensas") {
    $atts = shortcode_atts(array(), $atts, $shortcodename);
    ob_start();

    igfae_get_template_part("prensa", 'main', $atts);

    return ob_get_clean();
}

add_shortcode('igfae_personal', 'shortcode_personal');
function shortcode_personal($atts, $content = "", $shortcodename = "igfae_personal") {
    $atts = shortcode_atts(array(), $atts, $shortcodename);
    ob_start();

    igfae_get_template_part("personal", 'main', $atts);

    return ob_get_clean();
}

add_shortcode('igfae_events_calendar', 'shortcode_events_calendar');
function shortcode_events_calendar($atts, $content = "", $shortcodename = "igfae_events_calendar") {
    $atts = shortcode_atts(array(), $atts, $shortcodename);
    ob_start();

    igfae_get_template_part("events", 'calendar', $atts);

    return ob_get_clean();
}

add_shortcode('igfae_list_events', 'shortcode_list_events');
function shortcode_list_events($atts, $content = "", $shortcodename = "igfae_list_events") {

    $atts = shortcode_atts([
        'limit' => 3
    ], $atts, $shortcodename);

    ob_start();

    igfae_get_template_part("events", 'list', $atts);

    return ob_get_clean();
}

add_shortcode('igfae_list_personal', 'shortcode_list_personal');
function shortcode_list_personal($atts, $content = "", $shortcodename = "igfae_list_personal") {
    $atts = shortcode_atts([
        'id'    => ''
    ], $atts, $shortcodename);

    ob_start();

    igfae_get_template_part('personal', 'list', [
        'personal'  => IgfaePersonal::fetch('', NULL, $atts['id'], NULL, 10),
        'programa'  => $atts['id'],
    ]);

    return ob_get_clean();
}

add_shortcode('igfae_list_projects', 'shortcode_list_projects');
function shortcode_list_projects($atts, $content = "", $shortcodename = "igfae_list_projects") {
    $atts = shortcode_atts([
        'id'    => ''
    ], $atts, $shortcodename);

    ob_start();

    $info = IgfaeProject::fetch('', '', '', 0, 5, $atts['id']);
    igfae_get_template_part('project', 'list', [
        'projects' 		=> $info['projects'],
        'pagination' 	=> $info['pagination']
    ]); 

    return ob_get_clean();
}

add_shortcode('igfae_list_publications', 'shortcode_list_publications');
function shortcode_list_publications($atts, $content = "", $shortcodename = "igfae_list_publications") {
    $atts = shortcode_atts([
        'id'    => ''
    ], $atts, $shortcodename);

    ob_start();

    igfae_get_template_part( 'publications', 'list', IgfaePublication::all( '', '', 0, 5, $atts['id'] ) );

    return ob_get_clean();
}