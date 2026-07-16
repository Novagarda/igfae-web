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
$context['categorias_oferta'] = Timber::get_terms( 'oferta_category' );
$context['bodyClass'] = 'single';
$context['comment_form'] = TimberHelper::get_comment_form();

$programa_id = get_post_meta($post->ID, 'programa', true);
$context['programa_codigo'] = get_post_meta($programa_id, 'codigo', true);
$context['programa_link'] = get_permalink($programa_id);
$context['programa_nome'] = get_the_title($programa_id);
$context['ofertas_link']	= get_permalink(get_field('paxina_de_emprego', 'option'));


if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
