<?php

/** EXPORT */
add_action('admin_action_export_events', 'export_events');
function export_events()
{
    $headers = [
        __("ID"), __("Event Type"), __("Event Subtype"), __("IGFAE organizated"), __("IGFAE oganizated with"),
        __("IGFAE withMember presence"), __("IGFAE speaker user"), __("Speaker no IGFAE"), __("Organizated user"), __("Title"), __("Abstract"),
        __("keywords"), __("Venue"), __("Location link"), __("Event language"), __("Streaming"),
        __("Website"),  __("Participants"), __("Signature link"), __("Research areas"), __("Research programs"),
        __("International consortium"), __("Research groups"), __("Activity"), __("Financing project"),
        __("Assistants IGFAE"), __("Talk links"), __("Comment"), __("Validated"), __("ValidatedBy"),
        __("Validated On"), __("Published"), __("Published By"), __("Published On "), __("Assistants "), __("Event Dates")
    ];

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $events = getSearchEventRequestModel();

    $event = fetchEventExport($events);

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($event, function (&$v, $k) {
        $v = get_object_vars($v);
        if (!empty($v)) {
            $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
            $v['Published'] = $v['Published'] == 1 ? __("Yes") : __("No");

            if (isset($v['IGFAEOrganizated'])) {
                $v['IGFAEOrganizated'] = $v['IGFAEOrganizated'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['IGFAEWithMemberPresence'])) {
                $v['IGFAEWithMemberPresence'] = $v['IGFAEWithMemberPresence'] == 1 ? __("Yes") : __("No"); 
            }
        
            $v['ValidatedOn'] = isset($v['ValidatedOn']) ? formatDateWithHour($v['ValidatedOn']) : "";
            $v['PublishedOn'] = isset($v['PublishedOn']) ? formatDateWithHour($v['PublishedOn']) : "";
            $v['Date'] = isset($v['Date']) ? formatDate($v['Date']) : "";
            $v['StartHour'] = isset($v['StartHour']) ? formatHour($v['StartHour']) : "";
            $v['EndHour'] = isset($v['EndHour']) ? formatHour($v['EndHour']) : "";
        }
    });
    if (!isset($event)) {
        $event = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($event, $headers);

    //llamamos a la función común para exportar
    igfae_export($event, __('Events'));
}



add_action("wp_ajax_search_events_submit", "api_search_events_submit");
add_action("wp_ajax_nopriv_search_events_submit", "api_search_events_submit");
function api_search_events_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $events = getSearchEventRequestModel();

    $info = fetchevents($events, $offset, $length, $order);

    $data = [
        "data" => $info['events'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}


/**
 * Get events type
 */

function getEventType()
{

    $EventType = getSelectEventType();
    $EventTypeSelect = NULL;
    foreach ($EventType as $key => $item) {
        $EventTypeSelect[$key] = $item;
    }
    $EventTypeSelect['items'] = $EventTypeSelect;

    return $EventTypeSelect;
}


/** Get search request*/

function getSearchEventRequestModel()
{

    //Event Type select multiple
    $eventType         = [];
    if (isset($_POST['eventType'])) {
        foreach ($_POST['eventType'] as $item) {
            $eventType[] = $item;
        }
    }

    //Event SubType select multiple
    $eventSubType         = [];
    if (isset($_POST['eventSubtype'])) {
        foreach ($_POST['eventSubtype'] as $item) {
            $eventSubType[] = $item;
        }
    }

    // Title text
    $title  = trim(filter_var($_POST['title'], FILTER_SANITIZE_STRING));

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

    //Research Group select multiple
    $researchGroup         = [];
    if (isset($_POST['researchGroup'])) {
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }

    //workline select multiple
    $workline         = [];
    if (isset($_POST['workLine'])) {
        foreach ($_POST['workLine'] as $item) {
            $workline[] = $item;
        }
    }

    //Research Project select multiple
    $researchProject         = [];
    if (isset($_POST['researchProject'])) {
        foreach ($_POST['researchProject'] as $item) {
            $researchProject[] = $item;
        }
    }

    //IGFAE Organizated
    $igfaeorganizated         = NULL;
    if (isset($_POST['IGFAEOrganizated'])) {
        $igfaeorganizated     = trim(filter_var($_POST['IGFAEOrganizated'], FILTER_SANITIZE_STRING));
    }

    //Speaker IGFAE select multiple
    $speakerIGFAE         = [];
    if (isset($_POST['speakerIGFAE'])) {
        foreach ($_POST['speakerIGFAE'] as $item) {
            $speakerIGFAE[] = $item;
        }
    }

    //Speaker No Igfae select multiple
    $speakerNoIgfae         = [];
    if (isset($_POST['SpeakerNoIgfae'])) {
        foreach ($_POST['SpeakerNoIgfae'] as $item) {
            $speakerNoIgfae[] = $item;
        }
    }

    //Start date
    $startDate = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));

    //Keywords select multiple
    $keywords         = [];
    if (isset($_POST['keywords'])) {
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = $item;
        }
    }

    // Membership input
    $membership      = trim(filter_var($_POST['Membership'], FILTER_SANITIZE_STRING));

    // Assistant contact
    $email      = trim(filter_var($_POST['email'], FILTER_SANITIZE_STRING));

    // EventTalkLink input
    $eventTalkLink      = trim(filter_var($_POST['eventTalkLink'], FILTER_SANITIZE_STRING));

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

    $event = new IntranetEvents();
    $event->setEventType($eventType);
    $event->setEventSubType($eventSubType);
    $event->setTitle($title);
    $event->setResearchArea($researchArea);
    $event->setResearchProgram($researchProgram);
    $event->setResearchGroup($researchGroup);
    $event->setWorkline($workline);
    $event->setResearchProject($researchProject);
    $event->setIgfaeorganizated($igfaeorganizated);
    $event->setSpeakerIGFAE($speakerIGFAE);
    $event->setSpeakerNoIgfae($speakerNoIgfae);
    $event->setStartDate($startDate);
    $event->setKeywords($keywords);
    $event->setMembership($membership);
    $event->setEventTalkLink($eventTalkLink);
    $event->setEmail($email);
    $event->setValidated($validated);
    $event->setPublished($published);
    return $event;
}

function existsEvent($eventId)
{
    $where = "";
    $params = [];

    $where .= " AND e.EventID = %d";
    $params[] = $eventId;

    $sql = "SELECT distinct
	e.EventID as 'id'
    FROM Event e 
        WHERE 1=1 {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("id" => $row->id);
    }
    return count($resultArray);
}

// Change value Event type
add_action("wp_ajax_load_subtypes_events", "api_getEventsSubTypes");
add_action("wp_ajax_nopriv_load_subtypes_events", "api_getEventsSubTypes");
function api_getEventsSubTypes()
{

    //eventType select multiple
    $type = $_POST['eventType'];
    $subtypes = getSelectEventSubType($type);

    wp_send_json_success($subtypes);
    exit();
}

/**
 * Get languages
 */
function getLanguages()
{
    $languages = getSelectLanguages();
    $languagesSelect = NULL;
    foreach ($languages as $key => $item) {
        $languagesSelect[$key] = $item;
    }
    $languagesSelect['items'] = $languagesSelect;

    return $languagesSelect;
}

add_action("wp_ajax_new_event_submit", "api_new_event_submit");
add_action("wp_ajax_nopriv_new_event_submit", "api_new_event_submit");
function api_new_event_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();

    global $current_user;
    // Id Event
    $idEvent = trim(filter_var($_POST['idEvent'], FILTER_SANITIZE_STRING));

    // Title
    $titleES         = NULL;
    if (isset($_POST['title_es']) && trim($_POST['title_es']) != "" && !empty($_POST['title_es'])) {
        $titleES      = $_POST['title_es'];
    }
    $titleEN         = NULL;
    if (isset($_POST['title_en']) && trim($_POST['title_en']) != "" && !empty($_POST['title_en'])) {
        $titleEN      = $_POST['title_en'];
    }
    $titleGL         = NULL;
    if (isset($_POST['title_gl']) && trim($_POST['title_gl']) != "" && !empty($_POST['title_gl'])) {
        $titleGL      = $_POST['title_gl'];
    }

    $eventType         = NULL;
    if (isset($_POST['eventType'])) {
        $eventType     = trim(filter_var($_POST['eventType'], FILTER_SANITIZE_STRING));
    }
    $eventSubtype         = NULL;
    if (isset($_POST['eventSubtype']) && $_POST['eventSubtype'] != "") {
        $eventSubtype     = trim(filter_var($_POST['eventSubtype'], FILTER_SANITIZE_STRING));
    }
    //igfaeOrganizated radio
    $igfaeOrganizated         = null;
    if (isset($_POST['igfaeOrganizated']) && !empty($_POST['igfaeOrganizated'])) {
        if ($_POST['igfaeOrganizated'] == "1") {
            $igfaeOrganizated = 1;
        } else {
            $igfaeOrganizated = 0;
        }
    }
    //igfaeMemberPresence radio
    $igfaeMemberPresence         = null;
    if (isset($_POST['igfaeMemberPresence']) && !empty($_POST['igfaeMemberPresence'])) {
        if ($_POST['igfaeMemberPresence'] == "1") {
            $igfaeMemberPresence = 1;
        } else {
            $igfaeMemberPresence = 0;
        }
    }
    //Institutions select multiple
    $institution         = NULL;
    if (isset($_POST['institution']) && !empty($_POST['institution'])) {
        $institution         = [];
        foreach ($_POST['institution'] as $item) {
            $institution[] = $item;
        }
    }
    //speakerIGFAE select multiple
    $speakerIGFAE         = NULL;
    if (isset($_POST['speakerIGFAE']) && !empty($_POST['speakerIGFAE'])) {
        $speakerIGFAE        = [];
        foreach ($_POST['speakerIGFAE'] as $item) {
            $speakerIGFAE[] = $item;
        }
    }
    // speakerNoIgfae
    $speakerNoIgfae = NULL;
    if (isset($_POST['speakerNoIgfae']) && !empty($_POST['speakerNoIgfae'])) {
        $speakerNoIgfae                 = trim(filter_var($_POST['speakerNoIgfae'], FILTER_SANITIZE_STRING));
    }

    // location link    
    $locationLink = NULL;
    if (isset($_POST['locationLink']) && !empty($_POST['locationLink'])) {
        $locationLink                 = trim(filter_var($_POST['locationLink'], FILTER_SANITIZE_STRING));
    }
    //Language select
    $language         = NULL;
    if (isset($_POST['language']) && !empty($_POST['language'])) {
        $language     = trim(filter_var($_POST['language'], FILTER_SANITIZE_STRING));
    }
    // streaming

    // streaming    
    $streaming = NULL;
    if (isset($_POST['streaming']) && !empty($_POST['streaming']) && !empty(trim($_POST['streaming']))) {
        $streaming                 = trim(filter_var($_POST['streaming'], FILTER_SANITIZE_STRING));
    }
    // website
    $website = NULL;
    if (isset($_POST['website']) && !empty($_POST['website'])) {
        $website                 = trim(filter_var($_POST['website'], FILTER_SANITIZE_STRING));
    }

    // participants
    $participants                 = formatNumberToBD(trim(filter_var($_POST['participants'], FILTER_SANITIZE_STRING)));
    // signatureLink
    $signatureLink                 = trim(filter_var($_POST['signatureLink'], FILTER_SANITIZE_STRING));

    //Project select
    $financingProject         = NULL;
    if (isset($_POST['financingProject']) && !empty($_POST['financingProject'])) {
        $financingProject     = trim(filter_var($_POST['financingProject'], FILTER_SANITIZE_STRING));
    }
    //Assistants select multiple
    $assistants         = NULL;
    if (isset($_POST['assistants']) && !empty($_POST['assistants'])) {
        $assistants        = [];
        foreach ($_POST['assistants'] as $item) {
            $assistants[] = $item;
        }
    }
    //Talk link select multiple
    $talkLink         = NULL;
    if (isset($_POST['talkLink']) && !empty($_POST['talkLink'])) {
        $talkLink        = [];
        foreach ($_POST['talkLink'] as $item) {
            $talkLink[] = $item;
        }
    }
    //organizatedUser select multiple
    $organizatedUser         = NULL;
    if (isset($_POST['organizatedUser']) && !empty($_POST['organizatedUser'])) {
        $organizatedUser        = [];
        foreach ($_POST['organizatedUser'] as $item) {
            $organizatedUser[] = $item;
        }
    }
    //Keywords select multiple
    $keywords         = NULL;
    if (isset($_POST['keywords']) && !empty($_POST['keywords'])) {
        $keywords         = [];
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = $item;
        }
    }

    //Research Area select multiple
    $researchArea         = NULL;
    if (isset($_POST['researchArea']) && !empty($_POST['researchArea'])) {
        $researchArea         = [];
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }
    //Research Program select multiple
    $researchProgram         = NULL;
    if (isset($_POST['researchProgram']) && !empty($_POST['researchProgram'])) {
        $researchProgram         = [];
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }
    //Collaboration experiments select
    $collaborationExperiment         = NULL;
    if (isset($_POST['collaborationExperiment']) && !empty($_POST['collaborationExperiment'])) {
        $collaborationExperiment     = trim(filter_var($_POST['collaborationExperiment'], FILTER_SANITIZE_STRING));
    }
    //Research Group select multiple
    $researchGroup         = NULL;
    if (isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])) {
        $researchGroup        =  trim(filter_var($_POST['researchGroup'], FILTER_SANITIZE_STRING));
    }
    //Worklines select multiple
    $worklines         = NULL;
    if (isset($_POST['worklines']) && !empty($_POST['worklines'])) {
        $worklines        = [];
        foreach ($_POST['worklines'] as $item) {
            $worklines[] = $item;
        }
    }
    //Research Project select multiple
    $researchProject         = NULL;
    if (isset($_POST['financingProject']) && !empty($_POST['financingProject'])) {
        $researchProject        = trim(filter_var($_POST['financingProject'], FILTER_SANITIZE_STRING));
    }
    // Abstract textarea
    $description         = NULL;
    if (isset($_POST['description']) && trim($_POST['description']) != "" && !empty($_POST['description']) && !empty(strip_tags($_POST['description']))) {
        $textDescription = str_replace('\"', '"', $_POST["description"]);
        $description = $purifier->purify(trim($textDescription));
    }
    $venue         = NULL;
    if (isset($_POST['venue'])) {
        $venue     = trim(filter_var($_POST['venue'], FILTER_SANITIZE_STRING));
    }
    // Comments input
    $comments         = NULL;
    if (isset($_POST['comments']) && trim($_POST['comments']) != "" && !empty($_POST['comments']) && !empty(strip_tags($_POST['comments']))) {
        $textComments = str_replace('\"', '"', $_POST["comments"]);
        $comments = $purifier->purify(trim($textComments));
    }
    //Event dates
    $eventDates = NULL;
    if (isset($_POST['tableDates']) && !empty($_POST['tableDates'])) {
        $eventDates = json_decode(stripcslashes($_POST['tableDates']), true);
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


    //Construimos el array con las columnas y valores para el insert    
    $data = array(
        'EventTypeID' => $eventType,
        'EventSubTypeID' => $eventSubtype,
        'IGFAEOrganizated' => $igfaeOrganizated,
        'IGFAEWithMemberPresence' => $igfaeMemberPresence,
        'SpeakerNoIgfae' => $speakerNoIgfae,
        'Comment' => $comments,
        'Venue' => $venue,
        'LocationLink' => $locationLink,
        'Abstract' => $description,
        'Streaming' => $streaming,
        'LanguageID' => $language,
        'Website' => $website,
        'Participants' => $participants,
        'SignatureLink' => $signatureLink,
        'FinancingProjectID' => $financingProject,
        'Validated' => $validated,
        'Published' => $published,
        'ValidatedBy' => $validatedBy,
        'PublishedBy' => $publishedBy,
        'ValidatedOn' => $validatedOn,
        'PublishedOn' => $publishedOn

    );

    $event = new IntranetEvents();
    $event->setEventID($idEvent);
    $event->setResearchGroup($researchGroup);
    $event->setKeywords($keywords);
    $event->setResearchArea($researchArea);
    $event->setResearchProgram($researchProgram);
    $event->setResearchProject($researchProject);
    $event->setWorkLine($worklines);
    $event->setCollaborationExperiment($collaborationExperiment);
    $event->setInstitution($institution);
    $event->setSpeakerIgfae($speakerIGFAE);
    $event->setOrganizatedUser($organizatedUser);
    $event->setTitleES($titleES);
    $event->setTitleEN($titleEN);
    $event->setTitleGL($titleGL);
    $event->setAssistants($assistants);
    $event->setEventTalkLink($talkLink);
    $event->setEventDates($eventDates);

    if (!verificarValidarInformacion($validated, $published)) {
        // No se permite publicar sin haber validado previamente
        wp_send_json_error(new WP_Error('-1', __('In order to publish, the information must first be validated')));
    } else {
        // $idEvent-> id del evento a editar, $idEventSave-> id del nuevo evento
        $idEventSave = saveEvent($data, $event);
        $return = array(
            'idEvent' => $idEvent,
            'idEventSave'      => $idEventSave
        );
        wp_send_json_success($return);
    }

    exit();
}
// Botón Delete event
add_action("wp_ajax_delete_event_submit", "api_delete_event_submit");
add_action("wp_ajax_nopriv_delete_event_submit", "api_delete_event_submit");
function api_delete_event_submit()
{
    $id = $_POST['idEvent'];    
    // Eliminamos el evento
    deleteEvent($id);
    wp_send_json_success();

    exit();
}
// Botón Delete event
add_action("wp_ajax_permissions_delete_event", "api_permissions_delete_event");
add_action("wp_ajax_nopriv_permissions_delete_event", "api_permissions_delete_event");
function api_permissions_delete_event()
{
    $user_id = get_current_user_id();
    
    $id = $_POST['idEvent'];   
    
    if (hasPermissionsUserCreator($user_id, $id)) {
        wp_send_json_success();
    } else {
        wp_send_json_error(new WP_Error('-1', __('You do not have permission to delete the event')));
    }
    

    exit();
}

/**
 * Get venues
 */
function getVenues()
{
    $venues = getSelectVenues();
    $venuesSelect = NULL;
    foreach ($venues as $key => $item) {
        $venuesSelect[$key] = $item;
    }
    $venuesSelect['items'] = $venuesSelect;

    return $venuesSelect;
}