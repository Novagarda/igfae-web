<?php
class IgfaePrensa {
	protected $_title			= NULL;
	protected $_link			= NULL;
	protected $_date			= NULL;
	protected $_media			= NULL;
	protected $_media_type		= NULL;
	protected $_iso				= NULL;
	protected $_lang			= NULL;

	public function __construct( $title = NULL, $link = NULL, $date = NULL, $media = NULL, $media_type = NULL, $iso = NULL, $lang = NULL ) {
		$this->_title 		= $title; 	
		$this->_link 		= $link;
		$this->_date 		= $date;
		$this->_media 		= $media;
		$this->_media_type	= $media_type;
		$this->_iso			= $iso;
		$this->_lang		= $lang;
	}
	
	public static function fetch( $keywords = '', $type = null, $year = null, $page = null, $amount = 10 ) {
		$novas = [];
		
		$where = '';
		$where = IgfaePrensa::getSearchByKeyword( $keywords );

		if( $type && $type != 'ALL' ) {
			if (strlen($where)>0){
				$where .= " AND ";
			} else {
				$where .= " WHERE ";
			}
			$where .="(a.MediaType LIKE '{$type}')";
		}

		if ($year) {
			if (strlen($where)>0){
				$where .= " AND ";
			} else {
				$where .= " WHERE ";
			}
			$where .= "(YEAR(a.Date) = {$year})"; 
		}

		$sql = "SELECT
			a.Title 	as 'title',
			a.Link 		as 'link',
			a.Date 		as 'date',
			a.Media 	as 'media',
			a.MediaType as 'media_type',
			a.IsoCode 	as 'iso',
			a.Language 	as 'lang'
		FROM 
			IGFAE_Media a
			{$where}
		ORDER BY a.Date DESC";

		$pagination = IgfaePrensa::getPaginationInfo( $page, $amount, $sql );

		if ( !is_null($page) ) {
			$offset = ($page * $amount);
			$sql .= " LIMIT {$offset}, {$amount}";
		}

		$rows = igfae_fetch_data($sql);
/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Rows: ".print_r($rows, true)."\n";
		//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
		foreach ( $rows['rows'] as $row ) {			
			$novas[] = new IgfaePrensa(
				$row->title, 
				$row->link,
				$row->date,
				$row->media,
				$row->media_type,
				$row->iso,
				$row->lang,
			);
		}

		$info = [
			'projects' => $novas,
			'pagination' => $pagination
		];

		return $info;
	}

	private static function getPaginationInfo( $page, $amount, $sql ) {
		$result =null;

		if ( !is_null( $page ) ) {

			$rows = igfae_fetch_data($sql);
			$result = [
				'total' 		=> $rows['count'],
				'amount' 		=> $amount,
				'actualPage'	=> $page,
				'countPages'	=> intval(ceil($rows['count']/$amount))
			];
		}

		return $result;
	}

	private static function getSearchByKeyword($keywords) {
		$searchConditions = "";
		
		$parsed = preg_split( "/(,|;| )/", $keywords );

		if ( count($parsed) > 0 && $keywords != '' ) {
			$searchConditions .= "WHERE (";
			foreach ($parsed as $key => $keyword) {
				if ( $key > 0 ) {
					$searchConditions .= " OR ";
				}

				$searchConditions .= "(
					a.Title LIKE '%%{$keyword}%%' 
				OR
					a.Link LIKE '%%{$keyword}%%'
				OR
					a.Date LIKE '%%{$keyword}%%'
				OR
					a.Media LIKE '%%{$keyword}%%'
				OR
					a.MediaType LIKE '%%{$keyword}%%'
				)";
			}
			$searchConditions .= ")";
		}
		return $searchConditions;
	}

	public static function getAllGroups() {

		$info = [];
		$sql = "SELECT 
					MediaType AS 'media_type' 
				FROM 
					`IGFAE_Media`
				ORDER BY `MediaType` ASC";	

		$results = igfae_fetch_data($sql);

		$unicos = array();
		foreach ( $results['rows'] as $item ) {
			$gardar = true;
			foreach ($unicos as $key => $unico) {
				if($item->media_type == $unico){
					$gardar = false;
				}
			}
			if($gardar){
				$info[$item->media_type] = $item->media_type;
				array_push($unicos, $item->media_type);
			}
		}

		return $info;	
	}

	public static function getAllYears() {
		$sql = "SELECT DISTINCT
			YEAR(a.Date) as 'year'
		FROM 
			IGFAE_Media a
		ORDER BY 
			year
		DESC";
			
		$rows = igfae_fetch_data($sql);
		$result = [];

		/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Rows: ".print_r($rows, true)."\n";
		//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
		*/

		foreach ( $rows['rows'] as $row ) {
			$result[] = $row->year;
		}

		return $result;
	}

	public function getTitle() {
		return $this->_title;
	}

	public function getLink() {
		return $this->_link;
	}

	public function getDate() {
		return $this->_date;
	}

	public function getMedia() {
		return $this->_media;
	}

	public function getMediaType() {
		return $this->_media_type;
	}
	public function getIso() {
		return $this->_iso;
	}

	public function getLang() {
		return $this->_lang;
	}
}
