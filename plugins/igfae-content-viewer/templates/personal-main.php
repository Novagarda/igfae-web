<div class="filtros" id="caixa-filtros">
    <button id="close-filtros" type="button" class="btnclose"></button>
        <?php 
        global $wp_query;
        $catText = __('Todas', 'ifgae-content-viewer');
        $cat = NULL;

        $categories = [ 'ALL' => $catText ];
        
        //Obtenemos el parametro TYPE de la URL.
        if ( isset($wp_query->query_vars[ 'type' ] ) ) {
            $cat = sanitize_title( urldecode( $wp_query->query_vars[ 'type' ] ) );
        }

        foreach( IgfaePersonal::getAllCategories() as $key => $category ) {
            $categories[ $key ] = $category;
            if ( $cat == sanitize_title( $category ) ) {
                $catText = $category;
                $cat = $category;
            }
        }

        $groups = IgfaePersonal::getAllGroupNames();

        $groups['ALL'] =  __('Todos', 'ifgae-content-viewer');
        $group = NULL;

        if (isset($_GET['programm'])) {
            $group = $_GET['programm'];
        }
        if (isset($_GET['programm'])) {
            $programaTxt = $_GET['programm'];
        }else{
            $programaTxt = 'ALL';
        }

        ksort( $groups, SORT_STRING );

        //Obtenemos el parametro GROUP de la URL
        if ( isset( $wp_query->query_vars[ 'group' ] ) ) {
            $group = sanitize_title( urldecode( $wp_query->query_vars[ 'group' ] ) );

            foreach( $groups as $key => $grp ) {
                if ( $group == sanitize_title( $grp ) ) {
                    $group = $key;
                    break;
                }
            }
        }
        $logInfo .= "[".date("d/m/Y - h:i:s")."] cat: ".print_r($cat, true)."\n";
        //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);        

        igfae_get_template_part('template', 'form-complex', [
            'view' 	=> 'personal',
            'campos'    =>[
                '1'     => [
                    'label'     => __('Categorías', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type',
                    'tipo'      => 'radio',
                    'items'     => $categories,
                    'selected'  => 'ALL'
                ],
                '2'      => [
                    'label'     => __('Programa de investigación', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type2',
                    'tipo'      => 'checkbox',
                    'items'     => $groups,
                    'selected'  => $programaTxt
                ] 
            ]
        ]); 
?>

</div>
<div id="igfae-search-results" class="igfae-search-results">
    <?php igfae_get_template_part('personal', 'table3-view', [
        'personal' => IgfaePersonal::fetch('', $cat, $group)
    ]); 
//    $logInfo .= "[".date("d/m/Y - h:i:s")."] cat: ".print_r($cat, true)."\n";
 //   file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
    ?>
</div>
<div id="loader" class="hidden"></div>
