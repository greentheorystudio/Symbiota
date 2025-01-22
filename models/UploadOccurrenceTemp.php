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

    public function cleanUploadCoordinates($collid): int
    {
        $returnVal = 1;
        if($collid){
            $sql = 'UPDATE uploadspectemp SET decimallongitude = -1 * decimallongitude '.
                'WHERE collid = ' . (int)$collid . ' AND decimallongitude > 0 AND country IN("USA", "United States", "U.S.A.", "Canada", "Mexico") AND (stateprovince <> "Alaska" OR ISNULL(stateprovince)) ';
            if(!$this->conn->query($sql)){
                $returnVal = 0;
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET decimallatitude = NULL, decimallongitude = NULL '.
                    'WHERE collid = ' . (int)$collid . ' AND decimallatitude = 0 AND decimallongitude = 0 ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET verbatimcoordinates = CONCAT_WS(" ", decimallatitude, decimallongitude) '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(verbatimcoordinates) AND (decimallatitude < -90 OR decimallatitude > 90 OR decimallongitude < -180 OR decimallongitude > 180) ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET decimallatitude = NULL, decimallongitude = NULL '.
                    'WHERE collid = ' . (int)$collid . ' AND (decimallatitude < -90 OR decimallatitude > 90 OR decimallongitude < -180 OR decimallongitude > 180) ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function cleanUploadCountryStateNames($collid): int
    {
        $returnVal = 1;
        if($collid){
            $sql = 'UPDATE uploadspectemp AS u LEFT JOIN lkupcountry AS c ON u.country = c.iso3 '.
                'SET u.country = c.countryname '.
                'WHERE u.collid = ' . (int)$collid . ' AND c.countryname IS NOT NULL ';
            if(!$this->conn->query($sql)){
                $returnVal = 0;
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp AS u LEFT JOIN lkupcountry AS c ON u.country = c.iso '.
                    'SET u.country = c.countryname '.
                    'WHERE u.collid = ' . (int)$collid . ' AND c.countryname IS NOT NULL ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp AS u LEFT JOIN lkupstateprovince AS s ON u.stateprovince = s.abbrev '.
                    'SET u.stateprovince = s.statename '.
                    'WHERE u.collid = ' . (int)$collid . ' AND s.statename IS NOT NULL ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp AS u LEFT JOIN lkupstateprovince AS s ON u.stateprovince = s.statename '.
                    'LEFT JOIN lkupcountry AS c ON s.countryid = c.countryid '.
                    'SET u.country = c.countryname '.
                    'WHERE ISNULL(u.country) AND u.collid = ' . (int)$collid . ' AND c.countryname IS NOT NULL ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function cleanUploadEventDates($collid): int
    {
        $returnVal = 1;
        if($collid){
            $sql = 'UPDATE uploadspectemp SET `year` = YEAR(eventdate) '.
                'WHERE collid = ' . (int)$collid . ' AND eventdate IS NOT NULL AND ISNULL(`year`) ';
            if(!$this->conn->query($sql)){
                $returnVal = 0;
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET `month` = MONTH(eventdate) '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(`month`) AND eventdate IS NOT NULL ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET `day` = DAY(eventdate) '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(`day`) AND eventdate IS NOT NULL';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET startdayofyear = DAYOFYEAR(eventdate) '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(startdayofyear) AND eventdate IS NOT NULL';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET enddayofyear = DAYOFYEAR(eventdate) '.
                    'WHERE collid = ' . (int)$collid . ' AND ISNULL(enddayofyear) AND eventdate IS NOT NULL';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
    }

    public function cleanUploadTaxonomy($collid): int
    {
        $returnVal = 1;
        if($collid){
            $sql = 'UPDATE uploadspectemp SET family = sciname '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(family) AND (sciname LIKE "%aceae" OR sciname LIKE "%idae") ';
            if(!$this->conn->query($sql)){
                $returnVal = 0;
            }

            if($returnVal === 1){
                $sql = 'UPDATE uploadspectemp SET sciname = family '.
                    'WHERE collid = ' . (int)$collid . ' AND family IS NOT NULL AND ISNULL(sciname) ';
                if(!$this->conn->query($sql)){
                    $returnVal = 0;
                }
            }
        }
        return $returnVal;
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

    public function getDuplicateDbpkCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT dbpk FROM uploadspectemp GROUP BY dbpk, collid HAVING COUNT(upspid) > 1 AND collid  = ' . (int)$collid . ' ';
            if($result = $this->conn->query($sql)){
                $returnVal = $result->num_rows;
                $result->free();
            }
        }
        return $returnVal;
    }

    public function getExistingRecordCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(upspid) AS cnt FROM uploadspectemp WHERE collid  = ' . (int)$collid . ' AND occid IS NOT NULL ';
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

    public function getNewRecordCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(upspid) AS cnt FROM uploadspectemp WHERE collid  = ' . (int)$collid . ' AND ISNULL(occid) ';
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

    public function getNullDbpkCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(upspid) AS cnt FROM uploadspectemp WHERE collid  = ' . (int)$collid . ' AND ISNULL(dbpk) ';
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

    public function getUploadCount($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'SELECT COUNT(upspid) AS cnt FROM uploadspectemp WHERE collid  = ' . (int)$collid . ' ';
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

    public function getUploadData($collid, $dataType, $index = null, $limit = null): array
    {
        $retArr = array();
        if($collid && $dataType){
            $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
            $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
                'FROM uploadspectemp ';
            if($dataType !== 'dupdbpk'){
                $sql .= 'WHERE collid  = ' . (int)$collid . ' ';
            }
            if($dataType === 'new'){
                $sql .= 'AND ISNULL(occid) ';
            }
            elseif($dataType === 'update'){
                $sql .= 'AND occid IS NOT NULL ';
            }
            elseif($dataType === 'nulldbpk'){
                $sql .= 'AND ISNULL(dbpk) ';
            }
            elseif($dataType === 'dupdbpk'){
                $sql .= 'GROUP BY dbpk, collid HAVING COUNT(upspid) > 1 AND collid  = ' . (int)$collid . ' ';
            }
            if($index !== null && $limit !== null){
                $sql .= 'LIMIT ' . ((int)$index > 0 ? (((int)$index - 1) * (int)$limit) : (int)$index) . ', ' . (int)$limit;
            }
            //echo '<div>'.$sql.'</div>';
            if($result = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($result);
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $row){
                    $nodeArr = array();
                    foreach($fields as $val){
                        $name = $val->name;
                        $nodeArr[$name] = $row[$name];
                    }
                    $retArr[] = $nodeArr;
                    //unset($rows[$rIndex]);
                }
            }
        }
        return $retArr;
    }

    public function getUploadSummary($collid): array
    {
        $retArr = array();
        if($collid){
            $retArr['occur'] = $this->getUploadCount($collid);
            $retArr['new'] = $this->getNewRecordCount($collid);
            $retArr['update'] = $this->getExistingRecordCount($collid);
            $retArr['nulldbpk'] = $this->getNullDbpkCount($collid);
            $retArr['dupdbpk'] = $this->getDuplicateDbpkCount($collid);
        }
        return $retArr;
    }

    public function linkUploadToExistingOccurrenceData($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'UPDATE uploadspectemp AS u LEFT JOIN omoccurrences AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'SET u.occid = o.occid '.
                'WHERE u.collid  = ' . $collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function linkUploadToExistingOccurrenceDataByCatalogNumber($collid, $linkField): int
    {
        $returnVal = 0;
        if($collid && ($linkField === 'catalognumber' || $linkField === 'othercatalognumbers')){
            $sql = 'UPDATE uploadspectemp AS u LEFT JOIN omoccurrences AS o ON u.' . $linkField . ' = o.' . $linkField . ' AND u.collid = o.collid '.
                'SET u.occid = o.occid '.
                'WHERE u.collid  = ' . $collid . ' AND u.' . $linkField . ' IS NOT NULL AND o.occid IS NOT NULL ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function processCleaningScriptData($collid, $scriptData): int
    {
        $returnVal = 0;
        if($collid && $scriptData){
            $sql = 'DELETE u.* FROM uploadspectemp AS u ';
            if(array_key_exists('join', $scriptData) && $scriptData['join']){
                $sql .= $scriptData['join'] . ' ';
            }
            $sql .= 'WHERE u.collid = ' . (int)$collid . ' ';
            if(array_key_exists('where', $scriptData) && $scriptData['where']){
                $sql .= 'AND ' . $scriptData['where'] . ' ';
            }
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function removeExistingOccurrenceDataFromUpload($collid): int
    {
        $returnVal = 0;
        if($collid){
            $sql = 'DELETE up.*, u.* FROM uploadspectemppoints AS up LEFT JOIN uploadspectemp AS u ON up.upspid = u.upspid '.
                'LEFT JOIN omoccurrences AS o ON u.dbpk = o.dbpk AND u.collid = o.collid '.
                'WHERE u.collid  = ' . (int)$collid . ' AND u.dbpk IS NOT NULL AND o.occid IS NOT NULL ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function removeOrphanedPoints($collid): void
    {
        if($collid){
            $sql = 'DELETE FROM uploadspectemppoints WHERE upspid NOT IN(SELECT upspid FROM uploadspectemp '.
                'WHERE collid = ' . (int)$collid . ') ';
            $this->conn->query($sql);
        }
    }
}
