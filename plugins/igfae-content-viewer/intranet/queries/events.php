<?php
function fetchEventExport($Event)
{

    $resultIds = fetchevents($Event, 0, NULL);
    $result = [];
    $idsEvent = [];
    $idsEventStr = "";
    foreach ($resultIds['events'] as $row) {
        $idsEvent[] = $row->getEventID();
    }
    $idsEventStr = implode(',', $idsEvent);

    if (count($idsEvent) > 0) {
        $sql = "SELECT distinct 
        e.EventID as ID,
        et.Name as EventType,
        est.Name as EventSubtype,
        e.IGFAEOrganizated,
        group_concat(distinct i.InstitutionName) as IGFAEOganizatedWith,
        e.IGFAEWithMemberPresence,
        group_concat(distinct concat(p1.Name, ' ', p1.FirstSurname, ' ', coalesce(p1.SecondSurname, '')) separator ' ; ') as IGFAESpeakerUser,
        e.SpeakerNoIgfae,
        group_concat(distinct concat(p2.Name, ' ', p2.FirstSurname, ' ', coalesce(p2.SecondSurname, ''))) as OrganizatedUser,
        COALESCE(elen.Title, eles.Title, elgl.Title) as Title,
        e.Abstract,
        group_concat(distinct ek.Name separator ' ; ') as keywords,
        e.Venue,
        e.LocationLink,
        l.Name as EventLanguage,
        e.Streaming,
        e.Website,
        e.Participants,
        e.SignatureLink,
        group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
        group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
        group_concat(distinct ec.Name separator ' ; ') as InternationalConsortium,
        group_concat(distinct concat(rg.Name, ' (', rg.Acronym, ')') separator ' ; ') as ResearchGroups,
        wl.Name as Activity,
        COALESCE(plen.Title, ples.Title, plgl.Title) as FinancingProjectTitle,        
        group_concat(distinct concat(p3.Name, ' ', p3.FirstSurname, ' ', coalesce(p3.SecondSurname, '')) separator ' ; ') as AssistantsIGFAE,
        group_concat(distinct etl.Link separator ' ; ') as TalkLinks,
        e.Comment,
        e.Validated,
        e.ValidatedBy,
        e.ValidatedOn,
        e.Published,
        e.PublishedBy,
        e.PublishedOn 
    from Event e 
    inner join EventType et
        on et.EventTypeID = e.EventTypeID 
    left join EventSubType est
        on est.EventTypeID = et.EventTypeID 
        and est.EventSubTypeID = e.EventSubTypeID
    left join EventLanguage elen
        on elen.EventID = e.EventID 
        and elen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join EventLanguage eles
        on eles.EventID = e.EventID 
        and eles.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join EventLanguage elgl
        on elgl.EventID = e.EventID 
        and elgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    left join EventResearchArea era
        on era.EventID = e.EventID 
    left join ResearchArea ra
        on ra.ResearchAreaID = era.ResearchAreaID 
    left join EventResearchProgram erp
        on erp.EventID = e.EventID 
    left join ResearchProgram rp
        on rp.ResearchProgramID = erp.ResearchProgramID 
    left join EventResearchGroup erg
        on erg.EventID = e.EventID
    left join ResearchGroup rg
        on rg.ResearchGroupID = erg.ResearchGroupID 
    left join EventWorkLine ewl
        on ewl.EventID = e.EventID 
    left join WorkLine wl
        on wl.WorkLineID = ewl.WorkLineID 
    left join EventTalkLink etl
        on etl.EventID = e.EventID 
    left join EventPersonnel epspeaker
        on epspeaker.EventID = e.EventID 
        and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_SP . "
    left join Personnel p1 
        on p1.PersonnelID = epspeaker.PersonnelID
    left join EventInscription ei
        on ei.EventID = e.EventID 
    left join EventKeyword ek
        on ek.EventID = e.EventID 
    left join EventPersonnel eporgan
        on eporgan.EventID = e.EventID 
        and eporgan.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_UO . "
    left join Personnel p2
        on p2.PersonnelID = eporgan.PersonnelID
    left join EventInstitution ein
        on ein.EventID = ei.EventID 
    left join Institution i
        on i.InstitutionID = ein.InstitutionID 
    left join `Language` l
        on l.LanguageID = e.LanguageID 
    left join EventExperiment ee
        on ee.EventID = e.EventID 
    left join ExperimentalCollaboration ec
        on ec.ExperimentalCollaborationID = ee.ExperimentalCollaborationID 
    left join Project p
        on p.ProjectID = e.FinancingProjectID 
    left join ProjectLanguage plen
        on plen.ProjectID = p.ProjectID 
        and plen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProjectLanguage ples
        on ples.ProjectID = p.ProjectID 
        and ples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProjectLanguage plgl
        on plgl.ProjectID = p.ProjectID 
        and plgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    left join EventPersonnel asistant
        on asistant.EventID = e.EventID 
        and asistant.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_AS . "
    left join Personnel p3
        on p3.PersonnelID = asistant.PersonnelID 
    where 1=1
    and e.EventID in ($idsEventStr)
    group by e.EventID ";

        $result = igfae_intranet_fetch_data($sql);

        //Recuperamos los Asistentes del evento
        $sql = "select ei.EventID as ID,
        concat(ei.Name, ' ', ei.Surname) as Name, 
        ei.Email,
        ei.Phone,
        ei.Membership 
        from Event e 
        inner join EventInscription ei
        on ei.EventID = e.EventID 
        where 1=1
        and e.EventID in ($idsEventStr)";

        $resultAssistantsEvents = igfae_intranet_fetch_data($sql);

        //Recuperamos las fechas de los eventos
        $sql = "select e.EventID as ID, ed.`Date` as Date, ed.StartHour as StartHour, ed.EndHour as EndHour
        from Event e
        inner join EventDate ed
            on ed.EventID = e.EventID 
        where 1=1
        and e.EventID in ($idsEventStr)";

        $resultFechaEvents = igfae_intranet_fetch_data($sql);
        foreach ($result['rows'] as $row) {
            // Asistentes al evento
            $namesAsistentesStr = "";
            $namesAsistente = [];
            foreach ($resultAssistantsEvents['rows'] as $rowAsistente) {
                if ($row->ID == $rowAsistente->ID) {
                    if (isset($rowAsistente->Email)) {
                        $namesAsistente[] = $rowAsistente->Name . "-" . $rowAsistente->Email;
                    } else {
                        $namesAsistente[] = $rowAsistente->Name;
                    }
                }
            }
            if (count($namesAsistente) > 0) {
                $namesAsistentesStr = implode(' ; ', $namesAsistente);
            }

            $row->nameAsistente = $namesAsistentesStr;


            // Fechas del evento
            $namesDateStr = "";
            $namesDate = [];
            foreach ($resultFechaEvents['rows'] as $rowDate) {
                if ($row->ID == $rowDate->ID) {
                    if ($rowDate->StartHour != null && $rowDate->EndHour != null) {
                        $namesDate[] = formatDate($rowDate->Date) . " (" . formatHour($rowDate->StartHour) . "-" . formatHour($rowDate->EndHour) . ")";
                    } else if ($rowDate->StartHour != null && $rowDate->EndHour == null) {
                        $namesDate[] = formatDate($rowDate->Date) . " (" . formatHour($rowDate->StartHour) . ")";
                    } else if ($rowDate->StartHour == null && $rowDate->EndHour != null) {
                        $namesDate[] = formatDate($rowDate->Date) . " (" . formatHour($rowDate->EndHour) . ")";
                    } else {
                        $namesDate[] = formatDate($rowDate->Date);
                    }
                }
            }
            if (count($namesDate) > 0) {
                $namesDateStr = implode(' ; ', $namesDate);
            }

            $row->nameDate = $namesDateStr;
        }
        return $result['rows'];
    } else {
        return $result;
    }
}

function fetchevents($events, $offset = 0, $amount = 10, $order = null)
{

    $where = "";
    $prepareParams = [];
    if (isset($events)) {
        $queryPrepare = getPrepareSearchEvents($events);
        $where = $queryPrepare['where'];
        $prepareParams = $queryPrepare['params'];
    }
    $order = getOrderEvent($order);
    $pagination = [];
    $limit = "";

    $user_id = get_current_user_id();
    $igfaeUserId = getUserByWPUserID($user_id)[0]["id"];

    $sql = "select distinct 
	e.EventID,
	et.Name as EventType,
	est.Name as EventSubtype,
	COALESCE(elen.Title, eles.Title, elgl.Title) as Title,
	group_concat(distinct concat(p1.Name, ' ', p1.FirstSurname, ' ', coalesce(p1.SecondSurname, '')) separator ' ; ') as IGFAESpeakerUser,
	e.SpeakerNoIgfae,
	group_concat(distinct concat(p2.Name, ' ', p2.FirstSurname, ' ', coalesce(p2.SecondSurname, ''))) as OrganizatedUser,
	e.Validated,
    e.Published,
    if (e.CreatedBy= {$igfaeUserId}, 1, 0) as esCreador
from Event e 
inner join EventType et
	on et.EventTypeID = e.EventTypeID 
left join EventSubType est
	on est.EventTypeID = et.EventTypeID 
	and est.EventSubTypeID = e.EventSubTypeID
left join EventLanguage elen
	on elen.EventID = e.EventID 
	and elen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
left join EventLanguage eles
	on eles.EventID = e.EventID 
	and eles.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
left join EventLanguage elgl
	on elgl.EventID = e.EventID 
	and elgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
left join EventResearchArea era
	on era.EventID = e.EventID 
left join EventResearchProgram erp
	on erp.EventID = e.EventID 
left join EventResearchGroup erg
	on erg.EventID = e.EventID 
left join EventWorkLine ewl
	on ewl.EventID = e.EventID 
left join EventTalkLink etl
	on etl.EventID = e.EventID 
left join EventPersonnel epspeaker
	on epspeaker.EventID = e.EventID 
	and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_SP . "
left join Personnel p1 
	on p1.PersonnelID = epspeaker.PersonnelID
left join EventDate ed
	on ed.EventID = e.EventID 
left join EventInscription ei
	on ei.EventID = e.EventID 
left join EventKeyword ek
	on ek.EventID = e.EventID 
left join EventPersonnel eporgan
	on eporgan.EventID = e.EventID 
	and eporgan.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_UO . "
left join Personnel p2
    on p2.PersonnelID = eporgan.PersonnelID
left join EventPersonnel ep
    on ep.EventID = e.EventID      
    WHERE 
{$where}
group by e.EventID
order by {$order}";

    if (isset($amount)) {
        $pagination = getPaginationInfoEvent($offset, $amount, $sql, $prepareParams);
        $sql .= " LIMIT {$offset}, {$amount}";
    }

    $rows = igfae_intranet_fetch_data($sql, $prepareParams);
    $eventsList = [];
    foreach ($rows['rows'] as $row) {
        $event = new IntranetEvents();
        $event->setEventID($row->EventID);
        $event->setEventType($row->EventType);
        $event->setEventSubType($row->EventSubtype);
        $event->setTitle($row->Title);
        $event->setSpeakerIgfae($row->IGFAESpeakerUser);
        $event->setSpeakerNoIgfae($row->SpeakerNoIgfae);
        $event->setOrganizatedUser($row->OrganizatedUser);
        $event->setValidated($row->Validated);
        $event->setPublished($row->Published);
        $event->setEsCreador($row->esCreador);
        $eventsList[] = $event;
    }

    $info = [
        'events' => $eventsList,
        'pagination' => $pagination
    ];

    return $info;
}


function getOrderEvent($order)
{
    if (!isset($order)) {
        return ' Title ASC';
    } else {
        $sort = ' ';
        switch ($order[0]["column"]) {
            case 0:
                $sort .= 'EventType ';
                break;
            case 1:
                $sort .= 'EventSubType ';
                break;
            case 2:
                $sort .= 'Title ';
                break;
            case 3:
                $sort .= 'IGFAESpeakerUser ';
                break;
            case 4:
                $sort .= 'SpeakerNoIgfae ';
                break;
            case 5:
                $sort .= 'OrganizatedUser ';
                break;
            case 6:
                $sort .= 'Validated ';
                break;
            case 7:
                $sort .= 'Published ';
                break;
            default:
                $sort .= 'Title ';
                break;
        }
        $sort .= $order[0]["dir"];
        return $sort;
    }
}

function getPaginationInfoEvent($offset, $amount, $sql, $params = [])
{
    $rows = igfae_intranet_fetch_data($sql, $params);
    $result = [
        'draw'    => ($offset / $amount) + 1,
        'recordsTotal'  =>  $rows['count'],
        "recordsFiltered"  => $rows['count']
    ];


    return $result;
}

function getPrepareSearchEvents($events)
{
    $where = "1=1 ";
    $params = [];
    // Event type
    if ($events->getEventType()) {
        $typesCount = count($events->getEventType());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersEventType = implode(',', $paramsPlaceholders);

        $where .= " AND (et.EventTypeID IN ({$placeholdersEventType}))";
        $params = array_merge($params, $events->getEventType());
    }
    // Event Subtype
    if ($events->getEventSubType()) {
        $typesCount = count($events->getEventSubType());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersEventSubType = implode(',', $paramsPlaceholders);

        $where .= " AND (est.EventSubTypeID IN ({$placeholdersEventSubType}))";
        $params = array_merge($params, $events->getEventSubType());
    }
    if ($events->getTitle()) {
        $where .= " AND ((elen.Title) like %s or (eles.Title) like %s or (elgl.Title) like %s)";
        $params[] = '%' . $events->getTitle() . '%';
        $params[] = '%' . $events->getTitle() . '%';
        $params[] = '%' . $events->getTitle() . '%';
    }
    // Research area select multiple
    if ($events->getResearchArea()) {
        $typesCount = count($events->getResearchArea());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchArea = implode(',', $paramsPlaceholders);

        $where .= " AND (era.ResearchAreaID IN ({$placeholdersResearchArea}))";
        $params = array_merge($params, $events->getResearchArea());
    }
    // Research program select multiple
    if ($events->getResearchProgram()) {
        $typesCount = count($events->getResearchProgram());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProgram = implode(',', $paramsPlaceholders);

        $where .= " AND (erp.ResearchProgramID IN ({$placeholdersResearchProgram}))";
        $params = array_merge($params, $events->getResearchProgram());
    }
    //Research Group
    if ($events->getResearchGroup()) {
        $typesCount = count($events->getResearchGroup());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchGroup = implode(',', $paramsPlaceholders);

        $where .= " AND (erg.ResearchGroupID IN ({$placeholdersResearchGroup}))";
        $params = array_merge($params, $events->getResearchGroup());
    }
    // Work line select multiple
    if ($events->getWorkLine()) {
        $typesCount = count($events->getWorkLine());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersWorkLine = implode(',', $paramsPlaceholders);

        $where .= " AND (ewl.WorkLineID IN ({$placeholdersWorkLine}))";
        $params = array_merge($params, $events->getWorkLine());
    }
    //Research Project
    if ($events->getResearchProject()) {
        $typesCount = count($events->getResearchProject());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersResearchProject = implode(',', $paramsPlaceholders);

        $where .= " AND (FinancingProjectID IN ({$placeholdersResearchProject}))";
        $params = array_merge($params, $events->getResearchProject());
    }
    // IGFAE Organizated
    if ($events->getIGFAEOrganizated() != null) {
        $where .= " AND (e.IGFAEOrganizated  =  %d)";
        $params[] = $events->getIGFAEOrganizated();
    }
    // Speaker Igfae
    if ($events->getSpeakerIGFAE()) {
        $typesCount = count($events->getSpeakerIGFAE());
        $paramsPlaceholders = array_fill(0, $typesCount, '%d');
        $placeholdersSpeakerIgfae = implode(',', $paramsPlaceholders);

        $where .= " AND (epspeaker.PersonnelID IN ({$placeholdersSpeakerIgfae}))";
        $params = array_merge($params, $events->getSpeakerIGFAE());
    }
    // Speaker NO Igfae
    if ($events->getSpeakerNoIgfae()) {
        $typesCount = count($events->getSpeakerNoIgfae());
        $paramsPlaceholders = array_fill(0, $typesCount, '%s');
        $placeholdersSpeakerNoIgfae = implode(',', $paramsPlaceholders);

        $where .= " AND (e.SpeakerNoIgfae IN ({$placeholdersSpeakerNoIgfae}))";
        $params = array_merge($params, $events->getSpeakerNoIgfae());
    }
    // Start Date
    if ($events->getStartDate()) {
        $startDates =  explode("-", $events->getStartDate());
        $startDate1 = explode("/", trim($startDates[0]));
        $fechaFormateada1 = array($startDate1[2], $startDate1[0], $startDate1[1]);
        $fecha1 = implode("-", $fechaFormateada1);
        $startDate2 = explode("/", trim($startDates[1]));
        $fechaFormateada2 = array($startDate2[2], $startDate2[0], $startDate2[1]);
        $fecha2 = implode("-", $fechaFormateada2);
        $where .= " AND (ed.Date BETWEEN %s and %s)";
        $params[] = $fecha1;
        $params[] = $fecha2;
    }
    // Keyword select multiple
    if ($events->getKeywords()) {
        $typesCount = count($events->getKeywords());
        $paramsPlaceholders = array_fill(0, $typesCount, '%s');
        $placeholdersKeyword = implode(',', $paramsPlaceholders);

        $where .= " AND (ek.Name IN ({$placeholdersKeyword}))";
        $params = array_merge($params, $events->getKeywords());
    }
    // Membership
    if ($events->getMembership()) {
        $where .= " and (ei.Membership like %s) ";
        $params[] = '%' . $events->getMembership() . '%';
    }
    // Assistant contact
    if ($events->getEmail()) {
        $where .= " and (ei.email like '%s') ";
        $params[] = '%' . $events->getEmail() . '%';
    }
    // EventTalkLink
    if ($events->getEventTalkLink()) {
        $where .= " and (etl.Link like '%s') ";
        $params[] = '%' . $events->getEventTalkLink() . '%';
    }
    // Validated
    if ($events->getValidated() != null) {
        $where .= " AND (e.Validated  =  %d)";
        $params[] = $events->getValidated();
    }
    // Published
    if ($events->getPublished() != null) {
        $where .= " AND (e.Published  =  %d)";
        $params[] = $events->getPublished();
    }

    //permisos
    $user_id = get_current_user_id();

    if (
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_GESTION) &&
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_VISITANTE_TEMPORAL) &&
        !is_user_in_role($user_id, IGFAE_INTRANET_ROLE_COLABORADOR) &&
        (is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_GRUPO) ||
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_PROYECTO) ||
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR_REFERENCIA) ||
            is_user_in_role($user_id, IGFAE_INTRANET_ROLE_INVESTIGADOR))
    ) {

        $igfaeUserId = getUserByWPUserID($user_id);
        if (!empty($igfaeUserId)) {
            $igfae_user_id = $igfaeUserId[0]["id"];
            $where .= " AND (ep.PersonnelID = %d or e.CreatedBy = %d)";
            $params[] =  $igfae_user_id;
            $params[] =  $igfae_user_id;
        }
    }


    return array(
        "where" => $where,
        "params" => $params
    );
}


function saveEvent($datosInsert, $event)
{
    $idEvent = $_POST[IGFAE_INTRANET_PARAM_IDEVENT];

    if (isset($idEvent) && strlen($idEvent) > 0) {

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();


        $where = array('EventID' => $idEvent);

        igfae_intranet_update("Event", $datosInsert, $where, null, null);

        // ELIMINAMOS LOS DATOS ANTIGUOS E INSERTAMOS LOS NUEVOS
        deleteRelationsEvent($idEvent);

        //Alta datos tablas relacionadas
        insertEventLanguage($idEvent, $event->getTitleES(), IGFAE_INTRANET_LANGUAGE_ES);
        insertEventLanguage($idEvent, $event->getTitleEN(), IGFAE_INTRANET_LANGUAGE_EN);
        insertEventLanguage($idEvent, $event->getTitleGL(), IGFAE_INTRANET_LANGUAGE_GL);
        insertEventExperiment($idEvent, $event->getCollaborationExperiment());
        //insertEventInscription($idEvent, $event->getAssistants());
        insertEventInstitution($idEvent, $event->getInstitution());
        insertEventDate($idEvent, $event->getEventDates());
        insertEventKeyword($idEvent, $event->getKeywords());
        insertEventResearchGroup($idEvent, $event->getResearchGroup());
        insertEventResearchArea($idEvent, $event->getResearchArea());
        insertEventResearchProgram($idEvent, $event->getResearchProgram());
        insertEventWorkLine($idEvent, $event->getWorkLine());
        insertEventPersonnel($idEvent, $event->getSpeakerIgfae(), IGFAE_INTRANET_PERSONNEL_TYPE_SP);
        insertEventPersonnel($idEvent, $event->getOrganizatedUser(), IGFAE_INTRANET_PERSONNEL_TYPE_UO);
        insertEventPersonnel($idEvent, $event->getAssistants(), IGFAE_INTRANET_PERSONNEL_TYPE_AS);
        insertEventTalkLink($idEvent, $event->getEventTalkLink());

        // Auditoria        
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_MODIFY, IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        }

        // Authorization
        $hasAuthorization = igfae_user_has_authorization_for_resource(IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        if ($hasAuthorization) { //si tiene autorización actualizamos su estado a cerrada
            closeAuthorizedAuthorizationForResource(IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        }    

        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    } else {
        //NUEVO EVENTO
        global $current_user;

        /** START TRANSACTION */
        igfae_intranet_begin_transaction();

        $datosInsert['CreatedBy'] = getUserByWPUserID($current_user->ID)[0]["id"];

        $idEvent = igfae_intranet_insert("Event", $datosInsert, null);

        //Alta datos tablas relacionadas
        insertEventLanguage($idEvent, $event->getTitleES(), IGFAE_INTRANET_LANGUAGE_ES);
        insertEventLanguage($idEvent, $event->getTitleEN(), IGFAE_INTRANET_LANGUAGE_EN);
        insertEventLanguage($idEvent, $event->getTitleGL(), IGFAE_INTRANET_LANGUAGE_GL);
        insertEventExperiment($idEvent, $event->getCollaborationExperiment());
        //insertEventInscription($idEvent, $event->getAssistants());
        insertEventInstitution($idEvent, $event->getInstitution());
        insertEventDate($idEvent, $event->getEventDates());
        insertEventKeyword($idEvent, $event->getKeywords());
        insertEventResearchGroup($idEvent, $event->getResearchGroup());
        insertEventResearchArea($idEvent, $event->getResearchArea());
        insertEventResearchProgram($idEvent, $event->getResearchProgram());
        insertEventWorkLine($idEvent, $event->getWorkLine());
        insertEventPersonnel($idEvent, $event->getSpeakerIgfae(), IGFAE_INTRANET_PERSONNEL_TYPE_SP);
        insertEventPersonnel($idEvent, $event->getOrganizatedUser(), IGFAE_INTRANET_PERSONNEL_TYPE_UO);
        insertEventPersonnel($idEvent, $event->getAssistants(), IGFAE_INTRANET_PERSONNEL_TYPE_AS);
        insertEventTalkLink($idEvent, $event->getEventTalkLink());



        // Auditoria
        saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        if ($datosInsert["Validated"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_VALIDATE, IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        }
        if ($datosInsert["Published"] != NULL) {
            saveAudit(IGFAE_INTRANET_AUDIT_TYPE_PUBLISH, IGFAE_INTRANET_ENTITY_NAME_EVENT, $idEvent);
        }

        if (igfae_intranet_is_transaction_ok()) {
            igfae_intranet_commit_transaction();
        } else {
            igfae_intranet_rollback_transaction();
        }
    }

    return $idEvent;
}

// Delete relations event
function deleteRelationsEvent($id)
{
    $where = array('EventID' => $id);

    //Borramos las tablas relacionadas
    igfae_intranet_delete('EventDate', $where);
    igfae_intranet_delete('EventExperiment', $where);
    igfae_intranet_delete('EventInscription', $where);
    igfae_intranet_delete('EventInstitution', $where);
    igfae_intranet_delete('EventKeyword', $where);
    igfae_intranet_delete('EventLanguage', $where);
    igfae_intranet_delete('EventPersonnel', $where);
    igfae_intranet_delete('EventResearchArea', $where);
    igfae_intranet_delete('EventResearchGroup', $where);
    igfae_intranet_delete('EventResearchProgram', $where);
    igfae_intranet_delete('EventTalkLink', $where);
    igfae_intranet_delete('EventWorkLine', $where);
}

function insertEventDate($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $time_input = strtotime($item['date']);
            $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
            $startHour = NULL;
            $endHour = NULL;
            if ($item['startHour'] != "") {
                $time_input = strtotime(str_replace(' ', '', $item['startHour']));
                $startHour = date(IGFAE_INTRANET_DATE_FORMAT_H_I_S, $time_input);
            }
            if ($item['endHour'] != "") {
                $time_input = strtotime(str_replace(' ', '', $item['endHour']));
                $endHour = date(IGFAE_INTRANET_DATE_FORMAT_H_I_S, $time_input);
            }

            $data = array('Date' => $startDate, 'EventID' => $id, 'StartHour' => $startHour, 'EndHour' => $endHour);

            igfae_intranet_insert('EventDate', $data);
        }
    }
}
function insertEventLanguage($id, $item, $language)
{
    if (isset($id)  && $id > 0 && isset($item)) {
        $data = array('LanguageID' => $language, 'EventID' => $id, 'Title' => $item);
        igfae_intranet_insert('EventLanguage', $data);
    }
}
function insertEventExperiment($id, $item)
{
    if (isset($id)  && $id > 0 && isset($item)) {
        $data = array('ExperimentalCollaborationID' => $item, 'EventID' => $id);
        igfae_intranet_insert('EventExperiment', $data);
    }
}
function insertEventInscription($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('Name' => $item, 'EventID' => $id);
            //Faltan campos surname, email
            igfae_intranet_insert('EventInscription', $data);
        }
    }
}
function insertEventInstitution($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $item = existInstitution($item);
            $data = array('InstitutionID' => $item, 'EventID' => $id);
            igfae_intranet_insert('EventInstitution', $data);
        }
    }
}
function insertEventKeyword($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        existKeywords($elements);
        foreach ($elements as $item) {
            $data = array('Name' => trim($item), 'EventID' => $id);
            igfae_intranet_insert('EventKeyword', $data);
        }
    }
}
function insertEventResearchGroup($id, $item)
{
    if (isset($id)  && $id > 0 && isset($item)) {
        $data = array('ResearchGroupID' => $item, 'EventID' => $id);
        igfae_intranet_insert('EventResearchGroup', $data);
    }
}
function insertEventResearchArea($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('ResearchAreaID' => $item, 'EventID' => $id);
            igfae_intranet_insert('EventResearchArea', $data);
        }
    }
}
function insertEventResearchProgram($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('ResearchProgramID' => $item, 'EventID' => $id);
            igfae_intranet_insert('EventResearchProgram', $data);
        }
    }
}

function insertEventWorkLine($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        $idNewWorkLine =  0;
        //Calculamos el valor máximo para el ID
        $sql = "SELECT MAX(WorkLineID) as id FROM WorkLine";
        $rows = igfae_intranet_fetch_data($sql);
        foreach ($rows['rows'] as $row) {
            $workLineIDMAX = $row->id;
        }
        $idNewWorkLine =  intVal($workLineIDMAX);
        foreach ($elements as $item) {
            if (count(existWorkLines($item)) == 0) {
                $idNewWorkLine++;
                insertWorkLines($item, $idNewWorkLine);
                $data = array('WorkLineID' => $idNewWorkLine, 'EventID' => $id);
                igfae_intranet_insert('EventWorkLine', $data);
            } else {
                $data = array('WorkLineID' => $item, 'EventID' => $id);
                igfae_intranet_insert('EventWorkLine', $data);
            }
        }
    }
}

function insertEventPersonnel($id, $elements, $typePersonnel)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('PersonnelID' => $item, 'EventID' => $id, 'PersonnelTypeID' => $typePersonnel);
            igfae_intranet_insert('EventPersonnel', $data);
        }
    }
}
function insertEventTalkLink($id, $elements)
{
    if (isset($id)  && $id > 0 && isset($elements)) {
        foreach ($elements as $item) {
            $data = array('Link' => $item, 'EventID' => $id);
            igfae_intranet_insert('EventTalkLink', $data);
        }
    }
}
//Funcion que comprueba si existen institutions
function existInstitution($item)
{
    $where = "";
    $params = [];
    $where .= " AND InstitutionID =  %d";
    $params[] =  $item;

    $sql = "SELECT InstitutionName as 'name'
        FROM Institution 
            WHERE 1=1 
            {$where} ";
    $rows = igfae_intranet_fetch_data($sql, $params);
    $resultArray = array();

    foreach ($rows['rows'] as $row) {
        $resultArray[] = array("name" => $row->name);
    }
    if (count($resultArray) == 0) {
        $data = array('InstitutionName' => $item);
        $item = igfae_intranet_insert('Institution', $data);
    }
    return $item;
}

// Delete event
function deleteEvent($id)
{

    /** START TRANSACTION */
    igfae_intranet_begin_transaction();

    // Auditoria
    saveAudit(IGFAE_INTRANET_AUDIT_TYPE_DELETE, IGFAE_INTRANET_ENTITY_NAME_EVENT, $id);
    $where = array('EventID' => $id);

    igfae_intranet_delete('EventDate', $where);
    igfae_intranet_delete('EventExperiment', $where);
    igfae_intranet_delete('EventInscription', $where);
    igfae_intranet_delete('EventInstitution', $where);
    igfae_intranet_delete('EventKeyword', $where);
    igfae_intranet_delete('EventLanguage', $where);
    igfae_intranet_delete('EventPersonnel', $where);
    igfae_intranet_delete('EventResearchArea', $where);
    igfae_intranet_delete('EventResearchGroup', $where);
    igfae_intranet_delete('EventResearchProgram', $where);
    igfae_intranet_delete('EventTalkLink', $where);
    igfae_intranet_delete('EventWorkLine', $where);

    igfae_intranet_delete('Event', $where);


    /** END TRANSACTION */
    if (igfae_intranet_is_transaction_ok()) {
        igfae_intranet_commit_transaction();
    } else {
        igfae_intranet_rollback_transaction();
    }
}


function getEventById($eventId)
{
    $event = NULL;
    $where = "";
    $prepareParams = [];
    if (isset($eventId)) {
        $where = " AND e.EventID  = %d";
        $prepareParams = $eventId;
    }


    $sql = "SELECT distinct 
	e.EventID,
	et.EventTypeID,
	et.Name as EventType,
	est.EventSubTypeID,
	est.Name as EventSubtype,
	e.IGFAEOrganizated,
	group_concat(distinct i.InstitutionID ) as IGFAEOganizatedWithIDs,
	group_concat(distinct i.InstitutionName) as IGFAEOganizatedWith,
	e.IGFAEWithMemberPresence,
	group_concat(distinct p1.PersonnelID separator ' ; ') as IGFAESpeakerUserIDs,
	group_concat(distinct concat(p1.Name, ' ', p1.FirstSurname, ' ', coalesce(p1.SecondSurname, '')) separator ' ; ') as IGFAESpeakerUser,
	e.SpeakerNoIgfae,
	group_concat(distinct p2.PersonnelID) as OrganizatedUserIDs,
	group_concat(distinct concat(p2.Name, ' ', p2.FirstSurname, ' ', coalesce(p2.SecondSurname, ''))) as OrganizatedUser,
	elen.Title as TitleEn,
	eles.Title as TitleEs,
	elgl.Title as TitleGl,
	e.Abstract,
	group_concat(distinct ek.Name separator ' ; ') as keywords,
	e.Venue,
	e.LocationLink,
    l.LanguageID as EventLanguageID,
	l.Name as EventLanguage,
	e.Streaming,
	e.Website,
	e.Participants,
	e.SignatureLink,
	group_concat(distinct ra.ResearchAreaID separator ' ; ') as ResearchAreasIDs,
	group_concat(distinct ra.Name separator ' ; ') as ResearchAreas,
	group_concat(distinct rp.ResearchProgramID separator ' ; ') as ResearchProgramsIDs,
	group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchPrograms,
	group_concat(distinct ec.ExperimentalCollaborationID separator ' ; ') as ExperimentsIDs,
	group_concat(distinct ec.Name separator ' ; ') as Experiments,
	group_concat(distinct rg.ResearchGroupID separator ' ; ') as ResearchGroupsIDs,
	group_concat(distinct concat(rp.Name, ' (', rp.Acronym, ')') separator ' ; ') as ResearchGroups,
	e.FinancingProjectID,
	COALESCE(plen.Title, ples.Title, plgl.Title) as FinancingProjectTitle,
	group_concat(distinct p3.PersonnelID separator ' ; ') as AssistantsIGFAEIDs,
	group_concat(distinct concat(p3.Name, ' ', p3.FirstSurname, ' ', coalesce(p3.SecondSurname, '')) separator ' ; ') as AssistantsIGFAE,
	e.Comment,
	e.Validated,
	e.ValidatedBy,
	e.ValidatedOn,
	e.Published,
	e.PublishedBy,
	e.PublishedOn 
    from Event e 
    inner join EventType et
        on et.EventTypeID = e.EventTypeID 
    left join EventSubType est
        on est.EventTypeID = et.EventTypeID 
        and est.EventSubTypeID = e.EventSubTypeID
    left join EventLanguage elen
        on elen.EventID = e.EventID 
        and elen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join EventLanguage eles
        on eles.EventID = e.EventID 
        and eles.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join EventLanguage elgl
        on elgl.EventID = e.EventID 
        and elgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    left join EventResearchArea era
        on era.EventID = e.EventID 
    left join ResearchArea ra
        on ra.ResearchAreaID = era.ResearchAreaID 
    left join EventResearchProgram erp
        on erp.EventID = e.EventID 
    left join ResearchProgram rp
        on rp.ResearchProgramID = erp.ResearchProgramID 
    left join EventResearchGroup erg
        on erg.EventID = e.EventID
    left join ResearchGroup rg
        on rg.ResearchGroupID = erg.ResearchGroupID 
    left join EventWorkLine ewl
        on ewl.EventID = e.EventID 
    left join WorkLine wl
        on wl.WorkLineID = ewl.WorkLineID 
    left join EventPersonnel epspeaker
        on epspeaker.EventID = e.EventID 
        and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_SP . " 
    left join Personnel p1 
        on p1.PersonnelID = epspeaker.PersonnelID
    left join EventInscription ei
        on ei.EventID = e.EventID 
    left join EventKeyword ek
        on ek.EventID = e.EventID 
    left join EventPersonnel eporgan
        on eporgan.EventID = e.EventID 
        and eporgan.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_UO . " 
    left join Personnel p2
        on p2.PersonnelID = eporgan.PersonnelID
    left join EventInstitution ein
        on ein.EventID = ei.EventID 
    left join Institution i
        on i.InstitutionID = ein.InstitutionID 
    left join `Language` l
        on l.LanguageID = e.LanguageID 
    left join EventExperiment ee
        on ee.EventID = e.EventID 
    left join ExperimentalCollaboration ec
        on ec.ExperimentalCollaborationID = ee.ExperimentalCollaborationID 
    left join Project p
        on p.ProjectID = e.FinancingProjectID 
    left join ProjectLanguage plen
        on plen.ProjectID = p.ProjectID 
        and plen.LanguageID = " . IGFAE_INTRANET_LANGUAGE_EN . "
    left join ProjectLanguage ples
        on ples.ProjectID = p.ProjectID 
        and ples.LanguageID = " . IGFAE_INTRANET_LANGUAGE_ES . "
    left join ProjectLanguage plgl
        on plgl.ProjectID = p.ProjectID 
        and plgl.LanguageID = " . IGFAE_INTRANET_LANGUAGE_GL . "
    left join EventPersonnel asistant
        on asistant.EventID = e.EventID 
        and asistant.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_AS . " 
    left join Personnel p3
        on p3.PersonnelID = asistant.PersonnelID 
    where 1=1
    {$where} ";


    $rows = igfae_intranet_fetch_data($sql, $prepareParams);


    foreach ($rows['rows'] as $row) {
        $event = new IntranetEvents();
        $event->setEventID($row->EventID);
        $event->setEventType($row->EventTypeID . ";" . $row->EventType);
        $event->setEventSubType($row->EventSubTypeID . ";" . $row->EventSubtype);
        $event->setIGFAEOrganizated($row->IGFAEOrganizated);
        $event->setIgfaeMemberPresence($row->IGFAEWithMemberPresence);
        $event->setSpeakerNoIgfae($row->SpeakerNoIgfae);
        $event->setTitleES($row->TitleEs);
        $event->setTitleEN($row->TitleEn);
        $event->setTitleGL($row->TitleGl);
        $event->setAbstract($row->Abstract);
        $event->setKeywords($row->keywords);
        $event->setVenue($row->Venue);
        $event->setLocationLink($row->LocationLink);
        $event->setLanguage($row->EventLanguageID . ";" . $row->EventLanguage);
        $event->setStreaming($row->Streaming);
        $event->setParticipants($row->Participants);
        $event->setSignatureLink($row->SignatureLink);
        $event->setResearchProject($row->FinancingProjectID . ";" . $row->FinancingProjectTitle);
        $event->setAssistantsIgfae($row->AssistantsIGFAE);
        $event->setWebSite($row->Website);
        $event->setValidated($row->Validated);
        $event->setPublished($row->Published);
        $event->setValidatedBy($row->ValidatedBy);
        $event->setPublishedBy($row->PublishedBy);
        $event->setValidatedOn($row->ValidatedOn);
        $event->setPublishedOn($row->PublishedOn);
        $event->setComments($row->Comment);


        $users[] = $event;
    }
    $eventId = $event->getEventID();

    // Recuperamos los valores de las instituciones
    $sql = "select distinct i.InstitutionID as 'id', i.InstitutionName as 'name'
    from EventInstitution ein
	left join Institution i
	on i.InstitutionID = ein.InstitutionID        
    where 1=1
    AND ein.EventID in ($eventId)";

    $rows = igfae_intranet_fetch_data($sql);
    $institutions = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $institutions[$row->id] = $row->name;
        }
    }
    $event->setInstitution($institutions);

    // Recuperamos los valores de speaker IGFAE
    $sql = "select distinct p1.PersonnelID as 'id', concat(p1.Name, ' ', p1.FirstSurname, ' ', coalesce(p1.SecondSurname, '')) as 'name'
    from EventPersonnel epspeaker	
    left join Personnel p1 
	on p1.PersonnelID = epspeaker.PersonnelID       
    where 1=1
    and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_SP . " 
    AND epspeaker.EventID in ($eventId)";

    $rows = igfae_intranet_fetch_data($sql);
    $speakers = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $speakers[$row->id] = $row->name;
        }
    }
    $event->setSpeakerIgfae($speakers);

    // Recuperamos los valores de los organizadors
    $sql = "select distinct p2.PersonnelID as 'id', concat(p2.Name, ' ', p2.FirstSurname, ' ', coalesce(p2.SecondSurname, '')) as 'name'
    from EventPersonnel epspeaker	
    left join Personnel p2 
    on p2.PersonnelID = epspeaker.PersonnelID       
    where 1=1
    and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_UO . " 
    AND epspeaker.EventID in ($eventId)";

    $rows = igfae_intranet_fetch_data($sql);
    $organizers = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $organizers[$row->id] = $row->name;
        }
    }
    $event->setOrganizatedUser($organizers);


    // Recuperamos los valores de las areas de investigacion
    $sql = "select distinct ra.ResearchAreaID as 'id', ra.Name as 'name'
    from EventResearchArea pra
    left join ResearchArea ra
        on ra.ResearchAreaID = pra.ResearchAreaID       
    where 1=1
    AND pra.EventID in ($eventId)";

    $rows = igfae_intranet_fetch_data($sql);
    $researchAreas = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $researchAreas[$row->id] = $row->name;
        }
    }
    $event->setResearchArea($researchAreas);

    // Recuperamos los valores de los grupos de investigación    
    $sql = "select distinct rg.ResearchGroupID as 'id', concat(rg.Name, ' (', rg.Acronym, ')') as 'name'
    from EventResearchGroup prg
    left join ResearchGroup rg
	on rg.ResearchGroupID = prg.ResearchGroupID	 
    where 1=1
    AND prg.EventID in ($eventId);";

    $rows = igfae_intranet_fetch_data($sql);
    $researchGroups = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $researchGroups[$row->id] = $row->name;
        }
    }
    $event->setResearchGroup($researchGroups);

    // Recuperamos los valores de los programas de investigación    
    $sql = "select distinct rp.ResearchProgramID as 'id', concat(rp.Name, ' (', rp.Acronym, ')') as 'name'
    from EventResearchProgram prp
    left join ResearchProgram rp
	on rp.ResearchProgramID = prp.ResearchProgramID	
    where 1=1
    AND prp.EventID in ($eventId);";

    $rows = igfae_intranet_fetch_data($sql);
    $researchPrograms = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $researchPrograms[$row->id] = $row->name;
        }
    }
    $event->setResearchProgram($researchPrograms);
    // Recuperamos las líneas de trabajo
    $sql = "select distinct wl.WorkLineID as 'id', wl.Name as 'name'
    from EventWorkLine pwl
    left join WorkLine wl
	on wl.WorkLineID = pwl.WorkLineID	
    where 1=1
    AND pwl.EventID in ($eventId);";

    $rows = igfae_intranet_fetch_data($sql);
    $workLines = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $workLines[$row->id] = $row->name;
        }
    }
    $event->setWorkline($workLines);

    // Recuperamos los experimentos
    $sql = "select distinct ec.ExperimentalCollaborationID as 'id', ec.Name as 'name'
    from EventExperiment ee
    left join ExperimentalCollaboration ec
    on ec.ExperimentalCollaborationID = ee.ExperimentalCollaborationID 
    where 1=1
    AND ee.EventID in ($eventId);";

    $rows = igfae_intranet_fetch_data($sql);
    $experiments = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $experiments[$row->id] = $row->name;
        }
    }
    $event->setCollaborationExperiment($experiments);

    //EventTalkLink
    $sql = "select etl.EventTalkLinkID as 'id', etl.Link  as 'name'
    from EventTalkLink etl 
    where 1=1
    AND etl.EventID in ($eventId);";

    $rows = igfae_intranet_fetch_data($sql);
    $talklinks = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $talklinks[$row->id] = $row->name;
        }
    }
    $event->setEventTalkLink($talklinks);

    // Recuperamos los asistentes
    $sql = "select distinct p2.PersonnelID as 'id', concat(p2.Name, ' ', p2.FirstSurname, ' ', coalesce(p2.SecondSurname, '')) as 'name'
    from EventPersonnel epspeaker	
    left join Personnel p2 
    on p2.PersonnelID = epspeaker.PersonnelID       
    where 1=1
    and epspeaker.PersonnelTypeID = " . IGFAE_INTRANET_PERSONNEL_TYPE_AS . " 
    AND epspeaker.EventID in ($eventId)";

    $rows = igfae_intranet_fetch_data($sql);
    $assistants = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->name)) {
            $assistants[$row->id] = $row->name;
        }
    }
    $event->setAssistants($assistants);

    // Recuperamos los asistentesNoIgfae

    $sql = "select ei.EventInscriptionID,
    ei.EventID,
    concat(ei.Name, ' ', ei.Surname) as Name, 
    ei.Email,
    ei.Phone,
    ei.Membership 
    from Event e 
    inner join EventInscription ei
    on ei.EventID = e.EventID 
    where 1=1
    and e.EventID in ($eventId)";

    $rows = igfae_intranet_fetch_data($sql);
    $assistantsNoIgfae = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->Name)) {
            $assistantsNoIgfae[$row->EventInscriptionID] = $row->Name . ";" . $row->Email;            
        }
    }
    $event->setAssistantsNoIgfae($assistantsNoIgfae);




    // Recuperamos las fechas de los eventos
    $sql = "select ed.EventDateID as 'id', ed.`Date` as 'dateEvent', ed.StartHour, 
	ed.EndHour
    from Event e
    inner join EventDate ed
	on ed.EventID = e.EventID      
    where 1=1
    and e.EventID in ($eventId)";

    $rows = igfae_intranet_fetch_data($sql);
    $dates = [];
    foreach ($rows['rows'] as $row) {
        if (isset($row->StartHour)) {
            if (isset($row->EndHour)) {
                $dates[formatDate($row->dateEvent) . ";" . formatHour($row->StartHour) . ";" . formatHour($row->EndHour)] = formatDate($row->dateEvent) . " (" . formatHour($row->StartHour) . "-" . formatHour($row->EndHour) . ")";
            } else {
                $dates[formatDate($row->dateEvent) . ";" . formatHour($row->StartHour)] = formatDate($row->dateEvent) . " (" . formatHour($row->StartHour) . ")";
            }
        } else {
            $dates[formatDate($row->dateEvent)] = formatDate($row->dateEvent);
        }
    }
    $event->setEventDates($dates);


    return $event;
}

function getSelectVenues()
{
    $sql = "SELECT DISTINCT e.Venue FROM Event e order by e.Venue ASC";

    $rows = igfae_intranet_fetch_data($sql);

    $result = [];

    foreach ($rows['rows'] as $row) {
        $event = new IntranetEvents();
        $event->setVenue($row->Venue);
        $result[] = $event;
    }

    return $result;
}


/**
 * Indíca si el usuario está relacionado con el evento.
 * @param: $user_id: user
 * @param: $id: id del evento
 * @param: $mode: modo de búsqueda
 * Si modo == 1 solo si el usuario está relacionado. 
 * Si modo == 2 comprueba si el usuario está relacionado o es el creador
 */
function hasRelatedEvent($user_id, $id, $mode = 2)
{
    $sqlHasRelation =  "select count(*)
    from Event e ";

    if ($mode == 1) {
        $sqlHasRelation .= "
        inner join EventPersonnel ep
        on ep.EventID = e.EventID  
        where 1=1 
            and ep.PersonnelID = $user_id
            ";
    } else if ($mode == 2) {
        $sqlHasRelation .= "
        inner join EventPersonnel ep
        on ep.EventID = e.EventID  
        where 1=1 
            and (ep.PersonnelID = $user_id or e.CreatedBy = $user_id)
        ";
    } else if ($mode == 3) {
        $sqlHasRelation .= " 
            where 1=1 
            and e.CreatedBy = $user_id
        ";
    }

    $sqlHasRelation .= "
        and e.EventID = $id";

    
    $result = igfae_intranet_get_var($sqlHasRelation);

    if ($result) {       
        return true;
    } else {        
        return false;
    }
}

function isUserCreatorOrRelatedWithEvent($user_id)
{

    $idEvent = $_GET['idEvent'];

    if (!isset($idEvent) || !isset($user_id)) {
        return false;
    }

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedEvent($id, $idEvent)) {
            return true;
        }
    }

    return false;
}
function isUserCreator($user_id)
{

    $idEvent = $_GET['idEvent'];

    if (!isset($idEvent) || !isset($user_id)) {
        return false;
    }

    //obtener user a partir del user de wp
    $igfaeUserId = getUserByWPUserID($user_id);
    if (!empty($igfaeUserId)) {
        $id = $igfaeUserId[0]["id"];
        if (hasRelatedEvent($id, $idEvent, 3)) {
            return true;
        }
    }

    return false;
}

function hasPermissionsUserCreator($user_id, $idEvent)
{
    // Solo para el rol COLABORADOR    
    if (is_user_in_role($user_id, IGFAE_INTRANET_ROLE_COLABORADOR)) {
        
        $igfaeUserId = getUserByWPUserID($user_id);
        if (!empty($igfaeUserId)) {
            $id = $igfaeUserId[0]["id"];
            if (hasRelatedEvent($id, $idEvent, 3)) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    return true;
}
//Comprobamos si esta validado el evento
function isEventsConsolidated($id) { 
    $sql = "SELECT count(*) 
    from `Event` e
    where
    e.Validated = 1
    and e.EventID = $id";
    
    $result = igfae_intranet_get_var($sql);
    
    if ($result) {
    return true;
    } else {
    return false;
    }
  }