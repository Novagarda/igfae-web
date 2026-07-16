<?php

/*
 *
 * Widgets and Sidebars
 *
 */

// Register your widgets and sidebars here.


/*
 * Añadimos menu de opciones generales al backoffice
 * Aspectos generales del tema, logos footer, etc...
 */

if( function_exists('acf_add_options_page') ) {
    acf_add_options_page('Opciones generales');
}

add_filter( 'timber_context', 'mytheme_timber_context'  );

function mytheme_timber_context( $context ) {
    $context['options'] = get_fields('option');
    return $context;
}
