<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class SpatialModuleManager{

    protected $conn;
    protected $searchTermsArr = array();
    protected $recordCount = 0;
    private $sqlWhere = '';
    private $taxaArr = array();

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function getOccStrFromGeoJSON($json): string{
        $occArr = array();
        $jsonArr = json_decode($json, true);
        $featureArr = $jsonArr['features'];
        foreach($featureArr as $f => $data){
            $occArr[] = $data['properties']['occid'];
        }
        return implode(',',$occArr);
    }

    public function getLayersConfigJSON(): string
    {
        $returnStr = '';
        if(file_exists($GLOBALS['SERVER_ROOT'].'/content/json/spatiallayerconfig.json')){
            $returnStr = file_get_contents($GLOBALS['SERVER_ROOT'].'/content/json/spatiallayerconfig.json');
        }
        return $returnStr;
    }

    public function writeGPXFromGeoJSON($json): string{
        $returnStr = '<gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
            'xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" version="1.1" creator="Symbiota">';
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
        $sql = 'SELECT DISTINCT o.occid, o.collid, o.family, o.sciname, o.tidinterpreted, o.`year`, o.`month`, o.`day`, '.
            'o.decimalLatitude, o.decimalLongitude, c.CollectionName, c.CollType, ts.family AS accFamily, '.
            'c.InstitutionCode, o.catalogNumber, o.recordedBy, o.recordNumber, o.eventDate AS displayDate '.
            'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'INNER JOIN taxa AS t ON o.tidinterpreted = t.TID '.
            'INNER JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid ';
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
            $geoArr['properties']['CollType'] = utf8_encode($row->CollType);
            $geoArr['properties']['collid'] = utf8_encode($row->collid);
            $geoArr['properties']['coll_year'] = utf8_encode($row->year);
            $geoArr['properties']['tidinterpreted'] = utf8_encode($row->tidinterpreted);
            $geoArr['properties']['coll_day'] = utf8_encode($row->day);
            $geoArr['properties']['occid'] = utf8_encode($row->occid);
            $geoArr['properties']['CollectionName'] = utf8_encode($row->CollectionName);
            $geoArr['properties']['sciname'] = utf8_encode($row->sciname);
            $geoArr['properties']['family'] = utf8_encode($row->family);
            $geoArr['properties']['accFamily'] = utf8_encode($row->accFamily);
            $geoArr['properties']['coll_month'] = utf8_encode($row->month);
            $geoArr['properties']['InstitutionCode'] = utf8_encode($row->InstitutionCode);
            $geoArr['properties']['catalogNumber'] = utf8_encode($row->catalogNumber);
            $geoArr['properties']['recordedBy'] = utf8_encode($row->recordedBy);
            $geoArr['properties']['recordNumber'] = utf8_encode($row->recordNumber);
            $geoArr['properties']['displayDate'] = utf8_encode($row->displayDate);
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
            'c.InstitutionCode, c.CollectionCode, c.CollectionName, IFNULL(ts.family,o.family) AS family, o.fieldnumber, '.
            'o.occurrenceRemarks, o.dynamicProperties, o.reproductiveCondition, o.lifeStage, o.sex, o.individualCount '.
            'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'INNER JOIN taxa AS t ON o.tidinterpreted = t.TID '.
            'INNER JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid ';
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
            $geoArr['properties']['id'] = utf8_encode($row->occid);
            $geoArr['properties']['collid'] = utf8_encode($row->collid);
            $geoArr['properties']['basisOfRecord'] = utf8_encode($row->basisOfRecord);
            $geoArr['properties']['occurrenceID'] = utf8_encode($row->occurrenceID);
            $geoArr['properties']['catalogNumber'] = utf8_encode($row->catalogNumber);
            $geoArr['properties']['otherCatalogNumbers'] = utf8_encode($row->otherCatalogNumbers);
            $geoArr['properties']['InstitutionCode'] = utf8_encode($row->InstitutionCode);
            $geoArr['properties']['CollectionCode'] = utf8_encode($row->CollectionCode);
            $geoArr['properties']['CollectionName'] = utf8_encode($row->CollectionName);
            $geoArr['properties']['family'] = utf8_encode($row->family);
            $geoArr['properties']['sciname'] = utf8_encode($row->sciname);
            $geoArr['properties']['scientificNameAuthorship'] = utf8_encode($row->scientificNameAuthorship);
            $geoArr['properties']['identifiedBy'] = utf8_encode($row->identifiedBy);
            $geoArr['properties']['dateIdentified'] = utf8_encode($row->dateIdentified);
            $geoArr['properties']['identificationQualifier'] = utf8_encode($row->identificationQualifier);
            $geoArr['properties']['typeStatus'] = utf8_encode($row->typeStatus);
            $geoArr['properties']['recordedBy'] = utf8_encode($row->recordedBy);
            $geoArr['properties']['recordNumber'] = utf8_encode($row->recordNumber);
            $geoArr['properties']['associatedCollectors'] = utf8_encode($row->associatedCollectors);
            $geoArr['properties']['eventdate'] = utf8_encode($row->eventdate);
            $geoArr['properties']['year'] = utf8_encode($row->year);
            $geoArr['properties']['month'] = utf8_encode($row->month);
            $geoArr['properties']['day'] = utf8_encode($row->day);
            $geoArr['properties']['habitat'] = utf8_encode($row->habitat);
            $geoArr['properties']['substrate'] = utf8_encode($row->substrate);
            $geoArr['properties']['fieldnumber'] = utf8_encode($row->fieldnumber);
            $geoArr['properties']['occurrenceRemarks'] = utf8_encode($row->occurrenceRemarks);
            $geoArr['properties']['associatedTaxa'] = utf8_encode($row->associatedTaxa);
            $geoArr['properties']['dynamicProperties'] = utf8_encode($row->dynamicProperties);
            $geoArr['properties']['reproductiveCondition'] = utf8_encode($row->reproductiveCondition);
            $geoArr['properties']['lifeStage'] = utf8_encode($row->lifeStage);
            $geoArr['properties']['sex'] = utf8_encode($row->sex);
            $geoArr['properties']['individualCount'] = utf8_encode($row->individualCount);
            $geoArr['properties']['country'] = utf8_encode($row->country);
            $geoArr['properties']['stateProvince'] = utf8_encode($row->stateProvince);
            $geoArr['properties']['county'] = utf8_encode($row->county);
            $geoArr['properties']['municipality'] = utf8_encode($row->municipality);
            $geoArr['properties']['locality'] = utf8_encode($row->locality);
            $geoArr['properties']['geodeticDatum'] = utf8_encode($row->geodeticDatum);
            $geoArr['properties']['coordinateUncertaintyInMeters'] = utf8_encode($row->coordinateUncertaintyInMeters);
            $geoArr['properties']['minimumElevationInMeters'] = utf8_encode($row->minimumElevationInMeters);
            $geoArr['properties']['maximumElevationInMeters'] = utf8_encode($row->maximumElevationInMeters);
            $geoArr['properties']['minimumDepthInMeters'] = utf8_encode($row->minimumDepthInMeters);
            $geoArr['properties']['maximumDepthInMeters'] = utf8_encode($row->maximumDepthInMeters);
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
        $sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt FROM omoccurrences AS o INNER JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid '.
            'INNER JOIN taxa AS t ON o.tidinterpreted = t.TID ';
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

    public function getMapRecordPageArr($pageRequest,$cntPerPage): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, c.institutioncode, o.catalognumber, CONCAT_WS(" ",o.recordedby,o.recordnumber) AS collector, '.
            'o.eventdate, o.family, o.sciname, o.tidinterpreted, CONCAT_WS("; ",o.country, o.stateProvince, o.county) AS locality, o.DecimalLatitude, o.DecimalLongitude, '.
            'IFNULL(o.LocalitySecurity,0) AS LocalitySecurity, o.localitysecurityreason '.
            'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'INNER JOIN taxa AS t ON o.tidinterpreted = t.TID '.
            'INNER JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid ';
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
            $retArr[$occId]['i'] = Sanitizer::cleanOutStr($r->institutioncode);
            $retArr[$occId]['cat'] = Sanitizer::cleanOutStr($r->catalognumber);
            $retArr[$occId]['c'] = Sanitizer::cleanOutStr($r->collector);
            $retArr[$occId]['e'] = Sanitizer::cleanOutStr($r->eventdate);
            $retArr[$occId]['f'] = Sanitizer::cleanOutStr($r->family);
            $retArr[$occId]['s'] = Sanitizer::cleanOutStr($r->sciname);
            $retArr[$occId]['l'] = Sanitizer::cleanOutStr($r->locality);
            $retArr[$occId]['lat'] = Sanitizer::cleanOutStr($r->DecimalLatitude);
            $retArr[$occId]['lon'] = Sanitizer::cleanOutStr($r->DecimalLongitude);
            $retArr[$occId]['tid'] = Sanitizer::cleanOutStr($r->tidinterpreted);
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
        if(array_key_exists('taxontype',$this->searchTermsArr) && (int)$this->searchTermsArr['taxontype'] === 4) {
            $sqlJoin .= 'INNER JOIN taxaenumtree AS te ON o.tidinterpreted = te.tid ';
        }
        if(array_key_exists('clid',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(array_key_exists('assochost',$this->searchTermsArr)) {
            $sqlJoin .= 'LEFT JOIN omoccurassociations AS oas ON o.occid = oas.occid ';
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
        }
        else {
            $whereStr = 'WHERE ';
        }
        $this->sqlWhere = $whereStr . '(o.sciname IS NOT NULL AND o.DecimalLatitude IS NOT NULL AND o.DecimalLongitude IS NOT NULL) ';
    }

    public function getRecordCnt(): int
    {
        return $this->recordCount;
    }
}
