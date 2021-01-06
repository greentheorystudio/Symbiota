<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/ImageLibraryManager.php');

class TaxonQuickSearchManager extends ImageLibraryManager{

	public function getSpeciesListWVernaculars($taxon): array
    {
		$returnArray = Array();
		$sql = 'SELECT DISTINCT t.tid, t.SciName, tv.vernacularname
			FROM taxstatus ts INNER JOIN taxa t ON ts.tid = t.TID
			LEFT JOIN taxavernaculars tv ON ts.tid = tv.TID
			WHERE (ts.taxauthid = 1) AND (t.RankId >= 140) ';
		if($taxon){
			$taxon = $this->cleanInStr($taxon);
			$sql .= "AND ((t.SciName REGEXP '^".$taxon."[[:>:]]') OR (tv.VernacularName REGEXP '[[:<:]]".$taxon."[[:>:]]')) ";
		}
		$sql .= "ORDER BY t.SciName, tv.vernacularname";
		//echo $sql;
		$commonNames = '';
		$result = $this->conn->query($sql);
		$tid = 0;
		$sciName = '';
		while($row = $result->fetch_object()) {
			if($tid === (int)$row->tid) {
				if($row->vernacularname) {
					if($commonNames) {
                        $commonNames .= "; " . $row->vernacularname;
                    }
					else {
                        $commonNames = $row->vernacularname;
                    }
				}
			}
			else {
				if($tid) {
					if($commonNames) {
                        $returnArray[$tid] = $sciName . " [" . $commonNames . "]";
                    }
					else {
                        $returnArray[$tid] = $sciName;
                    }
				}
				$tid = (int)$row->tid;
				$sciName = $row->SciName;
				$commonNames = $row->vernacularname;
			}
	    }
		if(!array_key_exists($tid, $returnArray)) {
			if($commonNames) {
                $returnArray[$tid] = $sciName . " [" . $commonNames . "]";
            }
			else {
                $returnArray[$tid] = $sciName;
            }
		}
	    $result->free();
	    return $returnArray;
	}

	private function cleanInStr($str): string
    {
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
