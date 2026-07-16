<?php

/*
 *
 * Taxonomies
 *
 */

// Same as with Custom Types, you only need the arguments and register_taxonomy function here. They are hooked into WordPress in functions.php.

create_taxonomies("oferta_category", "oferta", "Categoría", "Categorías", "OfertaCategory", "OfertaCategories");

function create_taxonomies($slug, $destiny, $singular, $plural, $GraphSingular, $GraphPlural) {
  $labels = array(
        "name"                       => $plural,
        "singular_name"              => $singular,
        "menu_name"                  => $plural,
      );
      $args = array(
        "labels"                     => $labels,
        "hierarchical"               => true,
        "public"                     => true,
        "show_ui"                    => true,
        "show_in_quick_edit"         => false,
        "meta_box_cb"                => false,
        "show_admin_column"          => false,
        "show_in_nav_menus"          => false,
        "show_tagcloud"              => false,
        "show_in_rest"               => true,
        "show_in_graphql"            => true,
        "graphql_single_name"        => $GraphSingular,
        "graphql_plural_name"        => $GraphPlural,
      );

  register_taxonomy( $slug, array( $destiny ), $args );
}
