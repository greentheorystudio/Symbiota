<?php
include_once('DbConnection.php');

class TaxonomyUtilities {

	private $conn;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function parseScientificName($inStr, $rankId = 0): array
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
			if(count($sciNameArr)){
				if(strtolower($sciNameArr[0]) === 'x'){
					$retArr['unitind1'] = array_shift($sciNameArr);
				}
				$retArr['unitname1'] = ucfirst(strtolower(array_shift($sciNameArr)));
				if(count($sciNameArr)){
                    if(strtolower($sciNameArr[0]) === 'x'){
                        $retArr['unitind2'] = array_shift($sciNameArr);
                        $retArr['unitname2'] = array_shift($sciNameArr);
                    }
                    elseif(strpos($sciNameArr[0],'.') !== false){
                        $retArr['author'] = implode(' ',$sciNameArr);
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
				if($retArr['unitname2'] && !preg_match('/^[\-a-z]+$/', $retArr['unitname2'])) {
					$retArr['unitname2'] = strtolower($retArr['unitname2']);
					if(!preg_match('/^[a-z]+$/',$retArr['unitname2'])){
						$retArr['unitname2'] = '';
						unset($sciNameArr);
					}
				}
			}
			if(isset($sciNameArr) && $sciNameArr){
				if($rankId === 220){
					$retArr['author'] = implode(' ',$sciNameArr);
				}
				else{
					$authorArr = array();
					while($sciStr = array_shift($sciNameArr)){
						$sciStrTest = strtolower($sciStr);
						if($sciStrTest === 'f.' || $sciStrTest === 'fo.' || $sciStrTest === 'fo' || $sciStrTest === 'forma'){
							self::setInfraNode($sciStr, $sciNameArr, $retArr, $authorArr, 'f.');
						}
						elseif($sciStrTest === 'var.' || $sciStrTest === 'var' || $sciStrTest === 'v.'){
							self::setInfraNode($sciStr, $sciNameArr, $retArr, $authorArr, 'var.');
						}
						elseif($sciStrTest === 'ssp.' || $sciStrTest === 'ssp' || $sciStrTest === 'subsp.' || $sciStrTest === 'subsp' || $sciStrTest === 'sudbsp.'){
							self::setInfraNode($sciStr, $sciNameArr, $retArr, $authorArr, 'subsp.');
						}
						elseif(!$retArr['unitname3'] && ($rankId === 230 || preg_match('/^[a-z]{5,}$/',$sciStr))){
							$retArr['unitind3'] = '';
							$retArr['unitname3'] = $sciStr;
							unset($authorArr);
							$authorArr = array();
						}
						else{
							$authorArr[] = $sciStr;
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
								$retArr['author'] = implode(' ',$arr);
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
			$sciname = (isset($retArr['unitind1'])?$retArr['unitind1'].' ':'').$retArr['unitname1'].' ';
			$sciname .= (isset($retArr['unitind2'])?$retArr['unitind2'].' ':'').$retArr['unitname2'].' ';
			$sciname .= $retArr['unitind3'].' '.$retArr['unitname3'];
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

	public function buildHierarchyEnumTree($taxAuthId = 1){
		$status = true;
        $complete = false;
        $sql = 'INSERT INTO taxaenumtree(tid,parenttid,taxauthid) '.
            'SELECT DISTINCT ts.tid, ts.parenttid, ts.taxauthid '.
            'FROM taxstatus ts '.
            'WHERE (ts.taxauthid = '.$taxAuthId.') AND ts.tid NOT IN(SELECT tid FROM taxaenumtree WHERE taxauthid = '.$taxAuthId.')';
        //echo '<div>SQL1: '.$sql.'</div>';
        if(!$this->conn->query($sql)){
            $status = 'ERROR seeding taxaenumtree: '.$this->conn->error;
        }
        if($status === true){
            $sql2 = 'INSERT INTO taxaenumtree(tid,parenttid,taxauthid) '.
                'SELECT DISTINCT e.tid, ts.parenttid, ts.taxauthid '.
                'FROM taxaenumtree e INNER JOIN taxstatus ts ON e.parenttid = ts.tid AND e.taxauthid = ts.taxauthid '.
                'LEFT JOIN taxaenumtree e2 ON e.tid = e2.tid AND ts.parenttid = e2.parenttid AND e.taxauthid = e2.taxauthid '.
                'WHERE (ts.taxauthid = '.$taxAuthId.') AND ISNULL(e2.tid)';
            //echo '<div>SQL2: '.$sql2.'</div>';
            $cnt = 0;
            do{
                if(!$this->conn->query($sql2)){
                    $status = 'ERROR building taxaenumtree: '.$this->conn->error;
                    $complete = true;
                }
                if(!$this->conn->affected_rows) {
                    $complete = true;
                }
                $cnt++;
            }
            while($cnt < 100 && !$complete);
        }

		return $status;
	}
}
