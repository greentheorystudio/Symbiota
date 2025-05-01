<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/TaxonomyUtilities.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonomyAPIManager{

    private $conn;
    private $rankLimit = 0;
    private $rankLow = 0;
    private $rankHigh = 0;
    private $limit = 0;
    private $hideAuth = false;
    private $hideProtected = false;
    private $acceptedOnly = false;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function generateSciNameList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT SciName, Author, TID FROM taxa ';
        $sql .= 'WHERE SciName LIKE "'.SanitizerService::cleanInStr($this->conn,$queryString).'%" ';
        if($this->rankLimit){
            $sql .= 'AND RankId = '.$this->rankLimit.' ';
        }
        else{
            if($this->rankLow){
                $sql .= 'AND RankId >= '.$this->rankLow.' ';
            }
            if($this->rankHigh){
                $sql .= 'AND RankId <= '.$this->rankHigh.' ';
            }
        }
        if($this->hideProtected){
            $sql .= 'AND SecurityStatus <> 1 ';
        }
        if($this->acceptedOnly){
            $sql .= 'AND TID = tidaccepted ';
        }
        if($this->limit){
            $sql .= 'LIMIT '.$this->limit.' ';
        }
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $sciName = $r->SciName.($this->hideAuth?'':' '.$r->Author);
            $retArr[$sciName]['id'] = $r->TID;
            $retArr[$sciName]['value'] = $sciName;
            $retArr[$sciName]['author'] = $r->Author;
        }

        return $retArr;
    }

    public function generateVernacularList($queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT t.TID, v.VernacularName, t.SciName '.
            'FROM taxavernaculars AS v LEFT JOIN taxa AS t ON v.TID = t.TID ';
        $sql .= 'WHERE v.VernacularName LIKE "%'.SanitizerService::cleanInStr($this->conn,$queryString).'%" ';
        if($this->limit){
            $sql .= 'LIMIT '.$this->limit.' ';
        }
        $rs = $this->conn->query($sql);
        while ($r = $rs->fetch_object()){
            $label = $r->VernacularName;
            $retArr[$label]['id'] = $r->TID;
            $retArr[$label]['value'] = $label;
        }

        return $retArr;
    }

    public function setRankLimit($val): void
    {
        $this->rankLimit = SanitizerService::cleanInStr($this->conn,$val);
    }

    public function setRankLow($val): void
    {
        $this->rankLow = SanitizerService::cleanInStr($this->conn,$val);
    }

    public function setRankHigh($val): void
    {
        $this->rankHigh = SanitizerService::cleanInStr($this->conn,$val);
    }

    public function setLimit($val): void
    {
        $this->limit = SanitizerService::cleanInStr($this->conn,$val);
    }

    public function setHideAuth($val): void
    {
        if($val === 'true' || (int)$val === 1){
            $this->hideAuth = true;
        }
        else{
            $this->hideAuth = false;
        }
    }

    public function setAcceptedOnly($val): void
    {
        if($val === 'true' || (int)$val === 1){
            $this->acceptedOnly = true;
        }
        else{
            $this->acceptedOnly = false;
        }
    }

    public function setHideProtected($val): void
    {
        $this->hideProtected = SanitizerService::cleanInStr($this->conn,$val);
    }
}
