<?php
include_once(__DIR__ . '/TaxonHierarchy.php');
include_once(__DIR__ . '/TaxonVernaculars.php');
include_once(__DIR__ . '/../services/DbService.php');

class Glossary{

	private $conn;

    private $fields = array(
        'glossid' => array('dataType' => 'number', 'length' => 10),
        'term' => array('dataType' => 'string', 'length' => 150),
        'definition' => array('dataType' => 'string', 'length' => 2000),
        'language' => array('dataType' => 'string', 'length' => 45),
        'source' => array('dataType' => 'string', 'length' => 1000),
        'translator' => array('dataType' => 'string', 'length' => 250),
        'author' => array('dataType' => 'string', 'length' => 250),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'resourceurl' => array('dataType' => 'string', 'length' => 600),
        'uid' => array('dataType' => 'number', 'length' => 10),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateGlossaryRelationshipRecordsFromGlossidArr($groupid, $relationType, $glossidArr): int
    {
        $recordsCreated = 0;
        $valueArr = array();
        if((int)$groupid > 0 && $relationType && count($glossidArr) > 0){
            foreach($glossidArr as $glossid){
                $valueArr[] = '(' . (int)$groupid . ', ' . (int)$glossid . ', "' . SanitizerService::cleanInStr($this->conn, $relationType) . '")';
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT IGNORE INTO glossarytermlink(glossgrpid, glossid, relationshiptype) '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function batchCreateGlossaryTaxonRelationshipRecordsFromGlossidArr($tid, $glossidArr): int
    {
        $recordsCreated = 0;
        $valueArr = array();
        if((int)$tid > 0 && count($glossidArr) > 0){
            foreach($glossidArr as $glossid){
                $valueArr[] = '(' . (int)$glossid . ', ' . (int)$tid . ')';
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT IGNORE INTO glossarytaxalink(glossid, tid) '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function createGlossaryRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'glossid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                if($field === 'language'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'uid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'];
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO glossary(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteGlossaryRecord($glossid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM glossaryimages WHERE glossid = ' . (int)$glossid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        if($retVal){
            $sql = 'DELETE FROM glossarytaxalink WHERE glossid = ' . (int)$glossid . ' ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        if($retVal){
            $sql = 'DELETE FROM glossarytermlink WHERE glossid = ' . (int)$glossid . ' ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        if($retVal){
            $sql = 'DELETE FROM glossary WHERE glossid = ' . (int)$glossid . ' ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function getGlossaryArr($recCnt, $index, $includeTid = true, $includeGlossGrpId = true, $glossidArr = null): array
    {
        $retArr = array();
        $tempArr = array();
        $startIndex = (int)$recCnt > 0 ? ((int)$index * (int)$recCnt) : 0;
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . ' '.
            'FROM glossary ';
        if($glossidArr && count($glossidArr) > 0){
            $sql .= 'WHERE glossid IN(' . implode(',', $glossidArr) . ') ';
        }
        $sql .= 'ORDER BY term  ';
        if((int)$recCnt > 0){
            $sql .= 'LIMIT ' . $startIndex . ', ' . (int)$recCnt . ' ';
        }
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $glossidArr = array();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $rowIndex => $row){
                if(!in_array($row['glossid'], $glossidArr, true)){
                    $glossidArr[] = $row['glossid'];
                }
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                if($includeTid || $includeGlossGrpId){
                    $tempArr[] = $nodeArr;
                }
                else{
                    $retArr[] = $nodeArr;
                }
                unset($rows[$rowIndex]);
            }
            if($includeTid || $includeGlossGrpId){
                if($includeGlossGrpId){
                    $glossGrpIdDataArr = $this->getGlossGroupIdArrFromGlossidArr($glossidArr);
                }
                if($includeTid){
                    $tidDataArr = $this->getTidArrFromGlossidArr($glossidArr);
                }
                foreach($tempArr as $glossArr){
                    if($includeGlossGrpId){
                        $glossArr['groupIdArr'] = $glossGrpIdDataArr[$glossArr['glossid']] ?? array();
                    }
                    if($includeTid){
                        $glossArr['tidArr'] = $tidDataArr[$glossArr['glossid']] ?? array();
                    }
                    $retArr[] = $glossArr;
                }
            }
        }
        return $retArr;
    }

    public function getGlossaryData($glossid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM glossary WHERE glossid = ' . (int)$glossid . ' ';
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

    public function getGlossaryLanguageArr(): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT `language` FROM glossary ORDER BY `language` ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = $row['language'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getGlossaryRelatedTermsDataFromGlossidArr($glossidArr, $relationType = null, $languageArr = null): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'g');
        $fieldNameArr[] = 'gt.glossgrpid';
        $fieldNameArr[] = 'gt.relationshiptype';
        $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . ' '.
            'FROM glossary AS g LEFT JOIN glossarytermlink AS gt ON g.glossid = gt.glossid '.
            'WHERE gt.glossgrpid IN(SELECT DISTINCT glossgrpid FROM glossarytaxalink WHERE glossid IN(' . implode(',', $glossidArr) . ') ';
        if($relationType === 'translation'){
            $sql .= 'AND relationshiptype = "translation" ';
        }
        elseif($relationType === 'synonym'){
            $sql .= 'AND relationshiptype = "synonym" ';
        }
        $sql .= ') AND g.glossid NOT IN(' . implode(',', $glossidArr) . ') ';
        if($languageArr && count($languageArr) > 0){
            $sql .= 'AND g.`language` IN("' . implode('","', $languageArr) . '") ';
        }
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
                if($relationType === 'translation'){
                    $retArr[$row['glossgrpid']][$row['language']][] = $nodeArr;
                }
                else{
                    $retArr[$row['glossgrpid']][] = $nodeArr;
                }
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getGlossaryTaxaArr(): array
    {
        $retArr = array();
        $tempArr = array();
        $sql = 'SELECT DISTINCT t.tid, t.sciname FROM glossarytaxalink AS gt LEFT JOIN taxa AS t ON gt.tid = t.tid '.
            'ORDER BY t.sciname ';
        if($result = $this->conn->query($sql)){
            $tidArr = array();
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if((int)$row['tid'] > 0){
                    if(!in_array($row['tid'], $tidArr, true)){
                        $tidArr[] = $row['tid'];
                    }
                    $nodeArr = array();
                    $nodeArr['tid'] = $row['tid'];
                    $nodeArr['sciname'] = $row['sciname'];
                    $tempArr[] = $nodeArr;
                }
                unset($rows[$index]);
            }
            $vernacularDataArr = (new TaxonVernaculars)->getVernacularArrFromTidArr($tidArr);
            foreach($tempArr as $taxonArr){
                $taxonArr['vernacularData'] = $vernacularDataArr[$taxonArr['tid']] ?? null;
                $retArr[] = $taxonArr;
            }
        }
        return $retArr;
    }

    public function getGlossGroupIdArrFromGlossidArr($glossidArr): array
    {
        $retArr = array();
        $sql = 'SELECT glossgrpid, glossid, relationshiptype FROM glossarytermlink WHERE glossid IN(' . implode(',', $glossidArr) . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                if(!array_key_exists($row['glossid'], $retArr)){
                    $retArr[$row['glossid']] = array();
                }
                $nodeArr['glossgrpid']= (int)$row['glossgrpid'];
                $nodeArr['relationshiptype']= $row['relationshiptype'];
                $retArr[$row['glossid']][] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getGlossGroupIdStartIndex(): int
    {
        $retVal = 0;
        $sql = 'SELECT glossgrpid FROM glossarytermlink ORDER BY glossgrpid DESC LIMIT 1 ';
        if($result = $this->conn->query($sql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                $retVal = (int)$row['glossgrpid'];
            }
        }
        $retVal++;
        return $retVal;
    }

    public function getTaxonGlossary($tid): array
    {
        $retArr = array();
        if($tid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'g');
            $tidArr = (new TaxonHierarchy)->getParentTidDataFromTidArr(array($tid));
            $tidArr[$tid][] = $tid;
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM glossary AS g LEFT JOIN glossarytaxalink AS gt ON g.glossid = gt.glossid '.
                'WHERE gt.tid IN('.implode(',', $tidArr[$tid]).') '.
                'ORDER BY g.term ';
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
        }
        return $retArr;
    }

    public function getTidArrFromGlossidArr($glossidArr): array
    {
        $retArr = array();
        $sql = 'SELECT glossid, tid FROM glossarytaxalink '.
            'WHERE glossid IN(' . implode(',', $glossidArr) . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['glossid'], $retArr)){
                    $retArr[$row['glossid']] = array();
                }
                $retArr[$row['glossid']][] = (int)$row['tid'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function updateGlossaryRecord($glossid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($glossid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'glossid' && $field !== 'uid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    if($field === 'language'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = $fieldStr . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE glossary SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE glossid = ' . (int)$glossid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
