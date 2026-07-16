<?php

function igfae_event_get_date_display($event) {
	if (!$event || !method_exists($event, 'getDate') || !$event->getDate()) {
		return null;
	}

	$startDay = $event->getDate('Y-m-d');
	$endDay = null;
	if (method_exists($event, 'getFinalDate') && $event->getFinalDate()) {
		$endDay = $event->getFinalDate('Y-m-d');
	}

	$hasRealRange = !empty($endDay) && ($startDay !== $endDay);
	if ($hasRealRange) {
		return [
			'is_range' => true,
			'start' => $event->getDate('d/m/Y'),
			'end' => $event->getFinalDate('d/m/Y'),
			'has_time' => false,
		];
	}

	$single = $event->getDate('d/m/Y H:i');
	return [
		'is_range' => false,
		'single' => $single,
		'has_time' => strpos($single, ':') !== false,
	];
}
