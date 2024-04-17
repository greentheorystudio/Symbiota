<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class GeographyManager {

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

    public function getAutocompleteCountryList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT countryid, countryname FROM lkupcountry ';
        $sql .= 'WHERE countryname LIKE "'.Sanitizer::cleanInStr($this->conn,$queryString).'%" ';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $dataArr = array();
            $dataArr['id'] = $r->countryid;
            $dataArr['name'] = $r->countryname;
            $retArr[] = $dataArr;
        }

        return $retArr;
    }

    public function getAutocompleteCountyList($queryString, $stateProvince): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT c.countyid, c.countyname FROM lkupcounty AS c ';
        if($stateProvince){
            $sql .= 'LEFT JOIN lkupstateprovince AS s ON c.stateid = s.stateid ';
        }
        $sql .= 'WHERE c.countyname LIKE "'.Sanitizer::cleanInStr($this->conn,$queryString).'%" ';
        if($stateProvince){
            $sql .= 'AND s.statename = "'.$stateProvince.'" ';
        }
        $sql .= 'ORDER BY c.countyname';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $dataArr = array();
            $dataArr['id'] = $r->countyid;
            $dataArr['name'] = $r->countyname;
            $retArr[] = $dataArr;
        }

        return $retArr;
    }

    public function getAutocompleteStateProvinceList($queryString, $country): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT s.stateid, s.statename FROM lkupstateprovince AS s ';
        if($country){
            $sql .= 'LEFT JOIN lkupcountry AS c ON s.countryid = c.countryid ';
        }
        $sql .= 'WHERE s.statename LIKE "'.Sanitizer::cleanInStr($this->conn,$queryString).'%" ';
        if($country){
            $sql .= 'AND c.countryname = "'.$country.'" ';
        }
        $sql .= 'ORDER BY s.statename';
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $dataArr = array();
            $dataArr['id'] = $r->stateid;
            $dataArr['name'] = $r->statename;
            $retArr[] = $dataArr;
        }

        return $retArr;
    }
}
