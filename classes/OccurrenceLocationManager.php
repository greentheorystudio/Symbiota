<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceLocationManager{

	private $conn;

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
        $connection = new DbConnection();
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
                    if($field === 'year' || $field === 'month' || $field === 'day'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                    $fieldValueArr[] = Sanitizer::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
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

    public function getLocationData($locationid): array
    {
        $retArr = array();
        $sql = 'SELECT l.locationname, l.locationcode, l.waterbody, l.country, l.stateprovince, l.county, l.municipality, l.locality, '.
            'l.localitysecurity, l.localitysecurityreason, l.decimallatitude, l.decimallongitude, l.geodeticdatum, l.coordinateuncertaintyinmeters, '.
            'l.footprintwkt, l.coordinateprecision, l.locationremarks, l.verbatimcoordinates, l.verbatimcoordinatesystem, l.georeferencedby, '.
            'l.georeferenceprotocol, l.georeferencesources, l.georeferenceverificationstatus, l.georeferenceremarks, '.
            'l.minimumelevationinmeters, l.maximumelevationinmeters, l.verbatimelevation, l.initialtimestamp '.
            'FROM omoccurlocations AS l '.
            'WHERE l.locationID = ' . $locationid . ' ';
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
                    if($field === 'year' || $field === 'month' || $field === 'day'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = $fieldStr . ' = ' . Sanitizer::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE omoccurlocations SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE locationid = ' . $locationId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
