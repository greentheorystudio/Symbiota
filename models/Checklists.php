<?php
include_once(__DIR__ . '/../services/DbService.php');

class Checklists{

	private $conn;

    private $fields = array(
        "clid" => array("dataType" => "number", "length" => 10),
        "name" => array("dataType" => "string", "length" => 100),
        "title" => array("dataType" => "string", "length" => 150),
        "locality" => array("dataType" => "string", "length" => 500),
        "publication" => array("dataType" => "string", "length" => 500),
        "abstract" => array("dataType" => "text", "length" => 0),
        "authors" => array("dataType" => "string", "length" => 250),
        "type" => array("dataType" => "string", "length" => 50),
        "politicaldivision" => array("dataType" => "string", "length" => 45),
        "searchterms" => array("dataType" => "text", "length" => 0),
        "parent" => array("dataType" => "string", "length" => 50),
        "parentclid" => array("dataType" => "number", "length" => 10),
        "notes" => array("dataType" => "string", "length" => 500),
        "latcentroid" => array("dataType" => "number", "length" => 9),
        "longcentroid" => array("dataType" => "number", "length" => 9),
        "pointradiusmeters" => array("dataType" => "number", "length" => 10),
        "footprintwkt" => array("dataType" => "text", "length" => 0),
        "percenteffort" => array("dataType" => "number", "length" => 11),
        "access" => array("dataType" => "string", "length" => 45),
        "defaultsettings" => array("dataType" => "string", "length" => 250),
        "iconurl" => array("dataType" => "string", "length" => 150),
        "headerurl" => array("dataType" => "string", "length" => 150),
        "uid" => array("dataType" => "number", "length" => 10),
        "sortsequence" => array("dataType" => "number", "length" => 10),
        "expiration" => array("dataType" => "timestamp", "length" => 0),
        "datelastmodified" => array("dataType" => "date", "length" => 0),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function clearExpiredChecklists(): void
    {
        $sql1 = 'DELETE l.* FROM fmchecklists AS c LEFT JOIN fmchklsttaxalink AS l ON c.clid = l.clid '.
            'WHERE c.expiration IS NOT NULL AND c.expiration < NOW()';
        $this->conn->query($sql1);
        $sql2 = 'DELETE FROM fmchecklists WHERE expiration < NOW()';
        $this->conn->query($sql2);
    }

    public function createChecklistRecord($data, $dynamic = false): int
    {
        $this->clearExpiredChecklists();
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'clid' && $field !== 'uid' && $field !== 'expiration' && $field !== 'datelastmodified' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                if($field === 'name'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        if($dynamic){
            $fieldNameArr[] = 'expiration';
            $fieldValueArr[] = '"' . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 7, date('Y'))) . '"';
        }
        $fieldNameArr[] = 'uid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'] ?: 'NULL';
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO fmchecklists(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteChecklistRecord($clid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM fmchklstprojlink WHERE clid = ' . (int)$clid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM fmvouchers WHERE clid = ' . (int)$clid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM fmchklsttaxalink WHERE clid = ' . (int)$clid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM fmchecklists WHERE clid = ' . (int)$clid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getChecklistChildClidArr($clid): array
    {
        $retArr = array();
        $sql = 'SELECT clidchild FROM fmchklstchildren WHERE clid = ' . (int)$clid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = $row['clidchild'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getChecklistData($clid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM fmchecklists WHERE clid = ' . (int)$clid . ' ';
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

    public function getChecklistFromClid($clid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM fmchecklists WHERE clid = ' . (int)$clid . ' ';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getChecklistListByUserRights(): array
    {
        $retArr = array();
        $cArr = array();
        if(array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])) {
            $cArr = $GLOBALS['USER_RIGHTS']['ClAdmin'];
        }
        if($cArr){
            $sql = 'SELECT clid, name FROM fmchecklists WHERE clid IN('.implode(',', $cArr).') ORDER BY name ';
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
        }
        return $retArr;
    }

    public function updateChecklistRecord($clid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($clid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'clid' && $field !== 'uid' && $field !== 'expiration' && $field !== 'datelastmodified' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    if($field === 'name'){
                        $fieldName = '`' . $field . '`';
                    }
                    else{
                        $fieldName = $field;
                    }
                    $sqlPartArr[] = $fieldName . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sqlPartArr[] = 'datelastmodified = "' . date('Y-m-d H:i:s') . '"';
            $sql = 'UPDATE fmchecklists SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE clid = ' . (int)$clid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
