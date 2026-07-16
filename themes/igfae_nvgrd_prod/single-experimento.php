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
$context['post'] 					= $post;
$context['categories'] 				= Timber::get_terms( 'category' );
$context['bodyClass'] 				= 'single';
$context['comment_form'] 			= TimberHelper::get_comment_form();

$context['proxectos_link'] 			= get_permalink(get_field('paxina_de_proxectos', 'option'));
$context['persoal_link'] 			= get_permalink(get_field('pagina_de_personal', 'option'));
$context['publicacions_link']		= get_permalink(get_field('paxina_de_publicacions', 'option'));
$id_programa 						= get_field('programa', get_the_id());
$id_area 							= get_field('area', $id_programa);
$context['area_codigo']				= get_field('codigo', $id_area);
$context['area_titulo']				= get_the_title($id_area);
$context['area_link']				= get_permalink($id_area);
$context['prog_codigo']				= get_field('codigo', $id_programa);
$context['prog_titulo']				= get_the_title($id_programa);
$context['prog_link']				= get_permalink($id_programa);
$context['programa_do_experimento'] = get_field('id_do_programa', $id_programa);


if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
