<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceLocations{

	private $conn;

    private $collectingEventOverlapFields = array(
        "localitysecurity",
        "localitysecurityreason",
        "decimallatitude",
        "decimallongitude",
        "geodeticdatum",
        "coordinateuncertaintyinmeters",
        "footprintwkt",
        "georeferencedby",
        "georeferenceprotocol",
        "georeferencesources",
        "georeferenceverificationstatus",
        "georeferenceremarks"
    );

    private $fields = array(
        "locationid" => array("dataType" => "number", "length" => 11),
        "collid" => array("dataType" => "number", "length" => 10),
        "locationname" => array("dataType" => "string", "length" => 255),
        "locationcode" => array("dataType" => "string", "length" => 50),
        "waterbody" => array("dataType" => "string", "length" => 255),
        "country" => array("dataType" => "string", "length" => 64),
        "stateprovince" => array("dataType" => "string", "length" => 255),
        "county" => array("dataType" => "string", "length" => 255),
        "municipality" => array("dataType" => "string", "length" => 255),
        "locality" => array("dataType" => "text", "length" => 0),
        "localitysecurity" => array("dataType" => "number", "length" => 10),
        "localitysecurityreason" => array("dataType" => "string", "length" => 100),
        "decimallatitude" => array("dataType" => "number", "length" => 0),
        "decimallongitude" => array("dataType" => "number", "length" => 0),
        "geodeticdatum" => array("dataType" => "string", "length" => 255),
        "coordinateuncertaintyinmeters" => array("dataType" => "number", "length" => 10),
        "footprintwkt" => array("dataType" => "text", "length" => 0),
        "coordinateprecision" => array("dataType" => "number", "length" => 9),
        "locationremarks" => array("dataType" => "text", "length" => 0),
        "verbatimcoordinates" => array("dataType" => "string", "length" => 255),
        "verbatimcoordinatesystem" => array("dataType" => "string", "length" => 255),
        "georeferencedby" => array("dataType" => "string", "length" => 255),
        "georeferenceprotocol" => array("dataType" => "string", "length" => 255),
        "georeferencesources" => array("dataType" => "string", "length" => 255),
        "georeferenceverificationstatus" => array("dataType" => "string", "length" => 32),
        "georeferenceremarks" => array("dataType" => "string", "length" => 500),
        "minimumelevationinmeters" => array("dataType" => "number", "length" => 6),
        "maximumelevationinmeters" => array("dataType" => "number", "length" => 6),
        "verbatimelevation" => array("dataType" => "string", "length" => 255)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function createLocationRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $collId = array_key_exists('collid',$data) ? (int)$data['collid'] : 0;
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $data)){
                    $fieldNameArr[] = $field;
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'INSERT INTO omoccurlocations(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function getAutocompleteLocationList($collid, $key, $queryString): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT locationid, locationname, locationcode, country, stateprovince, county, decimallatitude, decimallongitude '.
            'FROM omoccurlocations WHERE collid = ' . (int)$collid . ' ';
        if($key === 'code'){
            $sql .= 'AND locationcode LIKE "' . SanitizerService::cleanInStr($this->conn, $queryString) . '%" ';
        }
        if($key === 'name'){
            $sql .= 'AND locationname LIKE "' . SanitizerService::cleanInStr($this->conn, $queryString) . '%" ';
        }
        $sql .= 'ORDER BY locationcode, locationname, country, stateprovince, county LIMIT 10 ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $label = '';
            if($r->locationcode || $r->locationname){
                if($r->locationcode){
                    $label .= $r->locationcode . ($r->locationname ? ':' : '');
                }
                if($r->locationname){
                    $label .= $r->locationname;
                }
                $label .= '; ';
            }
            if($r->country || $r->stateprovince || $r->county){
                if($r->country){
                    $label .= $r->country . (($r->stateprovince || $r->county) ? ', ' : '');
                }
                if($r->stateprovince){
                    $label .= $r->stateprovince . ($r->county ? ', ' : '');
                }
                if($r->county){
                    $label .= $r->county;
                }
                if($r->decimallatitude && $r->decimallongitude){
                    $label .= '; ';
                }
            }
            if($r->decimallatitude && $r->decimallongitude){
                $label .= $r->decimallatitude . ', ' . $r->decimallongitude;
            }
            $dataArr = array();
            $dataArr['id'] = $r->locationid;
            $dataArr['label'] = $label;
            $dataArr['locationname'] = $r->locationname;
            $dataArr['locationcode'] = $r->locationcode;
            $dataArr['country'] = $r->country;
            $dataArr['stateprovince'] = $r->stateprovince;
            $dataArr['county'] = $r->county;
            $dataArr['decimallatitude'] = $r->decimallatitude;
            $dataArr['decimallongitude'] = $r->decimallongitude;
            $retArr[] = $dataArr;
        }

        return $retArr;
    }

    public function getLocationData($locationid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurlocations WHERE locationid = ' . $locationid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getNearbyLocationArr($collid, $locationid, $decimalLatitude, $decimalLongitude): array
    {
        $retArr = array();
        $sql = 'SELECT locationid, locationname, locationcode, country, stateprovince, county, locality, decimallatitude, decimallongitude, '.
            'geodeticdatum, coordinateuncertaintyinmeters, locationremarks, verbatimcoordinates, minimumelevationinmeters, '.
            'maximumelevationinmeters, verbatimelevation '.
            'FROM omoccurlocations WHERE collid = ' . $collid . ' '.
            'AND ((3959 * ACOS(COS(RADIANS(decimallatitude)) * COS(RADIANS(' . $decimalLatitude . ')) * COS(RADIANS(' . $decimalLongitude . ') - RADIANS(decimallongitude)) + SIN(RADIANS(decimallatitude)) * SIN(RADIANS(' . $decimalLatitude . ')))) <= 10.955849477517672) ';
        if($locationid){
            $sql .= 'AND locationid <> ' . $locationid . ' ';
        }
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getLocationFields(): array
    {
        return $this->fields;
    }

    public function updateLocationRecord($locationId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($locationId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    $sqlPartArr[] = $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE omoccurlocations SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE locationid = ' . $locationId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
                foreach($this->fields as $field => $fieldArr){
                    if($field !== 'locationname' && $field !== 'locationcode' && array_key_exists($field, $editData)){
                        if(in_array($field, $this->collectingEventOverlapFields)){
                            $sqlOcc = 'UPDATE omoccurrences AS o LEFT JOIN omoccurcollectingevents AS e ON o.eventid = e.eventid '.
                                'SET o.' . $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']) . ' '.
                                'WHERE o.locationid = ' . $locationId . ' AND (ISNULL(o.eventid) OR ISNULL(e.' . $field . ')) ';
                        }
                        else{
                            $sqlOcc = 'UPDATE omoccurrences SET ' . $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']) . ' '.
                                'WHERE locationid = ' . $locationId . ' ';
                        }
                        if(!$this->conn->query($sqlOcc)){
                            $retVal = 0;
                        }
                    }
                }
            }
        }
        return $retVal;
    }
}
