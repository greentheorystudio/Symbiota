<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonVernaculars{

    private $conn;

    private $fields = array(
        'vid' => array('dataType' => 'number', 'length' => 11),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'vernacularname' => array('dataType' => 'string', 'length' => 80),
        'language' => array('dataType' => 'string', 'length' => 15),
        'langid' => array('dataType' => 'number', 'length' => 11),
        'source' => array('dataType' => 'string', 'length' => 50),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'username' => array('dataType' => 'string', 'length' => 45),
        'isupperterm' => array('dataType' => 'number', 'length' => 11),
        'sortsequence' => array('dataType' => 'number', 'length' => 11),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function createTaxonCommonNameRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'vid' && array_key_exists($field, $data)){
                if($field === 'source'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT IGNORE INTO taxavernaculars(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteTaxonCommonNameRecord($vid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM taxavernaculars WHERE vid = ' . (int)$vid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function deleteTaxonVernacularRecords($tid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM taxavernaculars WHERE tid = ' . (int)$tid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getAutocompleteVernacularList($opts): array
    {
        $retArr = array();
        $term = array_key_exists('term', $opts) ? SanitizerService::cleanInStr($this->conn, $opts['term']) : null;
        if($term){
            $ignoreChars = array(' ', '-', "'");
            $fixedTerm = str_replace($ignoreChars, '', $term);
            $limit = array_key_exists('limit', $opts) ? (int)$opts['limit'] : null;
            $sql = 'SELECT DISTINCT v.vid, v.vernacularname '.
                'FROM taxavernaculars AS v '.
                'WHERE REPLACE(REPLACE(REPLACE(v.vernacularname, " ", ""), "-", ""), "\'", "") LIKE "' . $fixedTerm . '%" '.
                'ORDER BY v.vernacularname ';
            if($limit){
                $sql .= 'LIMIT ' . $limit . ' ';
            }
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $scinameArr = array();
                    $scinameArr['tid'] = '';
                    $scinameArr['vid'] = $row['vid'];
                    $scinameArr['label'] = $row['vernacularname'];
                    $scinameArr['name'] = $row['vernacularname'];
                    $scinameArr['sciname'] = $row['vernacularname'];
                    $retArr[] = $scinameArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getCommonNamesByTaxonomicGroup($parentTid, $index): array
    {
        $retArr = array();
        if($parentTid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . ' FROM taxavernaculars '.
                'WHERE tid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') '.
                'ORDER BY vernacularname '.
                'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rIndex => $row){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $row[$name];
                    }
                    $retArr[] = $nodeArr;
                    unset($rows[$rIndex]);
                }
            }
        }
        return $retArr;
    }

    public function getCommonNamesFromTid($tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' FROM taxavernaculars WHERE tid = ' . (int)$tid . ' ORDER BY vernacularname ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getVernacularArrFromTidArr($tidArr): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT t.tidaccepted, v.vernacularname '.
            'FROM taxavernaculars AS v LEFT JOIN taxa AS t ON v.tid = t.tid '.
            'WHERE t.tidaccepted IN(' . implode(',', $tidArr) . ') '.
            'ORDER BY t.tidaccepted, v.vernacularname ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['tidaccepted'], $retArr)){
                    $retArr[$row['tidaccepted']] = array();
                }
                $nodeArr = array();
                $nodeArr['vernacularname'] = $row['vernacularname'];
                $nodeArr['vernaculartid'] = $row['tidaccepted'];
                $retArr[$row['tidaccepted']][] = $nodeArr;
                unset($rows[$index]);
            }
        }

        $sql = 'SELECT DISTINCT te.tid, v.tid AS vernaculartid, v.vernacularname '.
            'FROM taxavernaculars AS v LEFT JOIN taxaenumtree AS te ON v.tid = te.parenttid '.
            'WHERE te.tid IN(' . implode(',', $tidArr) . ') '.
            'ORDER BY te.tid, v.vernacularname ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['tid'], $retArr)){
                    $retArr[$row['tid']] = array();
                }
                $nodeArr = array();
                $nodeArr['vernacularname'] = $row['vernacularname'];
                $nodeArr['vernaculartid'] = $row['vernaculartid'];
                $retArr[$row['tid']][] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getHighestRankingTidByVernacular($vernacularId): int
    {
        $returnVal = 0;
        $sql = 'SELECT v.tid FROM taxavernaculars AS v LEFT JOIN taxa AS t ON v.tid = t.tid '.
            'WHERE v.vid = ' . (int)$vernacularId . ' ORDER BY t.rankid LIMIT 1 ';
        $result = $this->conn->query($sql);
        if($row = $result->fetch_array(MYSQLI_ASSOC)){
            $returnVal = $row['tid'];
        }
        $result->free();
        return $returnVal;
    }

    public function getTaxaListFromVernacularFuzzySearch($vernacular): array
    {
        $retArr = array();
        $tempArr = array();
        $vernacularDataArr = array();
        $ignoreChars = array(' ', '-', "'");
        $fixedVernacular = str_replace($ignoreChars, '', $vernacular);
        $sql = 'SELECT DISTINCT t.tidaccepted, t.sciname FROM taxa AS t LEFT JOIN taxavernaculars AS tv ON t.tidaccepted = tv.tid ';
        $sql .= 'WHERE REPLACE(REPLACE(REPLACE(tv.vernacularname, " ", ""), "-", ""), "\'", "") LIKE "%' . SanitizerService::cleanInStr($this->conn, $fixedVernacular) . '%" ';
        $sql .= 'ORDER BY t.sciname ';
        //error_log($sql);
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $tidArr = array();
            $result->free();
            foreach($rows as $index => $row){
                if(!in_array($row['tidaccepted'], $tidArr, true)){
                    $tidArr[] = $row['tidaccepted'];
                }
                $nodeArr = array();
                $nodeArr['tid'] = $row['tidaccepted'];
                $nodeArr['sciname'] = $row['sciname'];
                $nodeArr['vernacularData'] = array();
                $tempArr[] = $nodeArr;
                unset($rows[$index]);
            }
            if(count($tidArr) > 0){
                $vernacularDataArr = $this->getVernacularArrFromTidArr($tidArr);
            }
            foreach($tempArr as $taxonArr){
                $taxonArr['vernacularData'] = $vernacularDataArr[$taxonArr['tid']] ?? null;
                $retArr[] = $taxonArr;
            }
        }
        return $retArr;
    }

    public function getTaxonVernacularCount($tid): int
    {
        $retVal = 0;
        if($tid){
            $sql ='SELECT COUNT(vid) AS cnt FROM taxavernaculars WHERE tid = ' . (int)$tid;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $retVal = $row->cnt;
            }
            $result->free();
        }
        return $retVal;
    }

    public function getTidArrFromVernacular($vernacular): array
    {
        $retArr = array();
        $whereStr = '';
        $ignoreChars = array(' ', '-', "'");
        $fixedVernacular = str_replace($ignoreChars, '', $vernacular);
        $sql = 'SELECT DISTINCT t.tid, t.sciname FROM taxa AS t ';
        $whereStr .= 'OR REPLACE(REPLACE(REPLACE(v.vernacularname, " ", ""), "-", ""), "\'", "") LIKE "' . SanitizerService::cleanInStr($this->conn, $fixedVernacular) . '%" ';
        $sql .= 'WHERE tid IN(SELECT v.tid FROM taxavernaculars AS v WHERE ' . substr($whereStr,3) . ') ';
        $sql .= 'OR tid IN(SELECT te.tid FROM taxavernaculars AS v LEFT JOIN taxaenumtree AS te ON v.tid = te.parenttid WHERE ' . substr($whereStr,3) . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = $row['tid'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function remapTaxonVernaculars($tid, $targetTid): int
    {
        $retVal = 0;
        if($tid && $targetTid){
            $sql = 'UPDATE taxavernaculars SET tid = ' . (int)$targetTid . ' WHERE tid = ' . (int)$tid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function removeCommonNamesInTaxonomicGroup($parentTid): int
    {
        $retVal = 1;
        if($parentTid){
            $sql = 'DELETE FROM taxavernaculars WHERE TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function setSciNameSearchDataByVernaculars($searchData, $vernaculars): array
    {
        $whereStr = '';
        $ignoreChars = array(' ', '-', "'");
        $sql = 'SELECT DISTINCT t.tid, t.sciname FROM taxa AS t ';
        foreach($vernaculars as $name){
            $fixedName = str_replace($ignoreChars, '', $name);
            $whereStr .= 'OR REPLACE(REPLACE(REPLACE(v.vernacularname, " ", ""), "-", ""), "\'", "") LIKE "' . SanitizerService::cleanInStr($this->conn, $fixedName) . '%" ';
        }
        $sql .= 'WHERE tid IN(SELECT v.tid FROM taxavernaculars AS v WHERE ' . substr($whereStr,3) . ') ';
        $sql .= 'OR tid IN(SELECT te.tid FROM taxavernaculars AS v LEFT JOIN taxaenumtree AS te ON v.tid = te.parenttid WHERE ' . substr($whereStr,3) . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $searchData[$row['sciname']] = $row['tid'];
                unset($rows[$index]);
            }
        }
        return $searchData;
    }

    public function updateVernacularRecord($vid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($vid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    if($field === 'language' || $field === 'source'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = $fieldStr . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sqlPartArr[] = 'username = "' . $GLOBALS['PARAMS_ARR']['un'] . '"';
            $sql = 'UPDATE taxavernaculars SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE vid = ' . (int)$vid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
