<?php

class IgfaeUnifiedEvent extends IgfaeEvent {
	private static $table = 'IGFAE_Events';
	private static $columns = null;
	private static $cacheTtlSecondsDefault = 600; // 10 min metadata cache
	private static $cacheTtlFetchSecondsDefault = 120; // 2 min query cache
	private static $cacheTtlSingleSecondsDefault = 300; // 5 min single event cache

	private static $typeAliases = [
		'talk' => ['talk', 'conference'],
		'conference' => ['talk', 'conference'],
	];

	protected $_typeName = null;
	protected $_subType = null;
	protected $_abstract = null;
	protected $_webpage = null;
	protected $_finalDate = null;
	protected $_speaker = [];
	protected $_speakerLinks = [];
	protected $_institution = null;

	public function __construct($row) {
		$this->_id = isset($row->id) ? (int) $row->id : null;
		$this->_title = $row->title ?? null;
		$this->_date = !empty($row->date) ? strtotime($row->date) : null;
		$this->_finalDate = !empty($row->finalDate) ? strtotime($row->finalDate) : null;
		$this->_place = $row->place ?? null;
		$this->_image = $row->image ?? null;
		$this->_abstract = $row->abstract ?? null;
		$this->_webpage = $row->webpage ?? null;
		$this->_subType = $row->subType ?? null;
		$this->_institution = isset($row->institution) ? trim((string) $row->institution) : null;
		if ($this->_institution === '') {
			$this->_institution = null;
		}

		$normalized = self::normalizeType($row->typeName ?? null, $row->typeId ?? null);
		$this->_type = $normalized['slug'];
		$this->_typeName = $normalized['name'];

		$this->_speaker = [];
		if (!empty($row->speaker)) {
			$this->_speaker = array_values(array_filter(array_map('trim', explode(',', $row->speaker))));
		}

		$this->_speakerLinks = self::parseSpeakerLinks($row->speakerLinks ?? null);
		if (!empty($this->_speakerLinks)) {
			$normalize = function ($value) {
				$value = (string) $value;
				if (function_exists('mb_strtolower')) {
					return mb_strtolower($value);
				}
				return strtolower($value);
			};

			$existing = array_flip(array_map($normalize, $this->_speaker));
			foreach ($this->_speakerLinks as $speakerLink) {
				$name = trim((string) ($speakerLink['name'] ?? ''));
				if ($name === '') {
					continue;
				}

				$key = $normalize($name);
				if (!isset($existing[$key])) {
					$this->_speaker[] = $name;
					$existing[$key] = true;
				}
			}
		}
	}

	public static function fetch($keywords = '', $date = null, $page = null, $amount = 20, $type = null, $formatDate = 'Y', $order = 'DESC', $exclude = null, $featured = null, $upcoming_only = false) {
		$result = ['rows' => [], 'pagination' => null];
		if (!self::tableExists()) {
			return $result;
		}

		$ttlFetch = self::getCacheTtlFetch();
		$cachePayload = [
			'keywords' => (string) $keywords,
			'date' => $date,
			'page' => $page,
			'amount' => $amount,
			'type' => $type,
			'formatDate' => $formatDate,
			'order' => $order,
			'exclude' => $exclude,
			'featured' => $featured,
			'upcoming_only' => (bool) $upcoming_only,
		];
		$cacheKey = self::getCacheKey('fetch_' . md5(self::encodeCachePayload($cachePayload)));
		if ( $ttlFetch > 0 && function_exists('get_transient') ) {
			$cachedResult = get_transient($cacheKey);
			if ( is_array($cachedResult) && array_key_exists('rows', $cachedResult) ) {
				$cachedItems = [];
				foreach ( (array) ($cachedResult['rows'] ?? []) as $cachedRow ) {
					if ( is_object($cachedRow) ) {
						$cachedItems[] = new IgfaeUnifiedEvent($cachedRow);
					}
				}
				return [
					'rows' => $cachedItems,
					'pagination' => $cachedResult['pagination'] ?? null,
				];
			}
		}

		$sql = self::buildBaseSelectSql();
		$conditions = self::buildWhereConditions($keywords, $date, $type, $formatDate, $exclude, $featured, $upcoming_only);
		if (!empty($conditions)) {
			$sql .= ' WHERE ' . implode(' AND ', $conditions);
		}

		$sql .= self::buildGroupBySql();
		$sql .= self::buildOrderBySql($order);

		$pagination = getPaginationInfo($page, $amount, $sql);
		if (!is_null($page)) {
			$offset = ((int) $page) * ((int) $amount);
			$sql .= " LIMIT {$offset}, " . (int) $amount;
		}

		$rows = igfae_fetch_data($sql);
		$items = [];
		foreach ($rows['rows'] as $row) {
			$items[] = new IgfaeUnifiedEvent($row);
		}

		if ( $ttlFetch > 0 && function_exists('set_transient') ) {
			set_transient($cacheKey, [
				'rows' => $rows['rows'] ?? [],
				'pagination' => $pagination,
			], $ttlFetch);
		}

		return ['rows' => $items, 'pagination' => $pagination];
	}

	public static function getById($id) {
		if (!$id || !self::tableExists()) {
			return null;
		}

		$idCol = self::pickColumn(['EventID', 'EventId', 'ID', 'Id', 'id']);
		if (!$idCol) {
			return null;
		}

		$ttlSingle = self::getCacheTtlSingle();
		$cacheKey = self::getCacheKey('single_' . (int) $id);
		if ( $ttlSingle > 0 && function_exists('get_transient') ) {
			$cachedResult = get_transient($cacheKey);
			if ( is_array($cachedResult) && array_key_exists('found', $cachedResult) ) {
				if ( empty($cachedResult['found']) ) {
					return null;
				}
				if ( !empty($cachedResult['row']) && is_object($cachedResult['row']) ) {
					return new IgfaeUnifiedEvent($cachedResult['row']);
				}
			}
		}

		$sql = self::buildBaseSelectSql() . " WHERE a.`{$idCol}` = " . (int) $id;
		$sql .= self::buildGroupBySql();
		$sql .= " LIMIT 1";
		$rows = igfae_fetch_data($sql);

		if (empty($rows['rows'])) {
			if ( $ttlSingle > 0 && function_exists('set_transient') ) {
				set_transient($cacheKey, [
					'found' => false,
					'row' => null,
				], $ttlSingle);
			}
			return null;
		}

		if ( $ttlSingle > 0 && function_exists('set_transient') ) {
			set_transient($cacheKey, [
				'found' => true,
				'row' => $rows['rows'][0],
			], $ttlSingle);
		}

		return new IgfaeUnifiedEvent($rows['rows'][0]);
	}

	public static function getAllYears($type = null) {
		$events = self::fetch('', null, null, null, $type, 'Y', 'DESC');
		$years = [];
		foreach (($events['rows'] ?? []) as $item) {
			$year = $item->getYear();
			if ($year) {
				$years[] = $year;
			}
		}

		$years = array_values(array_unique($years));
		rsort($years);
		return $years;
	}

	public static function getAvailableTypes() {
		$cacheKey = self::getCacheKey('available_types');
		$ttlMetadata = self::getCacheTtlMetadata();
		if ( $ttlMetadata > 0 && function_exists('get_transient') ) {
			$cachedTypes = get_transient($cacheKey);
			if ( is_array($cachedTypes) && !empty($cachedTypes) ) {
				return $cachedTypes;
			}
		}

		$result = [ 'all' => __('All', 'ifgae-content-viewer') ];
		foreach (self::getTypeCatalog() as $typeInfo) {
			$slug = $typeInfo['slug'] ?? null;
			$name = $typeInfo['name'] ?? null;
			if ($slug && $name && !isset($result[$slug])) {
				$result[$slug] = $name;
			}
		}

		if ( $ttlMetadata > 0 && function_exists('set_transient') && count($result) > 1 ) {
			set_transient($cacheKey, $result, $ttlMetadata);
		}

		return $result;
	}

	public function getTypeName() {
		return $this->_typeName ?: parent::getTypeName();
	}

	public function getSubType() {
		return $this->_subType;
	}

	public function getAbstract() {
		return $this->_abstract;
	}

	public function getWebPage() {
		return $this->_webpage;
	}

	public function getFinalDate($format = 'Y-m-d H:i:s') {
		$result = null;
		if ($this->_finalDate) {
			$pos = strpos($format, 'H');
			if ($pos === false || (date('H:i:s', $this->_finalDate) != '00:00:00')) {
				$result = date($format, $this->_finalDate);
			} else {
				$result = date(trim(substr($format, 0, $pos)), $this->_finalDate);
			}
		}

		return $result;
	}

	public function getSpeaker() {
		return $this->_speaker;
	}

	public function getToStringSpeaker() {
		if (empty($this->_speaker)) {
			return null;
		}

		$speaker = implode(', ', $this->_speaker);
		if (!empty($this->_institution)) {
			$speaker .= ' (' . $this->_institution . ')';
		}

		return $speaker;
	}

	public function getSpeakerLinks() {
		return $this->_speakerLinks;
	}

	public function getInstitution() {
		return $this->_institution;
	}

	private static function tableExists() {
		return !empty(self::getColumns());
	}

	private static function getColumns() {
		if (self::$columns !== null) {
			return self::$columns;
		}

		$ttlMetadata = self::getCacheTtlMetadata();
		$cacheKey = self::getCacheKey('columns');
		if ( $ttlMetadata > 0 && function_exists('get_transient') ) {
			$cachedColumns = get_transient($cacheKey);
			if ( is_array($cachedColumns) ) {
				self::$columns = $cachedColumns;
				return self::$columns;
			}
		}

		$schema = igfae_fetch_data("SHOW COLUMNS FROM " . self::$table);
		$cols = [];
		foreach (($schema['rows'] ?? []) as $row) {
			if (!empty($row->Field)) {
				$cols[] = $row->Field;
			}
		}

		self::$columns = $cols;
		if ( $ttlMetadata > 0 && function_exists('set_transient') && !empty(self::$columns) ) {
			set_transient($cacheKey, self::$columns, $ttlMetadata);
		}
		return self::$columns;
	}

	private static function getCacheKey($suffix) {
		$dbName = function_exists('igfae_get_db_name') ? (string) igfae_get_db_name() : '';
		$dbHost = function_exists('igfae_get_db_host') ? (string) igfae_get_db_host() : '';
		$fingerprint = md5($dbHost . '|' . $dbName . '|' . self::$table);
		return 'igfae_events_unified_' . $suffix . '_' . $fingerprint;
	}

	private static function encodeCachePayload($payload) {
		if ( function_exists('wp_json_encode') ) {
			return (string) wp_json_encode($payload);
		}
		return (string) json_encode($payload);
	}

	public static function clearCache() {
		self::$columns = null;
		global $wpdb;
		if ( empty($wpdb) ) {
			return false;
		}

		$patterns = [
			'_transient_igfae_events_unified_%',
			'_transient_timeout_igfae_events_unified_%',
			'_site_transient_igfae_events_unified_%',
			'_site_transient_timeout_igfae_events_unified_%',
		];

		foreach ( $patterns as $pattern ) {
			$wpdb->query($wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$pattern
			));
		}

		return true;
	}

	private static function getCacheTtlMetadata() {
		$raw = function_exists('get_option') ? get_option('igfae_events_cache_ttl_metadata', self::$cacheTtlSecondsDefault) : self::$cacheTtlSecondsDefault;
		return self::sanitizeTtl($raw, self::$cacheTtlSecondsDefault);
	}

	private static function getCacheTtlFetch() {
		$raw = function_exists('get_option') ? get_option('igfae_events_cache_ttl_fetch', self::$cacheTtlFetchSecondsDefault) : self::$cacheTtlFetchSecondsDefault;
		return self::sanitizeTtl($raw, self::$cacheTtlFetchSecondsDefault);
	}

	private static function getCacheTtlSingle() {
		$raw = function_exists('get_option') ? get_option('igfae_events_cache_ttl_single', self::$cacheTtlSingleSecondsDefault) : self::$cacheTtlSingleSecondsDefault;
		return self::sanitizeTtl($raw, self::$cacheTtlSingleSecondsDefault);
	}

	private static function sanitizeTtl($value, $defaultValue) {
		if (!is_numeric($value)) {
			return (int) $defaultValue;
		}

		$ttl = (int) $value;
		if ($ttl < 0) {
			$ttl = 0;
		}
		return $ttl;
	}

	private static function hasColumn($column) {
		return in_array($column, self::getColumns(), true);
	}

	private static function pickColumn($candidates) {
		foreach ($candidates as $candidate) {
			if (self::hasColumn($candidate)) {
				return $candidate;
			}
		}
		return null;
	}

	private static function buildBaseSelectSql() {
		$idCol = self::pickColumn(['EventID', 'EventId', 'ID', 'Id', 'id']);
		$typeIdCol = self::pickColumn(['EventTypeID', 'TypeID']);
		$typeNameCol = self::pickColumn(['EventType', 'EventTypeName', 'Type', 'Category']);
		$subTypeCol = self::pickColumn(['EventSubType', 'EventSubtype', 'EventSubTypeName', 'SubType']);
		$titleCol = self::pickColumn(['Title', 'EventTitle', 'Name']);
		$dateCol = self::pickColumn(['EventDate', 'Date', 'InitialDate', 'StartDate', 'SeminarDate']);
		$finalDateCol = self::pickColumn(['FinalDate', 'EndDate']);
		$placeCol = self::pickColumn(['Place', 'Venue', 'Room', 'Location']);
		$institutionCol = self::pickColumn(['InstitutionName', 'Institution']);
		$webCol = self::pickColumn(['Webpage', 'Website', 'Web', 'LocationLink']);
		$abstractCol = self::pickColumn(['Abstract', 'Description', 'Summary']);
		$imageCol = self::pickColumn(['Image', 'ImagePath', 'Img']);
		$speakerCol = self::pickColumn(['Speaker', 'SpeakerNoIgfae']);
		$speakerIgfaeCol = self::pickColumn(['SpeakerIGFAE']);
		$personnelIdCol = self::pickColumn(['PersonnelID', 'SpeakerPersonnelID']);

		$speakerSql = "NULL as 'speaker'";
		if ($speakerCol) {
			$speakerSql = "GROUP_CONCAT(DISTINCT NULLIF(TRIM(a.`{$speakerCol}`), '') ORDER BY a.`{$speakerCol}` ASC SEPARATOR ', ') as 'speaker'";
		}

		$speakerLinksSql = "NULL as 'speakerLinks'";
		if ($speakerIgfaeCol && $personnelIdCol) {
			$speakerLinksSql = "GROUP_CONCAT(
				DISTINCT CASE
					WHEN COALESCE(a.`{$speakerIgfaeCol}`, '') <> '' AND COALESCE(a.`{$personnelIdCol}`, '') <> ''
					THEN CONCAT(a.`{$personnelIdCol}`, '::', a.`{$speakerIgfaeCol}`)
				END
				ORDER BY a.`{$speakerIgfaeCol}` ASC
				SEPARATOR '||'
			) as 'speakerLinks'";
		}

		return "SELECT
			" . ($idCol ? "a.`{$idCol}`" : "NULL") . " as 'id',
			" . ($typeIdCol ? "a.`{$typeIdCol}`" : "NULL") . " as 'typeId',
			" . ($typeNameCol ? "a.`{$typeNameCol}`" : "NULL") . " as 'typeName',
			" . ($subTypeCol ? "a.`{$subTypeCol}`" : "NULL") . " as 'subType',
			" . ($titleCol ? "a.`{$titleCol}`" : "NULL") . " as 'title',
			" . ($dateCol ? "a.`{$dateCol}`" : "NULL") . " as 'date',
			" . ($finalDateCol ? "a.`{$finalDateCol}`" : "NULL") . " as 'finalDate',
			" . ($placeCol ? "a.`{$placeCol}`" : "NULL") . " as 'place',
			{$speakerSql},
			{$speakerLinksSql},
			" . ($abstractCol ? "a.`{$abstractCol}`" : "NULL") . " as 'abstract',
			" . ($webCol ? "a.`{$webCol}`" : "NULL") . " as 'webpage',
			" . ($imageCol ? "a.`{$imageCol}`" : "NULL") . " as 'image',
			" . ($institutionCol ? "a.`{$institutionCol}`" : "NULL") . " as 'institution'
		FROM " . self::$table . " a";
	}

	private static function buildWhereConditions($keywords, $date, $type, $formatDate, $exclude, $featured, $upcoming_only = false) {
		$conditions = [];
		$idCol = self::pickColumn(['EventID', 'EventId', 'ID', 'Id', 'id']);
		$dateCol = self::pickColumn(['EventDate', 'Date', 'InitialDate', 'StartDate', 'SeminarDate']);
		$finalDateCol = self::pickColumn(['FinalDate', 'EndDate']);
		$typeIdCol = self::pickColumn(['EventTypeID', 'TypeID']);
		$typeNameCol = self::pickColumn(['EventType', 'EventTypeName', 'Type', 'Category']);
		$featuredCol = self::pickColumn(['Featured', 'IsFeatured']);
		$institutionCol = self::pickColumn(['InstitutionName', 'Institution']);

		if ($exclude && $idCol) {
			$conditions[] = "a.`{$idCol}` != " . (int) $exclude;
		}

		if ($featured && $featuredCol) {
			$conditions[] = "a.`{$featuredCol}` = 1";
		}

		if ($upcoming_only && $dateCol) {
			if ($finalDateCol) {
				$conditions[] = "COALESCE(a.`{$finalDateCol}`, a.`{$dateCol}`) >= CURDATE()";
			} else {
				$conditions[] = "a.`{$dateCol}` >= CURDATE()";
			}
		}

		if ($date && $dateCol) {
			if ($formatDate == 'Y') {
				$conditions[] = "YEAR(a.`{$dateCol}`) = " . (int) $date;
			} else {
				$conditions[] = "a.`{$dateCol}` >= '" . esc_sql($date) . "'";
			}
		}

		if ($type && $type !== 'all') {
			$typeFilters = [];
			$typeData = self::mapRequestedType($type);
			if ($typeIdCol && !empty($typeData['ids'])) {
				$typeFilters[] = "a.`{$typeIdCol}` IN (" . implode(',', array_map('intval', $typeData['ids'])) . ")";
			}
			if ($typeNameCol && !empty($typeData['names'])) {
				$names = array_map(function ($name) {
					return "'" . esc_sql($name) . "'";
				}, $typeData['names']);
				$typeFilters[] = "LOWER(a.`{$typeNameCol}`) IN (" . implode(',', $names) . ")";
			}
			if (!empty($typeFilters)) {
				$conditions[] = '(' . implode(' OR ', $typeFilters) . ')';
			}
		}

		if (!empty($keywords)) {
			$searchCols = array_filter([
				self::pickColumn(['Title', 'EventTitle', 'Name']),
				$dateCol,
				self::pickColumn(['Speaker', 'SpeakerNoIgfae', 'SpeakerIGFAE']),
				self::pickColumn(['Place', 'Venue', 'Room', 'Location']),
				$institutionCol,
				self::pickColumn(['EventSubType', 'EventSubtype', 'EventSubTypeName', 'SubType']),
				$typeNameCol,
				self::pickColumn(['Abstract', 'Description', 'Summary']),
			]);

			if (!empty($searchCols)) {
				$parsed = preg_split("/(,|;| )/", $keywords);
				$searchTokens = [];
				foreach ($parsed as $token) {
					$token = trim($token);
					if ($token === '') {
						continue;
					}
					$tokenSql = esc_sql($token);
					$orParts = [];
					foreach ($searchCols as $col) {
						$orParts[] = "a.`{$col}` LIKE '%{$tokenSql}%'";
					}
					if (!empty($orParts)) {
						$searchTokens[] = '(' . implode(' OR ', $orParts) . ')';
					}
				}

				if (!empty($searchTokens)) {
					$conditions[] = '(' . implode(' OR ', $searchTokens) . ')';
				}
			}
		}

		return $conditions;
	}

	private static function buildGroupBySql() {
		$idCol = self::pickColumn(['EventID', 'EventId', 'ID', 'Id', 'id']);
		if (!$idCol) {
			return '';
		}
		return " GROUP BY a.`{$idCol}`";
	}

	private static function buildOrderBySql($order) {
		$dateCol = self::pickColumn(['EventDate', 'Date', 'InitialDate', 'StartDate', 'SeminarDate']);
		$idCol = self::pickColumn(['EventID', 'EventId', 'ID', 'Id', 'id']);
		$order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

		if ($dateCol) {
			return " ORDER BY a.`{$dateCol}` {$order}";
		}
		if ($idCol) {
			return " ORDER BY a.`{$idCol}` {$order}";
		}
		return '';
	}

	private static function mapRequestedType($requestedType) {
		$slug = strtolower(trim((string) $requestedType));
		$aliases = self::$typeAliases[$slug] ?? [$slug];
		$ids = [];
		$names = [];
		foreach (self::getTypeCatalog() as $typeInfo) {
			$typeSlug = strtolower((string) ($typeInfo['slug'] ?? ''));
			$typeName = strtolower((string) ($typeInfo['name'] ?? ''));
			if (in_array($typeSlug, $aliases, true) || in_array($typeName, $aliases, true)) {
				if (!empty($typeInfo['id'])) {
					$ids[] = (int) $typeInfo['id'];
				}
				if (!empty($typeInfo['name'])) {
					$names[] = strtolower((string) $typeInfo['name']);
				}
			}
		}

		$names = array_values(array_unique(array_merge($aliases, $names)));
		$ids = array_values(array_unique($ids));
		return ['ids' => $ids, 'names' => $names];
	}

	private static function normalizeType($typeName, $typeId) {
		$name = is_string($typeName) ? trim($typeName) : '';
		$typeIdInt = is_numeric($typeId) ? (int) $typeId : null;

		$fallbackSlug = sanitize_title($name ?: 'event');
		if ($fallbackSlug === 'talk') {
			$fallbackSlug = 'conference';
		}
		return [
			'slug' => $fallbackSlug ?: 'event',
			'name' => $name ?: 'Event',
		];
	}

	private static function getTypeCatalog() {
		static $catalog = null;
		if (is_array($catalog)) {
			return $catalog;
		}

		$catalog = [];
		if (!self::tableExists()) {
			return $catalog;
		}

		$typeIdCol = self::pickColumn(['EventTypeID', 'TypeID']);
		$typeNameCol = self::pickColumn(['EventType', 'EventTypeName', 'Type', 'Category']);
		if (!$typeIdCol && !$typeNameCol) {
			return $catalog;
		}

		$sql = "SELECT ";
		$sql .= $typeIdCol ? "a.`{$typeIdCol}` as 'typeId'" : "NULL as 'typeId'";
		$sql .= ", ";
		$sql .= $typeNameCol ? "a.`{$typeNameCol}` as 'typeName'" : "NULL as 'typeName'";
		$sql .= " FROM " . self::$table . " a";
		$sql .= " GROUP BY ";
		if ($typeIdCol && $typeNameCol) {
			$sql .= "a.`{$typeIdCol}`, a.`{$typeNameCol}`";
		} elseif ($typeIdCol) {
			$sql .= "a.`{$typeIdCol}`";
		} else {
			$sql .= "a.`{$typeNameCol}`";
		}
		$sql .= " ORDER BY ";
		if ($typeNameCol) {
			$sql .= "a.`{$typeNameCol}` ASC";
		} else {
			$sql .= "a.`{$typeIdCol}` ASC";
		}

		$rows = igfae_fetch_data($sql);
		foreach (($rows['rows'] ?? []) as $row) {
			$name = isset($row->typeName) ? trim((string) $row->typeName) : '';
			$slug = sanitize_title($name ?: 'event');
			if ($slug === '') {
				$slug = 'event';
			}

			$catalog[] = [
				'id' => (isset($row->typeId) && is_numeric($row->typeId)) ? (int) $row->typeId : null,
				'name' => $name !== '' ? $name : 'Event',
				'slug' => strtolower($slug),
			];
		}

		return $catalog;
	}

	private static function parseSpeakerLinks($rawLinks) {
		$result = [];
		if (!$rawLinks) {
			return $result;
		}

		foreach (explode('||', $rawLinks) as $item) {
			if (strpos($item, '::') === false) {
				continue;
			}

			list($personnelId, $name) = explode('::', $item, 2);
			$personnelId = trim((string) $personnelId);
			$name = trim((string) $name);
			if ($personnelId !== '' && $name !== '') {
				$result[] = ['id' => $personnelId, 'name' => $name];
			}
		}

		return $result;
	}
}
