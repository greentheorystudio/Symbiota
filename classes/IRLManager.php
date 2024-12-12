<?php
include_once(__DIR__ . '/../services/DbService.php');

class IRLManager {

	private $conn;
    private $tidArr = array();

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

 	public function __destruct(){
		if(!($this->conn === null)) {
			$this->conn->close();
		}
	}

    public function getChecklistTaxa($clid): array
    {
        $returnArr = array();
        $sql = 'SELECT c.TID, t.SciName, c.Habitat, c.Notes ' .
            'FROM fmchklsttaxalink AS c LEFT JOIN taxa AS t ON c.TID = t.TID  ' .
            'WHERE c.CLID = ' .$clid. ' '.
            'ORDER BY t.SciName ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $sciname = $row->SciName;
            if(strpos($sciname, ' ') === false){
                $sciname .= ' spp.';
            }
            $returnArr[$row->TID]['sciname'] = $sciname;
            $returnArr[$row->TID]['habitat'] = $row->Habitat;
            $returnArr[$row->TID]['notes'] = $row->Notes;
            $this->tidArr[] = $row->TID;
        }
        $result->free();
        return $returnArr;
    }

    public function getChecklistVernaculars(): array
    {
        $returnArr = array();
        $sql = 'SELECT TID, VernacularName ' .
            'FROM taxavernaculars  ' .
            'WHERE TID IN(' .implode(',', $this->tidArr). ') '.
            'ORDER BY TID, VernacularName ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[$row->TID][] = $row->VernacularName;
        }
        $result->free();
        return $returnArr;
    }

    public function getConfiguredData($collid): array
    {
        $returnArr = array();
        $sql = 'SELECT configjson FROM omcollections WHERE collid = ' . $collid . ' ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if(($row = $result->fetch_object()) && $row->configjson) {
            $returnArr = json_decode($row->configjson, true);
        }
        $result->free();
        return $returnArr;
    }

    public function getNativeStatus($tid): string
    {
        $returnArr = array();
        if($tid){
            $sql = 'SELECT TID, CLID ' .
                'FROM fmchklsttaxalink  ' .
                'WHERE TID = ' .$tid. ' AND CLID IN(13,14) ';
            //echo $sql;
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                if((int)$row->CLID === 13) {
                    $returnArr[] = 'NON-NATIVE';
                }
                if((int)$row->CLID === 14) {
                    $returnArr[] = 'CRYPTOGENIC';
                }
            }
            $result->free();
        }
        return implode(',', $returnArr);
    }

    public function getProjectAmbiInfaunaData($collid): array
    {
        $returnArr = array();
        $alphaArr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l');
        $headerCodeArr = array('');
        $headerRepArr = array('');
        $keyArr = array();
        $taxaDataArr = array();
        $taxaNameArr = array();
        $sql = 'SELECT DISTINCT l.locationCode, o.`year`, o.`month`, o.rep '.
            'FROM omoccurrences AS o LEFT JOIN omoccurlocations AS l ON o.locationID = l.locationID '.
            'WHERE o.collid = ' . (int)$collid . ' ORDER BY l.locationCode, o.`year`, o.`month`, o.rep ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $headerCode = $row->locationCode . '.' . $row->year . '.' . ((int)$row->month < 10 ? ('0' . $row->month) : $row->month);
            $keyCode = $headerCode . '-' . $row->rep;
            $keyArr[] = $keyCode;
            $headerCodeArr[] = $headerCode;
            $headerRepArr[] = $alphaArr[((int)$row->rep - 1)];
        }
        $result->free();

        $returnArr[] = $headerCodeArr;
        $returnArr[] = $headerRepArr;

        $sql = 'SELECT l.locationCode, o.sciname, o.`year`, o.`month`, o.rep, o.individualCount '.
            'FROM omoccurrences AS o LEFT JOIN omoccurlocations AS l ON o.locationID = l.locationID '.
            'WHERE o.collid = ' . (int)$collid . ' ORDER BY o.sciname ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $keyCode = $row->locationCode . '.' . $row->year . '.' . ((int)$row->month < 10 ? ('0' . $row->month) : $row->month) . '-' . $row->rep;
            if(!in_array($row->sciname, $taxaNameArr)){
                $taxaNameArr[] = $row->sciname;
            }
            $taxaDataArr[$row->sciname][$keyCode] = $row->individualCount;
        }
        $result->free();

        foreach($taxaNameArr as $sciname){
            $rowArr = array();
            $rowArr[] = $sciname;
            foreach($keyArr as $key){
                $rowArr[] = ((array_key_exists($key, $taxaDataArr[$sciname]) && (int)$taxaDataArr[$sciname][$key] > 0) ? $taxaDataArr[$sciname][$key] : '0');
            }
            $returnArr[] = $rowArr;
        }

        return $returnArr;
    }

    public function getProjectEnvironmentalData($collid): array
    {
        $returnArr = array();
        $headerArr = array('sample_date', 'site', 'time', 'depth', 'decimalLong', 'decimalLat');
        $configuredDataArr = array();
        $configuredData = $this->getConfiguredData($collid);
        if($configuredData && array_key_exists('eventMofExtension', $configuredData) && array_key_exists('dataFields', $configuredData['eventMofExtension'])){
            foreach($configuredData['eventMofExtension']['dataFields'] as $field => $fieldArr){
                $headerArr[] = $field;
            }
            $returnArr[] = $headerArr;

            $sql = 'SELECT a.eventID, a.field, a.datavalue '.
                'FROM ommofextension AS a LEFT JOIN omoccurcollectingevents AS c ON a.eventID = c.eventID '.
                'WHERE c.collid = ' . (int)$collid . ' ';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $configuredDataArr[$row->eventID][$row->field] = $row->datavalue;
            }
            $result->free();

            $sql = 'SELECT c.eventID, c.eventDate, l.locationCode, c.eventTime, '.
                'c.minimumDepthInMeters, IFNULL(c.decimallongitude, l.decimalLongitude) AS "decimalLong", '.
                'IFNULL(c.decimalLatitude, l.decimalLatitude) AS "decimalLat" '.
                'FROM omoccurcollectingevents AS c LEFT JOIN omoccurlocations AS l ON c.locationID = l.locationID '.
                'WHERE c.collid = ' . (int)$collid . ' ORDER BY c.eventDate ';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $rowArr = array();
                $eventId = $row->eventID;
                $rowArr[] = $row->eventDate;
                $rowArr[] = $row->locationCode;
                $rowArr[] = $row->eventTime;
                $rowArr[] = $row->minimumDepthInMeters;
                $rowArr[] = $row->decimalLong;
                $rowArr[] = $row->decimalLat;
                foreach($configuredData['eventMofExtension']['dataFields'] as $field => $fieldArr){
                    $rowArr[] = (array_key_exists($eventId, $configuredDataArr) && array_key_exists($field, $configuredDataArr[$eventId])) ? $configuredDataArr[$eventId][$field] : '';
                }
                $returnArr[] = $rowArr;
            }
            $result->free();
        }
        return $returnArr;
    }

    public function getProjectRScriptData($collid): array
    {
        $returnArr = array();
        $oArr = array("M01", "M15");
        $mArr = array("M02", "M03", "M04", "M14");
        $pArr = array("M05", "M06", "M08");
        $eArr = array("M07", "M09", "M10", "M11", "M12", "M13");
        $sql = 'SELECT DISTINCT l.locationcode, o.decimallatitude, o.decimallongitude, o.rep, o.eventdate, '.
            'o.sciname, o.individualcount, o.identificationRemarks, m.datavalue '.
            'FROM omoccurrences AS o LEFT JOIN omoccurlocations AS l ON o.locationID = l.locationID '.
            'LEFT JOIN ommofextension AS m ON o.eventID = m.eventID '.
            'WHERE o.collid = ' . (int)$collid . ' AND (m.field = "bottom_salinity" OR ISNULL(m.mofID)) ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $nodeArr = array();
            $nodeArr['StationID'] = $row->locationcode;
            $nodeArr['Latitude'] = $row->decimallatitude;
            $nodeArr['Longitude'] = $row->decimallongitude;
            $nodeArr['Replicate'] = $row->rep;
            $nodeArr['SampleDate'] = $row->eventdate;
            if(in_array($row->locationcode, $oArr)){
                $nodeArr['HabClassName'] = 'Oligohaline';
            }
            elseif(in_array($row->locationcode, $mArr)){
                $nodeArr['HabClassName'] = 'Mesohaline';
            }
            elseif(in_array($row->locationcode, $pArr)){
                $nodeArr['HabClassName'] = 'Polyhaline';
            }
            elseif(in_array($row->locationcode, $eArr)){
                $nodeArr['HabClassName'] = 'Euhaline';
            }
            else{
                $nodeArr['HabClassName'] = '';
            }
            $nodeArr['Salinity'] = $row->datavalue;
            $nodeArr['Species'] = $row->sciname;
            $nodeArr['Abundance'] = $row->individualcount;
            $nodeArr['IDRemarks'] = $row->identificationRemarks;
            $returnArr[] = $nodeArr;
        }
        $result->free();
        return $returnArr;
    }

    public function getTotalTaxa(): int
    {
        $total = 0;
        $sql = 'SELECT COUNT(DISTINCT t2.TID) AS cnt FROM taxa AS t1 LEFT JOIN taxa AS t2 ON t1.tidaccepted = t2.TID WHERE ((t2.RankId > 180) OR (t2.RankId = 180 AND t2.TID NOT IN(SELECT parenttid FROM taxaenumtree))) ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $total = (int)$row->cnt;
        }
        $result->free();
        return $total;
    }

    public function getTotalTaxaWithDesc(): int
    {
        $total = 0;
        $sql = 'SELECT COUNT(TID) AS cnt FROM taxa WHERE TID IN(SELECT tid FROM taxadescrblock) AND TID = tidaccepted ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $total = (int)$row->cnt;
        }
        $result->free();
        return $total;
    }

    public function getTotalOccurrenceRecords(): int
    {
        $total = 0;
        $sql = 'SELECT COUNT(occid) AS cnt FROM omoccurrences ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $total = (int)$row->cnt;
        }
        $result->free();
        return $total;
    }
}
