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

/**
 * Privacy policy URL for the cookie banner.
 * ACF options link first, then WordPress Settings → Privacy.
 */
function igfae_privacy_policy_url(): string {
	$acf = get_field( 'privacy_page', 'option' );

	if ( is_array( $acf ) && ! empty( $acf['url'] ) ) {
		return (string) $acf['url'];
	}

	if ( is_string( $acf ) && $acf !== '' ) {
		return $acf;
	}

	$wp_url = get_privacy_policy_url();

	return is_string( $wp_url ) ? $wp_url : '';
}

function mytheme_timber_context( $context ) {
    $context['options'] = get_fields('option');
    $context['cookie_statement_url'] = igfae_privacy_policy_url();
    return $context;
}

add_filter( 'timber_context', 'mytheme_timber_context' );
add_filter( 'timber/context', 'mytheme_timber_context' );
