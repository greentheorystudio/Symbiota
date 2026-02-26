<?php
include_once(__DIR__ . '/../services/DbService.php');

class UploadMediaTemp{

	private $conn;

    private $fields = array(
        'upmid' => array('dataType' => 'number', 'length' => 50),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'url' => array('dataType' => 'string', 'length' => 255),
        'thumbnailurl' => array('dataType' => 'string', 'length' => 255),
        'originalurl' => array('dataType' => 'string', 'length' => 255),
        'accessuri' => array('dataType' => 'string', 'length' => 255),
        'descriptivetranscripturi' => array('dataType' => 'string', 'length' => 255),
        'photographer' => array('dataType' => 'string', 'length' => 100),
        'title' => array('dataType' => 'string', 'length' => 255),
        'imagetype' => array('dataType' => 'string', 'length' => 50),
        'format' => array('dataType' => 'string', 'length' => 45),
        'caption' => array('dataType' => 'string', 'length' => 100),
        'alttext' => array('dataType' => 'string', 'length' => 355),
        'description' => array('dataType' => 'string', 'length' => 1000),
        'creator' => array('dataType' => 'string', 'length' => 45),
        'owner' => array('dataType' => 'string', 'length' => 100),
        'type' => array('dataType' => 'string', 'length' => 45),
        'sourceurl' => array('dataType' => 'string', 'length' => 255),
        'furtherinformationurl' => array('dataType' => 'string', 'length' => 2048),
        'referenceurl' => array('dataType' => 'string', 'length' => 255),
        'language' => array('dataType' => 'string', 'length' => 45),
        'copyright' => array('dataType' => 'string', 'length' => 255),
        'accessrights' => array('dataType' => 'string', 'length' => 255),
        'usageterms' => array('dataType' => 'string', 'length' => 255),
        'rights' => array('dataType' => 'string', 'length' => 255),
        'locality' => array('dataType' => 'string', 'length' => 250),
        'locationcreated' => array('dataType' => 'string', 'length' => 1000),
        'bibliographiccitation' => array('dataType' => 'string', 'length' => 255),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'collid' => array('dataType' => 'number', 'length' => 10),
        'dbpk' => array('dataType' => 'string', 'length' => 150),
        'publisher' => array('dataType' => 'string', 'length' => 255),
        'contributor' => array('dataType' => 'string', 'length' => 255),
        'sourceidentifier' => array('dataType' => 'string', 'length' => 150),
        'notes' => array('dataType' => 'string', 'length' => 350),
        'anatomy' => array('dataType' => 'string', 'length' => 100),
        'username' => array('dataType' => 'string', 'length' => 45),
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

    public function batchCreateRecords($collid, $data, $fieldMapping =  null): int
    {
        $recordsCreated = 0;
        $fieldNameArr = array();
        $valueArr = array();
        $skipFields = array('upmid', 'tid', 'occid', 'collid', 'initialtimestamp');
        $mappedFields = array();
        if($collid){
            $fieldNameArr[] = 'collid';
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    if($field === 'language' || $field === 'owner'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                    if($fieldMapping){
                        $mappedFieldVal = null;
                        $mappedKey = $fieldMapping[(string)$field] ?? null;
                        if(($mappedKey && (string)$mappedKey !== 'unmapped') || (string)$mappedKey === '0'){
                            $mappedFieldVal = (string)$mappedKey;
                        }
                        $mappedFields[$field] = $mappedFieldVal;
                    }
                    elseif(array_key_exists($field, $data[0])){
                        $mappedFields[$field] = $field;
                    }
                }
            }
            foreach($data as $dataArr){
                $dataValueArr = array();
                $mediaData = array();
                $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $collid, $this->fields['collid']);
                foreach($mappedFields as $field => $key){
                    $mediaData[$field] = ($key || (string)$key === '0') ? $dataArr[$key] : null;
                }
                $mediaData['sortsequence'] = 50;
                foreach($this->fields as $field => $fieldArr){
                    if(!in_array($field, $skipFields)){
                        $dataValue = $mediaData[$field] ?? null;
                        $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, SanitizerService::cleanInStr($this->conn, $dataValue), $fieldArr);
                    }
                }
                $valueArr[] = '(' . implode(',', $dataValueArr) . ')';
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT INTO uploadmediatemp(' . implode(',', $fieldNameArr) . ') '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function cleanMediaRecordFormatValues($collid): int
    {
        $returnVal = 1;
        if($collid){
            $sql = 'UPDATE uploadmediatemp SET format = NULL '.
                'WHERE collid = ' . (int)$collid . ' AND format IS NOT NULL '.
                'AND format NOT IN("image/jpeg", "image/png", "application/zc", "video/mp4", "video/webm", "video/ogg", "audio/wav", "audio/mpeg") ';
            if(!$this->conn->query($sql)){
                $returnVal = 0;
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "image/jpeg" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.jpeg" OR url LIKE "%.jpg" OR accessuri LIKE "%.jpeg" OR accessuri LIKE "%.jpg") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "image/png" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.png" OR accessuri LIKE "%.png") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "application/zc" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.zc" OR accessuri LIKE "%.zc") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "video/mp4" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.mp4" OR accessuri LIKE "%.mp4") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "video/webm" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.webm" OR accessuri LIKE "%.webm") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "video/ogg" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.ogg" OR accessuri LIKE "%.ogg") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "audio/wav" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.wav" OR accessuri LIKE "%.wav") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET format = "audio/mpeg" '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(format) '.
                    'AND (url LIKE "%.mp3" OR accessuri LIKE "%.mp3") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function cleanMediaRecords($collid): int
    {
        $returnVal = 1;
        if($collid){
            $sql = 'UPDATE uploadmediatemp SET url = NULL '.
                'WHERE collid = ' . (int)$collid . ' AND (url = "" OR url = "empty") ';
            if(!$this->conn->query($sql)){
                $returnVal = 0;
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET accessuri = NULL '.
                    'WHERE collid = ' . (int)$collid . ' AND (accessuri = "" OR accessuri = "empty") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET url = accessuri, accessuri = NULL '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(url) AND accessuri IS NOT NULL '.
                    'AND format IS NOT NULL AND (format = "image/jpeg" OR format = "image/png") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET accessuri = url, url = NULL '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(accessuri) AND url IS NOT NULL '.
                    'AND format IS NOT NULL AND format <> "image/jpeg" AND format <> "image/png" ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET url = originalurl '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(url) AND originalurl IS NOT NULL '.
                    'AND format IS NOT NULL AND (format = "image/jpeg" OR format = "image/png") ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'DELETE FROM uploadmediatemp WHERE collid = ' . (int)$collid . ' AND ISNULL(url) AND ISNULL(accessuri) ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET sourceurl = url '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(sourceurl) AND url IS NOT NULL ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadmediatemp SET sourceurl = accessuri '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(sourceurl) AND accessuri IS NOT NULL ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function cleanMediaRecordTidValues($collid): int
    {
        $returnVal = 1;
        if($collid){
            $sql = 'UPDATE uploadmediatemp AS m LEFT JOIN omoccurrences AS o ON m.occid = o.occid '.
                'SET m.tid = o.tid '.
                'WHERE m.collid = ' . (int)$collid . ' AND o.tid IS NOT NULL ';
            if(!$this->conn->query($sql)){
                $returnVal = 0;
            }
        }
        return $returnVal;
    }

    public function clearCollectionData($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE FROM uploadmediatemp WHERE collid = ' . (int)$collid . ' LIMIT 50000 ';
            if($this->conn->query($sql)){
                $returnVal = $this->conn->affected_rows;
            }
        }
        return $returnVal;
    }

    public function clearOrphanedRecords($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE FROM uploadmediatemp WHERE dbpk NOT IN(SELECT dbpk FROM uploadspectemp '.
                'WHERE collid = ' . (int)$collid . ') LIMIT 25000 ';
            if($this->conn->query($sql)){
                $returnVal = $this->conn->affected_rows;
            }
        }
        return $returnVal;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getUploadCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(upmid) AS cnt FROM uploadmediatemp WHERE collid  = ' . (int)$collid . ' ';
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    $returnVal = (int)$row['cnt'];
                }
            }
        }
        return $returnVal;
    }

    public function populateOccidFromUploadOccurrenceData($collid): void
    {
        if($collid){
            $sql = 'UPDATE uploadmediatemp AS u LEFT JOIN uploadspectemp AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'SET u.occid = o.occid '.
                'WHERE u.collid  = ' . (int)$collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            $this->conn->query($sql);
        }
    }

    public function removeExistingMediaDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE u.* FROM uploadmediatemp AS u LEFT JOIN images AS i ON u.occid = i.occid '.
                'WHERE u.collid  = ' . $collid . ' AND i.occid IS NOT NULL AND (u.url = i.url OR u.originalurl = i.originalurl OR u.sourceurl = i.sourceurl) ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }

            if($returnVal === 1){
                $sql = 'DELETE u.* FROM uploadmediatemp AS u LEFT JOIN media AS m ON u.occid = m.occid '.
                    'WHERE u.collid  = ' . $collid . ' AND m.occid IS NOT NULL AND u.accessuri = m.accessuri ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function removeExistingOccurrenceDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE FROM uploadmediatemp AS u WHERE u.collid  = ' . $collid . ' AND u.dbpk IS NOT NULL '.
                'AND u.dbpk IN(SELECT dbpk FROM omoccurrences WHERE collid = ' . $collid . ')  LIMIT 50000 ';
            if($this->conn->query($sql)){
                $returnVal = $this->conn->affected_rows;
            }
        }
        return $returnVal;
    }
}
