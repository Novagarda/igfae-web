<?php
class IgfaeArticle extends IgfaePublication {
	//protected $_date 			= NULL;		//date
	protected $_journal         = NULL; 	//journal
	protected $_volume			= NULL;		//volume
	protected $_pages			= NULL;		//pages
	protected $_doi				= NULL;		//doi
	protected $_arxiv			= NULL;		//arxiv
	protected $_local_authors	= NULL;		//local authors
	protected $_all_authors		= NULL;		//all_authors

	public function __construct($title = NULL, $author = NULL, $date = NULL, $journal = NULL, $volume = NULL, $pages = NULL, $doi = NULL, $arxiv = NULL, $local_authors = [], $all_authors = [] ) {
		$this->_type 			= 'article';
		$this->_title 			= $title; 	//title
		$this->_author 			= $author;	//first_author
		$this->_date 			= $date;	//date
		$this->_journal 		= $journal;
		$this->_volume			= $volume;
		$this->_doi				= $doi;
		$this->_arxiv			= $arxiv;
		$this->_local_authors	= $local_authors;
		$this->_pages			= $pages;
		$this->_all_authors		= $all_authors;
	}

	public static function all($type=null, $year = NULL, $page = NULL, $amount = 10, $researchLine = null ) {
		$where = '';
	
		if ($year) {
			$where .= " WHERE (YEAR(a.Date) = {$year})"; 
		}
		/*
		$sql = "SELECT 
		 	a.ArticleTitle as 'title', 
		 	a.Date as 'date', 
		 	b.FullName as 'journal', 
		 	a.Volume as 'volume',
		 	a.NumberOfPages as 'pages',
		 	a.DOI as 'doi',
		 	a.ArxivRef as 'arxiv',
		 	a.LocalAuthors as 'local_authors',
		 	a.AllAuthors as 'all_authors'
		FROM 
			Articles a
		JOIN 
			Journals b
		ON
			((a.JournalID = b.JournalID) {$searchYear})
		ORDER BY 
			a.date
		DESC
		";
*/
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
			a.ArticleTitle as 'title', 
			a.DOI as 'doi', 
			a.Volume as 'volume', 
			a.Date as 'date', 
			a.FullName as 'author', 
			a.Pages as 'pages', 
			a.JournalRef as 'journal', 
			a.ArxivRef as 'arxiv', 
			a.ResearchLine as 'resLine',
			a.AuthorList as 'local_authors', 
			a.AllAuthors as 'all_authors' 
		FROM 
			IGFAE_Articles a
		{$where}
		ORDER BY a.date DESC";

		$pagination = IgfaeArticle::getPaginationInfo( $page, $amount, $sql );

		if ( !is_null($page) ) {
			$offset = ($page * $amount);
			$sql .= " LIMIT {$offset}, {$amount}";
		}

		$publications = igfae_fetch_data($sql);
		/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Article: ".print_r($publications, true)."\n";
		//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
		*/
		$info = [];
		foreach ( $publications['rows'] as $publication ) {
			$info[] = new IgfaeArticle(
				$publication->title, 
				$publication->author, 
				$publication->date, 
				$publication->journal, 
				$publication->volume, 
				$publication->pages, 
				$publication->doi, 
				$publication->arxiv, 
				$publication->local_authors, 
				$publication->all_authors
			);
		}
		
		return ['rows' => $info, 'pagination' => $pagination];
	}

	public static function search( $keywords = '', $type=null, $year = NULL, $page = NULL, $amount = 10, $researchLine = null ) {
		$info = [];
/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] researchLine: ".print_r($researchLine, true)."\n";
		//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
		$parsed = preg_split( "/(,|;| )/", $keywords );
		
		$searchConditions = "";

		if ( count( $parsed ) > 0 ) {
			
			if ( strlen( $searchConditions ) > 0) {
				$searchConditions .= ' AND ';
			} else {
				$searchConditions .= ' WHERE ';
			}

			foreach ($parsed as $key => $keyword) {
				if ( $key > 0 ) {
					$searchConditions .= " OR ";
				}

				$searchConditions .= "(
					a.AuthorList LIKE '%%{$keyword}%%'
				OR
					a.ArticleTitle LIKE '%%{$keyword}%%' 
				OR
					a.FullName LIKE '%%{$keyword}%%'
				OR
					a.AllAuthors LIKE '%%{$keyword}%%' 
				)";
			}
	
			if ($year) {
				if ( strlen( $searchConditions ) > 0) {
					$searchConditions .= ' AND ';
				} else {
					$searchConditions .= ' WHERE ';
				}
				
				$searchConditions .= " (YEAR(a.Date) = {$year})"; 
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
				a.ArticleTitle as 'title', 
				a.DOI as 'doi', 
				a.Volume as 'volume', 
				a.Date as 'date', 
				a.FullName as 'author', 
				a.Pages as 'pages', 
				a.JournalRef as 'journal', 
				a.ArxivRef as 'arxiv', 
				a.AuthorList as 'local_authors', 
				a.AllAuthors as 'all_authors' 
			FROM 
				IGFAE_Articles a
				{$searchConditions}
			ORDER BY 
				a.date
			DESC";

			$pagination = IgfaeArticle::getPaginationInfo( $page, $amount, $sql );

			if ( !is_null($page) ) {
				$offset = ($page * $amount);
				$sql .= " LIMIT {$offset}, {$amount}";
			}

			$publications = igfae_fetch_data( $sql );

			foreach ( $publications['rows'] as $publication ) {
				$info[] = new IgfaeArticle(
					$publication->title, 
					$publication->author, 
					$publication->date, 
					$publication->journal, 
					$publication->volume, 
					$publication->pages, 
					$publication->doi, 
					$publication->arxiv, 
					$publication->local_authors, 
					$publication->all_authors
				);
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
		 	YEAR(a.Date) as 'year'
		FROM 
			IGFAE_Articles a
		ORDER BY 
			year
		DESC";

		$rows = igfae_fetch_data( $sql );

		$result = [];

		foreach ( $rows['rows'] as $row ) {
			$result[] = $row->year;
		}
		
		return $result;
	}

	public function getJournal() {
		return $this->_journal;
	}

	public function getVolume() {
		return $this->_volume;
	}

	public function getPages() {
		return $this->_pages;
	}

	public function getDoi() {	
		return $this->_doi;
	}

	public function getUrlDoi() {
		return "http://dx.doi.org/{$this->_doi}";
	}
	
	public function getArxiv() {
		return $this->_arxiv;
	}
	
	public function getUrlArxiv() {
		return "http://es.arxiv.org/abs/{$this->_arxiv}";
	}

	public function getLocalAuthors() {
		return $this->_local_authors;
	}

	public function getAllAuthors() {
		return $this->_all_authors;
	}
}
