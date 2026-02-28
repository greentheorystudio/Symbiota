<?php
include_once(__DIR__ . '/OccurrenceLocations.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceCollectingEvents{

	private $conn;

    private $fields = array(
        'eventid' => array('dataType' => 'number', 'length' => 11),
        'collid' => array('dataType' => 'number', 'length' => 10),
        'locationid' => array('dataType' => 'number', 'length' => 11),
        'eventtype' => array('dataType' => 'string', 'length' => 255),
        'fieldnotes' => array('dataType' => 'text', 'length' => 0),
        'fieldnumber' => array('dataType' => 'string', 'length' => 45),
        'recordedby' => array('dataType' => 'string', 'length' => 255),
        'recordnumber' => array('dataType' => 'string', 'length' => 45),
        'recordedbyid' => array('dataType' => 'number', 'length' => 20),
        'associatedcollectors' => array('dataType' => 'string', 'length' => 255),
        'eventdate' => array('dataType' => 'date', 'length' => 0),
        'latestdatecollected' => array('dataType' => 'date', 'length' => 0),
        'eventtime' => array('dataType' => 'time', 'length' => 0),
        'year' => array('dataType' => 'number', 'length' => 10),
        'month' => array('dataType' => 'number', 'length' => 10),
        'day' => array('dataType' => 'number', 'length' => 10),
        'startdayofyear' => array('dataType' => 'number', 'length' => 10),
        'enddayofyear' => array('dataType' => 'number', 'length' => 10),
        'verbatimeventdate' => array('dataType' => 'string', 'length' => 255),
        'habitat' => array('dataType' => 'text', 'length' => 0),
        'localitysecurity' => array('dataType' => 'number', 'length' => 10),
        'localitysecurityreason' => array('dataType' => 'string', 'length' => 100),
        'decimallatitude' => array('dataType' => 'number', 'length' => 0),
        'decimallongitude' => array('dataType' => 'number', 'length' => 0),
        'geodeticdatum' => array('dataType' => 'string', 'length' => 255),
        'coordinateuncertaintyinmeters' => array('dataType' => 'number', 'length' => 10),
        'footprintwkt' => array('dataType' => 'text', 'length' => 0),
        'eventremarks' => array('dataType' => 'text', 'length' => 0),
        'georeferencedby' => array('dataType' => 'string', 'length' => 255),
        'georeferenceprotocol' => array('dataType' => 'string', 'length' => 255),
        'georeferencesources' => array('dataType' => 'string', 'length' => 255),
        'georeferenceverificationstatus' => array('dataType' => 'string', 'length' => 32),
        'georeferenceremarks' => array('dataType' => 'string', 'length' => 500),
        'minimumdepthinmeters' => array('dataType' => 'number', 'length' => 0),
        'maximumdepthinmeters' => array('dataType' => 'number', 'length' => 0),
        'verbatimdepth' => array('dataType' => 'string', 'length' => 50),
        'samplingprotocol' => array('dataType' => 'string', 'length' => 100),
        'samplingeffort' => array('dataType' => 'string', 'length' => 200),
        'repcount' => array('dataType' => 'number', 'length' => 10),
        'labelproject' => array('dataType' => 'string', 'length' => 250)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createCollectingEventRecord($data): int
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
                    $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'INSERT INTO omoccurcollectingevents(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function deleteCollectingEventRecord($eventid): int
    {
        $retVal = 1;
        $sql = 'DELETE FROM ommofextension WHERE eventid = ' . (int)$eventid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'UPDATE omoccurrences SET eventid = NULL WHERE eventid = ' . (int)$eventid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurcollectingevents WHERE eventid = ' . (int)$eventid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        return $retVal;
    }

    public function getCollectingEventCollectionsArr($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT occid, sciname, identificationremarks, identificationqualifier, family, associatedtaxa, individualcount, '.
            'lifestage, sex, occurrenceremarks, typestatus, reproductivecondition, establishmentmeans, dynamicproperties, '.
            'catalognumber, othercatalognumbers, basisofrecord, verbatimattributes '.
            'FROM omoccurrences WHERE eventid = ' . (int)$eventid . ' ';
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

    public function getCollectingEventData($eventid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurcollectingevents WHERE eventID = ' . (int)$eventid . ' ';
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

    public function getCollectingEventFields(): array
    {
        return $this->fields;
    }

    public function getCollectingEventReplicateData($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT occid, tid, sciname, family, scientificnameauthorship, identificationremarks, identificationqualifier, rep, individualcount '.
            'FROM omoccurrences WHERE eventid = ' . (int)$eventid . ' '.
            'ORDER BY sciname, identificationqualifier, identificationremarks, rep ';
        if($result = $this->conn->query($sql)){
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                if($row['rep'] && $row['individualcount'] && (int)$row['individualcount'] > 0){
                    $key = $row['sciname'] . ($row['identificationqualifier'] ? '-' . $row['identificationqualifier'] : '') . ($row['identificationremarks'] ? '-' . $row['identificationremarks'] : '');
                    $repLabel = 'rep' . (int)$row['rep'];
                    if(!array_key_exists($key, $retArr)){
                        $retArr[$key] = array();
                        $retArr[$key]['tid'] = $row['tid'];
                        $retArr[$key]['sciname'] = $row['sciname'];
                        $retArr[$key]['family'] = $row['family'];
                        $retArr[$key]['scientificnameauthorship'] = $row['scientificnameauthorship'];
                        $retArr[$key]['identificationqualifier'] = $row['identificationqualifier'];
                        $retArr[$key]['identificationremarks'] = $row['identificationremarks'];
                    }
                    $retArr[$key][$repLabel]['occid'] = $row['occid'];
                    $retArr[$key][$repLabel]['cnt'] = $row['individualcount'];
                }
                unset($rows[$index]);
            }
        }
        return $retArr;
    }

    public function getLocationCollectingEventArr($collid, $locationid): array
    {
        $retArr = array();
        $sqlWhereArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 'e');
        $locationFields = array('l.waterbody', 'l.country', 'l.stateprovince', 'l.county', 'l.municipality', 'l.locality',
            'l.coordinateprecision', 'l.locationremarks', 'l.verbatimcoordinates', 'l.verbatimcoordinatesystem', 'l.minimumelevationinmeters',
            'l.maximumelevationinmeters', 'l.verbatimelevation');
        $fieldNameArr = array_merge($fieldNameArr, $locationFields);
        $sqlWhereArr[] = 'e.locationid = ' . (int)$locationid;
        $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . ' FROM omoccurcollectingevents AS e '.
            'LEFT JOIN omoccurlocations AS l ON e.locationid = l.locationid '.
            'WHERE e.collid = ' . (int)$collid . ' AND ' . implode(' AND ', $sqlWhereArr) . ' '.
            'ORDER BY e.eventdate DESC, e.recordnumber ';
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

    public function updateCollectingEventLocation($eventId, $locationId): int
    {
        $retVal = 0;
        if($eventId && $locationId){
            $sql = 'UPDATE omoccurcollectingevents SET locationid = ' . (int)$locationId . ' '.
                'WHERE eventid = ' . (int)$eventId . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
                $sql = 'UPDATE omoccurrences SET locationid = ' . (int)$locationId . ' '.
                    'WHERE eventid = ' . (int)$eventId . ' ';
                if(!$this->conn->query($sql)){
                    $retVal = 0;
                }
                if($retVal){
                    $retVal = (new OccurrenceLocations)->updateOccurrencesFromLocationData($locationId);
                }
            }
        }
        return $retVal;
    }

    public function updateCollectingEventRecord($eventId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($eventId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    if($field === 'year' || $field === 'month' || $field === 'day'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = $fieldStr . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'UPDATE omoccurcollectingevents SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE eventid = ' . (int)$eventId . ' ';
            if($this->conn->query($sql)){
                $retVal = $this->updateOccurrencesFromCollectingEventData($eventId);
            }
        }
        return $retVal;
    }

    public function updateOccurrencesFromCollectingEventData($eventId): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($eventId){
            foreach($this->fields as $field => $fieldArr){
                if($field !== 'eventtype' && $field !== 'repcount'){
                    if($field === 'year' || $field === 'month' || $field === 'day'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = 'o.' . $fieldStr . ' = e.' . $fieldStr . ' ';
                }
            }
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN omoccurcollectingevents AS e ON o.eventid = e.eventid '.
                'SET ' . implode(', ', $sqlPartArr) . ' WHERE e.eventid = ' . (int)$eventId . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }
}
