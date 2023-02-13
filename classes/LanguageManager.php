<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class LanguageManager {

    private $conn;

	public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function getLanguageArr(): array
    {
        $retArr = array();
        $sql = 'SELECT iso639_1, langname '.
            'FROM adminlanguages '.
            'ORDER BY langname ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $langArr = array();
            $langArr['iso'] = $r->iso639_1;
            $langArr['name'] = $r->langname;
            $retArr[] = $langArr;
        }
        $rs->free();
        return $retArr;
    }

    public function getLanguageByIso($iso): array
    {
        $retArr = array();
        $sql = 'SELECT langid, iso639_1, langname '.
            'FROM adminlanguages '.
            'WHERE iso639_1 = "'.$iso.'" ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr['id'] = $r->langid;
            $retArr['iso'] = $r->iso639_1;
            $retArr['name'] = $r->langname;
        }
        $rs->free();
        return $retArr;
    }

    public function getLanguageByName($name): array
    {
        $retArr = array();
        $sql = 'SELECT langid, iso639_1, langname '.
            'FROM adminlanguages '.
            'WHERE langname = "'.$name.'" ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr['id'] = $r->langid;
            $retArr['iso'] = $r->iso639_1;
            $retArr['name'] = $r->langname;
        }
        $rs->free();
        return $retArr;
    }

    public function getAutocompleteLanguageList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT langid, iso639_1, langname FROM adminlanguages '.
            'WHERE langname LIKE "'.Sanitizer::cleanInStr($this->conn,$queryString).'%" '.
            'ORDER BY langname LIMIT 10 ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $langArr = array();
            $langArr['id'] = $r->langid;
            $langArr['iso'] = $r->iso639_1;
            $langArr['name'] = $r->langname;
            $retArr[] = $langArr;
        }

        return $retArr;
    }
}
