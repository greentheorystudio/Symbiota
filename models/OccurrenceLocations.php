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
        "island" => array("dataType" => "string", "length" => 75),
        "islandgroup" => array("dataType" => "string", "length" => 75),
        "waterbody" => array("dataType" => "string", "length" => 255),
        "continent" => array("dataType" => "string", "length" => 45),
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
        $this->conn->close();
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
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $label = '';
                if($row['locationcode'] || $row['locationname']){
                    if($row['locationcode']){
                        $label .= $row['locationcode'] . ($row['locationname'] ? ':' : '');
                    }
                    if($row['locationname']){
                        $label .= $row['locationname'];
                    }
                    $label .= '; ';
                }
                if($row['country'] || $row['stateprovince'] || $row['county']){
                    if($row['country']){
                        $label .= $row['country'] . (($row['stateprovince'] || $row['county']) ? ', ' : '');
                    }
                    if($row['stateprovince']){
                        $label .= $row['stateprovince'] . ($row['county'] ? ', ' : '');
                    }
                    if($row['county']){
                        $label .= $row['county'];
                    }
                    if($row['decimallatitude'] && $row['decimallongitude']){
                        $label .= '; ';
                    }
                }
                if($row['decimallatitude'] && $row['decimallongitude']){
                    $label .= $row['decimallatitude'] . ', ' . $row['decimallongitude'];
                }
                $dataArr = array();
                $dataArr['id'] = $row['locationid'];
                $dataArr['label'] = $label;
                $dataArr['locationname'] = $row['locationname'];
                $dataArr['locationcode'] = $row['locationcode'];
                $dataArr['country'] = $row['country'];
                $dataArr['stateprovince'] = $row['stateprovince'];
                $dataArr['county'] = $row['county'];
                $dataArr['decimallatitude'] = $row['decimallatitude'];
                $dataArr['decimallongitude'] = $row['decimallongitude'];
                $retArr[] = $dataArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getLocationData($locationid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurlocations WHERE locationid = ' . (int)$locationid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $row[$name];
                }
            }
        }
        return $retArr;
    }

    public function getNearbyLocationArr($collid, $locationid, $decimalLatitude, $decimalLongitude): array
    {
        $retArr = array();
        $sql = 'SELECT locationid, locationname, locationcode, country, stateprovince, county, locality, decimallatitude, decimallongitude, '.
            'geodeticdatum, coordinateuncertaintyinmeters, locationremarks, verbatimcoordinates, minimumelevationinmeters, '.
            'maximumelevationinmeters, verbatimelevation '.
            'FROM omoccurlocations WHERE collid = ' . (int)$collid . ' '.
            'AND ((3959 * ACOS(COS(RADIANS(decimallatitude)) * COS(RADIANS(' . $decimalLatitude . ')) * COS(RADIANS(' . $decimalLongitude . ') - RADIANS(decimallongitude)) + SIN(RADIANS(decimallatitude)) * SIN(RADIANS(' . $decimalLatitude . ')))) <= 10.955849477517672) ';
        if($locationid){
            $sql .= 'AND locationid <> ' . $locationid . ' ';
        }
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getLocationFields(): array
    {
        return $this->fields;
    }

    public function searchLocations($collid, $criteria): array
    {
        $retArr = array();
        $sqlWhereArr = array();
        if($criteria['country']){
            $sqlWhereArr[] = '(country = "' . SanitizerService::cleanInStr($this->conn, $criteria['country']) . '")';
        }
        if($criteria['stateprovince']){
            $sqlWhereArr[] = '(stateprovince = "' . SanitizerService::cleanInStr($this->conn, $criteria['stateprovince']) . '")';
        }
        if($criteria['county']){
            $sqlWhereArr[] = '(county = "' . SanitizerService::cleanInStr($this->conn, $criteria['county']) . '")';
        }
        if($criteria['locality']){
            $sqlWhereArr[] = '(locality REGEXP "' . SanitizerService::cleanInStr($this->conn, $criteria['locality']) . '")';
        }
        if($criteria['decimallatitude']){
            $sqlWhereArr[] = '(decimallatitude = ' . SanitizerService::cleanInStr($this->conn, $criteria['decimallatitude']) . ')';
        }
        if($criteria['decimallongitude']){
            $sqlWhereArr[] = '(decimallongitude = ' . SanitizerService::cleanInStr($this->conn, $criteria['decimallongitude']) . ')';
        }
        $sql = 'SELECT locationid, locationname, locationcode, country, stateprovince, county, locality, decimallatitude, decimallongitude, '.
            'geodeticdatum, coordinateuncertaintyinmeters, locationremarks, verbatimcoordinates, minimumelevationinmeters, '.
            'maximumelevationinmeters, verbatimelevation '.
            'FROM omoccurlocations WHERE collid = ' . (int)$collid . ' '.
            'AND ' . implode(' AND ', $sqlWhereArr);
        //echo '<div>'.$sql.'</div>';
        if($result = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($result);
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $row[$name];
                }
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
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
                'WHERE locationid = ' . (int)$locationId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
                foreach($this->fields as $field => $fieldArr){
                    if($field !== 'locationname' && $field !== 'locationcode' && array_key_exists($field, $editData)){
                        if(in_array($field, $this->collectingEventOverlapFields)){
                            $sqlOcc = 'UPDATE omoccurrences AS o LEFT JOIN omoccurcollectingevents AS e ON o.eventid = e.eventid '.
                                'SET o.' . $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']) . ' '.
                                'WHERE o.locationid = ' . (int)$locationId . ' AND (ISNULL(o.eventid) OR ISNULL(e.' . $field . ')) ';
                        }
                        else{
                            $sqlOcc = 'UPDATE omoccurrences SET ' . $field . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']) . ' '.
                                'WHERE locationid = ' . (int)$locationId . ' ';
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
