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
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function addTaxonCommonName($tid, $name, $langId): bool
    {
        if($tid && $name && $langId){
            $sql = 'INSERT IGNORE INTO taxavernaculars(TID,VernacularName,langid) VALUES('.
                (int)$tid . ',"' . SanitizerService::cleanInStr($this->conn, $name) . '",' . (int)$langId . ')';
            return $this->conn->query($sql);
        }
        return false;
    }

    public function getAutocompleteVernacularList($opts): array
    {
        $retArr = array();
        $term = array_key_exists('term', $opts) ? SanitizerService::cleanInStr($this->conn, $opts['term']) : null;
        if($term){
            $limit = array_key_exists('limit', $opts) ? (int)$opts['limit'] : null;
            $sql = 'SELECT DISTINCT v.VernacularName '.
                'FROM taxavernaculars AS v '.
                'WHERE v.VernacularName LIKE "%' . $term . '%" '.
                'ORDER BY v.VernacularName ';
            if($limit){
                $sql .= 'LIMIT ' . $limit . ' ';
            }
            $rs = $this->conn->query($sql);
            while ($r = $rs->fetch_object()){
                $scinameArr = array();
                $scinameArr['tid'] = '';
                $scinameArr['label'] = $r->VernacularName;
                $scinameArr['name'] = $r->VernacularName;
                $retArr[] = $scinameArr;
            }
        }
        return $retArr;
    }

    public function getCommonNamesByTaxonomicGroup($parentTid, $index): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT VID, VernacularName FROM taxavernaculars '.
                'WHERE TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') '.
                'ORDER BY VernacularName '.
                'LIMIT ' . (($index - 1) * 50000) . ', 50000';
            //echo $sql;
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    $nodeArr['vid'] = $r->VID;
                    $nodeArr['vernacularname'] = $r->VernacularName;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }

    public function getCommonNamesFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT VernacularName, langid FROM taxavernaculars WHERE TID = ' . (int)$tid . ' ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['commonname'] = $r->VernacularName;
                $nodeArr['langid'] = $r->langid;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getHighestRankingTidByVernacular($vernacular): int
    {
        $returnVal = 0;
        $sql = 'SELECT v.tid FROM taxavernaculars AS v LEFT JOIN taxa AS t ON v.TID = t.TID '.
            'WHERE v.VernacularName = "' . SanitizerService::cleanInStr($this->conn, $vernacular) . '" ORDER BY t.RankId LIMIT 1 ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $returnVal = $r->tid;
            }
            $rs->free();
        }
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
            'FROM taxa AS t LEFT JOIN taxavernaculars AS v ON t.TID = v.TID ';
        foreach($vernaculars as $name){
            $whereStr .= "OR v.VernacularName = '" . SanitizerService::cleanInStr($this->conn, $name) . "' ";
        }
        $sql .= 'WHERE ' .substr($whereStr,3). ' ';
        //echo "<div>sql: ".$sql."</div>";
        if($result = $this->conn->query($sql)){
            while($row = $result->fetch_object()){
                $searchData[$row->sciname] = $row->tid;
            }
        }
        $result->free();
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
