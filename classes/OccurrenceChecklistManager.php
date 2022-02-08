<?php
include_once(__DIR__ . '/OccurrenceManager.php');

class OccurrenceChecklistManager extends OccurrenceManager{
	
	private $checklistTaxaCnt = 0;

    public function getChecklistTaxaCnt(): int
	{
		return $this->checklistTaxaCnt;
	}

	public function getChecklist(): array
	{
		$returnVec = array();
		$this->checklistTaxaCnt = 0;
		$sqlWhere = $this->getSqlWhere();
        $sql = 'SELECT DISTINCT IFNULL(ts.family,o.family) AS family, o.sciname '.
            'FROM omoccurrences o LEFT JOIN taxa t ON o.tidinterpreted = t.tid '.
            'LEFT JOIN taxstatus ts ON t.tid = ts.tid ';
        $sql .= $this->setTableJoins($sqlWhere);
        $sql .= $sqlWhere;
        $sql .= ' AND (o.sciname IS NOT NULL) ';
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
            'FROM (taxstatus AS ts1 LEFT JOIN taxa AS t ON ts1.TidAccepted = t.Tid) '.
            'LEFT JOIN taxstatus AS ts ON t.tid = ts.tid '.
            'WHERE ts1.tid IN('.$tidStr.') '.
            'AND t.RankId > 140 ';
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

	public function buildSymbiotaChecklist($taxonAuthorityId,$tidArr){
		$expirationTime = date('Y-m-d H:i:s',time()+259200);
		$tidStr = '';
		if($tidArr){
            $tidStr = implode(',',$tidArr);
        }
		$dynClid = 0;
		$sqlCreateCl = 'INSERT INTO fmdynamicchecklists ( name, details, uid, type, notes, expiration ) '.
			"VALUES ('Occurrence Checklist #".time()."', 'Generated ".date('d-m-Y H:i:s')."', '".$GLOBALS['SYMB_UID']."', 'Occurrence Checklist', '', '".$expirationTime."') ";
		if($this->conn->query($sqlCreateCl)){
			$dynClid = $this->conn->insert_id;
			$sqlTaxaInsert = 'INSERT IGNORE INTO fmdyncltaxalink ( tid, dynclid ) ';
			if($tidStr){
                $sqlTaxaInsert .= 'SELECT DISTINCT t.tid, '.$dynClid.' FROM taxa AS t '.
                    'WHERE t.tid IN('.$tidStr.') AND t.RankId > 180 ';
            }
            else{
            	$sqlWhere = $this->getSqlWhere();
            	$sqlTaxaInsert .= 'SELECT DISTINCT t.tid, ' .$dynClid.' '.
                    'FROM omoccurrences o LEFT JOIN taxa t ON o.tidinterpreted = t.tid '.
                    'LEFT JOIN taxstatus ts ON t.tid = ts.tid ';
                $sqlTaxaInsert .= $this->setTableJoins($sqlWhere);
                $sqlTaxaInsert .= $sqlWhere;
                $sqlTaxaInsert .= ' AND (t.tid IS NOT NULL) ';
            }
			//echo "sqlTaxaInsert: ".$sqlTaxaInsert;
			$this->conn->query($sqlTaxaInsert);

			$this->conn->query('DELETE FROM fmdynamicchecklists WHERE expiration < now()');
		}
		else{
			echo 'ERROR: Building checklist.';
		}
		if($this->conn) {
			$this->conn->close();
		}
		return $dynClid;
	}
}
