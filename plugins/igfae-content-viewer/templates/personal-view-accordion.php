<div class='panel-group' id='igfae-accordion'>
<?php if( count($personal) > 0 ): ?>
    <?php 
        $group = $personal[0]->getGroup(); 
        $groupId = 0;
    ?>
    <div class='panel panel-default'>
        <a data-toggle='collapse'  href='#group-<?php echo $groupId; ?>'>
            <div class='panel-heading accordion-primary-head'>
                <h3 class='panel-title'>
                    <strong><?php echo $group; ?></strong>
                </h3>
            </div>
        </a>
        <div class='panel-collapse collapse in' id='group-<?php echo $groupId; ?>'>
            <table>
                <tbody>
                <?php foreach( $personal as $person ): ?>
                    <?php if ($person->getGroup() != $group): ?>
                        <?php 
                            $group = $person->getGroup(); 
                            $groupId++;
                        ?>

                        </tbody></table></div></div>
                        <div class='panel panel-default'>
                            <a data-toggle='collapse' href='#group-<?php echo $groupId; ?>'>
                                <div class='panel-heading accordion-primary-head'>
                                    <h3 class='panel-title'>
                                        <strong><?php echo $group; ?></strong>
                                    </h3>
                                </div>
                            </a>
                            <div class='panel-collapse collapse in' id='group-<?php echo $groupId; ?>'>
                                <table><tbody>
                    <?php endif; ?>

                    <?php igfae_get_template_part('personal', 'item', [
                        'item' => $person
                    ]); ?>

            	<?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info pad pad">
        <strong><?php _e('Not found!', 'ifgae-content-viewer'); ?></strong>
    </div>
<?php endif; ?>
</div>