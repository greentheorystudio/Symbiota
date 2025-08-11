<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class Languages {

    private $conn;

	public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function getLanguageArr(): array
    {
        $retArr = array();
        $sql = 'SELECT langid, iso639_1, iso639_2, langname '.
            'FROM adminlanguages '.
            'ORDER BY langname ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $langArr = array();
                $langArr['langid'] = (int)$row['langid'];
                $langArr['iso-1'] = $row['iso639_1'];
                $langArr['iso-2'] = $row['iso639_2'];
                $langArr['name'] = $row['langname'];
                $retArr[] = $langArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getLanguageByIso($iso): array
    {
        $retArr = array();
        $sql = 'SELECT langid, iso639_1, iso639_2, langname '.
            'FROM adminlanguages '.
            'WHERE iso639_1 = "' . SanitizerService::cleanInStr($this->conn, $iso) . '" OR iso639_2 = "' . SanitizerService::cleanInStr($this->conn, $iso) . '" ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $retArr['id'] = $row['langid'];
                $retArr['iso-1'] = $row['iso639_1'];
                $retArr['iso-2'] = $row['iso639_2'];
                $retArr['name'] = $row['langname'];
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getLanguageByName($name): array
    {
        $retArr = array();
        $sql = 'SELECT langid, iso639_1, iso639_2, langname '.
            'FROM adminlanguages '.
            'WHERE langname = "' . SanitizerService::cleanInStr($this->conn, $name)  .'" ';
        if($result = $this->conn->query($sql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                $retArr['id'] = $row['langid'];
                $retArr['iso-1'] = $row['iso639_1'];
                $retArr['iso-2'] = $row['iso639_2'];
                $retArr['name'] = $row['langname'];
            }
        }
        return $retArr;
    }

    public function getAutocompleteLanguageList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT langid, iso639_1, iso639_2, langname FROM adminlanguages '.
            'WHERE langname LIKE "%' . SanitizerService::cleanInStr($this->conn, $queryString) . '%" '.
            'ORDER BY langname LIMIT 10 ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $langArr = array();
                $langArr['id'] = $row['langid'];
                $langArr['iso-1'] = $row['iso639_1'];
                $langArr['iso-2'] = $row['iso639_2'];
                $langArr['name'] = $row['langname'];
                $retArr[] = $langArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }
}
