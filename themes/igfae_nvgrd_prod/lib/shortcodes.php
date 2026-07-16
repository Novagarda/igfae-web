<?php
// Get PROGRAMAS por id
add_shortcode('nvgrd_programas', 'nvgrd_shortcode_programas');
function nvgrd_shortcode_programas($atts, $content = "", $shortcodename = "nvgrd_programas") {
    $atts = shortcode_atts(array(
      'id'  => ''
    ), $atts, $shortcodename);
    ob_start();

    $prog_args = array(
      'post_type'   => 'programa',
      'meta_key'    => 'area',
      'meta_value'  => $atts['id']
    );

    $prog_query = new WP_Query( $prog_args );

    if ( $prog_query->have_posts() ) {
      echo '<div class="programa-items">';
      while ( $prog_query->have_posts() ) {
        $prog_query->the_post();
        $codigo = get_field('codigo', get_the_id());
        echo '<div>';
        echo '<p>'.$codigo.'</p>';
        echo '<a href="'.get_the_permalink().'" class="azul carga-loading">' . esc_html( get_the_title() ) . '</a>';
        echo '</div>';
      }
      echo '</div>';
    } else {
      esc_html_e( 'Sorry, no posts matched your criteria.' );
    }

    wp_reset_postdata();

    return ob_get_clean();
}

// Get EXPERIMENTOS por area id
add_shortcode('nvgrd_experimentos', 'nvgrd_shortcode_experimentos');
function nvgrd_shortcode_experimentos($atts, $content = "", $shortcodename = "nvgrd_experimentos") {
    $atts = shortcode_atts(array(
      'id'  => ''
    ), $atts, $shortcodename);
    ob_start();

    $prog_args = array(
      'post_type'   => 'experimento',
      'meta_key'    => 'area',
      'meta_value'  => $atts['id']
    );

    $prog_query = new WP_Query( $prog_args );

    if ( $prog_query->have_posts() ) {
      while ( $prog_query->have_posts() ) {
        $prog_query->the_post();
        $codigo = get_field('codigo', get_the_id());
        echo '<div>';
        echo '<a href="'.get_the_permalink().'" class="carga-loading">' . esc_html( get_the_title() ) . '</a>';
        echo '</div>';
      }
    } else {
      esc_html_e( 'Sorry, no posts matched your criteria.' );
    }

    wp_reset_postdata();

    return ob_get_clean();
}

// Get EXPERIMENTOS por programa id
add_shortcode('nvgrd_experimentos_por_programa', 'nvgrd_shortcode_experimentos_por_programa');
function nvgrd_shortcode_experimentos_por_programa($atts, $content = "", $shortcodename = "nvgrd_experimentos_por_programa") {
    $atts = shortcode_atts(array(
      'id'  => ''
    ), $atts, $shortcodename);
    ob_start();

    $prog_args = array(
      'post_type'   => 'experimento',
      'meta_key'    => 'programa',
      'meta_value'  => $atts['id']
    );

    $prog_query = new WP_Query( $prog_args );

    if ( $prog_query->have_posts() ) {
      while ( $prog_query->have_posts() ) {
        $prog_query->the_post();
        $codigo = get_field('codigo', get_the_id());
        echo '<div>';
        echo '<a href="'.get_the_permalink().'" class="carga-loading">' . esc_html( get_the_title() ) . '</a>';
        echo '</div>';
      }
    } else {
      esc_html_e( '' );
    }

    wp_reset_postdata();

    return ob_get_clean();
}

// Get EXPERIMENTOS por programa id
add_shortcode('nvgrd_get_codigo_programa', 'nvgrd_shortcode_get_codigo_programa');
function nvgrd_shortcode_get_codigo_programa($atts, $content = "", $shortcodename = "nvgrd_get_codigo_programa") {
    $atts = shortcode_atts(array(
      'id'  => ''
    ), $atts, $shortcodename);
    ob_start();

    echo get_field('codigo', $atts['id']);

    return ob_get_clean();
}

// Novas relacionadas
/*
add_shortcode('nvgrd_novas_relacionadas', 'nvgrd_shortcode_novas_relacionadas');
function nvgrd_shortcode_novas_relacionadas($atts, $content = "", $shortcodename = "nvgrd_novas_relacionadas") {
    $atts = shortcode_atts(array(
      'id'  => ''
    ), $atts, $shortcodename);
    ob_start();
    $cats = get_field('categorias', $atts['id']);

    if($cats){
      $lista_categorias = implode(', ', $cats);
      echo '<div class="bloque-novas-relacionadas">';
      echo '<div class="list-categorias">'
      foreach ($cats as $key => $cat) {
        echo '<a href="'.get_category_link($cat).'">'.get_cat_name($cat).'</a>';
      }
      echo '</div>';
  
      print_r($lista_categorias);
      $post_args = array(
        'post_type'       => 'post',
        'posts_per_page'  => 3,
        'cat'  => $lista_categorias
      );
  
      $post_query = new WP_Query( $post_args );
  
      if ( $post_query->have_posts() ) {
        while ( $post_query->have_posts() ) {
          $post_query->the_post();
          echo '<div>';
          echo '<a href="'.get_the_permalink().'">' . esc_html( get_the_title() ) . '</a>';
          echo '</div>';
        }
      } else {
        esc_html_e( 'Sorry, no posts matched your criteria.' );
      }
      echo '</div>';
  
      wp_reset_postdata();
    }


    return ob_get_clean();
}
*/