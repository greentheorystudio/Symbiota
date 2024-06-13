<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');
include_once(__DIR__ . '/UuidFactory.php');

class OccurrenceDataManager{

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
        $connection = new DbConnection();
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
        $sciname = array_key_exists('sciname',$data) ? Sanitizer::cleanInStr($this->conn, $data['sciname']) : '';
        if($collId && $sciname){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $data)){
                    if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                        $fieldNameArr[] = '`' . $field . '`';
                    }
                    else{
                        $fieldNameArr[] = $field;
                    }
                    $fieldValueArr[] = Sanitizer::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
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
                $guid = UuidFactory::getUuidV4();
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
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurdeterminations WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM guidoccurrences WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omcrowdsourcequeue WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omexsiccatiocclink WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccuraccessstats WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurdatasetlink WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccureditlocks WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccuredits WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurloanslink WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurpoints WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurrencesfulltext WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
            $retVal = 0;
        }
        $sql = 'DELETE FROM omoccurrences WHERE occid = ' . (int)$occid . ' ';
        if(!$rs = $this->conn->query($sql)){
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
        $delSql = 'DELETE FROM omoccureditlocks WHERE ts < '.(time()-900).' OR uid = '.$GLOBALS['SYMB_UID'].' ';
        if($this->conn->query($delSql)) {
            $sqlFind = 'SELECT * FROM omoccureditlocks WHERE occid = ' . (int)$occid . ' ';
            $frs = $this->conn->query($sqlFind);
            if(!$frs->num_rows){
                $sql = 'INSERT INTO omoccureditlocks(occid,uid,ts) '.
                    'VALUES ('.(int)$occid.','.$GLOBALS['SYMB_UID'].','.time().')';
                $this->conn->query($sql);
            }
            else{
                $isLocked = true;
            }
        }
        return $isLocked;
    }

    public function getOccurrenceChecklistData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT c.clid, c.name '.
            'FROM fmchecklists AS c LEFT JOIN fmvouchers AS v ON c.clid = v.clid '.
            'WHERE v.occid = ' . (int)$occid . ' ORDER BY c.name ';
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

    public function getOccurrenceData($occid): array
    {
        $retArr = array();
        $fieldNameArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                $fieldNameArr[] = '`' . $field . '`';
            }
            else{
                $fieldNameArr[] = $field;
            }
        }
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
                $retArr['taxonData'] = $this->getTaxonData($retArr['tid']);
            }
        }
        return $retArr;
    }

    public function getOccurrenceDeterminationData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT d.detid, d.identifiedby, d.dateidentified, d.sciname, d.verbatimscientificname, d.tid, d.scientificnameauthorship, ' .
            'd.identificationqualifier, d.iscurrent, d.appliedstatus, d.identificationreferences, d.identificationremarks, d.sortsequence '.
            'FROM omoccurdeterminations AS d '.
            'WHERE d.occid = ' . (int)$occid . ' ORDER BY d.iscurrent DESC, d.sortsequence ';
        //echo '<div>'.$sql.'</div>';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                foreach($fields as $val){
                    $name = $val->name;
                    $nodeArr[$name] = $r->$name;
                }
                if($nodeArr['tid'] && (int)$nodeArr['tid'] > 0){
                    $nodeArr['taxonData'] = $this->getTaxonData($nodeArr['tid']);
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getOccurrenceDuplicateData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT d.duplicateid, d.title, d.description, d.notes '.
            'FROM omoccurduplicates AS d INNER JOIN omoccurduplicatelink AS l ON d.duplicateid = l.duplicateid '.
            'WHERE l.occid = ' . (int)$occid . ' ';
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

    public function getOccurrenceGeneticLinkData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT g.idoccurgenetic, g.identifier, g.resourcename, g.locus, g.resourceurl, g.notes '.
            'FROM omoccurgenetic AS g '.
            'WHERE g.occid = ' . (int)$occid . ' ';
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

    public function getOccurrenceImageData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT i.imgid, i.url, i.thumbnailurl, i.originalurl, i.caption, i.photographer, i.photographeruid, i.sourceurl, i.copyright, '.
            'i.notes, i.username, i.sortsequence, i.initialtimestamp '.
            'FROM images AS i '.
            'WHERE i.occid = ' . (int)$occid . ' ORDER BY i.sortsequence ';
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

    public function getOccurrenceMediaData($occid): array
    {
        $retArr = array();
        $sql = 'SELECT m.mediaid, m.accessuri, m.title, m.creatoruid, m.creator, m.`type`, m.`format`, m.owner, m.furtherinformationurl, '.
            'm.language, m.usageterms, m.rights, m.bibliographiccitation, m.publisher, m.contributor, m.locationcreated, m.description, m.sortsequence '.
            'FROM media AS m '.
            'WHERE m.occid = ' . (int)$occid . ' ORDER BY m.sortsequence ';
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

    public function getOccurrencesByCatalogNumber($catalogNumber, $collid = null): array
    {
        $retArr = array();
        $fieldNameArr = array();
        foreach($this->fields as $field => $fieldArr){
            if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                $fieldNameArr[] = '`' . $field . '`';
            }
            else{
                $fieldNameArr[] = $field;
            }
        }
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM omoccurrences WHERE catalognumber = "' . Sanitizer::cleanInStr($this->conn, $catalogNumber) . '" ';
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

    public function getTaxonData($tid): array
    {
        $retArr = array();
        $sql = 'SELECT t.kingdomId, t.rankid, t.sciname, t.unitind1, t.unitname1, t.unitind2, t.unitname2, t.unitind3, t.unitname3, '.
            't.author, t.tidaccepted, t.parenttid, t.family, t.source, t.notes, t.hybrid, t.securitystatus '.
            'FROM taxa AS t '.
            'WHERE t.tid = ' . (int)$tid . ' ';
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

    public function updateOccurrenceRecord($occId, $editData): int
    {
        $retVal = 0;
        $fieldNameArr = array();
        $sqlPartArr = array();
        if($occId && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    $fieldStr = '';
                    if($field === 'year' || $field === 'month' || $field === 'day' || $field === 'language'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $fieldNameArr[] = $fieldStr;
                    $sqlPartArr[] = $fieldStr . ' = ' . Sanitizer::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            $sql = 'SELECT ' . implode(', ', $fieldNameArr) .
                ' FROM omoccurrences WHERE occid = ' . (int)$occId . ' ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            if($oldData = $rs->fetch_assoc()){
                $sqlEditsBase = 'INSERT INTO omoccuredits(occid, reviewstatus, appliedstatus, uid, fieldname, fieldvaluenew, fieldvalueold) '.
                    'VALUES(' . (int)$occId . ', 1, 1, ' . $GLOBALS['SYMB_UID'] . ', ';
                foreach($fieldNameArr as $fieldName){
                    $cleanedFieldName = str_replace('`','',$fieldName);
                    $oldValue = $oldData[$cleanedFieldName] ? Sanitizer::getSqlValueString($this->conn, $oldData[$cleanedFieldName], $this->fields[$cleanedFieldName]['dataType']) : '""';
                    $newValue = $editData[$cleanedFieldName] ? Sanitizer::getSqlValueString($this->conn, $editData[$cleanedFieldName], $this->fields[$cleanedFieldName]['dataType']) : '""';
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
            }
        }
        return $retVal;
    }
}
