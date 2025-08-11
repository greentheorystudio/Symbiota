<?php
include_once(__DIR__ . '/../services/DbService.php');

class TaxonRanks{

	private $conn;

    private $fields = array(
        'taxonunitid' => array('dataType' => 'number', 'length' => 11),
        'kingdomid' => array('dataType' => 'number', 'length' => 11),
        'rankid' => array('dataType' => 'number', 'length' => 5),
        'rankname' => array('dataType' => 'string', 'length' => 15),
        'suffix' => array('dataType' => 'string', 'length' => 45),
        'dirparentrankid' => array('dataType' => 'number', 'length' => 6),
        'reqparentrankid' => array('dataType' => 'number', 'length' => 6),
        'modifiedby' => array('dataType' => 'string', 'length' => 45),
        'modifiedtimestamp' => array('dataType' => 'date', 'length' => 0),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function getRankArr($kingdomId = null): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT rankid, rankname FROM taxonunits ';
        if($kingdomId){
            $sql .= 'WHERE kingdomid = ' . (int)$kingdomId . ' ';
        }
        $sql .= 'ORDER BY rankid ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(array_key_exists($row['rankid'], $retArr)){
                    $retArr[$row['rankid']]['rankname'] .= ', ' . $row['rankname'];
                }
                else{
                    $retArr[$row['rankid']]['rankname'] = $row['rankname'];
                }
                $retArr[$row['rankid']]['rankid'] = (int)$row['rankid'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getRankNameArr(): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT rankname, rankid FROM taxonunits ';
        //echo $sql;
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $rankName = strtolower($row['rankname']);
                $retArr[$rankName] = (int)$row['rankid'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }
}
