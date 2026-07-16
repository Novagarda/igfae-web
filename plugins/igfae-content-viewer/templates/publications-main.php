<div class="filtros" id="caixa-filtros">
    <button id="close-filtros" type="button" class="btnclose"></button>
    <?php 
        global $wp_query;
        $types = [
            'all'       => __('Todos', 'ifgae-content-viewer'),
            'article'   => __('Artigos', 'ifgae-content-viewer'),
            'thesis'    => __('Teses', 'ifgae-content-viewer')
        ];

        $research_line = NULL;

        $years = [ 'all' => __('Todos', 'ifgae-content-viewer') ];

        foreach ( IgfaePublication::getAllYears() as $year ) {
            $years[ $year ] = $year;
        }

        if ( isset($wp_query->query_vars[ 'date' ] ) ) {
            $year = intval( urldecode( $wp_query->query_vars[ 'date' ] ) );
            $yearText = ''.$year;
        } else{
            $year = NULL;
            $yearText = 'all';
        }
        
        if ( isset($wp_query->query_vars[ 'type' ] ) ) {
            $type = strtolower( $wp_query->query_vars[ 'type' ] );
        } else {
            $type = 'all';
        }

        $groups = IgfaeProject::getAllGroups();

        $groups['ALL'] =  __('Todos', 'ifgae-content-viewer');
        $group = NULL;

        if (isset($_GET['programm'])) {
            $group = $_GET['programm'];
        }else{
            $group = 'ALL';            
        }

        ksort( $groups, SORT_STRING );

        if (isset($_GET['programm'])) {
            $research_line = $_GET['programm'];
        }
        /*
        igfae_get_template_part('template', 'form-complex', [
            'view' => 'publications',
            'types'     => ['items' => $types, 'selected' => $type ],
            'types2'    => ['items' => $years, 'selected' => $yearText ]
        ]);
        */
        
        igfae_get_template_part('template', 'form-complex', [
            'view' => 'publications',
            'campos'    =>[
                '3'      => [
                    'label'     => __('Programa de investigación', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type3',
                    'tipo'      => 'checkbox',
                    'items'     => $groups,
                    'selected'  => $group
                ],                
                '1'     => [
                    'label'     => __('Tipo de publicación', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type',
                    'tipo'      => 'radio',
                    'items'     => $types,
                    'selected'  => $type
                ],
                '2'      => [
                    'label'     => __('Anos', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type2',
                    'tipo'      => 'radio',
                    'items'     => $years,
                    'selected'  => $yearText
                ]
            ]
//            'types'     => ['items' => $types, 'selected' => $type ],
//            'types2'    => ['items' => $years, 'selected' => $yearText ]
        ]);
        
    ?>
</div>

<div id="igfae-search-results" class="igfae-search-results">
    <?php igfae_get_template_part( 'publications', 'view-table', IgfaePublication::all( $type, $year, 0, 10, $research_line ) ); ?>
</div>
<div id="loader" class="hidden"></div>
