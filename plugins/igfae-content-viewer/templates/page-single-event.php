<?php 
    get_header('event'); 
    $type = NULL;
    $web = NULL;
    $speaker = NULL;
    $abstract = NULL;
    
    $classAbstract = 'col-xs-12';

    $type = $event->getTypeName();

    if ( method_exists($event, 'getAbstract') ) {
        $abstract = $event->getAbstract();
    }

    if ( method_exists($event, 'getToStringSpeaker') ) {
        $speaker = $event->getToStringSpeaker();
    }

    if ( method_exists($event, 'getWebPage') && $event->getWebPage() ) {
        $web = $event->getWebPage();
    }

    if ($abstract) {
        $classAbstract = 'col-xs-6 col-md-3';
    }
?>

<div id="igfae-search-results" class="igfae-search-results single-events">
    
    <div class="event-content pad">
    <?php if( $event ): ?>

        <?php if($type): ?>
            <span class="btnblanco btn-xxs --spacing-xs-b"><?php echo $type; ?></span>
        <?php endif; ?>

        <?php if($event->getTitle()): ?>
            <h1 class="--font-size-xl --font-size-mb-m"><?php echo $event->getTitle(); ?></h1>
        <?php endif; ?>
        
        <div class="corpo">
            
            <div class="left">
                <div class="ficha ficha-con-linha linha-b --font-size-xxs --spacing-l-t">

                    <?php if($speaker): ?>
                        <div>
                            <p><?php _e('Speaker', 'ifgae-content-viewer'); ?></p> 
                            <p class="--font-size-xs"><?php echo esc_html($speaker); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if($event->getDate()): ?>
                        <div>
                            <p><?php _e('Date', 'ifgae-content-viewer'); ?></p>
                            <?php $dateDisplay = igfae_event_get_date_display($event); ?>
                            <?php if( $dateDisplay && $dateDisplay['is_range'] ): ?>
                                <p class="--font-size-xs"><?php echo $dateDisplay['start']; ?> - <?php echo $dateDisplay['end']; ?></p>
                            <?php else: ?>
                                <?php if ($dateDisplay): ?>
                                    <p class="--font-size-xs"><?php echo $dateDisplay['single']; ?><?php if ($dateDisplay['has_time']) echo ' (CET)'; ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>        

                    <?php if($event->getPlace()): ?>
                        <div>
                            <p><?php _e('Place', 'ifgae-content-viewer'); ?></p>
                            <p class="--font-size-xs"><?php echo $event->getPlace(); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if( $web ): ?>
                        <div>
                            <p><?php _e('Web', 'ifgae-content-viewer'); ?></p> 
                            <p class="--font-size-xs"><a href="<?php echo $web; ?>" target="_blank"><?php echo $web; ?></a></p>
                        </div>
                    <?php endif; ?>        

                </div>
            
            </div>

            <div class="right">
                <?php if( $event->getImage() ): ?>
                    <div class="img img-blue">
                        <img src="<?php echo $event->getImage(); ?>" />
                    </div>
                <?php endif; ?>
            </div>

            <div class="contido --spacing-xxl-t">
                    <?php if( $abstract ): ?>
                        <?php echo $abstract; ?>
                    <?php endif; ?>
            </div>

        </div>

    <?php else: ?>
        <div>
            <h4>Not Found!</h4>
        </div>
    <?php endif; ?>
    </div>
    <div class="related-events">
        <div class="related-events-title">
        <h4 class=" --font-size-m"><?php _e('Outros eventos', 'ifgae-content-viewer'); ?></h4>
            <div>    
                <?php $events_page_url = get_permalink(get_field('paxina_de_axenda', 'option')); ?>
                <a href="<?php echo esc_url($events_page_url); ?>" class="btnnegro --font-size-xs"><?php _e('Ver todos', 'ifgae-content-viewer'); ?></a>
			</div>
        </div>

        <?php igfae_get_template_part( 'events', 'grid', IgfaeEvent::fetch( '', NULL, 0, 3, 'all', 'Y', 'DESC', $event->getId()) ); ?>
    </div>
</div>

<?php get_footer(); ?>