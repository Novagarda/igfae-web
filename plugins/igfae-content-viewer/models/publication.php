<?php

class IgfaePublication {
	protected $_type;
	protected $_title; 		//title
	protected $_author;		//first_author
	protected $_date;

	//FetchAll
	public static function all( $type = NULL, $year = NULL, $page = NULL, $amount = 10, $researchLine = null ) {
		$result = [];
		
		switch($type) {
			case 'article':
				$result = IgfaeArticle::all( $type, $year, $page, $amount, $researchLine );
			break;

			case 'thesis':
				$result = IgfaeThesis::all( $type, $year, $page, $amount, $researchLine );
			break;

			default:
				$articles = IgfaeArticle::all($type, $year, NULL, NULL, $researchLine )['rows'];
				$thesis = IgfaeThesis::all($type, $year, NULL, NULL, $researchLine)['rows'];
				
				if( (count( $articles ) > 0) && (count( $thesis ) > 0) ) {

					$merged = array_merge( $articles, $thesis );
					usort($merged, ['IgfaePublication',"compareDates"]);

					if ( !is_null($page) ) {
						$result['rows'] = IgfaePublication::paginate( $merged, $page, $amount );

						$result['pagination'] = [
							'total' 		=> count($merged),
							'amount' 		=> $amount,
							'actualPage'	=> $page,
							'countPages'	=> intval(ceil(count($merged)/$amount))
						];
					} else {
						$result['rows'] = $merged;
					}
				} elseif ( count( $articles ) > 0 ) {
					if ( !is_null($page) ) {
						$result['rows'] = IgfaePublication::paginate( $articles, $page, $amount );
						$result['pagination'] = [
							'total' 		=> count($articles),
							'amount' 		=> $amount,
							'actualPage'	=> $page,
							'countPages'	=> intval(ceil(count($articles)/$amount))
						];
					} else {
						$result['rows'] = $articles;
					}
				} elseif ( count( $thesis ) > 0 ) {
					if ( !is_null($page) ) {
						$result['rows'] = IgfaePublication::paginate( $thesis, $page, $amount );

						$result['pagination'] = [
							'total' 		=> count($thesis),
							'amount' 		=> $amount,
							'actualPage'	=> $page,
							'countPages'	=> intval(ceil(count($thesis)/$amount))
						];
					} else {
						$result['rows'] = $thesis;
					}
				}
			break;
		}
		/*
			$logsInfo = "[".date("d/m/Y - h:i:s")."] Result: ".print_r($result, true)."\n";
			//file_put_contents(IGFAE_PATH.'custom.log', $logsInfo, FILE_APPEND);
			*/
		return $result;
	}

	//Search
	public static function search($keywords='', $type = NULL, $year = NULL, $page = NULL, $amount = 10, $researchLine = null) {
		$result = [];

		switch($type) {
			case 'article':
				$result = IgfaeArticle::search($keywords, $type, $year, $page, $amount, $researchLine );
			break;

			case 'thesis':
				$result = IgfaeThesis::search($keywords, $type, $year, $page, $amount, $researchLine );
			break;

			default:
				$articles = IgfaeArticle::search($keywords, $type, $year, $page, $amount, $researchLine)['rows'];
				$thesis = IgfaeThesis::search($keywords, $type, $year, $page, $amount, $researchLine)['rows'];

				if( (count( $articles ) > 0) && (count( $thesis ) > 0) ){
					$merged = array_merge( $articles, $thesis );
					usort($merged, ['IgfaePublication',"compareDates"]);

					if ( !is_null($page) ) {
						$result['rows'] = IgfaePublication::paginate( $merged, $page, $amount );
						$result['pagination'] = [
							'total' 		=> count($merged),
							'amount' 		=> $amount,
							'actualPage'	=> $page,
							'countPages'	=> intval(ceil(count($merged)/$amount))
						];
					} else {
						$result['rows'] = $merged;
					}
				} elseif ( count( $articles ) > 0 ) {
					if ( !is_null($page) ) {
						$result['rows'] = IgfaePublication::paginate( $articles, $page, $amount );
						$result['pagination'] = [
							'total' 		=> count($articles),
							'amount' 		=> $amount,
							'actualPage'	=> $page,
							'countPages'	=> intval(ceil(count($articles)/$amount))
						];
					} else {
						$result['rows'] = $articles;
					}
				} elseif ( count( $thesis ) > 0 )  {
					if ( !is_null($page) ) {
						$result['rows'] = IgfaePublication::paginate( $thesis, $page, $amount );

						$result['pagination'] = [
							'total' 		=> count($thesis),
							'amount' 		=> $amount,
							'actualPage'	=> $page,
							'countPages'	=> intval(ceil(count($thesis)/$amount))
						];
					} else {
						$result['rows'] = $thesis;
					}
				}
			break;
		}
		
		return $result;
	}

	public static function getAllYears($type = NULL) {
		$result = [];

		$articles = IgfaeArticle::getAllYears();
		$thesis = IgfaeThesis::getAllYears();

		switch ($type) {
			case 'article':
				$result = $articles;
				break;
			case 'thesis':
				$result = $thesis;
				break;
			default:
				if( (count( $articles ) > 0) && (count( $thesis ) > 0) ){
					$result = array_merge( $articles, $thesis );
					//usort($result, ['IgfaePublication',"compareDates"]);
				} elseif ( count( $articles ) > 0 ) {
					$result = $articles;
				} elseif ( count( $thesis ) > 0 )  {
					$result = $thesis;
				}
				break;
		}

		return $result;
	}

	private static function paginate($list, $page = 0, $amount = 10 ) {
		$result = NULL;

		if( is_array( $list ) ) {
			$offset = ($page * $amount);
			$result = array_slice( $list, $offset, $amount ); 
		}

		return $result;
	}

	//GETTERS
	public function getDate() {
		return $this->_date;
	}

	public function getMonthYear() {
		$date = strtotime($this->_date);
		return date('F-Y', $date);
	}

	public function getType() {
		return $this->_type;
	}

	public function getYear() {
		return strtok($this->_date, '-');
	}

	public function getTitle() {
		return $this->_title;
	}

	public function getAuthor() {
		return $this->_author;
	}

	//!PRIVATE
	private static function compareDates($a, $b) {
		$dateA = strtotime($a->_date);
		$dateB = strtotime($b->_date);

		if ($dateA === $dateB) {
			return 0;
		}

		// Descendente: más reciente primero
		return ($dateA > $dateB) ? -1 : 1;
	}

}