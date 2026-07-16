<?php
add_action("wp_ajax_search_locations_submit", "api_search_locations_submit");
add_action("wp_ajax_nopriv_search_locations_submit", "api_search_locations_submit");
function api_search_locations_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $locations = getLocations($order);

    $data = [
        "data" => $locations,
        "draw" => $draw,
        'recordsTotal'  =>  count($locations),
        "recordsFiltered"  => count($locations)
    ];

    wp_send_json($data);

    exit();
}