<?php
class IgfaeThesis extends IgfaePublication {
	
//Habria que añadir el tutor (o tutores) que se obtienen a través de la tabla Tutors (el ID del tutor es PersonnelID1)
	protected $_tutors = NULL;

	public function __construct($title = NULL, $author = NULL, $date = NULL, $tutors = []) {
		$this->_type 	= 'thesis';
		$this->_title 	= $title; 	//title
		$this->_author 	= $author;	//first
		$this->_date	= $date;	//date
		$this->_tutors  = $tutors;	//tutors
	}

	public static function all($type=null, $year = NULL, $page = NULL, $amount = 10, $researchLine = null ) {
		$where = '';
	
		if ($year) {
			$where .= " WHERE (YEAR(a.DefenseDate) = {$year})"; 
		}

		if ($researchLine) {
/*
			$logInfo .= "[".date("d/m/Y - h:i:s")."] RP: ".print_r($researchLine, true)."\n";
			//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
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

		$sql = "SELECT 
			a.Title as 'title',
		 	a.DefenseDate as 'date', 
		 	a.PersonnelID as 'id_author',
			a.AuthorName as 'author_name',
			a.AuthorSurname as 'author_surname',
			a.ResearchLine as 'resLine',
			GROUP_CONCAT(CONCAT(a.TutorName, ',', a.TutorSurname) SEPARATOR '; ') AS 'tutors'
		FROM 
			IGFAE_Theses a
			{$where}
		GROUP BY 
			Title, defenseDate, PersonnelID, AuthorName, AuthorSurname
		ORDER BY 
			a.DefenseDate
		DESC";
		
		$pagination = IgfaeThesis::getPaginationInfo( $page, $amount, $sql );

		if ( !is_null($page) ) {
			$offset = ($page * $amount);
			$sql .= " LIMIT {$offset}, {$amount}";
		}

		$results = igfae_fetch_data($sql);
/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] These: ".print_r($results, true)."\n";
		//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
		$info = [];
		foreach ( $results['rows'] as $item ) {
			$obj = new IgfaeThesis( 
				$item->title, 
				[
					'name' 		=> $item->author_name,
					'surname' 	=> $item->author_surname
				], 
				$item->date
			);

			if($item->tutors){
				$tutors = explode(';', $item->tutors);
				foreach($tutors as $tutor) {
					$temp = explode(',', $tutor);
					if ( count($temp) > 1 ){
						$obj->_tutors[] = [
							'name' 		=> $temp[0],
							'surname'	=> $temp[1]
						];
					} elseif(count($temp) == 1) {
						$obj->_tutors[] = [
							'name' 	=> $temp[0]
						];
					}
				}
			}

			$info[] = $obj;
		}
		return ['rows' => $info, 'pagination' => $pagination];
	}

	public static function search($keywords ='', $type=null, $year = NULL, $page = NULL, $amount = 10, $researchLine = null ) {
		$info = [];

		$parsed = preg_split( "/(,|;| )/", $keywords );
		
		$searchConditions = "";

		if ( count($parsed) > 0 ) {
			foreach ($parsed as $key => $keyword) {
				if ( $key > 0 ) {
					$searchConditions .= " OR ";
				}

				$searchConditions .= "(
					a.Title LIKE '%%{$keyword}%%' COLLATE utf8mb4_bin 
				OR 
					a.DefenseDate LIKE '%%{$keyword}%%' COLLATE utf8mb4_bin 
				OR
					a.AuthorName LIKE '%%{$keyword}%%' COLLATE utf8mb4_bin 
				OR
					a.AuthorSurname LIKE '%%{$keyword}%%' COLLATE utf8mb4_bin 
				)";
			}
		
			if ($year) {
				if ( strlen( $searchConditions ) > 0 ) {
					$searchConditions .= " AND ";
				}
				$searchConditions .= " (YEAR(a.DefenseDate) = {$year}) "; 
			}

			if ($researchLine) {
				/*
				$logInfo .= "[".date("d/m/Y - h:i:s")."] RP: ".print_r($researchLine, true)."\n";
				//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
				*/
				if (strlen($searchConditions)>0){
					$searchConditions .= " AND ";
				} else {
					$searchConditions .= " WHERE ";
				}
				
				// Si hai comas, é dicir, si ven máis de un reseach programm como "RP1,RP2,RP3"
				if( strpos($researchLine, ',') !== false ) {
					$programas = explode(',', $researchLine);
					$searchConditions .= "(";
					foreach ($programas as $key => $programa) {
						$programa_limpo = str_replace(' ', '', $programa);
						if($key == 0){
							$searchConditions .= "a.ResearchLine LIKE '%%{$programa_limpo}%%'";
						}else{
							$searchConditions .= " OR a.ResearchLine LIKE '%%{$programa_limpo}%%'";
						}
					}
					$searchConditions .= " ) ";
	
				}else{
					$searchConditions .="(a.ResearchLine LIKE '{$researchLine}')";
				}
	
			}
			
			$sql = "SELECT
				a.Title as 'title',
			 	a.DefenseDate as 'date', 
			 	a.PersonnelID as 'id_author',
				a.AuthorName as 'author_name',
				a.AuthorSurname as 'author_surname',
				a.TutorName as 'tutor_name',
				a.TutorSurname as 'tutor_surname'
			FROM 
				IGFAE_Theses a
			WHERE
				{$searchConditions}
			ORDER BY a.DefenseDate DESC";

			$pagination = IgfaeThesis::getPaginationInfo( $page, $amount, $sql );

			if ( !is_null($page) ) {
				$offset = ($page * $amount);
				$sql .= " LIMIT {$offset}, {$amount}";
			}

			$results = igfae_fetch_data( $sql );
/*
			$logInfo .= "[".date("d/m/Y - h:i:s")."] Tese: ".print_r($results, true)."\n";
			//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
			foreach ( $results['rows'] as $item ) {
				$obj = new IgfaeThesis( 
					$item->title, 
					[
						'name' 		=> $item->author_name,
						'surname' 	=> $item->author_surname
					], 
					$item->date
				);

				if($item->tutors){
					$tutors = explode(';', $item->tutors);
					foreach($tutors as $tutor) {
						$temp = explode(',', $tutor);
						if ( count($temp) > 1 ){
							$obj->_tutors[] = [
								'name' 		=> $temp[0],
								'surname'	=> $temp[1]
							];
						} elseif(count($temp) == 1) {
							$obj->_tutors[] = [
								'name' 	=> $temp[0]
							];
						}
					}
				}

				$info[] = $obj;
			}
		}

		return ['rows' => $info, 'pagination' => $pagination];
	}

	private static function getPaginationInfo( $page, $amount, $sql ) {
		$result =null;

		if ( !is_null( $page ) ) {

			$rows = igfae_fetch_data($sql);

			$result = [
				'total' 		=> $rows['count'],
				'amount' 		=> $amount,
				'actualPage'	=> $page,
				'countPages'	=> intval(ceil( $rows['count']/$amount))
			];
		}

		return $result;
	}

	public static function getAllYears($type=null) {
		$sql = "SELECT DISTINCT
		 	YEAR(a.DefenseDate) as 'year'
		FROM 
			IGFAE_Theses a
		ORDER BY year
		DESC";

		$rows = igfae_fetch_data( $sql );

		$result = [];

		foreach ( $rows['rows'] as $row ) {
			$result[] = $row->year;
		}

		return $result;
	}

	public function getTutors() {
		$result = [];
		$result = $this->_tutors;
		
		return $result;
	}

	public function getAuthor() {
		$result = '';
		foreach ($this->_author as $info) {
			$result .= $info;
			$result .= ' ';
		}
		
		return $result;
	}

}
