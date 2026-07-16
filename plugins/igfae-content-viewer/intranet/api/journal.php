<?php

/**
 * Get journals editorials
 */
function getJournalsEditorials()
{
    global $wp_query;
    $journalEditorials = getSelectJournalEditorials();
    $journalEditorial = '';
    $journalEditorialSelected = NULL;
    $journalEditorialSelect = NULL;

    if (isset($wp_query->query_vars['magazineEditorial'])) {
        $journalEditorial = sanitize_title(urldecode($wp_query->query_vars['magazineEditorial']));
    }
    foreach ($journalEditorials as $key => $item) {
        $journalEditorialSelect[$key] = $item;
        if ($journalEditorial == sanitize_title($item->getName())) {
            $journalEditorialSelected[$key] = $item;
        }
    }
    $journalEditorialSelect['items'] = $journalEditorialSelect;
    $journalEditorialSelect['selected'] = $journalEditorialSelected;
    return $journalEditorialSelect;
}

/**
 * Get journals areas
 */
function getJournalsAreas()
{
    global $wp_query;
    $journalAreas = getSelectJournalAreas();
    $journalArea = '';
    $journalAreaSelect = NULL;
    $journalAreaSelect = NULL;
    
    if (isset($wp_query->query_vars['magazineArea'])) {
        $journalArea = sanitize_title(urldecode($wp_query->query_vars['magazineArea']));
    }
    foreach ($journalAreas as $key => $item) {
        $journalAreaSelect[$key] = $item;
        if ($journalArea == sanitize_title($item->getArea())) {
            $journalAreaSelect[$key] = $item;
        }
    }
    $journalAreaSelect['items'] = $journalAreaSelect;
    $journalAreaSelect['selected'] = $journalAreaSelect;
    return $journalAreaSelect;
}

// New Journal
add_action("wp_ajax_new_journal_pProjects_submit", "api_new_journal_pProjects_submit");
add_action("wp_ajax_nopriv_new_journal_pProjects_submit", "api_new_journal_pProjects_submit");
function api_new_journal_pProjects_submit()
{

    // Journal name
    $journalName                 = trim(filter_var($_POST['journalName'], FILTER_SANITIZE_STRING));
    // ISO Abbreviation     
    $ISOAbreviation         = NULL;
    if (isset($_POST['abbreviatedISOName']) && !empty($_POST['abbreviatedISOName'])) {
        $ISOAbreviation      = trim(filter_var($_POST['abbreviatedISOName'], FILTER_SANITIZE_STRING));
    }
    // Journal Area ID   
    $journalArea         = NULL;
    if (isset($_POST['magazineArea']) && !empty($_POST['magazineArea'])) {
        $journalArea      = trim(filter_var($_POST['magazineArea'], FILTER_SANITIZE_STRING));
    }
    // Electronic ISSN  
    $ISSNdigital         = NULL;
    if (isset($_POST['ISSNdigital']) && !empty($_POST['ISSNdigital'])) {
        $ISSNdigital      = trim(filter_var($_POST['ISSNdigital'], FILTER_SANITIZE_STRING));
    }
    // Printed ISSN  
    $ISSNprinting         = NULL;
    if (isset($_POST['ISSNprinting']) && !empty($_POST['ISSNprinting'])) {
        $ISSNprinting      = trim(filter_var($_POST['ISSNprinting'], FILTER_SANITIZE_STRING));
    }
    // Reference 
    $magazineReference         = NULL;
    if (isset($_POST['magazineReference']) && !empty($_POST['magazineReference'])) {
        $magazineReference      = trim(filter_var($_POST['magazineReference'], FILTER_SANITIZE_STRING));
    }
    $journal = new IntranetJournalComplete(NULL, $journalName, $ISOAbreviation, $journalArea, $ISSNdigital, $ISSNprinting, $magazineReference);
    if (getISSNValues($journal) > 0) {
        wp_send_json_error(new WP_Error('-1', 'Values not uniques'));
    } else {
        // Guardamos la nueva revista
        saveNewJournal($journal);
        wp_send_json_success();
    }
    exit();
}
