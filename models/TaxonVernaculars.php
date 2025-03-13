<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonVernaculars{

    private $conn;

    private $fields = array(
        "vid" => array("dataType" => "number", "length" => 11),
        "tid" => array("dataType" => "number", "length" => 10),
        "vernacularname" => array("dataType" => "string", "length" => 80),
        "language" => array("dataType" => "string", "length" => 15),
        "langid" => array("dataType" => "number", "length" => 11),
        "source" => array("dataType" => "string", "length" => 50),
        "notes" => array("dataType" => "string", "length" => 250),
        "username" => array("dataType" => "string", "length" => 45),
        "isupperterm" => array("dataType" => "number", "length" => 11),
        "sortsequence" => array("dataType" => "number", "length" => 11),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
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
        //echo "<div>".$sql."</div>";
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

    public function getAutocompleteVernacularList($opts): array
    {
        $retArr = array();
        $term = array_key_exists('term', $opts) ? SanitizerService::cleanInStr($this->conn, $opts['term']) : null;
        if($term){
            $limit = array_key_exists('limit', $opts) ? (int)$opts['limit'] : null;
            $sql = 'SELECT DISTINCT v.vernacularname '.
                'FROM taxavernaculars AS v '.
                'WHERE v.vernacularname LIKE "%' . $term . '%" '.
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
                    $scinameArr['label'] = $row['VernacularName'];
                    $scinameArr['name'] = $row['VernacularName'];
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
            //echo $sql;
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

    public function getHighestRankingTidByVernacular($vernacular): int
    {
        $returnVal = 0;
        $sql = 'SELECT v.tid FROM taxavernaculars AS v LEFT JOIN taxa AS t ON v.tid = t.tid '.
            'WHERE v.vernacularname = "' . SanitizerService::cleanInStr($this->conn, $vernacular) . '" ORDER BY t.rankid LIMIT 1 ';
        $result = $this->conn->query($sql);
        if($row = $result->fetch_array(MYSQLI_ASSOC)){
            $returnVal = $row['tid'];
        }
        $result->free();
        return $returnVal;
    }

    public function removeCommonNamesInTaxonomicGroup($parentTid): int
    {
        $retVal = 1;
        if($parentTid){
            $sql = 'DELETE FROM taxavernaculars WHERE TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function setSciNameSearchDataByVernaculars($searchData, $vernaculars): array
    {
        $whereStr = '';
        $sql = 'SELECT DISTINCT t.tid, t.sciname ' .
            'FROM taxa AS t LEFT JOIN taxavernaculars AS v ON t.tid = v.tid ';
        foreach($vernaculars as $name){
            $whereStr .= "OR v.vernacularname = '" . SanitizerService::cleanInStr($this->conn, $name) . "' ";
        }
        $sql .= 'WHERE ' . substr($whereStr,3) . ' ';
        //echo "<div>sql: ".$sql."</div>";
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
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
