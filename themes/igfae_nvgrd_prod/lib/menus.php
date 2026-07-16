<?php

/*
 *
 * Custom Menus
 *
 */

// Registramos los menus a utilizar

register_nav_menus(
    array(
        'main-menu'         => 'Main Menu',
        'top-menu'          => 'Top Menu',
        'top-right'          => 'Top Menu Right',
        'social-menu'       => 'Social Menu',
        'footer-menu'       => 'Footer Menu',
        'footer-top-menu'   => 'Footer Top Menu',
    )
);


add_filter( 'timber/context', 'add_to_context' );

function add_to_context( $context ) {

    $context['footer']      = new \Timber\Menu( 'footer-menu' );
    $context['footertop']   = new \Timber\Menu( 'footer-top-menu' );
    $context['social']      = new \Timber\Menu( 'social-menu' );
    $context['main']        = new \Timber\Menu( 'main-menu' );
    $context['top']         = new \Timber\Menu( 'top-menu' );
    $context['topRight']   = new \Timber\Menu( 'top-right' );

    return $context;
}
