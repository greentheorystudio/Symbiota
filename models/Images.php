<?php
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/FileSystemService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Images{

	private $conn;

    private $fields = array(
        "imgid" => array("dataType" => "number", "length" => 10),
        "tid" => array("dataType" => "number", "length" => 10),
        "url" => array("dataType" => "string", "length" => 255),
        "thumbnailurl" => array("dataType" => "string", "length" => 255),
        "originalurl" => array("dataType" => "string", "length" => 255),
        "photographer" => array("dataType" => "string", "length" => 100),
        "photographeruid" => array("dataType" => "number", "length" => 10),
        "format" => array("dataType" => "string", "length" => 45),
        "caption" => array("dataType" => "string", "length" => 750),
        "owner" => array("dataType" => "string", "length" => 250),
        "sourceurl" => array("dataType" => "string", "length" => 255),
        "referenceurl" => array("dataType" => "string", "length" => 255),
        "copyright" => array("dataType" => "string", "length" => 255),
        "rights" => array("dataType" => "string", "length" => 255),
        "locality" => array("dataType" => "string", "length" => 250),
        "occid" => array("dataType" => "number", "length" => 10),
        "notes" => array("dataType" => "string", "length" => 350),
        "anatomy" => array("dataType" => "string", "length" => 100),
        "username" => array("dataType" => "string", "length" => 45),
        "sourceidentifier" => array("dataType" => "string", "length" => 150),
        "mediamd5" => array("dataType" => "string", "length" => 45),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
        "sortsequence" => array("dataType" => "number", "length" => 10),
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

    public function addImageTag($imgid, $tag): int
    {
        $retVal = 0;
        if($imgid && $tag){
            $tagValue = SanitizerService::cleanInStr($this->conn, $tag);
            $sql = 'INSERT INTO imagetag(imgid, keyvalue) VALUES('.
                (int)$imgid . ', "' . $tagValue . '")';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function createImageRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'imgid' && $field !== 'tagArr' && array_key_exists($field, $data)){
                if($field === 'owner'){
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
        $sql = 'INSERT INTO images(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
            $guid = UuidService::getUuidV4();
            $this->conn->query('INSERT INTO guidimages(guid, imgid) VALUES("' . $guid . '",' . $newID . ')');
            if(array_key_exists('tagArr', $data) && is_array($data['tagArr']) && count($data['tagArr']) > 0){
                foreach($data['tagArr'] as $tag){
                    $this->addImageTag($newID, $tag);
                }
            }
        }
        return $newID;
    }

    public function deleteImageRecord($imgid): int
    {
        $retVal = 1;
        $data = $this->getImageData($imgid);
        if($data['url'] && strpos($data['url'], '/') === 0){
            $urlServerPath = FileSystemService::getServerPathFromUrlPath($data['url']);
            FileSystemService::deleteFile($urlServerPath, true);
        }
        if($data['thumbnailurl'] && strpos($data['thumbnailurl'], '/') === 0){
            $tnServerPath = FileSystemService::getServerPathFromUrlPath($data['thumbnailurl']);
            FileSystemService::deleteFile($tnServerPath, true);
        }
        if($data['originalurl'] && strpos($data['originalurl'], '/') === 0){
            $origServerPath = FileSystemService::getServerPathFromUrlPath($data['originalurl']);
            FileSystemService::deleteFile($origServerPath, true);
        }
        $sql = 'DELETE FROM imagetag WHERE imgid = ' . (int)$imgid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM guidimages WHERE imgid = ' . (int)$imgid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM images WHERE imgid = ' . (int)$imgid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function deleteImageTag($imgid, $tag): int
    {
        $retVal = 1;
        if($imgid && $tag){
            $sql = 'DELETE FROM imagetag WHERE imgid = ' . (int)$imgid . ' AND keyvalue = "' . SanitizerService::cleanInStr($this->conn, $tag) . '" ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function getImageArrByProperty($property, $value, $includeOccurrence = false, $limit = null): array
    {
        $returnArr = array();
        if($property === 'occid' || $property === 'tid'){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM images WHERE ' . $property . ' = ' . (int)$value . ' ';
            if($property === 'tid' && !$includeOccurrence){
                $sql .= 'AND ISNULL(occid) ';
            }
            $sql .= 'ORDER BY sortsequence ';
            if($limit){
                $sql .= 'LIMIT ' . (int)$limit . ' ';
            }
            //echo '<div>'.$sql.'</div>';
            if($rs = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($rs);
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $r->$name;
                    }
                    $nodeArr['tagArr'] = $this->getImageTags($r->imgid);
                    $returnArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $returnArr;
    }

    public function getImageArrByTaxonomicGroup($parentTid, $includeOccurrence = false, $limit = null): array
    {
        $returnArr = array();
        if($parentTid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM images WHERE tid = ' . (int)$parentTid . ' OR tid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . (int)$parentTid . ') ';
            if(!$includeOccurrence){
                $sql .= 'AND ISNULL(occid) ';
            }
            $sql .= 'ORDER BY sortsequence ';
            if($limit){
                $sql .= 'LIMIT ' . (int)$limit . ' ';
            }
            //echo '<div>'.$sql.'</div>';
            if($rs = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($rs);
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $r->$name;
                    }
                    $returnArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $returnArr;
    }

    public function getImageData($imgid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM images WHERE imgid = ' . (int)$imgid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
                $retArr['tagArr'] = $this->getImageTags($r->imgid);
                $retArr['taxonData'] = (int)$retArr['tid'] > 0 ? (new Taxa)->getTaxonFromTid($retArr['tid']) : null;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getImageTags($imgid): array
    {
        $retArr = array();
        $sql = 'SELECT keyvalue FROM imagetag WHERE imgid = ' . (int)$imgid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[] = $r->keyvalue;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function updateImageRecord($imgId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($imgId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'imgid' && $field !== 'tagArr' && array_key_exists($field, $editData)){
                    if($field === 'owner'){
                        $fieldName = '`' . $field . '`';
                    }
                    else{
                        $fieldName = $field;
                    }
                    $sqlPartArr[] = $fieldName . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            if(count($sqlPartArr) > 0){
                $sql = 'UPDATE images SET ' . implode(', ', $sqlPartArr) . ' '.
                    'WHERE imgid = ' . (int)$imgId . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
            }
            if(array_key_exists('tagArr', $editData) && is_array($editData['tagArr']) && count($editData['tagArr']) > 0){
                foreach($editData['tagArr'] as $tag){
                    if($this->addImageTag($imgId, $tag)){
                        $retVal = 1;
                    }
                }
            }
        }
        return $retVal;
    }

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE images SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
