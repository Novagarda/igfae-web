<?php if( count($personal) > 0 ): ?>
    <div class="azul personal-list">
        <?php foreach( $personal as $key => $person ): ?>
            
            <?php igfae_get_template_part( 'personal-table3', 'item', [
                'key' => $key,
                'item' => $person
            ]); ?>

        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info pad pad">
        <strong><?php _e('Not found!', 'ifgae-content-viewer'); ?></strong>
    </div>
<?php endif; ?>