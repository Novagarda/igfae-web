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

$args_child_posts = array(
    'post_type'      => 'actividad',
    'posts_per_page' => -1,
    'post_parent'    => $post->ID,
 );
 $context['filles'] = Timber::get_posts( $args_child_posts );

 if( $post->post_parent > 0 ) {
	$context['son_fille'] = true;
	$context['nai'] = Timber::get_post(get_post_parent($post->ID));
 }else{
	$context['son_fille'] = false;
 }


if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
