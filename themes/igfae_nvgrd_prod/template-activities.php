<?php
/**
 * Template Name: Activities
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

$args_posts = array(
    'numberposts'   => -1,
    'post_type'     => 'actividad',
    'post_parent'   => 0,
    'order'         => 'ASC'
);

$context['posts'] = Timber::get_posts( $args_posts );

Timber::render( array( 'page-activities.twig', 'page.twig' ), $context );
