<?php
/**

	Query Vars

**/

add_filter('query_vars', 'register_query_vars_igfae');
function register_query_vars_igfae($query_vars)
{
    $query_vars[] = 'event';
    $query_vars[] = 'type';
    $query_vars[] = 'date';
    $query_vars[] = 'group';
    return $query_vars;   
}

/**

	Routes

**/

add_action( 'init', 'rewrite_add_rule_single_event', 100, 0 );
function igfae_get_event_route_slugs() {
	static $cachedSlugs = null;
	if (is_array($cachedSlugs)) {
		return $cachedSlugs;
	}

	// Slugs are structural: defined statically to avoid any DB query on every
	// page load. Adding a new event type to the DB requires adding it here too.
	$cachedSlugs = [ 'outreach', 'seminar', 'talk', 'conference', 'training', 'coloquio', 'exhibition', 'award', 'dissertation' ];
	return $cachedSlugs;
}

add_filter('redirect_canonical', 'igfae_disable_canonical_for_event_routes', 10, 2);
function igfae_disable_canonical_for_event_routes($redirect_url, $requested_url) {
	if (empty($requested_url)) {
		return $redirect_url;
	}

	$path = wp_parse_url($requested_url, PHP_URL_PATH);
	if (!is_string($path) || $path === '') {
		return $redirect_url;
	}

	$path = trim($path, '/');
	if ($path === '') {
		return $redirect_url;
	}

	$parts = array_values(array_filter(explode('/', $path), 'strlen'));
	if (count($parts) < 2) {
		return $redirect_url;
	}

	// Support installs in subdirectories (e.g. /igfae/conference/2259)
	// by matching the last two URL segments as /{event-slug}/{event-id}.
	$eventId = $parts[count($parts) - 1];
	$slug = sanitize_title($parts[count($parts) - 2]);
	if (!ctype_digit((string) $eventId)) {
		return $redirect_url;
	}

	$eventSlugs = igfae_get_event_route_slugs();
	if (in_array($slug, $eventSlugs, true)) {
		return false;
	}

	return $redirect_url;
}

function rewrite_add_rule_single_event() {
	add_query_arg( 'event', '' );
	add_query_arg( 'type', '' );
	add_query_arg( 'date', '' );
	add_query_arg( 'group', '' );

	$eventSlugs = igfae_get_event_route_slugs();
	foreach ( $eventSlugs as $slug ) {
		add_rewrite_rule(
	        "{$slug}/([0-9]+)",
		    "index.php?pagename={$slug}&event=\$matches[1]",
		    'top'
		);
	}
	
	//flush_rewrite_rules();
}

add_action('template_redirect', 'template_redirect_igfae_event', 100);
function template_redirect_igfae_event() {
    global $wp_query, $post;
    
    $event = NULL;
    if ( !empty($wp_query->query) and array_key_exists('pagename',$wp_query->query)) {
		$eventSlugs = igfae_get_event_route_slugs();
		if ( in_array( $wp_query->query['pagename'], $eventSlugs, true ) && !empty($wp_query->query['event']) ) {
			// Temporary backward compatibility: old /talk/{id} URLs redirect to canonical /conference/{id}.
			if ($wp_query->query['pagename'] === 'talk') {
				$canonicalUrl = home_url('/conference/' . intval($wp_query->query['event']));
				wp_safe_redirect($canonicalUrl, 301);
				exit();
			}

			$event = IgfaeUnifiedEvent::getById( $wp_query->query['event'] );

			if ( !is_null( $event ) ) {
				$basePageId = function_exists('get_field') ? get_field('paxina_de_axenda', 'option') : null;
				$basePost = null;
				if ( $basePageId ) {
					$basePost = get_post((int) $basePageId);
				}
				if ( !$basePost ) {
					$basePost = get_page_by_path( $wp_query->query['pagename'] );
				}
				if ( $basePost instanceof WP_Post ) {
					$post = $basePost;
					$wp_query->post = $basePost;
					$wp_query->posts = [ $basePost ];
					$wp_query->queried_object = $basePost;
					$wp_query->queried_object_id = $basePost->ID;
					setup_postdata($basePost);
				}

				$wp_query->is_404 = false;
				$wp_query->is_single = true;
				$wp_query->is_singular = true;

				igfae_get_template_part('page', 'single-event', [
					'event' => $event
				]);
				if ( $basePost instanceof WP_Post ) {
					wp_reset_postdata();
				}
			} else {
				$wp_query->set_404();
				status_header( 404 );
				get_template_part( 404 );
			}
			exit();
		}
    }
	
}
