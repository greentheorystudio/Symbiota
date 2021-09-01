<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class ExsiccatiManager {

	private $conn;

	public function __construct() {
        $connection = new DbConnection();
	    $this->conn = $connection->getConnection();
	}

	public function __destruct(){
 		if($this->conn) {
            $this->conn->close();
        }
	}

	public function getTitleObj($ometid): array
    {
		$retArr = array();
		if($ometid){
			$sql = 'SELECT et.ometid, et.title, et.abbreviation, et.editor, et.exsrange, et.startdate, et.enddate, '.
				'et.source, et.notes, et.lasteditedby '.
				'FROM omexsiccatititles et '.
				'WHERE ometid = '.$ometid;
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr['title'] = Sanitizer::cleanOutStr($r->title);
					$retArr['abbreviation'] = Sanitizer::cleanOutStr($r->abbreviation);
					$retArr['editor'] = Sanitizer::cleanOutStr($r->editor);
					$retArr['exsrange'] = Sanitizer::cleanOutStr($r->exsrange);
					$retArr['startdate'] = Sanitizer::cleanOutStr($r->startdate);
					$retArr['enddate'] = Sanitizer::cleanOutStr($r->enddate);
					$retArr['source'] = Sanitizer::cleanOutStr($r->source);
					$retArr['notes'] = Sanitizer::cleanOutStr($r->notes);
					$retArr['lasteditedby'] = $r->lasteditedby;
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function getTitleArr($searchTerm = null, $specimenOnly = null, $imagesOnly = null, $collId = null, $sortBy = null): array
    {
		$retArr = array();
		$sql = 'SELECT DISTINCT et.ometid, et.title, et.editor, et.exsrange, et.abbreviation ';
		$sqlWhere = '';
		if($specimenOnly){
			if($imagesOnly){
				$sql .= 'FROM omexsiccatititles et INNER JOIN omexsiccatinumbers en ON et.ometid = en.ometid '.
						'INNER JOIN omexsiccatiocclink ol ON en.omenid = ol.omenid '.
						'INNER JOIN images i ON ol.occid = i.occid ';
			}
			else{
				$sql .= 'FROM omexsiccatititles et INNER JOIN omexsiccatinumbers en ON et.ometid = en.ometid '.
						'INNER JOIN omexsiccatiocclink ol ON en.omenid = ol.omenid ';
			}
			if($collId){
				$sql .= 'INNER JOIN omoccurrences o ON ol.occid = o.occid ';
				$sqlWhere = 'WHERE o.collid = '.$collId.' ';
			}
		}
		else{
			$sql .= 'FROM omexsiccatititles et ';
		}
		if($searchTerm){
			$sqlWhere .= ($sqlWhere?'AND ':'WHERE ').'et.title LIKE "%'.Sanitizer::cleanInStr($searchTerm).'%" OR et.abbreviation LIKE "%'.Sanitizer::cleanInStr($searchTerm).'%" OR et.editor LIKE "%'.Sanitizer::cleanInStr($searchTerm).'%" ';
		}
		$sql .= $sqlWhere.'ORDER BY '.($sortBy? 'IFNULL(et.abbreviation,et.title)' : 'et.title').', et.startdate';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				if((int)$sortBy === 1) {
					if($r->abbreviation) {
                        $titleStr = (strlen($r->abbreviation) > 100 ? substr($r->abbreviation, 0, 100) . '...' : $r->abbreviation);
                    }
					else {
						$titleStr = (strlen($r->title)>100?substr($r->title,0,100).'...':$r->title);
						$titleStr .= ', '.(strlen($r->editor)>50?substr($r->editor,0,50).'...':$r->editor);
					}
				} else {
					$titleStr = (strlen($r->title)>100?substr($r->title,0,100).'...':$r->title);
					if($r->editor) {
                        $titleStr .= ', ' . (strlen($r->editor) > 50 ? substr($r->editor, 0, 50) . '...' : $r->editor);
                    }
				}
				if($r->exsrange) {
                    $titleStr .= ' [' . $r->exsrange . ']';
                }
				$retArr[$r->ometid] = Sanitizer::cleanOutStr($titleStr);
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getExsNumberArr($ometid,$specimenOnly,$imagesOnly,$collId): array
    {
		$retArr = array();
		if($ometid){
			$sql = 'SELECT DISTINCT en.omenid, en.exsnumber, en.notes, o.sciname, '.
				'CONCAT(o.recordedby," (",IFNULL(o.recordnumber,"s.n."),") ",IFNULL(o.eventDate,"date unknown")) as collector '.
				'FROM omexsiccatinumbers en '.($specimenOnly || $imagesOnly?'INNER':'LEFT').' JOIN omexsiccatiocclink ol ON en.omenid = ol.omenid '.
				($specimenOnly || $imagesOnly?'INNER':'LEFT').' JOIN omoccurrences o ON ol.occid = o.occid ';
			if($imagesOnly) {
                $sql .= 'INNER JOIN images i ON o.occid = i.occid ';
            }
			$sql .= 'WHERE en.ometid = '.$ometid.' ';
			if($collId) {
                $sql .= 'AND o.collid = ' . $collId . ' ';
            }
			$sql .= 'ORDER BY en.exsnumber+1,en.exsnumber,ol.ranking';
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					if(!array_key_exists($r->omenid,$retArr)){
						$retArr[$r->omenid]['number'] = Sanitizer::cleanOutStr($r->exsnumber);
						$retArr[$r->omenid]['collector'] = $r->collector;
						$retArr[$r->omenid]['sciname'] = $r->sciname;
						$retArr[$r->omenid]['notes'] = Sanitizer::cleanOutStr($r->notes);
					}
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function getExsNumberObj($omenid): array
    {
		$retArr = array();
		if($omenid){
			$sql = 'SELECT et.ometid, et.title, et.abbreviation, et.editor, et.exsrange, en.exsnumber, en.notes '.
				'FROM omexsiccatititles et INNER JOIN omexsiccatinumbers en ON et.ometid = en.ometid '.
				'WHERE en.omenid = '.$omenid;
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				if($r = $rs->fetch_object()){
					$retArr['ometid'] = $r->ometid;
					$retArr['title'] = Sanitizer::cleanOutStr($r->title);
					$retArr['abbreviation'] = Sanitizer::cleanOutStr($r->abbreviation);
					$retArr['editor'] = Sanitizer::cleanOutStr($r->editor);
					$retArr['exsrange'] = Sanitizer::cleanOutStr($r->exsrange);
					$retArr['exsnumber'] = Sanitizer::cleanOutStr($r->exsnumber);
					$retArr['notes'] = Sanitizer::cleanOutStr($r->notes);
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	public function getExsOccArr($id, $target = null): array
    {
		$retArr = array();
		$sql = 'SELECT en.omenid, en.exsnumber, ol.ranking, ol.notes, o.occid, o.occurrenceid, o.catalognumber, '.
			'c.collid, c.collectionname, CONCAT_WS("-",c.institutioncode,c.collectioncode) AS collcode, '.
			'o.sciname, o.scientificnameauthorship, o.recordedby, o.recordnumber, DATE_FORMAT(o.eventdate,"%d %M %Y") AS eventdate, '.
			'trim(o.country) AS country, trim(o.stateprovince) AS stateprovince, trim(o.county) AS county, '.
			'trim(o.municipality) AS municipality, o.locality, o.decimallatitude, o.decimallongitude, '.
			'i.imgid, i.thumbnailurl, i.url '.
			'FROM omexsiccatiocclink ol INNER JOIN omoccurrences o ON ol.occid = o.occid '.
			'INNER JOIN omcollections c ON o.collid = c.collid '.
			'INNER JOIN omexsiccatinumbers en ON ol.omenid = en.omenid '.
			'LEFT JOIN images i ON o.occid = i.occid ';
		if($target){
			$sql .= 'WHERE en.ometid = '.$id;
		}
		else{
			$sql .= 'WHERE ol.omenid = '.$id;
		}
		$sql .= ' ORDER BY en.exsnumber+1, ol.ranking, o.recordedby, o.recordnumber';
		//echo $sql;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				if(!isset($retArr[$r->omenid][$r->occid])){
					$retArr[$r->omenid][$r->occid]['exsnum'] = Sanitizer::cleanOutStr($r->exsnumber);
					$retArr[$r->omenid][$r->occid]['ranking'] = Sanitizer::cleanOutStr($r->ranking);
					$retArr[$r->omenid][$r->occid]['notes'] = Sanitizer::cleanOutStr($r->notes);
					$retArr[$r->omenid][$r->occid]['collid'] = $r->collid;
					$retArr[$r->omenid][$r->occid]['collname'] = Sanitizer::cleanOutStr($r->collectionname);
					$retArr[$r->omenid][$r->occid]['collcode'] = Sanitizer::cleanOutStr($r->collcode);
					$retArr[$r->omenid][$r->occid]['occurrenceid'] = $r->occurrenceid;
					$retArr[$r->omenid][$r->occid]['catalognumber'] = $r->catalognumber;
					$retArr[$r->omenid][$r->occid]['sciname'] = Sanitizer::cleanOutStr($r->sciname);
					$retArr[$r->omenid][$r->occid]['author'] = Sanitizer::cleanOutStr($r->scientificnameauthorship);
					$retArr[$r->omenid][$r->occid]['recby'] = Sanitizer::cleanOutStr($r->recordedby);
					$retArr[$r->omenid][$r->occid]['recnum'] = Sanitizer::cleanOutStr($r->recordnumber);
					$retArr[$r->omenid][$r->occid]['eventdate'] = $r->eventdate;
					$retArr[$r->omenid][$r->occid]['country'] = $r->country;
					$retArr[$r->omenid][$r->occid]['state'] = $r->stateprovince;
					$retArr[$r->omenid][$r->occid]['county'] = $r->county;
					$retArr[$r->omenid][$r->occid]['locality'] = Sanitizer::cleanOutStr(($r->municipality?$r->municipality.'; ':'').$r->locality);
					$retArr[$r->omenid][$r->occid]['lat'] = $r->decimallatitude;
					$retArr[$r->omenid][$r->occid]['lng'] = $r->decimallongitude;
				}
				if($r->url){
					$retArr[$r->omenid][$r->occid]['img'][$r->imgid]['url'] = $r->url;
					$retArr[$r->omenid][$r->occid]['img'][$r->imgid]['tnurl'] = ($r->thumbnailurl?:$r->url);
				}
			}
			$rs->free();
		}
		return $retArr;
	}

	public function exportExsiccatiAsCsv($searchTerm, $specimenOnly, $imagesOnly, $collId): void
    {
		$fieldArr = array('et.ometid' => 'titleID', 'et.title' => 'exsiccatiTitle', 'et.abbreviation' => 'abbreviation', 'et.editor' => 'editors', 'et.exsrange' => 'range',
			'et.startdate' => 'startDate', 'et.enddate' => 'endDate', 'et.source' => 'source', 'et.notes' => 'titleNotes', 'en.exsnumber' => 'exsiccatiNumber');
		$fileName = 'exsiccatiOutput_'.time().'.csv';
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		$sqlInsert = '';
		if($collId || $specimenOnly){
			$sqlInsert .= 'INNER JOIN omexsiccatiocclink ol ON en.omenid = ol.omenid '.
				'INNER JOIN omoccurrences o ON ol.occid = o.occid ';
			if($imagesOnly) {
                $sqlInsert .= 'INNER JOIN images i ON o.occid = i.occid ';
            }
			if($collId) {
                $sqlInsert .= 'WHERE o.collid = ' . $collId . ' ';
            }
			$fieldArr['o.occid'] = 'occid';
			$fieldArr['o.catalognumber'] = 'catalogNumber';
			$fieldArr['o.othercatalognumbers'] = 'otherCatalogNumbers';
			$fieldArr['o.dbpk'] = 'sourceIdentifier_dbpk';
			$fieldArr['o.recordedby'] = 'collector';
			$fieldArr['o.recordnumber'] = 'collectorNumber';
			$fieldArr['ol.notes'] = 'occurrenceNotes';
		}
		if($searchTerm){
			$sqlInsert .= ($sqlInsert?'AND ':'WHERE ').'et.title LIKE "%'.Sanitizer::cleanInStr($searchTerm).'%" OR et.abbreviation LIKE "%'.Sanitizer::cleanInStr($searchTerm).'%" OR et.editor LIKE "%'.Sanitizer::cleanInStr($searchTerm).'%" ';
		}
		$sql = 'SELECT '.implode(',',array_keys($fieldArr)).' '.
			'FROM omexsiccatititles et INNER JOIN omexsiccatinumbers en ON et.ometid = en.ometid '.
			$sqlInsert.'ORDER BY et.title, et.startdate';
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$out = fopen('php://output', 'wb');
			fputcsv($out, $fieldArr);
			while($r = $rs->fetch_assoc()){
				fputcsv($out, $r);
			}
			fclose($out);
		}
		else{
			echo "Recordset is empty.\n";
		}
		$rs->free();
	}

	public function addTitle($pArr,$editedBy): void
    {
		$sql = 'INSERT INTO omexsiccatititles(title, abbreviation, editor, exsrange, startdate, enddate, source, notes,lasteditedby) '.
			'VALUES("'.Sanitizer::cleanInStr($pArr['title']).'","'.Sanitizer::cleanInStr($pArr['abbreviation']).'","'.
			Sanitizer::cleanInStr($pArr['editor']).'",'.
			($pArr['exsrange']?'"'.Sanitizer::cleanInStr($pArr['exsrange']).'"':'NULL').','.
			($pArr['startdate']?'"'.Sanitizer::cleanInStr($pArr['startdate']).'"':'NULL').','.
			($pArr['enddate']?'"'.Sanitizer::cleanInStr($pArr['enddate']).'"':'NULL').','.
			($pArr['source']?'"'.Sanitizer::cleanInStr($pArr['source']).'"':'NULL').','.
			($pArr['notes']?'"'.Sanitizer::cleanInStr($pArr['notes']).'"':'NULL').',"'.
			$editedBy.'")';
		//echo $sql;
        $this->conn->query($sql);
        $this->conn->close();
	}

	public function editTitle($pArr,$editedBy): void
    {
		$sql = 'UPDATE omexsiccatititles '.
			'SET title = "'.Sanitizer::cleanInStr($pArr['title']).'", abbreviation = "'.Sanitizer::cleanInStr($pArr['abbreviation']).
			'", editor = "'.Sanitizer::cleanInStr($pArr['editor']).'"'.
			', exsrange = '.($pArr['exsrange']?'"'.Sanitizer::cleanInStr($pArr['exsrange']).'"':'NULL').
			', startdate = '.($pArr['startdate']?'"'.Sanitizer::cleanInStr($pArr['startdate']).'"':'NULL').
			', enddate = '.($pArr['enddate']?'"'.Sanitizer::cleanInStr($pArr['enddate']).'"':'NULL').
			', source = '.($pArr['source']?'"'.Sanitizer::cleanInStr($pArr['source']).'"':'NULL').
			', notes = '.($pArr['notes']?'"'.Sanitizer::cleanInStr($pArr['notes']).'"':'NULL').' '.
			', lasteditedby = "'.$editedBy.'" '.
			'WHERE (ometid = '.$pArr['ometid'].')';
		//echo $sql;
        $this->conn->query($sql);
        $this->conn->close();
	}

	public function deleteTitle($ometid): string
    {
		$retStr = '';
		if($ometid && is_numeric($ometid)){
			$sql = 'DELETE FROM omexsiccatititles WHERE (ometid = '.$ometid.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$retStr = 'DELETE Failed: possibly due to existing exsiccati numbers, which first have to be deleted.';
			}
            $this->conn->close();
		}
		return $retStr;
	}

	public function mergeTitles($ometid,$targetOmetid): string
    {
		$retStr = '';
		if($ometid && is_numeric($ometid) && $targetOmetid && is_numeric($targetOmetid)){
			$sql = 'UPDATE IGNORE omexsiccatinumbers '.
				'SET ometid = '.$targetOmetid.' '.
				'WHERE ometid = '.$ometid;
			//echo $sql;
            $this->conn->query($sql);

			$sql = 'UPDATE IGNORE omexsiccatiocclink o INNER JOIN omexsiccatinumbers n1 ON o.omenid = n1.omenid '.
				'INNER JOIN omexsiccatinumbers n2 ON n1.exsnumber = n2.exsnumber '.
				'SET o.omenid = n2.omenid '.
				'WHERE n1.ometid = '.$ometid.' AND n2.ometid = '.$targetOmetid;
			//echo $sql;
            $this->conn->query($sql);

			$sql = 'DELETE n.* FROM omexsiccatinumbers n LEFT JOIN omexsiccatiocclink o ON n.omenid = o.omenid '.
				'WHERE o.omenid IS NULL AND n.ometid = '.$ometid;
			//echo $sql;
			if(!$this->conn->query($sql)){
				$retStr = 'ERROR deleting omexsiccatinumbers.';
			}

			$sql = 'DELETE FROM omexsiccatititles '.
				'WHERE ometid = '.$ometid;
			//echo $sql;
			if(!$this->conn->query($sql)){
				$retStr = 'ERROR deleting omexsiccatititles.';
			}

            $this->conn->close();
		}
		return $retStr;
	}

	public function addNumber($pArr): string
    {
		$retStr = '';
		$sql = 'INSERT INTO omexsiccatinumbers(ometid,exsnumber,notes) '.
			'VALUES('.$pArr['ometid'].',"'.Sanitizer::cleanInStr($pArr['exsnumber']).'",'.
			($pArr['notes']?'"'.Sanitizer::cleanInStr($pArr['notes']).'"':'NULL').')';
		//echo $sql;
		if(!$this->conn->query($sql)){
			$retStr = 'ERROR adding exsiccati number.';
		}
        $this->conn->close();
		return $retStr;
	}

	public function editNumber($pArr): string
    {
		$retStr = '';
		if($pArr['omenid'] && is_numeric($pArr['omenid'])){
			$sql = 'UPDATE omexsiccatinumbers '.
				'SET exsnumber = "'.Sanitizer::cleanInStr($pArr['exsnumber']).'",'.
				'notes = '.($pArr['notes']?'"'.Sanitizer::cleanInStr($pArr['notes']).'"':'NULL').' '.
				'WHERE (omenid = '.$pArr['omenid'].')';
			if(!$this->conn->query($sql)){
				$retStr = 'ERROR editing exsiccati number.';
			}
            $this->conn->close();
		}
		return $retStr;
	}

	public function deleteNumber($omenid): string
    {
		$retStr = '';
		if($omenid && is_numeric($omenid)){
			$sql = 'DELETE FROM omexsiccatinumbers WHERE (omenid = '.$omenid.')';
			if(!$this->conn->query($sql)){
				$retStr = 'ERROR deleting exsiccati number: possibly due to linked occurrences reocrds. Delete all occurrence records and then you should be able to delete this number.';
			}
            $this->conn->close();
		}
		return $retStr;
	}

	public function transferNumber($omenid,$targetOmetid): string
    {
		$retStr = '';
		if($omenid && is_numeric($omenid) && $targetOmetid && is_numeric($targetOmetid)){
			$sql = 'SELECT n1.omenid '.
				'FROM omexsiccatinumbers n1 INNER JOIN omexsiccatinumbers n2 ON n1.exsnumber = n2.exsnumber '.
				'WHERE n1.ometid = '.$targetOmetid.' AND n2.omenid = '.$omenid;
			//echo $sql;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$sql1 = 'UPDATE IGNORE omexsiccatiocclink '.
					'SET omenid = '.$r->omenid.' '.
					'WHERE omenid = '.$omenid;
				//echo $sql;
                $this->conn->query($sql1);

				$sql2 = 'DELETE n.* FROM omexsiccatinumbers n LEFT JOIN omexsiccatiocclink o ON n.omenid = o.omenid '.
					'WHERE o.omenid IS NULL AND n.omenid = '.$omenid;
				//echo $sql;
				if(!$this->conn->query($sql2)){
					$retStr = 'ERROR deleting omexsiccatinumber.';
				}
			}
			else{
				$sql1 = 'UPDATE omexsiccatinumbers '.
					'SET ometid = '.$targetOmetid.' '.
					'WHERE omenid = '.$omenid;
				//echo $sql;
				if(!$this->conn->query($sql1)){
					$retStr = 'ERROR transferring omexsiccatinumber.';
				}
			}

            $this->conn->close();
		}
		return $retStr;
	}

	public function addOccLink($pArr): string
    {
		$retStr = '';
		$collId = $pArr['occaddcollid'];
		if($collId && $pArr['omenid'] && is_numeric($pArr['omenid'])){
			$ranking = 10;
			if($pArr['ranking'] && is_numeric($pArr['ranking'])) {
                $ranking = $pArr['ranking'];
            }
			$identifier = $pArr['identifier'];
			if($collId === 'occid' && $identifier && is_numeric($identifier)){
				$sql = 'INSERT INTO omexsiccatiocclink(omenid,occid,ranking,notes) '.
					'VALUES ('.$pArr['omenid'].','.$identifier.','.$ranking.','.
					($pArr['notes']?'"'.Sanitizer::cleanInStr($pArr['notes']).'"':'NULL').')';
				if(!$this->conn->query($sql)){
					$retStr = 'ERROR linking occurrence to exsiccati number, SQL: '.$sql;
				}
			}
			elseif($collId && is_numeric($collId) && ($identifier || ($pArr['recordedby'] && $pArr['recordnumber']))){
				$sql1 = 'SELECT o.occid '.
					'FROM omoccurrences o LEFT JOIN omexsiccatiocclink l ON o.occid = l.occid '.
					'LEFT JOIN omoccurrencesfulltext f ON o.occid = f.occid '.
					'WHERE o.collid = '.$collId.' AND l.occid IS NULL ';
				if($identifier){
					$sql1 .= 'AND (o.catalogNumber = '.(is_numeric($identifier)?$identifier:'"'.$identifier.'"').') ';
				}
				else{
					if(strlen($pArr['recordedby']) < 4 || strtolower($pArr['recordedby']) === 'best'){
						$sql1 .= 'AND (o.recordedby LIKE "%'.$pArr['recordedby'].'%")';
					}
					else{
						$sql1 .= 'AND (MATCH(f.recordedby) AGAINST("'.$pArr['recordedby'].'")) ';
					}
					$sql1 .= 'AND (o.recordnumber = '.(is_numeric($pArr['recordnumber'])?$pArr['recordnumber']:'"'.$pArr['recordnumber'].'"').') ';
				}
				$sql1 .= 'LIMIT 5';
				//echo $sql1;
				$rs = $this->conn->query($sql1);
				$cnt = 0;
				while($r = $rs->fetch_object()){
					$sql = 'INSERT INTO omexsiccatiocclink(omenid,occid,ranking,notes) '.
						'VALUES('.$pArr['omenid'].', '.$r->occid.', '.$ranking.','.
						($pArr['notes']?'"'.Sanitizer::cleanInStr($pArr['notes']).'"':'NULL').')';
					//echo $sql;
					if(!$this->conn->query($sql)){
						$retStr = 'ERROR linking occurrence to exsiccati number, SQL: '.$sql;
					}
					$cnt++;
				}
				$rs->free();
				if($cnt){
					$retStr = 'SUCCESS: '.$cnt.' recorded loaded successfully';
				}
				else{
					$retStr = 'FAILED: no records located matching criteria';
				}
			}
            $this->conn->close();
		}
		else{
			$retStr = 'FAILED: criteria may have not been complete';
		}
		return $retStr;
	}

	public function editOccLink($pArr): void
    {
		if($pArr['omenid'] && $pArr['occid'] && is_numeric($pArr['omenid']) && is_numeric($pArr['occid']) && is_numeric($pArr['ranking'])){
			$sql = 'UPDATE omexsiccatiocclink '.
				'SET ranking = '.$pArr['ranking'].', notes = "'.Sanitizer::cleanInStr($pArr['notes']).'" '.
				'WHERE (omenid = '.$pArr['omenid'].') AND (occid = '.$pArr['occid'].')';
            $this->conn->query($sql);
            $this->conn->close();
		}
	}

	public function deleteOccLink($omenid, $occid): void
    {
		if($omenid && $occid && is_numeric($omenid) && is_numeric($occid)){
			$sql = 'DELETE FROM omexsiccatiocclink WHERE (omenid = '.$omenid.') AND (occid = '.$occid.')';
            $this->conn->query($sql);
            $this->conn->close();
		}
	}

	public function transferOccurrence($omenid, $occid, $targetOmetid, $targetExsNumber): string
    {
		$statusStr = '';
		if($omenid && is_numeric($omenid) && $targetOmetid && is_numeric($targetOmetid) && $targetExsNumber){
			$targetOmenid = 0;
			$sql = 'SELECT omenid FROM omexsiccatinumbers '.
				'WHERE ometid = '.$targetOmetid.' AND exsnumber = "'.Sanitizer::cleanInStr($targetExsNumber).'"';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$targetOmenid = $r->omenid;
			}
			else{
				$sql1 = 'INSERT INTO omexsiccatinumbers(ometid, exsnumber) '.
					'VALUES('.$targetOmetid.',"'.Sanitizer::cleanInStr($targetExsNumber).'") ';
				if($this->conn->query($sql1)){
					$targetOmenid = $this->conn->insert_id;
				}
				else{
					$statusStr = 'ERROR inserting new omexsiccatinumbers record, SQL: '.$sql1;
				}
			}
			$rs->free();
			if($targetOmenid){
				$sql2 = 'UPDATE omexsiccatiocclink SET omenid = '.$targetOmenid.' WHERE occid = '.$occid.' AND omenid = '.$omenid;
				if(!$this->conn->query($sql2)){
					$statusStr = 'ERROR tranferring occurrence.';
				}
			}
			else{
				$statusStr = 'ERROR looking up omenid while trying to transfer occurrence';
			}

            $this->conn->close();
		}
		return $statusStr;
	}

	public function batchImport($targetCollid,$postArr): string
    {
		$statusStr = '';
		$transferCnt = 0;
		if(array_key_exists('occid[]',$postArr)){
			$datasetId = '';
			if(array_key_exists('dataset',$postArr) && $postArr['dataset']){
				$sqlDs = 'INSERT INTO omoccurdatasets(name, uid) VALUES("'.Sanitizer::cleanInStr($postArr['dataset']).'",'.$GLOBALS['SYMB_UID'].') ';
				if($this->conn->query($sqlDs)){
					$datasetId = $this->conn->insert_id;
				}
				else{
					$statusStr = 'ERROR creating dataset.';
				}
			}
			$occidArr = $postArr['occid[]'];
			foreach($occidArr as $occid){
				if(is_numeric($occid)){
					$catNum = Sanitizer::cleanInStr($postArr['cat-'.$occid]);
					$sql1 = $targetCollid.', "'.$catNum.'", "'.date('Y-m-d H:i:s').'" AS dateEntered '.
						'FROM omoccurrences WHERE occid = '.$occid;
					if($this->conn->query($sql1)){
						$transferCnt++;
						$newOccid = $this->conn->insert_id;
						if($newOccid){
							$sql2 = 'INSERT INTO omexsiccatiocclink(omenid,occid) '.
								'SELECT omenid, occid FROM omexsiccatiocclink WHERE occid = '.$newOccid;
							if(!$this->conn->query($sql2)){
								$statusStr = 'ERROR linking new record to exsiccati.';
							}
						}
						if($datasetId){
							$sql3 = 'INSERT INTO omoccurdatasetlink(occid,datasetid) VALUES('.$newOccid.','.$datasetId.') ';
							if(!$this->conn->query($sql3)){
								$statusStr = 'ERROR add new record to dataset.';
							}
						}
					}
					else{
						$statusStr .= 'ERROR transferring record #'.$occid.'.';
					}
				}
			}
            $this->conn->close();
		}
		if($transferCnt){
			$statusStr = 'SUCCESS transferring '.$transferCnt.' records ';
		}
		return $statusStr;
	}

	public function exportAsCsv($postArr): void
    {
		if(array_key_exists('occid[]',$postArr)){
			$fieldArr = $this->getTargetFields();
			$occidArr = array_flip($postArr['occid[]']);
			$fileName = 'exsiccatiOutput_'.time().'.csv';
			header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header ('Content-Type: text/csv');
			header ('Content-Disposition: attachment; filename="'.$fileName.'"');
			$sql = 'SELECT '.implode(',',$fieldArr).', occid FROM omoccurrences WHERE occid IN('.implode(',',$occidArr).') ';
			$rs = $this->conn->query($sql);
			if($rs->num_rows){
				$out = fopen('php://output', 'wb');
				array_unshift($fieldArr,'catalogNumber');
				$fieldArr[] = 'occid';
				echo implode(',',$fieldArr)."\n";
				while($r = $rs->fetch_assoc()){
					array_unshift($r,$occidArr[$r->occid]);
					fputcsv($out, $r);
				}
				fclose($out);
			}
			else{
				echo "Recordset is empty.\n";
			}
			$rs->free();
		}
	}

	private function getTargetFields(): array
    {
		$fieldArr = array();
		$skipFields = array('occid','collid','dbpk','ownerinstitutioncode','institutionid','collectionid','datasetid','institutioncode','collectioncode',
			'occurrenceid', 'catalognumber', 'othercatalognumbers','previousidentifications', 'taxonremarks', 'identifiedby', 'dateidentified',
			'identificationreferences', 'identificationremarks', 'recordedbyid', 'informationwithheld', 'associatedoccurrences', 'datageneralizations',
			'dynamicproperties', 'verbatimcoordinatesystem', 'storagelocation', 'disposition', 'genericcolumn1', 'genericcolumn2', 'modified',
			'observeruid', 'processingstatus', 'recordenteredby', 'duplicatequantity', 'labelproject', 'dateentered', 'datelastmodified',
			'initialtimestamp');
		$sql = 'SHOW COLUMNS FROM uploadspectemp';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$field = strtolower($row->Field);
			if(!in_array($field, $skipFields, true)){
				$fieldArr[] = $field;
			}
		}
		$rs->free();
		return $fieldArr;
	}

	public function getCollArr($ometid = null): array
    {
		$retArr = array();
		$sql ='SELECT DISTINCT c.collid, c.collectionname, c.institutioncode, c.collectioncode '.
			'FROM omcollections c ';
		if($ometid){
			if($ometid === 'all'){
				$sql .= 'INNER JOIN omoccurrences o ON c.collid = o.collid '.
					'INNER JOIN omexsiccatiocclink ol ON o.occid = ol.occid ';
			}
			elseif(is_numeric($ometid)){
				$sql .= 'INNER JOIN omoccurrences o ON c.collid = o.collid '.
					'INNER JOIN omexsiccatiocclink ol ON o.occid = ol.occid '.
					'INNER JOIN omexsiccatinumbers en ON ol.omenid = en.omenid ';
			}
		}
		$sql .= 'WHERE (colltype != "General Observations") ';
		if($ometid && is_numeric($ometid)){
			$sql .= 'AND (en.ometid = '.$ometid.') ';
		}
		$sql .= 'ORDER BY c.collectionname, c.institutioncode';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid] = $r->collectionname.' ('.$r->institutioncode.($r->collectioncode?' - '.$r->collectioncode:'').')';
		}
		$rs->free();
		return $retArr;
	}

	public function getTargetCollArr(): array
    {
		$retArr = array();
        $collArr = $GLOBALS['USER_RIGHTS']['CollAdmin'] ?? array();
        if(isset($GLOBALS['USER_RIGHTS']['CollEditor'])){
			$collArr = array_merge($collArr,$GLOBALS['USER_RIGHTS']['CollEditor']);
		}
		if($collArr){
			$sql ='SELECT DISTINCT c.collid, c.collectionname, c.institutioncode, c.collectioncode '.
				'FROM omcollections c '.
				'WHERE (colltype != "Preserved Specimens") '.
				'ORDER BY c.collectionname, c.institutioncode';
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->collid] = $r->collectionname.' ('.$r->institutioncode.($r->collectioncode?' - '.$r->collectioncode:'').')';
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getExsTableRow($occid,$oArr,$omenid,$targetCollid): string
    {
		$isTarget = false;
		if((int)$targetCollid === (int)$oArr['collid']) {
            $isTarget = true;
        }
		$retStr = '<tr>';
		$retStr .= '<td style="text-align:center;">';
		$retStr .= '<input id="'.$occid.'" name="occid[]" type="checkbox" value="'.$occid.'" '.($isTarget?'disabled':'').' />';
		$retStr .= '</td>';
		$retStr .= '<td style="text-align:center;">';
		if($isTarget){
			$retStr .= '<span style="color:red;"><b>Cannot Import</b><br/>Is Target Collection</span>';
		}
		else{
			$retStr .= '<input name="cat-'.$occid.'" type="text" onchange="checkRecord(this,'.$occid.')" />';
		}
		$retStr .= '</td>';
		$retStr .= '<td style="text-align:center;"><a href="#" onclick="openExsPU('.$omenid.')">#'.$oArr['exsnum'].'</a></td>';
		$retStr .= '<td>';
		$retStr .= '<span '.($isTarget?'style="color:red;"':'').' title="'.$oArr['collname'].'">'.$oArr['collcode'].'</span>, ';
		$retStr .= '<a href="#" onclick="openIndPU('.$occid.')">'.$oArr['recby'].' '.($oArr['recnum']?:'s.n.').'</a>';
		$retStr .= ($oArr['eventdate']?', '.$oArr['eventdate']:'');
		$retStr .= ', <i>'.$oArr['sciname'].'</i> '.$oArr['author'];
		$retStr .= $oArr['country'].', '.$oArr['state'].', '.$oArr['county'].', '.(strlen($oArr['locality'])>75?substr($oArr['locality'],0,75).'...':$oArr['locality']);
		if($oArr['lat']) {
            $retStr .= ', ' . $oArr['lat'] . ' ' . $oArr['lng'];
        }
		if($oArr['notes']) {
            $retStr .= ', <b>' . $oArr['notes'] . '</b>';
        }
		$retStr .= '</td>';
		$retStr .= '</tr>';
		return $retStr;
	}
}
