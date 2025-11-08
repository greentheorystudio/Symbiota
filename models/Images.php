<?php
include_once(__DIR__ . '/Permissions.php');
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/FileSystemService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Images{

	private $conn;

    private $fields = array(
        'imgid' => array('dataType' => 'number', 'length' => 10),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'url' => array('dataType' => 'string', 'length' => 255),
        'thumbnailurl' => array('dataType' => 'string', 'length' => 255),
        'originalurl' => array('dataType' => 'string', 'length' => 255),
        'photographer' => array('dataType' => 'string', 'length' => 100),
        'photographeruid' => array('dataType' => 'number', 'length' => 10),
        'format' => array('dataType' => 'string', 'length' => 45),
        'caption' => array('dataType' => 'string', 'length' => 750),
        'alttext' => array('dataType' => 'string', 'length' => 355),
        'owner' => array('dataType' => 'string', 'length' => 250),
        'sourceurl' => array('dataType' => 'string', 'length' => 255),
        'referenceurl' => array('dataType' => 'string', 'length' => 255),
        'copyright' => array('dataType' => 'string', 'length' => 255),
        'rights' => array('dataType' => 'string', 'length' => 255),
        'locality' => array('dataType' => 'string', 'length' => 250),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'notes' => array('dataType' => 'string', 'length' => 350),
        'anatomy' => array('dataType' => 'string', 'length' => 100),
        'username' => array('dataType' => 'string', 'length' => 45),
        'sourceidentifier' => array('dataType' => 'string', 'length' => 150),
        'mediamd5' => array('dataType' => 'string', 'length' => 45),
        'dynamicproperties' => array('dataType' => 'text', 'length' => 0),
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

    public function batchCreateOccurrenceImageRecordGUIDs($collid): int
    {
        $returnVal = 1;
        $valueArr = array();
        $insertPrefix = 'INSERT INTO guidimages(guid, imgid) VALUES ';
        $sql = 'SELECT i.imgid FROM images AS i LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
            'WHERE o.collid = ' . (int)$collid . ' AND i.imgid NOT IN(SELECT imgid FROM guidimages) ';
        if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $row){
                if($returnVal){
                    if(count($valueArr) === 5000){
                        $sql2 = $insertPrefix . implode(',', $valueArr);
                        if(!$this->conn->query($sql2)){
                            $returnVal = 0;
                        }
                        $valueArr = array();
                    }
                    if($row['imgid']){
                        $guid = UuidService::getUuidV4();
                        $valueArr[] = '("' . $guid . '",' . $row['imgid'] . ')';
                    }
                }
            }
            if($returnVal && count($valueArr) > 0){
                $sql2 = $insertPrefix . implode(',', $valueArr);
                $this->conn->query($sql2);
            }
        }
        return $returnVal;
    }

    public function clearExistingImagesNotInUpload($collid, $clearDerivatives): int
    {
        $retVal = 1;
        $imgIdArr = array();
        $sql = 'SELECT DISTINCT i.imgid FROM images AS i LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
            'LEFT JOIN uploadmediatemp AS um ON i.occid = um.occid AND i.url = um.url '.
            'WHERE o.collid = ' . (int)$collid . ' AND i.url NOT IN(SELECT DISTINCT url FROM uploadmediatemp WHERE collid = ' . (int)$collid . ') ';
        if($result = $this->conn->query($sql)){
            while(($row = $result->fetch_assoc()) && $retVal){
                $imgIdArr[] = $row['imgid'];
                if(count($imgIdArr) === 10000){
                    $retVal = $this->clearImagesByArr($imgIdArr, $clearDerivatives);
                    $imgIdArr = array();
                }
            }
            $result->free();
            if(count($imgIdArr) > 0){
                $retVal = $this->clearImagesByArr($imgIdArr, $clearDerivatives);
            }
        }
        return $retVal;
    }

    public function clearImagesByArr($imgIdArr, $clearDerivatives): int
    {
        $retVal = 0;
        if($clearDerivatives){
            $this->deleteAssociatedImageFiles('imgidArr', $imgIdArr);
        }
        $sql = 'DELETE t.* FROM imagetag AS t WHERE t.imgid IN(' . implode(',', $imgIdArr) . ') ';
        if($this->conn->query($sql)){
            $retVal = 1;
        }
        if($retVal){
            $sql = 'DELETE g.* FROM guidimages AS g WHERE g.imgid IN(' . implode(',', $imgIdArr) . ') ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        if($retVal){
            $sql = 'DELETE i.* FROM images AS i WHERE i.imgid IN(' . implode(',', $imgIdArr) . ') ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
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

    public function createImageRecordsFromUploadData($collId): int
    {
        $skipFields = array('imgid', 'photographeruid', 'mediamd5', 'dynamicproperties', 'username', 'initialtimestamp');
        $retVal = 1;
        $fieldNameArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    if($field === 'owner'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                }
            }
            if(count($fieldNameArr) > 0){
                $sql = 'INSERT INTO images(' . implode(',', $fieldNameArr) . ') '.
                    'SELECT ' . implode(',', $fieldNameArr) . ' FROM uploadmediatemp '.
                    'WHERE collid = ' . (int)$collId . ' AND occid IS NOT NULL AND url IS NOT NULL AND format IS NOT NULL ';
                //echo "<div>".$sql."</div>";
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
        }
        return $retVal;
    }

    public function deleteAssociatedImageFiles($idType, $id): void
    {
        $sql = '';
        if($idType === 'occid'){
            $sql = 'SELECT url, thumbnailurl, originalurl FROM images WHERE occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $sql = 'SELECT url, thumbnailurl, originalurl FROM images WHERE occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'imgidArr'){
            $sql = 'SELECT url, thumbnailurl, originalurl FROM images WHERE imgid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $sql = 'SELECT i.url, i.thumbnailurl, i.originalurl FROM images AS i LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
                'WHERE o.collid = ' . (int)$id . ' ';
        }
        elseif($idType === 'tid'){
            $sql = 'SELECT url, thumbnailurl, originalurl FROM images WHERE tid = ' . (int)$id . ' AND ISNULL(occid) ';
        }
        //echo '<div>'.$sql.'</div>';
        if($sql && $result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if($row['url'] && strncmp($row['url'], '/', 1) === 0){
                    FileSystemService::deleteFile(FileSystemService::getServerPathFromUrlPath($row['url']), true);
                }
                if($row['thumbnailurl'] && strncmp($row['thumbnailurl'], '/', 1) === 0){
                    FileSystemService::deleteFile(FileSystemService::getServerPathFromUrlPath($row['thumbnailurl']), true);
                }
                if($row['originalurl'] && strncmp($row['originalurl'], '/', 1) === 0){
                    FileSystemService::deleteFile(FileSystemService::getServerPathFromUrlPath($row['originalurl']), true);
                }
                unset($rows[$index]);
            }
        }
    }

    public function deleteAssociatedImageRecords($idType, $id): int
    {
        $this->deleteAssociatedImageFiles($idType, $id);
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'i.occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'i.occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $whereStr = 'i.occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ') ';
        }
        elseif($idType === 'tid'){
            $whereStr = 'i.tid = ' . (int)$id . ' AND ISNULL(i.occid) ';
        }
        if($whereStr){
            $sql = 'DELETE t.* FROM imagetag AS t LEFT JOIN images AS i ON t.imgid = i.imgid WHERE ' . $whereStr . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
            if($retVal){
                $sql = 'DELETE g.* FROM guidimages AS g LEFT JOIN images AS i ON g.imgid = i.imgid WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE i.* FROM images AS i WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
        }
        return $retVal;
    }

    public function deleteChecklistTaxonImageTags($clid, $tid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM imagetag WHERE keyvalue = "CLID-' . (int)$clid . '-' . (int)$tid . '" ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function deleteImageRecord($imgid): int
    {
        $retVal = 1;
        $data = $this->getImageData($imgid);
        if($data['url'] && strncmp($data['url'], '/', 1) === 0){
            $urlServerPath = FileSystemService::getServerPathFromUrlPath($data['url']);
            FileSystemService::deleteFile($urlServerPath, true);
        }
        if($data['thumbnailurl'] && strncmp($data['thumbnailurl'], '/', 1) === 0){
            $tnServerPath = FileSystemService::getServerPathFromUrlPath($data['thumbnailurl']);
            FileSystemService::deleteFile($tnServerPath, true);
        }
        if($data['originalurl'] && strncmp($data['originalurl'], '/', 1) === 0){
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

    public function deleteTaxonImageTags($tid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM imagetag WHERE keyvalue = "TID-' . (int)$tid . '" ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getChecklistImageData($tidArr, $taxonLimit): array
    {
        $retArr = array();
        if(count($tidArr) > 0){
            $retArr = $this->getChecklistImageDataBatch($retArr, $tidArr, 'tidaccepted', $taxonLimit);
            $queryTidArr = $this->getChecklistQueryTidArr($tidArr, $retArr, $taxonLimit);
            if(count($queryTidArr) > 0){
                $retArr = $this->getChecklistImageDataBatch($retArr, $queryTidArr, 'parenttid', $taxonLimit);
            }
        }
        return $retArr;
    }

    public function getChecklistImageDataBatch($retArr, $tidArr, $matchField, $taxonLimit): array
    {
        if(count($tidArr) > 0){
            $sql = 'SELECT t.' . $matchField . ', i.imgid, i.url, i.thumbnailurl, i.alttext '.
                'FROM images AS i LEFT JOIN taxa AS t ON i.tid = t.tid '.
                'WHERE t.' . $matchField . ' IN(' . implode(',', $tidArr) . ') AND i.sortsequence < 500 ORDER BY i.sortsequence ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if(!array_key_exists($row[$matchField], $retArr)){
                        $retArr[$row[$matchField]] = array();
                    }
                    if(count($retArr[$row[$matchField]]) < $taxonLimit){
                        $nodeArr = array();
                        $nodeArr['imgid'] = $row['imgid'];
                        $nodeArr['url'] = $row['url'];
                        $nodeArr['thumbnailurl'] = $row['thumbnailurl'];
                        $nodeArr['alttext'] = $row['alttext'];
                        $retArr[$row[$matchField]][] = $nodeArr;
                    }
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getChecklistQueryTidArr($tidAcceptedArr, $retArr, $taxonLimit): array
    {
        $queryTidArr = array();
        foreach($tidAcceptedArr as $tid){
            if(!array_key_exists($tid, $retArr) || count($retArr[$tid]) < $taxonLimit){
                $queryTidArr[] = $tid;
            }
        }
        return $queryTidArr;
    }

    public function getChecklistTaggedImageData($clidArr, $taxonLimit, $tidArr = null): array
    {
        $retArr = array();
        $sqlWhereArr = array();
        if(count($clidArr) > 0){
            if($tidArr){
                $keyValueArr = array();
                foreach($clidArr as $clid){
                    foreach($tidArr as $tid){
                        $keyValueArr[] = '"CLID-' . (int)$clid . '-' . (int)$tid . '"';
                        $keyValueArr[] = '"TID-' . (int)$tid . '"';
                    }
                }
                $sqlWhereArr[] = 't.keyvalue IN(' . implode(',', $keyValueArr)  . ')';
            }
            else{
                foreach($clidArr as $clid){
                    $sqlWhereArr[] = 't.keyvalue LIKE "CLID-' . (int)$clid . '-%"';
                }
            }
            $sql = 'SELECT i.imgid, i.url, i.thumbnailurl, i.alttext, t.keyvalue '.
                'FROM images AS i LEFT JOIN imagetag AS t ON i.imgid = t.imgid '.
                'WHERE ' . implode(' OR ', $sqlWhereArr) . ' '.
                'ORDER BY t.keyvalue ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $tid = '';
                    $tagArr = explode('-', $row['keyvalue']);
                    if($tagArr){
                        if(strncmp($row['keyvalue'], 'CLID-', 5) === 0){
                            $tid = $tagArr[2];
                        }
                        else{
                            $tid = $tagArr[1];
                        }
                    }
                    if($tid){
                        if(!array_key_exists($tid, $retArr)){
                            $retArr[$tid] = array();
                        }
                        if(count($retArr[$tid]) < $taxonLimit){
                            $nodeArr = array();
                            $nodeArr['imgid'] = $row['imgid'];
                            $nodeArr['url'] = $row['url'];
                            $nodeArr['thumbnailurl'] = $row['thumbnailurl'];
                            $nodeArr['alttext'] = $row['alttext'];
                            $retArr[$tid][] = $nodeArr;
                        }
                    }
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getImageArrByProperty($property, $value, $limit = null): array
    {
        $returnArr = array();
        if($property === 'occid' || $property === 'tid'){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'i');
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ', o.collid, o.localitysecurity '.
                'FROM images AS i LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
                'WHERE i.' . SanitizerService::cleanInStr($this->conn, $property) . ' = ' . (int)$value . ' ';
            $sql .= 'ORDER BY i.sortsequence ';
            if($limit){
                $sql .= 'LIMIT ' . (int)$limit . ' ';
            }
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
                        $nodeArr['tagArr'] = $this->getImageTags($row['imgid']);
                        $returnArr[] = $nodeArr;
                    }
                    unset($rows[$index]);
                }
            }
        }
        return $returnArr;
    }

    public function getImageArrByTagValue($value): array
    {
        $returnArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'i');
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM images AS i LEFT JOIN imagetag AS t ON i.imgid = t.imgid '.
            'WHERE t.keyvalue = "' . SanitizerService::cleanInStr($this->conn, $value) . '" ';
        $sql .= 'ORDER BY i.sortsequence ';
        //echo '<div>'.$sql.'</div>';
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
                $returnArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $returnArr;
    }

    public function getImageArrByTaxonomicGroup($parentTid, $includeOccurrence, $limit = null): array
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
                    $returnArr[] = $nodeArr;
                    unset($rows[$index]);
                }
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
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
                $retArr['tagArr'] = $this->getImageTags($row['imgid']);
                $retArr['taxonData'] = (int)$retArr['tid'] > 0 ? (new Taxa)->getTaxonFromTid($retArr['tid']) : null;
            }
        }
        return $retArr;
    }

    public function getImageTags($imgid): array
    {
        $retArr = array();
        $sql = 'SELECT keyvalue FROM imagetag WHERE imgid = ' . (int)$imgid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = $row['keyvalue'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxonArrDisplayImageData($tidArr, $includeOccurrence, $limitToOccurrence, $limitPerTaxon = null, $sortsequenceLimit = null): array
    {
        $returnArr = array();
        $returnArr['count'] = 0;
        if($tidArr && is_array($tidArr) && count($tidArr) > 0){
            $sql = 'SELECT DISTINCT i.imgid, t.tidaccepted AS tid, i.occid, i.url, i.thumbnailurl, i.originalurl, i.alttext, i.caption, i.photographer, i.owner, '.
                't.securitystatus, o.sciname, o.basisofrecord, o.catalognumber, o.othercatalognumbers '.
                'FROM images AS i LEFT JOIN taxa AS t ON i.tid = t.tid '.
                'LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
                'WHERE t.tidaccepted IN(' . implode(',', $tidArr) . ') ';
            if($limitToOccurrence){
                $sql .= 'AND i.occid IS NOT NULL ';
            }
            elseif(!$includeOccurrence){
                $sql .= 'AND ISNULL(i.occid) ';
            }
            if($sortsequenceLimit && (int)$sortsequenceLimit > 0){
                $sql .= 'AND i.sortsequence <= ' . (int)$sortsequenceLimit . ' ';
            }
            $sql .= 'ORDER BY i.sortsequence ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                if(count($tidArr) === 1){
                    $returnArr['count'] += $result->num_rows;
                }
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

            $sql = 'SELECT DISTINCT i.imgid, te.parenttid AS tid, i.occid, i.url, i.thumbnailurl, i.originalurl, i.alttext, i.caption, i.photographer, i.owner, '.
                't.securitystatus, o.sciname, o.basisofrecord, o.catalognumber, o.othercatalognumbers '.
                'FROM images AS i LEFT JOIN taxa AS t ON i.tid = t.tid '.
                'LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
                'LEFT JOIN taxaenumtree AS te ON t.tidaccepted = te.tid '.
                'WHERE te.parenttid IN(' . implode(',', $tidArr) . ') ';
            if(!$includeOccurrence){
                $sql .= 'AND ISNULL(i.occid) ';
            }
            if($sortsequenceLimit && (int)$sortsequenceLimit > 0){
                $sql .= 'AND i.sortsequence <= ' . (int)$sortsequenceLimit . ' ';
            }
            $sql .= 'ORDER BY i.sortsequence ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                if(count($tidArr) === 1){
                    $returnArr['count'] += $result->num_rows;
                }
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
