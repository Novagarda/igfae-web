<?php
/*
 * Third party plugins that hijack the theme will call wp_head() to get the header template.
 * We use this to start our output buffer and render into the view/page-plugin.twig template in footer.php
 */
$GLOBALS['timberContext'] = Timber::get_context();
if($args['titulo_pax']){
    $GLOBALS['timberContext']['titulo_pax'] = $args['titulo_pax'];
}

ob_start();
