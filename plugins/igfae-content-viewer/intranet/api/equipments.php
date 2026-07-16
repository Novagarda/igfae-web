<?php
/** Get search request*/

function getSearchEquipmentsRequestModel()
{
    //Equipments Name select
    $equipmentName = NULL;
    if(isset($_POST['equipmentName'])){
        $equipmentName = trim(filter_var($_POST['equipmentName'], FILTER_SANITIZE_STRING));
    } 

    //Equipments Family select
    $equipmentFamily = [];
    if (isset($_POST['equipmentFamily'])) {
        foreach ($_POST['equipmentFamily'] as $item) {
            $equipmentFamily[] = $item;
        }
    }  

    // Equipments Laboratory select
    $equipmentLaboratory = [];
        if (isset($_POST['equipmentLaboratory'])) {
        foreach ($_POST['equipmentLaboratory'] as $item) {
            $equipmentLaboratory[] = $item;
        }
    }  

    // Equipments Technical Contact select
    $equipmentTechnicalContact = [];
        if (isset($_POST['equipmentTechnicalContact'])) {
        foreach ($_POST['equipmentTechnicalContact'] as $item) {
            $equipmentTechnicalContact[] = $item;
        }
    }  

        // Equipments Responsable Family select
    $equipmentResponsableFamily = [];
        if (isset($_POST['equipmentResponsableFamily'])) {
        foreach ($_POST['equipmentResponsableFamily'] as $item) {
            $equipmentResponsableFamily[] = $item;
        }
    } 

    // Equipment Available select
    $equipmentAvailable = NULL;
    if (isset($_POST['equipmentAvailable'])) {
        $equipmentAvailable = trim(filter_var($_POST['equipmentAvailable'], FILTER_SANITIZE_STRING));
    }

    $equipment = new IntranetEquipment();
    $equipment->setEquipmentName($equipmentName);
    $equipment->setEquipmentFamily($equipmentFamily);
    $equipment->setEquipmentLaboratory($equipmentLaboratory);
    $equipment->setEquipmentTechnicalContact($equipmentTechnicalContact);
    $equipment->setEquipmentResponsableFamily($equipmentResponsableFamily);
    $equipment->setEquipmentAvailable($equipmentAvailable);

    return $equipment;
}

add_action("wp_ajax_search_equipments_submit", "api_search_equipments_submit");
add_action("wp_ajax_nopriv_search_equipments_submit", "api_search_equipments_submit");
function api_search_equipments_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $equipments = getSearchEquipmentsRequestModel();

    $info = fetchEquipments($equipments, $offset, $length, $order);

    $data = [
        "data" => $info['equipments'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

/** EXPORT */
add_action('admin_action_export_equipments', 'export_equipments');
function export_equipments()
{
    $headers = [
        __("ID"), __("Equipment Name"), __("Family"), __("Laboratory"), __("Technical Contact"),  __("Responsable Family"), __("Available")
    ];

    $equipments = []; 

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $requestEquipments = getSearchEquipmentsRequestModel();

    $idEquipments = fetchEquipments($requestEquipments, null, null, null, true);

    if(!empty($idEquipments)){
      $equipments = fetchEquipmentsExport($idEquipments);
    }

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($equipments, function (&$v, $k) {
        $v = get_object_vars($v);
        if (!empty($v)) {          
            if (isset($v['Available'])) {
                $v['Available'] = $v['Available'] == 1 ? __("Yes") : __("No"); 
            }
        }
    });
    if (!isset($equipments)) {
        $equipments = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($equipments, $headers);

    //llamamos a la función común para exportar
    igfae_export($equipments, __('Equipments'));
}

// Botón Delete Equipment
add_action("wp_ajax_delete_equipment_submit", "api_delete_equipment_submit");
add_action("wp_ajax_nopriv_delete_equipment_submit", "api_delete_equipment_submit");
function api_delete_equipment_submit() 
{
    $id = $_POST['idEquipment'];
    // COMPROBAMOS QUE EL EQUIPMENT NO TENGA NINGUNA RESERVA HECHA
    if (getForHireHistoryByEquipment($id) > 0) {
        wp_send_json_error(new WP_Error('-1', __('Equipment could not be removed because of hiring records associated')));
    } else {
        // Eliminamos equipment
        deleteEquipment($id);
        wp_send_json_success();
    }
    exit();
}

function existsEquipment($equipmentId)
{
    $where = "";
    $params = [];

    $where .= " AND e.EquipmentID = %d";
    $params[] = $equipmentId;

    $sql = "SELECT distinct
	e.EquipmentID as 'id'
    FROM Equipment e 
        WHERE 1=1 {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

add_action("wp_ajax_new_equipment_submit", "api_new_equipment_submit");
add_action("wp_ajax_nopriv_new_equipment_submit", "api_new_equipment_submit");
function api_new_equipment_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();

    global $current_user;

    // Id Event
    $idEquip = trim(filter_var($_POST['idEquipment'], FILTER_SANITIZE_STRING));

    // Name
    $equipmentName  = trim(filter_var($_POST['nameEquipment'], FILTER_SANITIZE_STRING));

    // SN
    $equipmentSN  = trim(filter_var($_POST['equipmentSN'], FILTER_SANITIZE_STRING));

    // Inventory Number
    $equipmentInventoryNumber  = trim(filter_var($_POST['equipmentInventoryNumber'], FILTER_SANITIZE_STRING));

    // Invoice Number
    $equipmentInvoiceNumber  = trim(filter_var($_POST['equipmentInvoiceNumber'], FILTER_SANITIZE_STRING));

    // Laboratory ID
    $equipmentLaboratory = NULL;
    if (isset($_POST['equipmentLaboratory']) && !empty($_POST['equipmentLaboratory'])) {
        $equipmentLaboratory = trim(filter_var($_POST['equipmentLaboratory'], FILTER_SANITIZE_STRING));
    }

    // Family ID
    $equipmentFamily = NULL;
    if (isset($_POST['equipmentFamily']) && !empty($_POST['equipmentFamily'])) {
        $equipmentFamily = trim(filter_var($_POST['equipmentFamily'], FILTER_SANITIZE_STRING));
    }

    // Responsable Family ID
    $equipmentResponsableFamily = NULL;
    if (isset($_POST['equipmentResponsableFamily']) && !empty($_POST['equipmentResponsableFamily'])) {
        $equipmentResponsableFamily = trim(filter_var($_POST['equipmentResponsableFamily'], FILTER_SANITIZE_STRING));
    }

    // Location
    $equipmentLocation  = trim(filter_var($_POST['equipmentLocation'], FILTER_SANITIZE_STRING));

     // Technical Specification

    $technicalSpecifications  = NULL;
    if (isset($_POST['technicalSpecifications']) && !empty($_POST['technicalSpecifications']) && !empty(strip_tags($_POST['technicalSpecifications']))) {
        $textTechnicalSpecifications = str_replace('\"', '"', $_POST["technicalSpecifications"]);
        $technicalSpecifications = $purifier->purify(trim($textTechnicalSpecifications)); 
    }

    // Technical Contact
    $equipmentTechnicalContact  = trim(filter_var($_POST['nameTechnicalContact'], FILTER_SANITIZE_STRING)) . '/' . trim(filter_var($_POST['emailTechnicalContact'], FILTER_SANITIZE_STRING)) . '/' . trim(filter_var($_POST['telephoneTechnicalContact'], FILTER_SANITIZE_STRING));

    // Funding Code
    $equipmentFundingCode  = trim(filter_var($_POST['equipmentFundingCode'], FILTER_SANITIZE_STRING));

    // Other Funding
    $equipmentOtherFunding  = trim(filter_var($_POST['equipmentOtherFunding'], FILTER_SANITIZE_STRING));

    // Acceptance Date
    $equipmentAcceptanceDate = NULL;
    if (isset($_POST['equipmentAcceptanceDate']) && !empty($_POST['equipmentAcceptanceDate'])) {
        $time_input = strtotime($_POST['equipmentAcceptanceDate']);
        $equipmentAcceptanceDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    // Comment
    $equipmentComment = NULL;
    if (isset($_POST['equipmentComment']) && !empty($_POST['equipmentComment']) && !empty(strip_tags($_POST['equipmentComment']))) {
        $textEquipmentComment = str_replace('\"', "'", $_POST["equipmentComment"]);
        $equipmentComment = $purifier->purify(trim($textEquipmentComment)); 
    }
        
    // Delete de la imagen
    $photoEquipmentDelete = false;
    if (isset($_POST['photoEquipmentDelete']) && !empty($_POST['photoEquipmentDelete']) && $_POST['photoEquipmentDelete'] == "true") {
        $photoEquipmentDelete = true;
    }

    // Delete acceptance
    $acceptanceFileDelete = false;
    if (isset($_POST['acceptanceFileDelete']) && !empty($_POST['acceptanceFileDelete']) && $_POST['acceptanceFileDelete'] == "true") {
        $acceptanceFileDelete = true;
    }

    // Amortizations
    $amortizations = NULL;
    if (isset($_POST['tAmortization']) && !empty($_POST['tAmortization'])) {
        $amortizations = json_decode(stripcslashes($_POST['tAmortization']), true);
    }

    // Maintenance History
    $maintenanceHistory = NULL;
    if (isset($_POST['tMaintenanceHistory']) && !empty($_POST['tMaintenanceHistory'])) {
        $maintenanceHistory = json_decode(stripcslashes($_POST['tMaintenanceHistory']), true);
    }

    // Documentations
    $documentations = NULL;
    if (isset($_POST['tDocumentation']) && !empty($_POST['tDocumentation'])) {
        $documentations = json_decode(stripcslashes($_POST['tDocumentation']), true);
    }
    
    $listDocumentations = array();
    if(isset($documentations)){
        foreach($documentations as $item){
            $nameFile = $item['documentTitle'] . '-' . $item['documentType'];
            $textFile = str_replace(" ", "_", $nameFile);
            if (isset($_FILES[$textFile]) && !empty($_FILES[$textFile])) {
                $listDocumentations[] = $nameFile;
            }
        }
    }

    // DocumentationsR
    $documentationsR = NULL;
    if (isset($_POST['tDocumentationR']) && !empty($_POST['tDocumentationR'])) {
        $documentationsR = json_decode(stripcslashes($_POST['tDocumentationR']), true);
    }

    $listDeleteDocumentations = array();
    if(isset($documentationsR)){
        foreach($documentationsR as $item){
            $nameFile = 'fileDelete' . '-' . $item['documentTitle'] . '-' . $item['documentType'];
            $textFile = str_replace(" ", "_", $nameFile);
            if (isset($_POST[$textFile]) && !empty($_POST[$textFile]) && $_POST[$textFile] == 'true') {
                $listDeleteDocumentations[] = $nameFile;
            }
        }
    }

    // DocumentationsC, only change name file
    $documentationsC = NULL;
    if (isset($_POST['tDocumentationC']) && !empty($_POST['tDocumentationC'])) {
        $documentationsC = json_decode(stripcslashes($_POST['tDocumentationC']), true);
    }    

    //Construimos el array con las columnas y valores para el insert    
    $data = array(
        'EquipmentName' => $equipmentName,
        'SN' => $equipmentSN,  
        'InventoryNumber'=> $equipmentInventoryNumber,
        'InvoiceNumber' => $equipmentInvoiceNumber,
        'LaboratoryID' => $equipmentLaboratory,
        'FamilyID' => $equipmentFamily,
        'Location' => $equipmentLocation,
        'TechnicalContactID' => $equipmentTechnicalContact,
        'FundingCode' => $equipmentFundingCode,
        'OtherFunding' => $equipmentOtherFunding,
        'AcceptanceDate' => $equipmentAcceptanceDate,
        'TechnicalSpecification' => $technicalSpecifications,
        'Comments' => $equipmentComment

    );

    $equipment = new IntranetEquipment();
    $equipment->setEquipmentName($equipmentName);
    $equipment->setDeletePhoto($photoEquipmentDelete);
    $equipment->setEquipmentSN($equipmentSN);
    $equipment->setEquipmentInventoryNumber($equipmentInventoryNumber);
    $equipment->setEquipmentInvoiceNumber($equipmentInvoiceNumber);    
    $equipment->setEquipmentLaboratory($equipmentLaboratory);
    $equipment->setEquipmentFamily($equipmentFamily);
    $equipment->setEquipmentResponsableFamily($equipmentResponsableFamily);
    $equipment->setEquipmentLocation($equipmentLocation);
    $equipment->setEquipmentTechnicalContact($equipmentTechnicalContact);
    $equipment->setEquipmentFundingCode($equipmentFundingCode);
    $equipment->setEquipmentOtherFunding($equipmentOtherFunding);
    $equipment->setEquipmentAcceptance($equipmentAcceptanceDate);
    $equipment->setDeleteAcceptance($acceptanceFileDelete);
    $equipment->setTechnicalSpecifications($technicalSpecifications);
    $equipment->setComments($equipmentComment);
    $equipment->setAmortizations($amortizations);
    $equipment->setMaintenanceHistory($maintenanceHistory);
    $equipment->setDocumentations($listDocumentations);
    $equipment->setListDeleteDocumentations($listDeleteDocumentations);
    $equipment->setListChangeNameDocumentations($documentationsC);
   
    // $idEquipment-> id del equipment a editar, $idEquipmentSave-> id del nuevo equipment
    $idEquipmentSave = saveEquipment($data, $equipment);
    $idEquipment = $idEquip;
    $return = array(
        'idEquipment' => $idEquipment,
        'idEquipmentSave' => $idEquipmentSave
    );
    wp_send_json_success($return);

    exit();
}

add_action("wp_ajax_booking_equipments_submit", "api_booking_equipments_submit");
add_action("wp_ajax_nopriv_booking_equipments_submit", "api_booking_equipments_submit");
function api_booking_equipments_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $info = getBookingEquipments($offset, $length, $order);

    $data = [
        "data" => $info['equipmentsBooking'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

/** EXPORT BOOKING */
add_action('admin_action_export_equipments_booking', 'export_equipments_booking');
function export_equipments_booking()
{
    $headers = [
        __("Equipment Name"), __("User"), __("Temporal Location"), __("Start Date"),  __("End Date")
    ];

    $equipmentsBooking = fetchEquipmentsBookingExport();

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($equipmentsBooking, function (&$v, $k) {
        $v = get_object_vars($v);
    });
    if (!isset($equipmentsBooking)) {
        $equipmentsBooking = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($equipmentsBooking, $headers);

    //llamamos a la función común para exportar
    igfae_export($equipmentsBooking, __('Equipments Booking'));
}

/** EXPORT DETAIL*/
add_action('admin_action_export_equipments_detail', 'export_equipments_detail');
function export_equipments_detail()
{
    //Detail Management
    $management = NULL;
    if(isset($_POST['management'])){
        $management = $_POST['management'];
    } 
    //Detail Technical
    $technical = NULL;
    if(isset($_POST['technical'])){
        $technical = $_POST['technical'];
    } 

    //Equipment ID
    $idEquipment = NULL;
    if(isset($_POST['idEquipment'])){
        $idEquipment = $_POST['idEquipment'];
    }

    if($management !== NULL){
        $headers = [
            __("Equipment Name"), __("Serial Number"), __("Number Inventory"), __("Number Invoice"),  __("Funding Code"), __("Other Funding"),  __("Acceptance Date"),  __("Location")
        ];

        $headersType = [
            __("Code"), __("Year"), __("Porcentage"),  __("Amount")
        ];

        $equipmentData = fetchDetailEquipmentExport($idEquipment);
        $equipmentAmortization = fetchEquipmentAmortization($idEquipment);        
        $titleType = "Management";
        $title = "Equipment Detail - " .  $titleType;

        exportDetail($equipmentData,  $headers, $equipmentAmortization, $headersType, $title, $titleType);
    }

    if($technical !== NULL){       

        $headers = [
            __("Equipment Name"), __("Serial Number"), __("Number Invoice"), __("Technical Specification"), __("Family Name"),  __("Family Responsable"), __("Laboratory Name"), __("Technical Contact"), __("Acceptance File")
        ];

        $headersType = [
            __("Event"), __("Date"), __("Place"),  __("Cost"),   __("TechnicalIntervention")
        ];

        $equipmentData = fetchDetailTechnicalEquipmentExport($idEquipment);
        $equipmentTechnical = fetchEquipmentTechnical($idEquipment);
        $titleType = "Technical";
        $title = "Equipment Detail - " .  $titleType;

        exportDetail($equipmentData,  $headers, $equipmentTechnical, $headersType, $title, $titleType);
    }
}

function exportDetail($equipmentData,  $headers, $equipmentType, $headersType, $title, $titleType){
    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($equipmentData, function (&$v, $k) {
        $v = get_object_vars($v);      
        if (!empty($v)) {          
            if (isset($v['AcceptanceName'])) {
                $v['AcceptanceName'] = explode('-',$v['AcceptanceName'])[2];
            }
        }  
    });
    if (!isset($equipmentData)) {
        $equipmentData = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($equipmentData, $headers);

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($equipmentType, function (&$v, $k) {
        $v = get_object_vars($v);
    });
    if (!isset($equipmentType)) {
        $equipmentType = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($equipmentType, $headersType);

    //llamamos a la función común para exportar
    igfae_export_mode2($equipmentData, $equipmentType, $title, $titleType);
}


/** HIRE HISTORY */
add_action("wp_ajax_hire_equipments_submit", "api_hire_equipments_submit");
add_action("wp_ajax_nopriv_hire_equipments_submit", "api_hire_equipments_submit");
function api_hire_equipments_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $info = getHireEquipments($_POST[IGFAE_INTRANET_PARAM_IDEQUIPMENT], $offset, $length, $order);

    $data = [
        "data" => $info['equipmentsBooking'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

add_action("wp_ajax_check_user_email", "check_user_email");
add_action("wp_ajax_nopriv_check_user_email", "check_user_email");
function check_user_email()
{
    $result = checkUserEmail($_POST['userEmail']);

    $return = array(
        'checkUserEmail' => $result['error']
    );

    wp_send_json_success($return);

    exit();
}

/** EXPORT */
add_action('admin_action_export_equipments_hire', 'export_equipments_hire');
function export_equipments_hire()
{
    $headers = [
        __("Equipment Name"), __("User"), __("Temporal Location"), __("Start Date"),  __("End Date")
    ];

    $equipmentsHire = fetchEquipmentsHireExport($_POST['idEquipment']);

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($equipmentsHire, function (&$v, $k) {
        $v = get_object_vars($v);
    });
    if (!isset($equipmentsHire)) {
        $equipmentsHire = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($equipmentsHire, $headers);

    //llamamos a la función común para exportar
    igfae_export($equipmentsHire, __('Equipments History'));
}

add_action("wp_ajax_hire_equipments_submit_save", "hire_equipments_submit_save");
add_action("wp_ajax_nopriv_hire_equipments_submit_save", "hire_equipments_submit_save");
function hire_equipments_submit_save()
{
    // Id Event
    $idEquip = trim(filter_var($_POST['idEquipment'], FILTER_SANITIZE_STRING));

    // List Hire History
    $listHire = NULL;
    if (isset($_POST['tHire']) && !empty($_POST['tHire'])) {
        $listHire = json_decode(stripcslashes($_POST['tHire']), true);
    }

    // List Remove Hire History
    $listHireRemove = NULL;
    if (isset($_POST['tHireRemove']) && !empty($_POST['tHireRemove'])) {
        $listHireRemove = json_decode(stripcslashes($_POST['tHireRemove']), true);
    }

    $result = saveHireHistory($idEquip, $listHire, $listHireRemove);

    if($result){
        sendEmails($idEquip, $listHire, $listHireRemove);
    }

    $return = array(
        'idEquipment' => $idEquip
    );

    wp_send_json_success($return);

    exit();
}

function sendEmails($idEquip, $listHire, $listHireRemove){
    $equipment = getEquipmentById($idEquip);
    $subjectCreate = "Equipment reservation has been created: " . $equipment->getEquipmentName();
    $subjectUpdate = "Equipment reservation has been updated: " . $equipment->getEquipmentName();
    $subjectDelete = "Equipment reservation has been deleted: " . $equipment->getEquipmentName();
    $user_id = get_current_user_id();

    foreach ($listHire as $elem) { 
        if($elem['_equipmentReservationID'] == 0){
            sendEmail($elem, $user_id, 'created', $subjectCreate);
        } else if(isset($elem['_hasEdit']) && $elem['_hasEdit']){  
            sendEmail($elem, $user_id, 'updated', $subjectUpdate);
        } 
    }
    
    foreach ($listHireRemove as $elem) {               
        sendEmail($elem, $user_id, 'deleted', $subjectDelete);
    }
}

function sendEmail($elem, $user_id, $type, $subject){
    $temporalLocation = $elem['_temporal_location'];
    $startDate = $elem['_start_date'];
    $endDate = $elem['_end_date'];

    $personnel = getPersonnelByEmail($elem['_personnel'])[0];
    $igfaeUserID = getUserByWPUserID($user_id)[0]["id"];
    $user = getUserById($igfaeUserID);
    $email = $personnel->Name . ' ' . $personnel->FirstSurname . ' ' . $personnel->SecondSurname . '(' . $personnel->Email . ')';
    $bookedBy = $user->getName() . ' ' . $user->getFirstSurname() . ' ' . $user->getSecondSurname() . '(' . $user->getEmail() . ')';
    
    $body = "
        <p>The following reservation has been <strong>$type</strong>:</p>
        <ul>
            <li>Email: $email </li>
            <li>Temporal Location: $temporalLocation </li>
            <li>Start Date: $startDate </li>
            <li>End Date: $endDate </li>
            <li>Booked by: $bookedBy </li>
        <ul>            
    ";

    wp_mail(EMAIL_RECIPIENT, $subject , $body);
}

// Change value new Activity type
add_action("wp_ajax_getResponsableFamily", "api_getResponsableFamily");
add_action("wp_ajax_nopriv_getResponsableFamily", "api_getResponsableFamily");
function api_getResponsableFamily()
{

    //building select multiple
    $family = $_POST['family'];
    $responsables = getSelectEquipmentResponsableFamilyByFamily($family);
    
    $data = [
        "data" => $responsables
    ];

    wp_send_json($responsables);
    exit();
}
