<?php
/**
 * Template Name: Persoa
 */

 $parametros_url = get_query_var( 'id' );
 if ( $parametros_url === '' || $parametros_url === false ) {
	 $persoal_page = get_field( 'pagina_de_personal', 'option' );
	 if ( $persoal_page ) {
		 wp_safe_redirect( get_permalink( $persoal_page ), 301 );
		 exit;
	 }
	 igfae_render_404( true );
 }

 $id_persona = substr( $parametros_url, strrpos( $parametros_url, '/' ) + 1 );
 $personnel  = IgfaePersonal::fetch( '', null, null, $id_persona );
 if ( empty( $personnel ) || ! isset( $personnel[0] ) ) {
	 igfae_render_404( true );
 }
 $persoa = $personnel[0];
 $research_program      = $persoa->getResearchProg();
 $research_program_raw  = $persoa->getResearchProgRaw();
 $publications          = $persoa->getPublications();
 $projects              = $persoa->getProjects();

 get_header('', array( 'titulo_pax' => $persoa->getFullName() ));

?>

<div id="persoa-wrapper">
    <section class="home-wrapper persoa">

        <div class="cabeza medioalto linha-baixa flex alinha-abaixo pad">
            <div class="--font-size-xs"><?php echo $persoa->getCategory(); ?></div>
            <h1 class="--font-size-xl --font-size-mb-l azul"><?php echo $persoa->getFullName(); ?></h1>
        </div>


        <div class="persoa-ficha-mob pad ver-mobil">

            <div class="img-blue">
                <img src="<?php echo $persoa->getImgUrl(); ?>" />
            </div>

            <div class="persoa-ficha-mob-contacto --font-size-s --font-size-mb-s">
                <?php echo ($persoa->getEmail()) ? '<p><a href="mailto:'.$persoa->getEmail().'" target="_blank">'.$persoa->getEmail().'</a></p>': '' ?>
                <?php echo ($persoa->getPhone()) ? '<p>(+34) 8818'.$persoa->getPhone().'</p>': '' ?>
                <?php echo ($persoa->getRoom()) ? '<p>'.$persoa->getRoom().'</p>': '' ?>
            </div>

            <div class="persoa-ficha-ids --font-size-xs --font-size-mb-xs">
                <?php echo ($persoa->getOrcidID()) ? '<div><p class="--font-size-xxs">Orcid ID</p><p><a title="Orcid ID" href="https://orcid.org/'.$persoa->getOrcidID().'" target="_blank">'.$persoa->getOrcidID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getScopusID()) ? '<div><p class="--font-size-xxs">Scopus Author ID</p><p><a title="Scopus Author ID" href="http://www.scopus.com/authid/detail.url?authorId='.$persoa->getScopusID().'" target="_blank">'.$persoa->getScopusID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getResearchID()) ? '<div><p class="--font-size-xxs">Research ID</p><p><a title="Research ID" href="http://reserchid.com/rid/'.$persoa->getResearchID().'" target="_blank">'.$persoa->getResearchID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getInspireID()) ? '<div><p class="--font-size-xxs">Inspire ID</p><p><a title="Inspire ID" href="https://inspirehep.net/literature?q=a%20'.$persoa->getInspireID().'&ui-citation-summary=true" target="_blank">'.$persoa->getInspireID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getWebpage()) ? '<div><p class="--font-size-xxs">'.__('Website', 'nvgrd').'</p><p><a href="'.esc_url($persoa->getWebpage()).'" target="_blank">'.esc_html(preg_replace('#^https?://#', '', $persoa->getWebpage())).'</a></p></div>': '' ?>
            </div>

            <div class="persoa-ficha-redes --font-size-s --font-size-mb-xxs">
                <?php echo ($persoa->getLinkedin()) ? '<p><a href="https://www.linkedin.com/in/'.$persoa->getLinkedin().'" target="_blank">Linkedin</a></p>': '' ?>
                <?php echo ($persoa->getTwitter()) ? '<p><a href="https://twitter.com/'.$persoa->getTwitter().'" target="_blank">Twitter</a></p>': '' ?>
            </div>

        </div>


        <div class="persoa-contido">

            <?php if ( $persoa->getArea() ): ?>
                <div class="bloque bloque-inv">

                    <?php if ( $persoa->getArea() ): 
                        $ligazon_area = '';
                        
                        $args_area = array(
                            'post_type' => 'area-investigacion',
                            'meta_query' => array(
                                array(
                                    'key' => 'codigo',
                                    'value' => $persoa->getAreaCode(),
                                    'compare' => '=',
                                ),
                            ),
                        );
                        $query_area = new WP_Query( $args_area );
                        
                        if ( $query_area->have_posts() ) {
                            while ( $query_area->have_posts() ) {
                                $query_area->the_post();
                                $ligazon_area = get_the_permalink();
                            }
                        } 
                        wp_reset_postdata();                    
                        
                        ?>
                        <div class="persoa-researcharea">
                            <h3 class="labelItem --font-size-xxs azul"><?php _e( 'Research Area', 'ifgae-content-viewer' ); ?></h3>
                            <div class="persoa-researprogram-list">
                                <?php echo ($persoa->getAreaCode()) ? '<div class="left azul --font-size-xs">'.$persoa->getAreaCode().'</div>': ''; ?>
                                <div class="right --font-size-xs">
                                    <?php echo ($ligazon_area) ? '<a class="stretched-link bread-link carga-loading" href="'.$ligazon_area.'" data-breadcurrent="areas" data-breadparent="investigacion">': ''; ?>
                                    <?php echo $persoa->getArea(); ?>
                                    <?php echo ($ligazon_area) ? '</a>': ''; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- comeza problema -->
                    <?php if ( $research_program ): 
                        $ligazon_rp = '';
                        
                        $args_rp = array(
                            'post_type' => 'programa',
                            'meta_query' => array(
                                array(
                                    'key' => 'id_do_programa',
                                    'value' => $persoa->getResearchLine()['key'],
                                    'compare' => '=',
                                ),
                            ),
                        );
                        $query_rp = new WP_Query( $args_rp );
                        
                        if ( $query_rp->have_posts() ) {
                            while ( $query_rp->have_posts() ) {
                                $query_rp->the_post();
                                $ligazon_rp = get_the_permalink();
                            }
                        } 
                        wp_reset_postdata();
                        ?>

                        <div class="persoa-researprogram">
                            <h3 class="labelItem --font-size-xxs azul"><?php _e( 'Research Program', 'ifgae-content-viewer' ); ?></h3>
                            <div class="persoa-researprogram-list">
                                <?php foreach ($research_program as $key => $program) { ?>
                                    <div class="left azul --font-size-xs"><?php echo $program['key'] ?></div>
                                    <div class="right --font-size-xs">
                                        <?php echo ($ligazon_rp != '') ? '<a class="stretched-link bread-link carga-loading" href="'.$ligazon_rp.'" data-breadcurrent="programas" data-breadparent="investigacion">': ''; ?>
                                            <?php echo $program['name'] ?>
                                        <?php echo ($ligazon_rp != '') ? '</a>': ''; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- fin problema -->
                    
                </div>
            <?php endif; ?>

            <?php if ( trim($persoa->getDescription()) ): ?>
                <div class="bloque persoa-bio">
                    <h4 class="azul"><?php _e( 'Bio', 'ifgae-content-viewer' ); ?></h4>
                    <div class="--font-size-xs">
                        <?php echo $persoa->getDescription(); ?>
                    </div>
                </div>
            <?php endif; ?>


            <?php if ( $publications  ): ?>
                <div class="bloque persoa-publications --spacing-xl">
                    <h4 class="azul pad-lados"><?php _e( 'Recent Publications', 'ifgae-content-viewer' ); ?></h4>
                    <ul class="persoa-list-cv">
                        <?php 
                        $contador=0;
                        foreach( $publications as $publication ): 
                            if($contador<10){
                            ?>
                        <li class="--font-size-xs">
                            <?php if(str_contains($publication, '#')){ ?>
                                <?php list($publicationTitle,$publicationLink)=explode('#',$publication); ?>
                                <a class="stretched-link" href="<?php echo 'https://doi.org/'.$publicationLink;?>" target="_blank">
                                <?php echo $publicationTitle; ?> 
                                </a>
                            <?php }else{ ?>
                                <?php echo $publication; ?> 
                            <?php } ?>

                            <?php include 'views/partials/arrow.twig'; ?>
                        </li>
                        <?php 
                        }
                        $contador++;
                        endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>    

            <?php if ( $projects ): ?>
                <div class="bloque persoa-projects">
                    <h4 class="azul pad-lados"><?php _e( 'Recent Projects', 'ifgae-content-viewer' ); ?></h4>
                    <ul class="persoa-list-cv">
                        <?php 
                        $contador = 0;    
                        foreach( $projects as $project ): 
                        if($contador<10){
                        ?>
                        <li class="--font-size-xs"><?php echo $project; ?>
                        <?php include 'views/partials/arrow.twig'; ?>
                        </li>
                        <?php 
                        }
                        $contador++;
                        endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>

        <div class="persoa-ficha pad ver-desktop">

            <?php if ($persoa->getImgUrl()) { ?>
                <div class="img-blue">
                    <img src="<?php echo $persoa->getImgUrl(); ?>" />
                </div>
            <?php } else { ?>
                <div class="img-placeholder" style="">
                </div>
            <?php } ?>


            <div class="persoa-ficha-contacto --font-size-xs --font-size-mb-xxs">
                <?php echo ($persoa->getEmail()) ? '<p><a class="dim" href="mailto:'.$persoa->getEmail().'" target="_blank">'.$persoa->getEmail().'</a></p>': '' ?>
                <?php echo ($persoa->getPhone()) ? '<p>(+34) 8818'.$persoa->getPhone().'</p>': '' ?>
                <?php echo ($persoa->getRoom()) ? '<p>'.$persoa->getRoom().'</p>': '' ?>
            </div>

            <div class="persoa-ficha-ids --font-size-xs --font-size-mb-xxxs">
                <?php echo ($persoa->getOrcidID()) ? '<div><p class="--font-size-xxs">Orcid ID</p><p><a class="dim" title="Orcid ID" href="https://orcid.org/'.$persoa->getOrcidID().'" target="_blank">'.$persoa->getOrcidID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getScopusID()) ? '<div><p class="--font-size-xxs">Scopus Author ID</p><p><a class="dim" title="Scopus Author ID" href="http://www.scopus.com/authid/detail.url?authorId='.$persoa->getScopusID().'" target="_blank">'.$persoa->getScopusID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getResearchID()) ? '<div><p class="--font-size-xxs">Research ID</p><p><a class="dim" title="Research ID" href="http://reserchid.com/rid/'.$persoa->getResearchID().'" target="_blank">'.$persoa->getResearchID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getInspireID()) ? '<div><p class="--font-size-xxs">Inspire ID</p><p><a class="dim" title="Inspire ID" href="https://inspirehep.net/literature?q=a%20'.$persoa->getInspireID().'&ui-citation-summary=true" target="_blank">'.$persoa->getInspireID().'</a></p></div>': '' ?>
                <?php echo ($persoa->getWebpage()) ? '<div><p class="--font-size-xxs">'.__('Website', 'nvgrd').'</p><p><a class="dim" href="'.esc_url($persoa->getWebpage()).'" target="_blank">'.esc_html(preg_replace('#^https?://#', '', $persoa->getWebpage())).'</a></p></div>': '' ?>
            </div>

            <div class="persoa-ficha-redes --font-size-s --font-size-mb-xxxs">
                <?php echo ($persoa->getLinkedin()) ? '<p><a class="dim" href="https://www.linkedin.com/in/'.$persoa->getLinkedin().'" target="_blank">Linkedin</a></p>': '' ?>
                <?php echo ($persoa->getTwitter()) ? '<p><a class="dim" href="https://twitter.com/'.$persoa->getTwitter().'" target="_blank">Twitter</a></p>': '' ?>
            </div>

        </div>

    </section><!-- #main -->
</div>

 <?php
 get_footer();
 ?>
