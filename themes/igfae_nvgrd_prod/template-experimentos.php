<?php
/**
 * Template Name: Experimentos
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$args_experimentos = array(
    'numberposts'	=> -1,
    'post_type' => 'experimento',
);

$context['experimentos'] = Timber::get_posts( $args_experimentos );

Timber::render( array( 'page-experimentos.twig', 'page.twig' ), $context );
