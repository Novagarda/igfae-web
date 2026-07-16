<article class="list01">
	<div class="left">
		<?php 
		$date = null;
		if($item->getDate()){
			try {
				$date = new DateTime($item->getDate());
			} catch ( Exception $e ) {
				$date = null;
			}
			if ( $date ) {
				echo '<div class="date --font-size-xxs">' . $date->format('Y') . '-' . $date->format('m') . '-' . $date->format('d') . '</div>';
			}
		} 	
		?>

		<?php if($item->getType() == 'article'){ ?>
			<span class="btn btnazul btn-xxs"><?php _e('Article' ,'ifgae-content-viewer'); ?></span>
		<?php }else{ ?>
			<span class="btn btnazul btn-xxs"><?php _e('Thesis' ,'ifgae-content-viewer'); ?></span>
		<?php } ?>
	</div>

	<div class="right">
		<?php echo ($item->getTitle()) ? '<h3 class="--font-size-s"><button type="button" class="ver-emerxente text-left stretched-link" data-index="'.$index.'" onclick="abrirEmerxente()">'.$item->getTitle().'</button></h3>': ''; ?>
		<?php if($item->getType() == 'article' && $item->getJournal()): ?>
			<p class="list01-meta --font-size-xxs"><?php echo $item->getJournal(); ?></p>
		<?php endif; ?>
	</div>

</article>

<div class="caixa-emerxente backazul pad" id="emerxente-<?php echo $index; ?>">
	<button type="button" class="btnclose close-publicacion-emerxente -blanco"></button>

	<?php if($item->getType() == 'article'){ ?>
		<span class="btnnegro btn-xxs"><?php _e('Article' ,'ifgae-content-viewer'); ?></span>
	<?php }else{ ?>
		<span class="btnnegro btn-xxs"><?php _e('Thesis' ,'ifgae-content-viewer'); ?></span>
	<?php } ?>

	<?php if ( $date ) : ?>
		<div class="textop --font-size-xs"><?php echo $date->format('Y') . '-' . $date->format('m') . '-' . $date->format('d'); ?></div>
	<?php endif; ?>
	<?php echo ($item->getTitle()) ? '<div class="titulo --font-size-m">'.$item->getTitle().'</div>': ''; ?>
	
	<div class="ficha --font-size-xxs">

		<?php if($item->getType() == 'article'){ ?>

			<?php if($item->getVolume() || $item->getPages() || $item->getYear()): ?>
			<div>
				<p>
					<?php echo ($item->getVolume())? $item->getVolume() . ',':''; ?>
					<?php echo ($item->getPages())? $item->getPages():''; ?>
					<?php echo ($item->getYear())? '(' . $item->getYear() . ')':''; ?>
				</p>
			</div>
			<?php endif; ?>

		<?php } ?>

		<?php if($item->getType() == 'article' && $item->getDoi()){ ?>
			<div>
				<p>DOI</p>
				<p>
					<?php echo ($item->getUrlDoi()) ? '<a target="_blank" href="'.$item->getUrlDoi().'">': ''; ?> 
						<?php echo $item->getDoi(); ?>
					<?php echo ($item->getUrlDoi()) ? '</a>': ''; ?>
				</p>
			</div>
		<?php } ?>

		<?php if($item->getType() == 'article' && $item->getArxiv()): ?>
			<div>
				<p>ARXIV</p>
				<p>
					<?php echo ($item->getUrlArxiv()) ? '<a target="_blank" href="'.$item->getUrlArxiv().'">': ''; ?> 
						<?php echo $item->getArxiv(); ?>
					<?php echo ($item->getUrlArxiv()) ? '</a>': ''; ?>
				</p>
			</div>
		<?php endif; ?>

		<?php if($item->getType() == 'article' && $item->getLocalAuthors()): ?>
			<div>
				<p>JCAP</p>	
				<p>
					<?php echo $item->getLocalAuthors(); ?>
				</p>
			</div>
		<?php endif; ?>

		<?php if($item->getType() == 'thesis'){ ?>
			<div class="todos-autores --font-size-xxs">
				<p><?php _e('Author(s)','ifgae-content-viewer'); ?></p>
				<p>
					<?php echo $item->getAuthor(); ?>
				</p>
			</div>
		<?php } ?>

		<?php if($item->getType() == 'article' && $item->getJournal()): ?>
			<div>
				<p><?php _e('Journal','ifgae-content-viewer'); ?></p>
				<p><?php echo $item->getJournal(); ?></p>
			</div>
		<?php endif; ?>

	</div>

</div>
