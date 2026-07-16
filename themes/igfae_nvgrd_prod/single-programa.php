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

$area_id 						= get_field('area', $post->ID);
$context['area_titulo'] 		= get_the_title($area_id);
$context['area_codigo'] 		= get_field('codigo', $area_id);
$context['area_link'] 			= get_permalink($area_id);
$context['proxectos_link'] 		= get_permalink(get_field('paxina_de_proxectos', 'option'));
$context['persoal_link'] 		= get_permalink(get_field('pagina_de_personal', 'option'));
$context['publicacions_link']	= get_permalink(get_field('paxina_de_publicacions', 'option'));
$context['emprego_link']		= get_permalink(get_field('paxina_de_emprego', 'option'));

$args_jobs_abertos = array(
    'numberposts'	=> -1,
    'post_type' => 'oferta',
    'meta_query' => array(
        array(
            'key' => 'programa',
            'value' => $post->ID,
            'compare' => '=',
        ),
    ),
);

$context['jobs_abertos'] = Timber::get_posts( $args_jobs_abertos );
foreach ($context['jobs_abertos'] as $key => $value) {

    $programa_id = get_post_meta($context['jobs_abertos'][$key]->id, 'programa', true);
    if($programa_id){
        $programa_codigo = get_post_meta($programa_id, 'codigo', true);
        $programa_link = get_permalink($programa_id);
        $context['jobs_abertos'][$key]->codigo_programa = $programa_codigo;
        $context['jobs_abertos'][$key]->link_programa = $programa_link;
    }

}

if ( post_password_required( $post->ID ) ) {
	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
