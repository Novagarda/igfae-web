<?php
/**
 * Template Name: Areas de investigacion
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['categories'] = Timber::get_terms( 'category' );
$context['tags'] = Timber::get_terms( 'tags' );
$context['bodyClass'] = 'home';

$context['locomia'] = get_the_id();

$args_areas = array(
    'numberposts'	=> 3,
    'post_type'     => 'area-investigacion',
    'order'         => 'ASC',
);

$context['areas'] = Timber::get_posts( $args_areas );
$context['areas_progrm'] = array();

foreach ($context['areas'] as $key => $area) {
    $args_prog = array(
        'numberposts'	=> 4,
        'post_type'     => 'programa',
    );
    $programas = Timber::get_posts( $args_prog );
    array_push($context['areas_progrm'], $programas );
}


//$context['projectsFeatured'] = Timber::get_posts( $args );

Timber::render( array( 'page-areas-investigacion.twig', 'page.twig' ), $context );
