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
$context['post'] 				= $post;
$context['categories'] 			= Timber::get_terms( 'category' );
$context['bodyClass'] 			= 'single';
$context['comment_form'] 		= TimberHelper::get_comment_form();
$context['proxectos_link'] 		= get_permalink(get_field('paxina_de_proxectos', 'option'));
$context['persoal_link']		= get_permalink(get_field('pagina_de_personal', 'option'));
$context['publicacions_link'] 	= get_permalink(get_field('paxina_de_publicacions', 'option'));

$prog_args = array(
	'post_type'		=> 'programa',
	'meta_key'		=> 'area',
	'meta_value'	=> get_the_id()
);
$prog_query = new WP_Query( $prog_args );
$codigos_programas = array();
$list_programas = '';

if ( $prog_query->have_posts() ) {
	while ( $prog_query->have_posts() ) {
		$prog_query->the_post();
		$prog_area = get_field('id_do_programa', get_the_id());
		array_push($codigos_programas, $prog_area);
	}
	$list_programas = implode(',', $codigos_programas);
}
wp_reset_postdata();

$context['programas_da_area'] = $list_programas;


if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
