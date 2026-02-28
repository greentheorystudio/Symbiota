<?php
include_once(__DIR__ . '/Checklists.php');
include_once(__DIR__ . '/Images.php');
include_once(__DIR__ . '/KeyCharacterStates.php');
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/TaxonDescriptionBlocks.php');
include_once(__DIR__ . '/TaxonVernaculars.php');
include_once(__DIR__ . '/../services/DbService.php');

class ChecklistTaxa{

	private $conn;

    private $fields = array(
        'cltlid' => array('dataType' => 'number', 'length' => 10),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'clid' => array('dataType' => 'number', 'length' => 10),
        'habitat' => array('dataType' => 'string', 'length' => 250),
        'abundance' => array('dataType' => 'string', 'length' => 50),
        'notes' => array('dataType' => 'string', 'length' => 2000),
        'source' => array('dataType' => 'string', 'length' => 250),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateChecklistTaxaRecordsFromTidArr($clid, $tidArr): int
    {
        $recordsCreated = 0;
        $valueArr = array();
        if($clid && count($tidArr) > 0){
            foreach($tidArr as $tid){
                $valueArr[] = '(' . (int)$clid . ', ' . (int)$tid . ', "' . date('Y-m-d H:i:s') . '")';
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT INTO fmchklsttaxalink(clid, tid, initialtimestamp) '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function createChecklistTaxonRecord($clid, $data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'clid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'clid';
        $fieldValueArr[] = (int)$clid;
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO fmchklsttaxalink(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteChecklistTaxonRecords($tid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM fmchklsttaxalink WHERE tid = ' . (int)$tid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function deleteChecklistTaxonRecord($cltlid): int
    {
        $retVal = 0;
        $tidSql = 'SELECT clid, tid FROM fmchklsttaxalink WHERE cltlid = ' . (int)$cltlid . ' ';
        if($result = $this->conn->query($tidSql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                $clid = $row['clid'];
                $tid = $row['tid'];
                if((int)$clid > 0 && (int)$tid > 0){
                    (new Images)->deleteChecklistTaxonImageTags($clid, $tid);
                    $sql = 'DELETE FROM fmchklsttaxalink WHERE cltlid = ' . (int)$cltlid . ' ';
                    if($this->conn->query($sql)){
                        $retVal = 1;
                    }
                }
            }
        }
        return $retVal;
    }

    public function getChecklistTaxa($clidArr, $includeKeyData, $includeSynonymyData, $includeVernacularData, $useAcceptedNames, $taxonSort = null, $index = null, $recCnt = null, $descTab = null): array
    {
        $retArr = array();
        $tempArr = array();
        $descDataArr = array();
        $keyDataArr = array();
        $synonymyDataArr = array();
        $vernacularDataArr = array();
        if(count($clidArr) > 0){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'c');
            $fieldNameArr[] = 't.sciname';
            $fieldNameArr[] = 't.author';
            $fieldNameArr[] = 't.family';
            $fieldNameArr[] = 't.rankid';
            $fieldNameArr[] = 't.tidaccepted';
            $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . ' ';
            if($useAcceptedNames){
                $sql .= 'FROM fmchklsttaxalink AS c LEFT JOIN taxa AS t1 ON c.tid = t1.tid LEFT JOIN taxa AS t ON t1.tidaccepted = t.tid ';
            }
            else{
                $sql .= 'FROM fmchklsttaxalink AS c LEFT JOIN taxa AS t ON c.tid = t.tid ';
            }
            $sql .= 'WHERE c.clid IN(' . implode(',', $clidArr) . ') ';
            if($taxonSort === 'family'){
                $sql .= 'ORDER BY t.family, t.sciname ';
            }
            else{
                $sql .= 'ORDER BY t.sciname ';
            }
            if((int)$recCnt > 0){
                $startIndex = (int)$index * (int)$recCnt;
                $sql .= 'LIMIT ' . $startIndex . ', ' . (int)$recCnt . ' ';
            }
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $tidArr = array();
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rowIndex => $row){
                    if((int)$row['tidaccepted'] > 0){
                        if(!in_array($row['tidaccepted'], $tidArr, true)){
                            $tidArr[] = $row['tidaccepted'];
                        }
                        $nodeArr = array();
                        foreach($fields as $val){
                            $name = $val->name;
                            if($name === 'family'){
                                $nodeArr[$name] = $row['family'] ?: '[Incertae Sedis]';
                            }
                            else{
                                $nodeArr[$name] = $row[$name];
                            }
                        }
                        if($includeKeyData || $includeSynonymyData || $includeVernacularData){
                            $tempArr[] = $nodeArr;
                        }
                        else{
                            $retArr[] = $nodeArr;
                        }
                    }
                    unset($rows[$rowIndex]);
                }
                if(($includeKeyData || $includeSynonymyData || $includeVernacularData || $descTab) && count($tidArr) > 0){
                    if($includeKeyData){
                        $keyDataArr = (new KeyCharacterStates)->getCharacterStatesFromTidArr($tidArr);
                    }
                    if($includeSynonymyData){
                        $synonymyDataArr = (new Taxa)->getTaxaSynonymArrFromTidArr($tidArr);
                    }
                    if($includeVernacularData){
                        $vernacularDataArr = (new TaxonVernaculars)->getVernacularArrFromTidArr($tidArr);
                    }
                    if($descTab){
                        $descDataArr = (new TaxonDescriptionBlocks)->getTaxaDescriptionBlockStatementsFromTidArr($tidArr, $descTab);
                    }
                    if($keyDataArr || $synonymyDataArr || $vernacularDataArr || $descTab){
                        foreach($tempArr as $taxonArr){
                            if($includeSynonymyData){
                                $taxonArr['synonymyData'] = $synonymyDataArr[$taxonArr['tidaccepted']] ?? null;
                            }
                            if($includeVernacularData){
                                $taxonArr['vernacularData'] = $vernacularDataArr[$taxonArr['tidaccepted']] ?? null;
                            }
                            if($includeKeyData && array_key_exists($taxonArr['tidaccepted'], $keyDataArr)){
                                $taxonArr['keyData'] = $keyDataArr[$taxonArr['tidaccepted']];
                            }
                            if($descTab && array_key_exists($taxonArr['tidaccepted'], $descDataArr)){
                                $taxonArr['descData'] = $descDataArr[$taxonArr['tidaccepted']];
                            }
                            $retArr[] = $taxonArr;
                        }
                    }
                    else{
                        $retArr[] = $tempArr;
                    }
                }
            }
        }
        return $retArr;
    }

    public function getChecklistTaxonData($clid, $tid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM fmchklsttaxalink WHERE tid = ' . (int)$tid . ' AND clid = ' . (int)$clid . ' ';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
            }
        }
        return $retArr;
    }

    public function getTaxonChecklistArr($tid): array
    {
        $retArr = array();
        $sql = 'SELECT c.clid, c.`name` '.
            'FROM fmchklsttaxalink AS t LEFT JOIN fmchecklists AS c ON t.clid = c.clid '.
            'WHERE t.tid = ' . (int)$tid . ' AND ISNULL(c.expiration) ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['clid'] = $row['clid'];
                $nodeArr['name'] = $row['name'];
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function remapChecklistTaxon($tid, $targetTid): int
    {
        $retVal = 0;
        if($tid && $targetTid){
            $sql = 'UPDATE fmchklsttaxalink SET tid = ' . (int)$targetTid . ' WHERE tid = ' . (int)$tid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function updateChecklistTaxonRecord($cltlid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($cltlid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE fmchklsttaxalink SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE cltlid = ' . (int)$cltlid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
