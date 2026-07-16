<?php
/**
 * Template Name: Carreira
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$args_posts = array(
    'numberposts'	=> -1,
    'post_type' => 'carreira',
);

$context['posts'] = Timber::get_posts( $args_posts );

Timber::render( array( 'page-carreira.twig', 'page.twig' ), $context );
