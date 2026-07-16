<?php

/*
 *
 * Edit backoffice columns
 *
 */


 /**
  * Modifica la vista de posts en el backoffice de WordPress para mostrar una imagen personalizada en lugar de la imagen destacada del post.
  */
 
//  Añade la columna "Imagen destacada"
function custom_posts_columns( $columns ) {
    $new_columns = array();
//    $new_columns['featured_image'] = __( 'Imagen destacada', 'textdomain' );
    unset( $columns['date'] ); // Elimina la columna de fecha
    return array_merge( $new_columns, $columns );
}
add_filter( 'manage_posts_columns', 'custom_posts_columns' );

 
 // Muestra la imagen personalizada en la columna "Imagen destacada"
 function custom_posts_custom_column( $column, $post_id ) {
     if ( $column === 'featured_image' ) {
//         $image = get_field( 'imageFeatured', $post_id ); // Obtiene la imagen del campo personalizado "imageFeatured"
         if ( $image ) {
             echo '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '" width="50" height="50" />'; // Muestra la imagen personalizada
         } else {
             echo '-';
         }
     }
 }
 add_action( 'manage_posts_custom_column', 'custom_posts_custom_column', 10, 2 );
 
 // Ordena la columna "Imagen destacada"
 function custom_posts_columns_sortable( $columns ) {
//     $columns['featured_image'] = 'featured_image';
     return $columns;
 }
 add_filter( 'manage_edit-post_sortable_columns', 'custom_posts_columns_sortable' );

 // Carga la hoja de estilo en el backoffice
function load_custom_wp_admin_style() {
    wp_enqueue_style( 'custom_wp_admin_css', get_template_directory_uri() . '/lib/edit-back.css', false, '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );