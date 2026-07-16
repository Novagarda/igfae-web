<?php

/**
 * Get categories by type
 */
function getCategoriesByType($type)
{

    $categories = getSelectCategoriesByType($type);
    $categoriesSelect = NULL;
    foreach ($categories as $key => $item) {
        $categoriesSelect[$key] = $item;
    }
    $categoriesSelect['items'] = $categoriesSelect;

    return $categoriesSelect;
}
/**
 * Get categories by type
 */
function getTypesCategories()
{

    $categories = getSelectCategoryTypes();
    $categoriesSelect = NULL;
    foreach ($categories as $key => $item) {
        $categoriesSelect[$key] = $item;
    }
    $categoriesSelect['items'] = $categoriesSelect;

    return $categoriesSelect;
}
/**
 * Get team reference
 */
function getTeamReferences()
{
    $teamReferences = getSelectTeamReferences();
    $teamReferencesSelect = NULL;
    foreach ($teamReferences as $key => $item) {
        $teamReferencesSelect[$key] = $item;
    }
    $teamReferencesSelect['items'] = $teamReferencesSelect;

    return $teamReferencesSelect;
}

/**
 * Get departments
 */
function getDepartments()
{
    $departments = getSelectDepartments();
    $departmentsSelect = NULL;
    foreach ($departments as $key => $item) {
        $departmentsSelect[$key] = $item;
    }
    $departmentsSelect['items'] = $departmentsSelect;

    return $departmentsSelect;
}


/**
 * Get contracts
 */
function getContracts()
{
    $contracts = getSelectContracts();
    $contractsSelect = NULL;
    foreach ($contracts as $key => $item) {
        $contractsSelect[$key] = $item;
    }
    $contractsSelect['items'] = $contractsSelect;

    return $contractsSelect;
}

/**
 * Get buildings
 */
function getBuildings()
{
    $buildings = getSelectBuildings();
    $buildingsSelect = NULL;
    foreach ($buildings as $key => $item) {
        $buildingsSelect[$key] = $item;
    }
    $buildingsSelect['items'] = $buildingsSelect;

    return $buildingsSelect;
}
// Change value new Activity type
add_action("wp_ajax_load_locations", "api_getLocations");
add_action("wp_ajax_nopriv_load_locations", "api_getLocations");
function api_getLocations()
{

    //building select multiple
    $building = $_POST['building'];
    $locations = getSelectLocations($building);

    wp_send_json_success($locations);
    exit();
}
/**
 * Get social networks
 */
function getSocialNetworks()
{
    $socialNetworks = getSelectSocialNetworks();
    $socialNetworksSelect = NULL;
    foreach ($socialNetworks as $key => $item) {
        $socialNetworksSelect[$key] = $item;
    }
    $socialNetworksSelect['items'] = $socialNetworksSelect;

    return $socialNetworksSelect;
}
/**
 * Get scientific firms
 */
function getScientificFirms()
{
    $scientificFirms = getSelectScientificFirms();
    $scientificFirmsSelect = NULL;
    foreach ($scientificFirms as $key => $item) {
        $scientificFirmsSelect[$key] = $item;
    }
    $scientificFirmsSelect['items'] = $scientificFirmsSelect;

    return $scientificFirmsSelect;
}

/**
 * Get countries
 */
function getCountries()
{
    $countries = getSelectCountries();
    $countriesSelect = NULL;
    foreach ($countries as $key => $item) {
        $countriesSelect[$key] = $item;
    }
    $countriesSelect['items'] = $countriesSelect;

    return $countriesSelect;
}

/**
 * Get map locations
 */
function getMapLocations()
{
    $locations = getSelectMapLocations();
    $locationsSelect = NULL;
    foreach ($locations as $key => $item) {
        $locationsSelect[$key] = $item;
    }
    $locationsSelect['items'] = $locationsSelect;

    return $locationsSelect;
}
// Change value new Activity type
add_action("wp_ajax_load_categories", "api_getCategories");
add_action("wp_ajax_nopriv_load_categories", "api_getCategories");
function api_getCategories()
{
    $categoryType = $_POST['categoryType'];
    $categoriesSelect = getCategoriesByType($categoryType);

    wp_send_json_success($categoriesSelect);
    exit();
}
add_action("wp_ajax_search_users_submit", "api_search_users_submit");
add_action("wp_ajax_nopriv_search_users_submit", "api_search_users_submit");
function api_search_users_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $user = getSearchUserRequestModel();

    $resultSearch = fetchUsers($user, $offset, $length, $order);

    $data = [
        "data" => $resultSearch['users'],
        "draw" => $draw,
        "recordsTotal"  =>  $resultSearch['pagination']['recordsTotal'],
        "recordsFiltered"  => $resultSearch['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}
/** Get search request*/

function getSearchUserRequestModel()
{
    // Name
    $userName  = trim(filter_var($_POST['userName'], FILTER_SANITIZE_STRING));
    // First surname
    $userFirstSurname  = trim(filter_var($_POST['userFirstSurname'], FILTER_SANITIZE_STRING));
    // Second SurName
    $userSecondSurname  = trim(filter_var($_POST['userSecondSurname'], FILTER_SANITIZE_STRING));

    //Gender
    $gender         = NULL;
    if (isset($_POST['gender'])) {
        $gender     = trim(filter_var($_POST['gender'], FILTER_SANITIZE_STRING));
    }

    //Research Area select multiple
    $researchArea         = [];
    if (isset($_POST['researchArea'])) {
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }

    //Research Program select multiple
    $researchProgram         = [];
    if (isset($_POST['researchProgram'])) {
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }

    //Work line select multiple
    $workLine         = [];
    if (isset($_POST['workLine'])) {
        foreach ($_POST['workLine'] as $item) {
            $workLine[] = $item;
        }
    }
    //Collaboration experiment select multiple
    $collaborationExperiment         = [];
    if (isset($_POST['collaborationExperiment'])) {
        foreach ($_POST['collaborationExperiment'] as $item) {
            $collaborationExperiment[] = $item;
        }
    }

    //Research Group select multiple
    $researchGroup         = [];
    if (isset($_POST['researchGroup'])) {
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }
    //Xunta Category select multiple
    $xuntaCategory         = [];
    if (isset($_POST['xuntaCategory'])) {
        foreach ($_POST['xuntaCategory'] as $item) {
            $xuntaCategory[] = $item;
        }
    }
    //Igfae category select multiple
    $igfaeCategory         = [];
    if (isset($_POST['igfaeCategory'])) {
        foreach ($_POST['igfaeCategory'] as $item) {
            $igfaeCategory[] = $item;
        }
    }
    //UE category select multiple
    $ueCategory         = [];
    if (isset($_POST['ueCategory'])) {
        foreach ($_POST['ueCategory'] as $item) {
            $ueCategory[] = $item;
        }
    }
    //IGFAE Department select multiple
    $igfaeDepartment         = [];
    if (isset($_POST['igfaeDepartment'])) {
        foreach ($_POST['igfaeDepartment'] as $item) {
            $igfaeDepartment[] = $item;
        }
    }
    //Team reference select multiple
    $teamReference         = [];
    if (isset($_POST['teamReference'])) {
        foreach ($_POST['teamReference'] as $item) {
            $teamReference[] = $item;
        }
    }
    //UE category select multiple
    $contractType         = [];
    if (isset($_POST['contractType'])) {
        foreach ($_POST['contractType'] as $item) {
            $contractType[] = $item;
        }
    }
    //User reference
    $userReference         = [];
    if (isset($_POST['userReference'])) {
        foreach ($_POST['userReference'] as $item) {
            $userReference[] = $item;
        }
    }
    //Roles select multiple
    $roles         = [];
    if (isset($_POST['roles'])) {
        foreach ($_POST['roles'] as $item) {
            $roles[] = $item;
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
    //foreign radio
    $foreign         = NULL;
    if (isset($_POST['foreign']) && !empty($_POST['foreign'])) {
        if ($_POST['foreign'] == "1") {
            $foreign = 1;
        } else {
            $foreign = 0;
        }
    }
    // start date
    $startDate = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));

    // end date
    $endDate = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));

    // start date
    $currentContratStartDate = trim(filter_var($_POST['currentContratStartDate'], FILTER_SANITIZE_STRING));

    // end date
    $currentContratEndDate = trim(filter_var($_POST['currentContratEndDate'], FILTER_SANITIZE_STRING));
    
    $user = new IntranetUser();
    $user->setName($userName);
    $user->setFirstSurname($userFirstSurname);
    $user->setSecondSurname($userSecondSurname);
    $user->setGender($gender);
    $user->setResearchArea($researchArea);
    $user->setResearchProgram($researchProgram);
    $user->setWorkLine($workLine);
    $user->setResearchGroup($researchGroup);
    $user->setCollaborationExperiment($collaborationExperiment);
    $user->setXuntaCategory($xuntaCategory);
    $user->setIgfaeCategory($igfaeCategory);
    $user->setUeCategory($ueCategory);
    $user->setIgfaeDepartment($igfaeDepartment);
    $user->setTeamReference($teamReference);
    $user->setContractType($contractType);
    $user->setValidated($validated);
    $user->setPublished($published);
    $user->setUserReference($userReference);
    $user->setForeign($foreign);
    $user->setRol($roles);
    $user->setStartDate($startDate);
    $user->setEndDate($endDate);   
    $user->setCurrentContratStartDate($currentContratStartDate);
    $user->setCurrentContratEndDate($currentContratEndDate);
    return $user;
}

/** Get Economic Activity Code */
/**
 * Get departments
 */
function getEconomicActivityCode()
{
    $listEconomicActivityCode = getSelectEconomicActivityCode();
    $economicActivityCodeSelect = NULL;
    foreach ($listEconomicActivityCode as $key => $item) {
        $economicActivityCodeSelect[$key] = $item;
    }
    $economicActivityCodeSelect['items'] = $economicActivityCodeSelect;

    return $economicActivityCodeSelect;
}

/** EXPORT */
add_action('admin_action_export_users', 'export_users');
function export_users()
{
    $headers = [
        __("ID"), __("Name"), __("BirthDate"), __("IDCard"), __("Phone"), __("Mobile"), __("Building"), __("Room"), __("Email"), __("Arxiv"), __("Orcid"),
        __("Wosid"), __("Scopusid"), __("ScientificFirms"), __("Website"), __("Gender"), __("Country name"),  __("UE"), __("IGFAE ID"),
        __("Department"), __("Location"), __("Foreign"), __("Biography"), __("Research group reference"), __("User reference"), __("Research areas"), __("Research programs"), __("Activities"), __("International consortium"),
        __("Comment"), __("Research groups"), __("Contracts"),  __("Validated"), __("Validated by"), __("Validated on"), __("Published"), __("Published by"), __("Published on"), __("Social Networks"),
        __("Projects"), __("Production Projects"), __("Events & Outreach"), __("My secondments"), __("Tutor secondments"), __("Transfers"), __("Categories"), __("Roles")
    ];

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $user = getSearchUserRequestModel();

    $users = fetchUserExport($user);

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($users, function (&$v, $k) {
        $v = get_object_vars($v);
        if (!empty($v)) {

            if (isset($v['Foreign'])) {
                $v['Foreign'] = $v['Foreign'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['UE'])) {
                $v['UE'] = $v['UE'] == 1 ? __("Yes") : __("No"); 
            }
           
            $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
            $v['Published'] = $v['Published'] == 1 ? __("Yes") : __("No");
            $v['Gender'] = $v['Gender'] == 1 ? __("Woman") : __("Man");
            $v['BirthDate'] = isset($v['BirthDate']) ? formatDate($v['BirthDate']) : "";
            $v['ValidatedOn'] = isset($v['ValidatedOn']) ? formatDateWithHour($v['ValidatedOn']) : "";
            $v['PublishedOn'] = isset($v['PublishedOn']) ? formatDateWithHour($v['PublishedOn']) : "";
        }
    });

    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($users, $headers);

    //llamamos a la función común para exportar
    igfae_export($users, __('Users'));
}

// Botón Delete user
add_action("wp_ajax_delete_user_submit", "api_delete_user_submit");
add_action("wp_ajax_nopriv_delete_user_submit", "api_delete_user_submit");
function api_delete_user_submit()
{
    $id = $_POST['idPersonnel'];
    // COMPROBAMOS QUE EL USUARIO NO TENGA RELACIONES CON OTRAS ENTIDADES PRINCIPALES
    $relationPersonnel = getRelationsPersonnel($id);

    if ($relationPersonnel['count'] > 0) {
        wp_send_json_error(new WP_Error('-1', __($relationPersonnel['message'])));
    } else {
        // Eliminamos el usuario
        deleteUser($id);
        wp_send_json_success();
    }
    exit();
}
add_action("wp_ajax_new_user_submit", "api_new_user_submit");
add_action("wp_ajax_nopriv_new_user_submit", "api_new_user_submit");
function api_new_user_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();

    global $current_user;
    // Id User
    $idUser = trim(filter_var($_POST['idUser'], FILTER_SANITIZE_STRING));
    // WPUserId
    $wPUserId = trim(filter_var($_POST['wpUserId'], FILTER_SANITIZE_STRING));

    // Name
    $userName  = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));

    // First surname
    $userFirstSurname  = trim(filter_var($_POST['firstSurname'], FILTER_SANITIZE_STRING));

    // Second SurName
    $userSecondSurname  = NULL;
    if (isset($_POST['secondSurname']) && !empty($_POST['secondSurname'])) {
        $userSecondSurname  = trim(filter_var($_POST['secondSurname'], FILTER_SANITIZE_STRING));
    }
    // Birthdate input
    $birthDateInput    = NULL;
    if (isset($_POST['birthDate']) && !empty($_POST['birthDate'])) {
        $time_input = strtotime($_POST['birthDate']);
        $birthDateInput = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }
    // NIF
    $idCard  = trim(filter_var($_POST['idCard'], FILTER_SANITIZE_STRING));

    // Phone
    $phoneInput    = NULL;
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        $phoneInput    = trim(filter_var($_POST['phone'], FILTER_SANITIZE_STRING));
    }
    // mobile
    $mobileInput    = NULL;
    if (isset($_POST['mobile']) && !empty($_POST['mobile'])) {
        $mobileInput    = trim(filter_var($_POST['mobile'], FILTER_SANITIZE_STRING));
    }
    //building select
    $building         = NULL;
    if (isset($_POST['building']) && !empty($_POST['building'])) {
        $building     = trim(filter_var($_POST['building'], FILTER_SANITIZE_STRING));
    }

    // room select
    $room                 = NULL;
    if (isset($_POST['room']) && !empty($_POST['room'])) {
        $room                 = trim(filter_var($_POST['room'], FILTER_SANITIZE_STRING));
    }

    // email input
    $email    = NULL;
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email    = trim(filter_var($_POST['email'], FILTER_SANITIZE_STRING));
    }
    //Research groups
    $researchGroups = NULL;
    if (isset($_POST['tableResearchGroup']) && !empty($_POST['tableResearchGroup'])) {
        $researchGroups = json_decode(stripcslashes($_POST['tableResearchGroup']), true);
    }
    // orcid multiple add
    $orcid    = NULL;
    if (isset($_POST['orcid']) && !empty($_POST['orcid'])) {
        $orcid         = [];
        foreach ($_POST['orcid'] as $item) {
            $orcid[] = $item;
        }
    }

    // researchId multiple add
    $researchId    = NULL;
    if (isset($_POST['researchId']) && !empty($_POST['researchId'])) {
        $researchId         = [];
        foreach ($_POST['researchId'] as $item) {
            $researchId[] = $item;
        }
    }

    // scopus multiple add
    $scopus    = NULL;
    if (isset($_POST['scopus']) && !empty($_POST['scopus'])) {
        $scopus         = [];
        foreach ($_POST['scopus'] as $item) {
            $scopus[] = $item;
        }
    }

    // inspireRef multiple add
    $inspireRef    = NULL;
    if (isset($_POST['inspireRef']) && !empty($_POST['inspireRef'])) {
        $inspireRef         = [];
        foreach ($_POST['inspireRef'] as $item) {
            $inspireRef[] = $item;
        }
    }

    //Social networks
    $socialNetworks = NULL;
    if (isset($_POST['tableSocialNetwork']) && !empty($_POST['tableSocialNetwork'])) {
        $socialNetworks = json_decode(stripcslashes($_POST['tableSocialNetwork']), true);
    }

    //Scientific firms select multiple
    $scientificFirm    = NULL;
    if (isset($_POST['scientificFirm']) && !empty($_POST['scientificFirm'])) {
        $scientificFirm         = [];
        foreach ($_POST['scientificFirm'] as $item) {
            $scientificFirm[] = $item;
        }
    }

    //Research areas
    $researchAreas = NULL;
    if (isset($_POST['tableResearchArea']) && !empty($_POST['tableResearchArea'])) {
        $researchAreas = json_decode(stripcslashes($_POST['tableResearchArea']), true);
    }
    //Research programs
    $researchPrograms = NULL;
    if (isset($_POST['tableResearchProgram']) && !empty($_POST['tableResearchProgram'])) {
        $researchPrograms = json_decode(stripcslashes($_POST['tableResearchProgram']), true);
    }
    //Worklines
    $workLines = NULL;
    if (isset($_POST['tableWorkline']) && !empty($_POST['tableWorkline'])) {
        $workLines = json_decode(stripcslashes($_POST['tableWorkline']), true);
    }
    //Categories
    $categories = NULL;
    if (isset($_POST['tableCategory']) && !empty($_POST['tableCategory'])) {
        $categories = json_decode(stripcslashes($_POST['tableCategory']), true);
    }
    // webSite input
    $webSite         = NULL;
    if (isset($_POST['webSite']) && !empty($_POST['webSite'])) {
        $webSite      = trim(filter_var($_POST['webSite'], FILTER_SANITIZE_STRING));
    }

    // gender select    
    $gender      = trim(filter_var($_POST['gender'], FILTER_SANITIZE_STRING));


    // country select
    $country         = NULL;
    if (isset($_POST['country']) && !empty($_POST['country'])) {
        $country      = trim(filter_var($_POST['country'], FILTER_SANITIZE_STRING));
    }
    //ue select
    $ue         = null;
    if (isset($_POST['ue']) && !empty($_POST['ue'])) {
        if ($_POST['ue'] == "1") {
            $ue = 1;
        } else {
            $ue = 0;
        }
    }

    // igfaeId input
    $igfaeId         = NULL;
    if (isset($_POST['igfaeId']) && !empty($_POST['igfaeId'])) {
        $igfaeId      = trim(filter_var($_POST['igfaeId'], FILTER_SANITIZE_STRING));
    }



    // igfaeDepartment select
    $igfaeDepartment    = NULL;
    if (isset($_POST['igfaeDepartment']) && !empty($_POST['igfaeDepartment'])) {
        $igfaeDepartment   = [];
        foreach ($_POST['igfaeDepartment'] as $item) {
            $igfaeDepartment[] = trim(filter_var($item, FILTER_SANITIZE_STRING));
        }
    }


    //Contracts
    $contracts = NULL;
    if (isset($_POST['tableContract']) && !empty($_POST['tableContract'])) {
        $contracts = json_decode(stripcslashes($_POST['tableContract']), true);
    }
    //Roles
    $roles = NULL;
    if (isset($_POST['roles']) && !empty($_POST['roles'])) {
        $roles        = [];
        foreach ($_POST['roles'] as $item) {
            $roles[] = $item;
        }
    }
    // collaboration/experiment select
    $collaborationExperiment         = NULL;
    if (isset($_POST['tableExperiment']) && !empty($_POST['tableExperiment'])) {
        $collaborationExperiment = json_decode(stripcslashes($_POST['tableExperiment']), true);
    }
    // location select
    $location         = NULL;
    if (isset($_POST['location']) && !empty($_POST['location'])) {
        $location      = trim(filter_var($_POST['location'], FILTER_SANITIZE_STRING));
    }

    // Biography input
    $biography         = NULL;
    if (isset($_POST['biography']) && !empty($_POST['biography']) && !empty(strip_tags($_POST['biography']))) {
        $textBiography = str_replace('\"', '"', $_POST["biography"]); 
        $biography = $purifier->purify($textBiography); 
    }

    //teamReference select multiple
    $teamReference    = NULL;
    if (isset($_POST['teamReference']) && !empty($_POST['teamReference'])) {
        $teamReference         = [];
        foreach ($_POST['teamReference'] as $item) {
            $teamReference[] = $item;
        }
    }

    //userReference select multiple
    $inspireRef    = NULL;
    if (isset($_POST['inspireRef']) && !empty($_POST['inspireRef'])) {
        $inspireRef         = [];
        foreach ($_POST['inspireRef'] as $item) {
            $inspireRef[] = $item;
        }
    }

    $userReference    = NULL;
    if (isset($_POST['userReference']) && !empty($_POST['userReference'])) {
        $userReference   = [];
        foreach ($_POST['userReference'] as $item) {
            $userReference[] = trim(filter_var($item, FILTER_SANITIZE_STRING));
        }
    }

    // Comments input
    $comments         = NULL;
    if (isset($_POST['comments']) && !empty($_POST['comments']) && !empty(strip_tags($_POST['comments']))) {
        $textComments = str_replace('\"', '"', $_POST["comments"]); 
        $comments = $purifier->purify(trim($textComments)); 
    }

    //Validated select
    $validated         = NULL;
    $validatedOn       = NULL;
    $validatedBy       = NULL;
    if (isset($_POST['validated']) && !empty($_POST['validated'])) {
        $validated     = 1;
        $validatedBy = $current_user->user_login;
        $validatedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede validar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de validar
        $validated = 0;
    }
    //Published select
    $published         = NULL;
    $publishedOn       = NULL;
    $publishedBy       = NULL;
    if (isset($_POST['published']) && !empty($_POST['published'])) {
        $published     = 1;
        $publishedBy = $current_user->user_login;
        $publishedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    } else {
        // IMPORTANTE: Valor por defecto a 0
        //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede publicar), lo que implica:
        //    - Informacion nueva y existente: Se queda pendiente de publicar
        $published = 0;
    }
    //foreign radio
    $foreign         = null;
    if (isset($_POST['foreign']) && !empty($_POST['foreign'])) {
        if ($_POST['foreign'] == "1") {
            $foreign = 1;
        } else {
            $foreign = 0;
        }
    }

    // Delete de la imagen
    $deleteImage         = false;
    if (isset($_POST['photoUserDelete']) && !empty($_POST['photoUserDelete']) && $_POST['photoUserDelete'] == "true") {
        $deleteImage = true;
    }

    //Construimos el array con las columnas y valores para el insert    
    $data = array(
        'Name' => $userName,
        'FirstSurname' => $userFirstSurname,
        'SecondSurname' => $userSecondSurname,
        'Email' => $email,
        'Gender' => $gender,
        'BirthDate' => $birthDateInput,
        'IDCard' => $idCard,
        'Phone' => $phoneInput,
        'Mobile' => $mobileInput,
        'Website' => $webSite,
        'IGFAAEID' => $igfaeId,
        'LocationID' => $room,
        'CountryCode' => $country,
        'UE' => $ue,
        'MapLocationID' => $location,
        'Biography' => $biography,
        'Comment' => $comments,
        'Validated' => $validated,
        'Published' => $published,
        'ValidatedBy' => $validatedBy,
        'PublishedBy' => $publishedBy,
        'ValidatedOn' => $validatedOn,
        'PublishedOn' => $publishedOn,
        'Foreign' => $foreign
    );

    file_put_contents(ABSPATH."/usercreation.log","newuser: 'user' variable contents: ".print_r($data,true)."\n",FILE_APPEND);
    $user = new IntranetUser();
    file_put_contents(ABSPATH."/usercreation.log","newuser: 'user' variable contents (after initialization): ".print_r($user,true)."\n",FILE_APPEND);
    $user->setPersonnelId($idUser);
    file_put_contents(ABSPATH."/usercreation.log","newuser: 'user' variable contents (PersonnelId is set): ".print_r($user,true)."\n",FILE_APPEND);
    $user->setWpUserId($wPUserId);
    file_put_contents(ABSPATH."/usercreation.log","newuser: 'user' variable contents (WpUserId is set): ".print_r($user,true)."\n",FILE_APPEND);
    $user->setName($userName);
    $user->setIdCard($idCard);
    $user->setResearchGroup($researchGroups);
    $user->setOrcid($orcid);
    $user->setResearchId($researchId);
    $user->setScopus($scopus);
    $user->setInspireRef($inspireRef);
    $user->setSocialNetwork($socialNetworks);
    $user->setResearchArea($researchAreas);
    $user->setResearchProgram($researchPrograms);
    $user->setWorkLine($workLines);
    $user->setCategory($categories);
    $user->setContractType($contracts);
    $user->setRol($roles);
    $user->setCollaborationExperiment($collaborationExperiment);
    $user->setTeamReference($teamReference);
    $user->setScientificFirm($scientificFirm);
    $user->setBuilding($building);
    $user->setLocation($room);
    $user->setUserReference($userReference);
    $user->setEmail($email);
    $user->setDeleteImage($deleteImage);
    $user->setIgfaeDepartment($igfaeDepartment);
    
    file_put_contents(ABSPATH."/usercreation.log","newuser: 'user' variable contents: ".print_r($user,true)."\n",FILE_APPEND);

    // COMPROBAMOS QUE EL IDCARD SEA ÚNICO
    if ($user->getIdCard() != null && getUserByIDCard($user) > 0) {
        wp_send_json_error(new WP_Error('-1', __('ID card must be unique')));
    } else if (!verificarValidarInformacion($validated, $published)) {
        // No se permite publicar sin haber validado previamente
        wp_send_json_error(new WP_Error('-1', __('In order to publish, the information must first be validated')));
    } else {
        if ($user->getEmail() != null && getUserByEmail($user) > 0) {
            wp_send_json_error(new WP_Error('-1', __('Email must be unique')));
        } else {
            // $idUser-> id del usuario a editar, $idUserSave-> id del nuevo usuario
            $idUserSave = saveUser($data, $user);
            $return = array(
                'idUser' => $idUser,
                'idUserSave'      => $idUserSave
            );
            wp_send_json_success($return);
        }
    }
    exit();
}
