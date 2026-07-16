<?php
/**
 * Template Name: Works
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
$context['bodyClass'] = 'works';

Timber::render( array( 'page-works.twig', 'page.twig' ), $context );
