<?php
add_action("wp_ajax_search_transfer_submit", "api_search_transfer_submit");
add_action("wp_ajax_nopriv_search_transfer_submit", "api_search_transfer_submit");
function api_search_transfer_submit()
{    
    $draw = isset($_POST['draw'])? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)):0;
    $length = isset($_POST['length'])? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)): 10;
    $offset = isset($_POST['start'])? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)): 0;
    $order = isset($_POST['order'])? $_POST['order']: null;

    $transfers = getSearchRequestModelTransfer();
    
    $info = getSelectTransfer($transfers, $offset, $length, $order);

    $data = [
        "data" => $info['transfers'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);
    wp_send_json_success();
    exit();
}


function getSearchRequestModelTransfer()
{

    // Transfer Type
    $transferType  = [];
    if(isset($_POST['transferType']) && !empty($_POST['transferType'])){
        foreach ($_POST['transferType'] as $item) {
            $transferType[] = $item;
        }
    }

    // Transfer Subtype
    $transferSubtype = [];
    if(isset($_POST['transferSubtype']) && !empty($_POST['transferSubtype'])){
        foreach ($_POST['transferSubtype'] as $item) {
            $transferSubtype[] = $item;
        }
    }

    // Title
    $title  = trim(filter_var($_POST['title'], FILTER_SANITIZE_STRING));

    // Coordinator IGFAE
    $coordinatorIgfae         = [];
    if(isset($_POST['coordinatorIgfae']) && !empty($_POST['coordinatorIgfae'])){
        foreach ($_POST['coordinatorIgfae'] as $item) {
            $coordinatorIgfae[] = $item;
        }
    }
    
    // Transfer Personnel
    $transferPersonnel         = [];
    if(isset($_POST['transferPersonnel']) && !empty($_POST['transferPersonnel'])){
        foreach ($_POST['transferPersonnel'] as $item) {
            $transferPersonnel[] = $item;
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
    // National
    $national         = [];
    if(isset($_POST['national']) && !empty($_POST['national'])){
        foreach ($_POST['national'] as $item) {
            $national[] = $item;
        }
    }
    
    // TTA type
    $ttaType                  = trim(filter_var($_POST['ttaType'], FILTER_SANITIZE_STRING));
    
    // TTA start date
    $ttaStartDate             = trim(filter_var($_POST['ttaStartDate'], FILTER_SANITIZE_STRING));

    // TTA end date
    $ttaEndDate             = trim(filter_var($_POST['ttaEndDate'], FILTER_SANITIZE_STRING));

    // Research Group
    $researchGroup         = [];
    if(isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])){
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }
    // Work Line select
    $workLine         = [];
    if(isset($_POST['workline']) && !empty($_POST['workline'])){
        foreach ($_POST['workline'] as $item) {
            $workLine[] = $item;
        }
    }

    // TRL type
    $trlType         = [];
    if(isset($_POST['trlType']) && !empty($_POST['trlType'])){
        foreach ($_POST['trlType'] as $item) {
            $trlType[] = $item;
        }
    }

    // Keywords
    $keywords         = NULL;
    if (isset($_POST['keywords']) && !empty($_POST['keywords'])) {
        $keywords         = [];
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = $item;
        }
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

    $transfers = new IntranetTransfer(
        NULL,
        $title,
        NULL,
        NULL,
        NULL,
        $transferType,
        $transferSubtype,
        NULL, 
        NULL,
        $national,
        NULL,
        $ttaType,
        $ttaStartDate,
        NULL,
        NULL,
        NULL,
        $trlType,
        NULL,
        NULL,
        $ttaEndDate,
        $coordinatorIgfae,
        $transferPersonnel,
	    $researchArea,           
	    $researchProgram,              
        $researchGroup,  
        NULL,
        NULL,
        NULL,            
	    $workLine,                  
	    $keywords,               	
        NULL,
        $validated,
        $published,
        NULL,
        NULL,
        NULL,
        NULL
    );
    return $transfers;
}

/** EXPORT */
add_action( 'admin_action_export_transfer', 'export_transfer' );
function export_transfer()
{
    //definir con internacionalizacion
    $headers = [__("ID"),__("Type"),__("Subtype"), __("Title EN"), __("Title ES"), __("Title GL"),
                 __("IP coordinator IGFAE"), __("Researchers involved"), __("Reference"), __("TRL type"),
                __("Transfer partner"),__("Link"),__("National"), __("Description"),
                __("TTA type"),__("TTA start date"),__("TTA end date"),
                __("Report link"),__("TTA company target"),__("Keywords"),__("Research area"),
                __("Research program"), __("Research group"), __("International consortium"), __("Activity"),
                __("Production project"), __("Request link"), __("Annual income"), __("Annual cost"),
                __("Comments"), __("Validated"), __("Validated by"), __("Validated on"), __("Published"),
                __("Published by"), __("Published on")];
    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $workPl = getSearchRequestModelTransfer();

    //creamos un método propio para el export. Meter query correcta
    $transfers = getExportTransfer($workPl);

    //funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($transfers, function (&$v, $k) {
        $v = get_object_vars($v);  
        if (isset($v['National'])) {
            $v['National'] = $v['National'] == 1 ? __("Yes") : __("No"); 
        }
              
        $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
        $v['Published'] = $v['Published'] == 1 ? __("Yes") : __("No");
        $v['TTAStartDate'] = isset($v['TTAStartDate']) ? formatDate($v['TTAStartDate']) : "";
        $v['TTAEndDate'] = isset($v['TTAEndDate']) ? formatDate($v['TTAEndDate']) : "";
        $v['ValidatedOn'] = isset($v['ValidatedOn']) ? formatDateWithHour($v['ValidatedOn']) : "";
        $v['PublishedOn'] = isset($v['PublishedOn']) ? formatDateWithHour($v['PublishedOn']) : "";

        //Formateo numerico
        $v['AnnualIncome'] = isset($v['AnnualIncome']) ? formatNumber($v['AnnualIncome']) : "";
        $v['AnnualCost'] = isset($v['AnnualCost']) ? formatNumber($v['AnnualCost']) : "";

    }); 
    if (!isset($transfers)) {
        $transfers = [];
    }
    //al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($transfers, $headers);

    //llamamos a la función común para exportar
    igfae_export($transfers, __('TechnologyTransfers'));
}

// Botón Delete transfer
add_action("wp_ajax_delete_transfer_submit", "api_delete_transfer_submit");
add_action("wp_ajax_nopriv_delete_transfer_submit", "api_delete_transfer_submit");
function api_delete_transfer_submit()
{
    $id = $_POST['id'];
    // Eliminamos la transferencia
    deleteTransfer($id);
    wp_send_json_success();
    exit();
}

// New Transfer
add_action("wp_ajax_new_transfer_submit", "api_new_transfer_submit");
add_action("wp_ajax_nopriv_new_transfer_submit", "api_new_transfer_submit");
function api_new_transfer_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();
    
    // Id transfer
    $transferId     = trim(filter_var($_POST['idTransfer'], FILTER_SANITIZE_STRING));

    // Title
    $titleES        = trim(filter_var($_POST['title_es'], FILTER_SANITIZE_STRING));
    // Title
    $titleEN        = trim(filter_var($_POST['title_en'], FILTER_SANITIZE_STRING));
    // Title
    $titleGL        = trim(filter_var($_POST['title_gl'], FILTER_SANITIZE_STRING));

    //Start date
    $tTAStartDate   = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
    
    //End date
    $tTAEndDate     = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));

    //Reference
    $reference         = NULL;
    if (isset($_POST['reference']) && !empty($_POST['reference'])) {
        $reference     = trim(filter_var($_POST['reference'], FILTER_SANITIZE_STRING));
    }

    //Reference
    $tTAType         = NULL;
    if (isset($_POST['ttaType']) && !empty($_POST['ttaType'])) {
        $tTAType     = trim(filter_var($_POST['ttaType'], FILTER_SANITIZE_STRING));
    }
    
    //Company target
    $tTACompanyTarget         = NULL;
    if (isset($_POST['tTACompanyTarget']) && !empty($_POST['tTACompanyTarget'])) {
        $tTACompanyTarget   = trim(filter_var($_POST['tTACompanyTarget'], FILTER_SANITIZE_STRING));
    }

    //description
    $description         = NULL;
    if (isset($_POST['description']) && !empty($_POST['description'])) {
        $description    =  $_POST["description"];
    }

    //link
    $link         = NULL;
    if (isset($_POST['link']) && !empty($_POST['link'])) {
        $link      =  $_POST["link"];
    }

    //Report Link
    $reportLink         = NULL;
    if (isset($_POST['reportLink']) && !empty($_POST['reportLink'])) {
        $reportLink     =  $_POST["reportLink"];
    }

    //Annual Income
    $annualIncome         = NULL;
    if (isset($_POST['annualIncome']) && !empty($_POST['annualIncome'])) {
        $annualIncome    = formatNumberToBD(trim(filter_var($_POST['annualIncome'], FILTER_SANITIZE_STRING)));
    }
    //Annual Cost
    $annualCost         = NULL;
    if (isset($_POST['annualCost']) && !empty($_POST['annualCost'])) {
        $annualCost      = formatNumberToBD(trim(filter_var($_POST['annualCost'], FILTER_SANITIZE_STRING)));
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
    if (isset($_POST['coordinatorIgfae']) && !empty($_POST['coordinatorIgfae'])) {
        $coordinatorIgfae    = $_POST["coordinatorIgfae"];
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

    // TRL type
    $tRLTypeID    = NULL;
    if(isset($_POST['trlType']) && !empty($_POST['trlType'])){
         $tRLTypeID  = $_POST["trlType"];
    }
    
    //Partner select multiple
    $partners         = NULL;
    if(isset($_POST['partners']) && !empty($_POST['partners'])){
        $partners         = [];
        foreach ($_POST['partners'] as $item) {
            $partners[] = $item;
        }
    }

    // Comments textarea
    $comments         = NULL;
    if (isset($_POST['comments']) && !empty($_POST['comments']) && !empty(strip_tags($_POST['comments']))) {
        $textComments = str_replace('\"', '"', $_POST["comments"]); 
        $comments = $purifier->purify(trim($textComments));
    }

    //Keywords select multiple
    $keywords         = NULL;
    if (isset($_POST['keywords']) && !empty($_POST['keywords'])) {
        $keywords         = [];
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = $item;
        }
    }

    //Transfer Type select multiple
    $transferType         = NULL;
    if (isset($_POST['transferType']) && !empty($_POST['transferType'])) {
        $transferType      = $_POST["transferType"];
    }

    //Transfer Subtype select multiple
    $transferSubtype         = NULL;
    if (isset($_POST['transferSubtype']) && !empty($_POST['transferSubtype'])) {
        $transferSubtype      = $_POST["transferSubtype"];
    }

    //National
    $national  = NULL;
    if (isset($_POST['national']) && !empty($_POST['national'])) {
        if ($_POST['national'] == __("Yes")) {
            $national  = 1;
        } else {
            $national  = 0;
        }
    }
    //Keywords select multiple
    $transferPersonnel         = NULL;
    if (isset($_POST['reserchersInvolved']) && !empty($_POST['reserchersInvolved'])) {
        $transferPersonnel         = [];
        foreach ($_POST['reserchersInvolved'] as $item) {
            $transferPersonnel[] = $item;
        }
    }

    $transferPersonnel         = NULL;
    if (isset($_POST['reserchersInvolved']) && !empty($_POST['reserchersInvolved'])) {
        $transferPersonnel         = [];
        foreach ($_POST['reserchersInvolved'] as $item) {
            $transferPersonnel[] = $item;
        }
    }

    //Experimental collaboration
    $experimentalCollaboration   = NULL;
    if (isset($_POST['experimentalCollaboration']) && !empty($_POST['experimentalCollaboration'])) {
        $experimentalCollaboration   =  $_POST["experimentalCollaboration"];
    }

    //Request Link
    $requestLink         = NULL;
    if (isset($_POST['requestLink']) && !empty($_POST['requestLink'])) {
        $requestLink         = [];
        foreach ($_POST['requestLink'] as $item) {
            $requestLink[] = $item;
        }
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

    $transfer = new IntranetTransfer(
        $transferId,
        NULL,
        $titleES,
        $titleEN,
        $titleGL,
        $transferType,
        $transferSubtype,
        $reference,
        $link,
        $national,
        $description,
        $tTAType,
        $tTAStartDate,
        $tTACompanyTarget,
        $reportLink,
        $requestLink,
        $tRLTypeID,
        $annualIncome,
        $annualCost,
        $tTAEndDate,
        $coordinatorIgfae,
        $transferPersonnel,
        $researchArea,
        $researchProgram,
        $researchGroup,
        $asociatedPublications,
        $experimentalCollaboration,
        $partners,
        $workLine,
        $keywords,
        $comments,
        $validated,
        $published
    );

    if (!verificarValidarInformacion($validated, $published)) {
        // No se permite publicar sin haber validado previamente
        wp_send_json_error(new WP_Error('-1', __('In order to publish, the information must first be validated')));
    } else {
        // $idExperiment-> id del experimento a editar, $idExperimentSave-> id del nuevo experimento
        $idTransferSave = saveTransfer($transfer);
        $return = array(
            'transferId'          => $transferId,
            'idTransferSave'      => $idTransferSave
        );
        wp_send_json_success($return);
    }
    
    exit();
}



?>