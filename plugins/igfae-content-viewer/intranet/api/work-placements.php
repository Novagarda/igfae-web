<?php
add_action("wp_ajax_search_work_placement_submit", "api_search_work_placement_submit");
add_action("wp_ajax_nopriv_search_work_placement_submit", "api_search_work_placement_submit");
function api_search_work_placement_submit()
{    
    $draw = isset($_POST['draw'])? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)):0;
    $length = isset($_POST['length'])? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)): 10;
    $offset = isset($_POST['start'])? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)): 0;
    $order = isset($_POST['order'])? $_POST['order']: null;

    $workPlacements = getSearchRequestModelWorkPlacement();
    
    $info = getSelectWorkPlacements($workPlacements, $offset, $length, $order);

    $data = [
        "data" => $info['workPlacement'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);
    wp_send_json_success();
    exit();
}


function getSearchRequestModelWorkPlacement()
{

    //Displaced Researcher
    $displacedResearcher         = [];
    if(isset($_POST['displacedResearcher']) && !empty($_POST['displacedResearcher'])){
        foreach ($_POST['displacedResearcher'] as $item) {
            $displacedResearcher[] = $item;
        }
    }

    //Contract Link
    $contractLink = NULL;
    if(isset($_POST['contractLink']) && !empty($_POST['contractLink'])){
        $contractLink            = trim(filter_var($_POST['contractLink'], FILTER_SANITIZE_STRING));
    }
    //Institution
    $institution = NULL;
    if(isset($_POST['institution']) && !empty($_POST['institution'])){
        $institution             = trim(filter_var($_POST['institution'], FILTER_SANITIZE_STRING));
    }
    //External Tutor
    $externalTutor = NULL;
    if(isset($_POST['externalTutor']) && !empty($_POST['externalTutor'])){
        $externalTutor           = trim(filter_var($_POST['externalTutor'], FILTER_SANITIZE_STRING));
    }
    
    //Tutor
    $tutor         = [];
    if(isset($_POST['tutor']) && !empty($_POST['tutor'])){
        foreach ($_POST['tutor'] as $item) {
            $tutor[] = $item;
        }
    }
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
    //Start date
    $startDate                = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
    
    //End date
    $endDate                  = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));
    
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
    //Experimental collaboration
    $experiment         = [];
    if(isset($_POST['collaborationExperiment']) && !empty($_POST['collaborationExperiment'])){
        foreach ($_POST['collaborationExperiment'] as $item) {
            $experiment[] = $item;
        }
    }
    //Associated collaborations
    $associatedContract         = [];
    if(isset($_POST['associatedContract']) && !empty($_POST['associatedContract'])){
        foreach ($_POST['associatedContract'] as $item) {
            $associatedContract[] = $item;
        }
    }
    //Validated select
    $validated         = NULL;
    if (isset($_POST['validated'])) {
        $validated     = trim(filter_var($_POST['validated'], FILTER_SANITIZE_STRING));
    }

    if (isset($_POST['igfae-page'])) {
        $page = intval(filter_var($_POST['igfae-page'], FILTER_SANITIZE_NUMBER_INT));
    }

    $workPlacements = new IntranetWorkPlacements( 
        NULL,
		$displacedResearcher,
		$tutor,
		$associatedContract,
		$contractLink,
		$institution,
        $externalTutor,
        NULL,
        NULL,
        NULL,
		$researchArea,
		$researchProgram,
		$startDate,
		$endDate,
		$researchGroup,
        $workLine,
        $experiment,
        NULL,
        $validated
    );
    return $workPlacements;
}

/** EXPORT */
add_action( 'admin_action_export_work_placement', 'export_work_placement' );
function export_work_placement()
{
    //definir con internacionalizacion
    $headers = [__("ID"),__("Displaced researcher"),__("Tutor"), __("External tutor"), __("Start date"), __("End date"),
                __("Financiation"),__("Financed by"),__("Institution"),__("Cost"),
                __("Associated contract"),__("Contract link"),__("Research area"),__("Research program"),
                __("International consortium"),__("Research group"),__("Activity"),
                __("Comments"),__("Validated"),__("Validated by"),__("Validated on")];
    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $workPl = getSearchRequestModelWorkPlacement();

    //creamos un método propio para el export. Meter query correcta
    $workPlacements = getExportWorkPlacements($workPl);

    //funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($workPlacements, function (&$v, $k) {
        $v = get_object_vars($v);
        if (isset($v['AsociatedContract'])) {
            $v['AsociatedContract'] = $v['AsociatedContract'] == 1 ? __("Yes") : __("No");
        }
        
        $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
        $v['StartDate'] = isset($v['StartDate']) ? formatDate($v['StartDate']) : "";
        $v['EndDate'] = isset($v['EndDate']) ? formatDate($v['EndDate']) : "";
        $v['ValidatedOn'] = isset($v['ValidatedOn']) ? formatDateWithHour($v['ValidatedOn']) : "";

        //formateo numerico
        $v['Financiation'] = isset($v['Financiation']) ? formatNumber($v['Financiation']) : "";
        $v['Cost'] = isset($v['Cost']) ? formatNumber($v['Cost']) : "";

    }); 

    //al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($workPlacements, $headers);

    //llamamos a la función común para exportar
    igfae_export($workPlacements, __('Secondments'));
}

// Botón Delete experiment
add_action("wp_ajax_delete_work_placement_submit", "api_delete_work_placement_submit");
add_action("wp_ajax_nopriv_delete_work_placement_submit", "api_delete_work_placement_submit");
function api_delete_work_placement_submit()
{
    $id = $_POST['id'];
    // Eliminamos el experiment
    deleteWorkPlacements($id);
    wp_send_json_success();
    exit();
}

// Botón Edit de la pantalla del detalle de work placement
add_action("wp_ajax_edit_work_placement_submit", "api_edit_work_placement_submit");
add_action("wp_ajax_nopriv_edit_work_placement_submit", "api_edit_work_placement_submit");
function api_edit_work_placement_submit()
{
    wp_send_json_success();
    exit();
}

// New Experimental Collaboration
add_action("wp_ajax_new_work_placement_submit", "api_new_work_placement_submit");
add_action("wp_ajax_nopriv_new_work_placement_submit", "api_new_work_placement_submit");
function api_new_work_placement_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();
    
    // Id Experiment
    $workPlacementID   = trim(filter_var($_POST['idWorkPlacement'], FILTER_SANITIZE_STRING));

    //Start date
    $startDate      = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
    
    //End date
    $endDate        = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));

    //Institution
    $institution = NULL;
    if (isset($_POST['institution']) && !empty($_POST['institution'])) {
        $institution = trim(filter_var($_POST['institution'], FILTER_SANITIZE_STRING));
    }

    //Cost
    $cost = NULL;
    if (isset($_POST['cost']) && !empty($_POST['cost'])) {
        $cost = formatNumberToBD(trim(filter_var($_POST['cost'], FILTER_SANITIZE_STRING)));
    }

    //Financiation
    $financiation = NULL;
    if (isset($_POST['financiation']) && !empty($_POST['financiation'])) {
        $financiation = formatNumberToBD(trim(filter_var($_POST['financiation'], FILTER_SANITIZE_STRING)));
    }

    //Displaced Researcher
    $displacedResearcher     = NULL;
    if (isset($_POST['displacedResearcher']) && !empty($_POST['displacedResearcher'])) {
        $displacedResearcher    = trim(filter_var($_POST['displacedResearcher'], FILTER_SANITIZE_STRING));
    }
    
    //Tutor
    $tutor   = NULL;
    if (isset($_POST['tutor']) && !empty($_POST['tutor'])) {
        $tutor   = trim(filter_var($_POST['tutor'], FILTER_SANITIZE_STRING));
    }

    //External Tutor
    $externalTutor = NULL;
    if (isset($_POST['externalTutor']) && !empty($_POST['externalTutor'])) {
        $externalTutor = trim(filter_var($_POST['externalTutor'], FILTER_SANITIZE_STRING));
    }

    //Financiated By
    $financiatedBy = NULL;
    if (isset($_POST['financiatedBy']) && !empty($_POST['financiatedBy'])) {
        $financiatedBy = trim(filter_var($_POST['financiatedBy'], FILTER_SANITIZE_STRING));
    }

    //Contract Link
    $contractLink = NULL;
    if (isset($_POST['contractLink']) && !empty($_POST['contractLink'])) {
        $contractLink = trim(filter_var($_POST['contractLink'], FILTER_SANITIZE_STRING));
    }

    //Associated Contract 
    $associatedContract  = null;
    if (isset($_POST['associatedContract']) && !empty($_POST['associatedContract'])) {
        if ($_POST['associatedContract'] == __("Yes")) {
            $associatedContract  = 1;
        } else {
            $associatedContract  = 0;
        }
    }

    //Experimental collaboration
    $experimentalCollaboration   = NULL;
    if (isset($_POST['experimentalCollaboration']) && !empty($_POST['experimentalCollaboration'])) {
        $experimentalCollaboration   = trim(filter_var($_POST['experimentalCollaboration'], FILTER_SANITIZE_STRING));
    }

    //Research Group 
    $researchGroup     = NULL;
    if (isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])) {
        $researchGroup   = trim(filter_var($_POST['researchGroup'], FILTER_SANITIZE_STRING));
    }

    //Research Area select multiple
    $researchArea      = NULL;
    if(isset($_POST['researchArea']) && !empty($_POST['researchArea'])){
        $researchArea      = [];
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
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

    //Research Program select multiple
    $researchProgram    = NULL;
    if(isset($_POST['researchProgram']) && !empty($_POST['researchProgram'])){
        $researchProgram    = [];
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }
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

    $workPlacement = new IntranetWorkPlacements(
        $workPlacementID, 
		$displacedResearcher,
		$tutor,
		$associatedContract,
		$contractLink,
		$institution,
        $externalTutor,
        $financiation,
        $financiatedBy,
        $cost,
		$researchArea,
		$researchProgram,
		$startDate,
		$endDate,
		$researchGroup,
		$workLine,
        $experimentalCollaboration,
        $comments,
		$validated 
    );

    // $workPlacementID-> id de la estancia a editar, $idWorkPlacementSave-> id de la nueva estancia
    $idWorkPlacementSave = saveWorkPlacement($workPlacement);
    $return = array(
        'workPlacementID' => $workPlacementID,
        'idWorkPlacementSave'  => $idWorkPlacementSave
    );
    wp_send_json_success($return);
    exit();
}

?>