<?php
/*
 * Third party plugins that hijack the theme will call wp_head() to get the header template.
 * We use this to start our output buffer and render into the view/page-plugin.twig template in footer.php
 */
add_filter( 'body_class','my_body_classes' );
function my_body_classes( $classes ) {
    $classes[] = 'corpoazul';
    return $classes;
}

global $wp_query;
    
$event = NULL;
$titulo_evento = '';
if ( !empty($wp_query->query) and array_key_exists('pagename',$wp_query->query)) {
    $eventSlugs = [ 'outreach', 'seminar', 'talk', 'conference', 'training', 'coloquio', 'exhibition', 'award', 'dissertation' ];
    if ( in_array($wp_query->query['pagename'], $eventSlugs, true) && !empty($wp_query->query['event']) ) {
        if ( class_exists('IgfaeUnifiedEvent') ) {
            $event = IgfaeUnifiedEvent::getById( $wp_query->query['event'] );
        }
        if ( $event && method_exists($event, 'getTitle') ) {
            $titulo_evento = $event->getTitle();
        }
    }
}

$GLOBALS['timberContext'] = Timber::get_context();
$GLOBALS['timberContext']['titulo_evento'] = $titulo_evento;
ob_start();
