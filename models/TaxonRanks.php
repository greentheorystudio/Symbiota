<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/TaxonRankDataService.php');

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

    public function deleteKingdomRanks($kingdomName): int
    {
        $retVal = 1;
        $sql = 'DELETE u.* FROM taxonunits AS u LEFT JOIN taxonkingdoms AS k ON u.kingdomid = k.`kingdom_id` WHERE k.`kingdom_name` = "' . $kingdomName . '" ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getRankArr($kingdomId = null): array
    {
        $retArr = array();
        if($kingdomId){
            $sql = 'SELECT DISTINCT rankid, rankname FROM taxonunits ';
            $sql .= 'WHERE kingdomid = ' . (int)$kingdomId . ' ';
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
        }
        else{
            $retArr = TaxonRankDataService::getDefaultRankOptions();
        }
        return $retArr;
    }

    public function getRankNameArr(): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT rankname, rankid FROM taxonunits ORDER BY rankid ';
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

    public function setNewKingdomRanks($kingdomId, $kingdomName): void
    {
        $valueArr = array();
        $rankData = (new TaxonRankDataService)->getRankData($kingdomName);
        if($rankData){
            foreach($rankData as $data){
                $valueArr[] = '(' . $kingdomId . ', ' . $data['rankid'] . ', "' . $data['rankname'] . '", ' . $data['dirparentrankid'] . ', ' . $data['reqparentrankid'] . ')';
            }
            $sql = 'INSERT INTO taxonunits(kingdomid, rankid, rankname, dirparentrankid, reqparentrankid) '.
                'VALUES ' . implode(',', $valueArr) . ' ';
            $this->conn->query($sql);
        }
    }
}
