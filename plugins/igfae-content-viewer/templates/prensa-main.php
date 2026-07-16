<div class="filtros" id="caixa-filtros">
    <button id="close-filtros" type="button" class="btnclose"></button>
        <?php 
        global $wp_query;

    	$types = [ 'all' => __('Todos', 'ifgae-content-viewer') ];
    	$years = [ 'all' => __('Todos', 'ifgae-content-viewer') ];
        $type = NULL;
        $research_line = NULL;


        // CATEGORIAS
        $groups = IgfaePrensa::getAllGroups();
/*
        $logInfo .= "[".date("d/m/Y - h:i:s")."] Rows: ".print_r($groups, true)."\n";
		file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
        $groups['ALL'] =  __('Todos', 'ifgae-content-viewer');
        $group = NULL;

        if (isset($_GET['programm'])) {
            $group = $_GET['programm'];
        }

        ksort( $groups, SORT_STRING );


        // ANOS
        foreach (IgfaePrensa::getAllYears() as $year) {
    		$years[ $year ] = $year;
     	}

        if ( isset($wp_query->query_vars[ 'date' ] ) ) {
            $year = intval( urldecode( $wp_query->query_vars[ 'date' ] ) );
            $yearText = ''.$year;
        } else{
            $year = NULL;
            $yearText = 'all';
        }

        if (isset($_GET['programm'])) {
            $research_line = $_GET['programm'];
        }

        igfae_get_template_part('template', 'form-complex', [
            'view' => 'prensas',
            'campos'    =>[
                '1'     => [
                    'label'     => __('Tipo de medio', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type',
                    'tipo'      => 'radio',
                    'items'     => $groups,
                    'selected'  => $group
                ],
                '2'      => [
                    'label'     => __('Anos', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type2',
                    'tipo'      => 'radio',
                    'items'     => $years,
                    'selected'  => $yearText
                ] 
            ]
        ]);
    ?>
</div>
<div id="igfae-search-results" class="igfae-search-results">
    <?php 
        $info = IgfaePrensa::fetch('', $type, $year, 0, 10);
        
        igfae_get_template_part('prensa', 'view-table', [
            'projects' 		=> $info['projects'],
            'pagination' 	=> $info['pagination']
        ]);
        ?>
</div>
<div id="loader" class="hidden"></div>