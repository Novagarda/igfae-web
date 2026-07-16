<div class='publications-list' id='igfae-list'>

    <?php if( $rows ): ?>

        <?php foreach( $rows as $key => $publication ): ?>

            <?php igfae_get_template_part('publications', 'list-item', [
                'item' => $publication,
                'index' => $key
            ]); ?>                    
        
        <?php endforeach; ?>

        <?php if(( $pagination ) && ( $pagination['amount'] < $pagination['total'] ) ): ?>
            <?php igfae_get_template_part('template', 'pagination',[
                'actualPage'    => $pagination['actualPage'],
                'amount'        => $pagination['amount'],
                'countPages'    => $pagination['countPages'],
                'total'         => $pagination['total']
            ]); ?>
        <?php endif; ?>

    <?php else: ?>
        
        <div class="alert alert-info pad">
            <strong><?php _e('Non atopado','ifgae-content-viewer'); ?></strong>
        </div>

    <?php endif; ?>

</div>
