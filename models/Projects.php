<?php
include_once(__DIR__ . '/Checklists.php');
include_once(__DIR__ . '/Permissions.php');
include_once(__DIR__ . '/../services/DbService.php');

class Projects{

	private $conn;

    private $fields = array(
        'pid' => array('dataType' => 'number', 'length' => 10),
        'projname' => array('dataType' => 'string', 'length' => 45),
        'displayname' => array('dataType' => 'string', 'length' => 150),
        'managers' => array('dataType' => 'string', 'length' => 150),
        'briefdescription' => array('dataType' => 'string', 'length' => 300),
        'fulldescription' => array('dataType' => 'string', 'length' => 5000),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'iconurl' => array('dataType' => 'string', 'length' => 150),
        'headerurl' => array('dataType' => 'string', 'length' => 150),
        'occurrencesearch' => array('dataType' => 'number', 'length' => 10),
        'ispublic' => array('dataType' => 'number', 'length' => 10),
        'dynamicproperties' => array('dataType' => 'json', 'length' => 0),
        'parentpid' => array('dataType' => 'number', 'length' => 10),
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

    public function createProjectRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'pid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                if($field === 'dynamicproperties'){
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, json_encode($data[$field]), $fieldArr['dataType']);
                }
                else{
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO fmprojects(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
            (new Permissions)->addPermission($GLOBALS['SYMB_UID'], 'ProjAdmin', $newID);
            (new Permissions)->setUserPermissions();
        }
        return $newID;
    }

    public function deleteProjectRecord($pid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM userroles WHERE role = "ProjAdmin" AND tablepk = ' . (int)$pid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM fmchklstprojlink WHERE pid = ' . (int)$pid . ' ';
        //echo $sql;
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM fmprojects WHERE pid = ' . (int)$pid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getProjectArr(): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM fmprojects ';
        $sql .= 'ORDER BY projname ';
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

    public function getProjectChecklists($pid): array
    {
        $retArr = array();
        $sql = 'SELECT c.clid, c.`name` '.
            'FROM fmchklstprojlink AS p LEFT JOIN fmchecklists AS c ON p.clid = c.clid '.
            'WHERE p.pid = ' . (int)$pid . ' ';
        //echo '<div>'.$sql.'</div>';
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

    public function getProjectData($pid): array
    {
        $retArr = array();
        $clidArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM fmprojects WHERE pid = ' . (int)$pid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    if($row[$name] && ($name === 'dynamicproperties')){
                        $retArr[$name] = json_decode($row[$name], true);
                    }
                    else{
                        $retArr[$name] = $row[$name];
                    }
                }
            }
            $retArr['checklists'] = $this->getProjectChecklists($pid);
            foreach($retArr['checklists'] as $checklistArr) {
                $clidArr[] = $checklistArr['clid'];
            }
            $childClidArr = (new Checklists)->getChecklistChildClidArr($clidArr);
            $retArr['clidArr'] = array_unique(array_merge($childClidArr, $clidArr));
        }
        return $retArr;
    }

    public function getProjectListByUid($uid): array
    {
        $retArr = array();
        if((int)$uid > 0){
            $sql = 'SELECT DISTINCT p.pid, p.projname '.
                'FROM userroles AS r LEFT JOIN fmprojects AS p ON r.tablepk = p.pid '.
                'WHERE r.uid = ' . (int)$uid . ' AND r.role = "ProjAdmin" '.
                'ORDER BY p.projname ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    $nodeArr['pid'] = $row['pid'];
                    $nodeArr['projname'] = $row['projname'];
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getProjectPermissionLabels($permissionArr): array
    {
        $idStr = implode(',', array_keys($permissionArr));
        $sql = 'SELECT pid, projname FROM fmprojects WHERE pid IN(' . $idStr . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $permissionArr[$row['pid']] = $row['projname'];
                unset($rows[$index]);
            }
        }
        return $permissionArr;
    }

    public function updateProjectRecord($pid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($pid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'pid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    if($field === 'dynamicproperties'){
                        $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, json_encode($editData[$field]), $fieldArr['dataType']);
                    }
                    else{
                        $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                    }
                }
            }
            $sql = 'UPDATE fmprojects SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE pid = ' . (int)$pid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
