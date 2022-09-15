<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceAccessStats.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceDownload{

	private $conn;
	private $redactLocalities = true;
	private $rareReaderArr = array();
	private $schemaType = 'symbiota';
	private $extended = 0;
	private $delimiter = ',';
	private $charSetSource;
	private $charSetOut;
	private $zipFile = false;
 	private $sqlWhere = '';
 	private $conditionArr = array();
    private $taxonFilter;
    private $errorArr = array();
    private $tidArr = array();
    private $isPublicDownload = false;
    private $occArr = array();

 	public function __construct(){
		$connection = new DbConnection();
 		$this->conn = $connection->getConnection();

		if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll', $GLOBALS['USER_RIGHTS'])){
			$this->redactLocalities = false;
		}
		if(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS'])){
			$this->rareReaderArr = $GLOBALS['USER_RIGHTS']['CollEditor'];
		}
		if(array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS'])){
			$this->rareReaderArr = array_unique(array_merge($this->rareReaderArr,$GLOBALS['USER_RIGHTS']['RareSppReader']));
		}

		$this->charSetSource = strtoupper($GLOBALS['CHARSET']);
		$this->charSetOut = $this->charSetSource;
	}

	public function __destruct(){
 		if($this->conn) {
			$this->conn->close();
		}
	}

	public function downloadData(): void
	{
		$outstream = null;
		$contentDesc = '';
		$filePath = $this->getOutputFilePath();
		$fileName = $this->getOutputFileName();

		if($this->schemaType === 'checklist'){
			$contentDesc = 'Checklist File';
		}
		elseif($this->schemaType === 'georef'){
			$contentDesc = 'Occurrence Georeference Data';
		}
		if($this->zipFile){
			$zipArchive = null;
			if(class_exists('ZipArchive')){
				$zipArchive = new ZipArchive;
				$zipArchive->open($filePath.$fileName, ZipArchive::CREATE);
			}
			else{
				$this->errorArr[] = 'ERROR: Zip File creation not supported, see portal manager';
				$contentDesc = 'Output file ERROR: Zip File creation not supported';
			}
			if($zipArchive){
				if($this->schemaType === 'checklist'){
					$tempName = 'checklist';
				}
				elseif($this->schemaType === 'georef'){
					$tempName = 'georef';
				}
				else{
					$tempName = 'occurrence';
				}
				$tempPath = $filePath.$tempName.'_'.time();
				if($this->delimiter === "\t"){
					$tempPath .= '.tab';
					$tempName .= '.tab';
				}
				elseif($this->delimiter === ','){
					$tempPath .= '.csv';
					$tempName .= '.csv';
				}
				else{
					$tempPath .= '.txt';
					$tempName .= '.txt';
				}
				$fh = fopen($tempPath, 'wb');
				$this->writeOutData($fh);
				fclose($fh);
				if(file_exists($tempPath)){
					$zipArchive->addFile($tempPath,$tempName);
					$zipArchive->close();
					unlink($tempPath);
				}
			}
		}
		else{
			$fh = fopen($filePath.$fileName, 'wb');
			$this->writeOutData($fh);
			fclose($fh);
		}
		header('Content-Description: '.$contentDesc);
		header('Content-Type: '.$this->getContentType());
		header('Content-Disposition: attachment; filename='.$fileName);
		header('Content-Transfer-Encoding: '.$this->charSetOut);
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.filesize($filePath.$fileName));
		flush();
		readfile($filePath.$fileName);
		if(file_exists($filePath.$fileName)) {
			unlink($filePath . $fileName);
		}
	}

	private function writeOutData($outstream): int
	{
		$recCnt = 0;
		if($outstream){
			$sql = $this->getSql();
			$result = $this->conn->query($sql,MYSQLI_USE_RESULT);
			if($result){
				$statsManager = new OccurrenceAccessStats();
				$outputHeader = true;
				while($row = $result->fetch_assoc()){
					if($outputHeader){
						if($this->delimiter === ','){
							fputcsv($outstream, array_keys($row));
						}
						else{
							fwrite($outstream, implode($this->delimiter, array_keys($row))."\n");
						}
						$outputHeader = false;
					}
					$this->encodeArr($row);
					if($this->delimiter === ','){
						fputcsv($outstream, $row);
					}
					else{
						fwrite($outstream, implode($this->delimiter,$row)."\n");
					}
					if($this->isPublicDownload && $this->schemaType !== 'checklist' && array_key_exists('occid', $row)) {
						$statsManager->recordAccessEvent($row['occid'], 'download');
					}
					$recCnt++;
				}
			}
			else{
				echo "Recordset is empty.\n";
			}
			if($result) {
				$result->close();
			}
		}
		return $recCnt;
	}

	public function getDataEntryActivity($format, $days, $limit){
		if($format === 'json'){
			$xml = simplexml_load_string($this->getDataEntryXML($days,$limit));
			return json_encode($xml);
		}

		return $this->getDataEntryXML($days,$limit);
	}

	private function getDataEntryXML($days, $limit): string
	{
		$newDoc = new DOMDocument('1.0',$this->charSetOut);

		$rootElem = $newDoc->createElement('rss');
		$rootAttr = $newDoc->createAttribute('version');
		$rootAttr->value = '2.0';
		$rootElem->appendChild($rootAttr);
		$newDoc->appendChild($rootElem);

		$channelElem = $newDoc->createElement('channel');
		$rootElem->appendChild($channelElem);

		$titleElem = $newDoc->createElement('title');
		$titleElem->appendChild($newDoc->createTextNode($GLOBALS['DEFAULT_TITLE'].' New Occurrence Records'));
		$channelElem->appendChild($titleElem);

		$serverDomain = 'http://';
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
			$serverDomain = 'https://';
		}
		$serverDomain .= $_SERVER['HTTP_HOST'];
		if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80 && $_SERVER['SERVER_PORT'] !== 443) {
			$serverDomain .= ':' . $_SERVER['SERVER_PORT'];
		}
		$urlPathPrefix = '';
		if($serverDomain){
			$urlPathPrefix = $serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/');
		}

		$linkElem = $newDoc->createElement('link');
		$linkElem->appendChild($newDoc->createTextNode($urlPathPrefix));
		$channelElem->appendChild($linkElem);
		$descriptionElem = $newDoc->createElement('description');
		$descriptionElem->appendChild($newDoc->createTextNode('An RSS feed that lists summary information for new occurrence records recently entered into the '.$GLOBALS['DEFAULT_TITLE'].' portal'));
		$channelElem->appendChild($descriptionElem);
		$languageElem = $newDoc->createElement('language','en-us');
		$channelElem->appendChild($languageElem);

		$sql = 'SELECT o.occid, CONCAT_WS("-",c.institutioncode, c.collectioncode) as instcode, c.collectionname, g.guid, c.guidtarget, '.
			'o.occurrenceid, o.catalognumber, o.sciname, o.recordedby, o.recordnumber, IFNULL(CAST(o.eventdate AS CHAR),o.verbatimeventdate) as eventdate, '.
			'o.decimallatitude, o.decimallongitude, o.datelastmodified, o.recordenteredby, o.genericcolumn2, '.
			'IFNULL(i.thumbnailurl,i.url) AS thumbnailurl, o.processingstatus '.
			'FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid '.
			'INNER JOIN images i ON o.occid = i.occid '.
			'INNER JOIN guidoccurrences g ON o.occid = g.occid '.
			'WHERE c.colltype = "Preserved Specimens" '.
			'AND o.processingstatus IN("pending review","reviewed", "closed") AND (o.localitysecurity IS NULL OR o.localitysecurity = 0) ';
		if($days && is_numeric($days)) {
			$sql .= 'AND (o.datelastmodified > DATE_SUB(NOW(), INTERVAL ' . $days . ' DAY)) ';
		}
		$sql .= 'ORDER BY o.datelastmodified DESC ';
		if(!$days && !$limit) {
			$limit = '100';
		}
		if($limit && is_numeric($limit)) {
			$sql .= 'LIMIT ' . $limit;
		}
		$rs = $this->conn->query($sql);
		//echo $sql;
		while($r = $rs->fetch_object()){
			$itemElem = $newDoc->createElement('item');
			$channelElem->appendChild($itemElem);

			$itemTitleElem = $newDoc->createElement('title');
			$titleStr = $r->sciname.' - '.$r->recordedby.' '.($r->recordnumber?'#'.$r->recordnumber:'');
			$itemTitleElem->appendChild($newDoc->createTextNode($titleStr));
			$itemElem->appendChild($itemTitleElem);

			$collLinkElem = $newDoc->createElement('collectionName',$r->collectionname.' ('.$r->instcode.')');
			$itemElem->appendChild($collLinkElem);

			$catalogLinkElem = $newDoc->createElement('catalogNumber',$r->catalognumber);
			$itemElem->appendChild($catalogLinkElem);

			if($r->guidtarget){
				$occID = $r->guid;
				if($r->guidtarget === 'occurrenceId'){
					$occID = $r->occurrenceid;
				}
				if($r->guidtarget === 'catalogNumber'){
					$occID = $r->catalognumber;
				}
				$guidLinkElem = $newDoc->createElement('occurrenceID',$occID);
				$itemElem->appendChild($guidLinkElem);
			}

			$itemLinkElem = $newDoc->createElement('link');
			$itemLinkElem->appendChild($newDoc->createTextNode($serverDomain.'/collections/individual/index.php?occid='.$r->occid));
			$itemElem->appendChild($itemLinkElem);

			$tnUrl = $r->thumbnailurl;
			if(strncmp($tnUrl, '/', 1) === 0){
				if(isset($GLOBALS['IMAGE_DOMAIN'])){
					$tnUrl = $GLOBALS['IMAGE_DOMAIN'].$tnUrl;
				}
				else{
					$tnUrl = $serverDomain.$tnUrl;
				}
			}
			$tnLinkElem = $newDoc->createElement('thumbnailUri');
			$tnLinkElem->appendChild($newDoc->createTextNode($tnUrl));
			$itemElem->appendChild($tnLinkElem);

			$latLinkElem = $newDoc->createElement('decimalLatitude',$r->decimallatitude);
			$itemElem->appendChild($latLinkElem);
			$lngLinkElem = $newDoc->createElement('decimalLongitude',$r->decimallongitude);
			$itemElem->appendChild($lngLinkElem);
			$eventDateLinkElem = $newDoc->createElement('verbatimEventDate');
			$eventDateLinkElem->appendChild($newDoc->createTextNode($r->eventdate));
			$itemElem->appendChild($eventDateLinkElem);
			$pubDateLinkElem = $newDoc->createElement('pubDate',gmdate(DATE_RSS, strtotime($r->datelastmodified)));
			$itemElem->appendChild($pubDateLinkElem);
			$creatorLinkElem = $newDoc->createElement('creator',$r->recordenteredby);
			$itemElem->appendChild($creatorLinkElem);

			if($r->genericcolumn2){
				$ipAddr = $newDoc->createElement('ipAddress',$r->genericcolumn2);
				$itemElem->appendChild($ipAddr);
			}
		}

		return $newDoc->saveXML();
	}

	public function setSqlWhere($sqlStr): void
	{
		$this->sqlWhere = $sqlStr;
	}

	public function addCondition($field, $cond, $value = null): void
	{
		if($field){
			if(!trim($cond)) {
				$cond = 'EQUALS';
			}
			if($value || ($cond === 'NULL' || $cond === 'NOTNULL')){
				$this->conditionArr[$field][$cond][] = Sanitizer::cleanInStr($this->conn,$value);
			}
		}
	}

	private function applyConditions(): void
	{
		$sqlFrag = '';
		if($this->conditionArr){
			foreach($this->conditionArr as $field => $condArr){
				$sqlFrag2 = '';
				foreach($condArr as $cond => $valueArr){
					if($cond === 'NULL'){
						$sqlFrag2 .= 'OR o.'.$field.' IS NULL ';
					}
					elseif($cond === 'NOTNULL'){
						$sqlFrag2 .= 'OR o.'.$field.' IS NOT NULL ';
					}
					elseif($cond === 'EQUALS'){
						$sqlFrag2 .= 'OR o.'.$field.' IN("'.implode('","',$valueArr).'") ';
					}
					elseif($cond === 'NOTEQUALS'){
						$sqlFrag2 .= 'OR o.'.$field.' NOT IN("'.implode('","',$valueArr).'") ';
					}
					else{
						foreach($valueArr as $value){
							if($cond === 'STARTS'){
								$sqlFrag2 .= 'OR o.'.$field.' LIKE "'.$value.'%" ';
							}
							elseif($cond === 'LIKE'){
								$sqlFrag2 .= 'OR o.'.$field.' LIKE "%'.$value.'%" ';
							}
							elseif($cond === 'NOTLIKE'){
								$sqlFrag2 .= 'OR o.'.$field.' NOT LIKE "%'.$value.'%" ';
							}
							elseif($cond === 'LESSTHAN'){
								$sqlFrag2 .= 'OR o.'.$field.' < "'.$value.'" ';
							}
							elseif($cond === 'GREATERTHAN'){
								$sqlFrag2 .= 'OR o.'.$field.' > "'.$value.'" ';
							}
						}
					}
				}
				$sqlFrag .= 'AND ('.substr($sqlFrag2,3).') ';
			}

		}
		if($sqlFrag){
			$this->sqlWhere .= $sqlFrag;
		}
		if($this->sqlWhere){
			if(strncmp($this->sqlWhere, 'AND ', 4) === 0){
				$this->sqlWhere = 'WHERE'.substr($this->sqlWhere,3);
			}
			elseif(strncmp($this->sqlWhere, 'WHERE ', 6) !== 0){
				$this->sqlWhere = 'WHERE '.$this->sqlWhere;
			}
		}
	}

	private function getSql(): string
	{
        $sql = '';
		if($this->schemaType === 'checklist'){
            if($GLOBALS['SOLR_MODE'] && ($this->tidArr || $this->occArr)){
                $occStr = implode(',',$this->occArr);
                $sql = 'SELECT DISTINCT IFNULL(o.family,"not entered") AS family, o.sciname, CONCAT_WS(" ",t.unitind1,t.unitname1) AS genus, '.
                    'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet, t.unitind3 AS taxonRank, t.unitname3 AS infraSpecificEpithet, t.author AS scientificNameAuthorship '.
                    'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tidinterpreted = t.tid '.
                    'WHERE o.occid IN('.$occStr.') AND o.sciname IS NOT NULL '.
                    'ORDER BY IFNULL(o.family,"not entered"), o.sciname ';
            }
            else{
				$sql = 'SELECT DISTINCT IFNULL(o.family,"not entered") AS family, o.sciname, CONCAT_WS(" ",t.unitind1,t.unitname1) AS genus, '.
					'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet, t.unitind3 AS taxonRank, t.unitname3 AS infraSpecificEpithet, t.author AS scientificNameAuthorship '.
					'FROM omoccurrences o LEFT JOIN taxa t ON o.tidinterpreted = t.tid ';
				$sql .= $this->setTableJoins($this->sqlWhere);
				$sql .= $this->sqlWhere.'AND o.SciName NOT LIKE "%aceae" AND o.SciName NOT LIKE "%idea" AND o.SciName NOT IN ("Plantae","Polypodiophyta") ';
				if($this->redactLocalities){
					if($this->rareReaderArr){
						$sql .= 'AND (o.localitySecurity = 0 OR o.localitySecurity IS NULL OR c.collid IN('.implode(',',$this->rareReaderArr).')) ';
					}
					else{
						$sql .= 'AND (o.localitySecurity = 0 OR o.localitySecurity IS NULL) ';
					}
				}
				$sql .= 'ORDER BY IFNULL(o.family,"not entered"), o.SciName ';
			}
		}
		elseif($this->schemaType === 'georef'){
			$sql = 'SELECT IFNULL(o.institutionCode,c.institutionCode) AS institutionCode, IFNULL(o.collectionCode,c.collectionCode) AS collectionCode, '.
				'o.catalogNumber, o.occurrenceId, o.decimalLatitude, o.decimalLongitude, '.
				'o.geodeticDatum, o.coordinateUncertaintyInMeters, o.verbatimCoordinates, ';
			if($this->extended){
				$sql .= 'o.georeferencedBy, o.georeferenceProtocol, o.georeferenceSources, o.georeferenceVerificationStatus, '.
					'o.georeferenceRemarks, o.minimumElevationInMeters, o.maximumElevationInMeters, o.verbatimElevation, '.
					'o.localitySecurity, o.localitySecurityReason, IFNULL(o.modified,o.datelastmodified) AS modified, '.
					'o.processingStatus, o.collId, o.dbpk AS sourcePrimaryKey, o.occid, CONCAT("urn:uuid:",g.guid) AS recordId ';
			}
			else{
				$sql .= 'o.georeferenceProtocol, o.georeferenceSources, o.georeferenceVerificationStatus, '.
					'o.georeferenceRemarks, o.minimumElevationInMeters, o.maximumElevationInMeters, o.verbatimElevation, '.
					'IFNULL(o.modified,o.datelastmodified) AS modified, o.occid, CONCAT("urn:uuid:",g.guid) AS recordId ';
			}

			$sql .= 'FROM omcollections c INNER JOIN omoccurrences o ON c.collid = o.collid '.
				'LEFT JOIN guidoccurrences g ON o.occid = g.occid '.
				'LEFT JOIN taxa t ON o.tidinterpreted = t.tid ';
			$sql .= $this->setTableJoins($this->sqlWhere);
			$this->applyConditions();
            if($GLOBALS['SOLR_MODE'] && $this->occArr){
                $occStr = implode(',',$this->occArr);
                $sql .= 'WHERE o.occid IN('.$occStr.') ';
            }
            else{
                $sql .= $this->sqlWhere;
            }
			if($this->redactLocalities){
				if($this->rareReaderArr){
					$sql .= 'AND (o.localitySecurity = 0 OR o.localitySecurity IS NULL OR c.collid IN('.implode(',',$this->rareReaderArr).')) ';
				}
				else{
					$sql .= 'AND (o.localitySecurity = 0 OR o.localitySecurity IS NULL) ';
				}
			}
			$sql .= 'ORDER BY o.collid';
		}
		//echo $sql; exit;
		return $sql;
	}

	private function setTableJoins($sqlWhere): string
	{
		$sqlJoin = '';
		if(strpos($sqlWhere,'v.clid')) {
			$sqlJoin .= 'INNER JOIN fmvouchers v ON o.occid = v.occid ';
		}
		if(strpos($sqlWhere,'MATCH(f.recordedby)') || strpos($sqlWhere,'MATCH(f.locality)')){
			$sqlJoin .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
		}
		return $sqlJoin;
	}

	private function getOutputFilePath(): string
	{
		$retStr = $GLOBALS['TEMP_DIR_ROOT'];
		$sbStr = substr($retStr,-1);
		if(!$retStr){
			$retStr = $GLOBALS['SERVER_ROOT'];
			$subStr = substr($retStr,-1);
			if($subStr !== '/' && $subStr !== "\\") {
				$retStr .= '/';
			}
			$retStr .= 'temp/';
		}
		if($sbStr !== '/' && $sbStr !== "\\") {
			$retStr .= '/';
		}
		if(file_exists($retStr.'downloads/')){
			$retStr .= 'downloads/';
		}
		return $retStr;
	}

	private function getOutputFileName(): string
	{
		$retStr = '';
		$fileName = str_replace(Array('.', ':'), '',$GLOBALS['DEFAULT_TITLE']);
		if(strncasecmp($fileName, 'the ', 4) === 0) {
			$fileName = substr($fileName, 4);
		}
		if(strlen($fileName) > 15){
			if($p = strpos($fileName,'(')) {
				$fileName = substr($fileName, 0, $p);
			}
			if(strpos($fileName,' ')){
				$nameArr = explode(' ',trim($fileName));
				$fileName = '';
				foreach($nameArr as $v){
					$fileName .= $v[0];
				}
			}
			else{
				$fileName = substr($fileName,0,15);
			}
		}
		if($fileName){
			$retStr = str_replace(' ', '',$fileName).'_';
		}
		elseif($this->schemaType === 'georef'){
			$retStr .= 'Georef_';
		}
		elseif($this->schemaType === 'checklist'){
			$retStr .= 'Checklist_';
		}
		$retStr .= '_'.time();
		if($this->zipFile){
			$retStr .= '.zip';
		}
		elseif($this->delimiter === "\t"){
			$retStr .= '.tab';
		}
		elseif($this->delimiter === ','){
			$retStr .= '.csv';
		}
		else{
			$retStr .= '.txt';
		}
		return $retStr;
	}

	public function getCollectionMetadata($collid): array
	{
		$retArr = array();
		if(is_numeric($collid)){
			$sql = 'SELECT institutioncode, collectioncode, collectionname, managementtype '.
				'FROM omcollections '.
				'WHERE collid = '.$collid;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['instcode'] = $r->institutioncode;
				$retArr['collcode'] = $r->collectioncode;
				$retArr['collname'] = $r->collectionname;
				$retArr['manatype'] = $r->managementtype;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getProcessingStatusList($collid = null): array
	{
		$psArr = array();
		$sql = 'SELECT DISTINCT processingstatus FROM omoccurrences ';
		if($collid){
			$sql .= 'WHERE collid = '.$collid;
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->processingstatus) {
				$psArr[] = $r->processingstatus;
			}
		}
		$rs->free();
		$templateArr = array('unprocessed','unprocessed-nlp','pending duplicate','stage 1','stage 2','stage 3','pending review','reviewed');
		return array_merge(array_intersect($templateArr,$psArr),array_diff($psArr,$templateArr));
	}

	public function getAttributeTraits($collid = null): array
	{
		$retArr = array();
		$sql = 'SELECT DISTINCT t.traitid, t.traitname, s.stateid, s.statename '.
			'FROM tmtraits t INNER JOIN tmstates s ON t.traitid = s.traitid '.
			'INNER JOIN tmattributes a ON s.stateid = a.stateid '.
			'INNER JOIN omoccurrences o ON a.occid = o.occid ';
		if($collid) {
			$sql .= 'WHERE o.collid = ' . $collid;
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->traitid]['name'] = $r->traitname;
			$retArr[$r->traitid]['state'][$r->stateid] = $r->statename;
		}
		$rs->free();
		return $retArr;
	}

	public function setSchemaType($t): void
	{
		$this->schemaType = $t;
	}

	public function setExtended($e): void
	{
		$this->extended = $e;
	}

	public function setDelimiter($d): void
	{
		if($d === 'tab' || $d === "\t"){
			$this->delimiter = "\t";
		}
		elseif($d === 'csv' || $d === 'comma' || $d === ','){
			$this->delimiter = ',';
		}
		else{
			$this->delimiter = $d;
		}
	}

	private function getContentType(): ?string
	{
		$retStr = 'text/html; charset='.$this->charSetOut;
	    if ($this->zipFile) {
            $retStr = 'application/zip; charset='.$this->charSetOut;
		}

		if($this->delimiter === 'comma' || $this->delimiter === ',') {
            $retStr = 'text/csv; charset='.$this->charSetOut;
		}

		return $retStr;
	}

	public function setCharSetOut($cs): void
	{
		$cs = strtoupper($cs);
		if($cs === 'ISO-8859-1' || $cs === 'UTF-8'){
			$this->charSetOut = $cs;
		}
	}

	public function setZipFile($c): void
	{
		$this->zipFile = $c;
	}

	public function getErrorArr(): array
	{
		return $this->errorArr;
	}

	public function setRedactLocalities($cond): void
	{
		if($cond === 0 || $cond === false){
			$this->redactLocalities = false;
		}
	}

	public function setIsPublicDownload(): void
	{
		$this->isPublicDownload = true;
	}

	public function setTidArr($tidArr): void
	{
        if(is_array($tidArr)){
            $this->tidArr = $tidArr;
        }
    }

    public function setOccArr($occArr): void
	{
        if(is_array($occArr)){
            $this->occArr = $occArr;
        }
    }

	private function encodeArr(&$inArr): void
	{
		if($this->charSetSource && $this->charSetOut !== $this->charSetSource){
			foreach($inArr as $k => $v){
				$inArr[$k] = $this->encodeStr($v);
			}
		}
	}

	private function encodeStr($inStr): string
	{
		$retStr = $inStr;
		if($this->charSetSource){
			if($this->charSetOut === 'UTF-8' && $this->charSetSource === 'ISO-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
				}
			}
			elseif($this->charSetOut === 'ISO-8859-1' && $this->charSetSource === 'UTF-8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
				}
			}
		}
		return $retStr;
	}
}
