<div class='prensas-list' id='igfae-list'>

    <?php if( count($projects) > 0 ): ?>

        <?php 
            $ano = 0;
            foreach( $projects as $key => $project ): 
                $ano_nova = date('Y', strtotime($project->getDate()));
                if($ano_nova != $ano){ 
                    $ano = $ano_nova;
                    ?>

                    <div class="ano pad-l azul --font-size-m --spacing-xl-t"><?php echo $ano; ?></div>
                    
                <?php } ?>
            <?php 
            igfae_get_template_part('prensa', 'item', [
                'item' => $project,
                'index' => $key
            ]); 
            ?>
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