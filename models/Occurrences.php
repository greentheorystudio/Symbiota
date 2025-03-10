<?php
include_once(__DIR__ . '/ChecklistVouchers.php');
include_once(__DIR__ . '/Images.php');
include_once(__DIR__ . '/Media.php');
include_once(__DIR__ . '/OccurrenceCollectingEvents.php');
include_once(__DIR__ . '/OccurrenceDeterminations.php');
include_once(__DIR__ . '/OccurrenceGeneticLinks.php');
include_once(__DIR__ . '/OccurrenceLocations.php');
include_once(__DIR__ . '/OccurrenceMeasurementsOrFacts.php');
include_once(__DIR__ . '/Permissions.php');
include_once(__DIR__ . '/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Occurrences{

	private $conn;

    private $fields = array(
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
        "recordnumber" => array("dataType" => "string", "length" => 45),
        "recordedbyid" => array("dataType" => "number", "length" => 20),
        "associatedcollectors" => array("dataType" => "string", "length" => 255),
        "eventdate" => array("dataType" => "date", "length" => 0),
        "latestdatecollected" => array("dataType" => "date", "length" => 0),
        "eventtime" => array("dataType" => "time", "length" => 0),
        "year" => array("dataType" => "number", "length" => 10),
        "month" => array("dataType" => "number", "length" => 10),
        "day" => array("dataType" => "number", "length" => 10),
        "startdayofyear" => array("dataType" => "number", "length" => 10),
        "enddayofyear" => array("dataType" => "number", "length" => 10),
        "verbatimeventdate" => array("dataType" => "string", "length" => 255),
        "habitat" => array("dataType" => "text", "length" => 0),
        "substrate" => array("dataType" => "string", "length" => 500),
        "fieldnotes" => array("dataType" => "text", "length" => 0),
        "fieldnumber" => array("dataType" => "string", "length" => 45),
        "eventid" => array("dataType" => "number", "length" => 11),
        "eventremarks" => array("dataType" => "text", "length" => 0),
        "occurrenceremarks" => array("dataType" => "text", "length" => 0),
        "informationwithheld" => array("dataType" => "string", "length" => 250),
        "datageneralizations" => array("dataType" => "string", "length" => 250),
        "associatedoccurrences" => array("dataType" => "text", "length" => 0),
        "associatedtaxa" => array("dataType" => "text", "length" => 0),
        "dynamicproperties" => array("dataType" => "text", "length" => 0),
        "verbatimattributes" => array("dataType" => "text", "length" => 0),
        "behavior" => array("dataType" => "string", "length" => 500),
        "reproductivecondition" => array("dataType" => "string", "length" => 255),
        "cultivationstatus" => array("dataType" => "number", "length" => 10),
        "establishmentmeans" => array("dataType" => "string", "length" => 150),
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
        "coordinateprecision" => array("dataType" => "number", "length" => 0),
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
        "verbatimelevation" => array("dataType" => "string", "length" => 255),
        "minimumdepthinmeters" => array("dataType" => "number", "length" => 0),
        "maximumdepthinmeters" => array("dataType" => "number", "length" => 0),
        "verbatimdepth" => array("dataType" => "string", "length" => 50),
        "disposition" => array("dataType" => "string", "length" => 250),
        "storagelocation" => array("dataType" => "string", "length" => 100),
        "language" => array("dataType" => "string", "length" => 20),
        "processingstatus" => array("dataType" => "string", "length" => 45),
        "duplicatequantity" => array("dataType" => "number", "length" => 10),
        "labelproject" => array("dataType" => "string", "length" => 250),
        "recordenteredby" => array("dataType" => "string", "length" => 250),
        "dateentered" => array("dataType" => "date", "length" => 0),
        "datelastmodified" => array("dataType" => "timestamp", "length" => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function batchCreateOccurrenceRecordGUIDs($collid): int
    {
        $returnVal = 1;
        $valueArr = array();
        $insertPrefix = 'INSERT INTO guidoccurrences(guid, occid) VALUES ';
        $sql = 'SELECT occid FROM omoccurrences WHERE collid = ' . (int)$collid . ' AND occid NOT IN(SELECT occid FROM guidoccurrences) ';
        if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $row){
                if($returnVal){
                    if(count($valueArr) === 5000){
                        $sql2 = $insertPrefix . implode(',', $valueArr);
                        if(!$this->conn->query($sql2)){
                            $returnVal = 0;
                        }
                        $valueArr = array();
                    }
                    if($row['occid']){
                        $guid = UuidService::getUuidV4();
                        $valueArr[] = '("' . $guid . '",' . $row['occid'] . ')';
                    }
                }
            }
            if($returnVal && count($valueArr) > 0){
                $sql2 = $insertPrefix . implode(',', $valueArr);
                $this->conn->query($sql2);
            }
        }
        return $returnVal;
    }

    public function cleanDoubleSpaceNames($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname, "  ", " ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "%  %" ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanInfraAbbrNames($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql1 = 'UPDATE omoccurrences '.
                'SET verbatimscientificname = sciname '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND (sciname LIKE "% ssp. %" OR sciname LIKE "% ssp %" OR '.
                'sciname LIKE "% subspec. %" OR sciname LIKE "% subspec %" OR sciname LIKE "% subspecies %" OR sciname LIKE "% subsp %" OR '.
                'sciname LIKE "% var %" OR sciname LIKE "% variety %" OR sciname LIKE "% forma %" OR sciname LIKE "% form %" OR '.
                'sciname LIKE "% fo. %" OR sciname LIKE "% fo %") ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," ssp. "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% ssp. %" ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," ssp "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% ssp %" ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspec. "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subspec. %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspec "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subspec %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspecies "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subspecies %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subsp "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subsp %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," var "," var. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% var %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," variety "," var. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% variety %" ';
            //echo $sql5;
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," forma "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% forma %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," form "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% form %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," fo. "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% fo. %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," fo "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% fo %" ';
            //echo $sql6;
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanQualifierNames($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql1 = 'UPDATE omoccurrences '.
                'SET verbatimscientificname = sciname '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND (sciname LIKE "% cf. %" OR sciname LIKE "% cf %" OR '.
                'sciname LIKE "% aff. %" OR sciname LIKE "% aff %") ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," cf. "," "), identificationQualifier = "cf." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% cf. %" ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," cf "," "), identificationQualifier = "cf." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% cf %" ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," aff. "," "), identificationQualifier = "aff." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% aff. %" ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," aff "," "), identificationQualifier = "aff." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% aff %" ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanQuestionMarks($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql1 = 'UPDATE omoccurrences '.
                'SET verbatimscientificname = sciname '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "%?%" ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname,"?","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "%?%" ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanSpNames($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql1 = 'UPDATE omoccurrences '.
                'SET verbatimscientificname = sciname '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND (sciname LIKE "% sp." OR sciname LIKE "% sp" OR '.
                'sciname LIKE "% sp. nov." OR sciname LIKE "% sp. nov" OR sciname LIKE "% sp nov." OR sciname LIKE "% sp nov" OR '.
                'sciname LIKE "% spp." OR sciname LIKE "% spp" OR sciname LIKE "% group") ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp." ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp" ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp. nov.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp. nov." ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp. nov","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp. nov" ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp nov.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp nov." ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp nov","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp nov" ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," spp.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% spp." ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," spp","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% spp" ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," group","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% group" ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function cleanTrimNames($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql = 'UPDATE omoccurrences '.
                'SET sciname = TRIM(sciname) '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND (sciname LIKE " %" OR sciname LIKE "% ") ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function clearSensitiveOccurrenceData($occurrenceObj): array
    {
        $obscurredFieldArr = array();
        $sensitiveFieldArr = array('recordnumber', 'locality', 'locationremarks', 'minimumelevationinmeters', 'date',
            'maximumelevationinmeters', 'verbatimelevation', 'decimallatitude', 'decimallongitude', 'geodeticdatum',
            'coordinateuncertaintyinmeters', 'footprintwkt', 'verbatimcoordinates', 'georeferenceremarks', 'georeferencedby',
            'georeferenceprotocol', 'georeferencesources', 'georeferenceverificationstatus', 'habitat', 'informationwithheld',
            'eventdate', 'eventtime', 'year', 'month', 'day', 'startdayofyear', 'enddayofyear', 'verbatimeventdate',
            'substrate', 'associatedtaxa');
        foreach($occurrenceObj as $field => $fieldVal){
            if($fieldVal && in_array(strtolower($field), $sensitiveFieldArr)){
                $occurrenceObj[$field] = '';
                $obscurredFieldArr[] = $field;
            }
        }
        if(array_key_exists('informationwithheld', $occurrenceObj)){
            $occurrenceObj['informationwithheld'] = 'Fields redacted: ' . implode(', ', $obscurredFieldArr);
        }
        elseif(array_key_exists('informationWithheld', $occurrenceObj)){
            $occurrenceObj['informationWithheld'] = 'Fields redacted: ' . implode(', ', $obscurredFieldArr);
        }
        return $occurrenceObj;
    }

    public function createOccurrenceRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $collId = array_key_exists('collid', $data) ? (int)$data['collid'] : 0;
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'occid' && array_key_exists($field, $data)){
                    if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $fieldNameArr[] = 'dateentered';
            $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
            $fieldNameArr[] = 'recordenteredby';
            $fieldValueArr[] = '"' . $GLOBALS['USERNAME'] . '"';
            $sql = 'INSERT INTO omoccurrences(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                $guid = UuidService::getUuidV4();
                $this->conn->query('UPDATE omcollectionstats SET recordcnt = recordcnt + 1 WHERE collid = ' . $collId);
                $this->conn->query('INSERT INTO guidoccurrences(guid, occid) VALUES("' . $guid . '",' . $newID . ')');
            }
        }
        return $newID;
    }

    public function createOccurrenceRecordsFromUploadData($collId): int
    {
        $skipFields = array('occid', 'recordedbyid', 'associatedoccurrences', 'recordenteredby', 'dateentered', 'datelastmodified');
        $retVal = 0;
        $fieldNameArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                }
            }
            if(count($fieldNameArr) > 0){
                $sql = 'INSERT INTO omoccurrences(' . implode(',', $fieldNameArr) . ',dateentered) '.
                    'SELECT ' . implode(',', $fieldNameArr) . ', "' . date('Y-m-d H:i:s') . '" FROM uploadspectemp '.
                    'WHERE collid = ' . (int)$collId . ' AND ISNULL(occid) ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
            }
        }
        return $retVal;
    }

    public function deleteOccurrenceRecord($idType, $id): int
    {
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ') ';
        }
        if($whereStr){
            $retVal = (new OccurrenceDeterminations)->deleteOccurrenceDeterminationRecords($idType, $id);
            if($retVal){
                $retVal = (new OccurrenceGeneticLinks)->deleteOccurrenceGeneticLinkageRecords($idType, $id);
            }
            if($retVal){
                $retVal = (new ChecklistVouchers)->deleteOccurrenceChecklistVoucherRecords($idType, $id);
            }
            if($retVal){
                $retVal = (new Images)->deleteAssociatedImageRecords($idType, $id);
            }
            if($retVal){
                $retVal = (new Media)->deleteAssociatedMediaRecords($idType, $id);
            }
            if($retVal){
                $retVal = (new OccurrenceMeasurementsOrFacts)->deleteOccurrenceMofRecords($idType, $id);
            }
            if($retVal){
                $sql = 'DELETE FROM guidoccurrences WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omcrowdsourcequeue WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omexsiccatiocclink WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omoccuraccessstats WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omoccurdatasetlink WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omoccureditlocks WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omoccuredits WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omoccurloanslink WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omoccurpoints WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                $sql = 'DELETE FROM omoccurrencesfulltext WHERE ' . $whereStr . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
            if($retVal){
                if($idType === 'occid' || $idType === 'occidArr'){
                    $sql = 'DELETE FROM omoccurrences WHERE ' . $whereStr . ' ';
                }
                else{
                    $sql = 'DELETE FROM omoccurrences WHERE collid = ' . (int)$id . ' ';
                }
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
            }
        }
        return $retVal;
    }

    public function evaluateOccurrenceForDeletion($occid): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT imgid FROM images WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        $retArr['images'] = (int)$rs->num_rows;
        $rs->free();
        $sql = 'SELECT DISTINCT mediaid FROM media WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        $retArr['media'] = (int)$rs->num_rows;
        $rs->free();
        $sql = 'SELECT DISTINCT clid FROM fmvouchers WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        $retArr['checklists'] = (int)$rs->num_rows;
        $rs->free();
        $sql = 'SELECT DISTINCT idoccurgenetic FROM omoccurgenetic WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        $retArr['genetic'] = (int)$rs->num_rows;
        $rs->free();
        return $retArr;
    }

    public function getBadSpecimenCount($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql = 'SELECT COUNT(occid) AS cnt FROM omoccurrences '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname IS NOT NULL ';
            //echo $sql;
            if($rs = $this->conn->query($sql)){
                if($row = $rs->fetch_object()){
                    $retCnt = $row->cnt;
                }
                $rs->free();
            }
        }
        return $retCnt;
    }

    public function getBadTaxaCount($collid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql = 'SELECT COUNT(DISTINCT sciname) AS taxacnt FROM omoccurrences '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname IS NOT NULL ';
            //echo $sql;
            if($rs = $this->conn->query($sql)){
                if($row = $rs->fetch_object()){
                    $retCnt = $row->taxacnt;
                }
                $rs->free();
            }
        }
        return $retCnt;
    }

    public function getLock($occid): int
    {
        $isLocked = 0;
        $delSql = 'DELETE FROM omoccureditlocks WHERE ts < ' . (time() - 900) . ' OR uid = ' . $GLOBALS['SYMB_UID'] . ' ';
        if($this->conn->query($delSql)) {
            $sqlFind = 'SELECT * FROM omoccureditlocks WHERE occid = ' . (int)$occid . ' ';
            $frs = $this->conn->query($sqlFind);
            if(!$frs->num_rows){
                $sql = 'INSERT INTO omoccureditlocks(occid, uid, ts) '.
                    'VALUES (' . (int)$occid . ',' . $GLOBALS['SYMB_UID'] . ',' . time() . ')';
                $this->conn->query($sql);
            }
            else{
                $isLocked = true;
            }
            $frs->free();
        }
        return $isLocked;
    }

    public function getOccidByGUIDArr($guidArr): array
    {
        $retArr = array();
        if(is_array($guidArr)){
            $searchStr = implode('","', $guidArr);
        }
        else{
            $searchStr = SanitizerService::cleanInStr($this->conn, $guidArr);
        }
        if($guidArr){
            $sql = 'SELECT occid FROM guidoccurrences WHERE guid IN("' . $searchStr . '")';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $retArr[] = $row['occid'];
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getOccidArrNotIncludedInUpload($collid): array
    {
        $returnArr = array();
        if($collid){
            $sql = 'SELECT o.occid FROM omoccurrences AS o LEFT JOIN uploadspectemp AS u ON o.occid = u.occid '.
                'WHERE u.collid  = ' . (int)$collid . ' AND ISNULL(u.occid) ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $returnArr[] = $row['occid'];
                    unset($rows[$index]);
                }
            }
        }
        return $returnArr;
    }

    public function getOccurrenceCountNotIncludedInUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(o.occid) AS cnt FROM omoccurrences AS o LEFT JOIN uploadspectemp AS u ON o.occid = u.occid '.
                'WHERE u.collid  = ' . (int)$collid . ' AND ISNULL(u.occid) ';
            if($result = $this->conn->query($sql)){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $result->free();
                if($row){
                    $returnVal = (int)$row['cnt'];
                }
            }
        }
        return $returnVal;
    }

    public function getOccurrenceData($occid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'o');
        $fieldNameArr[] = 'g.`guid`';
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurrences AS o LEFT JOIN guidoccurrences AS g ON o.occid = g.occid '.
            'WHERE o.occid = ' . (int)$occid . ' ';
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
                $localitySecurity = (int)$row['localitysecurity'] === 1;
                if($localitySecurity){
                    $rareSpCollidAccessArr = (new Permissions)->getUserRareSpCollidAccessArr();
                    if(!in_array((int)$row['collid'], $rareSpCollidAccessArr, true)){
                        $retArr = $this->clearSensitiveOccurrenceData($retArr);
                    }
                }
            }
            if($retArr && $retArr['tid'] && (int)$retArr['tid'] > 0){
                $retArr['taxonData'] = (new Taxa)->getTaxonFromTid($retArr['tid']);
            }
        }
        return $retArr;
    }

    public function getOccurrenceDuplicateIdentifierRecordArr($collid, $occid, $identifierField, $identifier): array
    {
        $retArr = array();
        if($identifierField && $identifier){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' FROM omoccurrences '.
                'WHERE collid = ' . (int)$collid . ' ';
            if($occid){
                $sql .= 'AND occid <> ' . (int)$occid . ' ';
            }
            $sql .= 'AND ' . SanitizerService::cleanInStr($this->conn, $identifierField) . ' = "' . SanitizerService::cleanInStr($this->conn, $identifier) . '" '.
                'ORDER BY eventdate, recordnumber ';
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
        }
        return $retArr;
    }

    public function getOccurrenceEditData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT e.ocedid, e.fieldname, e.fieldvalueold, e.fieldvaluenew, e.reviewstatus, e.appliedstatus, '.
            'CONCAT_WS(", ",u.lastname,u.firstname) AS editor, e.initialtimestamp '.
            'FROM omoccuredits AS e LEFT JOIN users AS u ON e.uid = u.uid '.
            'WHERE e.occid = ' . (int)$occid . ' ORDER BY e.initialtimestamp DESC ';
        //echo $sql;
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $nodeArr = array();
                $nodeArr['ocedid'] = $row['ocedid'];
                $nodeArr['editor'] = $row['editor'];
                $nodeArr['ts'] = substr($row['initialtimestamp'], 0, 16);
                $nodeArr['reviewstatus'] = $row['reviewstatus'];
                $nodeArr['appliedstatus'] = $row['appliedstatus'];
                $nodeArr['fieldname'] = $row['fieldname'];
                $nodeArr['old'] = $row['fieldvalueold'];
                $nodeArr['new'] = $row['fieldvaluenew'];
                $retArr[] = $nodeArr;
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getOccurrenceFields(): array
    {
        return $this->fields;
    }

    public function getOccurrenceIdDataFromIdentifierArr($collid, $identifierField, $identifierArr): array
    {
        $retArr = array();
        if($identifierField === 'catalognumber' || $identifierField === 'othercatalognumbers'){
            $sql = 'SELECT DISTINCT occid, tid, ' . $identifierField . ' FROM omoccurrences  '.
                'WHERE collid = ' . (int)$collid . ' AND ' . $identifierField . ' IN("' . implode('","', $identifierArr) . '") ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $retArr[strtolower($row[$identifierField])]['occid'] = $row['occid'];
                    $retArr[strtolower($row[$identifierField])]['tid'] = $row['tid'];
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getOccurrenceRecordsNotIncludedInUpload($collid, $index = null, $limit = null): array
    {
        $retArr = array();
        if($collid){
            $sql = $this->getOccurrenceRecordsNotIncludedInUploadSql($collid);
            if($index && $limit){
                $sql .= 'LIMIT ' . (((int)$index - 1) * (int)$limit) . ', ' . (int)$limit . ' ';
            }
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $rIndex => $row){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $row[$name];
                    }
                    $retArr[] = $nodeArr;
                    unset($rows[$rIndex]);
                }
            }
        }
        return $retArr;
    }

    public function getOccurrenceRecordsNotIncludedInUploadSql($collid): string
    {
        $sql = '';
        if($collid){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'o');
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' FROM omoccurrences AS o LEFT JOIN uploadspectemp AS u  ON o.occid = u.occid '.
                'WHERE u.collid  = ' . (int)$collid . ' AND ISNULL(u.occid) ';
        }
        return $sql;
    }

    public function getOccurrencesByCatalogNumber($catalogNumber, $collid = null): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurrences WHERE catalognumber = "' . SanitizerService::cleanInStr($this->conn, $catalogNumber) . '" ';
        if($collid){
            $sql .= 'AND collid = ' . (int)$collid . ' ';
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

    public function getUnlinkedSciNames($collid): array
    {
        $retArr = array();
        if((int)$collid){
            $sql = 'SELECT DISTINCT sciname '.
                'FROM omoccurrences '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname IS NOT NULL '.
                'ORDER BY sciname ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[] = $r->sciname;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function protectGlobalSpecies($collid): int
    {
        $returnVal = 0;
        $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'SET o.localitysecurity = 1 WHERE t.securitystatus = 1 ';
        if((int)$collid > 0) {
            $sql .= 'AND o.collid = ' . (int)$collid . ' ';
        }
        if($this->conn->query($sql)){
            $returnVal += $this->conn->affected_rows;
        }
        $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'SET o.localitysecurity = 0 '.
            'WHERE t.TID IS NOT NULL AND t.securitystatus <> 1 AND ISNULL(o.localitysecurityreason) ';
        if((int)$collid > 0) {
            $sql2 .= 'AND o.collid = ' . (int)$collid . ' ';
        }
        if($this->conn->query($sql2)){
            $returnVal += $this->conn->affected_rows;
        }
        return $returnVal;
    }

    public function removePrimaryIdentifiersFromUploadedOccurrences($collid): int
    {
        $retVal = 0;
        if($collid){
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN uploadspectemp AS u ON o.occid = u.occid '.
                'SET o.dbpk = NULL '.
                'WHERE o.collid  = ' . (int)$collid . ' AND u.occid IS NOT NULL ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function transferOccurrenceRecord($occid, $transferToCollid): int
    {
        $returnVal = 0;
        if((int)$occid > 0 && (int)$transferToCollid > 0){
            $sql = 'UPDATE omoccurrences SET collid = ' . (int)$transferToCollid . ' WHERE occid = ' . (int)$occid;
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function undoOccRecordsCleanedScinameChange($collid, $oldSciname,$newSciname): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql = 'UPDATE omoccurrences SET sciname = verbatimscientificname, verbatimscientificname = NULL, tid = NULL '.
                'WHERE collid = ' . (int)$collid.' AND verbatimscientificname = "' . SanitizerService::cleanInStr($this->conn,$oldSciname) . '" AND sciname = "' . SanitizerService::cleanInStr($this->conn,$newSciname) . '" ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function updateOccRecordsWithCleanedSciname($collid, $sciname, $cleanedSciname, $tid): int
    {
        $retCnt = 0;
        if((int)$collid){
            $sql = 'UPDATE omoccurrences SET verbatimscientificname = sciname '.
                'WHERE collid = ' . (int)$collid . ' AND sciname = "' . SanitizerService::cleanInStr($this->conn, $sciname) . '" ';
            //echo $sql;
            if($this->conn->query($sql)){
                $sql2 = 'UPDATE omoccurrences SET sciname = "' . SanitizerService::cleanInStr($this->conn, $cleanedSciname) . '"'.
                    ((int)$tid > 0 ? ', tid = ' . (int)$tid . ' ' : ' ').
                    'WHERE collid = ' . (int)$collid . ' AND sciname = "' . SanitizerService::cleanInStr($this->conn, $sciname) . '" ';
                //echo $sql2;
                if($this->conn->query($sql2)){
                    $retCnt = $this->conn->affected_rows;
                }
            }
        }
        return $retCnt;
    }

    public function updateOccRecordsWithNewScinameTid($collid, $sciname, $tid): int
    {
        $retCnt = 0;
        $sciname = SanitizerService::cleanInStr($this->conn, $sciname);
        if((int)$collid && $sciname){
            $sql = 'UPDATE omoccurrences SET tid = '.$tid.' '.
                'WHERE collid = ' . (int)$collid . ' AND sciname = "' . $sciname . '" ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
                $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
                    'SET d.tid = ' . (int)$tid . ' '.
                    'WHERE o.collid = ' . (int)$collid . ' AND d.sciname = "' . $sciname . '" ';
                //echo $sql2;
                $this->conn->query($sql2);

                $sql3 = 'UPDATE omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
                    'SET i.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND o.sciname = "' . $sciname . '" ';
                //echo $sql3;
                $this->conn->query($sql3);

                $sql4 = 'UPDATE omoccurrences AS o LEFT JOIN media AS m ON o.occid = m.occid '.
                    'SET m.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND o.sciname = "' . $sciname . '" ';
                //echo $sql4;
                $this->conn->query($sql4);
            }
        }
        return $retCnt;
    }

    public function updateOccTaxonomicThesaurusLinkages($collid, $kingdomId): int
    {
        $retCnt = 0;
        $rankIdArr = array();
        if((int)$collid && $kingdomId){
            $sql = 'SELECT DISTINCT rankid FROM taxonunits WHERE kingdomid = ' . (int)$kingdomId . ' AND rankid < 180 AND rankid > 20 ORDER BY rankid DESC ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $rankIdArr[] = $r->rankid;
            }
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.SciName '.
                'SET o.tid = t.tid '.
                'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(o.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid >= 180 ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
            foreach($rankIdArr as $id){
                $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.SciName '.
                    'SET o.tid = t.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(o.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid = ' . $id . ' ';
                //echo $sql;
                if($this->conn->query($sql)){
                    $retCnt += $this->conn->affected_rows;
                }
            }
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.SciName '.
                'SET o.tid = t.tid '.
                'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(o.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid <= 20 ';
            //echo $sql;
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
            if($retCnt > 0){
                $sql = 'UPDATE omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
                    'SET i.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND i.imgid IS NOT NULL ';
                //echo $sql;
                $this->conn->query($sql);

                $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN media AS m ON o.occid = m.occid '.
                    'SET m.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND m.mediaid IS NOT NULL ';
                //echo $sql2;
                $this->conn->query($sql2);
            }
        }
        return $retCnt;
    }

    public function updateOccurrenceEventId($occId, $eventId, $updateData = false): int
    {
        $retVal = 0;
        if($occId && $eventId){
            $sql = 'UPDATE omoccurrences SET eventid = ' . (int)$eventId . ' WHERE occid = ' . (int)$occId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
                if($updateData){
                    $sql = 'SELECT locationid FROM omoccurrences WHERE occid = ' . (int)$occId . ' ';
                    //echo '<div>'.$sql.'</div>';
                    if($result = $this->conn->query($sql)){
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $result->free();
                        if((int)$row['locationid'] > 0){
                            $retVal = (new OccurrenceLocations)->updateOccurrencesFromLocationData($row['locationid']);
                        }
                    }
                    if($retVal){
                        $retVal = (new OccurrenceCollectingEvents)->updateOccurrencesFromCollectingEventData($eventId);
                    }
                }
            }
        }
        return $retVal;
    }

    public function updateOccurrenceLocationId($occId, $locationId, $updateData = false): int
    {
        $retVal = 0;
        if($occId && $locationId){
            $sql = 'UPDATE omoccurrences SET locationid = ' . (int)$locationId . ' WHERE occid = ' . (int)$occId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
                if($updateData){
                    $retVal = (new OccurrenceLocations)->updateOccurrencesFromLocationData($locationId);
                }
            }
        }
        return $retVal;
    }

    public function updateOccurrenceRecord($occId, $editData, $determinationUpdate = false): int
    {
        $retVal = 0;
        $fieldNameArr = array();
        $sqlPartArr = array();
        if($occId && $editData){
            if(!$determinationUpdate && (array_key_exists('sciname', $editData) || array_key_exists('tid', $editData))){
                $determinationData = array();
                $determinationFields = (new OccurrenceDeterminations)->getDeterminationFields();
                foreach($editData as $field => $value){
                    if(array_key_exists($field, $determinationFields)){
                        $determinationData[$field] = $value;
                        unset($editData[$field]);
                    }
                }
                $determinationData['occid'] = $occId;
                $determinationData['iscurrent'] = 1;
                $detId = (new OccurrenceDeterminations)->createOccurrenceDeterminationRecord($determinationData);
                $retVal = $detId > 0 ? 1 : 0;
            }
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $fieldNameArr[] = $fieldStr;
                    $sqlPartArr[] = $fieldStr . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            if(count($sqlPartArr) > 0){
                $sqlPartArr[] = 'datelastmodified = "' . date('Y-m-d H:i:s') . '"';
                $sql = 'SELECT ' . implode(', ', $fieldNameArr) .
                    ' FROM omoccurrences WHERE occid = ' . (int)$occId . ' ';
                //echo $sql;
                $rs = $this->conn->query($sql);
                if($oldData = $rs->fetch_assoc()){
                    $sqlEditsBase = 'INSERT INTO omoccuredits(occid, reviewstatus, appliedstatus, uid, fieldname, fieldvaluenew, fieldvalueold) '.
                        'VALUES(' . (int)$occId . ', 1, 1, ' . $GLOBALS['SYMB_UID'] . ', ';
                    foreach($fieldNameArr as $fieldName){
                        $cleanedFieldName = str_replace('`','',$fieldName);
                        $oldValue = $oldData[$cleanedFieldName] ? SanitizerService::getSqlValueString($this->conn, $oldData[$cleanedFieldName], $this->fields[$cleanedFieldName]['dataType']) : '""';
                        $newValue = $editData[$cleanedFieldName] ? SanitizerService::getSqlValueString($this->conn, $editData[$cleanedFieldName], $this->fields[$cleanedFieldName]['dataType']) : '""';
                        $sqlEdit = $sqlEditsBase . '"' . $cleanedFieldName . '",' . $newValue . ',' . $oldValue . ') ';
                        //echo '<div>'.$sqlEdit.'</div>';
                        $this->conn->query($sqlEdit);
                    }
                }
                $rs->free();
                $sql = 'UPDATE omoccurrences SET ' . implode(', ', $sqlPartArr) . ' '.
                    'WHERE occid = ' . (int)$occId . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $retVal = 1;
                    if($determinationUpdate){
                        (new ChecklistVouchers)->updateTidFromOccurrenceRecord($occId, $editData['tid']);
                        (new Images)->updateTidFromOccurrenceRecord($occId, $editData['tid']);
                        (new Media)->updateTidFromOccurrenceRecord($occId, $editData['tid']);
                    }
                }
            }
        }
        return $retVal;
    }

    public function updateOccurrenceRecordsFromUploadData($collId): int
    {
        $skipFields = array('occid', 'collid', 'dbpk', 'recordedbyid', 'associatedoccurrences', 'recordenteredby', 'dateentered', 'datelastmodified');
        $retVal = 0;
        $sqlPartArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields)){
                    if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = 'o.' . $fieldStr . ' = u.' . $fieldStr;
                }
            }
            if(count($sqlPartArr) > 0){
                $sqlPartArr[] = 'datelastmodified = "' . date('Y-m-d H:i:s') . '"';
                $sql = 'UPDATE omoccurrences AS o LEFT JOIN uploadspectemp AS u ON o.occid = u.occid '.
                    'SET ' . implode(', ', $sqlPartArr) . ' '.
                    'WHERE u.collid = ' . (int)$collId . ' ';
                //echo "<div>".$sql."</div>";
                if($this->conn->query($sql)){
                    $retVal = 1;
                }
            }
        }
        return $retVal;
    }
}
