<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/TaxonomyHarvester.php');
include_once(__DIR__ . '/Sanitizer.php');

class TaxonomyCleaner extends Manager{

	private $collid;
	private $taxAuthId = 1;
	private $targetKingdom;
	private $autoClean = 0;
	private $testValidity = 1;
	private $testTaxonomy = 1;
	private $checkAuthor = 1;
	private $verificationMode = 0;

	public function __construct(){
		parent::__construct();
	}

	public function getBadTaxaCount(): int
    {
		$retCnt = 0;
		if($this->collid){
			$sql = 'SELECT COUNT(DISTINCT sciname) AS taxacnt '.$this->getSqlFragment();
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				if($row = $rs->fetch_object()){
					$retCnt = $row->taxacnt;
				}
				$rs->free();
			}
		}
		return $retCnt;
	}

	public function getBadSpecimenCount(): int
    {
		$retCnt = 0;
		if($this->collid){
			$sql = 'SELECT COUNT(*) AS cnt '.$this->getSqlFragment();
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				if($row = $rs->fetch_object()){
					$retCnt = $row->cnt;
				}
				$rs->free();
			}
		}
		return $retCnt;
	}

	public function analyzeTaxa($taxResource, $startIndex, $limit = null){
		set_time_limit(1800);
		$isTaxonomyEditor = false;
		if($GLOBALS['USER_RIGHTS'] && array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])) {
			$isTaxonomyEditor = true;
		}
		$endIndex = 0;
		echo '<li>Starting taxa check</li>';
		$sql = 'SELECT sciname, family, scientificnameauthorship, count(*) as cnt '.$this->getSqlFragment();
		if($startIndex) {
			$sql .= 'AND (sciname > "' . Sanitizer::cleanInStr($startIndex) . '") ';
		}
		$sql .= 'GROUP BY sciname, family ORDER BY sciname LIMIT '.($limit ?: 50);
		//echo $sql; exit;
		if($rs = $this->conn->query($sql)){
			$taxonHarvester = new  TaxonomyHarvester();
			if($this->targetKingdom){
				$kingArr = explode(':',$this->targetKingdom);
				if($kingArr){
                    $taxonHarvester->setKingdomTid($kingArr[0]);
                    if(isset($kingArr[1])){
                        $taxonHarvester->setKingdomName($kingArr[1]);
                    }
                }
            }
            $taxonHarvester->setTaxonomicResources($taxResource);
            $taxaAdded = false;
            $taxaCnt = 1;
            $itemCnt = 0;
            while($r = $rs->fetch_object()){
                $editLink = '[<a href="#" onclick="openPopup(\''.$GLOBALS['CLIENT_ROOT'].
                    '/collections/editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=sciname&q_customtype1=EQUALS&q_customvalue1='.urlencode($r->sciname).'&collid='.
                    $this->collid.'\'); return false;">'.$r->cnt.' '.((int)$r->cnt === 1?'occurrence':'occurrences').' <i style="height:15px;width:15px;" class="far fa-edit"></i></a>]';
                echo '<li><div style="margin-top:5px">Resolving #'.$taxaCnt.': <b><i>'.$r->sciname.'</i></b>'.($r->family?' ('.$r->family.')':'').'</b> '.$editLink.'</div></li>';
                if($r->family) {
                    $taxonHarvester->setDefaultFamily($r->family);
                }
                if($r->scientificnameauthorship) {
                    $taxonHarvester->setDefaultAuthor($r->scientificnameauthorship);
                }
                $sciname = $r->sciname;
                $tid = 0;
                $manualCheck = true;
                $taxonArr = (new TaxonomyUtilities)->parseScientificName($r->sciname,$this->conn);
                if($taxonArr && $taxonArr['sciname']){
                    $sciname = $taxonArr['sciname'];
                    if($sciname !== $r->sciname){
                        echo '<li style="margin-left:15px;">Interpreted base name: <b>'.$sciname.'</b></li>';
                    }
                    $tid = $taxonHarvester->getTid($taxonArr);
                }
                if(!$tid && $taxonHarvester->processSciname($sciname)) {
                    $taxaAdded= true;
                    if($taxonHarvester->isFullyResolved()){
                        $manualCheck = false;
                    }
                    else{
                        echo '<li style="margin-left:15px;">Taxon not fully resolved...</li>';
                    }
                    $taxonArr = (new TaxonomyUtilities)->parseScientificName($sciname,$this->conn);
                    $tid = $taxonHarvester->getTid($taxonArr);
                }
                if($taxonArr && $taxonArr['sciname'] && $tid && $this->autoClean) {
                    $this->remapOccurrenceTaxon($this->collid, $r->sciname, $tid, ($taxonArr['identificationqualifier'] ?? ''));
                    echo '<li style="margin-left:15px;">Occurrences mapped to '.($taxonHarvester->getTidAccepted()?'taxon #'.$taxonHarvester->getTidAccepted():'<b>'.$sciname.'</b>').'</li>';
                    $manualCheck = false;
                }
                if($manualCheck){
                    $thesLink = '';
                    if($isTaxonomyEditor){
                        $thesLink = ' <a href="#" onclick="openPopup(\'../../taxa/taxonomy/taxonomyloader.php\'); return false;" title="Open Thesaurus New Record Form"><i style="height:15px;width:15px;" class="far fa-edit"></i><b style="font-size:70%;">T</b></a>';
                    }
                    echo '<li style="margin-left:15px;">Checking close matches in thesaurus'.$thesLink.'...</li>';
                    if($matchArr = $taxonHarvester->getCloseMatch($sciname)){
                        $strTestArr = array();
                        for($x=1; $x <= 3; $x++){
                            $indexStr = 'unitname'.$x;
                            if(isset($taxonArr[$indexStr]) && $taxonArr['unitname'.$x]) {
                                $strTestArr[] = $taxonArr['unitname' . $x];
                            }
                        }
                        foreach($matchArr as $tid => $scinameMatch){
                            $snTokens = explode(' ',$scinameMatch);
                            if($snTokens){
                                foreach($snTokens as $k => $v){
                                    if(in_array($v, $strTestArr, true)) {
                                        $snTokens[$k] = '<b>' . $v . '</b>';
                                    }
                                }
                                $idQual = (isset($taxonArr['identificationqualifier'])?str_replace("'", '', $taxonArr['identificationqualifier']):'');
                                $echoStr = '<i>'.implode(' ',$snTokens).'</i> =&gt; <span class="hideOnLoad">wait for page to finish loading...</span><span class="displayOnLoad" style="display:none">'.
                                    '<a href="#" onclick="return remappTaxon(\''.urlencode($r->sciname).'\','.$tid.',\''.$idQual.'\','.$itemCnt.')" style="color:blue"> remap to this taxon</a>'.
                                    '<span id="remapSpan-'.$itemCnt.'"></span></span>';
                                echo '<li style="margin-left:30px;">'.$echoStr.'</li>';
                                $itemCnt++;
                            }
						}
					}
					else{
						echo '<li style="margin-left:30px;">No close matches found</li>';
					}
					$manStr = 'Manual search: ';
					$manStr .= '<form onsubmit="return false" style="display:inline;">';
					$manStr .= '<input class="taxon" name="taxon" type="text" value="" />';
					$manStr .= '<input class="tid" name="tid" type="hidden" value="" />';
					$manStr .= '<button onclick="batchUpdate(this.form,\''.$r->sciname.'\','.$taxaCnt.')">Remap</button>';
					$manStr .= '<span id="remapSpan-'.$taxaCnt.'-c"></span>';
					$manStr .= '</form>';
					echo '<li style="margin-left:30px;">'.$manStr.'</li>';
				}
				$taxaCnt++;
				$endIndex = preg_replace("/[^A-Za-z\-. ]/", '', $r->sciname );
				flush();
			}
			$rs->free();
			if($taxaAdded) {
				$this->indexOccurrenceTaxa();
			}
		}

		echo '<li><b>Done with taxa check </b></li>';
		return $endIndex;
	}

	private function getSqlFragment(): string
	{
		return 'FROM omoccurrences WHERE (collid IN('.$this->collid.')) AND (tidinterpreted IS NULL) AND (sciname IS NOT NULL) AND (sciname NOT LIKE "% x %") AND (sciname NOT LIKE "% × %") ';
	}

	public function deepIndexTaxa(): void
	{
		$this->setVerboseMode(2);
		$kingdomName = '';
		if($this->targetKingdom) {
			$targetKingdomStr = explode(':', $this->targetKingdom);
			$kingdomName = array_pop($targetKingdomStr);
		}

		$this->logOrEcho('Cleaning leading and trailing spaces...');
		$sql = 'UPDATE omoccurrences '.
			'SET sciname = trim(sciname) '.
			'WHERE (collid IN('.$this->collid.')) AND (tidinterpreted is NULL) AND (sciname LIKE " %" OR sciname LIKE "% ")';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records cleaned',1);
		}
		flush();

		$this->logOrEcho('Cleaning double spaces embedded within name...');
		$sql = 'UPDATE omoccurrences '.
			'SET sciname = replace(sciname, "  ", " ") '.
			'WHERE (collid IN('.$this->collid.')) AND (tidinterpreted is NULL) AND (sciname LIKE "%  %") ';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records cleaned',1);
		}
		flush();

		$this->indexOccurrenceTaxa();

		$this->logOrEcho('Indexing names based on matching trinomials without taxonRank designation...');
		$triCnt = 0;
		$sql = 'SELECT DISTINCT o.sciname, t.tid '.
			'FROM omoccurrences o INNER JOIN taxa t ON o.sciname = CONCAT_WS(" ",t.unitname1,t.unitname2,t.unitname3) '.
			'WHERE (o.collid IN('.$this->collid.')) AND (t.rankid IN(230,240)) AND (o.sciname LIKE "% % %") AND (o.tidinterpreted IS NULL) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		$sql .= 'ORDER BY t.rankid';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$triCnt += $this->remapOccurrenceTaxon($this->collid, $r->sciname, $r->tid);
		}
		$rs->free();
		$this->logOrEcho($triCnt.' occurrence records remapped',1);
		flush();

		$this->logOrEcho('Indexing names ending in &quot;sp.&quot;...');
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON SUBSTRING(o.sciname,1, CHAR_LENGTH(o.sciname) - 4) = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE (o.collid IN('.$this->collid.')) AND (o.sciname LIKE "% sp.") AND (o.tidinterpreted IS NULL) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing &quot;spp.&quot;...');
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON REPLACE(o.sciname," spp.","") = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE (o.collid IN('.$this->collid.')) AND (o.sciname LIKE "% spp.%") AND (o.tidinterpreted IS NULL) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing &quot;cf.&quot;...');
		$cnt = 0;
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON REPLACE(o.sciname," cf. "," ") = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE (o.collid IN('.$this->collid.')) AND (o.sciname LIKE "% cf. %") AND (o.tidinterpreted IS NULL) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$cnt = $this->conn->affected_rows;
		}
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON REPLACE(o.sciname," cf "," ") = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE (o.collid IN('.$this->collid.')) AND (o.sciname LIKE "% cf %") AND (o.tidinterpreted IS NULL) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$cnt += $this->conn->affected_rows;
			$this->logOrEcho($cnt.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing &quot;aff.&quot;...');
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON REPLACE(o.sciname," aff. "," ") = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE (o.collid IN('.$this->collid.')) AND (o.sciname LIKE "% aff. %") AND (o.tidinterpreted IS NULL) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();

		$this->logOrEcho('Indexing names containing &quot;group&quot; statements...');
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON REPLACE(o.sciname," group"," ") = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE (o.collid IN('.$this->collid.')) AND (o.sciname LIKE "% group%") AND (o.tidinterpreted IS NULL) ';
		if($kingdomName) {
			$sql .= 'AND (t.kingdomname = "' . $kingdomName . '") ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		flush();
	}

	private function indexOccurrenceTaxa(): void
	{
		$this->logOrEcho('Populating null kingdom name tags...');
		$sql = 'UPDATE taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
			'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
			'SET t.kingdomname = t2.sciname '.
			'WHERE (e.taxauthid = '.$this->taxAuthId.') AND (t2.rankid = 10) AND ISNULL(t.kingdomName)';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' taxon records updated',1);
		}
		else{
			$this->logOrEcho('ERROR updating kingdoms.');
		}
		flush();

		$this->logOrEcho('Populating null family tags...');
		$sql = 'UPDATE taxa t INNER JOIN taxaenumtree e ON t.tid = e.tid '.
			'INNER JOIN taxa t2 ON e.parenttid = t2.tid '.
			'INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'SET ts.family = t2.sciname '.
			'WHERE (e.taxauthid = '.$this->taxAuthId.') AND (ts.taxauthid = '.$this->taxAuthId.') AND (t2.rankid = 140) AND ISNULL(ts.family)';
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' taxon records updated',1);
		}
		else{
			$this->logOrEcho('ERROR family tags.');
		}
		flush();

		$this->logOrEcho('Indexing names based on exact matches...');
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname '.
			'SET o.tidinterpreted = t.tid '.
			'WHERE (o.collid IN('.$this->collid.')) AND (o.tidinterpreted IS NULL) ';
		if($this->targetKingdom) {
			$sql .= 'AND t.kingdomname = "' . $this->targetKingdom . '" ';
		}
		if($this->conn->query($sql)){
			$this->logOrEcho($this->conn->affected_rows.' occurrence records mapped',1);
		}
		else{
			$this->logOrEcho('ERROR linking new data to occurrences.');
		}
		flush();
	}

	public function remapOccurrenceTaxon($collid, $oldSciname, $tid, $idQualifierIn = null): int
	{
		$affectedRows = 0;
		$idQualifier = '';
		if(is_numeric($collid) && $oldSciname && is_numeric($tid)){
			$hasEditType = false;
			$rsTest = $this->conn->query('SHOW COLUMNS FROM omoccuredits WHERE field = "editType"');
			if($rsTest->num_rows) {
				$hasEditType = true;
			}
			$rsTest->free();

			$newSciname = '';
			$newAuthor= '';
			$sql = 'SELECT sciname, author FROM taxa WHERE (tid = '.$tid.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$newSciname = $r->sciname;
				$newAuthor = $r->author;
			}
			$rs->free();

			$oldSciname = Sanitizer::cleanInStr($oldSciname);
			if($idQualifierIn) {
				$idQualifier = Sanitizer::cleanInStr($idQualifierIn);
			}
			$sqlWhere = 'WHERE (collid IN('.$collid.')) AND (sciname = "'.$oldSciname.'") AND (tidinterpreted IS NULL) ';
			$sql1 = 'INSERT INTO omoccuredits(occid, FieldName, FieldValueNew, FieldValueOld, uid, ReviewStatus, AppliedStatus'.($hasEditType?',editType ':'').') '.
				'SELECT occid, "sciname", "'.$newSciname.'", sciname, '.$GLOBALS['SYMB_UID'].', 1, 1'.($hasEditType?',1':'').' FROM omoccurrences '.$sqlWhere;
			if($this->conn->query($sql1)){
				if($newAuthor){
					$sql2 = 'INSERT INTO omoccuredits(occid, FieldName, FieldValueNew, FieldValueOld, uid, ReviewStatus, AppliedStatus'.($hasEditType?',editType ':'').') '.
						'SELECT occid, "scientificNameAuthorship" AS fieldname, "'.$newAuthor.'", IFNULL(scientificNameAuthorship,""), '.$GLOBALS['SYMB_UID'].', 1, 1 '.($hasEditType?',1 ':'').
						'FROM omoccurrences '.$sqlWhere.'AND (scientificNameAuthorship != "'.$newAuthor.'")';
					if(!$this->conn->query($sql2)){
						$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (author).',1);
					}
				}
				if($idQualifier){
					$sql3 = 'INSERT INTO omoccuredits(occid, FieldName, FieldValueNew, FieldValueOld, uid, ReviewStatus, AppliedStatus'.($hasEditType?',editType ':'').') '.
						'SELECT occid, "identificationQualifier" AS fieldname, CONCAT_WS("; ",identificationQualifier,"'.$idQualifier.'") AS idqual, '.
						'IFNULL(identificationQualifier,""), '.$GLOBALS['SYMB_UID'].', 1, 1 '.($hasEditType?',1 ':'').
						'FROM omoccurrences '.$sqlWhere;
					if(!$this->conn->query($sql3)){
						$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (idQual).',1);
					}
				}
				$sqlFinal = 'UPDATE omoccurrences '.
					'SET tidinterpreted = '.$tid.', sciname = "'.$newSciname.'" ';
				if($newAuthor){
					$sqlFinal .= ', scientificNameAuthorship = "'.$newAuthor.'" ';
				}
				if($idQualifier){
					$sqlFinal .= ', identificationQualifier = CONCAT_WS("; ",identificationQualifier,"'.$idQualifier.'") ';
				}
				$sqlFinal .= $sqlWhere;
				if($this->conn->query($sqlFinal)){
					$affectedRows = $this->conn->affected_rows;
				}
				else{
					$this->logOrEcho('ERROR thrown remapping occurrence taxon.',1);
				}
			}
			else{
				$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (E1).',1);
			}
		}
		return $affectedRows;
	}

	public function getVerificationCounts(): array
	{
		return [];
	}

	public function getCollMap(): array
	{
		$retArr = array();
		$collArr = $GLOBALS['USER_RIGHTS']['CollAdmin'] ?? array();
		if($GLOBALS['IS_ADMIN']) {
			$collArr = array_merge($collArr, explode(',', $this->collid));
		}
		$sql = 'SELECT collid, CONCAT_WS("-",institutioncode, collectioncode) AS code, collectionname, icon, colltype, managementtype FROM omcollections '.
			'WHERE (colltype IN("Preserved Specimens","Observations")) AND (collid IN('.implode(',', $collArr).')) '.
			'ORDER BY collectionname, collectioncode ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['code'] = $r->code;
			$retArr[$r->collid]['collectionname'] = $r->collectionname;
			$retArr[$r->collid]['icon'] = $r->icon;
			$retArr[$r->collid]['colltype'] = $r->colltype;
			$retArr[$r->collid]['managementtype'] = $r->managementtype;
		}
		$rs->free();
		return $retArr;
	}

	public function getTaxonomicResourceList(): array
	{
		$taArr = array('col'=>'Catalog of Life','worms'=>'World Register of Marine Species','tropicos'=>'TROPICOS','eol'=>'Encyclopedia of Life');
		if(!isset($GLOBALS['TAXONOMIC_AUTHORITIES'])) {
			return array('col' => 'Catalog of Life', 'worms' => 'World Register of Marine Species');
		}
		return array_intersect_key($taArr,array_change_key_case($GLOBALS['TAXONOMIC_AUTHORITIES']));
	}

	public function getTaxaSuggest($queryString): array
	{
		$retArr = array();
		$sql = 'SELECT tid, sciname FROM taxa ';
		$queryString = preg_replace('/[()\'"+\-=@$%]+/', '', $queryString);
		if($queryString){
			$tokenArr = explode(' ',Sanitizer::cleanInStr($queryString));
			$token = array_shift($tokenArr);
			if($token === 'x') {
				$token = array_shift($tokenArr);
			}
			if($token) {
				$sql .= 'WHERE unitname1 LIKE "' . $token . '%" ';
			}
			if($tokenArr){
				$token = array_shift($tokenArr);
				if($token === 'x') {
					$token = array_shift($tokenArr);
				}
				if($token) {
					$sql .= 'AND unitname2 LIKE "' . $token . '%" ';
				}
				if($tokenArr){
					$token = array_shift($tokenArr);
					if($tokenArr){
						$sql .= 'AND (unitind3 LIKE "'.$token.'%") AND (unitname3 LIKE "'.array_shift($tokenArr).'%") ';
					}
					else{
						$sql .= 'AND (unitind3 LIKE "'.$token.'%" OR unitname3 LIKE "'.$token.'%") ';
					}
				}
			}
			if($this->targetKingdom){
				$kingdomStr = explode(':',$this->targetKingdom);
				$kingdomName = array_pop($kingdomStr);
				$sql .= 'AND (kingdomname IS NULL OR kingdomname = "'.$kingdomName.'") ';
			}
			$sql .= 'LIMIT 30';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = array('id'=>$r->tid,'value'=>$r->sciname);
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getKingdomArr(): array
	{
		$retArr = array();
		$sql = 'SELECT tid, sciname FROM taxa WHERE rankid = 10 ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tid] = $r->sciname;
		}
		$rs->free();
		asort($retArr);
		return $retArr;
	}

	public function setTaxAuthId($id): void
	{
		if(is_numeric($id)) {
			$this->taxAuthId = $id;
		}
	}

	public function setTargetKingdom($k): void
	{
		$this->targetKingdom = $k;
	}

	public function setAutoClean($v): void
	{
		if(is_numeric($v)) {
			$this->autoClean = $v;
		}
	}

	public function setCollId($collid): void
	{
		if(preg_match('/^[\d,]+$/',$collid)){
			$this->collid = $collid;
		}
	}
}
