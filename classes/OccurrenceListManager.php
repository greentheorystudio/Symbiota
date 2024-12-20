<?php
include_once(__DIR__ . '/OccurrenceManager.php');
include_once(__DIR__ . '/OccurrenceAccessStats.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceListManager extends OccurrenceManager{

    protected $recordCount = 0;
    protected $sortField1 = '';
    protected $sortField2 = '';
    protected $sortOrder = '';

    public function getRecordArr($pageRequest,$cntPerPage): array
    {
        $canReadRareSpp = false;
        if($GLOBALS['USER_RIGHTS']){
            if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll', $GLOBALS['USER_RIGHTS'])){
                $canReadRareSpp = true;
            }
        }
        $returnArr = array();
        $imageSearchArr = array();
        $sqlWhere = $this->getSqlWhere();
        if(!$this->recordCount){
            $this->setRecordCnt($sqlWhere);
        }
        if($this->recordCount > 0){
            $sql = 'SELECT DISTINCT o.occid, c.CollID, IFNULL(o.institutioncode,c.institutioncode) AS institutioncode, IFNULL(o.collectioncode,c.collectioncode) AS collectioncode, c.collectionname, c.icon, '.
                'CONCAT_WS(":",c.institutioncode, c.collectioncode) AS collection, '.
                'IFNULL(o.CatalogNumber,"") AS catalognumber, t.family, o.sciname, t.tidaccepted, '.
                'IFNULL(DATE_FORMAT(o.eventDate,"%d %M %Y"),"") AS date, '.
                'IFNULL(o.scientificNameAuthorship,"") AS author, IFNULL(o.recordedBy,"") AS recordedby, IFNULL(o.recordNumber,"") AS recordnumber, '.
                'o.eventDate, IFNULL(o.country,"") AS country, IFNULL(o.StateProvince,"") AS state, IFNULL(o.county,"") AS county, '.
                'o.locality, o.decimallatitude, o.decimallongitude, '.
                'IFNULL(o.LocalitySecurity,0) AS LocalitySecurity, o.localitysecurityreason, IFNULL(o.habitat,"") AS habitat, '.
                'CONCAT_WS("-",o.minimumElevationInMeters, o.maximumElevationInMeters) AS elev, o.observeruid, '.
                'o.associatedtaxa, o.substrate, o.individualCount, o.lifeStage, o.sex, c.sortseq ';
            $sql .= 'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
                'LEFT JOIN taxa AS t ON o.tid = t.TID ';
            $sql .= $this->setTableJoins($sqlWhere);
            $sql .= $sqlWhere;
            if($this->sortField1 || $this->sortField2 || $this->sortOrder){
                $sortFields = array('Collection' => 'collection','Catalog Number' => 'o.CatalogNumber','Family' => 'o.family',
                    'Scientific Name' => 'o.sciname','Collector' => 'o.recordedBy','Number' => 'o.recordNumber','Event Date' => 'o.eventDate',
                    'Individual Count' => 'o.individualCount','Life Stage' => 'o.lifeStage','Sex' => 'o.sex',
                    'Country' => 'o.country','State/Province' => 'o.StateProvince','County' => 'o.county','Elevation' => 'CAST(elev AS UNSIGNED)');
                if($this->sortField1) {
                    $this->sortField1 = $sortFields[$this->sortField1];
                }
                if($this->sortField2) {
                    $this->sortField2 = $sortFields[$this->sortField2];
                }
                $sql .= 'ORDER BY ';
                if (!$canReadRareSpp) {
                    $sql .= 'LocalitySecurity ASC,';
                }
                $sql .= $this->sortField1.' '.$this->sortOrder.' ';
                if ($this->sortField2) {
                    $sql .= ','.$this->sortField2.' '.$this->sortOrder.' ';
                }
            }
            else{
                $sql .= 'ORDER BY c.sortseq, c.collectionname ';
            }
            $pageRequest = ($pageRequest - 1)*$cntPerPage;
            $sql .= 'LIMIT ' .$pageRequest. ',' .$cntPerPage;
            //echo "<div>Spec sql: ".$sql."</div>";
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $occId = $row->occid;
                $returnArr[$occId]['collid'] = $row->CollID;
                $returnArr[$occId]['institutioncode'] = $this->cleanOutStr($row->institutioncode);
                $returnArr[$occId]['collectioncode'] = $this->cleanOutStr($row->collectioncode);
                $returnArr[$occId]['collectionname'] = $this->cleanOutStr($row->collectionname);
                $returnArr[$occId]['collicon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($row->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->icon) : $row->icon;
                $returnArr[$occId]['accession'] = $this->cleanOutStr($row->catalognumber);
                $returnArr[$occId]['family'] = $this->cleanOutStr($row->family);
                $returnArr[$occId]['sciname'] = $this->cleanOutStr($row->sciname);
                $returnArr[$occId]['tid'] = $row->tidaccepted;
                $returnArr[$occId]['author'] = $this->cleanOutStr($row->author);
                $returnArr[$occId]['collector'] = $this->cleanOutStr($row->recordedby);
                $returnArr[$occId]['country'] = $this->cleanOutStr($row->country);
                $returnArr[$occId]['state'] = $this->cleanOutStr($row->state);
                $returnArr[$occId]['county'] = $this->cleanOutStr($row->county);
                $returnArr[$occId]['observeruid'] = $row->observeruid;
                $returnArr[$occId]['individualCount'] = $this->cleanOutStr($row->individualCount);
                $returnArr[$occId]['lifeStage'] = $this->cleanOutStr($row->lifeStage);
                $returnArr[$occId]['sex'] = $this->cleanOutStr($row->sex);
                $localitySecurity = $row->LocalitySecurity;
                if(!$localitySecurity || $canReadRareSpp
                    || (array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($row->CollID, $GLOBALS['USER_RIGHTS']['CollEditor'], true))
                    || (array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS']) && in_array($row->CollID, $GLOBALS['USER_RIGHTS']['RareSppReader'], true))){
                    $returnArr[$occId]['locality'] = $row->locality ? $this->cleanOutStr(str_replace('.,',',',$row->locality)) : '';
                    $returnArr[$occId]['collnumber'] = $this->cleanOutStr($row->recordnumber);
                    $returnArr[$occId]['habitat'] = $this->cleanOutStr($row->habitat);
                    $returnArr[$occId]['date'] = $row->date;
                    $returnArr[$occId]['decimallatitude'] = $row->decimallatitude;
                    $returnArr[$occId]['decimallongitude'] = $row->decimallongitude;
                    $returnArr[$occId]['eventDate'] = $row->eventDate;
                    $returnArr[$occId]['elev'] = $row->elev;
                    $returnArr[$occId]['substrate'] = $row->substrate;
                    $returnArr[$occId]['associatedtaxa'] = $row->associatedtaxa;
                    $imageSearchArr[] = $occId;
                }
                else{
                    $securityStr = '<span style="color:red;">Detailed locality information protected. ';
                    if($row->localitysecurityreason){
                        $securityStr .= $row->localitysecurityreason;
                    }
                    else{
                        $securityStr .= 'This is typically done to protect rare or threatened species localities.';
                    }
                    $returnArr[$occId]['locality'] = $securityStr.'</span>';
                }
            }
            $result->free();
        }
        if($imageSearchArr){
            $sql = 'SELECT o.collid, o.occid, i.thumbnailurl, i.url '.
                'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
                'WHERE o.occid IN('.implode(',',$imageSearchArr).') '.
                'ORDER BY o.occid, i.sortsequence';
            $rs = $this->conn->query($sql);
            $previousOccid = 0;
            while($r = $rs->fetch_object()){
                if($r->occid !== $previousOccid){
                    $tnUrl = ($r->thumbnailurl && $GLOBALS['CLIENT_ROOT'] && strncmp($r->thumbnailurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r->thumbnailurl) : $r->thumbnailurl;
                    if($tnUrl){
                        $returnArr[$r->occid]['img'] = $tnUrl;
                    }
                    if($r->url){
                        $returnArr[$r->occid]['hasimage'] = true;
                    }
                }
                $previousOccid = $r->occid;
            }
            $rs->free();
        }
        if($returnArr){
            $statsManager = new OccurrenceAccessStats();
            $statsManager->recordAccessEventByArr(array_keys($returnArr),'list');
        }
        return $returnArr;
    }

    private function setRecordCnt($sqlWhere): void
    {
        if($sqlWhere){
            $sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
                'LEFT JOIN taxa AS t ON o.tid = t.TID ';
            $sql .= $this->setTableJoins($sqlWhere);
            $sql .= $sqlWhere;
            //echo '<div>Count sql: ' .$sql. '</div>';
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $this->recordCount = $row->cnt;
            }
            $result->free();
            setCookie('collvars', 'reccnt:' .$this->recordCount,time()+64800,($GLOBALS['CLIENT_ROOT']?:'/'));
        }
    }

    public function getRecordCnt(): int
    {
        return $this->recordCount;
    }

    public function setSorting($sf1,$sf2,$so): void
    {
        $this->sortField1 = $sf1;
        $this->sortField2 = $sf2;
        $this->sortOrder = $so;
    }

    public function getCloseTaxaMatch($name): array
    {
        $retArr = array();
        $searchName = SanitizerService::cleanInStr($this->conn,$name);
        $sql = 'SELECT tid, sciname FROM taxa WHERE soundex(sciname) = soundex(?)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $searchName);
        $stmt->execute();
        $stmt->bind_result($tid, $sciname);
        while($stmt->fetch()){
            if($searchName !== $sciname) {
                $retArr[$tid] = $sciname;
            }
        }
        $stmt->close();
        return $retArr;
    }
}
