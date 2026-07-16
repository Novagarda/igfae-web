<div class="filtros" id="caixa-filtros">
    <button id="close-filtros" type="button" class="btnclose"></button>
    <?php
    global $wp_query;

    $years = [ 'all' => __( 'Todos', 'ifgae-content-viewer' ) ];
    foreach( IgfaeEvent::getAllYears() as $year ) {
        $years[ $year ] = $year;
    }

    if ( isset( $wp_query->query_vars[ 'date' ] ) ) {
        $year = intval( urldecode( $wp_query->query_vars[ 'date' ] ) );
    } else {
        $year = NULL;
        $yearText = 'all';
    }
    if ( isset( $wp_query->query_vars[ 'type' ] ) ) {
        $type = strtolower($wp_query->query_vars[ 'type' ]);
    } else {
        $type = 'all';
    }
    $eventTypes = IgfaeEvent::getAvailableTypes();
    if ( !isset($eventTypes[$type]) ) {
        $type = 'all';
    }
    ?>

    <?php
        igfae_get_template_part('template', 'form-complex', [
            'view' => 'events',
            'campos'    =>[
                '1'     => [
                    'label'     => __('Categorías', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type',
                    'tipo'      => 'radio',
                    'items' => $eventTypes,
                    'selected'  => $type
                ],
                '2'      => [
                    'label'     => __('Anos', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type2',
                    'tipo'      => 'radio',
                    'items'     => $years,
                    'selected'  => $year
                ] 
            ]
        ]);
?>

    
</div>

<div id="igfae-search-results" class="igfae-search-results">
    <?php 
    $eventos = IgfaeEvent::fetch( '', $year, 0, $limit, $type, 'Y', $order, '', $destacados, $upcoming);
    if(!$paginar){
        igfae_get_template_part( 'events', 'block', $eventos );
    }else{
        igfae_get_template_part( 'events', 'view-table', $eventos );
    }
    ?>
</div>
<div id="loader" class="hidden"></div>
