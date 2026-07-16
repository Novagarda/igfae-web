<?php

/*
 *
 * Custom Post Types
 *
 */

// Note that you only need the arguments and register_post_type function here. They are hooked into WordPress in functions.php.

nvgrd_rexistra_post_type( 'area-investigacion', 'area-de-investigacion', 'Área', false, true );
nvgrd_rexistra_post_type( 'programa', 'programa-de-investigacion', 'Programa', false, true );
nvgrd_rexistra_post_type( 'experimento', 'experimento', 'Experimento', false, true );
nvgrd_rexistra_post_type( 'actividad', 'actividad', 'Actividad', true, true );
nvgrd_rexistra_post_type( 'alumni', 'alumni', 'Alumni', false, false );
nvgrd_rexistra_post_type( 'oferta', 'oferta', 'Ofertas', false, true );
nvgrd_rexistra_post_type( 'carreira', 'carreira', 'Carreira e formación', false, true );


function nvgrd_rexistra_post_type($nome, $slug, $titulo, $parent, $publicly_queryable){
    $soporta = array('title');
    if($parent){
        array_push($soporta, 'page-attributes');
    }
    $labels = array(

        'name'                     => __( $titulo, 'nvgrd' ),
        'singular_name'            => __( $titulo, 'nvgrd' ),
        'add_new'                  => __( 'Add New', 'nvgrd' ),
        'add_new_item'             => __( 'Add New', 'nvgrd' ),
        'edit_item'                => __( 'Edit', 'nvgrd' ),
        'new_item'                 => __( 'New', 'nvgrd' ),
        'view_item'                => __( 'View', 'nvgrd' ),
        'view_items'               => __( 'View', 'nvgrd' ),
        'search_items'             => __( 'Search', 'nvgrd' ),
        'not_found'                => __( 'Nothing found.', 'nvgrd' ),
        'not_found_in_trash'       => __( 'Nothing found in Trash.', 'nvgrd' ),
        'parent_item_colon'        => __( 'Parent:', 'nvgrd' ),
        'all_items'                => __( 'All', 'nvgrd' ),
        'archives'                 => __( 'Archives', 'nvgrd' ),
        'attributes'               => __( 'Attributes', 'nvgrd' ),
        'insert_into_item'         => __( 'Insert into this', 'nvgrd' ),
        'uploaded_to_this_item'    => __( 'Uploaded to this', 'nvgrd' ),
        'filter_items_list'        => __( 'Filter list', 'nvgrd' ),
        'filter_by_date'           => __( 'Filter by date', 'nvgrd' ),
        'items_list_navigation'    => __( 'List navigation', 'nvgrd' ),
        'items_list'               => __( 'List', 'nvgrd' ),
        'item_published'           => __( 'Published.', 'nvgrd' ),
        'item_published_privately' => __( 'Published privately.', 'nvgrd' ),
        'item_reverted_to_draft'   => __( 'Reverted to draft.', 'nvgrd' ),
        'item_scheduled'           => __( 'Scheduled.', 'nvgrd' ),
        'item_updated'             => __( 'Updated.', 'nvgrd' ),
        'item_link'                => __( 'Link', 'nvgrd' ),
        'item_link_description'    => __( 'A link to this', 'nvgrd' ),
    
    );
    
    $args = array(
    
        'labels'                => $labels,
        'public'                => true,
        'hierarchical'          => $parent,
        'exclude_from_search'   => false,
        'publicly_queryable'    => $publicly_queryable,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'show_in_nav_menus'     => true,
        'show_in_admin_bar'     => false,
        'show_in_rest'          => true,
        'menu_position'         => null,
        'menu_icon'             => 'dashicons-admin-post',
        'capability_type'       => 'post',
        'capabilities'          => array(),
        'supports'              => $soporta,
        'taxonomies'            => array(),
        'has_archive'           => false,
        'rewrite'               => array( 'slug' => $slug ),
        'query_var'             => true,
        'can_export'            => true,
        'delete_with_user'      => false,
        'template'              => array(),
        'template_lock'         => false,
    
    );
    
    register_post_type( $nome, $args );
}