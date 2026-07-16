<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */
add_filter( 'body_class','my_body_classes' );
function my_body_classes( $classes ) {
    $classes[] = 'corpoazul';
    return $classes;
}

$context = Timber::get_context();
Timber::render( '404.twig', $context );
