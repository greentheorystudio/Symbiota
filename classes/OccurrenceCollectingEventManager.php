<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceCollectingEventManager{

	private $conn;

    private $fields = array(
        "eventid" => array("dataType" => "number", "length" => 11),
        "collid" => array("dataType" => "number", "length" => 10),
        "eventtype" => array("dataType" => "string", "length" => 255),
        "fieldnotes" => array("dataType" => "text", "length" => 0),
        "fieldnumber" => array("dataType" => "string", "length" => 45),
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
        "localitysecurity" => array("dataType" => "number", "length" => 10),
        "localitysecurityreason" => array("dataType" => "string", "length" => 100),
        "decimallatitude" => array("dataType" => "number", "length" => 0),
        "decimallongitude" => array("dataType" => "number", "length" => 0),
        "geodeticdatum" => array("dataType" => "string", "length" => 255),
        "coordinateuncertaintyinmeters" => array("dataType" => "number", "length" => 10),
        "footprintwkt" => array("dataType" => "text", "length" => 0),
        "eventremarks" => array("dataType" => "text", "length" => 0),
        "georeferencedby" => array("dataType" => "string", "length" => 255),
        "georeferenceprotocol" => array("dataType" => "string", "length" => 255),
        "georeferencesources" => array("dataType" => "string", "length" => 255),
        "georeferenceverificationstatus" => array("dataType" => "string", "length" => 32),
        "georeferenceremarks" => array("dataType" => "string", "length" => 500),
        "minimumdepthinmeters" => array("dataType" => "number", "length" => 0),
        "maximumdepthinmeters" => array("dataType" => "number", "length" => 0),
        "verbatimdepth" => array("dataType" => "string", "length" => 50),
        "samplingprotocol" => array("dataType" => "string", "length" => 100),
        "samplingeffort" => array("dataType" => "string", "length" => 200),
        "repcount" => array("dataType" => "number", "length" => 10),
        "labelproject" => array("dataType" => "string", "length" => 250)
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
                    $fieldValueArr[] = Sanitizer::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'INSERT INTO omoccurcollectingevents(' . implode(',', $fieldNameArr) . ') '.
                'VALUES (' . implode(',', $fieldValueArr) . ') ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
            }
        }
        return $newID;
    }

    public function getCollectingEventArr($collid, $occid, $vars): array
    {
        $retArr = array();
        $fieldNameArr = array();
        $sqlWhereArr = array();
        $recordedby = $vars['recordedby'] ? Sanitizer::cleanInStr($this->conn, $vars['recordedby']) : null;
        $lastname = $vars['lastname'] ? Sanitizer::cleanInStr($this->conn, $vars['lastname']) : null;
        $recordnumber = $vars['recordnumber'] ? Sanitizer::cleanInStr($this->conn, $vars['recordnumber']) : null;
        $eventdate = $vars['eventdate'] ? Sanitizer::cleanInStr($this->conn, $vars['eventdate']) : null;
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'eventtype' && $field !== 'eventremarks' && $field !== 'repcount'){
                if($field === 'year' || $field === 'month' || $field === 'day'){
                    $fieldNameArr[] = 'o.`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = 'o.' . $field;
                }
            }
        }
        $fieldNameArr = array_merge($fieldNameArr, array('ce.eventtype', 'ce.eventremarks', 'ce.repcount'));
        if((int)$vars['locationid'] > 0){
            $sqlWhereArr[] = 'o.locationid = ' . (int)$vars['locationid'];
            $sqlWhereArr[] = 'o.eventid IS NOT NULL';
        }
        else{
            $locationFields = array('o.waterbody', 'o.country', 'o.stateprovince', 'o.county', 'o.municipality', 'o.locality',
                'o.coordinateprecision', 'o.locationremarks', 'o.verbatimcoordinates', 'o.verbatimcoordinatesystem', 'o.minimumelevationinmeters',
                'o.maximumelevationinmeters', 'o.verbatimelevation');
            $fieldNameArr = array_merge($fieldNameArr, $locationFields);
            if($recordedby || $lastname){
                $collWhereArr = array();
                if($recordedby){
                    $collWhereArr[] = 'o.recordedby = "' . $recordedby . '"';
                }
                if($lastname){
                    $collWhereArr[] = 'o.recordedby LIKE "%'.$lastname.'%"';
                }
                $sqlWhereArr[] = '(' . implode(' OR ', $collWhereArr) . ')';
            }
            if($recordnumber){
                if(is_numeric($recordnumber)){
                    $nStart = (int)$recordnumber - 4;
                    if($nStart < 1){
                        $nStart = 1;
                    }
                    $nEnd = (int)$recordnumber + 4;
                    $sqlWhereArr[] = '(o.recordnumber BETWEEN ' . $nStart . ' AND ' . $nEnd . ')';
                }
                elseif(preg_match('/^(\d+)-?[a-zA-Z]{1,2}$/', $recordnumber, $m)){
                    $cNum = (int)$m[1];
                    $nStart = $cNum - 4;
                    if($nStart < 1){
                        $nStart = 1;
                    }
                    $nEnd = $cNum + 4;
                    $sqlWhereArr[] = '(CAST(o.recordnumber AS SIGNED) BETWEEN '.$nStart.' AND '.$nEnd.')';
                }
            }
            if($eventdate){
                $sqlWhereArr[] = 'o.eventdate = "' . $eventdate . '"';
            }
        }
        if((int)$occid > 0){
            $sqlWhereArr[] = 'o.occid <> ' . (int)$occid;
        }
        $sql = 'SELECT DISTINCT ' . implode(',', $fieldNameArr) . ' FROM omoccurrences AS o '.
            'LEFT JOIN omoccurcollectingevents AS ce ON o.eventid = ce.eventid '.
            'WHERE o.collid = ' . (int)$collid . ' AND ' . implode(' AND ', $sqlWhereArr) . ' '.
            'ORDER BY o.eventdate, o.recordnumber ';
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

    public function getCollectingEventBenthicData($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT occid, sciname, identificationremarks, identificationqualifier, rep, individualcount '.
            'FROM omoccurrences WHERE eventid = ' . (int)$eventid . ' '.
            'ORDER BY sciname, identificationqualifier, identificationremarks, rep ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($r->rep && $r->individualcount && (int)$r->individualcount > 0){
                    $key = $r->sciname . ($r->identificationqualifier ? '-' . $r->identificationqualifier : '') . ($r->identificationremarks ? '-' . $r->identificationremarks : '');
                    $repLabel = 'rep' . (int)$r->rep;
                    if(!array_key_exists($key, $retArr)){
                        $retArr[$key] = array();
                        $retArr[$key]['sciname'] = $r->sciname;
                        $retArr[$key]['identificationqualifier'] = $r->identificationqualifier;
                        $retArr[$key]['identificationremarks'] = $r->identificationremarks;
                    }
                    $retArr[$key][$repLabel]['occid'] = $r->occid;
                    $retArr[$key][$repLabel]['cnt'] = $r->individualcount;
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getCollectingEventCollectionsArr($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT occid, sciname, identificationremarks, identificationqualifier, family, associatedtaxa, individualcount, '.
            'lifestage, sex, occurrenceremarks, typestatus, reproductivecondition, establishmentmeans, dynamicproperties '.
            'FROM omoccurrences WHERE eventid = ' . (int)$eventid . ' ';
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

    public function getCollectingEventData($eventid): array
    {
        $retArr = array();
        $fieldNameArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field === 'year' || $field === 'month' || $field === 'day'){
                $fieldNameArr[] = '`' . $field . '`';
            }
            else{
                $fieldNameArr[] = $field;
            }
        }
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurcollectingevents WHERE eventID = ' . (int)$eventid . ' ';
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

    public function getCollectingEventFields(): array
    {
        return $this->fields;
    }

    public function getConfiguredFieldData($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT a.adddataid, a.field, a.datavalue, a.initialtimestamp '.
            'FROM omoccuradditionaldata AS a '.
            'WHERE a.eventID = ' . (int)$eventid . ' ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[$r->field] = $r->datavalue;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function updateCollectingEventRecord($eventId, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        $occSqlPartArr = array();
        if($eventId && $editData){
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
            $sql = 'UPDATE omoccurcollectingevents SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE eventid = ' . $eventId . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
                foreach($this->fields as $field => $fieldArr){
                    if($field !== 'repcount' && array_key_exists($field, $editData)){
                        if($field === 'year' || $field === 'month' || $field === 'day'){
                            $fieldStr = '`' . $field . '`';
                        }
                        else{
                            $fieldStr = $field;
                        }
                        $occSqlPartArr[] = $fieldStr . ' = ' . Sanitizer::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                        $occSql = 'UPDATE omoccurrences SET ' . implode(', ', $occSqlPartArr) . ' '.
                            'WHERE eventid = ' . $eventId . ' ';
                        if(!$this->conn->query($occSql)){
                            $retVal = 0;
                        }
                    }
                }
            }
        }
        return $retVal;
    }
}
