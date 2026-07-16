<?php
class IgfaeProject {
	protected $_title			= NULL;
	protected $_reference		= NULL;
	protected $_iniDate			= NULL;
	protected $_finDate			= NULL;
	protected $_group			= [];
	protected $_researchLine	= [];
	protected $_personal		= [];
	protected $_agency			= NULL;
	protected $_program			= NULL;
	protected $_rp				= NULL;

	public function __construct( $title = NULL, $reference = NULL, $iniDate = NULL, $finDate = NULL, $group = [], $personal = [], $agency = NULL, $researchLine = NULL, $program = NULL ) {
		$this->_title 			= $title; 	
		$this->_reference 		= $reference;
		$this->_iniDate 		= $iniDate;
		$this->_finDate 		= $finDate;
		$this->_group			= $group;
		$this->_personal		= $personal;
		$this->_agency			= $agency;
		$this->_researchLine	= $researchLine;
		$this->_program			= $program;
	}
	
	public static function fetch( $keywords = '', $type = null, $year = null, $page = null, $amount = 10, $researchLine = null ) {
		$projects = [];

		$limitResult = "";
		
		        
		error_log("[" . date("d/m/Y - h:i:s") . "] project type: " . print_r($type, true));

		$where = IgfaeProject::getSearchByKeyword( $keywords );

		if( $type ) {
			if (strlen($where)>0){
				$where .= " AND ";
			} else {
				$where .= " WHERE ";
			}

			// Si hai comas, é dicir, si ven máis de un reseach programm como "RP1,RP2,RP3"
			if( strpos($type, ',') !== false ) {
				$programas = explode(',', $type);
				$where .= "(";
				foreach ($programas as $key => $programa) {
					$programa_limpo = str_replace(' ', '', $programa);
					if($key == 0){
						$where .= "a.ResearchLine LIKE '%%{$programa_limpo}%%'";
					}else{
						$where .= " OR a.ResearchLine LIKE '%%{$programa_limpo}%%'";
					}
				}
				$where .= " ) ";

			}else{
				$where .="(a.ResearchLine LIKE '{$type}')";
			}
		}
	
		if ($year) {
			if (strlen($where)>0){
				$where .= " AND ";
			} else {
				$where .= " WHERE ";
			}

			$where .= "(YEAR(a.initialDate) = {$year})"; 
		}

		if ($researchLine) {
			if (strlen($where)>0){
				$where .= " AND ";
			} else {
				$where .= " WHERE ";
			}

			// Si hai comas, é dicir, si ven máis de un reseach programm como "RP1,RP2,RP3"
			if( strpos($researchLine, ',') !== false ) {
				$programas = explode(',', $researchLine);
				$where .= "(";
				foreach ($programas as $key => $programa) {
					$programa_limpo = str_replace(' ', '', $programa);
					if($key == 0){
						$where .= "a.ResearchLine LIKE '%%{$programa_limpo}%%'";
					}else{
						$where .= " OR a.ResearchLine LIKE '%%{$programa_limpo}%%'";
					}
				}
				$where .= " ) ";

			}else{
				$where .="(a.ResearchLine LIKE '{$researchLine}')";
			}

		}
//		(SELECT GROUP_CONCAT( CONCAT(b.Acronym,'#',b.Name) SEPARATOR '|') FROM ResearchProgram b NATURAL JOIN PersonnelResearchProgram c WHERE c.PersonnelID=a.PI) as 'program',
		
		$sql = "SELECT
			a.ProjectTitle as 'title',
			a.ProjectReference as 'reference',
			a.USCProjectReference as 'uscreference',
			a.IGFAEProjectReference as 'igfaereference',
			a.GroupName as 'group',
			a.ResearchLine as 'resLine',
			a.InitialDate as 'iniDate',
			a.FinalDate as 'finDate',
			a.PI as 'personal',
			(SELECT GROUP_CONCAT( CONCAT(b.Acronym) SEPARATOR '|') FROM ResearchProgram b NATURAL JOIN ProjectResearchProgram c WHERE c.ProjectID=a.IGFAEProjectReference) as 'program',
			(SELECT GROUP_CONCAT( CONCAT(b.ResearchProgramID) SEPARATOR '|') FROM ResearchProgram b WHERE b.Acronym=program) as 'rp',
			a.AgencyName as 'agency'
		FROM 
			IGFAE_Projects a
			{$where}
		ORDER BY a.InitialDate DESC";


		$pagination = IgfaeProject::getPaginationInfo( $page, $amount, $sql );

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
			$personnel = [];
			
			if($row->personal){
				$personnel = explode(',', $row->personal);
			}
			
			$projects[] = new IgfaeProject(
				$row->title, 
				$row->reference,
				$row->iniDate,
				$row->finDate,
				[$row->group],
				$personnel,
				$row->agency,
				$row->resLine,
				$row->program,
			);
		}

		$info = [
			'projects' => $projects,
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
					a.ProjectTitle LIKE '%%{$keyword}%%' 
				OR
					a.InitialDate LIKE '%%{$keyword}%%'
				OR
					a.FinalDate LIKE '%%{$keyword}%%'
				OR
					a.ProjectReference LIKE '%%{$keyword}%%'
				OR
					a.PI LIKE '%%{$keyword}%%'
				OR 
					a.AgencyName LIKE '%%{$keyword}%%'
				)";
			}
			$searchConditions .= ")";
		}
		return $searchConditions;
	}

	public static function getAllYears() {
		$sql = "SELECT DISTINCT
			YEAR(a.InitialDate) as 'year'
		FROM 
			IGFAE_Projects a
		ORDER BY 
			year
		DESC";
			
		$rows = igfae_fetch_data($sql);
		$result = [];

		foreach ( $rows['rows'] as $row ) {
			$result[] = $row->year;
		}

		return $result;
	}

	public static function getAllGroups() {
/*		
		$sql = "SELECT DISTINCT
			a.GroupName as 'group'
		FROM 
			IGFAE_Projects a
		ORDER BY 
			a.GroupName
		DESC";
			
		$rows = igfae_fetch_data($sql);
		$result = [];

		foreach ( $rows['rows'] as $row ) {
			$result[] = $row->group;
		}

		return $result;
*/
		$info = [];
		$sql = "SELECT 
					Acronym AS 'sax', 
					`ResearchProgramID` AS 'rpx' 
				FROM 
					`ResearchProgram` 
				GROUP BY Acronym, `ResearchProgramID` 
				ORDER BY `ResearchProgramID` ASC";

		$results = igfae_fetch_data($sql);

		foreach ( $results['rows'] as $item ) {
			if ($item->rpx && $item->sax && $item->sax != '-' && $item->sax != 'SAH') {

				foreach( explode(', ', $item->rpx) as $line ) {
					if ( !array_key_exists($line, $info) ) {
						$info['RP'.$line] = $item->sax;
					}
				}
			} 
		}
		/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Rows: ".print_r($info, true)."\n";
		//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
		*/
		return $info;	
	}

	public function getTitle() {
		return $this->_title;
	}

	public function getReference() {
		return $this->_reference;
	}

	public function getIniDate() {
		return $this->_iniDate;
	}

	public function getFinDate() {
		return $this->_finDate;
	}

	public function getFinYear() {
		return strtok($this->_finDate, '-');
	}
	public function getIniYear() {
		return strtok($this->_iniDate, '-');
	}

	public function getFinalMonthYear() {
		$date = strtotime($this->_finDate);
		return date('F-Y', $date);
	}

	public function getInitialMonthYear() {
		$date = strtotime($this->_iniDate);
		return date('F-Y', $date);
	}

	public function getGroups($index = NULL) {
		if (!is_null($index)) {
			return $this->_group[$index];
		}
		return $this->_group;
	}

	public function getResearchLine() {
        return $this->_researchLine;
    }

	public function getPersonals() {
		return $this->_personal;
	}

	public function getAgency() {
		return $this->_agency;
	}

	public function getProgram() {
		return $this->_program;
	}
}
