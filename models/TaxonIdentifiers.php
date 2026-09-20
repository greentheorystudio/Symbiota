<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonIdentifiers{

	private ?mysqli $conn;

    private array $fields = array(
        'tidentid' => array('dataType' => 'number', 'length' => 10),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'name' => array('dataType' => 'string', 'length' => 45),
        'identifier' => array('dataType' => 'string', 'length' => 255)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function addTaxonIdentifier($tid, $idName, $id): int
    {
        $returnVal = 0;
        if($tid && $idName && $id){
            $identifierName = SanitizerService::cleanInStr($this->conn, $idName);
            $identifier = SanitizerService::cleanInStr($this->conn, $id);
            $sql = 'INSERT IGNORE INTO taxaidentifiers(tid, `name`, identifier) VALUES('.
                (int)$tid . ',"' . $identifierName . '", "' . $identifier . '")';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
            else{
                $sql = 'UPDATE taxaidentifiers SET identifier = "' . $identifier . '" WHERE tid = ' . (int)$tid . ' AND `name` = "' . $identifierName . '" ';
                if($this->conn->query($sql)){
                    $returnVal = 1;
                }
            }
        }
        return $returnVal;
    }

    public function deleteTaxonIdentifierRecords($tid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM taxaidentifiers WHERE tid = ' . (int)$tid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getGroupIdentifierNameArr(): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT `name` FROM taxaidentifiers WHERE ISNULL(identifier) ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = $row['name'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getIdentifiersForTaxonomicGroup($tid, $index, $source): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, ti.identifier '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxaidentifiers AS ti ON t.TID = ti.tid '.
            'WHERE (te.parenttid = ' . (int)$tid . ' OR t.TID = ' . (int)$tid . ') AND ti.name = "' . SanitizerService::cleanInStr($this->conn, $source) . '" '.
            'LIMIT ' . (((int)$index - 1) * 50000) . ', 50000';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $rIndex => $row){
                $resultArr = array();
                $resultArr['tid'] = $row['TID'];
                $resultArr['identifier'] = $row['identifier'];
                $retArr[] = $resultArr;
                unset($rows[$rIndex]);
            }
        }
        return $retArr;
    }

    public function getIdentifiersFromTidArr($tidArr): array
    {
        $retArr = array();
        $sql = 'SELECT tid, name, identifier FROM taxaidentifiers WHERE tid IN(' . implode(',', $tidArr) . ') ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if(!array_key_exists($row['tid'], $retArr)){
                    $retArr[$row['tid']] = array();
                }
                $resultArr = array();
                $resultArr['name'] = $row['name'];
                $resultArr['identifier'] = $row['identifier'];
                $retArr[$row['tid']][] = $resultArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getTaxonIdentifiersFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT `name`, identifier FROM taxaidentifiers WHERE tid = ' . (int)$tid . ' ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['name'] = $row['name'];
                $nodeArr['identifier'] = $row['identifier'];
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getValueIdentifierNameArr(): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT `name` FROM taxaidentifiers WHERE identifier IS NOT NULL ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr[] = $row['name'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function updateGeneticDataIdentifiers(): int
    {
        $returnVal = 0;
        $sql = 'DELETE FROM taxaidentifiers WHERE `name` = "genetic-data-available" ';
        if($this->conn->query($sql)){
            $sql = 'INSERT IGNORE INTO taxaidentifiers(tid, `name`) '.
                'SELECT DISTINCT o.tid, "genetic-data-available" FROM omoccurgenetic AS g LEFT JOIN omoccurrences AS o ON g.occid = o.occid '.
                'WHERE o.tid IS NOT NULL ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function updateTaxonIdentifier($tid, $idName, $id): int
    {
        $returnVal = 0;
        if($tid && $idName && $id){
            $identifierName = SanitizerService::cleanInStr($this->conn, $idName);
            $identifier = SanitizerService::cleanInStr($this->conn, $id);
            $sql = 'UPDATE taxaidentifiers SET identifier = "' . $identifier . '" WHERE tid = ' . (int)$tid . ' AND `name` = "' . $identifierName . '" ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }
}
