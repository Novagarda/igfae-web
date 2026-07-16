<div class="filtros" id="caixa-filtros">
    <button id="close-filtros" type="button" class="btnclose"></button>
        <?php 
        global $wp_query;

    	$types = [ 'all' => __('Todos', 'ifgae-content-viewer') ];
    	$years = [ 'all' => __('Todos', 'ifgae-content-viewer') ];
//        $groups = IgfaeProject::getAllGroups();
        $type = NULL;
        $research_line = NULL;

        $groups = IgfaeProject::getAllGroups();

        $groups['ALL'] =  __('Todos', 'ifgae-content-viewer');
        $group = NULL;

        if (isset($_GET['programm'])) {
            $group = $_GET['programm'];
        }else{
            $group = 'ALL';            
        }
        
        ksort( $groups, SORT_STRING );

        
     	foreach (IgfaeProject::getAllYears() as $year) {
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
            'view' => 'projects',
            'campos'    =>[
                '1'     => [
                    'label'     => __('Programa de investigación', 'ifgae-content-viewer'),
                    'nome'      => 'igfae-type',
                    'tipo'      => 'checkbox',
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
/*
    	igfae_get_template_part('template', 'form', [
	        'view' 		=> 'projects',
	        'types'     => [ 'items' => $types, 'selected' => $type ],
            'types2'    => [ 'items' => $years, 'selected' => $yearText ]
    	]);
*/
    ?>
</div>
<div id="igfae-search-results" class="igfae-search-results">
    <?php 
        $info = IgfaeProject::fetch('', $type, $year, 0, 10, $research_line);
        igfae_get_template_part('project', 'view-table', [
            'projects' 		=> $info['projects'],
            'pagination' 	=> $info['pagination']
        ]); 
    ?>
</div>
<div id="loader" class="hidden"></div>