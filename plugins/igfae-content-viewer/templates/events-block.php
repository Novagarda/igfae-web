<div class='events-list' id='igfae-table'>
<?php if( !empty($rows) && is_array($rows) && count($rows) > 0 ): ?>

        <?php
            foreach( $rows as $key => $event ) {
                igfae_get_template_part('events', 'item', [
                    'item' => $event,
                    'index' => $key
                ]);
            }
        ?>

<?php else: ?>
    <div class="alert alert-info pad pad">
        <strong><?php _e('Not found!', 'ifgae-content-viewer'); ?></strong>
    </div>
<?php endif; ?>
</div>