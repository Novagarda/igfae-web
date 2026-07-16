<?php
	if ( $limit ) {
		$events = IgfaeEvent::fetchFromDate( $limit );
		//$events = IgfaeEventCalendar::search( $limit, 'ASC', $now );
	} else {
		//$events = IgfaeEventCalendar::search();
		IgfaeEvent::fetchFromDate( );
	}
?>
<div class="igfae-events-list">
	<div class="container-title">
		<h3 class="">
			<strong><?php _e('Next Events'); ?></strong>
		</h3>
	</div>
	<div class="row">
		<?php 
		foreach( $events as $event ) {
			igfae_get_template_part( 'events', 'list-item', [
				'event' => $event
			]);
		} 
		?>
	</div>
</div>
