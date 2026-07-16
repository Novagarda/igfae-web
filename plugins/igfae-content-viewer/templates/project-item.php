<article class="list01">
	<div class="left">

		<?php echo ($item->getReference()) ? '<div class="date --font-size-xxs">'.$item->getReference().'</div>':''; ?>
		<?php echo ($item->getProgram()) ? '<span class="btnazul btn-xxs">'.$item->getProgram().'</span>':''; ?>

	</div>
	<div class="right">
		<?php echo ($item->getTitle()) ? '<h3 class="--font-size-xs"><button type="button" class="ver-emerxente text-left stretched-link " data-index="'.$index.'" onclick="abrirEmerxente()">'.$item->getTitle().'</button></h3>': ''; ?>
	</div>
</article>

<div class="caixa-emerxente backazul pad" id="emerxente-<?php echo $index; ?>">
	<button type="button" class="btnclose close-publicacion-emerxente -blanco"></button>
	
	<?php echo ($item->getProgram()) ? '<span class="btnnegro btn-xxs">'.$item->getProgram().'</span>':''; ?>

	<?php echo ($item->getReference()) ? '<div class="textop --font-size-xs">'.$item->getReference().'</div>':''; ?>

	<?php echo ($item->getTitle()) ? '<div class="titulo --font-size-m">'.$item->getTitle().'</div>': ''; ?>

	<div class="ficha ficha-con-linha --font-size-xxs">

		<?php if(count($item->getPersonals()) > 0): ?>
			<div>
				<p><?php _e('Principal investigator', 'ifgae-content-viewer'); ?></p>
				<p>
					<?php 
						$result = '';
						foreach( $item->getPersonals() as $key => $personal ) {
							if ($key > 0) {
								$result .= ', ';
							}
							$result .= $personal;
						}
						echo $result;
					?>
				</p>
			</div>
		<?php endif; ?>

		<?php if($item->getIniDate()): ?>
			<div>
				<p><?php _e('Initial date', 'ifgae-content-viewer'); ?></p> 
				<p><?php echo $item->getIniDate(); ?></p>
			</div>
		<?php endif; ?>

		<?php if($item->getFinDate()): ?>
			<div>
				<p><?php _e('Final date', 'ifgae-content-viewer'); ?></p> 
				<p><?php echo $item->getFinDate(); ?></p>
			</div>
		<?php endif; ?>

		<?php if($item->getAgency()): ?>
			<div>
				<p> <?php _e('Agency', 'ifgae-content-viewer'); ?></p> 
				<p><?php echo $item->getAgency(); ?></p>
			</div>
		<?php endif; ?>		

	</div>

</div>