<?php
include_once(__DIR__ . '/../services/DbService.php');

class Projects{

	private $conn;

    private $fields = array(
        "pid" => array("dataType" => "number", "length" => 10),
        "projname" => array("dataType" => "string", "length" => 45),
        "displayname" => array("dataType" => "string", "length" => 150),
        "managers" => array("dataType" => "string", "length" => 150),
        "briefdescription" => array("dataType" => "string", "length" => 300),
        "fulldescription" => array("dataType" => "string", "length" => 5000),
        "notes" => array("dataType" => "string", "length" => 250),
        "iconurl" => array("dataType" => "string", "length" => 150),
        "headerurl" => array("dataType" => "string", "length" => 150),
        "occurrencesearch" => array("dataType" => "number", "length" => 10),
        "ispublic" => array("dataType" => "number", "length" => 10),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
        "parentpid" => array("dataType" => "number", "length" => 10),
        "sortsequence" => array("dataType" => "number", "length" => 10),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
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
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO fmprojects(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteProjectRecord($pid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM fmprojects WHERE pid = ' . (int)$pid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getProjectData($pid): array
    {
        $retArr = array();
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
                    $retArr[$name] = $row[$name];
                }
            }
        }
        return $retArr;
    }

    public function getProjectListByUserRights(): array
    {
        $retArr = array();
        $cArr = array();
        if(array_key_exists('ProjAdmin',$GLOBALS['USER_RIGHTS'])) {
            $cArr = $GLOBALS['USER_RIGHTS']['ProjAdmin'];
        }
        if($cArr){
            $sql = 'SELECT pid, projname FROM fmprojects WHERE pid IN(' . implode(',', $cArr) . ') ORDER BY projname ';
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

    public function updateProjectRecord($pid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($pid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'pid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
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
