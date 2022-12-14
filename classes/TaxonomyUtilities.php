<?php
include_once(__DIR__ . '/DbConnection.php');

class TaxonomyUtilities {

	private $conn;

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
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $this->deleteTidFromHierarchyTable($tidStr);
                $this->primeHierarchyTable($tidStr);
                do {
                    $hierarchyAdded = $this->populateHierarchyTable($tidStr);
                } while($hierarchyAdded > 0);
            }
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
            $retArr[$r->kingdom_id] = $r->sciname;
        }
        $rs->free();
        asort($retArr);
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
            $sql .= 'WHERE kingdomId = ' . $kingdomId;
        }
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($name !== $r->sciname && levenshtein($name,$r->sciname) <= $levDistance){
                    $retArr[$r->tid] = $r->sciname;
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
}
