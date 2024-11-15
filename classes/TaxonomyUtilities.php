<?php
include_once(__DIR__ . '/../services/DbService.php');

class TaxonomyUtilities {

    private $conn;

    public function __construct() {
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function getChildTidArr($tid): array
    {
        $returnArr = array();
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'SELECT tid FROM taxaenumtree '.
                    'WHERE parenttid IN('.$tidStr.') ';
                //echo $sql;
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $returnArr[] = (int)$r->tid;
                }
                $rs->free();
            }
        }
        return $returnArr;
    }

    public function updateFamily($tid): void
    {
        $tidStr = '';
        if($tid){
            if(is_array($tid)){
                $tidStr = implode(',', $tid);
            }
            elseif(is_numeric($tid)){
                $tidStr = $tid;
            }
            if($tidStr){
                $sql = 'UPDATE taxa AS t LEFT JOIN taxaenumtree AS te ON t.TID = te.tid '.
                    'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID '.
                    'SET t.family = t2.SciName '.
                    'WHERE t.TID IN('.$tidStr.') AND t.RankId > 140 AND t2.RankId = 140 ';
                //echo $sql;
                $this->conn->query($sql);
            }
        }
    }

    public function getTid($sciName, $kingdomid, $rankid, $author): int
    {
        $retTid = 0;
        if($sciName && $kingdomid){
            $sql = 'SELECT tid FROM taxa WHERE sciname = "'.$sciName.'" AND kingdomId = '.$kingdomid.' ';
            if($rankid){
                $sql .= 'AND rankid = '.$rankid.' ';
            }
            if($author){
                $sql .= 'AND author = "'.$author.'" ';
            }
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $retTid = (int)$r->tid;
            }
            $rs->close();
        }
        return $retTid;
    }

    public function getSensitiveTaxa(): array
    {
        $sensitiveArr = array();
        $sql = 'SELECT DISTINCT tid FROM taxa WHERE SecurityStatus = 1 ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $sensitiveArr[] = $r->tid;
        }
        $rs->free();
        return $sensitiveArr;
    }

    public function getRankArr($kingdomId = null): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT rankid, rankname FROM taxonunits ';
        if($kingdomId){
            $sql .= 'WHERE kingdomid = ' . $kingdomId . ' ';
        }
        $sql .= 'ORDER BY rankid ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            if(array_key_exists($row->rankid,$retArr)){
                $retArr[$row->rankid]['rankname'] .= ', ' . $row->rankname;
            }
            else{
                $retArr[$row->rankid]['rankname'] = $row->rankname;
            }
            $retArr[$row->rankid]['rankid'] = (int)$row->rankid;
        }
        $result->free();
        return $retArr;
    }

    public function getParentTids($tid): array
    {
        $returnArr = array();
        $sql = 'SELECT parenttid FROM taxaenumtree ' .
            'WHERE tid = ' .$tid. ' ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[] = $row->parenttid;
        }
        $result->close();
        return $returnArr;
    }

    public function editVernacular($postArr,$vId): int
    {
        $status = 0;
        if((int)$vId){
            $sql = 'UPDATE taxavernaculars SET ';
            if(array_key_exists('tid',$postArr) && (int)$postArr['tid']){
                $sql .= 'TID = '.(int)$postArr['tid'].', ';
            }
            if(array_key_exists('vernacularname',$postArr) && $postArr['vernacularname']){
                $sql .= 'VernacularName = '.(SanitizerService::cleanInStr($this->conn,$postArr['vernacularname'])?'"'.SanitizerService::cleanInStr($this->conn,$postArr['vernacularname']).'"':'NULL').', ';
            }
            if(array_key_exists('language',$postArr) && $postArr['language']){
                $sql .= '`Language` = '.(SanitizerService::cleanInStr($this->conn,$postArr['language'])?'"'.SanitizerService::cleanInStr($this->conn,$postArr['language']).'"':'NULL').', ';
            }
            if(array_key_exists('langid',$postArr) && (int)$postArr['langid']){
                $sql .= 'langid = '.(int)$postArr['langid'].', ';
            }
            if(array_key_exists('source',$postArr) && $postArr['source']){
                $sql .= 'Source = '.(SanitizerService::cleanInStr($this->conn,$postArr['source'])?'"'.SanitizerService::cleanInStr($this->conn,$postArr['source']).'"':'NULL').', ';
            }
            if(array_key_exists('notes',$postArr) && $postArr['notes']){
                $sql .= 'notes = '.(SanitizerService::cleanInStr($this->conn,$postArr['notes'])?'"'.SanitizerService::cleanInStr($this->conn,$postArr['notes']).'"':'NULL').', ';
            }
            if(array_key_exists('sortsequence',$postArr) && (int)$postArr['sortsequence']){
                $sql .= 'SortSequence = '.(int)$postArr['sortsequence'].', ';
            }
            $sql .= 'username = "'.$GLOBALS['PARAMS_ARR']['un'].'" ';
            $sql .= 'WHERE VID = '.$vId.' ';
            //echo $sql;
            if($this->conn->query($sql)){
                $status = 1;
            }
        }
        return $status;
    }
}
