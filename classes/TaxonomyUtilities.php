<?php
include_once(__DIR__ . '/DbConnection.php');

class TaxonomyUtilities {

	private $conn;
    private $rankLimit = 0;
    private $rankLow = 0;
    private $rankHigh = 0;
    private $limit = 0;
    private $hideAuth = false;
    private $hideProtected = false;
    private $acceptedOnly = false;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function parseScientificName($inStr, $rankId = null): array
	{
        $retArr = array('unitname1'=>'','unitname2'=>'','unitind3'=>'','unitname3'=>'');
        if($inStr && is_string($inStr)){
			$inStr = preg_replace('/_+/',' ',$inStr);
			$inStr = str_replace(array('?','*'),'',$inStr);

			if(stripos($inStr,'cfr. ') !== false || stripos($inStr,' cfr ') !== false){
				$retArr['identificationqualifier'] = 'cf. ';
				$inStr = str_ireplace(array(' cfr ','cfr. '),' ',$inStr);
			}
			elseif(stripos($inStr,'cf. ') !== false || stripos($inStr,'c.f. ') !== false || stripos($inStr,' cf ') !== false){
				$retArr['identificationqualifier'] = 'cf. ';
				$inStr = str_ireplace(array(' cf ','c.f. ','cf. '),' ',$inStr);
			}
			elseif(stripos($inStr,'aff. ') !== false || stripos($inStr,' aff ') !== false){
				$retArr['identificationqualifier'] = 'aff.';
				$inStr = trim(str_ireplace(array(' aff ','aff. '),' ',$inStr));
			}
			if(stripos($inStr,' spp.')){
				$rankId = 180;
				$inStr = str_ireplace(' spp.','',$inStr);
			}
			if(stripos($inStr,' sp.')){
				$rankId = 180;
				$inStr = str_ireplace(' sp.','',$inStr);
			}
			$inStr = preg_replace('/\s\s+/',' ',$inStr);

			$sciNameArr = explode(' ',$inStr);
			if($sciNameArr){
                if(strtolower($sciNameArr[0]) === 'x'){
					$retArr['unitind1'] = array_shift($sciNameArr);
				}
				$retArr['unitname1'] = ucfirst(strtolower(array_shift($sciNameArr)));
				if(count($sciNameArr)){
                    $secondStr = $sciNameArr[0];
                    if($secondStr[0] === '"' || $secondStr[0] === "'" || $sciNameArr[0] === 'sect.' || $sciNameArr[0] === 'sp' || $sciNameArr[0] === 'sp.' || $sciNameArr[0] === 'subgenus' || $sciNameArr[0] === 'subsect.'){
                        unset($sciNameArr);
                    }
                    elseif(strtolower($sciNameArr[0]) === 'x'){
                        $retArr['unitind2'] = array_shift($sciNameArr);
                        $retArr['unitname2'] = array_shift($sciNameArr);
                    }
                    elseif(strpos($sciNameArr[0],'.') !== false){
                        $retArr['author'] = implode(' ',$sciNameArr);
                        $retArr['author2'] = $sciNameArr[0];
                        unset($sciNameArr);
                    }
                    else{
                        if(strpos($sciNameArr[0],'(') !== false){
                            $retArr['author'] = implode(' ',$sciNameArr);
                            array_shift($sciNameArr);
                        }
                        $retArr['unitname2'] = array_shift($sciNameArr);
                    }
                }
                if(isset($sciNameArr) && $retArr['unitname2'] && !preg_match('/^[\-\'a-z]+$/',$retArr['unitname2'])){
                    if(preg_match('/[A-Z][\-\'a-z]+/',$retArr['unitname2'])){
                        $sql = 'SELECT tid FROM taxa '.
                            'WHERE unitname1 = "'.$retArr['unitname1'].'" AND unitname2 = "'.$retArr['unitname2'].'" ';
                        //echo $sql.'<br/>';
                        $rs = $this->conn->query($sql);
                        if($rs->num_rows){
                            if(isset($retArr['author'])){
                                unset($retArr['author']);
                            }
                        }
                        else{
                            $retArr['author'] = trim($retArr['unitname2'].' '.implode(' ', $sciNameArr));
                            $retArr['unitname2'] = '';
                            unset($sciNameArr);
                        }
                        $rs->free();
                        $this->conn->close();
                    }
                    if(isset($sciNameArr) && $retArr['unitname2']){
                        $retArr['unitname2'] = strtolower($retArr['unitname2']);
                        if(!preg_match('/^[\-\'a-z]+$/',$retArr['unitname2'])){
                            $retArr['author'] = trim($retArr['unitname2'].' '.implode(' ', $sciNameArr));
                            $retArr['unitname2'] = '';
                            unset($sciNameArr);
                        }
                    }
                }
			}
			if(isset($sciNameArr) && $sciNameArr){
                $testAuthor = implode(' ',$sciNameArr);
                if($rankId === 220 || preg_match('~^\p{Lu}~u', $testAuthor) || $testAuthor[0] === '('){
                    $retArr['author'] = $testAuthor;
				}
				else{
					$authorArr = array();
					while($sciStr = array_shift($sciNameArr)){
						$sciStrTest = strtolower($sciStr);
                        if(stripos($sciStrTest,' x ') === false && strpos($sciStrTest,'"') === false && substr_count($sciStrTest,"'") < 2){
                            if($sciStrTest === 'f.' || $sciStrTest === 'fo.' || $sciStrTest === 'fo' || $sciStrTest === 'forma'){
                                self::setInfraNode($sciStr, $sciNameArr, $retArr, $authorArr, 'f.');
                            }
                            elseif($sciStrTest === 'var.' || $sciStrTest === 'var' || $sciStrTest === 'v.'){
                                self::setInfraNode($sciStr, $sciNameArr, $retArr, $authorArr, 'var.');
                            }
                            elseif($sciStrTest === 'ssp.' || $sciStrTest === 'ssp' || $sciStrTest === 'subsp.' || $sciStrTest === 'subsp' || $sciStrTest === 'sudbsp.'){
                                self::setInfraNode($sciStr, $sciNameArr, $retArr, $authorArr, 'subsp.');
                            }
                            elseif($sciStrTest === 'x' || $sciStrTest === 'X'){
                                self::setInfraNode($sciStr, $sciNameArr, $retArr, $authorArr, 'X');
                            }
                            elseif(!$retArr['unitname3'] && ($rankId === 230 || preg_match('/^[a-z]{5,}$/',$sciStr))){
                                $retArr['unitind3'] = '';
                                $retArr['unitname3'] = $sciStr;
                                unset($authorArr);
                                $authorArr = array();
                            }
                            elseif(preg_match('/[A-Z]+/',$sciStr)){
                                $authorArr[] = $sciStr;
                            }
                            else{
                                $authorArr = array();
                            }
                        }
					}
					$retArr['author'] = implode(' ', $authorArr);
					if(!$retArr['unitname3'] && $retArr['author']){
						$arr = explode(' ',$retArr['author']);
						$firstWord = array_shift($arr);
						if(preg_match('/^[a-z]{2,}$/',$firstWord)){
							$sql = 'SELECT unitind3 FROM taxa '.
								'WHERE unitname1 = "'.$retArr['unitname1'].'" AND unitname2 = "'.$retArr['unitname2'].'" AND unitname3 = "'.$firstWord.'" ';
							//echo $sql.'<br/>';
							$rs = $this->conn->query($sql);
							if($r = $rs->fetch_object()){
								$retArr['unitind3'] = $r->unitind3;
								$retArr['unitname3'] = $firstWord;
                                $authorStr = implode(' ',$arr);
								if(preg_match('/[A-Z]+/',$authorStr)){
                                    $retArr['author'] = implode(' ',$arr);
                                }
                                else{
                                    $retArr['author'] = '';
                                }
							}
							$rs->free();
							$this->conn->close();
						}
					}
				}
			}
			if(array_key_exists('unitind3',$retArr) && $retArr['unitind3'] === 'ssp.'){
				$retArr['unitind3'] = 'subsp.';
			}
			$sciname = ((isset($retArr['unitind1']) && $retArr['unitind1'])?$retArr['unitind1'].' ':'').$retArr['unitname1'].' ';
			$sciname .= ((isset($retArr['unitind2']) && $retArr['unitind2'])?$retArr['unitind2'].' ':'').$retArr['unitname2'].' ';
			$sciname .= ((isset($retArr['unitind3']) && $retArr['unitind3'])?$retArr['unitind3'].' ':'').$retArr['unitname3'];
			$retArr['sciname'] = trim($sciname);
			if($rankId && is_numeric($rankId)){
				$retArr['rankid'] = $rankId;
			}
			else if($retArr['unitname3']){
				if($retArr['unitind3'] === 'subsp.' || !$retArr['unitind3']){
					$retArr['rankid'] = 230;
				}
				elseif($retArr['unitind3'] === 'var.'){
					$retArr['rankid'] = 240;
				}
				elseif($retArr['unitind3'] === 'f.'){
					$retArr['rankid'] = 260;
				}
			}
			elseif($retArr['unitname2']){
				$retArr['rankid'] = 220;
			}
			elseif($retArr['unitname1']){
				if(substr($retArr['unitname1'],-5) === 'aceae' || substr($retArr['unitname1'],-4) === 'idae'){
					$retArr['rankid'] = 140;
				}
			}
		}
		return $retArr;
	}

    public function formatScientificName($inStr){
        $sciNameStr = trim($inStr);
        $sciNameStr = preg_replace('/\s\s+/', ' ',$sciNameStr);
        $tokens = explode(' ',$sciNameStr);
        if($tokens){
            $sciNameStr = array_shift($tokens);
            if(strlen($sciNameStr) < 2) {
                $sciNameStr = ' ' . array_shift($tokens);
            }
            if($tokens){
                $term = array_shift($tokens);
                $sciNameStr .= ' '.$term;
                if($term === 'x') {
                    $sciNameStr .= ' ' . array_shift($tokens);
                }
            }
            $tRank = '';
            $infraSp = '';
            foreach($tokens as $c => $v){
                switch($v) {
                    case 'subsp.':
                    case 'subsp':
                    case 'ssp.':
                    case 'ssp':
                    case 'subspecies':
                    case 'var.':
                    case 'var':
                    case 'variety':
                    case 'forma':
                    case 'form':
                    case 'f.':
                    case 'fo.':
                        if(array_key_exists($c+1,$tokens) && ctype_lower($tokens[$c+1])){
                            $tRank = $v;
                            if(($tRank === 'ssp' || $tRank === 'subsp' || $tRank === 'var') && substr($tRank,-1) !== '.') {
                                $tRank .= '.';
                            }
                            $infraSp = $tokens[$c+1];
                        }
                }
            }
            if($infraSp){
                $sciNameStr .= ' '.$tRank.' '.$infraSp;
            }
        }
        return $sciNameStr;
    }

    private static function setInfraNode($sciStr, &$sciNameArr, &$retArr, &$authorArr, $rankTag): void
	{
		if($sciNameArr){
			$infraStr = array_shift($sciNameArr);
			if(preg_match('/^[a-z]{3,}$/', $infraStr)){
				$retArr['unitind3'] = $rankTag;
				$retArr['unitname3'] = $infraStr;
				$authorArr = array();
			}
			else{
				$authorArr[] = $sciStr;
				$authorArr[] = $infraStr;
			}
		}
	}

    public function primeHierarchyTable($tid = null): int
    {
        $retCnt = 0;
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'INSERT IGNORE INTO taxaenumtree(tid,parenttid) '.
                    'SELECT DISTINCT tid, parenttid FROM taxa '.
                    'WHERE tid IN('.$tidStr.') AND tid NOT IN(SELECT tid FROM taxaenumtree) AND parenttid IS NOT NULL ';
                //echo $sql . '<br />';;
                if($this->conn->query($sql)){
                    $retCnt += $this->conn->affected_rows;
                }
            }
        }
        return $retCnt;
    }

    public function populateHierarchyTable($tid = null): int
    {
        $retCnt = 0;
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'INSERT IGNORE INTO taxaenumtree(tid,parenttid) '.
                    'SELECT DISTINCT e.tid, t.parenttid '.
                    'FROM taxaenumtree AS e LEFT JOIN taxa AS t ON e.parenttid = t.tid '.
                    'WHERE e.tid IN('.$tidStr.') AND t.parenttid NOT IN(SELECT parenttid FROM taxaenumtree WHERE tid IN('.$tidStr.')) ';
                //echo $sql . '<br />';
                if($this->conn->query($sql)){
                    $retCnt += $this->conn->affected_rows;
                }
            }
        }
        return $retCnt;
    }

    public function updateHierarchyTable($tid = null): void
    {
        if(is_array($tid) || is_numeric($tid)){
            $this->deleteTidFromHierarchyTable($tid);
            $this->primeHierarchyTable($tid);
            do {
                $hierarchyAdded = $this->populateHierarchyTable($tid);
            } while($hierarchyAdded > 0);
        }
    }

    public function deleteTidFromHierarchyTable($tid): void
    {
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'DELETE FROM taxaenumtree '.
                    'WHERE tid IN('.$tidStr.') OR parenttid IN('.$tidStr.') ';
                //echo $sql;
                $this->conn->query($sql);
            }
        }
    }

    public function getChildTidArr($tid): array
    {
        $returnArr = array();
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'SELECT tid FROM taxaenumtree '.
                    'WHERE parenttid IN('.$tidStr.') ';
                //echo $sql;
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $returnArr[] = (int)$r->tid;
                }
                $rs->free();
            }
        }
        return $returnArr;
    }

    public function updateFamily($tid): void
    {
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'UPDATE taxa AS t LEFT JOIN taxaenumtree AS te ON t.TID = te.tid '.
                    'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID '.
                    'SET t.family = t2.SciName '.
                    'WHERE t.TID IN('.$tidStr.') AND t.RankId > 140 AND t2.RankId = 140 ';
                //echo $sql;
                $this->conn->query($sql);
            }
        }
    }

	public function getTidAccepted($tid): int
    {
        $retTid = 0;
        $sql = 'SELECT tidaccepted FROM taxa WHERE tid = '.$tid.' ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retTid = (int)$r->tidaccepted;
        }
        $rs->free();
        return $retTid;
    }

    public function getKingdomArr(): array
    {
        $retArr = array();
        $sql = 'SELECT k.kingdom_id, t.sciname '.
            'FROM taxonkingdoms AS k LEFT JOIN taxa AS t ON k.kingdom_name = t.SciName '.
            'WHERE t.TID IS NOT NULL '.
            'ORDER BY t.SciName ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $tArr = array();
            $tArr['id'] = $r->kingdom_id;
            $tArr['name'] = $r->sciname;
            $retArr[] = $tArr;
        }
        $rs->free();
        return $retArr;
    }

    public function getSensitiveTaxa(): array
    {
        $sensitiveArr = array();
        $sql = 'SELECT DISTINCT tid FROM taxa WHERE (SecurityStatus > 0)';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $sensitiveArr[] = $r->tid;
        }
        $rs->free();
        $sql2 = 'SELECT DISTINCT tidaccepted FROM taxa '.
            'WHERE SecurityStatus > 0 AND tid <> tidaccepted ';
        $rs2 = $this->conn->query($sql2);
        while($r2 = $rs2->fetch_object()){
            $sensitiveArr[] = $r2->tidaccepted;
        }
        $rs2->free();
        return $sensitiveArr;
    }

    public function getCloseTaxaMatches($name,$levDistance,$kingdomId = null): array
    {
        $retArr = array();
        $sql = 'SELECT tid, sciname FROM taxa ';
        if($kingdomId){
            $sql .= 'WHERE kingdomId = ' . $kingdomId . ' ';
        }
        $sql .= 'ORDER BY sciname ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($name !== $r->sciname && levenshtein($name,$r->sciname) <= $levDistance){
                    $valArr = array();
                    $valArr['tid'] = $r->tid;
                    $valArr['sciname'] = $r->sciname;
                    $retArr[] = $valArr;
                }
            }
        }
        return $retArr;
    }

    public function getRankNameArr(): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT rankname, rankid FROM taxonunits ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $rankName = strtolower($r->rankname);
            $retArr[$rankName] = (int)$r->rankid;
        }
        $rs->free();
        return $retArr;
    }

    public function getRankArr($kingdomId = null): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT rankid, rankname FROM taxonunits ';
        if($kingdomId){
            $sql .= 'WHERE kingdomid = ' . $kingdomId . ' ';
        }
        $sql .= 'ORDER BY rankid ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            if(array_key_exists($row->rankid,$retArr)){
                $retArr[$row->rankid]['rankname'] .= ', ' . $row->rankname;
            }
            else{
                $retArr[$row->rankid]['rankname'] = $row->rankname;
            }
            $retArr[$row->rankid]['rankid'] = (int)$row->rankid;
        }
        $result->free();
        return $retArr;
    }

    public function getAutocompleteSciNameList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT kingdomId, SciName, Author, TID FROM taxa ';
        $sql .= 'WHERE SciName LIKE "'.Sanitizer::cleanInStr($this->conn,$queryString).'%" ';
        if($this->rankLimit){
            $sql .= 'AND RankId = '.$this->rankLimit.' ';
        }
        else{
            if($this->rankLow){
                $sql .= 'AND RankId >= '.$this->rankLow.' ';
            }
            if($this->rankHigh){
                $sql .= 'AND RankId <= '.$this->rankHigh.' ';
            }
        }
        if($this->hideProtected){
            $sql .= 'AND SecurityStatus <> 1 ';
        }
        if($this->acceptedOnly){
            $sql .= 'AND TID = tidaccepted ';
        }
        $sql .= 'ORDER BY SciName ';
        if($this->limit){
            $sql .= 'LIMIT '.$this->limit.' ';
        }
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $label = $r->SciName.($this->hideAuth?'':' '.$r->Author);
            $scinameArr = array();
            $scinameArr['tid'] = $r->TID;
            $scinameArr['label'] = $label;
            $scinameArr['name'] = $r->SciName;
            $scinameArr['kingdomid'] = $r->kingdomId;
            $retArr[] = $scinameArr;
        }

        return $retArr;
    }

    public function getAutocompleteVernacularList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT v.VernacularName '.
            'FROM taxavernaculars AS v ';
        $sql .= 'WHERE v.VernacularName LIKE "%'.Sanitizer::cleanInStr($this->conn,$queryString).'%" ';
        $sql .= 'ORDER BY v.VernacularName ';
        if($this->limit){
            $sql .= 'LIMIT '.$this->limit.' ';
        }
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $scinameArr = array();
            $scinameArr['tid'] = '';
            $scinameArr['label'] = $r->VernacularName;
            $scinameArr['name'] = $r->VernacularName;
            $retArr[] = $scinameArr;
        }

        return $retArr;
    }

    public function getImageCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(i.imgid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN images AS i ON t.TID = i.tid '.
            'WHERE (te.parenttid = '.$tid.' OR t.TID = '.$tid.') AND t.TID = t.tidaccepted ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(i.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getTaxonImages($tid, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT imgid, url, thumbnailurl, originalurl, archiveurl, photographer, imagetype, format, caption, owner, '.
            'sourceurl, referenceUrl, rights, accessrights, locality, occid, notes, anatomy, mediaMD5, dynamicProperties, sortsequence '.
            'FROM images '.
            'WHERE tid = '.$tid.' ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(occid) ';
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['imgid'] = $row->imgid;
            $resultArr['url'] = $row->url;
            $resultArr['thumbnailurl'] = $row->thumbnailurl;
            $resultArr['originalurl'] = $row->originalurl;
            $resultArr['archiveurl'] = $row->archiveurl;
            $resultArr['photographer'] = $row->photographer;
            $resultArr['imagetype'] = $row->imagetype;
            $resultArr['format'] = $row->format;
            $resultArr['caption'] = $row->caption;
            $resultArr['owner'] = $row->owner;
            $resultArr['sourceurl'] = $row->sourceurl;
            $resultArr['referenceUrl'] = $row->referenceUrl;
            $resultArr['rights'] = $row->rights;
            $resultArr['accessrights'] = $row->accessrights;
            $resultArr['locality'] = $row->locality;
            $resultArr['occid'] = $row->occid;
            $resultArr['notes'] = $row->notes;
            $resultArr['anatomy'] = $row->anatomy;
            $resultArr['mediaMD5'] = $row->mediaMD5;
            $resultArr['dynamicProperties'] = $row->dynamicProperties;
            $resultArr['sortsequence'] = $row->sortsequence;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getTaxonVideos($tid, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT mediaid, occid, accessuri, title, creator, type, format, owner, furtherinformationurl, language, usageterms, '.
            'rights, bibliographiccitation, publisher, contributor, locationcreated, description, sortsequence '.
            'FROM media '.
            'WHERE tid = '.$tid.' AND format LIKE "video/%" ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(occid) ';
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['mediaid'] = $row->mediaid;
            $resultArr['occid'] = $row->occid;
            $resultArr['accessuri'] = $row->accessuri;
            $resultArr['title'] = $row->title;
            $resultArr['creator'] = $row->creator;
            $resultArr['type'] = $row->type;
            $resultArr['format'] = $row->format;
            $resultArr['owner'] = $row->owner;
            $resultArr['furtherinformationurl'] = $row->furtherinformationurl;
            $resultArr['language'] = $row->language;
            $resultArr['usageterms'] = $row->usageterms;
            $resultArr['rights'] = $row->rights;
            $resultArr['bibliographiccitation'] = $row->bibliographiccitation;
            $resultArr['publisher'] = $row->publisher;
            $resultArr['contributor'] = $row->contributor;
            $resultArr['locationcreated'] = $row->locationcreated;
            $resultArr['description'] = $row->description;
            $resultArr['sortsequence'] = $row->sortsequence;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getVideoCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(m.mediaid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN media AS m ON t.TID = m.tid '.
            'WHERE (te.parenttid = '.$tid.' OR t.TID = '.$tid.') AND t.TID = t.tidaccepted AND (m.format LIKE "video/%" OR ISNULL(m.format)) ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(m.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getAudioCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(m.mediaid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN media AS m ON t.TID = m.tid '.
            'WHERE (te.parenttid = '.$tid.' OR t.TID = '.$tid.') AND t.TID = t.tidaccepted AND (m.format LIKE "audio/%" OR ISNULL(m.format)) ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(m.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getTaxonAudios($tid, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT mediaid, occid, accessuri, title, creator, type, format, owner, furtherinformationurl, language, usageterms, '.
            'rights, bibliographiccitation, publisher, contributor, locationcreated, description, sortsequence '.
            'FROM media '.
            'WHERE tid = '.$tid.' AND format LIKE "audio/%" ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(occid) ';
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['mediaid'] = $row->mediaid;
            $resultArr['occid'] = $row->occid;
            $resultArr['accessuri'] = $row->accessuri;
            $resultArr['title'] = $row->title;
            $resultArr['creator'] = $row->creator;
            $resultArr['type'] = $row->type;
            $resultArr['format'] = $row->format;
            $resultArr['owner'] = $row->owner;
            $resultArr['furtherinformationurl'] = $row->furtherinformationurl;
            $resultArr['language'] = $row->language;
            $resultArr['usageterms'] = $row->usageterms;
            $resultArr['rights'] = $row->rights;
            $resultArr['bibliographiccitation'] = $row->bibliographiccitation;
            $resultArr['publisher'] = $row->publisher;
            $resultArr['contributor'] = $row->contributor;
            $resultArr['locationcreated'] = $row->locationcreated;
            $resultArr['description'] = $row->description;
            $resultArr['sortsequence'] = $row->sortsequence;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getDescriptionCountsForTaxonomicGroup($tid, $index): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(tdb.tdbid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxadescrblock AS tdb ON t.TID = tdb.tid '.
            'WHERE (te.parenttid = '.$tid.' OR t.TID = '.$tid.') AND t.TID = t.tidaccepted '.
            'GROUP BY t.TID '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getTaxonDescriptions($tid): array
    {
        $retArr = array();
        $sql = 'SELECT tdbid, caption, source, sourceurl, language, displaylevel, notes '.
            'FROM taxadescrblock WHERE tid = '.$tid.' '.
            'ORDER BY displaylevel ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $descrArr = array();
                $descrArr['tdbid'] = $r->tdbid;
                $descrArr['caption'] = $r->caption;
                $descrArr['source'] = $r->source;
                $descrArr['sourceurl'] = $r->sourceurl;
                $descrArr['language'] = $r->language;
                $descrArr['displaylevel'] = $r->displaylevel;
                $descrArr['notes'] = $r->notes;
                $descrArr['stmts'] = $this->getTaxonDescriptionStatements($r->tdbid);
                $retArr[] = $descrArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonDescriptionStatements($tdbid): array
    {
        $retArr = array();
        $sql = 'SELECT tdsid, heading, statement, displayheader, notes, sortsequence '.
            'FROM taxadescrstmts WHERE tdbid = '.$tdbid.' '.
            'ORDER BY sortsequence';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $statementArr = array();
                $statementArr['tdsid'] = $r->tdsid;
                $statementArr['heading'] = $r->heading;
                $statementArr['statement'] = $r->statement;
                $statementArr['displayheader'] = $r->displayheader;
                $statementArr['notes'] = $r->notes;
                $statementArr['sortsequence'] = $r->sortsequence;
                $retArr[] = $statementArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getParentTids($tid): array
    {
        $returnArr = array();
        $sql = 'SELECT parenttid FROM taxaenumtree ' .
            'WHERE tid = ' .$tid. ' ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[] = $row->parenttid;
        }
        $result->close();
        return $returnArr;
    }

    public function addTaxonDescriptionTab($description): int
    {
        $retVal = 0;
        if($description){
            $sql = 'INSERT INTO taxadescrblock (tid, caption, `source`, sourceurl, `language`, langid, displaylevel, uid, notes) '.
                'VALUES ('.
                (isset($description['tid']) ? (int)$description['tid'] :'NULL').','.
                (isset($description['caption']) ? '"'.Sanitizer::cleanInStr($this->conn,$description['caption']).'"' :'NULL').','.
                (isset($description['source']) ? '"'.Sanitizer::cleanInStr($this->conn,$description['source']).'"' :'NULL').','.
                (isset($description['sourceurl']) ? '"'.Sanitizer::cleanInStr($this->conn,$description['sourceurl']).'"' :'NULL').','.
                (isset($description['language']) ? '"'.Sanitizer::cleanInStr($this->conn,$description['language']).'"' :'NULL').','.
                (isset($description['langid']) ? (int)$description['langid'] :'NULL').','.
                (isset($description['displaylevel']) ? (int)$description['displaylevel'] :'1').','.
                '"'.$GLOBALS['USERNAME'].'",'.
                (isset($description['notes']) ? '"'.Sanitizer::cleanInStr($this->conn,$description['notes']).'"' :'NULL').')';
            //echo $sql; exit;
            if($this->conn->query($sql)){
                $retVal = $this->conn->insert_id;
            }
        }
        return $retVal;
    }

    public function addTaxonDescriptionStatement($statement): int
    {
        $retVal = 0;
        if($statement){
            $sql = 'INSERT INTO taxadescrstmts (tdbid, heading, `statement`, displayheader, notes, sortsequence) '.
                'VALUES ('.
                (isset($statement['tdbid']) ? (int)$statement['tdbid'] :'NULL').','.
                (isset($statement['heading']) ? '"'.Sanitizer::cleanInStr($this->conn,$statement['heading']).'"' :'NULL').','.
                (isset($statement['statement']) ? '"'.Sanitizer::cleanInStr($this->conn,strip_tags($statement['statement'], '<i><b><em><div>')).'"' :'NULL').','.
                (isset($statement['displayheader']) ? (int)$statement['displayheader'] :'1').','.
                (isset($statement['notes']) ? '"'.Sanitizer::cleanInStr($this->conn,$statement['notes']).'"' :'NULL').','.
                (isset($statement['sortsequence']) ? (int)$statement['sortsequence'] :'1').')';
            //echo $sql; exit;
            if($this->conn->query($sql)){
                $retVal = $this->conn->insert_id;
            }
        }
        return $retVal;
    }

    public function addTaxonIdentifier($tid, $idName, $id): bool
    {
        if($tid && $idName && $id){
            $sql = 'INSERT IGNORE INTO taxaidentifiers(tid,`name`,identifier) VALUES('.
                $tid.',"'.Sanitizer::cleanInStr($this->conn,$idName).'","'.Sanitizer::cleanInStr($this->conn,$id).'")';
            return $this->conn->query($sql);
        }
        return false;
    }

    public function getIdentifiersForTaxonomicGroup($tid, $index, $source): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, ti.identifier '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxaidentifiers AS ti ON t.TID = ti.tid '.
            'WHERE (te.parenttid = '.$tid.' OR t.TID = '.$tid.') AND ti.name = "'.$source.'" '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['identifier'] = $row->identifier;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function taxonHasChildren($tid): bool
    {
        $retVal = false;
        $sql = 'SELECT TID FROM taxa WHERE parenttid = '.$tid.' LIMIT 1 ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($result->num_rows){
            $retVal = true;
        }
        return $retVal;
    }

    public function getTaxonomicTreeKingdomNodes(): array
    {
        $retArr = array();
        $sql = 'SELECT TID, SciName, Author FROM taxa '.
            'WHERE RankId = 10 AND TID = tidaccepted '.
            'ORDER BY SciName ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nTid = $r->TID;
                $expandable = $this->taxonHasChildren($nTid);
                $nodeArr = array();
                $nodeArr['tid'] = $nTid;
                $nodeArr['sciname'] = $r->SciName;
                $nodeArr['author'] = $r->Author;
                $nodeArr['rankname'] = 'Kingdom';
                $nodeArr['nodetype'] = 'child';
                $nodeArr['expandable'] = $expandable;
                $nodeArr['lazy'] = $expandable;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonomicTreeChildNodes($tId): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.Author, tu.rankname '.
            'FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid  '.
            'WHERE t.tidaccepted = '.$tId.' AND TID <> tidaccepted '.
            'ORDER BY tu.rankid, t.SciName ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nTid = $r->TID;
                $nodeArr = array();
                $nodeArr['tid'] = $nTid;
                $nodeArr['sciname'] = $r->SciName;
                $nodeArr['author'] = $r->Author;
                $nodeArr['rankname'] = $r->rankname;
                $nodeArr['nodetype'] = 'synonym';
                $nodeArr['expandable'] = false;
                $nodeArr['lazy'] = false;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }

        $sql = 'SELECT t.TID, t.SciName, t.Author, tu.rankname '.
            'FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid  '.
            'WHERE t.parenttid = '.$tId.' AND TID = tidaccepted '.
            'ORDER BY tu.rankid, t.SciName ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nTid = $r->TID;
                $expandable = $this->taxonHasChildren($nTid);
                $nodeArr = array();
                $nodeArr['tid'] = $nTid;
                $nodeArr['sciname'] = $r->SciName;
                $nodeArr['author'] = $r->Author;
                $nodeArr['rankname'] = $r->rankname;
                $nodeArr['nodetype'] = 'child';
                $nodeArr['expandable'] = $expandable;
                $nodeArr['lazy'] = $expandable;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonomicTreeTaxonPath($tId): array
    {
        $retArr = array();
        $sql = 'SELECT t2.TID, t2.SciName '.
            'FROM taxa AS t LEFT JOIN taxaenumtree AS te ON t.tidaccepted = te.tid  '.
            'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID  '.
            'WHERE t.TID = '.$tId.' AND t2.RankId >= 10 '.
            'ORDER BY t2.RankId ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['tid'] = $r->TID;
                $nodeArr['sciname'] = $r->SciName;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }

        $sql = 'SELECT t.TID, t2.TID AS accTID, t2.SciName AS accSciName '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID  '.
            'WHERE t.TID = '.$tId.' ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($r->TID !== $r->accTID){
                    $nodeArr = array();
                    $nodeArr['tid'] = $r->accTID;
                    $nodeArr['sciname'] = $r->accSciName;
                    $retArr[] = $nodeArr;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxaArrFromNameArr($nameArr): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT sciname, tidaccepted FROM taxa  '.
            'WHERE sciname IN("'.implode('","', $nameArr).'") ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['tid'] = $r->tidaccepted;
                $nodeArr['sciname'] = $r->sciname;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonFromTid($tid, $includeCommonNames = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.SciName, t.Author, k.kingdom_name, t.RankId, t.tidaccepted, t2.SciName AS acceptedSciName, t.parenttid, t3.SciName AS parentSciName '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
            'LEFT JOIN taxa AS t3 ON t.parenttid = t3.TID '.
            'LEFT JOIN taxonkingdoms AS k ON t.kingdomId = k.kingdom_id '.
            'WHERE t.TID = '.$tid.' ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr['sciname'] = $r->SciName;
                $retArr['author'] = $r->Author;
                $retArr['kingdom'] = $r->kingdom_name;
                $retArr['rankid'] = (int)$r->RankId;
                $retArr['tidaccepted'] = $r->tidaccepted;
                $retArr['acceptedsciname'] = $r->acceptedSciName;
                $retArr['parenttid'] = $r->parenttid;
                $retArr['parentsciname'] = $r->parentSciName;
                $retArr['identifiers'] = $this->getTaxonIdentifiersFromTid($tid);
                if($includeCommonNames){
                    $retArr['commonnames'] = $this->getCommonNamesFromTid($tid);
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonIdentifiersFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT `name`, identifier FROM taxaidentifiers WHERE tid = '.$tid.' ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['name'] = $r->name;
                $nodeArr['identifier'] = $r->identifier;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getCommonNamesFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT VernacularName, langid FROM taxavernaculars WHERE TID = '.$tid.' ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['commonname'] = $r->VernacularName;
                $nodeArr['langid'] = $r->langid;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function setRankLimit($val): void
    {
        $this->rankLimit = Sanitizer::cleanInStr($this->conn,$val);
    }

    public function setRankLow($val): void
    {
        $this->rankLow = Sanitizer::cleanInStr($this->conn,$val);
    }

    public function setRankHigh($val): void
    {
        $this->rankHigh = Sanitizer::cleanInStr($this->conn,$val);
    }

    public function setLimit($val): void
    {
        $this->limit = Sanitizer::cleanInStr($this->conn,$val);
    }

    public function setHideAuth($val): void
    {
        if($val === 'true' || (int)$val === 1){
            $this->hideAuth = true;
        }
        else{
            $this->hideAuth = false;
        }
    }

    public function setAcceptedOnly($val): void
    {
        if($val === 'true' || (int)$val === 1){
            $this->acceptedOnly = true;
        }
        else{
            $this->acceptedOnly = false;
        }
    }

    public function setHideProtected($val): void
    {
        $this->hideProtected = Sanitizer::cleanInStr($this->conn,$val);
    }
}
