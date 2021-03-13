<?php
include_once(__DIR__ . '/DbConnection.php');

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
        if(!($this->conn === false)) {
            $this->conn->close();
        }
    }

    public function getLayersArr(): array{
        global $GEOSERVER_URL, $GEOSERVER_LAYER_WORKSPACE;
        $url = $GEOSERVER_URL.'/wms?service=wms&version=2.0.0&request=GetCapabilities';
        $xml = simplexml_load_string(file_get_contents($url));
        $layers = $xml->Capability->Layer->Layer;
        $retArr = Array();
        foreach ($layers as $l){
            $nameArr = explode(':',(string)$l->Name);
            $workspace = $nameArr[0];
            $layername = $nameArr[1];
            if($workspace === $GEOSERVER_LAYER_WORKSPACE){
                $i = strtolower((string)$l->Title);
                $retArr[$i]['Name'] = $layername;
                $retArr[$i]['Title'] = (string)$l->Title;
                $retArr[$i]['Abstract'] = (string)$l->Abstract;
                $crsArr = $l->CRS;
                foreach ($crsArr as $c){
                    if(strpos($c, 'EPSG:') !== false) {
                        $retArr[$i]['DefaultCRS'] = (string)$c;
                    }
                }
                $keywordArr = $l->KeywordList->Keyword;
                foreach ($keywordArr as $k){
                    if($k === 'features') {
                        $retArr[$i]['layerType'] = 'vector';
                    }
                    elseif($k === 'GeoTIFF') {
                        $retArr[$i]['layerType'] = 'raster';
                    }
                }
                $retArr[$i]['legendUrl'] = (string)$l->Style->LegendURL->OnlineResource->attributes('xlink', TRUE)->href;
            }
        }
        ksort($retArr);

        return $retArr;
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

    public function getSynonyms($searchTarget,$taxAuthId = 1): array{
        $synArr = array();
        $targetTidArr = array();
        $searchStr = '';
        if(is_array($searchTarget)){
            if(is_numeric(current($searchTarget))){
                $targetTidArr[] = $searchTarget;
            }
            else{
                $searchStr = implode('","',$searchTarget);
            }
        }
        else if(is_numeric($searchTarget)){
            $targetTidArr[] = $searchTarget;
        }
        else{
            $searchStr = $searchTarget;
        }
        if($searchStr){
            $sql1 = 'SELECT tid FROM taxa WHERE sciname IN("'.$searchStr.'")';
            $rs1 = $this->conn->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $targetTidArr[] = $r1->tid;
            }
            $rs1->free();
        }

        if($targetTidArr){
            $accArr = array();
            $rankId = 0;
            $sql2 = 'SELECT DISTINCT t.tid, t.sciname, t.rankid '.
                'FROM taxa t INNER JOIN taxstatus ts ON t.Tid = ts.TidAccepted '.
                'WHERE (ts.taxauthid = '.$taxAuthId.') AND (ts.tid IN('.implode(',',$targetTidArr).')) ';
            $rs2 = $this->conn->query($sql2);
            while($r2 = $rs2->fetch_object()){
                $accArr[] = $r2->tid;
                $rankId = $r2->rankid;
                $synArr[$r2->tid] = $r2->sciname;
            }
            $rs2->free();

            if($accArr){
                $sql3 = 'SELECT DISTINCT t.tid, t.sciname ' .
                    'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid ' .
                    'WHERE (ts.taxauthid = ' . $taxAuthId . ') AND (ts.tidaccepted IN(' . implode('', $accArr) . ')) ';
                $rs3 = $this->conn->query($sql3);
                while ($r3 = $rs3->fetch_object()) {
                    $synArr[$r3->tid] = $r3->sciname;
                }
                $rs3->free();

                if ($rankId === 220) {
                    $sql4 = 'SELECT DISTINCT t.tid, t.sciname ' .
                        'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid ' .
                        'WHERE (ts.parenttid IN(' . implode('', $accArr) . ')) AND (ts.taxauthid = ' . $taxAuthId . ') ' .
                        'AND (ts.TidAccepted = ts.tid)';
                    $rs4 = $this->conn->query($sql4);
                    while ($r4 = $rs4->fetch_object()) {
                        $synArr[$r4->tid] = $r4->sciname;
                    }
                    $rs4->free();
                }
            }
        }
        return $synArr;
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
        global $USER_RIGHTS;
        $geomArr = array();
        $featuresArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, o.family, o.sciname, o.tidinterpreted, o.`year`, o.`month`, o.`day`, '.
            'o.decimalLatitude, o.decimalLongitude, c.CollectionName, c.CollType, ts.family AS accFamily, '.
            'c.InstitutionCode, o.catalogNumber, o.recordedBy, o.recordNumber, o.eventDate AS displayDate '.
            'FROM omoccurrences AS o LEFT JOIN omcollections AS c ON o.collid = c.collid '.
            'LEFT JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid ';
        if(array_key_exists('polyArr',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
        }
        if(array_key_exists('clid',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(strpos($this->sqlWhere,'MATCH(f.recordedby)') || strpos($this->sqlWhere,'MATCH(f.locality)')) {
            $sql .= 'INNER JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
        }
        if(strpos($this->sqlWhere, 'WHERE ') !== 0){
            $sql .= 'WHERE ';
        }
        $sql .= $this->sqlWhere;
        if(!array_key_exists('SuperAdmin',$USER_RIGHTS) && !array_key_exists('CollAdmin',$USER_RIGHTS) && !array_key_exists('RareSppAdmin',$USER_RIGHTS) && !array_key_exists('RareSppReadAll',$USER_RIGHTS)){
            if(array_key_exists('RareSppReader',$USER_RIGHTS)){
                $sql .= ' AND (o.CollId IN (' .implode(',',$USER_RIGHTS['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR ISNULL(o.LocalitySecurity))) ';
            }
            else{
                $sql .= ' AND (o.LocalitySecurity = 0 OR ISNULL(o.LocalitySecurity)) ';
            }
        }
        $sql .= ' AND (ts.taxauthid = 1 OR ISNULL(ts.taxauthid)) ';
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
        global $USER_RIGHTS;
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
            'LEFT JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid ';
        if(array_key_exists('polyArr',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
        }
        if(array_key_exists('clid',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(strpos($this->sqlWhere,'MATCH(f.recordedby)') || strpos($this->sqlWhere,'MATCH(f.locality)')) {
            $sql .= 'INNER JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
        }
        if(strpos($this->sqlWhere, 'WHERE ') !== 0){
            $sql .= 'WHERE ';
        }
        $sql .= $this->sqlWhere;
        if(!array_key_exists('SuperAdmin',$USER_RIGHTS) && !array_key_exists('CollAdmin',$USER_RIGHTS) && !array_key_exists('RareSppAdmin',$USER_RIGHTS) && !array_key_exists('RareSppReadAll',$USER_RIGHTS)){
            if(array_key_exists('RareSppReader',$USER_RIGHTS)){
                $sql .= ' AND (o.CollId IN (' .implode(',',$USER_RIGHTS['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ';
            }
            else{
                $sql .= ' AND (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL) ';
            }
        }
        $sql .= ' AND (ts.taxauthid = 1 OR ISNULL(ts.taxauthid)) ';
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
        global $USER_RIGHTS;
        $sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt FROM omoccurrences o ';
        if(array_key_exists('polyArr',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN omoccurpoints p ON o.occid = p.occid ';
        }
        if(array_key_exists('clid',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(strpos($this->sqlWhere,'MATCH(f.recordedby)') || strpos($this->sqlWhere,'MATCH(f.locality)')) {
            $sql .= 'INNER JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
        }
        $sql .= $this->sqlWhere;
        if(!array_key_exists('SuperAdmin',$USER_RIGHTS) && !array_key_exists('CollAdmin',$USER_RIGHTS) && !array_key_exists('RareSppAdmin',$USER_RIGHTS) && !array_key_exists('RareSppReadAll',$USER_RIGHTS)){
            if(array_key_exists('RareSppReader',$USER_RIGHTS)){
                $sql .= ' AND (o.CollId IN (' .implode(',',$USER_RIGHTS['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ';
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
        global $USER_RIGHTS;
        $retArr = array();
        $sql = 'SELECT DISTINCT o.occid, o.collid, c.institutioncode, o.catalognumber, CONCAT_WS(" ",o.recordedby,o.recordnumber) AS collector, '.
            'o.eventdate, o.family, o.sciname, CONCAT_WS("; ",o.country, o.stateProvince, o.county) AS locality, o.DecimalLatitude, o.DecimalLongitude, '.
            'IFNULL(o.LocalitySecurity,0) AS LocalitySecurity, o.localitysecurityreason '.
            'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.collid ';
        if(array_key_exists('polyArr',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
        }
        if(array_key_exists('clid',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(strpos($this->sqlWhere,'MATCH(f.recordedby)') || strpos($this->sqlWhere,'MATCH(f.locality)')) {
            $sql .= 'INNER JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
        }
        $sql .= $this->sqlWhere;
        if(!array_key_exists('SuperAdmin',$USER_RIGHTS) && !array_key_exists('CollAdmin',$USER_RIGHTS) && !array_key_exists('RareSppAdmin',$USER_RIGHTS) && !array_key_exists('RareSppReadAll',$USER_RIGHTS)){
            if(array_key_exists('RareSppReader',$USER_RIGHTS)){
                $sql .= ' AND (o.CollId IN (' .implode(',',$USER_RIGHTS['RareSppReader']). ') OR (o.LocalitySecurity = 0 OR o.LocalitySecurity IS NULL)) ';
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
        if(array_key_exists('SuperAdmin', $USER_RIGHTS) || array_key_exists('CollAdmin', $USER_RIGHTS) || array_key_exists('RareSppAdmin', $USER_RIGHTS) || array_key_exists('RareSppReadAll', $USER_RIGHTS)){
            $canReadRareSpp = true;
        }
        while($r = $result->fetch_object()){
            $occId = $r->occid;
            $collId = $r->collid;
            $retArr[$occId]['i'] = $this->cleanOutStr($r->institutioncode);
            $retArr[$occId]['cat'] = $this->cleanOutStr($r->catalognumber);
            $retArr[$occId]['c'] = $this->cleanOutStr($r->collector);
            $retArr[$occId]['e'] = $this->cleanOutStr($r->eventdate);
            $retArr[$occId]['f'] = $this->cleanOutStr($r->family);
            $retArr[$occId]['s'] = $this->cleanOutStr($r->sciname);
            $retArr[$occId]['l'] = $this->cleanOutStr($r->locality);
            $retArr[$occId]['lat'] = $this->cleanOutStr($r->DecimalLatitude);
            $retArr[$occId]['lon'] = $this->cleanOutStr($r->DecimalLongitude);
            $localitySecurity = $r->LocalitySecurity;
            if(!$localitySecurity || $canReadRareSpp
                || (array_key_exists('CollEditor', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['CollEditor'], true))
                || (array_key_exists('RareSppReader', $USER_RIGHTS) && in_array($collId, $USER_RIGHTS['RareSppReader'], true))){
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

    private function formatDate($inDate){
        $inDate = trim($inDate);
        $retDate = '';
        $y=''; $m=''; $d='';
        if(preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/',$inDate)){
            $dateTokens = explode('-',$inDate);
            $y = $dateTokens[0];
            $m = $dateTokens[1];
            $d = $dateTokens[2];
        }
        elseif(preg_match('/^\d{1,2}\/*\d{0,2}\/\d{2,4}$/',$inDate)){
            $dateTokens = explode('/',$inDate);
            $m = $dateTokens[0];
            if(count($dateTokens) === 3){
                $d = $dateTokens[1];
                $y = $dateTokens[2];
            }
            else{
                $d = '00';
                $y = $dateTokens[1];
            }
        }
        elseif(preg_match('/^\d{0,2}\s*\D+\s*\d{2,4}$/',$inDate)){
            $dateTokens = explode(' ',$inDate);
            if(count($dateTokens) === 3){
                $y = $dateTokens[2];
                $mText = substr($dateTokens[1],0,3);
                $d = $dateTokens[0];
            }
            else{
                $y = $dateTokens[1];
                $mText = substr($dateTokens[0],0,3);
                $d = '00';
            }
            $mText = strtolower($mText);
            $mNames = Array('ene' =>1, 'jan' =>1, 'feb' =>2, 'mar' =>3, 'abr' =>4, 'apr' =>4, 'may' =>5, 'jun' =>6, 'jul' =>7, 'aug' =>8, 'sep' =>9, 'oct' =>10, 'nov' =>11, 'dec' =>12);
            $m = $mNames[$mText];
        }
        elseif(preg_match('/^\s*\d{4}\s*$/',$inDate)){
            $retDate = $inDate.'-00-00';
        }
        elseif($dateObj = strtotime($inDate)){
            $retDate = date('Y-m-d',$dateObj);
        }
        if(!$retDate && $y){
            if(strlen($y) === 2){
                if($y < 20){
                    $y = '20' .$y;
                }
                else{
                    $y = '19' .$y;
                }
            }
            if(strlen($m) === 1){
                $m = '0'.$m;
            }
            if(strlen($d) === 1){
                $d = '0'.$d;
            }
            $retDate = $y.'-'.$m.'-'.$d;
        }
        return $retDate;
    }

    protected function setSynonyms(): void
    {
        foreach($this->taxaArr as $key => $value){
            if(array_key_exists('scinames',$value)){
                if(!in_array('no records', $value['scinames'], true)){
                    $synArr = $this->getSynonyms($value['scinames']);
                    if($synArr) {
                        $this->taxaArr[$key]['synonyms'] = $synArr;
                    }
                }
            }
            else{
                $synArr = $this->getSynonyms($key);
                if($synArr) {
                    $this->taxaArr[$key]['synonyms'] = $synArr;
                }
            }
        }
    }

    protected function setSciNamesByVerns(): void
    {
        $sql = 'SELECT DISTINCT v.VernacularName, t.tid, t.sciname, ts.family, t.rankid ' .
            'FROM (taxstatus ts LEFT JOIN taxavernaculars v ON ts.TID = v.TID) ' .
            'LEFT JOIN taxa t ON t.TID = ts.tidaccepted ';
        $whereStr = '';
        foreach($this->taxaArr as $key => $value){
            $whereStr .= "OR v.VernacularName = '".$key."' ";
        }
        $sql .= 'WHERE (ts.taxauthid = 1) AND (' .substr($whereStr,3). ') ORDER BY t.rankid LIMIT 20';
        //echo "<div>sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        if($result->num_rows){
            while($row = $result->fetch_object()){
                $vernName = strtolower($row->VernacularName);
                if($row->rankid < 140){
                    $this->taxaArr[$vernName]['tid'][] = $row->tid;
                }
                elseif($row->rankid === 140){
                    $this->taxaArr[$vernName]['families'][] = $row->sciname;
                }
                else{
                    $this->taxaArr[$vernName]['scinames'][] = $row->sciname;
                }
            }
        }
        else{
            $this->taxaArr['no records']['scinames'][] = 'no records';
        }
        $result->close();
    }

    public function setSearchTermsArr($stArr): void
    {
        $this->searchTermsArr = $stArr;
    }

    public function setSqlWhere($whereStr): void
    {
        if(!$whereStr){
            $whereStr = 'WHERE ';
        }
        else{
            $whereStr .= 'AND ';
        }
        $this->sqlWhere = $whereStr . '(o.sciname IS NOT NULL AND o.DecimalLatitude IS NOT NULL AND o.DecimalLongitude IS NOT NULL) ';
    }

    public function getRecordCnt(): int
    {
        return $this->recordCount;
    }

    protected function cleanOutStr($str){
        return str_replace(array('"', "'"), array('&quot;', '&apos;'), $str);
    }
}
