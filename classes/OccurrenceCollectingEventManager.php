<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceCollectingEventManager{

	private $conn;

    private $fields = array(
        "eventid" => array("dataType" => "number", "length" => 11),
        "collid" => array("dataType" => "number", "length" => 10),
        "locationid" => array("dataType" => "number", "length" => 11),
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

    public function getAdditionalData($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT a.adddataid, a.field, a.datavalue, a.initialtimestamp '.
            'FROM omoccuradditionaldata AS a '.
            'WHERE a.eventID = ' . (int)$eventid . ' ';
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

    public function getCollectionEventData($eventid): array
    {
        $retArr = array();
        $sql = 'SELECT e.locationid, e.eventtype, e.fieldnotes, e.fieldnumber, e.eventdate, e.latestdatecollected, e.eventtime, '.
            'e.`year`, e.`month`, e.`day`, e.startdayofyear, e.enddayofyear, e.verbatimeventdate, e.habitat, e.localitysecurity, '.
            'e.localitysecurityreason, e.decimallatitude, e.decimallongitude, e.geodeticdatum, e.coordinateuncertaintyinmeters, '.
            'e.footprintwkt, e.eventremarks, e.georeferencedby, e.georeferenceprotocol, e.georeferencesources, e.georeferenceverificationstatus, '.
            'e.georeferenceremarks, e.minimumdepthinmeters, e.maximumdepthinmeters, e.verbatimdepth, e.samplingprotocol, '.
            'e.samplingeffort, e.initialtimestamp '.
            'FROM omoccurcollectingevents AS e '.
            'WHERE e.eventID = ' . (int)$eventid . ' ';
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

    public function getCollectionEventFields(): array
    {
        return $this->fields;
    }
}
