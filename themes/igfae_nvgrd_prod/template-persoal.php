<?php
/**
 * Template Name: Persoal
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['lol'] = 'home';

Timber::render( array( 'page-persoal.twig', 'page.twig' ), $context );
