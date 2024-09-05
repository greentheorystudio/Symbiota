<?php
include_once(__DIR__ . '/ChecklistVouchers.php');
include_once(__DIR__ . '/Images.php');
include_once(__DIR__ . '/Media.php');
include_once(__DIR__ . '/OccurrenceDeterminations.php');
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
        "previousidentifications" => array("dataType" => "text", "length" => 0),
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
		if($this->conn) {
            $this->conn->close();
        }
	}

    public function createOccurrenceRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $collId = array_key_exists('collid',$data) ? (int)$data['collid'] : 0;
        $sciname = array_key_exists('sciname',$data) ? SanitizerService::cleanInStr($this->conn, $data['sciname']) : '';
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
                $this->conn->query('INSERT INTO guidoccurrences(guid,occid) VALUES("' . $guid . '",' . $newID . ')');
            }
        }
        return $newID;
    }

    public function deleteOccurrenceRecord($occid): int
    {
        $retVal = 1;
        $sql = 'DELETE gd.* FROM omoccurdeterminations AS d LEFT JOIN guidoccurdeterminations AS gd ON d.detid = gd.detid WHERE d.occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurdeterminations WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM guidoccurrences WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omcrowdsourcequeue WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omexsiccatiocclink WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccuraccessstats WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurdatasetlink WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccureditlocks WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccuredits WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurloanslink WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurpoints WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurrencesfulltext WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurrences WHERE occid = ' . (int)$occid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
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
        $sql = 'SELECT DISTINCT mediaid FROM media WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        $retArr['media'] = (int)$rs->num_rows;
        $sql = 'SELECT DISTINCT clid FROM fmvouchers WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        $retArr['checklists'] = (int)$rs->num_rows;
        $sql = 'SELECT DISTINCT idoccurgenetic FROM omoccurgenetic WHERE occid = ' . (int)$occid . ' ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        $retArr['genetic'] = (int)$rs->num_rows;
        return $retArr;
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
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[] = $r->occid;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getOccurrenceData($occid): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields);
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurrences WHERE occid = ' . (int)$occid . ' ';
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
            if($retArr && $retArr['tid'] && (int)$retArr['tid'] > 0){
                $retArr['taxonData'] = (new Taxa)->getTaxonFromTid($retArr['tid']);
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
        $result = $this->conn->query($sql);
        if($result){
            while($r = $result->fetch_object()){
                $nodeArr = array();
                $nodeArr['ocedid'] = $r->ocedid;
                $nodeArr['editor'] = $r->editor;
                $nodeArr['ts'] = substr($r->initialtimestamp,0,16);
                $nodeArr['reviewstatus'] = $r->reviewstatus;
                $nodeArr['appliedstatus'] = $r->appliedstatus;
                $nodeArr['fieldname'] = $r->fieldname;
                $nodeArr['old'] = $r->fieldvalueold;
                $nodeArr['new'] = $r->fieldvaluenew;
                $retArr[] = $nodeArr;
            }
            $result->free();
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
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $retArr[strtolower($r->$identifierField)]['occid'] = $r->occid;
                    $retArr[strtolower($r->$identifierField)]['tid'] = $r->tid;
                }
                $rs->free();
            }
        }
        return $retArr;
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
}
