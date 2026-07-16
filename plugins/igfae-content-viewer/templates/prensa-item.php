<article class="list01 con-frecha">
	
	<div class="left">

		<?php echo ($item->getDate()) ? '<div class="date --font-size-xxs">'.$item->getDate().'</div>':''; ?>
		<?php echo ($item->getMediaType()) ? '<span class="btnazul btn-xxs">'.$item->getMediaType().'</span>':''; ?>

	</div>
	
	<div class="right flex">

		<?php if($item->getTitle()){ ?>
			<h3 class="--font-size-xs">
				<?php echo ($item->getLink()) ? '<a href="'.$item->getLink().'" class="text-left stretched-link" target="_blank">':''; ?>
					<?php echo $item->getTitle(); ?>
				<?php echo ($item->getLink()) ? '</a>':''; ?>
			</h3>
		<?php } ?>
		<div class="espacio"></div>
		<?php echo ($item->getMedia()) ? '<div class="--font-size-xxs --spacing-s-t con-punto">'.$item->getMedia().'</div>':''; ?>

	</div>

	<div class="arrow">
		<svg xmlns="http://www.w3.org/2000/svg" width="18" height="17" viewBox="0 0 18 17" fill="none">
			<path d="M9.44103 0.0892155L17.918 8.50457L9.44103 16.9199L8.46915 15.9582L16.0282 8.50457L8.46915 1.05097L9.44103 0.0892155Z" fill="#1E1E1C"/>
			<path d="M17.1367 9.16992L-0.00164223 9.16992L-0.00164211 7.83415L17.1367 7.83415L17.1367 9.16992Z" fill="#1E1E1C"/>
		</svg>
	</div>
</article>