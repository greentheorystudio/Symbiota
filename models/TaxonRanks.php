<?php
include_once(__DIR__ . '/../services/DbService.php');

class TaxonRanks{

	private $conn;

    private $defaultRanks = array(
        '10' => array('rankname' => 'Kingdom', 'rankid' => 10),
        '20' => array('rankname' => 'Subkingdom', 'rankid' => 20),
        '25' => array('rankname' => 'Infrakingdom', 'rankid' => 25),
        '27' => array('rankname' => 'Superphylum', 'rankid' => 27),
        '30' => array('rankname' => 'Phylum', 'rankid' => 30),
        '40' => array('rankname' => 'Subphylum', 'rankid' => 40),
        '45' => array('rankname' => 'Infraphylum', 'rankid' => 45),
        '50' => array('rankname' => 'Superclass', 'rankid' => 50),
        '60' => array('rankname' => 'Class', 'rankid' => 60),
        '70' => array('rankname' => 'Subclass', 'rankid' => 70),
        '80' => array('rankname' => 'Infraclass', 'rankid' => 80),
        '90' => array('rankname' => 'Superorder', 'rankid' => 90),
        '100' => array('rankname' => 'Order', 'rankid' => 100),
        '110' => array('rankname' => 'Suborder', 'rankid' => 110),
        '120' => array('rankname' => 'Infraorder', 'rankid' => 120),
        '124' => array('rankname' => 'Section', 'rankid' => 124),
        '126' => array('rankname' => 'Subsection', 'rankid' => 126),
        '130' => array('rankname' => 'Superfamily', 'rankid' => 130),
        '140' => array('rankname' => 'Family', 'rankid' => 140),
        '150' => array('rankname' => 'Subfamily', 'rankid' => 150),
        '160' => array('rankname' => 'Tribe', 'rankid' => 160),
        '170' => array('rankname' => 'Subtribe', 'rankid' => 170),
        '180' => array('rankname' => 'Genus', 'rankid' => 180),
        '190' => array('rankname' => 'Subgenus', 'rankid' => 190),
        '220' => array('rankname' => 'Species', 'rankid' => 220),
        '230' => array('rankname' => 'Subspecies', 'rankid' => 230),
        '240' => array('rankname' => 'Variety', 'rankid' => 240),
        '245' => array('rankname' => 'Form', 'rankid' => 245),
        '250' => array('rankname' => 'Race', 'rankid' => 250),
        '255' => array('rankname' => 'Stirp', 'rankid' => 255),
        '260' => array('rankname' => 'Morph', 'rankid' => 260),
        '265' => array('rankname' => 'Aberration', 'rankid' => 265),
        '300' => array('rankname' => 'Unspecified', 'rankid' => 300)
    );

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
            $retArr = $this->defaultRanks;
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
}
