<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class Geography {

    private $conn;

	public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function getAutocompleteCountryList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT countryid, countryname FROM lkupcountry ';
        $sql .= 'WHERE countryname LIKE "'.SanitizerService::cleanInStr($this->conn,$queryString).'%" ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $dataArr = array();
                $dataArr['id'] = $row['countryid'];
                $dataArr['name'] = $row['countryname'];
                $retArr[] = $dataArr;
                unset($rows[$index]);
            }
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
        $sql .= 'WHERE c.countyname LIKE "' . SanitizerService::cleanInStr($this->conn, $queryString) . '%" ';
        if($stateProvince){
            $sql .= 'AND s.statename = "' . SanitizerService::cleanInStr($this->conn, $stateProvince) . '" ';
        }
        $sql .= 'ORDER BY c.countyname ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $dataArr = array();
                $dataArr['id'] = $row['countyid'];
                $dataArr['name'] = $row['countyname'];
                $retArr[] = $dataArr;
                unset($rows[$index]);
            }
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
        $sql .= 'WHERE s.statename LIKE "' . SanitizerService::cleanInStr($this->conn, $queryString) . '%" ';
        if($country){
            $sql .= 'AND c.countryname = "' . SanitizerService::cleanInStr($this->conn, $country) . '" ';
        }
        $sql .= 'ORDER BY s.statename ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $dataArr = array();
                $dataArr['id'] = $row['stateid'];
                $dataArr['name'] = $row['statename'];
                $retArr[] = $dataArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }
}
