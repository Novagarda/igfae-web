<tr class="ifgae-table-row">
	<td class='ifgae-table-cell info-cell'>
	
	<?php if($item->getTitle()): ?>
		<h4 class='igfae-show-title'><?php echo $item->getTitle(); ?></h4>
	<?php endif; ?>
	<p class="igfae-show-pub-info">
		<?php if($item->getJournal()): ?>
			<?php echo $item->getJournal(); ?>,
		<?php endif; ?>

		<?php if($item->getVolume()): ?>
			<?php echo $item->getVolume(); ?>,
		<?php endif; ?>

		<?php if($item->getPages()): ?>
			<?php echo $item->getPages(); ?>
		<?php endif; ?>
		
		<?php if($item->getYear()): ?>
			(<?php echo $item->getYear(); ?>)
		<?php endif; ?>

		<span class="igfae-type type-article"><?php _e('Article' ,'ifgae-content-viewer'); ?></span>
	</p>

	<?php if($item->getDoi() && $item->getArxiv()): ?>

	<ul class ="list-inline ifgae-show-links">
		<?php if($item->getDoi()): ?>
			<li>
				<strong>DOI</strong>: 
				<a href="<?php echo $item->getUrlDoi(); ?>">
					<?php echo $item->getDoi(); ?>
				</a>
			</li>
		<?php endif; ?>

		<?php if($item->getArxiv()): ?>
			<li>
				<strong>ARXIV</strong>: 
				<a href="<?php echo $item->getUrlArxiv(); ?>">
					<?php echo $item->getArxiv(); ?>
				</a>
			</li>
		<?php endif; ?>
	</ul>

	<?php endif; ?>

	<?php if($item->getAuthor()): ?>
		<p class='igfae-show-main-author'><?php echo $item->getAuthor(); ?></p>
	<?php endif; ?>

	<?php if($item->getLocalAuthors()): ?>
		<p class='igfae-show-local-authors'><?php echo $item->getLocalAuthors(); ?></p>
	<?php endif; ?>

	<?php if($item->getAllAuthors()): ?>
		<a href="#authors-<?php echo $index ?>" data-toggle="collapse"><?php _e('Show all authors','ifgae-content-viewer'); ?></a>
		<div id="authors-<?php echo $index ?>" class="collapse">
	    	<p class='igfae-show-all-authors'><?php echo $item->getAllAuthors(); ?></p>
	  	</div>
	<?php endif; ?>
	</td>
</tr>