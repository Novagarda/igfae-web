<?php
/* PARA LOGEAR
$logInfo .= "[".date("d/m/Y - h:i:s")."] Events: ".print_r($template_file, true)."\n";
file_put_contents(get_stylesheet_directory().'/custom.log', $logInfo, FILE_APPEND);
*/

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';

$timber = new Timber\Timber();

// If the Timber plugin isn't activated, print a notice in the admin.
if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		} );
	return;
}

require_once __DIR__ . '/lib/not-found.php';


// Create our version of the TimberSite object
class StarterSite extends TimberSite {

	// This function applies some fundamental WordPress setup, as well as our functions to include custom post types and taxonomies.
	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_menus' ) );
		add_action( 'init', array( $this, 'register_widgets' ) );
		add_action( 'init', array( $this, 'register_editback' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		parent::__construct();
	}


	// Abstracting long chunks of code.

	// The following included files only need to contain the arguments and register_whatever functions. They are applied to WordPress in these functions that are hooked to init above.

	// The point of having separate files is solely to save space in this file. Think of them as a standard PHP include or require.

	function register_post_types(){
		require('lib/custom-types.php');
	}

	function register_taxonomies(){
		require('lib/taxonomies.php');
	}

	function register_menus(){
		require('lib/menus.php');
	}

	function register_widgets(){
		require('lib/widgets.php');
	}

	function register_editback(){
		require('lib/edit-back.php');
	}

	function register_shortcodes(){
		require('lib/shortcodes.php');
	}

	// Access data site-wide.

	// This function adds data to the global context of your site. In less-jargon-y terms, any values in this function are available on any view of your website. Anything that occurs on every page should be added here.

	function add_to_context( $context ) {

		$lang_dir = get_stylesheet_directory().'/languages';
		load_theme_textdomain('theme', $lang_dir);

		$args = array(
			'numberposts'	=> -1,
			'post_type' => 'post'
		);
		
		$context['projects'] = Timber::get_posts( $args );
		$context['page_news_permalink'] = get_permalink(get_field('paxina_de_noticias', 'option'));
		$context['page_agenda_permalink'] = get_permalink(get_field('paxina_de_axenda', 'option'));

		// Para o menú
		$context['menu'] = new TimberMenu();

		// Chekea que single é para o menú
		$page_persoa 							= get_field('pagina_de_persona', 'option');
		$context['e_nova'] 						= (is_singular( 'post' )) ? true : false;
		$context['e_experimento'] 				= (is_singular( 'experimento' )) ? true : false;
		$context['e_oferta'] 					= (is_singular( 'oferta' )) ? true : false;
		$context['e_actividad'] 				= (is_singular( 'actividad' )) ? true : false;
		$context['e_programa_investigacion'] 	= (is_singular( 'programa' )) ? true : false;
		global $post;
		$context['e_persoa'] 					= ($post->ID == $page_persoa ) ? true : false;
		global $wp;
		$current_url 							= home_url( $wp->request );
		$context['e_axenda'] 					= (strpos($current_url,'talk') !== false || strpos($current_url,'seminar') !== false || strpos($current_url,'outreach') !== false) ? true : false;
		$context['e_persoa'] 					= (strpos($current_url,'persoa') !== false) ? true : false;

		if(is_singular( 'actividad' )){
			$args_fillos = array(
				'post_type'	=> 'post',
				'post_parent' => $post->ID, // Current post's ID
			);
			$children = get_children( $args_fillos );
			if ( empty($children) ) {
				$context['post_non_ten_fillos'] = true;
			} else {
				$context['post_non_ten_fillos'] = false;
			}
		}

		// Contido flexible 
		$context['test'] = get_the_ID();
		if (get_the_ID()) {
			if (have_rows('content_flexible')):
		
				while (have_rows('content_flexible')) : the_row();
				
					// Case: Novas relacionadas por categoria.
					if (get_row_layout() == 'related_row'):
		
						$cats = get_sub_field('related_news');
						$cats_array = array();
		
						if (is_array($cats) && !empty($cats)) {
							foreach ($cats as $key => $cat) {
								array_push($cats_array, $cat->term_id);
							}
						}
		
						$cats_list = implode(',', $cats_array);
						$num_of_posts = get_sub_field('numero');
		
						$args_related = array(
							'numberposts' => $num_of_posts,
							'post_type' => 'post',
							'cat' => $cats_list
						);
		
						$context['relacionadas'] = Timber::get_posts($args_related);
		
					// Case: Download layout.
					elseif (get_row_layout() == 'download'): 
						$file = get_sub_field('file');
						// Do something...
		
					endif;
		
				// End loop.
				endwhile;
		
			endif;
		}
		

		// This 'site' context below allows you to access main site information like the site title or description.
		$context['site'] = $this;
		return $context;
	}

	// Here you can add your own fuctions to Twig. Don't worry about this section if you don't come across a need for it.
	// See more here: http://twig.sensiolabs.org/doc/advanced.html
	function add_to_twig( $twig ) {
		/**
		 * versioned_theme_uri() Twig Function
		 *
		 * Create an auto-versioned URI for a relative theme file.
		 *
		 * @param \Twig\Environment $twig The Twig environment.
		 * @return \RandomNumber
		 */

		 $theme_uri_versioned = new Timber\Twig_Function( 'theme_uri_versioned', function ($theme_file) {
			$uri = get_theme_file_uri( $theme_file );
			$filepath = get_theme_file_path( $theme_file );
			$version = file_exists( $filepath ) ? filemtime( $filepath ) : 'FILE_NOT_FOUND';
		
			return "$uri?v=$version";
		  } );

		$twig->addFilter( new Timber\Twig_Filter( 'slugify', function( $title ) {
			return sanitize_title( $title );
		} ) );

		$twig->addFunction($theme_uri_versioned);
		$twig->addExtension( new Twig_Extension_StringLoader() );
		return $twig;
	}

}

new StarterSite();


/*
 *
 * My Functions (not from Timber)
 *
 */

// Enqueue scripts
function my_scripts() {

	// Use jQuery from a CDN
//	wp_deregister_script('jquery');

}

add_action( 'wp_enqueue_scripts', 'my_scripts' );

//add_action( 'init', 'cp_change_post_object' );
// Change dashboard Posts to News
function cp_change_post_object() {
    $get_post_type = get_post_type_object('post');
    $labels = $get_post_type->labels;
	$labels->name = 'Proyectos';
	$labels->singular_name = 'Proyecto';
	$labels->add_new = 'Agregar proyecto';
	$labels->add_new_item = 'Agregar proyecto';
	$labels->edit_item = 'Editar proyecto';
	$labels->new_item = 'Proyecto';
	$labels->view_item = 'Ver proyectos';
	$labels->search_items = 'Buscar proyectos';
	$labels->not_found = 'No se encontraron proyectos';
	$labels->not_found_in_trash = 'No se encontraron proyectos en la Papelera';
	$labels->all_items = 'Todos los proyectos';
	$labels->menu_name = 'Proyectos';
	$labels->name_admin_bar = 'Proyectos';
}


/////IMAGES SIZES
///// Borrado de los que vienen de serie menos thumbnail
add_filter( 'intermediate_image_sizes_advanced', 'prefix_remove_default_images' );
function prefix_remove_default_images( $sizes ) {
    unset( $sizes['thumbnail']); // 150px
    unset( $sizes['medium']); // 300px
    unset( $sizes['medium_large']); // 1024px
    unset( $sizes['large']); // 768px
    unset( $sizes['1536x1536']); // 768px
    unset( $sizes['2048x2048']); // 768px
    return $sizes;
}

add_theme_support( "post-thumbnails" );
// add_image_size( '@4x', 3000);
add_image_size( '@3x', 2225);
add_image_size( '@2x', 1500);
add_image_size( '@1x', 750);
add_image_size( '@0300x', 30);


add_filter('use_block_editor_for_post', '__return_false');


// Quita la opción de tener thumbnail en los posts. Lo hacemos con un custom field.
function remove_post_thumbnail_support() {
//    remove_post_type_support( 'post', 'thumbnail' );
	remove_post_type_support( 'page', 'thumbnail' );
	remove_post_type_support( 'post', 'excerpt' );
	unregister_taxonomy_for_object_type( 'post_tag', 'post' );
}
add_action( 'init', 'remove_post_thumbnail_support' );

// Quita o editor de texto de páxinas concretas
function remove_pages_editor(){

	if(get_the_ID()){
		$template_file = get_post_meta(get_the_ID(), '_wp_page_template', TRUE);
		remove_post_type_support( 'page', 'editor' );

		if ($template_file == 'template-areas-investigacion.php') {
			remove_post_type_support('page', 'editor');
		}
	}
} 
add_action( 'add_meta_boxes', 'remove_pages_editor' );

// Desactivar el sistema de comentarios
function disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'disable_comments_admin_menu');

// Redirigir cualquier intento de acceso a la página de comentarios
function disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url()); // Redirige a la página principal del panel de administración
        exit;
    }
}
add_action('admin_init', 'disable_comments_admin_menu_redirect');

// Desactivar cualquier soporte para comentarios en el tema
function disable_comments_support() {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
    remove_post_type_support('attachment', 'comments');
}
add_action('init', 'disable_comments_support');

// Desactivar el frontend de comentarios
function disable_comments_status() {
    return false;
}
add_filter('comments_open', 'disable_comments_status', 20, 2);
add_filter('pings_open', 'disable_comments_status', 20, 2);

// Ocultar cualquier referencia a comentarios en el frontend
function disable_comments_hide_comments_template() {
    if (is_singular()) {
        ob_start(function($content) {
            return preg_replace('/<!--(.|\s)*?-->/', '', $content);
        });
    }
}
add_action('template_redirect', 'disable_comments_hide_comments_template', 9);



/*
* Adding a custom styles drop-down menu
*/

function wpb_mce_buttons_2($buttons) {
	array_unshift($buttons, 'styleselect');
	return $buttons;
}
add_filter('mce_buttons_2', 'wpb_mce_buttons_2');

/*
* Callback function to filter the MCE settings
*/

function my_mce_before_init_insert_formats( $init_array ) {  

	// Define the style_formats array
		$style_formats = array(  
			array(  
				'title' => 'superindice',  
				'block' => 'sup',  
				'classes' => 'sup',
				'wrapper' => false,
				
			),
			array(  
				'title' => 'link',  
				'block' => 'link',  
				'classes' => 'link',
				'wrapper' => false,
			)
		);  
		// Insert the array, JSON ENCODED, into 'style_formats'
		$init_array['style_formats'] = json_encode( $style_formats );  
		
		return $init_array;  
	  
	} 
	// Attach callback to 'tiny_mce_before_init' 
	add_filter( 'tiny_mce_before_init', 'my_mce_before_init_insert_formats' ); 

	// Print styles on backoffice
	function my_theme_add_editor_styles() {
		add_editor_style( 'custom-editor-style.css' );
	}
	add_action( 'init', 'my_theme_add_editor_styles' );


	// Limitar medios de imagen a 2MB
	function limitar_tamano_imagenes($file) {
		$tamanho_maximo = 2 * 1024 * 1024; // 2 MB (en bytes)

		// Verificar si el archivo es una imagen
		$file_info = wp_check_filetype($file['name']);
		$allowed_image_types = array('jpeg', 'jpg', 'png', 'gif');
		if (in_array($file_info['ext'], $allowed_image_types)) {
			if ($file['size'] > $tamanho_maximo) {
				$file['error'] = 'El archivo excede el tamaño máximo permitido (2MB).';
			}
		}

		return $file;
	}
	add_filter('wp_handle_upload_prefilter', 'limitar_tamano_imagenes');

	// Habilitar la subida de archivos SVG
	function allow_svg_upload($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
	add_filter('upload_mimes', 'allow_svg_upload');
  

///// PERMALINKS
add_action( 'init', 'nvgrd_url_getparam_init' ); 
function nvgrd_url_getparam_init(){
	add_rewrite_rule( '^persoa/(.*)/?', 'index.php?page_id=16091&id=$matches[1]', 'top' ); 
//	add_rewrite_rule( '^persoal/(.*)/?', 'index.php?page_id=16085&id=$matches[1]', 'top' ); 
}

add_filter('query_vars', 'nvgrd_view_getparam_from_url');
function nvgrd_view_getparam_from_url($vars) {
	$vars[] = 'id';
	return $vars;
}