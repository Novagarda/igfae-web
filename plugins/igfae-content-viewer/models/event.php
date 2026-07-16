<?php

/**

	Clase general de la que heredan 

**/

class IgfaeEvent {
	protected $_id;			//ID OF EVENT
	protected $_type;		//TYPE OF EVENT
	protected $_title; 		//title
	protected $_place;		//place
	protected $_date;		//date
	protected $_image;		//image


	public static function fetch( $keywords = '', $date = NULL, $page = 0, $amount = 10, $type = NULL, $formatDate = 'Y', $order='DESC', $exclude = null, $featured = null, $upcoming_only = false ) {
		return IgfaeUnifiedEvent::fetch( $keywords, $date, $page, $amount, $type, $formatDate, $order, $exclude, $featured, $upcoming_only );
	}

	public static function getAllYears($type = NULL) {
		return IgfaeUnifiedEvent::getAllYears($type);
	}

	public static function getAvailableTypes() {
		return IgfaeUnifiedEvent::getAvailableTypes();
	}

	//GETTERS
	public function getDate($format = 'Y-m-d H:i:s') {
		$result = NULL;

		if ($this->_date) {
			$pos = strpos($format, 'H');
			if ( $pos === false || ( date('H:i:s', $this->_date) != '00:00:00' ) ) {
			    $result = date( $format, $this->_date );
			} else {
				$result = date( trim( substr( $format, 0, $pos ) ), $this->_date );
			}
		}

		return $result;
		
	}

	public function getType() {
		return $this->_type;
	}

	public function getMonthYear() {
		return $this->getDate('F-Y');
	}

	public function getYear() {
		return $this->getDate('Y');
	}

	public function getTitle() {
		return $this->_title;
	}
	
	public function getId() {
		return $this->_id;
	}

	public function getPlace() {
		return $this->_place;
	}

	public function getTypeName() {
		switch($this->_type){
			case 'seminar':
				$result = 'Seminar';
				break;
			case 'outreach':
				$result = 'Outreach';
				break;
			case 'talk':
				$result = 'Conference';
				break;
			default:
				$result = NULL;
		}
		return $result;
	}

	public function getImage() {
        if ( $this->_image ) return get_site_url() . $this->_image; //site_url( $this->_img );

        return NULL;
	}

	public function isAllDay() {
		//Por ahora suponemos que ningun evento es todo el dia
		return false;
	}

	public static final function fetchJSON() {
		$events = self::fetch( '', NULL, NULL, NULL, NULL );

		$result = [];

		if ( count( $events[ 'rows' ] ) > 0 ) {
			$events = $events['rows'];
		}

		foreach ($events as $event) {
			$result[] = $event->jsonSerialize();
		}

		/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Events: ".print_r($result, true)."\n";
    	//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
    	*/

 		return $result;
    }

    public static final function fetchFromDate( $limit = 3, $date = NULL, $order = 'ASC' ) {
   		
    	if ( !$date ) {
    		$date = date('Y-m-d H:i:s');
    	}

		$events = self::fetch( '', $date, NULL, NULL, NULL, 'Y-m-d H:i:s', $order);

		if ( count( $events[ 'rows' ] ) > 0 ) {
			$events = $events['rows'];
		}


		/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Events: ".print_r($result, true)."\n";
    	//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
    	*/

 		return array_slice( $events, 0, $limit );
    }

    public function getUrl() {
		$slug = $this->getType();
		if (!$slug) {
			$slug = 'talk';
		}
    	return home_url("/{$slug}/{$this->getId()}");
    }

    public function jsonSerialize() {
        $toJson =[
        	'title' 	=> $this->getTitle(),
            'start' 	=> $this->getDate(),
            /*
            'end' 		=> $this->getEndDate(),
            'speaker' 	=> $this->getSpeaker(),
            */
            'place'		=> $this->getPlace(),
            'allDay'	=> $this->isAllDay() 				//suponemos que los eventos no son todo el día (por ahora)
        ];

        if ($this->getId()) {
        	$toJson['url'] = $this->getUrl();
        }

        return $toJson;
    }
	
	//!PRIVATE
	public static function compareDates($a, $b) {
		$result = (date_create("@$a->_date") < date_create("@$b->_date"));
		/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Events: {$a->_date} < {$b->_date} = {$result}\n";
    	//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
    	*/
    	return $result;
	}
	public static function compareDatesInv($a, $b) {
		$result = (date_create("@$a->_date") > date_create("@$b->_date"));
		/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Events: {$a->_date} < {$b->_date} = {$result}\n";
    	//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
    	*/
    	return $result;
	}
}
