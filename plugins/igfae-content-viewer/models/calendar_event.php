<?php
class IgfaeEventCalendar implements JsonSerializable {
	protected $_id 			= NULL;
	protected $_title 		= NULL;
	protected $_startDate 	= NULL;
	protected $_endDate 	= NULL;
	protected $_speaker 	= NULL;
	protected $_place 		= NULL;
	
	public function __construct($id = NULL, $title = NULL, $startDate = NULL, $endDate = NULL, $speaker = NULL, $place = NULL ) {
		$this->_id 			= $id;
		$this->_title 		= $title;
		$this->_startDate 	= $startDate;
		$this->_endDate		= $endDate;
		$this->_place 		= $place;
		$this->_speaker 	= $speaker;
	}

	public static function all($limit=null, $order='DESC') {
		$sql = "SELECT 
		 	a.Title 		as 'title', 
		 	a.InitialDate 	as 'start', 
		 	a.FinalDate 	as 'end', 
		 	a.Speaker 		as 'speaker',
		 	a.Place 		as 'place'
		FROM 
			IGFAE_Calendar a
		ORDER BY 
			a.InitialDate,
			a.Title
		".$order;

		$results = igfae_fetch_data($sql);
		
		$info = [];

		foreach ( $results['rows'] as $key => $item ) {
			$info[] = new IgfaeEventCalendar(
				$key,
				$item->title, 
				$item->start,
				$item->end,
				$item->speaker,
				$item->place
			);
		}
		
		return $info;
	}

	public static function search( $limit=NULL, $order='DESC', $start=NULL, $end=NULL ) {
		$info = [];
		$searchConditions = "";
		
		if ($start && $end) {
			$searchConditions .=" WHERE (a.InitialDate BETWEEN '{$start}' AND '{$end}') OR (a.FinalDate BETWEEN '{$start}' AND '{$end}')";

		} else {
			if ($start) {
				$searchConditions .=" WHERE (a.InitialDate >= '{$start}') ";
			}
			if ( $end ) {
				$searchConditions .=" WHERE (a.FinalDate <= '{$end}') ";
			}
		}

		$sql = "SELECT 
			a.Title 		as 'title', 
		 	a.InitialDate 	as 'start', 
		 	a.FinalDate 	as 'end', 
		 	a.Speaker 		as 'speaker',
		 	a.Place 		as 'place'
		FROM 
			IGFAE_Calendar a
		{$searchConditions}	
		ORDER BY 
			a.InitialDate
		".$order;

		if ( !is_null( $limit ) ) {
			$sql .= " LIMIT {$limit}";
		}

		$results = igfae_fetch_data($sql);
		
		foreach ( $results['rows'] as $item ) {
			$info[] = new IgfaeEventCalendar(
				$key,
				$item->title, 
				$item->start,
				$item->end,
				$item->speaker,
				$item->place
			);
		}

		return $info;

	}

	public function getId() {
		return $this->_id;
	}

	public function getTitle() {
		return $this->_title;
	}

	public function getStartDate() {
		return $this->_startDate;
	}

	public function getEndDate() {
		return $this->_endDate;
	}

	public function getSpeaker() {
		return $this->_speaker;
	}

	public function getPlace() {
		return $this->_place;
	}

	public function isAllDay() {
		//Por ahora suponemos que ningun evento es todo el dia
		return false;
	}

	public function jsonSerialize() {
        return [
        	'id'		=> $this->getId(),
            'title' 	=> $this->getTitle(),
            'start' 	=> $this->getStartDate(),
            'end' 		=> $this->getEndDate(),
            'speaker' 	=> $this->getSpeaker(),
            'place'		=> $this->getPlace(),
            'allDay'	=> $this->isAllDay()
        ];
    }
}