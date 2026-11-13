<?php
/**
 * Theme 404 response (Timber + body.page-404).
 */

/**
 * @param bool $exit Stop request after render (virtual routes e.g. persoa).
 */
function igfae_render_404( bool $exit = false ): void {
	global $wp_query;

	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();

	$context               = Timber::get_context();
	$context['bodyClass']  = 'page-404';
	Timber::render( '404.twig', $context );

	if ( $exit ) {
		exit;
	}
}
