<?php
class IgfaePersonal {
	protected $_id 				= NULL; //Name
	protected $_name 			= NULL; //Name
	protected $_surname 		= NULL; //Surname
	protected $_groups 			= NULL; //Group
    protected $_group       = NULL; //Group
	protected $_phone 			= NULL; //Phone
	protected $_email 			= NULL; //Email
	protected $_room 			= NULL; //Room
    protected $_building    = NULL; //Building
	protected $_category		= NULL; //Category
	protected $_webpage 		= NULL; //Webpage
    protected $_area        = []; //Area
    protected $_researchProg = [];
    protected $_researchProgRaw = NULL;
    protected $_researchLine = [];
    protected $_program     = NULL; //Program
    protected $_twitter     = NULL; //Twitter
    protected $_linkedin    = NULL; //Linkedin
    protected $_projects    = []; //Projects
    protected $_publications    = []; //Publications
    protected $_researchid  = NULL; //ResearchID
    protected $_orcid       = NULL; //OrcidID
    protected $_inspireid  	= NULL; //InspireID
    protected $_scopus      = NULL; //Scopus Author ID
    protected $_img         = NULL; //Image URL
    protected $_description = NULL; //Description

    public function __construct( $values=[] ) {
        if ( is_array( $values ) && !empty( $values ) ) {
            foreach ( $values as $key => $value ) {
                $key = "_" . $key;

                if ( property_exists ( $this , $key ) ) {
                    $this->$key = $value;
                }
            }
        }
	}

	public static function fetch( $keywords = '', $type = null, $researchLine = null, $id = null, $amount = null) {
		$info = [];
		$searchConditions = "";

/*		        
        $logInfo .= "[".date("d/m/Y - h:i:s")."] cat: ".print_r($type, true)."\n";
        //file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND); 
*/
		if ( $keywords != '' ) {
			
			if ( strlen( $searchConditions ) > 0 ) {
				$searchConditions .= ' AND ';
			} else{
				$searchConditions .= ' WHERE ';
			}

			$parsed = preg_split( "/(,|;| )/", $keywords );

			if ( count($parsed) > 0 ) {
				foreach ($parsed as $key => $keyword) {
					if ( $key > 0 ) {
						$searchConditions .= " OR ";
					}

					$searchConditions .= "(
						a.Name LIKE '%%{$keyword}%%' 
					OR
						a.Surname LIKE '%%{$keyword}%%' 
					OR
						a.EMail LIKE '%%{$keyword}%%' 
					OR
						a.GroupName LIKE '%%{$keyword}%%' 
					)";
				}
			}
		}

		if ( !is_null( $type ) ) {

			if ( strlen( $searchConditions ) > 0 ) {
				$searchConditions .= ' AND ';
			} else{
				$searchConditions .= ' WHERE ';
			}

			$searchConditions .= "( a.Category = '{$type}'  )";
		}

		if ( !is_null( $researchLine ) ) {
			if ( strlen( $searchConditions ) > 0 ) {
				$searchConditions .= ' AND ';
			} else{
				$searchConditions .= ' WHERE ';
			}

			// Si hai comas, é dicir, si ven máis dun reseach programm como "RP1,RP2,RP3"
			if( strpos($researchLine, ',') !== false ) {
				$programas = explode(',', $researchLine);
				$searchConditions .= "(";
				foreach ($programas as $key => $programa) {
					$programa_limpo = str_replace(' ', '', $programa);
					if($key == 0){
						$searchConditions .= "a.`Research Line` LIKE '%%{$programa_limpo}%%'";
					}else{
						$searchConditions .= " OR a.`Research Line` LIKE '%%{$programa_limpo}%%'";
					}
				}
				$searchConditions .= " ) ";

			}else{
				if($researchLine != 'ZZ'){
					$searchConditions .= "(a.`Research Line` LIKE '%%{$researchLine}%%' ) ";
				} else {
					$searchConditions .= "(a.`Research Line` is NULL) ";
				}
			}

		}

		if ( !is_null( $id ) ) {
			if ( strlen( $searchConditions ) > 0 ) {
				$searchConditions .= ' AND ';
			} else{
				$searchConditions .= ' WHERE ';
			}
			$searchConditions .= "( a.PersonnelID = '{$id}'  )";
		}

		$allGroups = self::getAllGroupNames();

		$sql = "SELECT 
                    a.`PersonnelID`             as 'id',
                    a.`Name`                    as 'name',
                    a.`Surname`                 as 'surname',
                    a.`OrcID`                   as 'orcId',
                    a.`InspireID`               as 'inspireId',
                    a.`ScopusID`                as 'scopusId',
                    a.`ResearcherID`            as 'researchId',
                    a.`Webpage`                 as 'webpage',
                    a.`Category`                as 'category',
                    a.`Phone`                   as 'phone',
                    a.`Building`                as 'building',
                    a.`Room`                    as 'room',
                    a.`EMail`                   as 'email',
                    a.`GroupName`               as 'group',
                    a.`Research Line`           as 'line',
		     GROUP_CONCAT(a.`Research Line`  SEPARATOR ', ') AS 'lines',
                    (SELECT GROUP_CONCAT( CONCAT(b.Acronym,'#',b.Name) SEPARATOR '|') FROM ResearchProgram b NATURAL JOIN PersonnelResearchProgram c WHERE c.PersonnelID=a.PersonnelID) as 'program',
                    (SELECT GROUP_CONCAT( b.Name SEPARATOR ', ') FROM ResearchProgram b NATURAL JOIN PersonnelResearchProgram c WHERE c.PersonnelID=a.PersonnelID) as 'lineName',
                    (SELECT GROUP_CONCAT( b.Name SEPARATOR ', ') FROM ResearchProgram b NATURAL JOIN PersonnelResearchProgram c WHERE c.PersonnelID=a.PersonnelID) as 'programName',
                    a.`Description`             as 'description',
                    a.`Twitter`                 as 'twitter',
                    a.`LinkedIn`                as 'linkedin',
                    a.`ReasearchArea`           as 'area',
                    a.`ResearchAreaURL`         as 'areaUrl',
                    (SELECT GROUP_CONCAT( CONCAT(b.Acronym) SEPARATOR '|') FROM ResearchProgram b NATURAL JOIN PersonnelResearchProgram c WHERE c.PersonnelID=a.PersonnelID) as 'areaCode',
                    a.`PictureURL`              as 'img',
                    (SELECT GROUP_CONCAT( Concat(b.ArticleTitle,'#',b.DOI) ORDER BY b.`Date` DESC  SEPARATOR '|') FROM IGFAE_Articles b JOIN ProductionProjectAuthor c ON c.ProductionProjectID=b.ArticleID WHERE c.PersonnelID = a.PersonnelID) as 'articles',
                    (SELECT GROUP_CONCAT( b.ProjectTitle  ORDER BY b.`InitialDate` DESC SEPARATOR '|') FROM IGFAE_Projects b NATURAL JOIN ProjectPersonnel c WHERE c.PersonnelID = a.PersonnelID) as 'projects'
                FROM `IGFAE_Personnel` a
				{$searchConditions}
			GROUP BY a.Name, a.Surname, a.Webpage, a.Category, a.Phone, a.EMail
			ORDER BY 
				a.Surname,
				a.Name
			ASC";

		if ( !is_null( $amount ) ) {	
			$sql .= " LIMIT {$amount}";
		}

		$results = igfae_fetch_data($sql);
/*
		$logInfo .= "[".date("d/m/Y - h:i:s")."] Persoa: ".print_r($results, true)."\n";
		//file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
*/
		foreach ( $results['rows'] as $item ) {
			$groups = NULL;

			if ( ($item->lines != '-') && !is_null($item->lines) ) {

				foreach ( explode(',', $item->lines) as $line ) {
					$line = trim( $line );
					$groups[ $line ] = $allGroups[ $line ];
				}
			}
            $info[] = new IgfaePersonal([
                'id'          	=> $item->id,
                'name'          => $item->name,
                'surname'       => $item->surname,
                $groups,
                'orcid'         => $item->orcId,
				'inspireid'		=> $item->inspireId,
                'scopus'        => $item->scopusId,
                'researchid'    => $item->researchId,
                'webpage'       => $item->webpage,
                'category'      => $item->category,
                'phone'         => $item->phone,
                'building'      => $item->building,
                'room'          => $item->room,
                'email'         => $item->email,
                'group'         => $item->group,
                'researchLine'  => [
                    'key'   => $item->line,
                    'name'  => $item->lineName,
                ],
                'researchProg'  => array_map(function ($elem) { return array_combine(array('key','name'), preg_split('/#/mu',$elem,  NULL, PREG_SPLIT_NO_EMPTY));},preg_split( '/\|/mu', $item->program,  NULL, PREG_SPLIT_NO_EMPTY )),
				'researchProgRaw' => $item->program,
                'twitter'       => $item->twitter,
                'linkedin'      => $item->linkedin,
                'area'          => [
                    'name'  => $item->area,
                    'url'   => $item->areaUrl,
                    'code'  => strtok($item->areaCode, '_') . '_'
                ],
                'img'           => $item->img,
                'description'   => $item->description,
                'publications'  => preg_split( '/\|/mu', $item->articles,  NULL, PREG_SPLIT_NO_EMPTY ),//self::getPersonnelPublications( $item->id )],
                'projects'      => preg_split( '/\|/mu', $item->projects,  NULL, PREG_SPLIT_NO_EMPTY )//self::getPersonnelProjects( $item->id )
            ]);
			$logsInfo = "[".date("d/m/Y - h:i:s")."] Result: ".print_r($info, true)."\n";
			//file_put_contents(IGFAE_PATH.'custom.log', $logsInfo, FILE_APPEND);
		}

		return $info;
	}

	public static function getAllCategories() {
		$info = [];

		$sql = "SELECT DISTINCT
			a.Category as 'category'
		FROM 
			IGFAE_Personnel a
		WHERE (
			(a.Category IS NOT NULL) 
			AND (a.Category <> '-') 
			AND (a.Category <> '')
	    )
		ORDER BY 
			category
		ASC";

		$results = igfae_fetch_data($sql);

		foreach ( $results['rows'] as $item ) {
			$info[ $item->category ] = __( $item->category, 'igfae-content-viewer');
		}

		return $info;
	}
	
	public static function getAllGroupNames() {
		/*
		$info = [
			'RP1' 	=> __( 'LHCb', 'igfae-content-viewer' ),
	    	'RP2' 	=> __( 'QCD Pheno', 'igfae-content-viewer' ),
	    	'RP3' 	=> __( 'String Theory', 'igfae-content-viewer' ),
	    	'RP4' 	=> __( 'Astroparticle', 'igfae-content-viewer' ),
	    	'RP5' 	=> __( 'Next', 'igfae-content-viewer' ),
	    	'RP6' 	=> __( 'Nuclear Structure', 'igfae-content-viewer' ),
	    	'RP7' 	=> __( 'LaserPET', 'igfae-content-viewer' ),
	    	'RP8' 	=> __( 'Gravitational Waves', 'igfae-content-viewer' ),
	    	'ZZ'	=> __( 'No Group', 'igfae-content-viewer' ),
		];
		*/
		// Púxenlle eu o info
		/*
		$info = [];
		$sql = "SELECT 
					GroupName AS 'group', 
					`Research Line` AS 'line' 
				FROM 
					`IGFAE_Personnel` 
				GROUP BY GroupName, `Research Line` 
				ORDER BY `Research Line` ASC";

		$results = igfae_fetch_data($sql);

		foreach ( $results['rows'] as $item ) {
			if ($item->line && $item->group) {
//				$logInfo .= "[".date("d/m/Y - h:i:s")."] Result: ".print_r($item, true)."\n";
//       		file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);

				foreach( explode(', ', $item->line) as $line ) {
					if ( !array_key_exists($line, $info) ) {
//						$logInfo .= "[".date("d/m/Y - h:i:s")."] Result: ".print_r($line, true)."\n";
//        				file_put_contents(IGFAE_PATH.'custom.log', $logInfo, FILE_APPEND);
						$info[$line] = $item->group;
					}
				}
			} else {
				if ($item->line) {
					$info[$item->line] = __( 'Unknown', 'igfae-content-viewer' );
				} else {
					$info['ZZ'] = __( 'No Group', 'igfae-content-viewer' );
				}
			}
		}
		
		return $info;
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
		$info['ZZ'] = __( 'No Group', 'igfae-content-viewer' );
		
		return $info;		
	}
	

    private static function getPersonnelPublications($id) {
        $sql = "SELECT a.ArticleTitle as 'Name' FROM IGFAE_Articles a NATURAL JOIN ProductionProjectAuthor b WHERE b.PersonnelID = %d";
        
        $query = igfae_fetch_data($sql, [ $id ]);

        $result = [];
        foreach ( $query['rows'] as $line ) {
            $result[] = $line->Name;
        }

        return $result;
    }

    private static function getPersonnelProjects($id) {
        $sql = "SELECT a.ProjectTitle as 'Name' FROM IGFAE_Projects a NATURAL JOIN ProjectPersonnel b WHERE b.PersonnelID = %d";
        
        $query = igfae_fetch_data($sql, [ $id ]);
        $result = [];
        
        foreach ( $query['rows'] as $line ) {
            $result[] = $line->Name;
        }
        return $result;
    } 	
	
	public function getId() {
		return $this->_id;
	}

	public function getName() {
		return $this->_name;
	}

	public function getFullName() {
		$result = '';

		if ( $this->_surname ) {
			$result .= $this->_surname;
		}
		if ($this->_surname && $this->_name) {
			$result .= ', ';
		}

		if ( $this->_name ) {
			$result .= $this->_name;
		}

		return $result;
	}

	public function getSurname() {
		return $this->_surname;
	}

	public function getGroups() {
		return $this->_groups;
	}

    public function getGroup() {
        return $this->_group;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function getResearchLine() {
        return $this->_researchLine;
    }

    public function getResearchProg() {
        return $this->_researchProg;
    }

	public function getResearchProgRaw() {
        return $this->_researchProgRaw;
    }

    public function getArea() {
        return $this->_area[ 'name' ];
    }

    public function getAreaUrl() {
        return $this->_area[ 'url' ];
    }

	public function getAreaCode() {
        return $this->_area[ 'code' ];
    }

    public function getPublications() {
        return $this->_publications;
    }

    public function getProjects() {
        return $this->_projects;
    }

    public function getTwitter() {
        return $this->_twitter;
    }

    public function getLinkedin() {
        return $this->_linkedin;
    }

    public function getProgram() {
        return $this->_program;
    }

	public function getWebpage() {
		return $this->_webpage;
	}

	public function getPhone() {
		return $this->_phone;
	}

	public function getEmail() {
		return $this->_email;
	}

	public function getRoom() {
		return $this->_room;
	}

	public function getCategory() {
		return $this->_category;
	}

	public function printGroups() {
		$result = NULL;

		if ($this->_groups) {
			$count = 0;
			foreach ( $this->_groups as $line => $group ) {
				if ( $count > 0 ) {
					$result .= ', ';
				}
				$result .= $line.' '.$group;
				$count++;
			}
		}
		
		return $result;
	}
    public function getOrcidID() {
        return $this->_orcid;
    }

	public function getInspireID() {
		return $this->_inspireid;
    }

    public function getScopusID()  {
        return $this->_scopus;
    }

    public function getResearchID() {
        return $this->_researchid;
    }
 		
    public function getImgUrl() {

        if ( $this->_img ) return get_site_url() . $this->_img; //site_url( $this->_img );

        return NULL;
        
    }
}
