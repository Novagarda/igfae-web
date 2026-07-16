<div class='panel-group group-projects' id='igfae-accordion'>
<?php if( count($projects) > 0 ): ?>
    <?php 
    $groupName = $projects[0]->getGroups(0); 
    $groupId = 0;
    $year = $projects[0]->getIniYear();
    ?>
    <div class='panel panel-default'>
        <a data-toggle='collapse' data-parent='#igfae-accordion' href='#group-<?php echo $groupId; ?>'>
            <div class='panel-heading accordion-primary-head'>
                <h3 class='panel-title'>
                    <?php echo $groupName; ?>
                </h3>
            </div>
        </a>
        <div class='panel-collapse collapse' id='group-<?php echo $groupId; ?>'>
            <div class='panel-group group-body' id='igfae-accordion-group-<?php echo $groupId; ?>'>
                <div class='panel panel-default'>
                    <a data-toggle='collapse' data-parent='#igfae-accordion-group-<?php echo $groupId; ?>' href='#group-<?php echo "{$groupId}-{$year}"; ?>'>
                        <div class='panel-heading sub-acordion-heading'>
                            <h3 class='panel-title'>
                                <?php echo $year; ?>
                            </h3>
                        </div>
                    </a>
                    <div class='panel-collapse collapse in' id='group-<?php echo "{$groupId}-{$year}"; ?>'>
                        <table>
                            <tbody>
                            <?php foreach( $projects as $project ): ?>

                                <?php if ($project->getGroups(0) != $groupName): ?>
                                    <?php 
                                        $groupName = $project->getGroups(0); 
                                        $groupId++;
                                        $year = $project->getIniYear();
                                    ?>

                                    </tbody></table></div></div></div></div></div>
                                    <div class='panel panel-default'>
                                        <a data-toggle='collapse' data-parent='#igfae-accordion' href='#group-<?php echo $groupId; ?>'>
                                            <div class='panel-heading accordion-primary-head'>
                                                <h3 class='panel-title'>
                                                    <?php echo $groupName; ?>
                                                </h3>
                                            </div>
                                        </a>
                                        <div class='panel-collapse collapse' id='group-<?php echo $groupId; ?>'>
                                            <div class='panel-group group-body' id='igfae-accordion-group-<?php echo $groupId; ?>'>
                                                <div class='panel panel-default'>
                                                    <a data-toggle='collapse' data-parent='#igfae-accordion-group-<?php echo $groupId; ?>' href='#group-<?php echo "{$groupId}-{$year}"; ?>'>
                                                        <div class='panel-heading sub-acordion-heading'>
                                                            <h3 class='panel-title'>
                                                                <?php echo $year; ?>
                                                            </h3>
                                                        </div>
                                                    </a>
                                                    <div class='panel-collapse collapse in' id='group-<?php echo "{$groupId}-{$year}"; ?>'>
                                                        <table><tbody>
                                <?php endif; ?>

                                <?php if ($project->getIniYear() != $year): ?>
                                    <?php $year = $project->getIniYear(); ?>

                                    </tbody></table></div></div>
                                    <div class='panel panel-default'>
                                        <a data-toggle='collapse' data-parent='#igfae-accordion-group-<?php echo $groupId; ?>' href='#group-<?php echo "{$groupId}-{$year}"; ?>'>
                                            <div class='panel-heading sub-acordion-heading'>
                                                <h3 class='panel-title'>
                                                    <?php echo $year; ?>
                                                </h3>
                                            </div>
                                        </a>
                                        <div class='panel-collapse collapse' id='group-<?php echo "{$groupId}-{$year}"; ?>'>
                                            <table><tbody>
                                <?php endif; ?>

                                <?php igfae_get_template_part('project', 'item', [
                                    'item' => $project
                                ]); ?>
                        	<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-info pad">
        <strong><?php _e('Not found!!','ifgae-content-viewer'); ?></strong>
    </div>
<?php endif; ?>
</div>