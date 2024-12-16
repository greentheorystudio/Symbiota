<?php
include_once(__DIR__ . '/../services/DataUtilitiesService.php');
include_once(__DIR__ . '/../services/DbService.php');

class UploadOccurrenceTemp{

	private $conn;

    private $fields = array(
        "upspid" => array("dataType" => "number", "length" => 50),
        "occid" => array("dataType" => "number", "length" => 10),
        "collid" => array("dataType" => "number", "length" => 10),
        "dbpk" => array("dataType" => "string", "length" => 150),
        "basisofrecord" => array("dataType" => "string", "length" => 32),
        "occurrenceid" => array("dataType" => "string", "length" => 255),
        "catalognumber" => array("dataType" => "string", "length" => 32),
        "othercatalognumbers" => array("dataType" => "string", "length" => 255),
        "ownerinstitutioncode" => array("dataType" => "string", "length" => 32),
        "institutionid" => array("dataType" => "string", "length" => 255),
        "collectionid" => array("dataType" => "string", "length" => 255),
        "datasetid" => array("dataType" => "string", "length" => 255),
        "institutioncode" => array("dataType" => "string", "length" => 64),
        "collectioncode" => array("dataType" => "string", "length" => 64),
        "family" => array("dataType" => "string", "length" => 255),
        "verbatimscientificname" => array("dataType" => "string", "length" => 255),
        "sciname" => array("dataType" => "string", "length" => 255),
        "tid" => array("dataType" => "number", "length" => 10),
        "genus" => array("dataType" => "string", "length" => 255),
        "specificepithet" => array("dataType" => "string", "length" => 255),
        "taxonrank" => array("dataType" => "string", "length" => 32),
        "infraspecificepithet" => array("dataType" => "string", "length" => 255),
        "scientificnameauthorship" => array("dataType" => "string", "length" => 255),
        "taxonremarks" => array("dataType" => "text", "length" => 0),
        "identifiedby" => array("dataType" => "string", "length" => 255),
        "dateidentified" => array("dataType" => "string", "length" => 45),
        "identificationreferences" => array("dataType" => "text", "length" => 0),
        "identificationremarks" => array("dataType" => "text", "length" => 0),
        "identificationqualifier" => array("dataType" => "string", "length" => 255),
        "typestatus" => array("dataType" => "string", "length" => 255),
        "recordedby" => array("dataType" => "string", "length" => 255),
        "recordnumber" => array("dataType" => "string", "length" => 32),
        "associatedcollectors" => array("dataType" => "string", "length" => 255),
        "eventdate" => array("dataType" => "date", "length" => 0),
        "eventtime" => array("dataType" => "string", "length" => 12),
        "year" => array("dataType" => "number", "length" => 10),
        "month" => array("dataType" => "number", "length" => 10),
        "day" => array("dataType" => "number", "length" => 10),
        "startdayofyear" => array("dataType" => "number", "length" => 10),
        "enddayofyear" => array("dataType" => "number", "length" => 10),
        "latestdatecollected" => array("dataType" => "date", "length" => 0),
        "verbatimeventdate" => array("dataType" => "string", "length" => 255),
        "habitat" => array("dataType" => "text", "length" => 0),
        "substrate" => array("dataType" => "string", "length" => 500),
        "fieldnotes" => array("dataType" => "text", "length" => 0),
        "fieldnumber" => array("dataType" => "string", "length" => 45),
        "eventid" => array("dataType" => "number", "length" => 11),
        "eventdbpk" => array("dataType" => "string", "length" => 150),
        "eventtype" => array("dataType" => "string", "length" => 255),
        "eventremarks" => array("dataType" => "text", "length" => 0),
        "occurrenceremarks" => array("dataType" => "text", "length" => 0),
        "informationwithheld" => array("dataType" => "string", "length" => 250),
        "datageneralizations" => array("dataType" => "string", "length" => 250),
        "associatedtaxa" => array("dataType" => "text", "length" => 0),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
        "verbatimattributes" => array("dataType" => "text", "length" => 0),
        "behavior" => array("dataType" => "string", "length" => 500),
        "reproductivecondition" => array("dataType" => "string", "length" => 255),
        "cultivationstatus" => array("dataType" => "number", "length" => 10),
        "establishmentmeans" => array("dataType" => "string", "length" => 32),
        "lifestage" => array("dataType" => "string", "length" => 45),
        "sex" => array("dataType" => "string", "length" => 45),
        "individualcount" => array("dataType" => "string", "length" => 45),
        "samplingprotocol" => array("dataType" => "string", "length" => 100),
        "samplingeffort" => array("dataType" => "string", "length" => 200),
        "rep" => array("dataType" => "number", "length" => 10),
        "preparations" => array("dataType" => "string", "length" => 100),
        "locationid" => array("dataType" => "number", "length" => 11),
        "island" => array("dataType" => "string", "length" => 75),
        "islandgroup" => array("dataType" => "string", "length" => 75),
        "waterbody" => array("dataType" => "string", "length" => 255),
        "continent" => array("dataType" => "string", "length" => 45),
        "locationname" => array("dataType" => "string", "length" => 255),
        "locationcode" => array("dataType" => "string", "length" => 50),
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
        "latdeg" => array("dataType" => "number", "length" => 11),
        "latmin" => array("dataType" => "number", "length" => 0),
        "latsec" => array("dataType" => "number", "length" => 0),
        "latns" => array("dataType" => "string", "length" => 3),
        "lngdeg" => array("dataType" => "number", "length" => 11),
        "lngmin" => array("dataType" => "number", "length" => 0),
        "lngsec" => array("dataType" => "number", "length" => 0),
        "lngew" => array("dataType" => "string", "length" => 3),
        "verbatimlatitude" => array("dataType" => "string", "length" => 45),
        "verbatimlongitude" => array("dataType" => "string", "length" => 45),
        "utmnorthing" => array("dataType" => "string", "length" => 45),
        "utmeasting" => array("dataType" => "string", "length" => 45),
        "utmzoning" => array("dataType" => "string", "length" => 45),
        "trstownship" => array("dataType" => "string", "length" => 45),
        "trsrange" => array("dataType" => "string", "length" => 45),
        "trssection" => array("dataType" => "string", "length" => 45),
        "trssectiondetails" => array("dataType" => "string", "length" => 45),
        "georeferencedby" => array("dataType" => "string", "length" => 255),
        "georeferenceprotocol" => array("dataType" => "string", "length" => 255),
        "georeferencesources" => array("dataType" => "string", "length" => 255),
        "georeferenceverificationstatus" => array("dataType" => "string", "length" => 32),
        "georeferenceremarks" => array("dataType" => "string", "length" => 255),
        "minimumelevationinmeters" => array("dataType" => "number", "length" => 6),
        "maximumelevationinmeters" => array("dataType" => "number", "length" => 6),
        "verbatimelevation" => array("dataType" => "string", "length" => 255),
        "minimumdepthinmeters" => array("dataType" => "number", "length" => 11),
        "maximumdepthinmeters" => array("dataType" => "number", "length" => 11),
        "verbatimdepth" => array("dataType" => "string", "length" => 50),
        "disposition" => array("dataType" => "string", "length" => 32),
        "storagelocation" => array("dataType" => "string", "length" => 100),
        "exsiccatiidentifier" => array("dataType" => "number", "length" => 11),
        "exsiccatinumber" => array("dataType" => "string", "length" => 45),
        "exsiccatinotes" => array("dataType" => "string", "length" => 250),
        "language" => array("dataType" => "string", "length" => 20),
        "duplicatequantity" => array("dataType" => "number", "length" => 10),
        "repcount" => array("dataType" => "number", "length" => 10),
        "labelproject" => array("dataType" => "string", "length" => 45),
        "processingstatus" => array("dataType" => "string", "length" => 45),
        "tempfield01" => array("dataType" => "text", "length" => 0),
        "tempfield02" => array("dataType" => "text", "length" => 0),
        "tempfield03" => array("dataType" => "text", "length" => 0),
        "tempfield04" => array("dataType" => "text", "length" => 0),
        "tempfield05" => array("dataType" => "text", "length" => 0),
        "tempfield06" => array("dataType" => "text", "length" => 0),
        "tempfield07" => array("dataType" => "text", "length" => 0),
        "tempfield08" => array("dataType" => "text", "length" => 0),
        "tempfield09" => array("dataType" => "text", "length" => 0),
        "tempfield10" => array("dataType" => "text", "length" => 0),
        "tempfield11" => array("dataType" => "text", "length" => 0),
        "tempfield12" => array("dataType" => "text", "length" => 0),
        "tempfield13" => array("dataType" => "text", "length" => 0),
        "tempfield14" => array("dataType" => "text", "length" => 0),
        "tempfield15" => array("dataType" => "text", "length" => 0),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateRecords($collid, $data, $processingStatus, $fieldMapping =  null): int
    {
        $recordsCreated = 0;
        $fieldNameArr = array();
        $valueArr = array();
        $skipFields = array('upspid', 'occid', 'collid', 'institutionid', 'collectionid', 'datasetid', 'tid', 'eventid', 'locationid', 'initialtimestamp');
        $mappedFields = array();
        if($collid){
            $fieldNameArr[] = 'collid';
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                    if($fieldMapping){
                        $mappedKey = array_search($field, $fieldMapping, true);
                        if($mappedKey){
                            $mappedFields[$field] = $mappedKey;
                        }
                    }
                    elseif(array_key_exists($field, $data[0])){
                        $mappedFields[$field] = $field;
                    }
                }
            }
            foreach($data as $dataArr){
                $dataValueArr = array();
                $occurrenceData = array();
                $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $collid, $this->fields['collid']);
                foreach($mappedFields as $field => $key){
                    $occurrenceData[$field] = $dataArr[$key];
                }
                if($processingStatus){
                    $occurrenceData['processingstatus'] = $processingStatus;
                }
                $occurrenceData = DataUtilitiesService::cleanOccurrenceData($occurrenceData);
                foreach($this->fields as $field => $fieldArr){
                    if(!in_array($field, $skipFields)){
                        $dataValue = $occurrenceData[$field] ?? null;
                        $dataValueArr[] = SanitizerService::getSqlValueString($this->conn, $dataValue, $fieldArr);
                    }
                }
                $valueArr[] = '(' . implode(',', $dataValueArr) . ')';
            }
            if(count($valueArr) > 0){
                $sql = 'INSERT INTO uploadspectemp(' . implode(',', $fieldNameArr) . ') '.
                    'VALUES ' . implode(',', $valueArr) . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $recordsCreated = $this->conn->affected_rows;
                }
            }
        }
        return $recordsCreated;
    }

    public function clearCollectionData($collid): bool
    {
        if($collid){
            $sql = 'DELETE FROM uploadspectemppoints WHERE collid = ' . (int)$collid . ' ';
            if($this->conn->query($sql)){
                $sql = 'DELETE FROM uploadspectemp WHERE collid = ' . (int)$collid . ' ';
                if($this->conn->query($sql)){
                    return true;
                }
            }
        }
        return false;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
