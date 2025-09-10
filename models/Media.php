<?php
include_once(__DIR__ . '/Permissions.php');
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/FileSystemService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class Media{

	private $conn;

    private $fields = array(
        'mediaid' => array('dataType' => 'number', 'length' => 10),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'accessuri' => array('dataType' => 'string', 'length' => 2048),
        'sourceurl' => array('dataType' => 'string', 'length' => 255),
        'title' => array('dataType' => 'string', 'length' => 255),
        'creatoruid' => array('dataType' => 'number', 'length' => 10),
        'creator' => array('dataType' => 'string', 'length' => 45),
        'type' => array('dataType' => 'string', 'length' => 45),
        'format' => array('dataType' => 'string', 'length' => 45),
        'owner' => array('dataType' => 'string', 'length' => 250),
        'furtherinformationurl' => array('dataType' => 'string', 'length' => 2048),
        'language' => array('dataType' => 'string', 'length' => 45),
        'usageterms' => array('dataType' => 'string', 'length' => 255),
        'rights' => array('dataType' => 'string', 'length' => 255),
        'bibliographiccitation' => array('dataType' => 'string', 'length' => 255),
        'publisher' => array('dataType' => 'string', 'length' => 255),
        'contributor' => array('dataType' => 'string', 'length' => 255),
        'locationcreated' => array('dataType' => 'string', 'length' => 1000),
        'description' => array('dataType' => 'string', 'length' => 1000),
        'sortsequence' => array('dataType' => 'number', 'length' => 10),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function clearExistingMediaNotInUpload($collid): int
    {
        $retVal = 0;
        $sql = 'DELETE m.* FROM media AS m LEFT JOIN omoccurrences AS o ON m.occid = o.occid '.
            'WHERE o.collid = ' . (int)$collid . ' AND m.accessuri NOT IN(SELECT DISTINCT accessuri FROM uploadmediatemp WHERE collid = ' . (int)$collid . ') ';
        if($this->conn->query($sql)){
            $retVal = 1;
        }
        return $retVal;
    }

    public function createMediaRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'mediaid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                if($field === 'language' || $field === 'owner'){
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
        $sql = 'INSERT INTO media(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function createMediaRecordsFromUploadData($collId): int
    {
        $skipFields = array('mediaid', 'creatoruid', 'initialtimestamp');
        $retVal = 1;
        $fieldNameArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    if($field === 'language' || $field === 'owner'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                }
            }
            if(count($fieldNameArr) > 0){
                $sql = 'INSERT INTO media(' . implode(',', $fieldNameArr) . ') '.
                    'SELECT ' . implode(',', $fieldNameArr) . ' FROM uploadmediatemp '.
                    'WHERE collid = ' . (int)$collId . ' AND occid IS NOT NULL AND accessuri IS NOT NULL AND format IS NOT NULL ';
                //echo "<div>".$sql."</div>";
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
        }
        return $retVal;
    }

    public function deleteAssociatedMediaFiles($idType, $id): void
    {
        $sql = '';
        if($idType === 'occid'){
            $sql = 'SELECT accessuri FROM media WHERE occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $sql = 'SELECT accessuri FROM media WHERE occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $sql = 'SELECT m.accessuri FROM media AS m LEFT JOIN omoccurrences AS o ON m.occid = o.occid '.
                'WHERE o.collid = ' . (int)$id . ' ';
        }
        elseif($idType === 'tid'){
            $sql = 'SELECT accessuri FROM media WHERE tid = ' . (int)$id . ' AND ISNULL(occid) ';
        }
        //echo '<div>'.$sql.'</div>';
        if($sql && $result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(strncmp($row['accessuri'], '/', 1) === 0){
                    FileSystemService::deleteFile(($GLOBALS['SERVER_ROOT'] . $row['accessuri']), true);
                }
                unset($rows[$index]);
            }
        }
    }

    public function deleteAssociatedMediaRecords($idType, $id): int
    {
        $this->deleteAssociatedMediaFiles($idType, $id);
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ') ';
        }
        elseif($idType === 'tid'){
            $whereStr = 'tid = ' . (int)$id . ' AND ISNULL(occid) ';
        }
        if($whereStr){
            $sql = 'DELETE FROM media WHERE ' . $whereStr . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function deleteMediaRecord($mediaid): int
    {
        $retVal = 1;
        $data = $this->getMediaData($mediaid);
        if($data['accessuri'] && strncmp($data['accessuri'], '/', 1) === 0){
            $urlServerPath = FileSystemService::getServerPathFromUrlPath($data['accessuri']);
            FileSystemService::deleteFile($urlServerPath, true);
        }
        $sql = 'DELETE FROM media WHERE mediaid = ' . (int)$mediaid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getMediaArrByProperty($property, $value, $limitFormat = null): array
    {
        $returnArr = array();
        if($property === 'occid' || $property === 'tid'){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'm');
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ', o.collid, o.localitysecurity '.
                'FROM media AS m LEFT JOIN omoccurrences AS o ON m.occid = o.occid '.
                'WHERE m.' . SanitizerService::cleanInStr($this->conn, $property) . ' = ' . (int)$value . ' ';
            if($limitFormat){
                if($limitFormat === 'audio'){
                    $sql .= 'AND m.format LIKE "audio/%" ';
                }
                elseif($limitFormat === 'video'){
                    $sql .= 'AND m.format LIKE "video/%" ';
                }
            }
            $sql .= 'ORDER BY m.sortsequence ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $permitted = true;
                    $localitySecurity = (int)$row['localitysecurity'] === 1;
                    if($localitySecurity){
                        $rareSpCollidAccessArr = (new Permissions)->getUserRareSpCollidAccessArr();
                        if(!in_array((int)$row['collid'], $rareSpCollidAccessArr, true)){
                            $permitted = false;
                        }
                    }
                    if($permitted){
                        $nodeArr = array();
                        foreach($fields as $val){
                            $name = $val->name;
                            if($name !== 'collid' && $name !== 'localitysecurity'){
                                $nodeArr[$name] = $row[$name];
                            }
                        }
                        $returnArr[] = $nodeArr;
                    }
                    unset($rows[$index]);
                }
            }
        }
        return $returnArr;
    }

    public function getMediaData($mediaid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM media WHERE mediaid = ' . (int)$mediaid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
                $retArr['taxonData'] = (int)$retArr['tid'] > 0 ? (new Taxa)->getTaxonFromTid($retArr['tid']) : null;
            }
        }
        return $retArr;
    }

    public function getTaxonArrDisplayMediaData($tidArr, $includeOccurrence = false, $limitPerTaxon = null, $sortsequenceLimit = null): array
    {
        $returnArr = array();
        if($tidArr && is_array($tidArr) && count($tidArr) > 0){
            $sql = 'SELECT DISTINCT m.mediaid, t.tidaccepted AS tid, m.occid, m.accessuri, m.title, m.creator, m.`type`, m.format, m.owner, m.description, '.
                't.securitystatus, o.basisofrecord, o.catalognumber, o.othercatalognumbers '.
                'FROM media AS m LEFT JOIN taxa AS t ON m.tid = t.tid '.
                'LEFT JOIN omoccurrences AS o ON m.occid = o.occid '.
                'WHERE t.tidaccepted IN(' . implode(',', $tidArr) . ') ';
            if(!$includeOccurrence){
                $sql .= 'AND ISNULL(m.occid) ';
            }
            if($sortsequenceLimit && (int)$sortsequenceLimit > 0){
                $sql .= 'AND m.sortsequence <= ' . (int)$sortsequenceLimit . ' ';
            }
            $sql .= 'ORDER BY m.sortsequence ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if((int)$row['securitystatus'] !== 1 || (int)$row['occid'] === 0){
                        if(!array_key_exists($row['tid'], $returnArr)){
                            $returnArr[$row['tid']] = array();
                        }
                        if((int)$limitPerTaxon === 0 || count($returnArr[$row['tid']]) < (int)$limitPerTaxon){
                            $nodeArr = array();
                            foreach($fields as $val){
                                $name = $val->name;
                                $nodeArr[$name] = $row[$name];
                            }
                            $returnArr[$row['tid']][] = $nodeArr;
                        }
                    }
                    unset($rows[$index]);
                }
            }

            $sql = 'SELECT DISTINCT m.mediaid, te.parenttid AS tid, m.occid, m.accessuri, m.title, m.creator, m.`type`, m.format, m.owner, m.description, '.
                't.securitystatus, o.basisofrecord, o.catalognumber, o.othercatalognumbers '.
                'FROM media AS m LEFT JOIN taxa AS t ON m.tid = t.tid '.
                'LEFT JOIN omoccurrences AS o ON m.occid = o.occid '.
                'LEFT JOIN taxaenumtree AS te ON t.tidaccepted = te.tid '.
                'WHERE te.parenttid IN(' . implode(',', $tidArr) . ') ';
            if(!$includeOccurrence){
                $sql .= 'AND ISNULL(m.occid) ';
            }
            if($sortsequenceLimit && (int)$sortsequenceLimit > 0){
                $sql .= 'AND m.sortsequence <= ' . (int)$sortsequenceLimit . ' ';
            }
            $sql .= 'ORDER BY m.sortsequence ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if((int)$row['securitystatus'] !== 1 || (int)$row['occid'] === 0){
                        if(!array_key_exists($row['tid'], $returnArr)){
                            $returnArr[$row['tid']] = array();
                        }
                        if((int)$limitPerTaxon === 0 || count($returnArr[$row['tid']]) < (int)$limitPerTaxon){
                            $nodeArr = array();
                            foreach($fields as $val){
                                $name = $val->name;
                                $nodeArr[$name] = $row[$name];
                            }
                            $returnArr[$row['tid']][] = $nodeArr;
                        }
                    }
                    unset($rows[$index]);
                }
            }
        }
        return $returnArr;
    }

    public function updateMediaRecord($medId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($medId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'mediaid' && array_key_exists($field, $editData)){
                    if($field === 'language' || $field === 'owner'){
                        $fieldName = '`' . $field . '`';
                    }
                    else{
                        $fieldName = $field;
                    }
                    $sqlPartArr[] = $fieldName . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE media SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE mediaid = ' . (int)$medId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE media SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
