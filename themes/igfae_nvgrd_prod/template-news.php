<?php
/**
 * Template Name: News
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;
$context['categories'] = Timber::get_terms( [
    'taxonomy' => 'category',
    'hide_empty'    => true,
]);
$context['tags'] = Timber::get_terms( 'tags' );
$context['bodyClass'] = 'home';

global $paged;
if (!isset($paged) || !$paged) {
    $paged = 1;
}
$args_novas = array(
    'posts_per_page'	=> 12,
    'post_type' => 'post',
    'paged' => $paged,
);

$context['novas'] = new Timber\PostQuery( $args_novas );
$context['pagination'] = $context['novas']->pagination(4);

Timber::render( array( 'page-news.twig', 'page.twig' ), $context );
