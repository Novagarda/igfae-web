<div class="col-xs-12">
	<a href="<?php echo $event->getURL(); ?> " class="carga-loading">
		<div class="panel panel-default calendar-list-item">
			<div class="panel-body">

				<h5> <?php echo $event->getTitle(); ?> </h5>
				<ul class="list-unstyled">
					<?php if( $event->getDate() ): ?>
					<li>
						<strong><?php _e( 'Date', 'ifgae-content-viewer' ) ?></strong>: 
						<?php $dateDisplay = igfae_event_get_date_display($event); ?>
						<?php if( $dateDisplay && $dateDisplay['is_range'] ): ?>
							<?php echo $dateDisplay['start']; ?>
							<span> -> </span>
							<?php echo $dateDisplay['end']; ?>
						<?php else: ?>
							<?php if ($dateDisplay) : ?>
								<?php echo $dateDisplay['single']; ?><?php if ($dateDisplay['has_time']) echo ' (CET)'; ?>
							<?php endif; ?>
						<?php endif; ?>
					</li>
					<?php endif; ?>

					<?php if( $event->getPlace() ): ?>
					<li>
						<strong><?php _e( 'Place', 'ifgae-content-viewer' ) ?></strong>: <?php echo $event->getPlace(); ?>
					</li>
					<?php endif; ?>

					<?php if( method_exists($event, 'getSpeaker') && $event->getSpeaker() ): ?>
					<li>
						<strong><?php _e( 'Speaker', 'ifgae-content-viewer' ) ?></strong>: <?php echo esc_html($event->getToStringSpeaker()); ?>
					</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</a>
</div>