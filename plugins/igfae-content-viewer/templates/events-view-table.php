<div class='events-list block-eventos' id='igfae-table'>
<?php if( !empty($rows) && is_array($rows) && count($rows) > 0 ): ?>

        <?php
            foreach( $rows as $key => $event ) {
                igfae_get_template_part('events', 'item', [
                    'item' => $event,
                    'index' => $key
                ]);
            }
        ?>

        <?php if(( $pagination ) && ( $pagination['amount'] < $pagination['total'] ) ): ?>
            <div class="row">
                <?php igfae_get_template_part('template', 'pagination',[
                    'actualPage'    => $pagination['actualPage'],
                    'amount'        => $pagination['amount'],
                    'countPages'    => $pagination['countPages'],
                    'total'         => $pagination['total']
                ]); ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info pad">
            <strong><?php _e('Not found!', 'ifgae-content-viewer'); ?></strong>
        </div>
    <?php endif; ?>
</div>