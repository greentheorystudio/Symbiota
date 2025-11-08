<?php
include_once(__DIR__ . '/TaxonHierarchy.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/FileSystemService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonMaps{

	private $conn;

    private $fields = array(
        'mid' => array('dataType' => 'number', 'length' => 10),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'url' => array('dataType' => 'string', 'length' => 255),
        'title' => array('dataType' => 'string', 'length' => 100),
        'alttext' => array('dataType' => 'string', 'length' => 355),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createTaxonMapRecord($imgFile, $uploadPath, $data): int
    {
        $newID = 0;
        if($imgFile && $uploadPath && $data){
            $origFilename = $imgFile['name'];
            if(strtolower(substr($origFilename, -4)) === '.jpg' || strtolower(substr($origFilename, -5)) === '.jpeg' || strtolower(substr($origFilename, -4)) === '.png'){
                $targetPath = FileSystemService::getServerMediaUploadPath($uploadPath);
                if($targetPath && $origFilename) {
                    $targetFilename = FileSystemService::getServerUploadFilename($targetPath, $origFilename);
                    if($targetFilename && FileSystemService::moveUploadedFileToServer($imgFile, $targetPath, $targetFilename)){
                        $data['url'] = FileSystemService::getUrlPathFromServerPath($targetPath . '/' . $targetFilename);
                    }
                }
            }
            $fieldNameArr = array();
            $fieldValueArr = array();
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'mid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'initialtimestamp';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $sql = 'INSERT INTO taxamaps(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function deleteTaxonMapRecord($idType, $id): int
    {
        $retVal = 1;
        $whereStr = '';
        $data = $this->getTaxonMapData($idType, $id);
        if($idType === 'mid'){
            $whereStr = 'mid = ' . (int)$id . ' ';
        }
        elseif($idType === 'tid'){
            $whereStr = 'tid = ' . (int)$id . ' ';
        }
        if($data && $data['url'] && strncmp($data['url'], '/', 1) === 0){
            $urlServerPath = FileSystemService::getServerPathFromUrlPath($data['url']);
            FileSystemService::deleteFile($urlServerPath, true);
        }
        $sql = 'DELETE FROM taxamaps WHERE ' . $whereStr . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getTaxonMapData($idType, $id): array
    {
        $retArr = array();
        $whereStr = '';
        if($idType === 'mid'){
            $whereStr = 'mid = ' . (int)$id . ' ';
        }
        elseif($idType === 'tid'){
            $whereStr = 'tid = ' . (int)$id . ' ';
        }
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxamaps WHERE ' . $whereStr . ' ';
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
            }
        }
        return $retArr;
    }

    public function getTaxonMaps($tid, $includeSubtaxa): array
    {
        $retArr = array();
        $tidArr = array();
        if($includeSubtaxa){
            $tidArr = (new TaxonHierarchy)->getSubtaxaTidArrFromTid($tid);
        }
        $tidArr[] = $tid;
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxamaps WHERE tid IN(' . implode(',', $tidArr) . ') ';
        //echo $sql;
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
                $retArr[$row['tid']] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function updateTaxonMapRecord($mid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($mid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'mid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE taxamaps SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE mid = ' . (int)$mid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
