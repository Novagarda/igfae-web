<div class='panel-group' id='igfae-table'>
<?php if( count($personal) > 0 ): ?>
    <table>
        <tbody>
        <?php foreach( $personal as $person ): ?>
           
            <?php igfae_get_template_part('personal', 'item', [
                'item' => $person
            ]); ?>

    	<?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info pad pad">
        <strong><?php _e('Not found!', 'ifgae-content-viewer'); ?></strong>
    </div>
<?php endif; ?>
</div>