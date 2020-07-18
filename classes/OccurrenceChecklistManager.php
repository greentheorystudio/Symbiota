<?php
include_once('OccurrenceManager.php');

class OccurrenceChecklistManager extends OccurrenceManager{
	
	private $checklistTaxaCnt = 0;

 	public function __construct(){
 		parent::__construct();
	}

	public function getChecklistTaxaCnt(): int
	{
		return $this->checklistTaxaCnt;
	}

	public function getChecklist($taxonAuthorityId): array
	{
		$returnVec = array();
		$this->checklistTaxaCnt = 0;
		$sqlWhere = $this->getSqlWhere();
        if($taxonAuthorityId && is_numeric($taxonAuthorityId)){
			$sql = 'SELECT DISTINCT ts.family, t.sciname '.
                'FROM ((omoccurrences o INNER JOIN taxstatus ts1 ON o.TidInterpreted = ts1.Tid) '.
                'INNER JOIN taxa t ON ts1.TidAccepted = t.Tid) '.
				'INNER JOIN taxstatus ts ON t.tid = ts.tid ';
			$sql .= $this->setTableJoins($sqlWhere);
			$sql .= str_ireplace('o.sciname', 't.sciname',str_ireplace('o.family', 'ts.family',$sqlWhere)).
				' AND ts1.taxauthid = ' .$taxonAuthorityId. ' AND ts.taxauthid = ' .$taxonAuthorityId. ' AND t.RankId > 140 ';
        }
        else{
			$sql = 'SELECT DISTINCT IFNULL(ts.family,o.family) AS family, o.sciname '.
				'FROM omoccurrences o LEFT JOIN taxa t ON o.tidinterpreted = t.tid '.
				'LEFT JOIN taxstatus ts ON t.tid = ts.tid ';
			$sql .= $this->setTableJoins($sqlWhere);
			$sql .= $sqlWhere. ' AND (t.rankid > 140) AND (ts.taxauthid = 1) ';
        }
		//echo "<div>".$sql."</div>"; 
        $result = $this->conn->query($sql);
		while($row = $result->fetch_object()){
			$family = strtoupper($row->family);
			if(!$family) {
				$family = 'undefined';
			}
			$sciName = $row->sciname;
			if($sciName && substr($sciName,-5) !== 'aceae'){
				$returnVec[$family][] = $sciName;
				$this->checklistTaxaCnt++;
			}
        }
        return $returnVec;
	}

    public function getTidChecklist($tidArr,$taxonFilter): array
	{
        $returnVec = array();
        $tidStr = implode(',',$tidArr);
        $this->checklistTaxaCnt = 0;
        $sql = 'SELECT DISTINCT ts.family, t.sciname '.
            'FROM (taxstatus AS ts1 INNER JOIN taxa AS t ON ts1.TidAccepted = t.Tid) '.
            'INNER JOIN taxstatus AS ts ON t.tid = ts.tid '.
            'WHERE ts1.tid IN('.$tidStr.') '.
            'AND ts1.taxauthid = '.$taxonFilter.' AND ts.taxauthid = '.$taxonFilter.' AND t.RankId > 140 ';
        //echo "<div>".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $family = strtoupper($row->family);
            if(!$family) {
				$family = 'undefined';
			}
            $sciName = $row->sciname;
            if($sciName && substr($sciName,-5) !== 'aceae'){
                $returnVec[$family][] = $sciName;
                $this->checklistTaxaCnt++;
            }
        }
        return $returnVec;
    }

	public function buildSymbiotaChecklist($taxonAuthorityId,$tidArr = ''){
		global $SYMB_UID;
		$expirationTime = date('Y-m-d H:i:s',time()+259200);
		$tidStr = '';
		if($tidArr){
            $tidStr = implode((array)',',$tidArr);
        }
		$dynClid = 0;
		$sqlCreateCl = 'INSERT INTO fmdynamicchecklists ( name, details, uid, type, notes, expiration ) '.
			"VALUES ('Specimen Checklist #".time()."', 'Generated ".date('d-m-Y H:i:s')."', '".$SYMB_UID."', 'Specimen Checklist', '', '".$expirationTime."') ";
		if($this->conn->query($sqlCreateCl)){
			$dynClid = $this->conn->insert_id;
			$sqlTaxaInsert = 'INSERT IGNORE INTO fmdyncltaxalink ( tid, dynclid ) ';
			if($tidStr){
                if(is_numeric($taxonAuthorityId)){
                    $sqlTaxaInsert .= 'SELECT DISTINCT t.tid, '.$dynClid.' '.
                        'FROM taxstatus AS ts INNER JOIN taxa AS t ON ts.TidAccepted = t.Tid '.
                        'WHERE ts.tid IN('.$tidStr.') AND ts.taxauthid = '.$taxonAuthorityId.' AND t.RankId > 180';
                }
                else{
                    $sqlTaxaInsert .= 'SELECT DISTINCT t.tid, '.$dynClid.' FROM taxa AS t '.
                        'WHERE t.tid IN('.$tidStr.') AND t.RankId > 180 ';
                }
            }
            else{
            	$sqlWhere = $this->getSqlWhere();
            	if(is_numeric($taxonAuthorityId)){
                    $sqlTaxaInsert .= 'SELECT DISTINCT t.tid, ' .$dynClid. ' ' .
						'FROM ((omoccurrences o INNER JOIN taxstatus ts ON o.TidInterpreted = ts.Tid) INNER JOIN taxa t ON ts.TidAccepted = t.Tid) ';
                    $sqlTaxaInsert .= $this->setTableJoins($sqlWhere);
                    $sqlTaxaInsert .= str_ireplace('o.sciname', 't.sciname',str_ireplace('o.family', 'ts.family',$sqlWhere)). 'AND ts.taxauthid = ' .$taxonAuthorityId. ' AND t.RankId > 180';
                }
                else{
                    $sqlTaxaInsert .= 'SELECT DISTINCT t.tid, ' .$dynClid. ' FROM (omoccurrences o INNER JOIN taxa t ON o.TidInterpreted = t.tid) ';
                    $sqlTaxaInsert .= $this->setTableJoins($sqlWhere);
                    $sqlTaxaInsert .= $sqlWhere. ' AND t.RankId > 180';
                }
            }
			//echo "sqlTaxaInsert: ".$sqlTaxaInsert;
			$this->conn->query($sqlTaxaInsert);

			$this->conn->query('DELETE FROM fmdynamicchecklists WHERE expiration < now()');
		}
		else{
			echo 'ERROR: ' .$this->conn->error;
			echo 'insertSql: ' .$sqlCreateCl;
		}
		if($this->conn !== false) {
			$this->conn->close();
		}
		return $dynClid;
	}
}
