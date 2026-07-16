<div class='panel-group' id='igfae-accordion'>
<?php if( count($publications) > 0 ): ?>
    <?php $month = $publications[0]->getMonthYear(); ?>
    <div class='panel panel-default'>
        <a data-toggle='collapse' data-parent='#igfae-accordion' href='#<?php echo $month; ?>'>
            <div class='panel-heading'>
                <h3 class='panel-title'>
                    <strong><?php echo str_replace('-', ' ', $month); ?></strong>
                </h3>
            </div>
        </a>
        <div class='panel-collapse collapse in' id='<?php echo $month; ?>'>
            <table>
                <tbody>
                <?php foreach( $publications as $key => $publication ): ?>
                    <?php if ($publication->getMonthYear() != $month): ?>
                        <?php $month = $publication->getMonthYear(); ?>

                        </tbody></table></div></div>
                        <div class='panel panel-default'>
                            <a data-toggle='collapse' data-parent='#igfae-accordion' href='#<?php echo $month; ?>'>
                                <div class='panel-heading'>
                                    <h3 class='panel-title'>
                                        <strong><?php echo str_replace('-', ' ', $month); ?></strong>
                                    </h3>
                                </div>
                            </a>
                            <div class='panel-collapse collapse' id='<?php echo $month; ?>'>
                                <table><tbody>
                    <?php endif; ?>

                    <?php if($publication->getType() == 'article'): ?>
                        <?php igfae_get_template_part('publications', 'article', [
                            'item'  => $publication,
                            'index' => $key
                        ]); ?>
                    <?php else: ?>
                        <?php igfae_get_template_part('publications', 'thesis', [
                            'item' => $publication,
                            'index' => $key
                        ]); ?>
                    <?php endif; ?>
                                
            	<?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info pad">
        <strong><?php _e('Non atopado','ifgae-content-viewer'); ?></strong>
    </div>
<?php endif; ?>
</div>