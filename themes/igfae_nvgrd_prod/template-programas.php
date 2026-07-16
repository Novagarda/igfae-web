<?php
/**
 * Template Name: Programas
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$context['areas'] = array();
$args_areas = array(
    'numberposts'	=> -1,
    'post_type'     => 'area-investigacion',
    'order'         => 'ASC'
);

$areas = Timber::get_posts( $args_areas );
foreach ($areas as $key => $area) {
    $temp_area = array();
    $temp_area['id'] = $area->ID;
    $temp_area['titulo'] = get_the_title($area);
    $temp_area['enlace'] = get_the_permalink($area);
    $temp_area['codigo'] = get_post_meta($area->ID, 'codigo', true);

    $args_programas = array(
        'numberposts'	=> -1,
        'post_type' => 'programa',
        'meta_query' => array(
            array(
            'key' => 'area',
            'value' => $area->ID,
            'compare' => 'LIKE',
            ),
        ),
    );    
    $programas = Timber::get_posts( $args_programas );
    $temp_area['programas'] = $programas;

    array_push($context['areas'], $temp_area);
}
/*
$logInfo .= "[".date("d/m/Y - h:i:s")."] Areas: ".print_r($context['areas'], true)."\n";
file_put_contents(get_stylesheet_directory().'/custom.log', $logInfo, FILE_APPEND);
*/
Timber::render( array( 'page-programas.twig', 'page.twig' ), $context );
