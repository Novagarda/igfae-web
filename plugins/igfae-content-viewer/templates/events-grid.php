<div class='events-grid'>
    <?php if( count($rows) > 0 ): ?>
        <?php foreach( $rows as $key => $event ) { 
            $type = NULL;    
            $type = $event->getTypeName();
            ?>
            <article class="event-grid">
                <div class="event-grid-inner">
                <?php if($event->getTitle()){ ?>
                    <h2 class="titulo --font-size-s">
                        <?php echo $event->getTitle(); ?>
                </h2>
                <?php } ?>

                <?php if($type): ?>
                    <div><span class="btnblanco btn-xxs --spacing-s-t"><?php echo $type; ?></span></div>
                <?php endif; ?>

                <div class="espacio"></div>

                <div class="--spacing-xl-t bottom">
                    <div class="left">
                        <?php if($event->getDate()): ?>
                            <div>
                                <?php $dateDisplay = igfae_event_get_date_display($event); ?>
                                <?php if( $dateDisplay && $dateDisplay['is_range'] ): ?>
                                    <p class="--font-size-xs"><?php echo $dateDisplay['start']; ?></p>
                                    <p class="--font-size-xs"><?php echo $dateDisplay['end']; ?></p>
                                <?php else: ?>
                                    <?php if ($dateDisplay): ?>
                                        <p class="--font-size-xs"><?php echo $dateDisplay['single']; ?><?php if ($dateDisplay['has_time']) echo ' (CET)'; ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>       
                        
                        <?php if($event->getPlace()): ?>
                            <div class="--spacing-s-t">
                                <p class="--font-size-xs"><?php echo $event->getPlace(); ?></p>
                            </div>
                        <?php endif; ?>                    
                    </div>

                    <div class="right">
                        <?php if( $event->getImage() ): ?>
                            <div class="img img-blue">
                                <img src="<?php echo $event->getImage(); ?>" />
                            </div>
                        <?php endif; ?>                    
                    </div>

                    <?php echo ($event->getURL()) ? '<a class="stretched-link carga-loading" href="'.$event->getURL().'">': ''; ?>
                        <?php echo ($event->getURL()) ? '</a>': ''; ?>

                </div>

                <div class="block-job-postover"></div>
                </div>
                
            </article>

        <?php } ?>
    <?php endif; ?>
</div>