<div class='list02 publications-list pequena'>

    <?php if( $rows ): ?>

        <?php foreach( $rows as $key => $publication ): ?>

            <article class="grid grid-3 grid-list-pequena">
                <div class="left">
                    <?php 
                        $date = null;
                        if($publication->getDate()){
                            try {
                                $date = new DateTime($publication->getDate());
                            } catch ( Exception $e ) {
                                $date = null;
                            }
                            if ( $date ) {
                                echo '<div class="date --font-size-xxs">' . $date->format('Y') . '-' . $date->format('m') . '-' . $date->format('d') . '</div>';
                            }
                        } 	
                    ?>

                    <?php if($publication->getType() == 'article'){ ?>
                        <span class="btnazul btn-xxs"><?php _e('Article' ,'ifgae-content-viewer'); ?></span>
                    <?php }else{ ?>
                        <span class="btnazul btn-xxs"><?php _e('Thesis' ,'ifgae-content-viewer'); ?></span>
                    <?php } ?>
                </div>

                <div class="right">
                    <?php echo ($publication->getTitle()) ? '<h3 class="--font-size-xs"><button type="button" class="ver-emerxente text-left stretched-link" data-index="pub-'.$key.'" onclick="abrirEmerxente()">'.$publication->getTitle().'</button></h3>': ''; ?>
                    <?php if($publication->getType() == 'article' && $publication->getJournal()): ?>
                        <p class="list01-meta --font-size-xxs"><?php echo $publication->getJournal(); ?></p>
                    <?php endif; ?>
                </div>
                

            </article>     
            
            


<div class="caixa-emerxente backazul pad" id="emerxente-pub-<?php echo $key; ?>">
	<button type="button" class="btnclose close-publicacion-emerxente -blanco"></button>

	<?php if($publication->getType() == 'article'){ ?>
		<span class="btnnegro btn-xxs"><?php _e('Article' ,'ifgae-content-viewer'); ?></span>
	<?php }else{ ?>
		<span class="btnnegro btn-xxs"><?php _e('Thesis' ,'ifgae-content-viewer'); ?></span>
	<?php } ?>
	<?php if ( $date ) : ?>
		<div class="textop --font-size-xs"><?php echo $date->format('Y') . '-' . $date->format('m') . '-' . $date->format('d'); ?></div>
	<?php endif; ?>

	<?php echo ($publication->getTitle()) ? '<div class="titulo --font-size-m">'.$publication->getTitle().'</div>': ''; ?>
	
	<div class="ficha --font-size-xxs">

		<?php if($publication->getType() == 'article'){ ?>
			<?php if($publication->getVolume() || $publication->getPages() || $publication->getYear()): ?>
			<div>
				<p>
					<?php echo ($publication->getVolume())? $publication->getVolume() . ',':''; ?>
					<?php echo ($publication->getPages())? $publication->getPages():''; ?>
					<?php echo ($publication->getYear())? '(' . $publication->getYear() . ')':''; ?>
				</p>
			</div>
			<?php endif; ?>
		<?php } ?>


		<?php if($publication->getType() == 'article' && $publication->getDoi()){ ?>
			<div>
				<p>DOI</p>
				<p>
					<?php echo ($publication->getUrlDoi()) ? '<a target="_blank" href="'.$publication->getUrlDoi().'">': ''; ?> 
						<?php echo $publication->getDoi(); ?>
					<?php echo ($publication->getUrlDoi()) ? '</a>': ''; ?>
				</p>
			</div>
		<?php } ?>

		<?php if($publication->getType() == 'article' && $publication->getArxiv()): ?>
			<div>
				<p>ARXIV</p>
				<p>
					<?php echo ($publication->getUrlArxiv()) ? '<a target="_blank" href="'.$publication->getUrlArxiv().'">': ''; ?> 
						<?php echo $publication->getArxiv(); ?>
					<?php echo ($publication->getUrlArxiv()) ? '</a>': ''; ?>
				</p>
			</div>
		<?php endif; ?>

		<?php if($publication->getType() == 'article' && $publication->getLocalAuthors()): ?>
			<div>
				<p>JCAP</p>	
				<p>
					<?php echo $publication->getLocalAuthors(); ?>
				</p>
			</div>
		<?php endif; ?>

	</div>

	<?php if($publication->getType() == 'article' && $publication->getAllAuthors()): ?>
		<div class="todos-autores --font-size-xxs">
			<p><?php _e('All authors','ifgae-content-viewer'); ?></p>
			<p>
				<?php echo $publication->getAllAuthors(); ?>
			</p>
		</div>
	<?php endif; ?>

	<?php if($publication->getType() == 'article' && $publication->getJournal()): ?>
		<div class="ficha --font-size-xxs">
			<div>
				<p><?php _e('Journal','ifgae-content-viewer'); ?></p>
				<p><?php echo $publication->getJournal(); ?></p>
			</div>
		</div>
	<?php endif; ?>		

</div>


        
        <?php endforeach; ?>

    <?php else: ?>
        
        <div class="alert alert-info pad">
            <strong><?php _e('Non atopado','ifgae-content-viewer'); ?></strong>
        </div>

    <?php endif; ?>

</div>
