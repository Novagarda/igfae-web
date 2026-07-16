<?php


if (!function_exists("ABKitLoadTemplate")) {
    function ABKitLoadTemplate($template_path, $attrs = null, $once = false)
    {
        if (file_exists($template_path)) {
            if ($attrs) {
                extract($attrs);
            }
            if ($once) {
                include_once($template_path);
            } else {
                include($template_path);
            }
        }
    }
}

function igfae_get_template_part($slug, $name = "", $attrs = null)
{
    if ($name) {
        $slug = "templates/{$slug}-{$name}.php";
    } else {
        $slug = "templates/{$slug}.php";
    }
    $path = igfae_get_path($slug);
    //$template = include($path); //locate_template([$path]);

    ABKitLoadTemplate($path, $attrs);
}

function igfae_get_path($subpath = "")
{
    return IGFAE_PATH . $subpath;
}

function igfae_fetch_data($sql, $params = [], $page = NULL, $limit = 20)
{
    // Berio: esta constante daba erro. Comentamos esta liña mais o bloque da liña 63
    //$groupconcatlen = MYSQL_GROUP_CONCAT_LEN;

    if ($mydb = new wpdb(igfae_get_db_user(), igfae_get_db_pass(), igfae_get_db_name(), igfae_get_db_host())) {

        $mydb->show_errors();

        /* 
        if ( !is_null($page) ) {
            $offset = ($page - 1) * $limit;
            $sql .= "LIMIT {$limit} OFFSET {$offset}";
        }
        */

        if ( strpos( $sql, '%' ) !== false ) {
            $sql = $mydb->prepare($sql, $params);
        }

        /*
        $logInfo .= "[".date("d/m/Y - h:i:s")."] SQL: ".print_r($sql, true)."\n";
        //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
        */
        /*
        if ( $groupconcatlen > 0) {
            $sqllen=$mydb->prepare('set session group_concat_max_len=%d', $groupconcatlen);
            $mydb->query($sqllen);
        }
        */
        $rows = $mydb->get_results($sql);
        if ( $rows === null || !empty($mydb->last_error) ) {
            $result = [
                'rows'  => [],
                'count' => 0
            ];
        } else {
            $result = [
                'rows'  => $rows,
                'count' => $mydb->num_rows
            ];
        }

        $mydb->close();
        /*
        $logInfo .= "[".date("d/m/Y - h:i:s")."] Result: ".print_r($result, true)."\n";
        //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
        */
    } else {
        $result = [
            'rows'  => [],
            'count' => 0
        ];
    }
    return $result;
}


function igfae_intranet_fetch_data($sql, $params = [], $page = NULL, $limit = 20)
{

    if ($mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host())) {

        $mydb->show_errors();

        /* 
        if ( !is_null($page) ) {
            $offset = ($page - 1) * $limit;
            $sql .= "LIMIT {$limit} OFFSET {$offset}";
        }
        */

        if ( strpos( $sql, '%' ) !== false ) {
            $sql = $mydb->prepare($sql, $params);
        }

        /*
        $logInfo .= "[".date("d/m/Y - h:i:s")."] SQL: ".print_r($sql, true)."\n";
        //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
        */
        $rows = $mydb->get_results($sql);
        if ( $rows === null || !empty($mydb->last_error) ) {
            $result = [
                'rows'  => [],
                'count' => 0
            ];
        } else {
            $result = [
                'rows'  => $rows,
                'count' => $mydb->num_rows
            ];
        }

        $mydb->close();
        /*
        $logInfo .= "[".date("d/m/Y - h:i:s")."] Result: ".print_r($result, true)."\n";
        //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
        */
    } else {
        $result = [
            'rows'  => [],
            'count' => 0
        ];
    }
    return $result;
}

function getPaginationInfo($page, $amount, $sql)
{
    $result = null;

    if (!is_null($page)) {

        $rows = igfae_fetch_data($sql);

        $result = [
            'total'         => $rows['count'],
            'amount'        => $amount,
            'actualPage'    => $page,
            'countPages'    => intval(ceil($rows['count'] / $amount))
        ];
    }

    return $result;
}

function paginate($list, $page = 0, $amount = 10)
{
    $result = NULL;

    if (is_array($list)) {
        $result = array_slice($list, $page * $amount, $amount);
    }

    return $result;
}


function paginateMergedArray($array, $page, $amount, $type = 'stdClass', $order = 'DESC')
{
    $result = [];
    $compareFunc = 'compareDates';

    if ($order == 'ASC') {
        $compareFunc = 'compareDatesInv';
    }

    if (is_array($array) && (count($array) > 0)) {
        $merged = array_merge(...array_filter($array));
        $res = usort($merged, [$type, $compareFunc]);
        /*
        $logInfo .= "[".date("d/m/Y - h:i:s")."] Result: ".print_r($res, true)."\n";
        //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
        if (!is_null($page)) {
            $result['rows'] = paginate($merged, $page, $amount);

            $result['pagination'] = [
                'total'         => count($merged),
                'amount'        => $amount,
                'actualPage'    => $page,
                'countPages'    => intval(ceil(count($merged) / $amount))
            ];
        } else {
            $result['rows'] = $merged;
        }
    }

    return $result;
}

function igfae_intranet_save($sql, $params = [], $page = NULL, $limit = 20)
{
    $isTransaction = false;
    global $mybdTransaction;
    if (empty($mybdTransaction)){
        $mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host());
    } else {
        $isTransaction = true;
        $mydb = $mybdTransaction;
    }

    
    if (!empty($mydb)) {

        $mydb->show_errors();

        if ( strpos( $sql, '%' ) !== false ) {
            $sql = $mydb->prepare($sql, $params);
        }
        $mydb->query($sql);

        if (!$isTransaction) {
            $mydb->close();
        } else {
            igfae_intranet_check_query_error($mydb);
        }
        
        return $mydb->insert_id;
    }
}

function igfae_intranet_setSession($sql)
{
    $isTransaction = false;
    global $mybdTransaction;
    if (empty($mybdTransaction)){
        $mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host());
    } else {
        $isTransaction = true;
        $mydb = $mybdTransaction;
    }

    if (!empty($mydb)) {

        $mydb->show_errors();
        $mydb->query( $sql);
        //mysqli_query($mydb, $sql);
        if (!$isTransaction) {
            $mydb->close();
        } else {
            igfae_intranet_check_query_error($mydb);
        }
    }
}

function igfae_intranet_insert($table, $data, $format = null)
{
    $isTransaction = false;
    global $mybdTransaction;
    if (empty($mybdTransaction)){        
        $mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host());
    } else {
        $isTransaction = true;
        $mydb = $mybdTransaction;
    }

    if (!empty($mydb)) {

        $mydb->show_errors();

        $mydb->insert($table,$data,$format);

        if (!$isTransaction) {
            $mydb->close();
        } else {
            igfae_intranet_check_query_error($mydb);
        }
        
        return $mydb->insert_id;
    }
}

function igfae_intranet_update($table, $data, $where, $format = null, $where_format = null)
{
    $isTransaction = false;
    global $mybdTransaction;
    if (empty($mybdTransaction)){
        $mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host());
    } else {
        $isTransaction = true;
        $mydb = $mybdTransaction;
    }

    if (!empty($mydb)) {

        $mydb->show_errors();

        $mydb->update($table,$data,$where,$format,$where_format);

        if (!$isTransaction) {
            $mydb->close();
        } else {
            igfae_intranet_check_query_error($mydb);
        }
    }
}

/**
 * Ejecuta una query y devulve directamente una variable como resultado de la query
 * Se usa por ejemplo para querys de tipo count
 * @param string       $query  
 */
function igfae_intranet_get_var($query)
{
    $mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host());

    if (!empty($mydb)) {
        $result = $mydb->get_var($query);
        $mydb->close();
        return $result;
    }

    return null;
}

/**
 * Realiza el borrado de registros de una tabla dada
 * 
 * @param string       $table        Table name
 * @param array        $where        A named array of WHERE clauses (in column => value pairs).
 *                                   Multiple clauses will be joined with ANDs.
 *                                   Both $where columns and $where values should be "raw".
 *                                   Sending a null value will create an IS NULL comparison - the corresponding format will be ignored in this case.
 * @param array|string $where_format Optional. An array of formats to be mapped to each of the values in $where.
 *                                   If string, that format will be used for all of the items in $where.
 *                                   A format is one of '%d', '%f', '%s' (integer, float, string).
 *                                   If omitted, all values in $where will be treated as strings unless otherwise specified in wpdb::$field_types.
 * @return int|false The number of rows updated, or false on error.
 */
function igfae_intranet_delete($table, $where, $format = null)
{

    global $mybdTransaction;
    
    if (empty($mybdTransaction)){
        $mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host());
    } else {
        $isTransaction = true;
        $mydb = $mybdTransaction;
    }

    if (!empty($mydb)) {

        $mydb->show_errors();

        $deleted = $mydb->delete($table, $where, $format);

        if (!$isTransaction) {
            $mydb->close();
        } else {
            igfae_intranet_check_query_error($mydb);
        }
        
        return $deleted;
    }
}

//TRANSACIONES

function igfae_intranet_begin_transaction()
{
    global $mybdTransaction;
    global $igfaeIntranetErrorTransaction;
    $igfaeIntranetErrorTransaction = false;
    $mybdTransaction = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host());
    if (!empty($mybdTransaction)) {
        $mybdTransaction->query('START TRANSACTION;');
    }
}

function igfae_intranet_commit_transaction()
{
    global $mybdTransaction;
    $mybdTransaction->query('COMMIT;');
}


function igfae_intranet_rollback_transaction()
{
    global $mybdTransaction;
    $mybdTransaction->query('ROLLBACK;');
}

function igfae_intranet_check_query_error($query)
{
    global $igfaeIntranetErrorTransaction;
    $igfaeIntranetErrorTransaction = $igfaeIntranetErrorTransaction || !empty($query->last_error);
}

function igfae_intranet_is_transaction_ok()
{
    global $mybdTransaction;
    global $igfaeIntranetErrorTransaction;
    if ($igfaeIntranetErrorTransaction) {
        return false;
    } else {
        return true;
    }
}


/**
 * Guarda un fichero en la ruta pasada por parametro
 */
function igfae_intranet_create_file($file,$filepath,$docs=null)
{
    $fileType = pathinfo($file['name'],PATHINFO_EXTENSION);
    $fileType = strtolower($fileType);
    $path = $filepath . '.' . $fileType;	

    if( !isset($docs) && !in_array(strtolower($fileType), IGFAE_INTRANET_IMAGE_FILE_EXTENSIONS) ) {
        return null;
    }else if(!in_array(strtolower($fileType), IGFAE_INTRANET_DOCS_FILE_EXTENSIONS) ){
        return null;
    }

    //comprobamos si existe el fichero
    $i= 1;		
    while (file_exists($path))
    {
        $path = $filepath . '-' . $i . '.' . $fileType;        
        $i++;
    }

    if(move_uploaded_file($file['tmp_name'],$path)){
        return $path;
    }else{
        return null;
    }  
}

/**
 * Borra el fichero pasado por parámetro
 */
function igfae_intranet_delete_file($filepath)
{
    if (file_exists($filepath)) {
        unlink($filepath);
    }
}


/**
 * Ejecuta un Script SQL que tiene N sentencias y la última de ellas es la SELECT que devuelve los datos solicitados
 * 
 * @param $sqlArray Array de string con las sentencias
 * @param $paramsArray Array de array con los parametros de cada sentencia
 * 
 * Las longitudes de $sqlArray y $paramsArray deben ser exactamente las mismas
 */
function igfae_intranet_execute_script($sqlArray, $paramsArray = [[]])
{
    if (sizeof($sqlArray) > 0 && sizeof($sqlArray) == sizeof($paramsArray) && $mydb = new wpdb(igfae_get_db_intranet_user(), igfae_get_db_intranet_pass(), igfae_get_db_intranet_name(), igfae_get_db_intranet_host())) {

        $mydb->show_errors();

        // Ejecucion del script SQL
        for($i = 0; $i < sizeof($sqlArray)-1; ++$i) {
            $sql = $sqlArray[$i];
            $params = $paramsArray[$i];
            if ( strpos( $sql, '%' ) !== false ) {
                $sql = $mydb->prepare($sql, $params);
            }
    
            $mydb->query($sql);
        }

        // Ejecucion de la ultima sentencia SELECT
        for($i = sizeof($sqlArray)-1; $i < sizeof($sqlArray); ++$i) {
            $sql = $sqlArray[$i];
            $params = $paramsArray[$i];

            if ( strpos( $sql, '%' ) !== false ) {
                $sql = $mydb->prepare($sql, $params);
            }
    
            $rows = $mydb->get_results($sql);
            if ( $rows === null || !empty($mydb->last_error) ) {
                $result = [
                    'rows'  => [],
                    'count' => 0
                ];
            } else {
                $result = [
                    'rows'  => $rows,
                    'count' => $mydb->num_rows
                ];
            }
        }

        $mydb->close();
    } else {
        $result = [
            'rows'  => [],
            'count' => 0
        ];
    }
    return $result;
}
