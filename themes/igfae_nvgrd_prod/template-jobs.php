<?php
/**
 * Template Name: Jobs
 *
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

$today = date("Ymd");

$args_jobs_abertos = array(
    'numberposts'	=> -1,
    'post_type' => 'oferta',
    'meta_query' => array(
        array(
            'key' => 'data_limite',
            'value' => date("Ymd"),
            'compare' => '>=',
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

$args_jobs_pechados = array(
    'numberposts'	=> -1,
    'post_type' => 'oferta',
    'meta_query' => array(
        array(
            'key' => 'data_limite',
            'value' => date("Ymd"),
            'compare' => '<',
        ),
    ),
);
$context['jobs_pechados'] = Timber::get_posts( $args_jobs_pechados );
foreach ($context['jobs_pechados'] as $key => $value) {
    
    $programa_id = get_post_meta($context['jobs_pechados'][$key]->id, 'programa', true);
    if($programa_id){
        $programa_codigo = get_post_meta($programa_id, 'codigo', true);
        $programa_link = get_permalink($programa_id);
        $context['jobs_pechados'][$key]->codigo_programa = $programa_codigo;
        $context['jobs_pechados'][$key]->link_programa = $programa_link;
    }
}

// Posts por categorias
$categorias = [];
$categorias_posts_pechados = [];
$categorias_slugs = get_terms(array(
    'taxonomy'   => 'oferta_category',
    'hide_empty' => false,
    'fields'    => 'slugs'
));
foreach ($categorias_slugs as $slug) {
    $categoria = get_term_by('slug', $slug, 'oferta_category');
    if ($categoria) {
        $args = [
            'post_type'      => 'oferta',
            'posts_per_page' => -1,  // Number of posts per category
            'tax_query'      => [
                [
                    'taxonomy' => 'oferta_category',
                    'field'    => 'slug',
                    'terms'    => $slug,
                ],
            ],
            'meta_query' => array(
                array(
                    'key' => 'open_check',
                    'value' => 1,
                    'compare' => '!=',
                ),
            ),
        ];

        $categorias[] = [
            'name'  => $categoria->name,
            'slug'  => $categoria->slug,
            'posts' => Timber::get_posts($args),
        ];

        $args_pechadas = [
            'post_type'      => 'oferta',
            'posts_per_page' => -1,  // Number of posts per category
            'tax_query'      => [
                [
                    'taxonomy' => 'oferta_category',
                    'field'    => 'slug',
                    'terms'    => $slug,
                ],
            ],
            'meta_query' => array(
                array(
                    'key' => 'open_check',
                    'value' => 1,
                    'compare' => '==',
                ),
            ),
        ];

        $categorias_posts_pechados[] = [
            'name'  => $categoria->name,
            'slug'  => $categoria->slug,
            'posts' => Timber::get_posts($args_pechadas),
        ];
    }
}
$context['categorias'] = $categorias;
$context['categorias_posts_pechados'] = $categorias_posts_pechados;

/*
$logInfo .= "[".date("d/m/Y - h:i:s")."] Categorias: ".print_r($categorias, true)."\n";
file_put_contents(get_stylesheet_directory().'/custom.log', $logInfo, FILE_APPEND);
*/
Timber::render( array( 'page-jobs.twig', 'page.twig' ), $context );
