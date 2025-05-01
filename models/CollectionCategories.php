<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class CollectionCategories {

    private $conn;

    private $fields = array(
        'ccpk' => array('dataType' => 'number', 'length' => 10),
        'category' => array('dataType' => 'string', 'length' => 75),
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

    public function getCollectionCategoryArr(): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' ' .
            'FROM omcollcategories ';
        $sql .= 'ORDER BY sortsequence, category ';
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
}
