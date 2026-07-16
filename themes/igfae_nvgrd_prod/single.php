<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = Timber::query_post();
$context['post'] = $post;
$context['categories'] = Timber::get_terms( 'category' );
$context['bodyClass'] = 'single';
$context['comment_form'] = TimberHelper::get_comment_form();

$post_categories = wp_get_post_categories( $post->ID, array( 'fields' => 'names' ) );
$post_categories_array = wp_get_post_categories( $post->ID );

// Declaramos unha variable "item" para meter as novas relacionadas e sexa compatible co bloque flexible de novas relacionadas
$context['item'] = array();
$context['item']['borde'] = true;
//$context['item']['titulo'] = __('Novas relacionadas', 'nvgrd');
$context['item']['related_news'] = $post_categories;

$cats_list = implode(',', $post_categories_array);
$args_related = array(
	'numberposts' => 3,
	'post_type' => 'post',
	'cat' => $cats_list
);

$context['relacionadas'] = Timber::get_posts($args_related);


if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
