<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/TaxonomyHarvester.php');

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
		parent::__construct(null);
	}

	public function getBadTaxaCount(){
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

	public function getBadSpecimenCount(){
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

	public function analyzeTaxa($taxResource, $startIndex, $limit = 50){
		set_time_limit(1800);
		$isTaxonomyEditor = false;
		if($GLOBALS['USER_RIGHTS'] && array_key_exists('Taxonomy', $GLOBALS['USER_RIGHTS'])) {
			$isTaxonomyEditor = true;
		}
		$endIndex = 0;
		$this->logOrEcho('Starting taxa check ');
		$sql = 'SELECT sciname, family, scientificnameauthorship, count(*) as cnt '.$this->getSqlFragment();
		if($startIndex) {
			$sql .= 'AND (sciname > "' . $this->cleanInStr($startIndex) . '") ';
		}
		$sql .= 'GROUP BY sciname, family ORDER BY sciname LIMIT '.$limit;
		//echo $sql; exit;
		if($rs = $this->conn->query($sql)){
			$taxonHarvester = new  TaxonomyHarvester();
			if($this->targetKingdom){
				$kingArr = explode(':',$this->targetKingdom);
				if($kingArr){
                    $taxonHarvester->setKingdomTid($kingArr[0]);
                    $taxonHarvester->setKingdomName($kingArr[1]);
                }
			}
			$taxonHarvester->setTaxonomicResources($taxResource);
			$taxonHarvester->setVerboseMode(2);
			$this->setVerboseMode(2);
			$taxaAdded = false;
			$taxaCnt = 1;
			$itemCnt = 0;
			while($r = $rs->fetch_object()){
				$editLink = '[<a href="#" onclick="openPopup(\''.$GLOBALS['CLIENT_ROOT'].
					'/collections/editor/occurrenceeditor.php?q_catalognumber=&occindex=0&q_customfield1=sciname&q_customtype1=EQUALS&q_customvalue1='.urlencode($r->sciname).'&collid='.
					$this->collid.'\'); return false;">'.$r->cnt.' specimens <i style="height:15px;width:15px;" class="far fa-edit"></i></a>]';
				$this->logOrEcho('<div style="margin-top:5px">Resolving #'.$taxaCnt.': <b><i>'.$r->sciname.'</i></b>'.($r->family?' ('.$r->family.')':'').'</b> '.$editLink.'</div>');
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
						$this->logOrEcho('Interpreted base name: <b>'.$sciname.'</b>',1);
					}
					$tid = $taxonHarvester->getTid($taxonArr);
					if($tid && $this->autoClean){
						$this->remapOccurrenceTaxon($this->collid, $r->sciname, $tid, ($taxonArr['identificationqualifier'] ?? ''));
						$this->logOrEcho('Taxon remapped to <b>'.$sciname.'</b>',1);
						$manualCheck = false;
					}
				}
				if(!$tid && $taxonHarvester->processSciname($sciname)) {
					$taxaAdded= true;
					if($taxonHarvester->isFullyResolved()){
						$manualCheck = false;
					}
					else{
						$this->logOrEcho('Taxon not fully resolved...',1);
					}
				}
				if($manualCheck){
					$thesLink = '';
					if($isTaxonomyEditor){
						$thesLink = ' <a href="#" onclick="openPopup(\'../../taxa/taxonomy/taxonomyloader.php\'); return false;" title="Open Thesaurus New Record Form"><i style="height:15px;width:15px;" class="far fa-edit"></i><b style="font-size:70%;">T</b></a>';
					}
					$this->logOrEcho('Checking close matches in thesaurus'.$thesLink.'...',1);
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
                                $this->logOrEcho($echoStr,2);
                                $itemCnt++;
                            }
						}
					}
					else{
						$this->logOrEcho('No close matches found',2);
					}
					$manStr = 'Manual search: ';
					$manStr .= '<form onsubmit="return false" style="display:inline;">';
					$manStr .= '<input class="taxon" name="taxon" type="text" value="" />';
					$manStr .= '<input class="tid" name="tid" type="hidden" value="" />';
					$manStr .= '<button onclick="batchUpdate(this.form,\''.$r->sciname.'\','.$taxaCnt.')">Remap</button>';
					$manStr .= '<span id="remapSpan-'.$taxaCnt.'-c"></span>';
					$manStr .= '</form>';
					$this->logOrEcho($manStr,2);
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

		$this->logOrEcho('<b>Done with taxa check </b>');
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
			$this->logOrEcho('ERROR updating kingdoms: '.$this->conn->error);
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
			$this->logOrEcho('ERROR family tags: '.$this->conn->error);
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
			$this->logOrEcho('ERROR linking new data to occurrences: '.$this->conn->error);
		}
		flush();
	}

	public function remapOccurrenceTaxon($collid, $oldSciname, $tid, $idQualifierIn = ''): int
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

			$oldSciname = $this->cleanInStr($oldSciname);
			if($idQualifierIn) {
				$idQualifier = $this->cleanInStr($idQualifierIn);
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
						$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (author): '.$this->conn->error,1);
					}
				}
				if($idQualifier){
					$sql3 = 'INSERT INTO omoccuredits(occid, FieldName, FieldValueNew, FieldValueOld, uid, ReviewStatus, AppliedStatus'.($hasEditType?',editType ':'').') '.
						'SELECT occid, "identificationQualifier" AS fieldname, CONCAT_WS("; ",identificationQualifier,"'.$idQualifier.'") AS idqual, '.
						'IFNULL(identificationQualifier,""), '.$GLOBALS['SYMB_UID'].', 1, 1 '.($hasEditType?',1 ':'').
						'FROM omoccurrences '.$sqlWhere;
					if(!$this->conn->query($sql3)){
						$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (idQual): '.$this->conn->error,1);
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
					$this->logOrEcho('ERROR thrown remapping occurrence taxon: '.$this->conn->error,1);
				}
			}
			else{
				$this->logOrEcho('ERROR thrown versioning of remapping of occurrence taxon (E1): '.$this->conn->error,1);
			}
		}
		return $affectedRows;
	}

	public function getVerificationCounts(): array
	{
		return [];
	}

	public function verifyTaxa($verSource): void
	{
		$this->logOrEcho('Starting accepted taxa verification');
		$sql = 'SELECT t.sciname, t.tid, t.author, ts.tidaccepted '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'WHERE (ts.taxauthid = '.$this->taxAuthId.') AND (ts.tid = ts.tidaccepted) '.
			'AND (t.verificationStatus IS NULL OR t.verificationStatus = 0 OR t.verificationStatus = 2 OR t.verificationStatus = 3)';
		$sql .= 'LIMIT 1';
		//echo '<div>'.$sql.'</div>';
		if($rs = $this->conn->query($sql)){
			while($accArr = $rs->fetch_assoc()){
				$externalTaxonObj = array();
				if($externalTaxonObj){
					$this->verifyTaxonObj($externalTaxonObj,$accArr,$accArr['tid']);
				}
				else{
					$this->logOrEcho('Taxon not found', 1);
				}
			}
			$rs->free();
		}
		else{
			$this->logOrEcho('ERROR: unable query accepted taxa',1);
			$this->logOrEcho($sql);
		}
		$this->logOrEcho('Finished accepted taxa verification');

		$this->logOrEcho('Starting remaining taxa verification');
		$sql = 'SELECT t.sciname, t.tid, t.author, ts.tidaccepted FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'WHERE (ts.taxauthid = '.$this->taxAuthId.') '.
			'AND (t.verificationStatus IS NULL OR t.verificationStatus = 0 OR t.verificationStatus = 2 OR t.verificationStatus = 3)';
		$sql .= 'LIMIT 1';
		//echo '<div>'.$sql.'</div>';
		if($rs = $this->conn->query($sql)){
			while($taxonArr = $rs->fetch_assoc()){
				$externalTaxonObj = array();
				if($verSource === 'col') {
					$externalTaxonObj = $this->getTaxonObjSpecies2000($taxonArr['sciname']);
				}
				if($externalTaxonObj){
					$this->verifyTaxonObj($externalTaxonObj,$taxonArr,$taxonArr['tidaccepted']);
				}
				else{
					$this->logOrEcho('Taxon not found', 1);
				}
			}
			$rs->free();
		}
		else{
			$this->logOrEcho('ERROR: unable query unaccepted taxa',1);
			$this->logOrEcho($sql);
		}
		$this->logOrEcho('Finishing remaining taxa verification');
	}

	private function verifyTaxonObj($externalTaxonObj, $internalTaxonObj, $tidCurrentAccepted): void
	{
		if($externalTaxonObj){
			$source = $externalTaxonObj['source_database'];
			if($this->testValidity){
				$sql = 'UPDATE taxa SET validitystatus = 1, validitysource = "'.$source.'" WHERE (tid = '.$internalTaxonObj['tid'].')';
				$this->conn->query($sql);
			}
			if($this->checkAuthor && $externalTaxonObj['author'] && $internalTaxonObj['author'] !== $externalTaxonObj['author']) {
				$sql = 'UPDATE taxa SET author = '.$externalTaxonObj['author'].' WHERE (tid = '.$internalTaxonObj['tid'].')';
				$this->conn->query($sql);
			}
			if($this->testTaxonomy){
				$nameStatus = $externalTaxonObj['name_status'];

				if($this->verificationMode === 0){
					if($nameStatus === 'accepted'){
						$synArr = $externalTaxonObj['synonyms'];
						foreach($synArr as $synObj){
							$this->evaluateTaxonomy($synObj,$tidCurrentAccepted);
						}
					}
				}
				elseif($this->verificationMode === 1){
					if($externalTaxonObj['tid'] === $tidCurrentAccepted){
						if($nameStatus === 'accepted'){
							$synArr = $externalTaxonObj['synonyms'];
							foreach($synArr as $synObj){
								$this->evaluateTaxonomy($synObj,$tidCurrentAccepted);
							}
						}
						elseif($nameStatus === 'synonym'){
							$accObj = $externalTaxonObj['accepted_name'];
							$accTid = $this->evaluateTaxonomy($accObj,0);
							$sql = 'UPDATE taxstatus SET tidaccetped = '.$accTid.' WHERE (taxauthid = '.$this->taxAuthId.') AND (tid = '.
								$externalTaxonObj['tid'].') AND (tidaccepted = '.$tidCurrentAccepted.')';
							$this->conn->query($sql);
							$this->updateDependentData($externalTaxonObj['tid'],$accTid);
							$synArr = $externalTaxonObj['synonyms'];
							foreach($synArr as $synObj){
								$this->evaluateTaxonomy($synObj,$accTid);
							}
						}
					}
					else if($nameStatus === 'accepted'){
						$this->evaluateTaxonomy($externalTaxonObj,0);
					}
					elseif($nameStatus === 'synonym'){
						$sql = 'SELECT sciname FROM taxa WHERE (tid = '.$externalTaxonObj['tidaccepted'].')';
						$rs = $this->conn->query($sql);
						$systemAccName = '';
						if($r = $rs->fetch_object()){
							$systemAccName = $r->sciname;
						}
						$rs->free();
						$accObj = $externalTaxonObj['accepted_name'];
						if($accObj['name'] !== $systemAccName){
							$tidToBeAcc = $this->evaluateTaxonomy($accObj,0);
							$sql = 'UPDATE taxstatus SET tidaccetped = '.$tidToBeAcc.' WHERE (taxauthid = '.$this->taxAuthId.') AND (tid = '.
								$externalTaxonObj['tid'].') AND (tidaccepted = '.$externalTaxonObj['tidaccepted'].')';
							$this->conn->query($sql);
						}
					}
				}
			}
		}
		else if($this->testValidity){
			$sql = 'UPDATE taxa SET validitystatus = 0, validitysource = "Species 2000" WHERE (tid = '.$externalTaxonObj['tid'].')';
			$this->conn->query($sql);
		}
	}

	private function evaluateTaxonomy($testObj, $anchorTid){
		$retTid = 0;
		$sql = 'SELECT t.tid, ts.tidaccepted, t.sciname, t.author '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'WHERE (ts.taxauthid = '.$this->taxAuthId.')';
		if(array_key_exists('name',$testObj)){
			$sql .= ' AND (t.sciname = "'.$testObj['name'].'")';
		}
		else{
			$sql .= ' AND (t.tid = "'.$testObj['tid'].'")';
		}
		$rs = $this->conn->query($sql);
		if($rs){
			if($this->testValidity){
				$sql = 'UPDATE taxa SET validitystatus = 1, validitysource = "Species 2000" WHERE (t.sciname = "'.$testObj['name'].'")';
				$this->conn->query($sql);
			}
			while($r = $rs->fetch_object()){
				$retTid = $r->tid;
				if(!$anchorTid) {
					$anchorTid = $retTid;
				}
				$tidAcc = $r->tidaccepted;
				if($tidAcc !== $anchorTid){
					$sql = 'UPDATE taxstatus SET tidaccepted = '.$anchorTid.' WHERE (taxauthid = '.$this->taxAuthId.
						') AND (tid = '.$retTid.') AND (tidaccepted = '.$tidAcc.')';
					$this->conn->query($sql);
					$sql = 'UPDATE taxstatus SET tidaccepted = '.$anchorTid.' WHERE (taxauthid = '.$this->taxAuthId.
						') AND (tidaccepted = '.$retTid.')';
					$this->conn->query($sql);
					if($retTid === $tidAcc){
						$this->updateDependentData($tidAcc,$anchorTid);
					}
				}
			}
		}
		else{
			$parsedArr = (new TaxonomyUtilities)->parseScientificName($testObj['name'],$this->conn);
			// $this->loadNewTaxon($parsedArr);
		}
		return $retTid;
	}

	private function updateDependentData($tid, $tidNew): void
	{
		if(is_numeric($tid) && is_numeric($tidNew)){
			$this->conn->query('DELETE FROM kmdescr WHERE inherited IS NOT NULL AND (tid = '.$tid.')');
			$this->conn->query('UPDATE IGNORE kmdescr SET tid = '.$tidNew.' WHERE (tid = '.$tid.')');
			$this->conn->query('DELETE FROM kmdescr WHERE (tid = '.$tid.')');
			$this->resetCharStateInheritance($tidNew);

			$sqlVerns = 'UPDATE taxavernaculars SET tid = '.$tidNew.' WHERE (tid = '.$tid.')';
			$this->conn->query($sqlVerns);

			$sqltl = 'UPDATE taxalinks SET tid = '.$tidNew.' WHERE (tid = '.$tid.')';
			$this->conn->query($sqltl);
		}
	}

	private function resetCharStateInheritance($tid): void
	{
		$sqlAdd1 = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
			'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
			'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
			'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
			'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
			'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
			'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
			'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
			'WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.tid = ts2.tidaccepted) '.
			'AND (t2.tid = '.$tid.') AND ISNULL(d2.CID)';
		$this->conn->query($sqlAdd1);

		if($this->rankId === 140){
			$sqlAdd2a = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
				'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
				'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
				'WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.tid = ts2.tidaccepted) '.
				'AND (t2.RankId = 180) AND (t1.tid = '.$tid.') AND ISNULL(d2.CID)';
			$this->conn->query($sqlAdd2a);
			$sqlAdd2b = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
				'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
				'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
				"WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.family = '".$this->sciName."') AND (ts2.tid = ts2.tidaccepted) ".
				'AND (t2.RankId = 220) AND ISNULL(d2.CID)';
			$this->conn->query($sqlAdd2b);
		}

		if($this->rankId > 140 && $this->rankId < 220){
			$sqlAdd3 = 'INSERT INTO kmdescr ( TID, CID, CS, Modifier, X, TXT, Seq, Notes, Inherited ) '.
				'SELECT DISTINCT t2.TID, d1.CID, d1.CS, d1.Modifier, d1.X, d1.TXT, '.
				'd1.Seq, d1.Notes, IFNULL(d1.Inherited,t1.SciName) AS parent '.
				'FROM ((((taxa AS t1 INNER JOIN kmdescr d1 ON t1.TID = d1.TID) '.
				'INNER JOIN taxstatus ts1 ON d1.TID = ts1.tid) '.
				'INNER JOIN taxstatus ts2 ON ts1.tidaccepted = ts2.ParentTID) '.
				'INNER JOIN taxa t2 ON ts2.tid = t2.tid) '.
				'LEFT JOIN kmdescr d2 ON (d1.CID = d2.CID) AND (t2.TID = d2.TID) '.
				'WHERE (ts1.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ts2.tid = ts2.tidaccepted) '.
				'AND (t2.RankId = 220) AND (t1.tid = '.$tid.') AND ISNULL(d2.CID)';
			$this->conn->query($sqlAdd3);
		}
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
			$tokenArr = explode(' ',$queryString);
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
