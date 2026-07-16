<?php 
	wp_enqueue_style( 'igfae-events-calendar' );
	wp_enqueue_style( 'igfae-fullcalendar' );
	wp_enqueue_style( 'igfae-fullcalendar-print' );

	wp_enqueue_script( 'igfae-moment' );
	wp_enqueue_script( 'igfae-fullcalendar' );
	wp_enqueue_script( 'igfae-events-calendar' );
	//https://fullcalendar.io/
?>

<div id="igfae-calendar-view">
	<div class="row">
		<div class="col-xs-12">
			<div id="igfae-events-calendar"></div>
		</div>
	</div>
</div>
