<?php
include_once(__DIR__ . '/KeyCharacterHeadings.php');
include_once(__DIR__ . '/../services/DbService.php');

class KeyCharacters{

	private $conn;

    private $fields = array(
        'cid' => array('dataType' => 'number', 'length' => 10),
        'chid' => array('dataType' => 'number', 'length' => 10),
        'charactername' => array('dataType' => 'string', 'length' => 150),
        'description' => array('dataType' => 'string', 'length' => 255),
        'infourl' => array('dataType' => 'string', 'length' => 500),
        'language' => array('dataType' => 'string', 'length' => 45),
        'langid' => array('dataType' => 'number', 'length' => 11),
        'sortsequence' => array('dataType' => 'number', 'length' => 11),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function addCharacterDependencyRecord($cid, $dcid, $dcsid): int
    {
        $newID = 0;
        if($cid && $dcid){
            $sql = 'INSERT INTO keycharacterdependence(cid, dcid, dcsid) '.
                'VALUES (' . (int)$cid . ', ' . (int)$dcid . ', ' . ((int)$dcsid > 0 ? (int)$dcsid : 'NULL') . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function createKeyCharacterRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'cid' && $field !== 'initialtimestamp' && array_key_exists($field, $data)){
                $fieldNameArr[] = $field;
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT INTO keycharacters(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteKeyCharacterDependencyRecord($cdid): int
    {
        $retVal = 0;
        $sql = 'DELETE FROM keycharacterdependence WHERE cdid = ' . (int)$cdid . ' ';
        if($this->conn->query($sql)){
            $retVal = 1;
        }
        return $retVal;
    }

    public function deleteKeyCharacterRecord($cid): int
    {
        $retVal = 0;
        $sql = 'DELETE FROM keycharacterdependence WHERE cid = ' . (int)$cid . ' OR dcid = ' . (int)$cid . ' ';
        if($this->conn->query($sql)){
            $retVal = 1;
        }
        if($retVal){
            $sql = 'DELETE FROM keycharacterstatetaxalink WHERE cid = ' . (int)$cid . ' ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        if($retVal){
            $sql = 'DELETE FROM keycharacters WHERE cid = ' . (int)$cid . ' ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function getAutocompleteCharacterList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT cid, chid, charactername, `language` FROM keycharacters ';
        $sql .= 'WHERE charactername LIKE "' . SanitizerService::cleanInStr($this->conn, $queryString) . '%" ORDER BY charactername ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $dataArr = array();
                $dataArr['cid'] = $row['cid'];
                $dataArr['chid'] = $row['chid'];
                $dataArr['charactername'] = $row['charactername'];
                $dataArr['language'] = $row['language'];
                $retArr[] = $dataArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getCharacterArr(): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'c');
        $sql = 'SELECT h.headingname, ' . implode(',', $fieldNameArr) . ' '.
            'FROM keycharacters AS c LEFT JOIN keycharacterheadings AS h ON c.chid = h.chid '.
            'ORDER BY h.sortsequence, h.headingname, c.sortsequence, c.charactername ';
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
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getCharacterDependencies($cid): array
    {
        $retArr = array();
        $sql = 'SELECT cd.cdid, cd.cid, cd.dcid, cd.dcsid, c.charactername, cs.characterstatename '.
            'FROM keycharacterdependence AS cd LEFT JOIN keycharacters AS c ON cd.dcid = c.cid '.
            'LEFT JOIN keycharacterstates AS cs ON cd.dcsid = cs.csid '.
            'WHERE cd.cid IN(' . (is_array($cid) ? implode(',', $cid) : $cid) . ') ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['cdid'] = $row['cdid'];
                $nodeArr['cid'] = $row['dcid'];
                $nodeArr['csid'] = $row['dcsid'];
                $nodeArr['charactername'] = $row['charactername'];
                $nodeArr['characterstatename'] = $row['characterstatename'];
                $retArr[$row['cid']][] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getKeyCharacterData($cid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM keycharacters WHERE chid = ' . (int)$cid . ' ';
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

    public function getKeyCharactersArrByChidArr($chidArr): array
    {
        $retArr = array();
        $cidArr = array();
        $tempArr = array();
        if(count($chidArr) > 0){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM keycharacters WHERE chid IN(' . implode(',', $chidArr) . ') '.
                'ORDER BY chid, sortsequence, charactername ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $cidArr[] = $row['cid'];
                    if(!array_key_exists($row['cid'], $tempArr)){
                        $tempArr[$row['cid']] = array();
                    }
                    foreach($fields as $val){
                        $name = $val->name;
                        $tempArr[$row['cid']][$name] = $row[$name];
                    }
                    unset($rows[$index]);
                }
                $depArr = $this->getCharacterDependencies($cidArr);
                foreach($tempArr as $cid => $cArr){
                    $cArr['dependencies'] = array_key_exists($cid, $depArr) ? $depArr[$cid] : array();
                    if(!array_key_exists($cArr['chid'], $retArr)){
                        $retArr[$cArr['chid']] = array();
                    }
                    $retArr[$cArr['chid']][] = $cArr;
                }
            }
        }
        return $retArr;
    }

    public function getKeyCharactersArrByCidArr($cidArr, $includeFullKeyData = false): array
    {
        $retArr = array();
        $chidArr = array();
        $tempArr = array();
        if(count($cidArr) > 0){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM keycharacters WHERE cid IN(' . implode(',', $cidArr) . ') ';
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $retArr['characters'] = array();
                $retArr['character-headings'] = array();
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if($includeFullKeyData && !in_array((int)$row['chid'], $chidArr, true)){
                        $chidArr[] = (int)$row['chid'];
                    }
                    if(!array_key_exists($row['cid'], $tempArr)){
                        $tempArr[$row['cid']] = array();
                    }
                    foreach($fields as $val){
                        $name = $val->name;
                        if($name !== 'cid'){
                            $tempArr[$row['cid']][$name] = $row[$name];
                        }
                    }
                    unset($rows[$index]);
                }
                $depArr = $this->getCharacterDependencies($cidArr);
                foreach($tempArr as $cid => $cArr){
                    $cArr['cid'] = $cid;
                    $cArr['dependencies'] = array_key_exists($cid, $depArr) ? $depArr[$cid] : array();
                    $retArr['characters'][] = $cArr;
                }
                if($includeFullKeyData){
                    $retArr['character-headings'] = (new KeyCharacterHeadings)->getKeyCharacterHeadingsArrByChidArr($chidArr);
                }
            }
        }
        return $retArr;
    }

    public function updateKeyCharacterRecord($cid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($cid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'cid' && $field !== 'initialtimestamp' && array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE keycharacters SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE cid = ' . (int)$cid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
