<div class='list02 projects-list pequena'>

    <?php if( count($projects) > 0 ): ?>

        <?php foreach( $projects as $key => $project ): ?>

            <article class="grid grid-3 grid-list-pequena">
                <div class="left">
                    <?php echo ($project->getIniDate()) ? '<div class="date --font-size-xxs">'.$project->getIniDate().'</div>':''; ?>
                    <span class="btnazul btn-xxs"><?php echo $project->getProgram(); ?></span>
                </div>

                <div class="right">
                    <?php echo ($project->getTitle()) ? '<h3 class="--font-size-xs"><button type="button" class="ver-emerxente text-left stretched-link " data-index="pro-'.$key.'" onclick="abrirEmerxente()">'.$project->getTitle().'</button></h3>': ''; ?>
                </div>
            </article>



<div class="caixa-emerxente backazul pad" id="emerxente-pro-<?php echo $key; ?>">
	<button type="button" class="btnclose close-publicacion-emerxente -blanco"></button>
	
	<?php echo ($project->getProgram()) ? '<span class="btnnegro btn-xxs">'.$project->getProgram().'</span>':''; ?>

	<?php echo ($project->getReference()) ? '<div class="textop --font-size-xs">'.$project->getReference().'</div>':''; ?>

	<?php echo ($project->getTitle()) ? '<div class="titulo --font-size-m">'.$project->getTitle().'</div>': ''; ?>

	<div class="ficha ficha-con-linha --font-size-xxs">

		<?php if(count($project->getPersonals()) > 0): ?>
			<div>
				<p><?php _e('Principal investigator', 'ifgae-content-viewer'); ?></p>
				<p>
					<?php 
						$result = '';
						foreach( $project->getPersonals() as $keyy => $personal ) {
							if ($keyy > 0) {
								$result .= ', ';
							}
							$result .= $personal;
						}
						echo $result;
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php if($project->getIniDate()): ?>
			<div>
				<p><?php _e('Initial date', 'ifgae-content-viewer'); ?></p> 
				<p><?php echo $project->getIniDate(); ?></p>
			</div>
		<?php endif; ?>

		<?php if($project->getFinDate()): ?>
			<div>
				<p><?php _e('Final date', 'ifgae-content-viewer'); ?></p> 
				<p><?php echo $project->getFinDate(); ?></p>
			</div>
		<?php endif; ?>

		<?php if($project->getAgency()): ?>
			<div>
				<p> <?php _e('Agency', 'ifgae-content-viewer'); ?></p> 
				<p><?php echo $project->getAgency(); ?></p>
			</div>
		<?php endif; ?>		

	</div>

</div>            

        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-info pad">
            <strong><?php _e('Non atopado','ifgae-content-viewer'); ?></strong>
        </div>
    <?php endif; ?>
</div>