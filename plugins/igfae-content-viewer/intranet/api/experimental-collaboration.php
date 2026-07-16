<?php
add_action("wp_ajax_search_experimental_collaboration_submit", "api_search_experimental_collaboration_submit");
add_action("wp_ajax_nopriv_search_experimental_collaboration_submit", "api_search_experimental_collaboration_submit");
function api_search_experimental_collaboration_submit()
{    
    $draw = isset($_POST['draw'])? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)):0;
    $length = isset($_POST['length'])? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)): 10;
    $offset = isset($_POST['start'])? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)): 0;
    $order = isset($_POST['order'])? $_POST['order']: null;

    $experimentalCollaboration = getSearchRequestModelExperimental();
    
    $info = getSelectExperimentalCollaborations($experimentalCollaboration, $offset, $length, $order);

    $data = [
        "data" => $info['collaboration'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);
    wp_send_json_success();
    exit();
}

// Botón Delete experiment
add_action("wp_ajax_delete_experimental_collaboration_submit", "api_delete_experimental_collaboration_submit");
add_action("wp_ajax_nopriv_delete_experimental_collaboration_submit", "api_delete_experimental_collaboration_submit");
function api_delete_experimental_collaboration_submit()
{
    $id = $_POST['id'];
    // Eliminamos el experiment
    deleteExperiment($id);
    wp_send_json_success();
    exit();
}

// Botón Edit de la pantalla del detalle de experimento
add_action("wp_ajax_edit_experimental_collaboration_submit", "api_edit_experimental_collaboration_submit");
add_action("wp_ajax_nopriv_edit_experimental_collaboration_submit", "api_edit_experimental_collaboration_submit");
function api_edit_experimental_collaboration_submit()
{
    wp_send_json_success();
    exit();
}

// New Experimental Collaboration
add_action("wp_ajax_new_experimental_collaboration_submit", "api_new_experimental_collaboration_submit");
add_action("wp_ajax_nopriv_new_experimental_collaboration_submit", "api_new_experimental_collaboration_submit");
function api_new_experimental_collaboration_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();
    
    // Id Experiment
    $experimentalCollaborationId   = trim(filter_var($_POST['idExperiment'], FILTER_SANITIZE_STRING));

    // Nombre
    $name           = trim(filter_var($_POST['nombre'], FILTER_SANITIZE_STRING));

    // Nombre
    $abbreviation   = trim(filter_var($_POST['abbreviation'], FILTER_SANITIZE_STRING));

    //Start date
    $startDate      = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
    
    //End date
    $endDate        = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));

    //MOU signature date
    $mouDate        = trim(filter_var($_POST['mouDate'], FILTER_SANITIZE_STRING));

    //MOU link
    $mouLink    = NULL;
    if(isset($_POST['mouLink']) && !empty($_POST['mouLink'])){
        $mouLink    = [];
        foreach ($_POST['mouLink'] as $item) {
            $mouLink[] = $item;
        }
    }

    //IGFAE asociated users
    $asociatedUsers    = NULL;
    if(isset($_POST['asociatedUsers']) && !empty($_POST['asociatedUsers'])){
        $asociatedUsers    = [];
        foreach ($_POST['asociatedUsers'] as $item) {
            $asociatedUsers[] = $item;
        }
    }

    //Web page
    $webPage        = trim(filter_var($_POST['webPage'], FILTER_SANITIZE_STRING));

    //External Collaboration
    $external         = null;
    if (isset($_POST['externalCollaboration']) && !empty($_POST['externalCollaboration'])) {
        if ($_POST['externalCollaboration'] == __("Yes")) {
            $external = 1;
        } else {
            $external = 0;
        }
    }

     // Abstract textarea
     $abstract         = NULL;
     if (isset($_POST['abstract']) && !empty($_POST['abstract']) && !empty(strip_tags($_POST['abstract']))) {       
        $textAbstract = str_replace('\"', '"', $_POST["abstract"]); 
        $abstract = $purifier->purify(trim($textAbstract));      
     }

    //Research Group select multiple
    $researchGroup     = NULL;
    if (isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])) {
        $researchGroup     = [];
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }
    //Research Area select multiple
    $researchArea      = NULL;
    if(isset($_POST['researchArea']) && !empty($_POST['researchArea'])){
        $researchArea      = [];
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }
    //Research Project
    $researchProject   = NULL;
    if(isset($_POST['researchProject']) && !empty($_POST['researchProject'])){
        $researchProject   = [];
        foreach ($_POST['researchProject'] as $item) {
            $researchProject[] = $item;
        }
    }
    //Work Line select multiple
    $workLine         = NULL;
    if(isset($_POST['workline']) && !empty($_POST['workline'])){
        $workLine         = [];
        foreach ($_POST['workline'] as $item) {
            $workLine[] = $item;
        }
    }
    //Thesis director select multiple
    $coordinatorIgfae         = NULL;
    if (isset($_POST['thesisDirector']) && !empty($_POST['thesisDirector'])) {
        $coordinatorIgfae      = trim(filter_var($_POST['thesisDirector'], FILTER_SANITIZE_STRING));
    }
   
    //Research Program select multiple
    $researchProgram    = NULL;
    if(isset($_POST['researchProgram']) && !empty($_POST['researchProgram'])){
        $researchProgram    = [];
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }

    //Associated Publications
    $asociatedPublications    = NULL;
    if(isset($_POST['associatedPublications']) && !empty($_POST['associatedPublications'])){
    $asociatedPublications    = [];
        foreach ($_POST['associatedPublications'] as $item) {
            $asociatedPublications[] = $item;
        }
    }

    //Facilities
    $facilities = trim(filter_var($_POST['facilities'], FILTER_SANITIZE_STRING));
    
    // Comments textarea
    $comments         = NULL;
    if (isset($_POST['comments']) && !empty($_POST['comments']) && !empty(strip_tags($_POST['comments']))) {
        $textComments = str_replace('\"', '"', $_POST["comments"]); 
        $comments = $purifier->purify(trim($textComments));
    }

    // Validated select
    $validated         = NULL;
    if (isset($_POST['validated']) && !empty($_POST['validated'])) {
        $validated = 1;
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede validar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de validar
        $validated = 0;
    }
    // Published select
    $published         = NULL;
    if (isset($_POST['published']) && !empty($_POST['published'])) {
        $published = 1;
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede publicar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de publicar
        $published = 0;
    }

    $experimentalCollaboration = new IntranetExperimentalCollaboration(
        $experimentalCollaborationId, 
		$name,
		$researchArea,
		$researchProgram,
		$coordinatorIgfae,
		$startDate,
        $endDate,
        NULL,
		$researchProject,
		$researchGroup,
		$workLine,
		$facilities,
		$validated,
		$published,
		//Campos de pagina new
		$abbreviation,
		$mouDate,
		$mouLink,
		$webPage,
		$external,
		$abstract,
		$asociatedPublications,
        $asociatedUsers,
		$comments
    );

    if (!verificarValidarInformacion($validated, $published)) {
        // No se permite publicar sin haber validado previamente
        wp_send_json_error(new WP_Error('-1', __('In order to publish, the information must first be validated')));
    } else {
        // $idExperiment-> id del experimento a editar, $idExperimentSave-> id del nuevo experimento
        $idExperimentSave = saveExperimentalCollaboration($experimentalCollaboration);
        $return = array(
            'experimentalCollaborationId' => $experimentalCollaborationId,
            'idExperimentSave'      => $idExperimentSave
        );
        wp_send_json_success($return);
    }
    
    exit();
}

/** EXPORT */
add_action( 'admin_action_export_experimental_collaboration', 'export_experimental_collaboration' );
function export_experimental_collaboration()
{
    //definir con internacionalizacion
    $headers = [__("ID"),__("Name"),__("Abbreviation"), __("IP coordinator IGFAE"), __("MOU signature date"), __("MOU link"),
                __("Start date"),__("End date"),__("Associated users"),__("Web page"),
                __("Abstract"),__("Research areas"),__("Research programs"),__("Research groups"),
                __("Activities"),__("Research projects"),__("Production projects"),
                __("Facilitites"),__("External collaboration"),__("Comments"),__("Validated"),
                __("Validated by"),__("Validated on"),__("Published"),__("Published by"),__("Published on"),
                __("Consortium"),__("Project reference"),__("Title EN"), __("Title ES"), __("Title GL"),
                __("Scope"),__("Description"), __("Activity type"),__("USC Activity type"), __("USC Subactivity type"), __("USC Project reference"),
                __("Call"), __("Management center"), __("Amount without overhead"), __("Granted"), __("Concession date"), __("Office code"), __("Reference 2"),
                __("Partners"), __("Financing agencies"), __("Total amount"), __("Annual justification"), __("Cofinancing"), __("Cofinancing type"),
                __("Resolution"), __("Extended"), __("End date extended"), __("FEDER"), __("FEDER %"),  __("International consortium"),
                __("Competitive"),__("Competitive call"), __("Keywords"), __("Indirect costs"), __("IGFAE leader"), __("Project type"), __("Budget link"),
                __("Annual gifted USC"), __("Annual executed"), __("Awarded allotment"), __("Annual justified"),
                __("Principal researcher"), __("Asocciate researchers"), __("Technician"), __("Work team users ID"),__("Work team users")
            ];

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $experimentalCollaboration = getSearchRequestModelExperimental();

    //creamos un método propio para el export. Meter query correcta
    $experimets = getExport($experimentalCollaboration);

    //funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($experimets, function (&$v, $k) {
        $v = get_object_vars($v);
        if (!empty($v)) {

            if (isset($v['External'])) {
                $v['External'] = $v['External'] == 1 ? __("Yes") : __("No"); 
            }
            
            $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
            $v['Published'] = $v['Published'] == 1 ? __("Yes") : __("No");
            $v['StartDate'] = isset($v['StartDate']) ? formatDate($v['StartDate']) : "";
            $v['EndDate'] = isset($v['EndDate']) ? formatDate($v['EndDate']) : "";
            $v['MouSignatureDate'] = isset($v['MouSignatureDate']) ? formatDate($v['MouSignatureDate']) : "";
            $v['ValidatedOn'] = isset($v['ValidatedOn']) ? formatDateWithHour($v['ValidatedOn']) : "";
            $v['PublishedOn'] = isset($v['PublishedOn']) ? formatDateWithHour($v['PublishedOn']) : "";

            if (isset($v['Granted'])) {
                $v['Granted'] = $v['Granted'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['AnnualJustification'])) {
                $v['AnnualJustification'] = $v['AnnualJustification'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['Cofinancing'])) {
                $v['Cofinancing'] = $v['Cofinancing'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['Extended'])) {
                $v['Extended'] = $v['Extended'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['FEDER'])) {
                $v['FEDER'] = $v['FEDER'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['Competitive'])) {
                $v['Competitive'] = $v['Competitive'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['CompetitiveCall'])) {
                $v['CompetitiveCall'] = $v['CompetitiveCall'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['IGFAELeader'])) {
                $v['IGFAELeader'] = $v['IGFAELeader'] == 1 ? __("Yes") : __("No"); 
            }
                                                         
            $v['ConcessionDate'] = isset($v['ConcessionDate']) ? formatDate($v['ConcessionDate']) : "";
            $v['EndDateExtended'] = isset($v['EndDateExtended']) ? formatDate($v['EndDateExtended']) : "";
        }
    }); 

    //al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($experimets, $headers);

    //llamamos a la función común para exportar
    igfae_export($experimets, __('InternationalConsortium'));
}

function getSearchRequestModelExperimental()
{

    // Nombre
    $name                 = trim(filter_var($_POST['nombre'], FILTER_SANITIZE_STRING));

    //Research Area select multiple
    $researchArea         = [];
    if(isset($_POST['researchArea']) && !empty($_POST['researchArea'])){
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }
    //Research Program select multiple
    $researchProgram         = [];
    if(isset($_POST['researchProgram']) && !empty($_POST['researchProgram'])){
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }
    //Thesis director select multiple
    $thesisDirector         = [];
    if(isset($_POST['thesisDirector']) && !empty($_POST['thesisDirector'])){
        foreach ($_POST['thesisDirector'] as $item) {
            $thesisDirector[] = $item;
        }
    }
    //Start date
    $startDate                = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
    
    //End date
    $endDate                  = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));
    
    //Research Project
    $researchProject         = [];
    if(isset($_POST['researchProject']) && !empty($_POST['researchProject'])){
        foreach ($_POST['researchProject'] as $item) {
            $researchProject[] = $item;
        }
    }

    // Research Group
    $researchGroup         = [];
    if(isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])){
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }
    //Work Line select multiple
    $workLine         = [];
    if(isset($_POST['workline']) && !empty($_POST['workline'])){
        foreach ($_POST['workline'] as $item) {
            $workLine[] = $item;
        }
    }
    // Facility
    $facilities        = trim(filter_var($_POST['facilities'], FILTER_SANITIZE_STRING));
    
    //Consortium select
    $consortium         = NULL;
    if (isset($_POST['consortium'])) {
        $consortium = trim(filter_var($_POST['consortium'], FILTER_SANITIZE_STRING));
    }

    //Validated select
    $validated         = NULL;
    if (isset($_POST['validated'])) {
        $validated     = trim(filter_var($_POST['validated'], FILTER_SANITIZE_STRING));
    }

    //Published select
    $published         = NULL;
    if (isset($_POST['published'])) {
        $published     = trim(filter_var($_POST['published'], FILTER_SANITIZE_STRING));
    }

    if (isset($_POST['igfae-page'])) {
        $page = intval(filter_var($_POST['igfae-page'], FILTER_SANITIZE_NUMBER_INT));
    }

    $experimentalCollaboration = new IntranetExperimentalCollaboration( 
        NULL,
		$name,
		$researchArea,
		$researchProgram,
		$thesisDirector,
		$startDate,
        $endDate,
        $consortium,
		$researchProject,
		$researchGroup,
		$workLine,
		$facilities,
		$validated,
        $published,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL
    );

    return $experimentalCollaboration;
}

?>