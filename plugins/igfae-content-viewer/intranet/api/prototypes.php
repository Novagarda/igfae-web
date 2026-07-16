<?php
add_action("wp_ajax_search_prototypes_submit", "api_search_prototypes_submit");
add_action("wp_ajax_nopriv_search_prototypes_submit", "api_search_prototypes_submit");
function api_search_prototypes_submit()
{

    $draw = isset($_POST['draw']) ? trim(filter_var($_POST['draw'], FILTER_SANITIZE_STRING)) : 0;
    $length = isset($_POST['length']) ? trim(filter_var($_POST['length'], FILTER_SANITIZE_STRING)) : 10;
    $offset = isset($_POST['start']) ? trim(filter_var($_POST['start'], FILTER_SANITIZE_STRING)) : 0;
    $order = isset($_POST['order']) ? $_POST['order'] : null;

    $prototype = getPrototypeRequestModel();

    $info = fetchPrototypes($prototype, $offset, $length, $order);

    $data = [
        "data" => $info['prototypes'],
        "draw" => $draw,
        "recordsTotal"  =>  $info['pagination']['recordsTotal'],
        "recordsFiltered"  => $info['pagination']['recordsFiltered']
    ];

    wp_send_json($data);

    exit();
}

function getPrototypeRequestModel(){

      global $current_user;
      // start date
      $startDate = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));

      // end date
      $endDate = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));
  
      //state select multiple
      $state         = [];
      if (isset($_POST['state'])) {
          foreach ($_POST['state'] as $item) {
              $state[] = $item;
          }
      }
  
      // name text
      $name  = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
  
      //publication Date
      $publicationDate = trim(filter_var($_POST['publicationDate'], FILTER_SANITIZE_STRING));
      
      // type text
      $type  = trim(filter_var($_POST['type'], FILTER_SANITIZE_STRING));
  
      //igfae Researchers select multiple
      $igfaeResearchers         = [];
      if (isset($_POST['igfaeResearchers'])) {
          foreach ($_POST['igfaeResearchers'] as $item) {
              $igfaeResearchers[] = $item;
          }
      }
  
      //Research Program select multiple
      $researchProgram         = [];
      if (isset($_POST['researchProgram'])) {
          foreach ($_POST['researchProgram'] as $item) {
              $researchProgram[] = $item;
          }
      }
  
      //Research Area select multiple
      $researchArea         = [];
      if (isset($_POST['researchArea'])) {
          foreach ($_POST['researchArea'] as $item) {
              $researchArea[] = $item;
          }
      }
  
      //Experimental collaboration select multiple
      $experimentalCollaboration         = [];
      if (isset($_POST['experimentalCollaboration'])) {
          foreach ($_POST['experimentalCollaboration'] as $item) {
              $experimentalCollaboration[] = $item;
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
      if (isset($_POST['workline'])) {
          foreach ($_POST['workline'] as $item) {
              $workline[] = $item;
          }
      }
  
      //workline select multiple
      $iPCoordinators         = [];
      if (isset($_POST['iPCoordinators'])) {
          foreach ($_POST['iPCoordinators'] as $item) {
              $iPCoordinators[] = $item;
          }
      }
  
      //validated select
      $validated         = NULL;
      $validatedOn       = NULL;
      $validatedBy       = NULL;
      if (isset($_POST['validated'])) {
          $validated     = trim(filter_var($_POST['validated'], FILTER_SANITIZE_STRING));
          $validatedBy = $current_user->user_login;
          $validatedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
      }
  
      $prototype = new IntranetPrototype(
          NULL,
          $startDate,
          $endDate,
          $state,
          $name,
          NULL,
          NULL,
          NULL,
          $publicationDate,
          $type,
          $igfaeResearchers,
          $researchProgram,
          $researchGroup,
          $researchArea,
          $experimentalCollaboration,
          $workline,
          $iPCoordinators,
          $validated,
          $validatedBy,
          $validatedOn,
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
          NULL
      );

      return $prototype;
}

// Delete prototype
add_action("wp_ajax_delete_prototypes_submit", "api_delete_prototypes_submit");
add_action("wp_ajax_nopriv_delete_prototypes_submit", "api_delete_prototypes_submit");
function api_delete_prototypes_submit()
{
    $id = $_POST['id'];

    // delete prototype
    deletePrototype($id);
    wp_send_json_success();

    exit();
}

add_action("wp_ajax_new_prototype_submit", "api_new_prototype_submit");
add_action("wp_ajax_nopriv_new_prototype_submit", "api_new_prototype_submit");
function api_new_prototype_submit()
{
    require_once( IGFAE_PATH . 'lib/vendor/htmlpurifier/library/HTMLPurifier.auto.php' );
    $purifier = new HTMLPurifier();

    global $current_user;

    // Id Prototype
    $idPrototype = NULL;
    if(isset($_POST['idPrototype'])){
      $idPrototype = trim(filter_var($_POST['idPrototype'], FILTER_SANITIZE_STRING));
    }


    // Start date
    $startDate = NULL;
    if (isset($_POST['startDate']) && !empty($_POST['startDate'])) {
        $startDate    = trim(filter_var($_POST['startDate'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($startDate);
        $startDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    // End date
    $endDate = NULL;
    if (isset($_POST['endDate']) && !empty($_POST['endDate'])) {
        $endDate    = trim(filter_var($_POST['endDate'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($endDate);
        $endDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    // Deliverables input
    $deliverables = NULL;
    if(isset($_POST['deliverables'])){
      $deliverables         = [];
        foreach ($_POST['deliverables'] as $item) {
            $deliverables[] = trim(filter_var($item, FILTER_SANITIZE_STRING));
        }
    }

    //state select
    $state         = NULL;
    if (isset($_POST['state'])) {
        $state     = trim(filter_var($_POST['state'], FILTER_SANITIZE_STRING));
    }

    // Name ES
    $nameES = NULL;
    if(isset($_POST['name_es'])){
      $nameES = trim(filter_var($_POST['name_es'], FILTER_SANITIZE_STRING));
    }

    // Name EN
    $nameEN = NULL;
    if(isset($_POST['name_en'])){
      $nameEN = trim(filter_var($_POST['name_en'], FILTER_SANITIZE_STRING));
    }

    // Name GL
    $nameGL = NULL;
    if(isset($_POST['name_gl'])){
      $nameGL = trim(filter_var($_POST['name_gl'], FILTER_SANITIZE_STRING));
    }

    // Description ES
    $descriptionES = NULL;
    if(isset($_POST['description_es'])){
      $descriptionES = trim(filter_var($_POST['description_es'], FILTER_SANITIZE_STRING));
    }

    // Description EN
    $descriptionEN = NULL;
    if(isset($_POST['description_en'])){
      $descriptionEN = trim(filter_var($_POST['description_en'], FILTER_SANITIZE_STRING));
    }

    // Description GL
    $descriptionGL = NULL;
    if(isset($_POST['description_gl'])){
      $descriptionGL = trim(filter_var($_POST['description_gl'], FILTER_SANITIZE_STRING));
    }

    // Promotional description ES
    $promotionalDescriptionES = NULL;
    if(isset($_POST['promotionalDescription_es'])){
      $promotionalDescriptionES = trim(filter_var($_POST['promotionalDescription_es'], FILTER_SANITIZE_STRING));
    }

    // Promotional description EN
    $promotionalDescriptionEN = NULL;
    if(isset($_POST['promotionalDescription_en'])){
      $promotionalDescriptionEN = trim(filter_var($_POST['promotionalDescription_en'], FILTER_SANITIZE_STRING));
    }

    // Promotional description GL
    $promotionalDescriptionGL = NULL;
    if(isset($_POST['promotionalDescription_gl'])){
      $promotionalDescriptionGL = trim(filter_var($_POST['promotionalDescription_gl'], FILTER_SANITIZE_STRING));
    }

    // Slug input
    $slug = NULL;
    if(isset($_POST['slug'])){
      $slug = trim(filter_var($_POST['slug'], FILTER_SANITIZE_STRING));
    }

    // Website input
    $website = NULL;
    if(isset($_POST['website'])){
      $website = trim(filter_var($_POST['website'], FILTER_SANITIZE_STRING));
    }

    // publication date
    $publicationDate = NULL;
    if (isset($_POST['publicationDate']) && !empty($_POST['publicationDate'])) {
        $publicationDate    = trim(filter_var($_POST['publicationDate'], FILTER_SANITIZE_STRING));
        $time_input = strtotime($publicationDate);
        $publicationDate = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D, $time_input);
    }

    // Type input
    $type = NULL;
    if(isset($_POST['type'])){
      $type         = [];
        foreach ($_POST['type'] as $item) {
            $type[] = trim(filter_var($item, FILTER_SANITIZE_STRING));
        }
    }

    // Access input
    $access = NULL;
    if(isset($_POST['access'])){
      $access = trim(filter_var($_POST['access'], FILTER_SANITIZE_STRING));
    }

    // Access Documentation input
    $accessDocumentation = NULL;
    if(isset($_POST['accessDocumentation'])){
      $accessDocumentation = trim(filter_var($_POST['accessDocumentation'], FILTER_SANITIZE_STRING));    
    }

    //IGFAE Researchers select multiple
    $IGFAEResearchers    = NULL;
    if (isset($_POST['IGFAEResearchers']) && !empty($_POST['IGFAEResearchers'])) {
        $IGFAEResearchers         = [];
        foreach ($_POST['IGFAEResearchers'] as $item) {
            $IGFAEResearchers[] = $item;
        }
    }

    // Other researchers input
    $otherResearchers = NULL;
    if(isset($_POST['otherResearchers'])){
      $otherResearchers         = [];
      foreach ($_POST['otherResearchers'] as $item) {
          $otherResearchers[] = trim(filter_var($item, FILTER_SANITIZE_STRING));
      }
    }

    // Projects input
    $projects = NULL;
    if(isset($_POST['projects'])){
      $projects         = [];
      foreach ($_POST['projects'] as $item) {
          $projects[] = trim(filter_var($item, FILTER_SANITIZE_STRING));
      }
    }
    
    // Research area select multiple
    $researchArea    = NULL;
    if (isset($_POST['researchArea']) && !empty($_POST['researchArea'])) {
        $researchArea         = [];
        foreach ($_POST['researchArea'] as $item) {
            $researchArea[] = $item;
        }
    }

    // Collaboration experiment select multiple
    $collaborationExperiment    = NULL;
    if (isset($_POST['collaborationExperiment']) && !empty($_POST['collaborationExperiment'])) {
      $collaborationExperiment = trim(filter_var($_POST['collaborationExperiment'], FILTER_SANITIZE_STRING));    
    } 

    // Research program select multiple
    $researchProgram    = NULL;
    if (isset($_POST['researchProgram']) && !empty($_POST['researchProgram'])) {
        $researchProgram         = [];
        foreach ($_POST['researchProgram'] as $item) {
            $researchProgram[] = $item;
        }
    }
    
    // Research group select multiple
    $researchGroup    = NULL;
    if (isset($_POST['researchGroup']) && !empty($_POST['researchGroup'])) {
        $researchGroup         = [];
        foreach ($_POST['researchGroup'] as $item) {
            $researchGroup[] = $item;
        }
    }  
    
    // Workline select multiple
    $workline    = NULL;
    if (isset($_POST['workline']) && !empty($_POST['workline'])) {
        $workline         = [];
        foreach ($_POST['workline'] as $item) {
            $workline[] = $item;
        }
    }  

    // Linked organizations input
    $linkedOrganizations = NULL;
    if(isset($_POST['linkedOrganizations'])){
      $linkedOrganizations         = [];
      foreach ($_POST['linkedOrganizations'] as $item) {
          $linkedOrganizations[] = trim(filter_var($item, FILTER_SANITIZE_STRING));
      }
    }

    //iPCoordinator select
    $iPCoordinator         = NULL;
    if (isset($_POST['iPCoordinator'])) {
        $iPCoordinator     = trim(filter_var($_POST['iPCoordinator'], FILTER_SANITIZE_STRING));
    }

    // Comments input
    $comments = NULL;
    if(isset($_POST['comments']) && !empty($_POST['comments']) && !empty(strip_tags($_POST['comments']))){
        $textComments = str_replace('\"', '"', $_POST["comments"]); 
        $comments = $purifier->purify(trim($textComments)); 
    }
    
    //validated select
    $validated         = NULL;
    $validatedOn       = NULL;
    $validatedBy       = NULL;
    if (isset($_POST['validated'])) {
        $validated     = trim(filter_var($_POST['validated'], FILTER_SANITIZE_STRING));
        $validatedBy = $current_user->user_login;
        $validatedOn = date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S);
    } else {
      // IMPORTANTE: Valor por defecto a 0
      //  - Cuando el campo no esta en la vista (usuario que da de alta o edita no puede validar), lo que implica:
      //    - Informacion nueva y existente: Se queda pendiente de validar
      $validated = 0;
    }

    // Delete de la imagen
    $deleteImage         = false;
    if (isset($_POST['photoPrototypeDelete']) && !empty($_POST['photoPrototypeDelete']) && $_POST['photoPrototypeDelete'] == "true") {
        $deleteImage = true;
    }

   
    $prototype = new IntranetPrototype(
      $idPrototype,
      $startDate,
      $endDate,
      $state,
      NULL,
      $nameES,
      $nameEN,
      $nameGL,
      $publicationDate,
      $type,
      $IGFAEResearchers,
      $researchProgram,
      $researchGroup,
      $researchArea,
      $collaborationExperiment,
      $workline,
      $iPCoordinator,
      $validated,
      $validatedBy,
      $validatedOn,
      NULL,
      $descriptionES,
      $descriptionEN,
      $descriptionGL,
      NULL,
      $promotionalDescriptionES,
      $promotionalDescriptionEN,
      $promotionalDescriptionGL,
      $slug,
      $website,
      $access,
      $accessDocumentation,
      NULL,
      NULL,
      $otherResearchers,
      $deliverables,
      $projects,
      $linkedOrganizations,
      $comments
    );
    $prototype->setDeleteImage($deleteImage);

    if($idPrototype == NULL){
      wp_send_json_success(saveNewPrototype($prototype));
    } else {
      wp_send_json_success(editPrototype($prototype));
    }

    exit();
}

/** EXPORT */
add_action( 'admin_action_export_prototypes', 'export_prototypes' );
function export_prototypes()
{
    $headers = [__("ID"), __("Start date"),__("End date"),__("Deliverable/s"), __("State"),__("Name english"), __("Name spanish"),
    __("Name galician"), __("Slug"), __("Description english"), __("Description spanish"), __("Description galician"),
    __("Promotional description english"), __("Promotional description spanish"), __("Promotional description galician"),
    __("Publication date"), __("Type"), __("Website"),__("Access"),__("File id"), __("Access documentation"),
    __("IGFAE researchers"), __("Other researchers"), __("Projects"), __("Research area"), __("Research program"),
    __("Research group"), __("International consortium"),  __("Activities"), __("Linked organizations"),
    __("Principal investigator coordinator"),__("Comments"), __("Validated"), __("Validated by"), __("Validated on")];
    $prototypes = []; 
    $requestPrototype = getPrototypeRequestModel();

    $idPrototypes = fetchPrototypes($requestPrototype, null, null, null, true);

    if(!empty($idPrototypes)){
      $prototypes = fetchPrototypesExport($idPrototypes);
    }

    array_walk($prototypes, function (&$v, $k) {
      $v = get_object_vars($v);
      if (!empty($v)) {
          $v['StartDate'] = isset($v['StartDate']) ? formatDate($v['StartDate']) : "";
          $v['EndDate'] = isset($v['EndDate']) ? formatDate($v['EndDate']) : "";
          $v['PublicationDate'] = isset($v['PublicationDate']) ? formatDate($v['PublicationDate']) : "";
          $v['Validated'] = $v['Validated'] == 1 ? __("Yes") : __("No");
          if(isset($v['ValidatedOn'])){
            $validateHour = substr(explode(" ", $v['ValidatedOn'])[1], 0, -3);
            $v['ValidatedOn'] = formatDate($v['ValidatedOn'])." ".$validateHour ;
          } else {
            $v['ValidatedOn']="";
          }
      }
    }); 

    array_unshift($prototypes, $headers);

    //llamamos a la función común para exportar
    igfae_export($prototypes, __('Prototypes'));
}