<?php
include_once(__DIR__ . '/ChecklistTaxa.php');
include_once(__DIR__ . '/Permissions.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Checklists{

	private $conn;

    private $fields = array(
        'clid' => array('dataType' => 'number', 'length' => 10),
        'name' => array('dataType' => 'string', 'length' => 100),
        'title' => array('dataType' => 'string', 'length' => 150),
        'locality' => array('dataType' => 'string', 'length' => 500),
        'publication' => array('dataType' => 'string', 'length' => 500),
        'abstract' => array('dataType' => 'text', 'length' => 0),
        'authors' => array('dataType' => 'string', 'length' => 250),
        'type' => array('dataType' => 'string', 'length' => 50),
        'politicaldivision' => array('dataType' => 'string', 'length' => 45),
        'searchterms' => array('dataType' => 'json', 'length' => 0),
        'parent' => array('dataType' => 'string', 'length' => 50),
        'parentclid' => array('dataType' => 'number', 'length' => 10),
        'notes' => array('dataType' => 'string', 'length' => 500),
        'latcentroid' => array('dataType' => 'number', 'length' => 9),
        'longcentroid' => array('dataType' => 'number', 'length' => 9),
        'pointradiusmeters' => array('dataType' => 'number', 'length' => 10),
        'footprintwkt' => array('dataType' => 'text', 'length' => 0),
        'percenteffort' => array('dataType' => 'number', 'length' => 11),
        'access' => array('dataType' => 'string', 'length' => 45),
        'defaultsettings' => array('dataType' => 'json', 'length' => 250),
        'iconurl' => array('dataType' => 'string', 'length' => 150),
        'headerurl' => array('dataType' => 'string', 'length' => 150),
        'uid' => array('dataType' => 'number', 'length' => 10),
        'sortsequence' => array('dataType' => 'number', 'length' => 10),
        'expiration' => array('dataType' => 'timestamp', 'length' => 0),
        'datelastmodified' => array('dataType' => 'date', 'length' => 0),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
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

    public function createChecklistRecord($data): int
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
                if($field === 'defaultsettings' || $field === 'searchterms'){
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, json_encode($data[$field]), $fieldArr['dataType']);
                }
                else{
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
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
            (new Permissions)->addPermission($GLOBALS['SYMB_UID'], 'ClAdmin', $newID);
            (new Permissions)->setUserPermissions();
        }
        return $newID;
    }

    public function createTemporaryChecklistFromTidArr($tidArr): int
    {
        $this->clearExpiredChecklists();
        $newID = 0;
        if(count($tidArr) > 0){
            $guid = UuidService::getUuidV4();
            $fieldNameArr = array();
            $fieldValueArr = array();
            $fieldNameArr[] = '`name`';
            $fieldValueArr[] = '"' . $guid . '"';
            $fieldNameArr[] = 'expiration';
            $fieldValueArr[] = '"' . date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 7, date('Y'))) . '"';
            $fieldNameArr[] = 'uid';
            $fieldValueArr[] = $GLOBALS['SYMB_UID'] ?: 'NULL';
            $fieldNameArr[] = 'initialtimestamp';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $sql = 'INSERT INTO fmchecklists(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                if(!(new ChecklistTaxa)->batchCreateChecklistTaxaRecordsFromTidArr($newID, $tidArr)){
                    $newID = 0;
                }
            }
        }
        return $newID;
    }

    public function deleteChecklistRecord($clid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM userroles WHERE role = "ClAdmin" AND tablepk = ' . (int)$clid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
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

    public function getChecklistArr(): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM fmchecklists ';
        $sql .= 'ORDER BY `name` ';
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
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getChecklistChildClidArr($clidArr): array
    {
        $retArr = array();
        $sql = 'SELECT clidchild FROM fmchklstchildren WHERE clid IN(' . implode(',', $clidArr) . ') ';
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
                    if($row[$name] && ($name === 'defaultsettings' || $name === 'searchterms')){
                        $retArr[$name] = json_decode($row[$name], true);
                    }
                    else{
                        $retArr[$name] = $row[$name];
                    }
                }
                $clidArr = $this->getChecklistChildClidArr(array($clid));
                $clidArr[] = $clid;
                $retArr['clidArr'] = $clidArr;
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

    public function getChecklistListByUid($uid): array
    {
        $retArr = array();
        if((int)$uid > 0){
            $sql = 'SELECT DISTINCT c.clid, c.name '.
                'FROM userroles AS r LEFT JOIN fmchecklists AS c ON r.tablepk = c.clid '.
                'WHERE r.uid = ' . (int)$uid . ' AND r.role = "ClAdmin" '.
                'ORDER BY c.name ';
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

    public function saveTemporaryChecklist($clid, $searchTerms): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($clid){
            $sqlPartArr[] = 'expiration = NULL';
            $sqlPartArr[] = 'datelastmodified = "' . date('Y-m-d H:i:s') . '"';
            if($searchTerms){
                $sqlPartArr[] = 'searchterms = ' . SanitizerService::getSqlValueString($this->conn, json_encode($searchTerms), $this->fields['searchterms']['dataType']);
            }
            $sql = 'UPDATE fmchecklists SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE clid = ' . (int)$clid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
                (new Permissions)->addPermission($GLOBALS['SYMB_UID'], 'ClAdmin', $clid);
                (new Permissions)->setUserPermissions();
            }
        }
        return $retVal;
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
                    if($field === 'defaultsettings' || $field === 'searchterms'){
                        $sqlPartArr[] = $fieldName . ' = ' . SanitizerService::getSqlValueString($this->conn, json_encode($editData[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $sqlPartArr[] = $fieldName . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                    }
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

    public function getChecklistPermissionLabels($permissionArr): array
    {
        $idStr = implode(',', array_keys($permissionArr));
        $sql = 'SELECT clid, `name` FROM fmchecklists WHERE clid IN(' . $idStr . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $permissionArr[$row['clid']] = $row['name'];
                unset($rows[$index]);
            }
        }
        return $permissionArr;
    }
}
