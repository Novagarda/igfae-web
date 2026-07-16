<?php
	$typeLabel = $item->getTypeName();
?>

<article class="list01">
	
	<div class="left">

		<?php if( $item->getDate() ): ?>
			<div class="date --font-size-xxs">
				<?php $dateDisplay = igfae_event_get_date_display($item); ?>
				<?php if( $dateDisplay && $dateDisplay['is_range'] ): ?>
					<p><?php echo $dateDisplay['start']; ?></p>
					<p><?php echo $dateDisplay['end']; ?></p>
				<?php else: ?>
					<?php if ($dateDisplay): ?>
						<p><?php echo $dateDisplay['single']; ?><?php if ($dateDisplay['has_time']) echo ' (CET)'; ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>		

		<?php if (!empty($typeLabel)): ?>
			<span class="btnazul btn-xxs"><?php echo esc_html($typeLabel); ?></span>
		<?php endif; ?>

	</div>
				
	<div class="middle">
		
		<?php if( $item->getImage() ): ?>
            <div class="img-blue">
                <img src="<?php echo $item->getImage(); ?>" />
            </div>
		<?php endif; ?>

	</div>

	<div class="right">
		<?php if ($item->getTitle()) : ?>
			<?php if ($item->getURL()) : ?>
				<a href="<?php echo $item->getURL(); ?>" class="stretched-link carga-loading bread-link" data-breadcurrent="axenda" data-breadparent="sociedade">
			<?php endif; ?>

			<h3 class="title --font-size-ms azul"><?php echo $item->getTitle(); ?></h3>

			<?php if ($item->getURL()) : ?>
				</a>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ( method_exists($item, 'getSpeaker') && $item->getSpeaker() ) : ?>
			<p class="speaker --font-size-xxs"><?php echo $item->getToStringSpeaker(); ?></p>
		<?php endif; ?>

		<?php if ($item->getPlace()) : ?>
			<p class="place --font-size-xxs"><?php echo $item->getPlace(); ?></p>
		<?php endif; ?>

	</div>

	<?php include 'partials/arrow.php'; ?>

</article>