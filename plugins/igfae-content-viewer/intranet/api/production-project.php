<?php
/** EXPORT */
add_action('admin_action_export_production_projects', 'export_production_projects');
function export_production_projects()
{
    $headers = [
        __("ID"), __("Type activity"), __("Title EN"), __("Title ES"), __("Title GL"), __("Journal"), __("Scopus reference"), __("Wos reference"), __("Inspire reference"), __("Article number"),
        __("Acceptance date"), __("Publication date"), __("Thesis reading date"), __("Thesis type"), __("Thesis reading place"), __("Qualification"),  __("International mention"), __("Thesis link"), __("DOI"), __("ISBN"),
        __("Keywords"), __("Thesis director"), __("External director"), __("Journal editorial"), __("Citation"), __("Volume"), __("Chapter of the book"), __("Start page"), __("End page"), __("Number of pages"),
        __("Number of external authors"), __("Link production project"), __("Link to article"), __("SPI Index"), __("Internation co authorship"), __("Abstract"), __("Comments"), __("Validated"), __("Validated by"), __("Validated on"), __("Published"), __("Published by"), __("Published on"), __("Authors"),
        __("External authors"), __("Research group"), __("Research area"), __("Research program"), __("Research project"), __("International consortium")
    ];

    //como el filtro es el mismo que la búsqueda aprovechamos la parte de obtener el POST
    $productionProject = getSearchRequestModel();

    $projects = fetchProductionProjectExport($productionProject);

    //Funcion de mapeo. Aqui mapeamos las filas como necesitemos
    array_walk($projects, function (&$v, $k) {
        $v = get_object_vars($v);
        if (!empty($v)) {          

            if (isset($v['InternationalMention'])) {
                $v['InternationalMention'] = $v['InternationalMention'] == 1 ? __("Yes") : __("No"); 
            }
            if (isset($v['InternationalCoAuthorship'])) {
                $v['InternationalCoAuthorship'] = $v['InternationalCoAuthorship'] == 1 ? __("Yes") : __("No"); 
            }

            $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
            $v['Published'] = $v['Published'] == 1 ? __("Yes") : __("No");
            $v['ThesisReadDate'] = isset($v['ThesisReadDate']) ? formatDate($v['ThesisReadDate']) : "";
            $v['Date'] = isset($v['Date']) ? formatDate($v['Date']) : "";
            $v['ValidatedOn'] = isset($v['ValidatedOn']) ? formatDateWithHour($v['ValidatedOn']) : "";
            $v['PublishedOn'] = isset($v['PublishedOn']) ? formatDateWithHour($v['PublishedOn']) : "";
        }
    });
    if (!isset($projects)) {
        $projects = [];
    }
    //Al array de resultados le añadimos la fila de cabeceras al inicio
    array_unshift($projects, $headers);

    //llamamos a la función común para exportar
    igfae_export($projects, __('ProductionProjects'));
}

add_action("wp_ajax_search_production_projects_submit", "api_search_production_projects_submit");
add_action("wp_ajax_nopriv_search_production_projects_submit", "api_search_production_projects_submit");
function api_search_production_projects_submit()
{
    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $productionProject = getSearchRequestModel();

    $info = fetchProductionProject($productionProject, $offset, $length, $order);

    $data = [
        "data" => $info['projects'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

add_action("wp_ajax_new_production_projects_submit", "api_new_production_projects_submit");
add_action("wp_ajax_nopriv_new_production_projects_submit", "api_new_production_projects_submit");
function api_new_production_projects_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();

    global $current_user;
    // Id Project
    $idProject = trim(filter_var($_POST['idProject'], FILTER_SANITIZE_STRING));

    //Type activity select
    $typeActivity         = NULL;
    if (isset($_POST['typeActivity'])) {
        $typeActivity     = trim(filter_var($_POST['typeActivity'], FILTER_SANITIZE_STRING));
    }
    // Title
    $titleES                 = trim(filter_var($_POST['title_es'], FILTER_SANITIZE_STRING));

    // Title
    $titleEN                 = trim(filter_var($_POST['title_en'], FILTER_SANITIZE_STRING));
    // Title
    $titleGL                 = trim(filter_var($_POST['title_gl'], FILTER_SANITIZE_STRING));

    //journal select
    $journal         = NULL;
    if (isset($_POST['journal']) && !empty($_POST['journal'])) {
        $journal     = trim(filter_var($_POST['journal'], FILTER_SANITIZE_STRING));
    }
    // Research INSPIRE input
    $inspire                 = NULL;
    if (isset($_POST['inspire']) && !empty($_POST['inspire'])) {
        $inspire                 = trim(filter_var($_POST['inspire'], FILTER_SANITIZE_STRING));
    }

    // Article number input
    $articleNumber    = NULL;
    if (isset($_POST['artNumber']) && !empty($_POST['artNumber'])) {
        $articleNumber    = trim(filter_var($_POST['artNumber'], FILTER_SANITIZE_STRING));
    }

    // Acceptance date input
    $acceptanceDate    = NULL;
    if (isset($_POST['acceptanceDate']) && !empty($_POST['acceptanceDate'])) {
        $acceptanceDate    = trim(filter_var($_POST['acceptanceDate'], FILTER_SANITIZE_STRING));
    }

    // Publication date input
    $publicationDate    = NULL;
    if (isset($_POST['publicationDate']) && !empty($_POST['publicationDate'])) {
        $publicationDate    = trim(filter_var($_POST['publicationDate'], FILTER_SANITIZE_STRING));
    }

    // Thesis Reading Date input
    $thesisReadingDate    = NULL;
    if (isset($_POST['thesisReadingDate']) && !empty($_POST['thesisReadingDate'])) {
        $thesisReadingDate    = trim(filter_var($_POST['thesisReadingDate'], FILTER_SANITIZE_STRING));
    }

    //Thesis type select
    $thesisType         = NULL;
    if (isset($_POST['thesisType']) && !empty($_POST['thesisType'])) {
        $thesisType     = trim(filter_var($_POST['thesisType'], FILTER_SANITIZE_STRING));
    }

    // Thesis Reading Place input
    $thesisReadingPlace    = NULL;
    if (isset($_POST['thesisReadingPlace']) && !empty($_POST['thesisReadingPlace'])) {
        $thesisReadingPlace    = trim(filter_var($_POST['thesisReadingPlace'], FILTER_SANITIZE_STRING));
    }

    //Qualification select multiple
    $qualification    = NULL;
    if (isset($_POST['qualification']) && !empty($_POST['qualification'])) {
        $qualification         = [];
        foreach ($_POST['qualification'] as $item) {
            $qualification[] = $item;
        }
    }
    //International Menthion select
    $internationalMention         = null;
    if (isset($_POST['internationalMention']) && !empty($_POST['internationalMention'])) {
        if ($_POST['internationalMention'] == __("Yes")) {
            $internationalMention = 1;
        } else {
            $internationalMention = 0;
        }
    }

    // DOI input
    $doi         = NULL;
    if (isset($_POST['doi']) && !empty($_POST['doi']) && trim($_POST['doi']) != "" ) {        
        $doi      = trim(filter_var($_POST['doi'], FILTER_SANITIZE_STRING));
    }

    // ISBN input
    $isbn         = NULL;
    if (isset($_POST['isbn']) && !empty($_POST['isbn'])) {
        $isbn      = trim(filter_var($_POST['isbn'], FILTER_SANITIZE_STRING));
    }

    //Keywords select multiple
    $keywords         = NULL;
    if (isset($_POST['keywords']) && !empty($_POST['keywords'])) {
        $keywords         = [];
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = $item;
        }
    }
    // SPI Indexing input
    $spiIndexing         = NULL;
    if (isset($_POST['spiIndex']) && !empty($_POST['spiIndex'])) {
        $spiIndexing      = trim(filter_var($_POST['spiIndex'], FILTER_SANITIZE_STRING));
    }

    //authors select multiple
    $authors         = NULL;
    if (isset($_POST['author']) && !empty($_POST['author'])) {
        $authors         = [];
        foreach ($_POST['author'] as $item) {
            $authors[] = $item;
        }
    }
    //Thesis director select multiple
    $thesisDirector         = NULL;
    if (isset($_POST['thesisDirector']) && !empty($_POST['thesisDirector'])) {
        $thesisDirector         = [];
        foreach ($_POST['thesisDirector'] as $item) {
            $thesisDirector[] = $item;
        }
    }

    //External author select multiple
    $externalAuthor         = NULL;
    if (isset($_POST['strExternalAuthor']) && !empty($_POST['strExternalAuthor'])) {
        $itemsArr = explode(";", $_POST['strExternalAuthor']);
        $externalAuthor         = [];
        foreach ($itemsArr as $item) {
            $externalAuthor[] = $item;
        }
    }

    // External Thesis Director input
    $externalThesisDirector         = NULL;
    if (isset($_POST['externalThesisDirector']) && !empty($_POST['externalThesisDirector'])) {
        $externalThesisDirector         = [];
        foreach ($_POST['externalThesisDirector'] as $item) {
            $externalThesisDirector[] = $item;
        }
    }

    // Journal editorial table
    $journalEditorialTable         = NULL;
    $itemsTable = NULL;
    if (isset($_POST['tableUserEditorial']) && !empty($_POST['tableUserEditorial'])) {
        $journalEditorialTable         = [];
        foreach ($_POST['tableUserEditorial'] as $item) {
            $itemsTable = explode(",", $item);
            foreach ($itemsTable as $itemEditorial) {
                $journalEditorialTable[] = $itemEditorial;
            }
        }
    }
    // Citation input
    $citation         = NULL;
    if (isset($_POST['citation']) && !empty($_POST['citation']) && !empty(strip_tags($_POST['citation']))) {
        $textCitation = str_replace('\"', '"', $_POST["citation"]); 
        $citation = $purifier->purify(trim($textCitation));
    }
    
    // Link Article input
    $linkArticle         = NULL;
    if (isset($_POST['linkArticle']) && !empty($_POST['linkArticle'])) {
        $linkArticle      = trim(filter_var($_POST['linkArticle'], FILTER_SANITIZE_STRING));
    }
    // Link Thesis certificate input
    $linkThesisCertificate         = NULL;
    if (isset($_POST['linkThesisCertificate']) && !empty($_POST['linkThesisCertificate'])) {
        $linkThesisCertificate      = trim(filter_var($_POST['linkThesisCertificate'], FILTER_SANITIZE_STRING));
    }
    // Volume input
    $volume         = NULL;
    if (isset($_POST['volume']) && !empty($_POST['volume'])) {
        $volume      = trim(filter_var($_POST['volume'], FILTER_SANITIZE_STRING));
    }

    // Chapter book input
    $chapterBook         = NULL;
    if (isset($_POST['chapterBook']) && !empty($_POST['chapterBook'])) {
        $chapterBook      = formatNumberToBD(trim(filter_var($_POST['chapterBook'], FILTER_SANITIZE_STRING)));
    }
    // Home page input
    $homepage         = NULL;
    if (isset($_POST['homepage']) && !empty($_POST['homepage'])) {
        $homepage      = trim(filter_var($_POST['homepage'], FILTER_SANITIZE_STRING));
    }
    // Final Page input
    $finalPage         = NULL;
    if (isset($_POST['finalPage']) && !empty($_POST['finalPage'])) {
        $finalPage      = formatNumberToBD(trim(filter_var($_POST['finalPage'], FILTER_SANITIZE_STRING)));
    }
    // Number Pages input
    $numberPages         = NULL;
    if (isset($_POST['numberPages']) && !empty($_POST['numberPages'])) {
        $numberPages      = formatNumberToBD(trim(filter_var($_POST['numberPages'], FILTER_SANITIZE_STRING)));
    }

    //Research Group select multiple
    $researchGroup         = NULL;
    if (isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])) {
        $researchGroup         = [];
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
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
    //Research Program select
    $collaborationExperiment         = NULL;
    if (isset($_POST['collaborationExperiment']) && !empty($_POST['collaborationExperiment'])) {
        $collaborationExperiment     = trim(filter_var($_POST['collaborationExperiment'], FILTER_SANITIZE_STRING));
    }
    //Research Project select multiple
    $researchProject         = NULL;
    if (isset($_POST['researchProject']) && !empty($_POST['researchProject'])) {
        $researchProject        = [];
        foreach ($_POST['researchProject'] as $item) {
            $researchProject[] = $item;
        }
    }
    // Abstract textarea
    $abstract         = NULL;
    if (isset($_POST['abstract']) && !empty($_POST['abstract']) && trim($_POST['abstract']) != "" && !empty(strip_tags($_POST['abstract']))) {
        $textAbstract = str_replace('\"', '"', $_POST["abstract"]); 
        $abstract = $purifier->purify(trim($textAbstract));
    }
    // Vinculacion
    $linkProductionProject         = NULL;
    if (isset($_POST['linkProductionProject']) && !empty($_POST['linkProductionProject'])) {
        $linkProductionProject     = trim(filter_var($_POST['linkProductionProject'], FILTER_SANITIZE_STRING));
    }
    // Comments textarea
    $comments         = NULL;
    if (isset($_POST['comments']) && !empty($_POST['comments']) && trim($_POST['comments']) != "" && !empty(strip_tags($_POST['comments']))) {
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
    //InternationalCoAuthorship
    $internationalCoAuthorship         = null;
    if (isset($_POST['internationalCoAuthorship']) && !empty($_POST['internationalCoAuthorship'])) {
        if ($_POST['internationalCoAuthorship'] == "1") {
            $internationalCoAuthorship = 1;
        } else {
            $internationalCoAuthorship = 0;
        }
    }
   
    $productionProject = new IntranetProductionProject(
        $idProject,
        $typeActivity,
        NULL,
        $titleES,
        $titleEN,
        $titleGL,
        $researchArea,
        $researchProgram,
        $collaborationExperiment,
        $researchGroup,
        $journal,
        $articleNumber,
        $spiIndexing,
        $doi,
        $thesisType,
        $internationalMention,
        $keywords,
        $authors,
        $thesisDirector,
        $externalAuthor,
        $externalThesisDirector,
        $journalEditorialTable,
        $citation,
        $validated,
        $published,
        // Campos new
        $inspire,
        $acceptanceDate,
        $publicationDate,
        $thesisReadingDate,
        $thesisReadingPlace,
        $qualification,
        $isbn,
        $linkArticle,
        $linkThesisCertificate,
        $volume,
        $chapterBook,
        $homepage,
        $finalPage,
        $numberPages,
        NULL, // numberAuthors
        NULL, // numberAuthorsigfae
        $abstract,
        $linkProductionProject,
        $comments,
        $researchProject,
        $publishedBy,
        $publishedOn,
        $validatedBy,
        $validatedOn,
        $internationalCoAuthorship,
        NULL,
        NULL
    );

    // COMPROBAMOS QUE EL DOI SEA ÚNICO
    if ($productionProject->getDoi() != null && getProductionProjectByDOI($productionProject) > 0) {
        wp_send_json_error(new WP_Error('-1', __('DOI must be unique')));
    } else if (!verificarValidarInformacion($validated, $published)) {
        // No se permite publicar sin haber validado previamente
        wp_send_json_error(new WP_Error('-1', __('In order to publish, the information must first be validated')));
    } else {
        // $idProject-> id del proyecto a editar, $idProjectSave-> id del nuevo proyecto
        $idProjectSave = saveProductionProject($productionProject);
        $return = array(
            'idProject' => $idProject,
            'idProjectSave'      => $idProjectSave
        );
        wp_send_json_success($return);
    }

    exit();
}
// Botón Edit de la pantalla del detalle de proyecto
add_action("wp_ajax_edit_production_projects_submit", "api_edit_production_projects_submit");
add_action("wp_ajax_nopriv_edit_production_projects_submit", "api_edit_production_projects_submit");
function api_edit_production_projects_submit()
{

    exit();
}

// Botón Delete proyect
add_action("wp_ajax_delete_production_projects_submit", "api_delete_production_projects_submit");
add_action("wp_ajax_nopriv_delete_production_projects_submit", "api_delete_production_projects_submit");
function api_delete_production_projects_submit()
{
    $id = $_POST['id'];
    // COMPROBAMOS QUE EL PROYECTO NO ESTÉ REFERENCIADO EN OTRO PROYECTO
    if (getLinkProductionProject($id) > 0) {
        wp_send_json_error(new WP_Error('-1', __('The production project cannot be removed because it is referenced in another project')));
    } else {
        // Eliminamos el proyecto
        deleteProductionProject($id);
        wp_send_json_success();
    }

    exit();
}

/**
 * Get qualifications
 */
function getQualifications()
{
    global $wp_query;
    $qualifications = getSelectQualifications();
    $qualification = "";
    $qualificationSelect = NULL;
    $qualificationSelect = NULL;

    if (isset($wp_query->query_vars['qualification'])) {
        $qualification = sanitize_title(urldecode($wp_query->query_vars['qualification']));
    }
    foreach ($qualifications as $key => $item) {
        $qualificationSelect[$key] = $item;
        if ($qualification == sanitize_title($item->getName())) {
            $qualificationSelected[$key] = $item;
        }
    }
    $qualificationSelect['items'] = $qualificationSelect;
    $qualificationSelect['selected'] = $qualificationSelect;
    return $qualificationSelect;
}

/** Get search request*/

function getSearchRequestModel()
{
    //Type activity select multiple
    $typeActivity         = [];
    if (isset($_POST['typeActivity'])) {       
        foreach ($_POST['typeActivity'] as $item) {           
            $typeActivity[] = $item;
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

    //Experiment select multiple
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

    //journal select multiple
    $journal         = [];
    if (isset($_POST['journal'])) {
        foreach ($_POST['journal'] as $item) {
            $journal[] = $item;
        }
    }

    // International coauthorship multiple
    $internationalCoAuthorship         = [];
    if (isset($_POST['internationalCoAuthorship'])) {
        foreach ($_POST['internationalCoAuthorship'] as $item) {
            $internationalCoAuthorship[] = $item;
        }
    }

    // Article number input
    $articleNumber    = trim(filter_var($_POST['articleNumber'], FILTER_SANITIZE_STRING));
    // SPI Indexing input
    $spiIndexing      = trim(filter_var($_POST['spiIndixing'], FILTER_SANITIZE_STRING));
    // DOI input
    $doi      = trim(filter_var($_POST['doi'], FILTER_SANITIZE_STRING));

    //Thesis type select multiple
    $thesisType         = [];
    if (isset($_POST['thesisType'])) {
        foreach ($_POST['thesisType'] as $item) {
            $thesisType[] = $item;
        }
    }

    //International Menthion select
    $internationalMention         = NULL;
    if (isset($_POST['internationalMention'])) {
        $internationalMention     = trim(filter_var($_POST['internationalMention'], FILTER_SANITIZE_STRING));
    }
    //Keywords select multiple
    $keywords         = [];
    if (isset($_POST['keywords'])) {
        foreach ($_POST['keywords'] as $item) {
            $keywords[] = $item;
        }
    }
    //authors select multiple
    $authors         = [];
    if (isset($_POST['author'])) {
        foreach ($_POST['author'] as $item) {
            $authors[] = $item;
        }
    }

    //Thesis director select multiple
    $thesisDirector         = [];
    if (isset($_POST['thesisDirector'])) {
        foreach ($_POST['thesisDirector'] as $item) {
            $thesisDirector[] = $item;
        }
    }

    //External author select multiple
    $externalAuthor         = [];
    if (isset($_POST['externalAuthor'])) {
        foreach ($_POST['externalAuthor'] as $item) {
            $externalAuthor[] = $item;
        }
    }

    //External thesis director select multiple
    $externalThesisDirector         = [];
    if (isset($_POST['externalThesisDirector'])) {
        foreach ($_POST['externalThesisDirector'] as $item) {
            $externalThesisDirector[] = $item;
        }
    }

    //journal editorial select
    $journalEditorial         = [];
    if (isset($_POST['magazineEditorial'])) {
        foreach ($_POST['magazineEditorial'] as $item) {
            $journalEditorial[] = $item;
        }
    }
    // Citation input
    $citation      = trim(filter_var($_POST['citation'], FILTER_SANITIZE_STRING));

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

    // Orcid
    $orcid    = trim(filter_var($_POST['orcid'], FILTER_SANITIZE_STRING));

    $productionProject = new IntranetProductionProject(
        NULL,
        $typeActivity,
        $title,
        NULL,
        NULL,
        NULL,
        $researchArea,
        $researchProgram,
        $collaborationExperiment,
        $researchGroup,
        $journal,
        $articleNumber,
        $spiIndexing,
        $doi,
        $thesisType,
        $internationalMention,
        $keywords,
        $authors,
        $thesisDirector,
        $externalAuthor,
        $externalThesisDirector,
        $journalEditorial,
        $citation,
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
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        $internationalCoAuthorship,
        NULL,
        $orcid
    );

    return $productionProject;
}