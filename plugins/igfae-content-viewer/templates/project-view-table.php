<div class=projects-list' id='igfae-list'>

    <?php if( count($projects) > 0 ): ?>

        <?php foreach( $projects as $key => $project ): ?>
            <?php igfae_get_template_part('project', 'item', [
                'item' => $project,
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