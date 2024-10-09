<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class SpatialModuleManager{

    protected $conn;
    protected $searchTermsArr = array();
    protected $recordCount = 0;
    private $sqlWhere = '';
    private $taxaArr = array();

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function getIdStrFromGeoJSON($json): string{
        $idArr = array();
        $jsonArr = json_decode($json, true);
        $featureArr = $jsonArr['features'];
        foreach($featureArr as $f => $data){
            $idArr[] = $data['properties']['id'];
        }
        return implode(',',$idArr);
    }

    public function getLayersConfigJSON(): string
    {
        if(file_exists($GLOBALS['SERVER_ROOT'].'/content/json/spatiallayerconfig.json')){
            $returnStr = file_get_contents($GLOBALS['SERVER_ROOT'].'/content/json/spatiallayerconfig.json');
        }
        else{
            $returnStr = '{}';
        }
        return $returnStr;
    }

    public function writeGPXFromGeoJSON($json): string{
        $returnStr = '<gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
            'xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" version="1.1" creator="BioSurv">';
        $jsonArr = json_decode($json, true);
        $featureArr = $jsonArr['features'];
        foreach($featureArr as $f => $data){
            $coordArr = $data['geometry']['coordinates'];
            $returnStr .= '<wpt lat="'.$coordArr[1].'" lon="'.$coordArr[0].'"/>';
        }
        $returnStr .= '</gpx>';

        return $returnStr;
    }

    public function writeKMLFromGeoJSON($json): string{
        $returnStr = '<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
            'xsi:schemaLocation="http://www.opengis.net/kml/2.2 https://developers.google.com/kml/schema/kml22gx.xsd">';
        $jsonArr = json_decode($json, true);
        $featureArr = $jsonArr['features'];
        $returnStr .= '<Document>';
        foreach($featureArr as $f => $data){
            $returnStr .= '<Placemark>';
            $coordArr = $data['geometry']['coordinates'];
            $propArr = $data['properties'];
            $propKeys = array_keys($propArr);
            if($propArr){
                $returnStr .= '<ExtendedData>';
                foreach($propKeys as $k){
                    $prop = htmlspecialchars((is_array($propArr[$k])?$propArr[$k][0]:$propArr[$k]), ENT_QUOTES);
                    if($propArr[$k]){
                        $returnStr .= '<Data><value>'.$prop.'</value></Data>';
                    }
                    else{
                        $returnStr .= '<Data value="'.$prop.'"/>';
                    }
                }
                $returnStr .= '</ExtendedData>';
            }
            $returnStr .= '<Point><coordinates>'.$coordArr[0].','.$coordArr[1].'</coordinates></Point>';
            $returnStr .= '</Placemark>';
        }
        $returnStr .= '</Document></kml>';

        return $returnStr;
    }

    public function getOccPointMapGeoJson($pageRequest,$cntPerPage){
        $geomArr = array();
        $featuresArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, o.family, o.sciname, o.tid, o.`year`, o.`month`, o.`day`, '.
            'o.decimalLatitude, o.decimalLongitude, c.CollectionName, c.CollType, t.family AS accFamily, '.
            'c.InstitutionCode, o.catalogNumber, o.recordedBy, o.recordNumber, o.eventDate AS displayDate '.
            'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'LEFT JOIN taxa AS t ON o.tid = t.TID ';
        $sql .= $this->setTableJoins();
        if(strncmp($this->sqlWhere, 'WHERE ', 6) !== 0){
            $sql .= 'WHERE ';
        }
        $sql .= $this->sqlWhere;
        if(!array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS'])){
            if(array_key_exists('RareSppReader',$GLOBALS['USER_RIGHTS'])){
                $sql .= ' AND (o.CollId IN (' .implode(',',$GLOBALS['USER_RIGHTS']['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR ISNULL(o.LocalitySecurity))) ';
            }
            else{
                $sql .= ' AND (o.LocalitySecurity = 0 OR ISNULL(o.LocalitySecurity)) ';
            }
        }
        $sql .= 'LIMIT ' .($pageRequest ?: 0). ',' .$cntPerPage;
        //return '<div>SQL: ' .$sql. '</div>';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $geoArr = array();
            $geoArr['type'] = 'Feature';
            $geoArr['geometry']['type'] = 'Point';
            $geoArr['geometry']['coordinates'] = [$row->decimalLongitude, $row->decimalLatitude];
            $geoArr['properties']['CollType'] = $row->CollType;
            $geoArr['properties']['collid'] = $row->collid;
            $geoArr['properties']['coll_year'] = $row->year;
            $geoArr['properties']['tid'] = $row->tid;
            $geoArr['properties']['coll_day'] = $row->day;
            $geoArr['properties']['id'] = $row->occid;
            $geoArr['properties']['CollectionName'] = $row->CollectionName;
            $geoArr['properties']['sciname'] = $row->sciname;
            $geoArr['properties']['family'] = $row->family;
            $geoArr['properties']['accFamily'] = $row->accFamily;
            $geoArr['properties']['coll_month'] = $row->month;
            $geoArr['properties']['InstitutionCode'] = $row->InstitutionCode;
            $geoArr['properties']['catalogNumber'] = $row->catalogNumber;
            $geoArr['properties']['recordedBy'] = $row->recordedBy;
            $geoArr['properties']['recordNumber'] = $row->recordNumber;
            $geoArr['properties']['displayDate'] = $row->displayDate;
            $featuresArr[] = $geoArr;
        }
        $result->close();

        $geomArr['type'] = 'FeatureCollection';
        $geomArr['numFound'] = $cntPerPage;
        $geomArr['start'] = 0;
        $geomArr['features'] = $featuresArr;

        return json_encode($geomArr);
    }

    public function getOccPointDownloadGeoJson($pageRequest,$cntPerPage){
        $geomArr = array();
        $featuresArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, o.catalogNumber, o.otherCatalogNumbers, o.sciname, o.associatedCollectors, '.
            'o.scientificNameAuthorship, o.identifiedBy, o.dateIdentified, o.typeStatus, o.recordedBy, o.recordNumber, '.
            'o.eventdate, o.`year`, o.`month`, o.`day`, o.habitat, o.associatedTaxa, o.basisOfRecord, o.occurrenceID, '.
            'o.`country`, o.stateProvince, o.`county`, o.municipality, o.locality, o.substrate, o.minimumDepthInMeters, '.
            'o.decimalLatitude, o.decimalLongitude, o.minimumElevationInMeters, o.geodeticDatum, o.coordinateUncertaintyInMeters, '.
            'o.maximumElevationInMeters, o.lifeStage, o.sex, o.individualCount, o.identificationQualifier, o.maximumDepthInMeters,  '.
            'c.InstitutionCode, c.CollectionCode, c.CollectionName, IFNULL(t.family,o.family) AS family, o.fieldnumber, '.
            'o.occurrenceRemarks, o.dynamicProperties, o.reproductiveCondition, o.lifeStage, o.sex, o.individualCount '.
            'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'LEFT JOIN taxa AS t ON o.tid = t.TID ';
        $sql .= $this->setTableJoins();
        if(strncmp($this->sqlWhere, 'WHERE ', 6) !== 0){
            $sql .= 'WHERE ';
        }
        $sql .= $this->sqlWhere;
        if(!array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS'])){
            if(array_key_exists('RareSppReader',$GLOBALS['USER_RIGHTS'])){
                $sql .= ' AND (o.CollId IN (' .implode(',',$GLOBALS['USER_RIGHTS']['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ';
            }
            else{
                $sql .= ' AND (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL) ';
            }
        }
        if($pageRequest && $cntPerPage){
            $sql .= 'LIMIT ' .$pageRequest. ',' .$cntPerPage;
        }
        //echo '<div>SQL: ' .$sql. '</div>';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $geoArr = array();
            $geoArr['type'] = 'Feature';
            $geoArr['geometry']['type'] = 'Point';
            $geoArr['geometry']['coordinates'] = [$row->decimalLongitude, $row->decimalLatitude];
            $geoArr['properties']['id'] = $row->occid;
            $geoArr['properties']['collid'] = $row->collid;
            if($row->basisOfRecord){
                $geoArr['properties']['basisOfRecord'] = $row->basisOfRecord;
            }
            if($row->occurrenceID){
                $geoArr['properties']['occurrenceID'] = $row->occurrenceID;
            }
            if($row->catalogNumber){
                $geoArr['properties']['catalogNumber'] = $row->catalogNumber;
            }
            if($row->otherCatalogNumbers){
                $geoArr['properties']['otherCatalogNumbers'] = $row->otherCatalogNumbers;
            }
            if($row->InstitutionCode){
                $geoArr['properties']['InstitutionCode'] = $row->InstitutionCode;
            }
            if($row->CollectionCode){
                $geoArr['properties']['CollectionCode'] = $row->CollectionCode;
            }
            if($row->CollectionName){
                $geoArr['properties']['CollectionName'] = $row->CollectionName;
            }
            if($row->family){
                $geoArr['properties']['family'] = $row->family;
            }
            if($row->sciname){
                $geoArr['properties']['sciname'] = $row->sciname;
            }
            if($row->scientificNameAuthorship){
                $geoArr['properties']['scientificNameAuthorship'] = $row->scientificNameAuthorship;
            }
            if($row->identifiedBy){
                $geoArr['properties']['identifiedBy'] = $row->identifiedBy;
            }
            if($row->dateIdentified){
                $geoArr['properties']['dateIdentified'] = $row->dateIdentified;
            }
            if($row->identificationQualifier){
                $geoArr['properties']['identificationQualifier'] = $row->identificationQualifier;
            }
            if($row->typeStatus){
                $geoArr['properties']['typeStatus'] = $row->typeStatus;
            }
            if($row->recordedBy){
                $geoArr['properties']['recordedBy'] = $row->recordedBy;
            }
            if($row->recordNumber){
                $geoArr['properties']['recordNumber'] = $row->recordNumber;
            }
            if($row->associatedCollectors){
                $geoArr['properties']['associatedCollectors'] = $row->associatedCollectors;
            }
            if($row->eventdate){
                $geoArr['properties']['eventdate'] = $row->eventdate;
            }
            if($row->year){
                $geoArr['properties']['year'] = $row->year;
            }
            if($row->month){
                $geoArr['properties']['month'] = $row->month;
            }
            if($row->day){
                $geoArr['properties']['day'] = $row->day;
            }
            if($row->habitat){
                $geoArr['properties']['habitat'] = $row->habitat;
            }
            if($row->substrate){
                $geoArr['properties']['substrate'] = $row->substrate;
            }
            if($row->fieldnumber){
                $geoArr['properties']['fieldnumber'] = $row->fieldnumber;
            }
            if($row->occurrenceRemarks){
                $geoArr['properties']['occurrenceRemarks'] = $row->occurrenceRemarks;
            }
            if($row->associatedTaxa){
                $geoArr['properties']['associatedTaxa'] = $row->associatedTaxa;
            }
            if($row->dynamicProperties){
                $geoArr['properties']['dynamicProperties'] = $row->dynamicProperties;
            }
            if($row->reproductiveCondition){
                $geoArr['properties']['reproductiveCondition'] = $row->reproductiveCondition;
            }
            if($row->lifeStage){
                $geoArr['properties']['lifeStage'] = $row->lifeStage;
            }
            if($row->sex){
                $geoArr['properties']['sex'] = $row->sex;
            }
            if($row->individualCount){
                $geoArr['properties']['individualCount'] = $row->individualCount;
            }
            if($row->country){
                $geoArr['properties']['country'] = $row->country;
            }
            if($row->stateProvince){
                $geoArr['properties']['stateProvince'] = $row->stateProvince;
            }
            if($row->county){
                $geoArr['properties']['county'] = $row->county;
            }
            if($row->municipality){
                $geoArr['properties']['municipality'] = $row->municipality;
            }
            if($row->locality){
                $geoArr['properties']['locality'] = $row->locality;
            }
            if($row->geodeticDatum){
                $geoArr['properties']['geodeticDatum'] = $row->geodeticDatum;
            }
            if($row->coordinateUncertaintyInMeters){
                $geoArr['properties']['coordinateUncertaintyInMeters'] = $row->coordinateUncertaintyInMeters;
            }
            if($row->minimumElevationInMeters){
                $geoArr['properties']['minimumElevationInMeters'] = $row->minimumElevationInMeters;
            }
            if($row->maximumElevationInMeters){
                $geoArr['properties']['maximumElevationInMeters'] = $row->maximumElevationInMeters;
            }
            if($row->minimumDepthInMeters){
                $geoArr['properties']['minimumDepthInMeters'] = $row->minimumDepthInMeters;
            }
            if($row->maximumDepthInMeters){
                $geoArr['properties']['maximumDepthInMeters'] = $row->maximumDepthInMeters;
            }
            $featuresArr[] = $geoArr;
        }
        $result->close();

        $geomArr['type'] = 'FeatureCollection';
        $geomArr['numFound'] = $cntPerPage;
        $geomArr['start'] = 0;
        $geomArr['features'] = $featuresArr;

        return json_encode($geomArr);
    }

    public function setRecordCnt(): void
    {
        if($this->sqlWhere){
            $sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
                'LEFT JOIN taxa AS t ON o.tid = t.TID ';
            $sql .= $this->setTableJoins();
            $sql .= $this->sqlWhere;
            if(!array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS'])){
                if(array_key_exists('RareSppReader',$GLOBALS['USER_RIGHTS'])){
                    $sql .= ' AND (o.CollId IN (' .implode(',',$GLOBALS['USER_RIGHTS']['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ';
                }
                else{
                    $sql .= ' AND (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL) ';
                }
            }
            //echo "<div>Count sql: ".$sql."</div>";
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $this->recordCount = $row->cnt;
            }
            $result->close();
        }
    }

    public function getMapRecordPageArr($pageRequest,$cntPerPage): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, c.institutioncode, o.catalognumber, CONCAT_WS(" ",o.recordedby,o.recordnumber) AS collector, '.
            'o.eventdate, o.family, o.sciname, o.tid, CONCAT_WS("; ",o.country, o.stateProvince, o.county) AS locality, o.DecimalLatitude, o.DecimalLongitude, '.
            'IFNULL(o.LocalitySecurity,0) AS LocalitySecurity, o.localitysecurityreason '.
            'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'LEFT JOIN taxa AS t ON o.tid = t.TID ';
        $sql .= $this->setTableJoins();
        $sql .= $this->sqlWhere;
        if(!array_key_exists('SuperAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) && !array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS'])){
            if(array_key_exists('RareSppReader',$GLOBALS['USER_RIGHTS'])){
                $sql .= ' AND (o.CollId IN (' .implode(',',$GLOBALS['USER_RIGHTS']['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ';
            }
            else{
                $sql .= ' AND (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL) ';
            }
        }
        $bottomLimit = ($pageRequest - 1)*$cntPerPage;
        $sql .= 'ORDER BY o.sciname, o.eventdate ';
        $sql .= 'LIMIT ' .$bottomLimit. ',' .$cntPerPage;
        //echo "<div>Spec sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        $canReadRareSpp = false;
        if(array_key_exists('SuperAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll', $GLOBALS['USER_RIGHTS'])){
            $canReadRareSpp = true;
        }
        while($r = $result->fetch_object()){
            $occId = $r->occid;
            $collId = $r->collid;
            $retArr[$occId]['i'] = SanitizerService::cleanOutStr($r->institutioncode);
            $retArr[$occId]['cat'] = SanitizerService::cleanOutStr($r->catalognumber);
            $retArr[$occId]['c'] = SanitizerService::cleanOutStr($r->collector);
            $retArr[$occId]['e'] = SanitizerService::cleanOutStr($r->eventdate);
            $retArr[$occId]['f'] = SanitizerService::cleanOutStr($r->family);
            $retArr[$occId]['s'] = SanitizerService::cleanOutStr($r->sciname);
            $retArr[$occId]['l'] = SanitizerService::cleanOutStr($r->locality);
            $retArr[$occId]['lat'] = SanitizerService::cleanOutStr($r->DecimalLatitude);
            $retArr[$occId]['lon'] = SanitizerService::cleanOutStr($r->DecimalLongitude);
            $retArr[$occId]['tid'] = SanitizerService::cleanOutStr($r->tid);
            $localitySecurity = $r->LocalitySecurity;
            if(!$localitySecurity || $canReadRareSpp
                || (array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['CollEditor'], true))
                || (array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS']) && in_array($collId, $GLOBALS['USER_RIGHTS']['RareSppReader'], true))){
                $retArr[$occId]['l'] = str_replace('.,',',',$r->locality);
            }
            else{
                $securityStr = '<span style="color:red;">Detailed locality information protected. ';
                if($r->localitysecurityreason){
                    $securityStr .= $r->localitysecurityreason;
                }
                else{
                    $securityStr .= 'This is typically done to protect rare or threatened species localities.';
                }
                $retArr[$occId]['l'] = $securityStr.'</span>';
            }
        }
        $result->close();
        return $retArr;
        //return $sql;
    }

    protected function setTableJoins(): string
    {
        $sqlJoin = '';
        if(array_key_exists('taxontype',$this->searchTermsArr) && ((int)$this->searchTermsArr['taxontype'] === 4 || (int)$this->searchTermsArr['taxontype'] === 5)) {
            $sqlJoin .= 'INNER JOIN taxaenumtree AS te ON o.tid = te.tid ';
        }
        if(array_key_exists('clid',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(array_key_exists('polyArr',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
        }
        if(strpos($this->sqlWhere,'MATCH(f.recordedby)') || strpos($this->sqlWhere,'MATCH(f.locality)')){
            $sqlJoin .= 'LEFT JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
        }
        if(array_key_exists('phuid',$this->searchTermsArr) || array_key_exists('imagetag',$this->searchTermsArr) || array_key_exists('imagekeyword',$this->searchTermsArr) || array_key_exists('uploaddate1',$this->searchTermsArr) || array_key_exists('imagetype',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN images AS i ON o.occid = i.occid ';
            $sqlJoin .= array_key_exists('phuid',$this->searchTermsArr) ? 'LEFT JOIN users AS u ON i.photographeruid = u.uid ' :'';
            $sqlJoin .= array_key_exists('imagetag',$this->searchTermsArr) ? 'LEFT JOIN imagetag AS it ON i.imgid = it.imgid ' :'';
            $sqlJoin .= array_key_exists('imagekeyword',$this->searchTermsArr) ? 'LEFT JOIN imagekeywords AS ik ON i.imgid = ik.imgid ' :'';
        }
        return $sqlJoin;
    }

    public function setSearchTermsArr($stArr): void
    {
        $this->searchTermsArr = $stArr;
    }

    public function setSqlWhere($whereStr): void
    {
        if($whereStr) {
            $whereStr .= 'AND ';
            $this->sqlWhere = $whereStr . '(o.sciname IS NOT NULL AND o.DecimalLatitude IS NOT NULL AND o.DecimalLongitude IS NOT NULL) ';
        }
    }

    public function getRecordCnt(): int
    {
        return $this->recordCount;
    }
}
