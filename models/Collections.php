<?php
include_once(__DIR__ . '/Occurrences.php');
include_once(__DIR__ . '/../services/DataUploadService.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/SOLRService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Collections {

    private $conn;

    private $fields = array(
        "collid" => array("dataType" => "number", "length" => 10),
        "ccpk" => array("dataType" => "number", "length" => 10),
        "institutioncode" => array("dataType" => "string", "length" => 45),
        "collectioncode" => array("dataType" => "string", "length" => 45),
        "collectionname" => array("dataType" => "string", "length" => 150),
        "collectionid" => array("dataType" => "string", "length" => 100),
        "datasetid" => array("dataType" => "string", "length" => 250),
        "datasetname" => array("dataType" => "string", "length" => 100),
        "iid" => array("dataType" => "number", "length" => 10),
        "fulldescription" => array("dataType" => "string", "length" => 2000),
        "homepage" => array("dataType" => "string", "length" => 250),
        "individualurl" => array("dataType" => "string", "length" => 500),
        "contact" => array("dataType" => "string", "length" => 250),
        "email" => array("dataType" => "string", "length" => 45),
        "latitudedecimal" => array("dataType" => "number", "length" => 8),
        "longitudedecimal" => array("dataType" => "number", "length" => 9),
        "icon" => array("dataType" => "string", "length" => 250),
        "colltype" => array("dataType" => "string", "length" => 45),
        "managementtype" => array("dataType" => "string", "length" => 45),
        "datarecordingmethod" => array("dataType" => "string", "length" => 45),
        "defaultrepcount" => array("dataType" => "number", "length" => 11),
        "collectionguid" => array("dataType" => "string", "length" => 45),
        "securitykey" => array("dataType" => "string", "length" => 45),
        "guidtarget" => array("dataType" => "string", "length" => 45),
        "rightsholder" => array("dataType" => "string", "length" => 250),
        "rights" => array("dataType" => "string", "length" => 250),
        "usageterm" => array("dataType" => "string", "length" => 250),
        "publishtogbif" => array("dataType" => "number", "length" => 11),
        "publishtoidigbio" => array("dataType" => "number", "length" => 11),
        "aggkeysstr" => array("dataType" => "string", "length" => 1000),
        "dwcaurl" => array("dataType" => "string", "length" => 250),
        "bibliographiccitation" => array("dataType" => "string", "length" => 1000),
        "accessrights" => array("dataType" => "string", "length" => 1000),
        "configjson" => array("dataType" => "json", "length" => 0),
        "ispublic" => array("dataType" => "number", "length" => 6),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

	public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function cleanSOLRIndex($collidStr): int
    {
        $SOLROccArr = array();
        $mysqlOccArr = array();
        $collidStr = SanitizerService::cleanInStr($this->conn, $collidStr);
        $solrWhere = 'q=(collid:(' . $collidStr . '))';
        $solrURL = $GLOBALS['SOLR_URL'].'/select?'.$solrWhere;
        $solrURL .= '&rows=1&start=1&wt=json';
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        $cnt = $solrArr['response']['numFound'];
        $occURL = $GLOBALS['SOLR_URL'].'/select?'.$solrWhere.'&rows='.$cnt.'&start=1&fl=occid&wt=json';
        //echo str_replace(' ','%20',$occURL);
        $solrOccArrJson = file_get_contents(str_replace(' ','%20',$occURL));
        $solrOccArr = json_decode($solrOccArrJson, true);
        $recArr = $solrOccArr['response']['docs'];
        foreach($recArr as $k){
            $SOLROccArr[] = $k['occid'];
        }
        $sql = 'SELECT occid FROM omoccurrences WHERE collid IN(' . $collidStr . ') ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $mysqlOccArr[] = $r->occid;
            }
        }
        $delOccArr = array_diff($SOLROccArr, $mysqlOccArr);
        if($delOccArr){
            (new SOLRService)->deleteSOLRDocument($delOccArr);
        }
        return 1;
    }

    public function createCollectionRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $collId = array_key_exists('collid', $data) ? (int)$data['collid'] : 0;
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'collid' && $field !== 'collectionguid' && $field !== 'securitykey' && array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    if($field === 'configjson'){
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, json_encode($data[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                    }
                }
            }
            $fieldNameArr[] = 'collectionguid';
            $fieldValueArr[] = '"' . UuidService::getUuidV4() . '"';
            $fieldNameArr[] = 'securitykey';
            $fieldValueArr[] = '"' . UuidService::getUuidV4() . '"';
            $sql = 'INSERT INTO omcollections(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                $sql = 'INSERT INTO omcollectionstats(collid, recordcnt, uploadedby) '.
                    'VALUES(' . $newID . ', 0, "' . $GLOBALS['USERNAME'] . '")';
                $this->conn->query($sql);
            }
        }
        return $newID;
    }

    public function deleteCollectionRecord($collid): int
    {
        $retVal = 1;
        if($collid){
            $retVal = (new Occurrences)->deleteOccurrenceRecord('collid', $collid);
            if($retVal){
                $retVal = (new DataUploadService)->clearOccurrenceUploadTables($collid, true);
                $sql = 'DELETE FROM userroles WHERE (role = "CollAdmin" OR role = "CollEditor" OR role = "RareSppReader") AND tablepk = ' . (int)$collid . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
                $sql = 'DELETE m.* FROM omcolldatauploadparameters AS u LEFT JOIN uploadspecmap AS m ON u.uspid = m.uspid '.
                    'WHERE u.collid = ' . (int)$collid . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
                $sql = 'DELETE FROM omcolldatauploadparameters WHERE collid = ' . (int)$collid . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
                $sql = 'DELETE FROM omcollmediauploadparameters WHERE collid = ' . (int)$collid . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
                $sql = 'DELETE FROM omcollectionstats WHERE collid = ' . (int)$collid . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
                $sql = 'DELETE FROM omcrowdsourcecentral WHERE collid = ' . (int)$collid . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
                return $retVal;
            }
        }
        return $retVal;
    }

    public function getCollectionArr(): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'c');
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ', s.uploaddate '.
            'FROM omcollections AS c LEFT JOIN omcollectionstats AS s ON c.collid = s.collid ';
        if(!$GLOBALS['IS_ADMIN']){
            $sql .= 'WHERE c.ispublic = 1 ';
            if($GLOBALS['PERMITTED_COLLECTIONS']){
                $sql .= 'OR c.collid IN('.implode(',', $GLOBALS['PERMITTED_COLLECTIONS']).') ';
            }
        }
        $sql .= 'ORDER BY c.collectionname ';
        //echo $sql;
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $uDate = null;
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                if($row['uploaddate']){
                    $uDate = $row['uploaddate'];
                    $month = substr($uDate,5,2);
                    $day = substr($uDate,8,2);
                    $year = substr($uDate,0,4);
                    $uDate = date('j F Y', mktime(0,0,0, $month, $day, $year));
                }
                $nodeArr['uploaddate'] = $uDate;
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getCollectionInfoArr($collId): array
    {
        $retArr = array();
        $retArr['configuredData'] = null;
        $uDate = null;
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'c');
        $fieldNameArr = array_merge($fieldNameArr, array('s.uploaddate', 's.recordcnt', 's.georefcnt', 's.familycnt', 's.genuscnt', 's.speciescnt',
            's.dynamicproperties', 'i.institutionname', 'i.address1', 'i.address2', 'i.city', 'i.stateprovince', 'i.postalcode', 'i.country'));
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omcollections AS c LEFT JOIN omcollectionstats AS s ON c.collid = s.collid '.
            'LEFT JOIN institutions AS i ON c.iid = i.iid '.
            'WHERE c.collid = ' . (int)$collId . ' ';
        //echo $sql;
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    if($name === 'configjson' && $row[$name]){
                        $retArr['configuredData'] = json_decode($row[$name], true);
                    }
                    $retArr[$name] = $row[$name];
                }
                if($row['uploaddate']){
                    $uDate = $row['uploaddate'];
                    $month = substr($uDate,5,2);
                    $day = substr($uDate,8,2);
                    $year = substr($uDate,0,4);
                    $uDate = date('j F Y', mktime(0,0,0, $month, $day, $year));
                }
                $retArr['uploaddate'] = $uDate;
            }
        }
        return $retArr;
    }

    public function getCollectionListByUid($uid): array
    {
        $retArr = array();
        if((int)$uid > 0){
            $sql = 'SELECT DISTINCT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.colltype '.
                'FROM userroles AS r LEFT JOIN omcollections AS c ON r.tablepk = c.collid '.
                'WHERE r.uid = ' . (int)$uid . ' AND (r.role = "CollAdmin" OR r.role = "CollEditor") '.
                'ORDER BY c.collectionname ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $collCode = '';
                    if($row['institutioncode']){
                        $collCode .= $row['institutioncode'];
                    }
                    if($row['collectioncode']){
                        $collCode .= ($collCode ? '-' : '') . $row['collectioncode'];
                    }
                    $collid = (int)$row['collid'];
                    $nodeArr = array();
                    $nodeArr['collectionpermissions'] = array();
                    if(array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollAdmin'], true)){
                        $nodeArr['collectionpermissions'][] = 'CollAdmin';
                    }
                    if(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($collid, $GLOBALS['USER_RIGHTS']['CollEditor'], true)){
                        $nodeArr['collectionpermissions'][] = 'CollEditor';
                    }
                    $nodeArr['collid'] = $collid;
                    $nodeArr['label'] = $row['collectionname'] . ($collCode ? (' (' . $collCode . ')') : '');
                    $nodeArr['colltype'] = $row['colltype'];
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getGeographicDistributionData($collId, $country = null, $state = null): array
    {
        $retArr = array();
        if($state){
            $sql = 'SELECT county AS termstr, COUNT(occid) AS cnt FROM omoccurrences '.
                'WHERE collid = ' . (int)$collId . ' AND county IS NOT NULL '.
                'AND country = "' . SanitizerService::cleanInStr($this->conn, $country) . '" '.
                'AND stateprovince = "' . SanitizerService::cleanInStr($this->conn, $state) . '" '.
                'GROUP BY stateprovince, county ORDER BY termstr ';
        }
        elseif($country){
            $sql = 'SELECT stateprovince AS termstr, COUNT(occid) AS cnt FROM omoccurrences '.
                'WHERE collid = ' . (int)$collId . ' AND stateprovince IS NOT NULL '.
                'AND country = "' . SanitizerService::cleanInStr($this->conn, $country) . '" '.
                'GROUP BY stateprovince, country ORDER BY termstr ';
        }
        else{
            $sql = 'SELECT country AS termstr, COUNT(occid) AS cnt FROM omoccurrences '.
                'WHERE collid = ' . (int)$collId . ' AND country IS NOT NULL '.
                'GROUP BY country ORDER BY termstr ';
        }
        //echo $sql; exit;
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if($row['termstr']) {
                    $t = $row['termstr'];
                    $cnt = $row['cnt'];
                    if($state){
                        $t = trim(str_ireplace(array(' county', ' co.', ' counties'),'', $t));
                    }
                    if(array_key_exists($t, $retArr)) {
                        $cnt += $retArr[$t];
                    }
                    $retArr[$t] = $cnt;
                }
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getPublicCollections(): array
    {
        $retArr = array();
        $sql = 'SELECT collid FROM omcollections WHERE isPublic = 1 ';
        //echo "<div>$sql</div>";
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = (int)$row['collid'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getSpeciesListDownloadData($collid): array
    {
        $returnArr = array();
        $targetTidArr = array();
        $parentTaxonArr = array();
        $tidSql = 'SELECT DISTINCT tid FROM omoccurrences WHERE collid = ' . (int)$collid . ' ';
        if($result = $this->conn->query($tidSql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if($row['tid']){
                    $targetTidArr[] = $row['tid'];
                }
                unset($rows[$index]);
            }
        }
        $parentTaxonSql = 'SELECT DISTINCT te.tid, t.TID AS parentTid, t.RankId, t.SciName '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
            'WHERE te.tid IN(' . implode(',', $targetTidArr) . ') AND t.tid = t.tidaccepted AND t.RankId IN(10,30,60,100,140) ';
        //echo '<div>Parent sql: ' .$parentTaxonSql. '</div>';
        if($result = $this->conn->query($parentTaxonSql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $parentTaxonArr[$row['tid']][(int)$row['RankId']]['id'] = $row['parentTid'];
                $parentTaxonArr[$row['tid']][(int)$row['RankId']]['sciname'] = $row['SciName'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT DISTINCT t.TID, t.SciName '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'WHERE (te.tid IN(' . implode(',', $targetTidArr) . ') AND t.RankId >= 180 AND t.tid = t.tidaccepted) '.
            'AND (t.SciName LIKE "% %" OR t.TID NOT IN(SELECT DISTINCT parenttid FROM taxa)) ';
        //echo '<div>Table sql: ' .$sql. '</div>';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $tid = $row['TID'];
                if($tid){
                    $recordArr = array();
                    $parentArr = (array_key_exists($tid,$parentTaxonArr) ? $parentTaxonArr[$tid] : array());
                    $recordArr['kingdomName'] = (array_key_exists(10, $parentArr) ? $parentArr[10]['sciname'] : '');
                    $recordArr['phylumName'] = (array_key_exists(30, $parentArr) ? $parentArr[30]['sciname'] : '');
                    $recordArr['className'] = (array_key_exists(60, $parentArr) ? $parentArr[60]['sciname'] : '');
                    $recordArr['orderName'] = (array_key_exists(100, $parentArr) ? $parentArr[100]['sciname'] : '');
                    $recordArr['familyName'] = (array_key_exists(140, $parentArr) ? $parentArr[140]['sciname'] : '');
                    $recordArr['SciName'] = $row['SciName'];
                    $returnArr[] = $recordArr;
                }
                unset($rows[$index]);
            }
        }
        $kingdomName  = array_column($returnArr, 'kingdomName');
        $phylumName = array_column($returnArr, 'phylumName');
        $className = array_column($returnArr, 'className');
        $orderName = array_column($returnArr, 'orderName');
        $familyName = array_column($returnArr, 'familyName');
        $SciName = array_column($returnArr, 'SciName');
        array_multisort($kingdomName, SORT_ASC, $phylumName, SORT_ASC, $className, SORT_ASC, $orderName, SORT_ASC, $familyName, SORT_ASC, $SciName, SORT_ASC, $returnArr);
        array_unshift($returnArr, array('Kingdom', 'Phylum', 'Class', 'Order', 'Family', 'Scientific Name'));
        return $returnArr;
    }

    public function getTaxonomicDistributionData($collId): array
    {
        $retArr = array();
        $sql = 'SELECT family, count(occid) AS cnt FROM omoccurrences WHERE family IS NOT NULL AND collid = ' . $collId . ' GROUP BY family';
        //echo $sql; exit;
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[ucwords($row['family'])] = $row['cnt'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function performOccurrenceCleaning($collidStr): void
    {
        $sql = 'UPDATE omoccurrences SET sciname = family WHERE collid IN(' . $collidStr . ') AND family IS NOT NULL AND ISNULL(sciname) ';
        $this->conn->query($sql);

        $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.sciname '.
            'SET o.tid = t.tid '.
            'WHERE o.collid IN(' . $collidStr . ') AND ISNULL(o.tid) AND t.tid IS NOT NULL ';
        $this->conn->query($sql);

        $sql = 'UPDATE omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
            'SET i.tid = o.tid '.
            'WHERE o.collid IN(' . $collidStr . ') AND i.tid <> o.tid ';
        $this->conn->query($sql);
    }

    public function updateCollectionRecord($collid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($collid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'collid' && $field !== 'collectionguid' && $field !== 'securitykey' && array_key_exists($field, $editData)){
                    $fieldNameArr[] = $field;
                    if($field === 'configjson'){
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, json_encode($editData[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                    }
                }
            }
            $sql = 'UPDATE omcollections SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE collid = ' . (int)$collid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function updateCollectionStatistics($collidStr): int
    {
        $collidStr = SanitizerService::cleanInStr($this->conn, $collidStr);
        $this->performOccurrenceCleaning($collidStr);
        return $this->updateCollectionStats($collidStr);
    }

    public function updateCollectionStats($collidStr): int
    {
        $returnVal = 1;
        $recordCnt = 0;
        $georefCnt = 0;
        $familyCnt = 0;
        $genusCnt = 0;
        $speciesCnt = 0;
        $statsArr = array();
        
        $sql = 'SELECT COUNT(o.occid) AS SpecimenCount, COUNT(o.decimalLatitude) AS GeorefCount, '.
            'COUNT(DISTINCT o.family) AS FamilyCount, COUNT(o.typeStatus) AS TypeCount, '.
            'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS SpecimensCountID, '.
            'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
            'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount '.
            'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $recordCnt = $row['SpecimenCount'];
                $georefCnt = $row['GeorefCount'];
                $familyCnt = $row['FamilyCount'];
                $genusCnt = $row['GeneraCount'];
                $speciesCnt = $row['SpeciesCount'];
                $statsArr['SpecimensCountID'] = $row['SpecimensCountID'];
                $statsArr['TotalTaxaCount'] = $row['TotalTaxaCount'];
                $statsArr['TypeCount'] = $row['TypeCount'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT count(DISTINCT o.occid) as imgcnt '.
            'FROM omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $statsArr['imgcnt'] = $row['imgcnt'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT COUNT(CASE WHEN g.resourceurl LIKE "http://www.boldsystems%" THEN o.occid ELSE NULL END) AS boldcnt, '.
            'COUNT(CASE WHEN g.resourceurl LIKE "http://www.ncbi%" THEN o.occid ELSE NULL END) AS gencnt '.
            'FROM omoccurrences AS o LEFT JOIN omoccurgenetic AS g ON o.occid = g.occid '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $statsArr['boldcnt'] = $row['boldcnt'];
                $statsArr['gencnt'] = $row['gencnt'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT count(r.occid) AS refcnt '.
            'FROM omoccurrences AS o LEFT JOIN referenceoccurlink AS r ON o.occid = r.occid '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $statsArr['refcnt'] = $row['refcnt'];
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT o.family, COUNT(o.occid) AS SpecimensPerFamily, COUNT(o.decimalLatitude) AS GeorefSpecimensPerFamily, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerFamily, '.
            'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerFamily '.
            'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'WHERE o.collid IN(' . $collidStr . ') '.
            'GROUP BY o.family ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $family = $row['family'] ? str_replace(array('"', "'"), '', $row['family']) : '';
                if($family){
                    $statsArr['families'][$family]['SpecimensPerFamily'] = $row['SpecimensPerFamily'];
                    $statsArr['families'][$family]['GeorefSpecimensPerFamily'] = $row['GeorefSpecimensPerFamily'];
                    $statsArr['families'][$family]['IDSpecimensPerFamily'] = $row['IDSpecimensPerFamily'];
                    $statsArr['families'][$family]['IDGeorefSpecimensPerFamily'] = $row['IDGeorefSpecimensPerFamily'];
                }
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT o.country, COUNT(o.occid) AS CountryCount, COUNT(o.decimalLatitude) AS GeorefSpecimensPerCountry, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerCountry, '.
            'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerCountry '.
            'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'WHERE o.collid IN(' . $collidStr . ') '.
            'GROUP BY o.country ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $country = $row['country'] ? str_replace(array('"', "'"), '', $row['country']) : '';
                if($country){
                    $statsArr['countries'][$country]['CountryCount'] = $row['CountryCount'];
                    $statsArr['countries'][$country]['GeorefSpecimensPerCountry'] = $row['GeorefSpecimensPerCountry'];
                    $statsArr['countries'][$country]['IDSpecimensPerCountry'] = $row['IDSpecimensPerCountry'];
                    $statsArr['countries'][$country]['IDGeorefSpecimensPerCountry'] = $row['IDGeorefSpecimensPerCountry'];
                }
                unset($rows[$index]);
            }
        }

        $returnArrJson = json_encode($statsArr);
        $sql = 'UPDATE omcollectionstats '.
            "SET dynamicProperties = '".SanitizerService::cleanInStr($this->conn, $returnArrJson)."' ".
            'WHERE collid IN(' . $collidStr . ') ';
        if(!$this->conn->query($sql)){
            $returnVal = 0;
        }
        
        $sql = 'UPDATE omcollectionstats AS cs '.
            'SET cs.recordcnt = '.$recordCnt.',cs.georefcnt = '.$georefCnt.',cs.familycnt = '.$familyCnt.',cs.genuscnt = '.$genusCnt.
            ',cs.speciescnt = '.$speciesCnt.', cs.datelastmodified = CURDATE() '.
            'WHERE cs.collid IN(' . $collidStr . ') ';
        if(!$this->conn->query($sql)){
            $returnVal = 0;
        }
        
        if($GLOBALS['SOLR_MODE']){
            (new SOLRService)->updateSOLR();
        }
        return $returnVal;
    }

    public function updateUploadDate($collid): void
    {
        $sql = 'UPDATE omcollectionstats SET uploaddate = CURDATE() WHERE collid = ' . (int)$collid . ' ';
        $this->conn->query($sql);
    }
}
