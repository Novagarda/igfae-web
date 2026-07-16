<?php
/**
 * Template Name: Agenda
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

$args = array(
    'numberposts'	=> -1,
    'post_type' => 'post',
    'meta_key'		=> 'featured',
    'meta_value'	=> 1
);

$context['projectsFeatured'] = Timber::get_posts( $args );

Timber::render( array( 'page-agenda.twig', 'page.twig' ), $context );
