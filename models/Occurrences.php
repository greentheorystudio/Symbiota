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
include_once(__DIR__ . '/../services/SearchService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Occurrences{

	private $conn;

    private $fields = array(
        'occid' => array('dataType' => 'number', 'length' => 10),
        'collid' => array('dataType' => 'number', 'length' => 10),
        'dbpk' => array('dataType' => 'string', 'length' => 150),
        'basisofrecord' => array('dataType' => 'string', 'length' => 32),
        'occurrenceid' => array('dataType' => 'string', 'length' => 255),
        'catalognumber' => array('dataType' => 'string', 'length' => 32),
        'othercatalognumbers' => array('dataType' => 'string', 'length' => 255),
        'ownerinstitutioncode' => array('dataType' => 'string', 'length' => 32),
        'institutionid' => array('dataType' => 'string', 'length' => 255),
        'collectionid' => array('dataType' => 'string', 'length' => 255),
        'datasetid' => array('dataType' => 'string', 'length' => 255),
        'institutioncode' => array('dataType' => 'string', 'length' => 64),
        'collectioncode' => array('dataType' => 'string', 'length' => 64),
        'family' => array('dataType' => 'string', 'length' => 255),
        'verbatimscientificname' => array('dataType' => 'string', 'length' => 255),
        'sciname' => array('dataType' => 'string', 'length' => 255),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'genus' => array('dataType' => 'string', 'length' => 255),
        'specificepithet' => array('dataType' => 'string', 'length' => 255),
        'taxonrank' => array('dataType' => 'string', 'length' => 32),
        'infraspecificepithet' => array('dataType' => 'string', 'length' => 255),
        'scientificnameauthorship' => array('dataType' => 'string', 'length' => 255),
        'taxonremarks' => array('dataType' => 'text', 'length' => 0),
        'identifiedby' => array('dataType' => 'string', 'length' => 255),
        'dateidentified' => array('dataType' => 'string', 'length' => 45),
        'identificationreferences' => array('dataType' => 'text', 'length' => 0),
        'identificationremarks' => array('dataType' => 'text', 'length' => 0),
        'identificationqualifier' => array('dataType' => 'string', 'length' => 255),
        'typestatus' => array('dataType' => 'string', 'length' => 255),
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
        'substrate' => array('dataType' => 'string', 'length' => 500),
        'fieldnotes' => array('dataType' => 'text', 'length' => 0),
        'fieldnumber' => array('dataType' => 'string', 'length' => 45),
        'eventid' => array('dataType' => 'number', 'length' => 11),
        'eventremarks' => array('dataType' => 'text', 'length' => 0),
        'occurrenceremarks' => array('dataType' => 'text', 'length' => 0),
        'informationwithheld' => array('dataType' => 'string', 'length' => 250),
        'datageneralizations' => array('dataType' => 'string', 'length' => 250),
        'associatedoccurrences' => array('dataType' => 'text', 'length' => 0),
        'associatedtaxa' => array('dataType' => 'text', 'length' => 0),
        'dynamicproperties' => array('dataType' => 'text', 'length' => 0),
        'verbatimattributes' => array('dataType' => 'text', 'length' => 0),
        'behavior' => array('dataType' => 'string', 'length' => 500),
        'reproductivecondition' => array('dataType' => 'string', 'length' => 255),
        'cultivationstatus' => array('dataType' => 'number', 'length' => 10),
        'establishmentmeans' => array('dataType' => 'string', 'length' => 150),
        'lifestage' => array('dataType' => 'string', 'length' => 45),
        'sex' => array('dataType' => 'string', 'length' => 45),
        'individualcount' => array('dataType' => 'string', 'length' => 45),
        'samplingprotocol' => array('dataType' => 'string', 'length' => 100),
        'samplingeffort' => array('dataType' => 'string', 'length' => 200),
        'rep' => array('dataType' => 'number', 'length' => 10),
        'preparations' => array('dataType' => 'string', 'length' => 100),
        'locationid' => array('dataType' => 'number', 'length' => 11),
        'island' => array('dataType' => 'string', 'length' => 75),
        'islandgroup' => array('dataType' => 'string', 'length' => 75),
        'waterbody' => array('dataType' => 'string', 'length' => 255),
        'continent' => array('dataType' => 'string', 'length' => 45),
        'country' => array('dataType' => 'string', 'length' => 64),
        'stateprovince' => array('dataType' => 'string', 'length' => 255),
        'county' => array('dataType' => 'string', 'length' => 255),
        'municipality' => array('dataType' => 'string', 'length' => 255),
        'locality' => array('dataType' => 'text', 'length' => 0),
        'localitysecurity' => array('dataType' => 'number', 'length' => 10),
        'localitysecurityreason' => array('dataType' => 'string', 'length' => 100),
        'decimallatitude' => array('dataType' => 'number', 'length' => 0),
        'decimallongitude' => array('dataType' => 'number', 'length' => 0),
        'geodeticdatum' => array('dataType' => 'string', 'length' => 255),
        'coordinateuncertaintyinmeters' => array('dataType' => 'number', 'length' => 10),
        'footprintwkt' => array('dataType' => 'text', 'length' => 0),
        'coordinateprecision' => array('dataType' => 'number', 'length' => 0),
        'locationremarks' => array('dataType' => 'text', 'length' => 0),
        'verbatimcoordinates' => array('dataType' => 'string', 'length' => 255),
        'verbatimcoordinatesystem' => array('dataType' => 'string', 'length' => 255),
        'georeferencedby' => array('dataType' => 'string', 'length' => 255),
        'georeferenceprotocol' => array('dataType' => 'string', 'length' => 255),
        'georeferencesources' => array('dataType' => 'string', 'length' => 255),
        'georeferenceverificationstatus' => array('dataType' => 'string', 'length' => 32),
        'georeferenceremarks' => array('dataType' => 'string', 'length' => 500),
        'minimumelevationinmeters' => array('dataType' => 'number', 'length' => 6),
        'maximumelevationinmeters' => array('dataType' => 'number', 'length' => 6),
        'verbatimelevation' => array('dataType' => 'string', 'length' => 255),
        'minimumdepthinmeters' => array('dataType' => 'number', 'length' => 0),
        'maximumdepthinmeters' => array('dataType' => 'number', 'length' => 0),
        'verbatimdepth' => array('dataType' => 'string', 'length' => 50),
        'disposition' => array('dataType' => 'string', 'length' => 250),
        'storagelocation' => array('dataType' => 'string', 'length' => 100),
        'language' => array('dataType' => 'string', 'length' => 20),
        'processingstatus' => array('dataType' => 'string', 'length' => 45),
        'duplicatequantity' => array('dataType' => 'number', 'length' => 10),
        'labelproject' => array('dataType' => 'string', 'length' => 250),
        'recordenteredby' => array('dataType' => 'string', 'length' => 250),
        'dateentered' => array('dataType' => 'date', 'length' => 0),
        'datelastmodified' => array('dataType' => 'timestamp', 'length' => 0)
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

    public function batchUpdateOccurrenceData($searchTermsArr, $field, $oldValue, $newValue, $matchType): int
    {
        $returnVal = 0;
        if($searchTermsArr && $field && (($matchType === 'part' && $oldValue) || ($matchType === 'whole' && ($oldValue || $newValue)))){
            $sqlWhere = (new SearchService)->prepareOccurrenceWhereSql($searchTermsArr);
            if($sqlWhere){
                $fromStr = (new SearchService)->setFromSql('occurrence');
                $fromStr .= ' ' . (new SearchService)->setTableJoinsSql($searchTermsArr);
                $whereStr = (new SearchService)->setWhereSql($sqlWhere, 'occurrence', false);
                $sql = str_replace('FROM', 'UPDATE', $fromStr);
                if($matchType === 'part'){
                    $sql .= 'SET o.' . SanitizerService::cleanInStr($this->conn, $field) . ' = REPLACE(' . SanitizerService::cleanInStr($this->conn, $field) . ', "' . SanitizerService::cleanInStr($this->conn, $oldValue) . '", ' . ($newValue ? SanitizerService::getSqlValueString($this->conn, SanitizerService::cleanInStr($this->conn, $newValue), $this->fields[$field]['dataType']) : '""') . ') ';
                }
                else{
                    $sql .= 'SET o.' . SanitizerService::cleanInStr($this->conn, $field) . ' = ' . SanitizerService::getSqlValueString($this->conn, SanitizerService::cleanInStr($this->conn, $newValue), $this->fields[$field]['dataType']) . ' ';
                }
                if($matchType === 'part'){
                    $sql .= 'WHERE o.' . SanitizerService::cleanInStr($this->conn, $field) . ' LIKE "%' . SanitizerService::cleanInStr($this->conn, $oldValue) . '%" ';
                }
                else{
                    $sql .= 'WHERE ' . ($oldValue ? (SanitizerService::cleanInStr($this->conn, ('o.' . $field)) . ' = "' . SanitizerService::cleanInStr($this->conn, $oldValue) . '" ') : ('ISNULL(' . SanitizerService::cleanInStr($this->conn, ('o.' . $field)) . ') '));
                }
                $sql .= 'AND ' . substr($whereStr, 6);
                if($this->conn->query($sql)){
                    $returnVal = 1;
                }
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
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," ssp. "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% ssp. %" ';
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," ssp "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% ssp %" ';
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspec. "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subspec. %" ';
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspec "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subspec %" ';
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subspecies "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subspecies %" ';
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," subsp "," subsp. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% subsp %" ';
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," var "," var. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% var %" ';
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql5 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," variety "," var. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% variety %" ';
            if($this->conn->query($sql5)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," forma "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% forma %" ';
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," form "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% form %" ';
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," fo. "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% fo. %" ';
            if($this->conn->query($sql6)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql6 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," fo "," f. ") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% fo %" ';
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
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," cf. "," "), identificationQualifier = "cf." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% cf. %" ';
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," cf "," "), identificationQualifier = "cf." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% cf %" ';
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," aff. "," "), identificationQualifier = "aff." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% aff. %" ';
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," aff "," "), identificationQualifier = "aff." '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% aff %" ';
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
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname,"?","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "%?%" ';
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
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp." ';
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql1 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp" ';
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp. nov.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp. nov." ';
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp. nov","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp. nov" ';
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp nov.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp nov." ';
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql2 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," sp nov","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% sp nov" ';
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," spp.","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% spp." ';
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," spp","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% spp" ';
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE omoccurrences '.
                'SET sciname = REPLACE(sciname," group","") '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname LIKE "% group" ';
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
                if($field !== 'occid' && $field !== 'dateentered' && $field !== 'recordenteredby' && array_key_exists($field, $data)){
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
            if($this->conn->query($sql)){
                $newID = $this->conn->insert_id;
                $guid = UuidService::getUuidV4();
                $this->conn->query('UPDATE omcollectionstats SET recordcnt = recordcnt + 1 WHERE collid = ' . $collId);
                $this->conn->query('INSERT INTO guidoccurrences(guid, occid) VALUES("' . $guid . '",' . $newID . ')');
            }
        }
        return $newID;
    }

    public function createOccurrenceRecordsFromUploadData($collId, $index): int
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
                    'WHERE collid = ' . (int)$collId . ' AND ISNULL(occid) LIMIT ' . ($index * 50000) . ', 50000 ';
                if($this->conn->query($sql)){
                    $retVal = $this->conn->affected_rows;
                }
            }
        }
        return $retVal;
    }

    public function deleteOccurrenceRecord($idType, $id): int
    {
        $retVal = 1;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id . ' ';
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ') ';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ') LIMIT 10000 ';
        }
        if($whereStr){
            (new OccurrenceDeterminations)->deleteOccurrenceDeterminationRecords($idType, $id);
            (new Images)->deleteAssociatedImageRecords($idType, $id);
            (new OccurrenceGeneticLinks)->deleteOccurrenceGeneticLinkageRecords($idType, $id);
            (new ChecklistVouchers)->deleteOccurrenceChecklistVoucherRecords($idType, $id);
            (new Media)->deleteAssociatedMediaRecords($idType, $id);
            (new OccurrenceMeasurementsOrFacts)->deleteOccurrenceMofRecords($idType, $id);
            $sql = 'DELETE FROM guidoccurrences WHERE ' . $whereStr . ' ';
            if(!$this->conn->query($sql)){
                $retVal = 0;
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
                $sql = 'DELETE FROM omoccurrences WHERE ' . $whereStr . ' ';
                if($this->conn->query($sql)){
                    $retVal = $this->conn->affected_rows;
                }
            }
        }
        return $retVal;
    }

    public function evaluateOccurrenceForDeletion($occid): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT imgid FROM images WHERE occid = ' . (int)$occid . ' ';
        $rs = $this->conn->query($sql);
        $retArr['images'] = (int)$rs->num_rows;
        $rs->free();
        $sql = 'SELECT DISTINCT mediaid FROM media WHERE occid = ' . (int)$occid . ' ';
        $rs = $this->conn->query($sql);
        $retArr['media'] = (int)$rs->num_rows;
        $rs->free();
        $sql = 'SELECT DISTINCT clid FROM fmvouchers WHERE occid = ' . (int)$occid . ' ';
        $rs = $this->conn->query($sql);
        $retArr['checklists'] = (int)$rs->num_rows;
        $rs->free();
        $sql = 'SELECT DISTINCT idoccurgenetic FROM omoccurgenetic WHERE occid = ' . (int)$occid . ' ';
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
            if($rs = $this->conn->query($sql)){
                if($row = $rs->fetch_object()){
                    $retCnt = $row->taxacnt;
                }
                $rs->free();
            }
        }
        return $retCnt;
    }

    public function getBatchUpdateCount($searchTermsArr, $field, $oldValue, $matchType): int
    {
        $returnVal = 0;
        if($searchTermsArr && $field && ($matchType !== 'part' || $oldValue)){
            $sqlWhere = (new SearchService)->prepareOccurrenceWhereSql($searchTermsArr);
            if($sqlWhere){
                $fromStr = (new SearchService)->setFromSql('occurrence');
                $fromStr .= ' ' . (new SearchService)->setTableJoinsSql($searchTermsArr);
                $whereStr = (new SearchService)->setWhereSql($sqlWhere, 'occurrence', false);
                $sql = 'SELECT COUNT(DISTINCT occid) AS cnt ' . $fromStr . $whereStr . ' ';
                if($matchType === 'part'){
                    $sql .= 'AND ' . SanitizerService::cleanInStr($this->conn, $field) . ' LIKE "%' . SanitizerService::cleanInStr($this->conn, $oldValue) . '%" ';
                }
                else{
                    $sql .= $oldValue ? ('AND ' . SanitizerService::cleanInStr($this->conn, $field) . ' = "' . SanitizerService::cleanInStr($this->conn, $oldValue) . '" ') : ('AND ISNULL(' . SanitizerService::cleanInStr($this->conn, $field) . ') ');
                }
                $result = $this->conn->query($sql);
                if($row = $result->fetch_array(MYSQLI_ASSOC)){
                    $returnVal = (int)$row['cnt'];
                }
                $result->free();
            }
        }
        return $returnVal;
    }

    public function getLock($occid): int
    {
        $isLocked = 0;
        $delSql = 'DELETE FROM omoccureditlocks WHERE ts < ' . (time() - 900) . ' OR uid = ' . $GLOBALS['SYMB_UID'] . ' ';
        if($this->conn->query($delSql)) {
            $sqlFind = 'SELECT * FROM omoccureditlocks WHERE occid = ' . (int)$occid . ' ';
            $frs = $this->conn->query($sqlFind);
            if($frs->num_rows) {
                $isLocked = true;
            }
            else {
                $sql = 'INSERT INTO omoccureditlocks(occid, uid, ts) '.
                    'VALUES (' . (int)$occid . ',' . $GLOBALS['SYMB_UID'] . ',' . time() . ')';
                $this->conn->query($sql);
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
            $sql = 'SELECT occid FROM omoccurrences WHERE collid  = ' . (int)$collid . ' '.
                'AND occid NOT IN(SELECT occid FROM uploadspectemp WHERE collid = ' . (int)$collid . ') LIMIT 10000 ';
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
                    $retArr[$row[$identifierField]]['occid'] = $row['occid'];
                    $retArr[$row[$identifierField]]['tid'] = $row['tid'];
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

    public function getTaxonOccurrenceCount($tid): int
    {
        $retVal = 0;
        if($tid){
            $sql ='SELECT COUNT(occid) AS cnt FROM omoccurrences WHERE tid = ' . (int)$tid;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $retVal = $row->cnt;
            }
            $result->free();
        }
        return $retVal;
    }

    public function getUnlinkedSciNames($collid): array
    {
        $retArr = array();
        if((int)$collid){
            $sql = 'SELECT DISTINCT sciname '.
                'FROM omoccurrences '.
                'WHERE collid = ' . (int)$collid . ' AND ISNULL(tid) AND sciname IS NOT NULL '.
                'ORDER BY sciname ';
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

    public function remapTaxonOccurrences($tid, $targetTid): int
    {
        $retVal = 0;
        if($tid && $targetTid){
            $sql = 'UPDATE omoccurrences SET tid = ' . (int)$targetTid . ' WHERE tid = ' . (int)$tid . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function removePrimaryIdentifiersFromUploadedOccurrences($collid): int
    {
        $retVal = 0;
        if($collid){
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN uploadspectemp AS u ON o.occid = u.occid '.
                'SET o.dbpk = NULL '.
                'WHERE o.collid  = ' . (int)$collid . ' AND u.occid IS NOT NULL ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function removeTaxonFromOccurrenceRecords($tid): int
    {
        $retVal = 1;
        $sql = 'UPDATE omoccurrences SET tid = NULL WHERE tid = ' . (int)$tid . ' ';
        if(!$this->conn->query($sql)){
            $retVal = 0;
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
            if($this->conn->query($sql)){
                $sql2 = 'UPDATE omoccurrences SET sciname = "' . SanitizerService::cleanInStr($this->conn, $cleanedSciname) . '"'.
                    ((int)$tid > 0 ? ', tid = ' . (int)$tid . ' ' : ' ').
                    'WHERE collid = ' . (int)$collid . ' AND sciname = "' . SanitizerService::cleanInStr($this->conn, $sciname) . '" ';
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
            if($this->conn->query($sql)){
                $retCnt = $this->conn->affected_rows;
                $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
                    'SET d.tid = ' . (int)$tid . ' '.
                    'WHERE o.collid = ' . (int)$collid . ' AND d.sciname = "' . $sciname . '" ';
                $this->conn->query($sql2);

                $sql3 = 'UPDATE omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
                    'SET i.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND o.sciname = "' . $sciname . '" ';
                $this->conn->query($sql3);

                $sql4 = 'UPDATE omoccurrences AS o LEFT JOIN media AS m ON o.occid = m.occid '.
                    'SET m.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND o.sciname = "' . $sciname . '" ';
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
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
            foreach($rankIdArr as $id){
                $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.SciName '.
                    'SET o.tid = t.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(o.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid = ' . $id . ' ';
                if($this->conn->query($sql)){
                    $retCnt += $this->conn->affected_rows;
                }
            }
            $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.SciName '.
                'SET o.tid = t.tid '.
                'WHERE o.collid = ' . (int)$collid . ' AND ISNULL(o.tid) AND t.kingdomId = ' . (int)$kingdomId . ' AND t.rankid <= 20 ';
            if($this->conn->query($sql)){
                $retCnt += $this->conn->affected_rows;
            }
            if($retCnt > 0){
                $sql = 'UPDATE omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
                    'SET i.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND i.imgid IS NOT NULL ';
                $this->conn->query($sql);

                $sql2 = 'UPDATE omoccurrences AS o LEFT JOIN media AS m ON o.occid = m.occid '.
                    'SET m.tid = o.tid '.
                    'WHERE o.collid = ' . (int)$collid . ' AND m.mediaid IS NOT NULL ';
                $this->conn->query($sql2);
            }
        }
        return $retCnt;
    }

    public function updateOccurrenceEventId($occId, $eventId, $updateData = null): int
    {
        $retVal = 0;
        if($occId && $eventId){
            $sql = 'UPDATE omoccurrences SET eventid = ' . (int)$eventId . ' WHERE occid = ' . (int)$occId . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
                if($updateData){
                    $sql = 'SELECT locationid FROM omoccurrences WHERE occid = ' . (int)$occId . ' ';
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

    public function updateOccurrenceLocationId($occId, $locationId, $updateData = null): int
    {
        $retVal = 0;
        if($occId && $locationId){
            $sql = 'UPDATE omoccurrences SET locationid = ' . (int)$locationId . ' WHERE occid = ' . (int)$occId . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
                if($updateData){
                    $sql = 'SELECT eventid FROM omoccurrences WHERE occid = ' . (int)$occId . ' ';
                    if($result = $this->conn->query($sql)){
                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $result->free();
                        if((int)$row['eventid'] > 0){
                            $retVal = (new OccurrenceCollectingEvents)->updateCollectingEventLocation((int)$row['eventid'], $locationId);
                        }
                    }
                    if($retVal){
                        $retVal = (new OccurrenceLocations)->updateOccurrencesFromLocationData($locationId);
                    }
                }
            }
        }
        return $retVal;
    }

    public function updateOccurrenceRecord($occId, $editData, $determinationUpdate = null): int
    {
        $retVal = 0;
        $fieldNameArr = array();
        $sqlPartArr = array();
        if($occId && $editData){
            if(!$determinationUpdate && array_key_exists('sciname', $editData) && array_key_exists('tid', $editData)){
                $determinationData = array();
                $determinationFields = (new OccurrenceDeterminations)->getDeterminationFields();
                foreach($editData as $field => $value){
                    if(array_key_exists($field, $determinationFields)){
                        $determinationData[$field] = $value;
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
                $rs = $this->conn->query($sql);
                if($oldData = $rs->fetch_assoc()){
                    $sqlEditsBase = 'INSERT INTO omoccuredits(occid, reviewstatus, appliedstatus, uid, fieldname, fieldvaluenew, fieldvalueold) '.
                        'VALUES(' . (int)$occId . ', 1, 1, ' . $GLOBALS['SYMB_UID'] . ', ';
                    foreach($fieldNameArr as $fieldName){
                        $cleanedFieldName = str_replace('`','',$fieldName);
                        $oldValue = $oldData[$cleanedFieldName] ? SanitizerService::getSqlValueString($this->conn, $oldData[$cleanedFieldName], $this->fields[$cleanedFieldName]['dataType']) : '""';
                        $newValue = $editData[$cleanedFieldName] ? SanitizerService::getSqlValueString($this->conn, $editData[$cleanedFieldName], $this->fields[$cleanedFieldName]['dataType']) : '""';
                        $sqlEdit = $sqlEditsBase . '"' . $cleanedFieldName . '",' . $newValue . ',' . $oldValue . ') ';
                        $this->conn->query($sqlEdit);
                    }
                }
                $rs->free();
                $sql = 'UPDATE omoccurrences SET ' . implode(', ', $sqlPartArr) . ' '.
                    'WHERE occid = ' . (int)$occId . ' ';
                if($this->conn->query($sql)){
                    $retVal = 1;
                    if($determinationUpdate){
                        $newTid = array_key_exists('tid', $editData) ? (int)$editData['tid'] : 0;
                        (new ChecklistVouchers)->updateTidFromOccurrenceRecord($occId, $newTid);
                        (new Images)->updateTidFromOccurrenceRecord($occId, $newTid);
                        (new Media)->updateTidFromOccurrenceRecord($occId, $newTid);
                    }
                }
            }
        }
        return $retVal;
    }

    public function updateOccurrenceRecordsFromUploadData($collId, $mappedFields, $index): int
    {
        $skipFields = array('occid', 'collid', 'dbpk', 'recordedbyid', 'associatedoccurrences', 'recordenteredby', 'dateentered', 'datelastmodified');
        $retVal = 0;
        $sqlPartArr = array();
        if($collId){
            foreach($this->fields as $field => $fieldArr){
                if(!in_array($field, $skipFields) && in_array($field, $mappedFields, true)){
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
                $idArr = array();
                $sql = 'SELECT upspid FROM uploadspectemp WHERE collid = ' . (int)$collId . ' AND occid IS NOT NULL LIMIT ' . ($index * 50000) . ', 25000 ';
                if($result = $this->conn->query($sql)){
                    while($row = $result->fetch_assoc()){
                        $idArr[] = $row['upspid'];
                    }
                    $result->free();
                    if(count($idArr) > 0){
                        $sql = 'UPDATE omoccurrences AS o LEFT JOIN uploadspectemp AS u ON o.occid = u.occid SET ' . implode(', ', $sqlPartArr) . ' '.
                            'WHERE u.upspid IN(' . implode(',', $idArr) . ') ';
                        if($this->conn->query($sql)){
                            $retVal = $this->conn->affected_rows;
                        }
                    }
                }
            }
        }
        return $retVal;
    }
}
