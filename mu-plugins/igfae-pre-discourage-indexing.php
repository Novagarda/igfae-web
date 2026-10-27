<?php
/**
 * Pre/staging only — deployed via deploy-pre.yml, not prod.
 *
 * Prod DB sync sets blog_public=1 (same as production). This mu-plugin keeps
 * pre non-indexable even if the option is wrong or someone imports SQL manually.
 *
 * @package IGFAE
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'pre_option_blog_public', static function (): string {
	return '0';
}, 10, 0 );

add_filter( 'wp_robots', static function ( array $robots ): array {
	$robots['noindex']   = true;
	$robots['nofollow']  = true;
	$robots['noarchive'] = true;

	return $robots;
}, 99 );

add_action( 'send_headers', static function (): void {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}
	if ( headers_sent() ) {
		return;
	}
	header( 'X-Robots-Tag: noindex, nofollow, noarchive', true );
}, 1 );
