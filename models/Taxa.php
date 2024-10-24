<?php
include_once(__DIR__ . '/Images.php');
include_once(__DIR__ . '/TaxonHierarchy.php');
include_once(__DIR__ . '/TaxonKingdoms.php');
include_once(__DIR__ . '/TaxonVernaculars.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/TaxonomyService.php');

class Taxa{

	private $conn;

    private $fields = array(
        "tid" => array("dataType" => "number", "length" => 10),
        "kingdomid" => array("dataType" => "number", "length" => 11),
        "rankid" => array("dataType" => "number", "length" => 5),
        "sciname" => array("dataType" => "string", "length" => 250),
        "unitind1" => array("dataType" => "string", "length" => 1),
        "unitname1" => array("dataType" => "string", "length" => 50),
        "unitind2" => array("dataType" => "string", "length" => 1),
        "unitname2" => array("dataType" => "string", "length" => 50),
        "unitind3" => array("dataType" => "string", "length" => 15),
        "unitname3" => array("dataType" => "string", "length" => 35),
        "author" => array("dataType" => "string", "length" => 100),
        "tidaccepted" => array("dataType" => "number", "length" => 10),
        "parenttid" => array("dataType" => "number", "length" => 10),
        "family" => array("dataType" => "string", "length" => 50),
        "source" => array("dataType" => "string", "length" => 250),
        "notes" => array("dataType" => "string", "length" => 250),
        "hybrid" => array("dataType" => "string", "length" => 50),
        "securitystatus" => array("dataType" => "number", "length" => 10),
        "modifieduid" => array("dataType" => "number", "length" => 10),
        "modifiedtimestamp" => array("dataType" => "date", "length" => 0),
        "initialtimestamp" => array("dataType" => "timestamp", "length" => 0)
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

    public function addTaxonIdentifier($tid, $idName, $id): int
    {
        $returnVal = 0;
        if($tid && $idName && $id){
            $identifierName = SanitizerService::cleanInStr($this->conn, $idName);
            $identifier = SanitizerService::cleanInStr($this->conn, $id);
            $sql = 'INSERT IGNORE INTO taxaidentifiers(tid,`name`,identifier) VALUES('.
                $tid . ',"' . $identifierName . '", "' . $identifier . '")';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
            else{
                $sql = 'UPDATE taxaidentifiers SET identifier = "' . $identifier . '" WHERE tid = ' . $tid . ' AND `name` = "' . $identifierName . '" ';
                if($this->conn->query($sql)){
                    $returnVal = 1;
                }
            }
        }
        return $returnVal;
    }

    public function createTaxaRecord($data): int
    {
        $newID = 0;
        $fieldNameArr = array();
        $fieldValueArr = array();
        $data = $this->validateNewTaxaData($data);
        foreach($this->fields as $field => $fieldArr){
            if($field !== 'tid' && array_key_exists($field, $data)){
                if($field === 'source'){
                    $fieldNameArr[] = '`' . $field . '`';
                }
                else{
                    $fieldNameArr[] = $field;
                }
                $fieldValueArr[] = SanitizerService::getSqlValueString($this->conn, $data[$field], $fieldArr['dataType']);
            }
        }
        $fieldNameArr[] = 'modifieduid';
        $fieldValueArr[] = $GLOBALS['SYMB_UID'];
        $fieldNameArr[] = 'modifiedtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $fieldNameArr[] = 'initialtimestamp';
        $fieldValueArr[] = '"' . date('Y-m-d H:i:s') . '"';
        $sql = 'INSERT IGNORE INTO taxa(' . implode(',', $fieldNameArr) . ') '.
            'VALUES (' . implode(',', $fieldValueArr) . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
            if((int)$data['acceptstatus'] === 1){
                $sqlNewTaxUpdate = 'UPDATE taxa SET tidaccepted = ' . $newID . ' WHERE tid = ' . $newID . ' ';
                $this->conn->query($sqlNewTaxUpdate);
            }
            if(array_key_exists('source-name', $data) && array_key_exists('source-id', $data) && $data['source-name'] && $data['source-id']){
                $sqlId = 'INSERT IGNORE INTO taxaidentifiers(tid, `name`, identifier) VALUES('.
                    $newID . ', "' . SanitizerService::cleanInStr($this->conn, $data['source-name']) . '", '.
                    '"' . SanitizerService::cleanInStr($this->conn, $data['source-id']) . '") ';
                //echo $sqlId; exit;
                $this->conn->query($sqlId);
            }
        }
        return $newID;
    }

    public function deleteTaxon($tid): int
    {
        $retVal = 1;
        if($tid){
            $sql = 'DELETE FROM taxaenumtree WHERE tid = ' . (int)$tid . ' OR parenttid = ' . (int)$tid . ' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }

            $sql = 'DELETE FROM taxavernaculars WHERE TID = ' . (int)$tid . ' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }

            $sql = 'DELETE FROM taxa WHERE TID = ' . (int)$tid . ' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function editTaxonParent($parentTid, $tId = null): string
    {
        $status = '';
        if($tId && is_numeric($parentTid) && $parentTid){
            $sql = 'UPDATE taxa '.
                'SET parenttid = ' . $parentTid . ' '.
                'WHERE tid = ' . (int)$tId . ' ';
            if(!$this->conn->query($sql)){
                $status = 'Unable to edit taxonomic placement.';
            }
        }
        return $status;
    }

    public function evaluateTaxonForDeletion($tid): int
    {
        $retVal = 0;
        if($tid){
            $sql = 'SELECT DISTINCT TID FROM taxa '.
                'WHERE TID IN(SELECT tid FROM taxa WHERE parenttid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM taxa WHERE TID <> tidaccepted AND tidaccepted = '.$tid.') '.
                'OR TID IN(SELECT tid FROM fmchklsttaxalink WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM fmdyncltaxalink WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM fmvouchers WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM glossarysources WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM glossarytaxalink WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM images WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM kmchartaxalink WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM kmdescr WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM media WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM omoccurassociations WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM omoccurdeterminations WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM omoccurrences WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM referencetaxalink WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM taxadescrblock WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM taxamaps WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM tmtraittaxalink WHERE tid = '.$tid.') '.
                'OR TID IN(SELECT tid FROM usertaxonomy WHERE tid = '.$tid.') ';
            //echo $sql;
            $rs = $this->conn->query($sql);
            $retVal = $rs->num_rows;
            $rs->free();
        }
        return $retVal;
    }

    public function getAcceptedTaxaByTaxonomicGroup($parentTid, $index, $rankId = null): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT TID, SciName, parenttid FROM taxa '.
                'WHERE TID = tidaccepted AND (TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . $parentTid . ') '.
                'OR parenttid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . $parentTid . ')) ';
            if($rankId){
                $sql .= 'AND RankId = ' . $rankId . ' ';
            }
            $sql .= 'ORDER BY SciName '.
                'LIMIT ' . (($index - 1) * 50000) . ', 50000';
            //echo $sql;
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    $nodeArr['tid'] = $r->TID;
                    $nodeArr['sciname'] = $r->SciName;
                    $nodeArr['parenttid'] = $r->parenttid;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }

    public function getAudioCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(m.mediaid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN media AS m ON t.TID = m.tid '.
            'WHERE (te.parenttid = ' . $tid . ' OR t.TID = ' . $tid . ') AND t.TID = t.tidaccepted AND (m.format LIKE "audio/%" OR ISNULL(m.format)) ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(m.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'ORDER BY t.RankId, t.SciName '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getAutocompleteSciNameList($opts): array
    {
        $retArr = array();
        $term = array_key_exists('term', $opts) ? SanitizerService::cleanInStr($this->conn, $opts['term']) : null;
        if($term){
            $acceptedOnly = array_key_exists('acceptedonly', $opts) && (($opts['acceptedonly'] === 'true' || (int)$opts['acceptedonly'] === 1));
            $hideAuth = array_key_exists('hideauth', $opts) && (($opts['hideauth'] === 'true' || (int)$opts['hideauth'] === 1));
            $hideProtected = array_key_exists('hideprotected', $opts) && (($opts['hideprotected'] === 'true' || (int)$opts['hideprotected'] === 1));
            $limit = array_key_exists('limit', $opts) ? (int)$opts['limit'] : null;
            $rankHigh = array_key_exists('rhigh', $opts) ? (int)$opts['rhigh'] : null;
            $rankLimit = array_key_exists('rlimit', $opts) ? (int)$opts['rlimit'] : null;
            $rankLow = array_key_exists('rlow', $opts) ? (int)$opts['rlow'] : null;
            $sql = 'SELECT DISTINCT tid, kingdomId, rankid, sciname, unitind1, unitname1, unitind2, unitname2, unitind3, unitname3, '.
                'author, tidaccepted, parenttid, family, source, notes, hybrid, securitystatus  '.
                'FROM taxa WHERE sciname LIKE "' . $term . '%" ';
            if($rankLimit){
                $sql .= 'AND rankid = ' . $rankLimit . ' ';
            }
            else{
                if($rankLow){
                    $sql .= 'AND rankid >= ' . $rankLow . ' ';
                }
                if($rankHigh){
                    $sql .= 'AND rankid <= ' . $rankHigh . ' ';
                }
            }
            if($hideProtected){
                $sql .= 'AND securitystatus <> 1 ';
            }
            if($acceptedOnly){
                $sql .= 'AND tid = tidaccepted ';
            }
            $sql .= 'ORDER BY sciname ';
            if($limit){
                $sql .= 'LIMIT ' . $limit . ' ';
            }
            if($rs = $this->conn->query($sql)){
                $fields = mysqli_fetch_fields($rs);
                while($r = $rs->fetch_object()){
                    $scinameArr = array();
                    $label = $r->sciname . ($hideAuth ? '' : (' ' . $r->author));
                    $scinameArr['label'] = $label;
                    foreach($fields as $val){
                        $name = $val->name;
                        $scinameArr[$name] = $r->$name;
                    }
                    $retArr[] = $scinameArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }

    public function getChildTaxaFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT TID, SciName, Author, RankId, family '.
            'FROM taxa WHERE parenttid = '.$tid.' AND TID = tidaccepted ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['tid'] = $r->TID;
                $nodeArr['sciname'] = $r->SciName;
                $nodeArr['author'] = $r->Author;
                $nodeArr['rankid'] = $r->RankId;
                $nodeArr['family'] = $r->family;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getCloseTaxaMatches($name, $levDistance, $kingdomId = null): array
    {
        $retArr = array();
        $sql = 'SELECT tid, sciname FROM taxa ';
        if($kingdomId){
            $sql .= 'WHERE kingdomId = ' . $kingdomId . ' ';
        }
        $sql .= 'ORDER BY sciname ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                if($name !== $r->sciname && levenshtein($name,$r->sciname) <= $levDistance){
                    $valArr = array();
                    $valArr['tid'] = $r->tid;
                    $valArr['sciname'] = $r->sciname;
                    $retArr[] = $valArr;
                }
            }
        }
        return $retArr;
    }

    public function getDescriptionCountsForTaxonomicGroup($tid, $index): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(tdb.tdbid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxadescrblock AS tdb ON t.TID = tdb.tid '.
            'WHERE (te.parenttid = ' . $tid . ' OR t.TID = ' . $tid . ') AND t.TID = t.tidaccepted '.
            'GROUP BY t.TID '.
            'ORDER BY t.RankId, t.SciName '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getIdentifiersForTaxonomicGroup($tid, $index, $source): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, ti.identifier '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN taxaidentifiers AS ti ON t.TID = ti.tid '.
            'WHERE (te.parenttid = ' . $tid . ' OR t.TID = ' . $tid . ') AND ti.name = "' . SanitizerService::cleanInStr($this->conn, $source) . '" '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['identifier'] = $row->identifier;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getImageCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(i.imgid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN images AS i ON t.TID = i.tid '.
            'WHERE (te.parenttid = ' . $tid . ' OR t.TID = ' . $tid . ') AND t.TID = t.tidaccepted ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(i.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'ORDER BY t.RankId, t.SciName '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function getRankArrForTaxonomicGroup($parentTid): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT t.RankId, tu.rankname FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.RankId = tu.rankid AND t.kingdomId = tu.kingdomid '.
                'WHERE t.TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = '.$parentTid.') '.
                'OR t.parenttid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = '.$parentTid.') '.
                'ORDER BY t.RankId ';
            //echo $sql;
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    $nodeArr['rankid'] = $r->RankId;
                    $nodeArr['rankname'] = $r->rankname;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }

    public function getTaxaIdDataFromNameArr($nameArr): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT tid, sciname FROM taxa  '.
            'WHERE sciname IN("' . implode('","', $nameArr) . '") ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[strtolower($r->sciname)]['tid'] = $r->tid;
                $retArr[strtolower($r->sciname)]['sciname'] = $r->sciname;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonFromSciname($sciname, $kingdomId, $includeCommonNames = false, $includeChildren = false): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 't');
        $fieldNameArr[] = 'k.kingdom_name AS kingdom';
        $fieldNameArr[] = 't2.sciname AS acceptedsciname';
        $fieldNameArr[] = 't3.sciname AS parentsciname';
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
            'LEFT JOIN taxa AS t3 ON t.parenttid = t3.TID '.
            'LEFT JOIN taxonkingdoms AS k ON t.kingdomId = k.kingdom_id '.
            'WHERE t.SciName = "' . SanitizerService::cleanInStr($this->conn, $sciname) . '" AND t.kingdomId = ' . (int)$kingdomId . ' ';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
                $retArr['identifiers'] = $this->getTaxonIdentifiersFromTid($r->tid);
                if($includeCommonNames){
                    $retArr['commonnames'] = (new TaxonVernaculars)->getCommonNamesFromTid($r->tid);
                }
                if($includeChildren){
                    $retArr['children'] = $this->getChildTaxaFromTid($r->tid);
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonFromTid($tid, $includeCommonNames = false, $includeChildren = false): array
    {
        $retArr = array();
        $fieldNameArr = (new DbService)->getSqlFieldNameArrFromFieldData($this->fields, 't');
        $fieldNameArr[] = 'k.kingdom_name AS kingdom';
        $fieldNameArr[] = 't2.sciname AS acceptedsciname';
        $fieldNameArr[] = 't3.sciname AS parentsciname';
        $sql = 'SELECT ' . implode(',', $fieldNameArr) . ' '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
            'LEFT JOIN taxa AS t3 ON t.parenttid = t3.TID '.
            'LEFT JOIN taxonkingdoms AS k ON t.kingdomId = k.kingdom_id '.
            'WHERE t.tid = ' . (int)$tid . ' ';
        if($rs = $this->conn->query($sql)){
            $fields = mysqli_fetch_fields($rs);
            if($r = $rs->fetch_object()){
                foreach($fields as $val){
                    $name = $val->name;
                    $retArr[$name] = $r->$name;
                }
                $retArr['identifiers'] = $this->getTaxonIdentifiersFromTid($tid);
                if($includeCommonNames){
                    $retArr['commonnames'] = (new TaxonVernaculars)->getCommonNamesFromTid($tid);
                }
                if($includeChildren){
                    $retArr['children'] = $this->getChildTaxaFromTid($tid);
                }
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonIdentifiersFromTid($tid): array
    {
        $retArr = array();
        $sql = 'SELECT `name`, identifier FROM taxaidentifiers WHERE tid = '.$tid.' ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nodeArr = array();
                $nodeArr['name'] = $r->name;
                $nodeArr['identifier'] = $r->identifier;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonomicTreeChildNodes($tId, $limitToAccepted, $includeImage): array
    {
        $retArr = array();
        if(!$limitToAccepted){
            $sql = 'SELECT t.TID, t.SciName, t.Author, tu.rankname '.
                'FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid  '.
                'WHERE t.tidaccepted = '.$tId.' AND TID <> tidaccepted '.
                'ORDER BY tu.rankid, t.SciName ';
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $nTid = $r->TID;
                    $nodeArr = array();
                    $nodeArr['tid'] = $nTid;
                    $nodeArr['sciname'] = $r->SciName;
                    $nodeArr['author'] = $r->Author;
                    $nodeArr['rankname'] = $r->rankname;
                    $nodeArr['nodetype'] = 'synonym';
                    $nodeArr['expandable'] = false;
                    $nodeArr['lazy'] = false;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }

        $sql = 'SELECT t.TID, t.SciName, t.Author, tu.rankname '.
            'FROM taxa AS t LEFT JOIN taxonunits AS tu ON t.kingdomId = tu.kingdomid AND t.rankid = tu.rankid  '.
            'WHERE t.parenttid = '.$tId.' AND TID = tidaccepted '.
            'ORDER BY tu.rankid, t.SciName ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nTid = $r->TID;
                $expandable = $this->taxonHasChildren($nTid);
                $nodeArr = array();
                $nodeArr['tid'] = $nTid;
                $nodeArr['sciname'] = $r->SciName;
                $nodeArr['author'] = $r->Author;
                $nodeArr['rankname'] = $r->rankname;
                $nodeArr['nodetype'] = 'child';
                $nodeArr['expandable'] = $expandable;
                $nodeArr['lazy'] = $expandable;
                if($includeImage){
                    $nodeArr['image'] = null;
                    $imageArr = (new Images)->getImageArrByTaxonomicGroup($nTid, false, 1);
                    if(count($imageArr) > 0){
                        $nodeArr['image'] = $imageArr[0]['url'];
                    }
                }
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTaxonomicTreeKingdomNodes(): array
    {
        $retArr = array();
        $sql = 'SELECT TID, SciName, Author FROM taxa '.
            'WHERE RankId = 10 AND TID = tidaccepted '.
            'ORDER BY SciName ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $nTid = $r->TID;
                $expandable = $this->taxonHasChildren($nTid);
                $nodeArr = array();
                $nodeArr['tid'] = $nTid;
                $nodeArr['sciname'] = $r->SciName;
                $nodeArr['author'] = $r->Author;
                $nodeArr['rankname'] = 'Kingdom';
                $nodeArr['nodetype'] = 'child';
                $nodeArr['expandable'] = $expandable;
                $nodeArr['lazy'] = $expandable;
                $retArr[] = $nodeArr;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getTid($sciName, $kingdomid = null, $rankid = null, $author = null): int
    {
        $retTid = 0;
        if($sciName){
            $sql = 'SELECT tid FROM taxa WHERE sciname = "' . SanitizerService::cleanInStr($this->conn, $sciName) . '" ';
            if($kingdomid){
                $sql .= 'AND kingdomId = ' . (int)$kingdomid . ' ';
            }
            if($rankid){
                $sql .= 'AND rankid = ' . (int)$rankid . ' ';
            }
            if($author){
                $sql .= 'AND author = "' . SanitizerService::cleanInStr($this->conn, $author) . '" ';
            }
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $retTid = (int)$r->tid;
            }
            $rs->close();
        }
        return $retTid;
    }

    public function getUnacceptedTaxaByTaxonomicGroup($parentTid, $index, $rankId = null): array
    {
        $retArr = array();
        if($parentTid){
            $sql = 'SELECT DISTINCT TID, SciName FROM taxa '.
                'WHERE TID <> tidaccepted AND (TID IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . $parentTid . ') '.
                'OR parenttid IN(SELECT DISTINCT tid FROM taxaenumtree WHERE parenttid = ' . $parentTid . ')) ';
            if($rankId){
                $sql .= 'AND RankId = ' . $rankId . ' ';
            }
            $sql .= 'ORDER BY SciName '.
                'LIMIT ' . (($index - 1) * 50000) . ', 50000';
            //echo $sql;
            if($rs = $this->conn->query($sql)){
                while($r = $rs->fetch_object()){
                    $nodeArr = array();
                    $nodeArr['tid'] = $r->TID;
                    $nodeArr['sciname'] = $r->SciName;
                    $retArr[] = $nodeArr;
                }
                $rs->free();
            }
        }
        return $retArr;
    }

    public function getVideoCountsForTaxonomicGroup($tid, $index, $includeOcc = false): array
    {
        $retArr = array();
        $sql = 'SELECT t.TID, t.SciName, t.RankId, COUNT(m.mediaid) AS cnt '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'LEFT JOIN media AS m ON t.TID = m.tid '.
            'WHERE (te.parenttid = ' . $tid . ' OR t.TID = ' . $tid . ') AND t.TID = t.tidaccepted AND (m.format LIKE "video/%" OR ISNULL(m.format)) ';
        if(!$includeOcc){
            $sql .= 'AND ISNULL(m.occid) ';
        }
        $sql .= 'GROUP BY t.TID '.
            'ORDER BY t.RankId, t.SciName '.
            'LIMIT ' . (($index - 1) * 50000) . ', 50000';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $resultArr = array();
            $resultArr['tid'] = $row->TID;
            $resultArr['sciname'] = $row->SciName;
            $resultArr['rankid'] = $row->RankId;
            $resultArr['cnt'] = $row->cnt;
            $retArr[] = $resultArr;
        }
        $result->free();
        return $retArr;
    }

    public function setSynonymSearchData($searchData): array
    {
        foreach($searchData as $key => $tid){
            $targetTidArr = array();
            if($key){
                $sql = 'SELECT tid, tidaccepted FROM taxa WHERE sciname IN("' . $key . '") ';
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    if($r->tid && !in_array($r->tid, $targetTidArr, true)){
                        $targetTidArr[] = $r->tid;
                    }
                    if($r->tidaccepted && !in_array($r->tidaccepted, $targetTidArr, true)){
                        $targetTidArr[] = $r->tidaccepted;
                    }
                }
                $rs->free();
            }

            if($targetTidArr){
                $parentTidArr = array();
                $sql = 'SELECT DISTINCT tid, sciname, rankid FROM taxa '.
                    'WHERE tid IN(' . implode(',', $targetTidArr) . ') OR tidaccepted IN(' . implode(',', $targetTidArr) . ') ';
                $rs = $this->conn->query($sql);
                while($r = $rs->fetch_object()){
                    $searchData[$r->sciname] = $r->tid;
                    if((int)$r->rankid === 220){
                        $parentTidArr[] = $r->tid;
                    }
                }
                $rs->free();

                if($parentTidArr) {
                    $searchData = (new TaxonHierarchy)->setParentSearchDataByTidArr($searchData, $parentTidArr);
                }
            }
        }
        return $searchData;
    }

    public function setTaxaSearchDataTids($searchData): array
    {
        foreach($searchData as $name => $tid){
            $cleanName = SanitizerService::cleanInStr($this->conn, $name);
            $sql = 'SELECT DISTINCT TID, SciName FROM taxa '.
                "WHERE SciName = '" . $cleanName . "' OR SciName LIKE '" . $cleanName . " %' ";
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $searchData[$r->SciName] = $r->TID;
            }
        }
        return $searchData;
    }

    public function setUpdateFamiliesAccepted($parentTid): int
    {
        $retCnt = 0;
        if($parentTid){
            $sql1 = 'UPDATE taxa '.
                'SET family = SciName '.
                'WHERE RankId = 140 AND TID = tidaccepted AND (TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = '.$parentTid.') OR TID = '.$parentTid.') ';
            //echo $sql1;
            if($this->conn->query($sql1)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql3 = 'UPDATE taxa AS t LEFT JOIN taxaenumtree AS te ON t.TID = te.tid '.
                'LEFT JOIN taxa AS t2 ON te.parenttid = t2.TID '.
                'SET t.family = t2.SciName '.
                'WHERE t.RankId >= 140 AND t.TID = t.tidaccepted AND (t.TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = '.$parentTid.') OR t.TID = '.$parentTid.') AND (t2.RankId = 140 OR ISNULL(t2.RankId)) ';
            //echo $sql3;
            if($this->conn->query($sql3)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function setUpdateFamiliesUnaccepted($parentTid): int
    {
        $retCnt = 0;
        if($parentTid){
            $sql2 = 'UPDATE taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
                'SET t.family = t2.family '.
                'WHERE t.RankId = 140 AND t.TID <> t.tidaccepted AND (t.TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = '.$parentTid.') OR t.TID = '.$parentTid.') ';
            //echo $sql2;
            if($this->conn->query($sql2)){
                $retCnt += $this->conn->affected_rows;
            }

            $sql4 = 'UPDATE taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
                'LEFT JOIN taxaenumtree AS te ON t2.TID = te.tid '.
                'LEFT JOIN taxa AS t3 ON te.parenttid = t3.TID '.
                'SET t.family = t3.SciName '.
                'WHERE t.RankId >= 140 AND t.TID <> t.tidaccepted AND (t.TID IN(SELECT tid FROM taxaenumtree WHERE parenttid = '.$parentTid.') OR t.TID = '.$parentTid.') AND t3.RankId = 140 ';
            //echo $sql4;
            if($this->conn->query($sql4)){
                $retCnt += $this->conn->affected_rows;
            }
        }
        return $retCnt;
    }

    public function submitChangeToNotAccepted($tid, $tidAccepted, $kingdom = false): string
    {
        $status = '';
        if(is_numeric($tid)){
            $sql = 'SELECT parenttid, kingdomId FROM taxa WHERE TID = '.$tidAccepted.' ';
            //echo $sql."<br>";
            $rs = $this->conn->query($sql);
            if($r = $rs->fetch_object()){
                $parentTid = $r->parenttid;
                $kingdomId = $r->kingdomId;
                $sql2 = 'UPDATE taxa SET tidaccepted = '.$tidAccepted.', parenttid = '.$parentTid.', kingdomId = '.$kingdomId.' WHERE tid = '.$tid.' ';
                //echo $sql2;
                if($this->conn->query($sql2)) {
                    $sqlSyns = 'UPDATE taxa SET tidaccepted = '.$tidAccepted.', parenttid = '.$parentTid.', kingdomId = '.$kingdomId.' WHERE tidaccepted = '.$tid.' ';
                    if(!$this->conn->query($sqlSyns)){
                        $status = 'ERROR: unable to transfer linked synonyms to accepted taxon.';
                    }
                    $sqlParent = 'UPDATE taxa SET parenttid = '.$tidAccepted.', kingdomId = '.$kingdomId.' WHERE parenttid = '.$tid.' ';
                    if(!$this->conn->query($sqlParent)){
                        $status = 'ERROR: unable to transfer children taxa to accepted taxon.';
                    }
                    $sqlHierarchy = 'UPDATE taxaenumtree SET parenttid = '.$tidAccepted.' WHERE parenttid = '.$tid.' ';
                    if(!$this->conn->query($sqlHierarchy)){
                        $status = 'ERROR: unable to update taxonomic hierarchy with accepted taxon.';
                    }
                    if((int)$tid !== (int)$tidAccepted){
                        $sqlHierarchy = 'DELETE FROM taxaenumtree WHERE tid = '.$tid.' ';
                        if(!$this->conn->query($sqlHierarchy)){
                            $status = 'ERROR: unable to remove taxonomic hierarchy for unaccepted taxon.';
                        }
                    }
                    if($kingdom){
                        (new TaxonKingdoms)->updateKingdomAcceptance($tid,$tidAccepted);
                    }
                    $this->updateDependentData($tid,$tidAccepted);
                }
                else {
                    $status = 'ERROR: unable to switch acceptance.';
                }
            }
            $rs->free();
        }
        return $status;
    }

    public function taxonHasChildren($tid): bool
    {
        $retVal = false;
        $sql = 'SELECT TID FROM taxa WHERE parenttid = '.$tid.' LIMIT 1 ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($result->num_rows){
            $retVal = true;
        }
        return $retVal;
    }

    private function updateDependentData($tid, $tidNew): void
    {
        if(is_numeric($tid) && is_numeric($tidNew)){
            /*$this->conn->query('DELETE FROM kmdescr WHERE inherited IS NOT NULL AND tid = '.$tid.' ');
            $this->conn->query('UPDATE IGNORE kmdescr SET tid = '.$tidNew.' WHERE tid = '.$tid.' ');
            $this->conn->query('DELETE FROM kmdescr WHERE tid = '.$tid.' ');
            $this->resetCharStateInheritance($tidNew);*/

            $sqlVerns = 'DELETE v2.* '.
                'FROM taxavernaculars AS v1 LEFT JOIN taxavernaculars AS v2 ON v1.VernacularName = v2.VernacularName AND v1.langid = v2.langid '.
                'WHERE v1.TID = '.$tidNew.' AND v2.TID = '.$tid.' AND v2.VID IS NOT NULL ';
            $this->conn->query($sqlVerns);

            $sqlVerns = 'UPDATE taxavernaculars SET tid = '.$tidNew.' WHERE tid = '.$tid.' ';
            $this->conn->query($sqlVerns);
        }
    }

    public function updateTaxaRecord($tid, $editData): int
    {
        $retVal = 0;
        $sqlPartArr = array();
        if($tid && $editData){
            foreach($this->fields as $field => $fieldArr){
                if(array_key_exists($field, $editData)){
                    if($field === 'source'){
                        $fieldStr = '`' . $field . '`';
                    }
                    else{
                        $fieldStr = $field;
                    }
                    $sqlPartArr[] = $fieldStr . ' = ' . SanitizerService::getSqlValueString($this->conn, $editData[$field], $fieldArr['dataType']);
                }
            }
            if((!array_key_exists('sciname', $editData) || !$editData['sciname']) && array_key_exists('unitname1', $editData) && $editData['unitname1']){
                $sciNameConcat = ($editData['unitind1'] ? ($editData['unitind1'] . ' ') : '').
                    $editData['unitname1'] . ($editData['unitind2'] ? (' ' . $editData['unitind2']) : '').
                    ($editData['unitname2'] ? (' ' . $editData['unitname2']) : '').
                    ($editData['unitind3'] ? (' ' . $editData['unitind3']) : '').
                    ($editData['unitname3'] ? (' ' . $editData['unitname3']) : '');
                $sqlPartArr[] = 'sciname = ' . SanitizerService::getSqlValueString($this->conn, $sciNameConcat, 'string');
            }
            $sqlPartArr[] = 'modifieduid = ' . $GLOBALS['SYMB_UID'];
            $sqlPartArr[] = 'modifiedtimestamp = "' . date('Y-m-d H:i:s') . '"';
            $sql = 'UPDATE taxa SET ' . implode(', ', $sqlPartArr) . ' '.
                'WHERE tid = ' . (int)$tid . ' ';
            //echo "<div>".$sql."</div>";
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function updateTaxonIdentifier($tid, $idName, $id): int
    {
        $returnVal = 0;
        if($tid && $idName && $id){
            $identifierName = SanitizerService::cleanInStr($this->conn, $idName);
            $identifier = SanitizerService::cleanInStr($this->conn, $id);
            $sql = 'UPDATE taxaidentifiers SET identifier = "' . $identifier . '" WHERE tid = ' . $tid . ' AND `name` = "' . $identifierName . '" ';
            if($this->conn->query($sql)){
                $returnVal = 1;
            }
        }
        return $returnVal;
    }

    public function validateNewTaxaData($dataArr): array
    {
        $dataArr['kingdomid'] = 0;
        $dataArr['family'] = '';
        if(array_key_exists('rankid',$dataArr) && (int)$dataArr['rankid'] === 10 && SanitizerService::cleanInStr($this->conn, $dataArr['sciname'])){
            $dataArr['kingdomid'] = (new TaxonKingdoms)->createTaxonKingdomRecord($dataArr['sciname']);
        }
        elseif((array_key_exists('parenttid',$dataArr) && $dataArr['parenttid']) && (!array_key_exists('kingdomid',$dataArr) || !$dataArr['kingdomid'] || !array_key_exists('family',$dataArr) || !$dataArr['family'])){
            $sqlKg = 'SELECT kingdomId, family FROM taxa WHERE tid = '.(int)$dataArr['parenttid'].' ';
            //echo $sqlKg; exit;
            $rsKg = $this->conn->query($sqlKg);
            if($r = $rsKg->fetch_object()){
                $dataArr['kingdomid'] = $r->kingdomId;
                $dataArr['family'] = $r->family;
            }
            $rsKg->free();
            if(!$dataArr['family'] && (int)$dataArr['rankid'] === 140){
                $dataArr['family'] = $dataArr['sciname'];
            }
        }
        if(!array_key_exists('unitname1',$dataArr) || !$dataArr['unitname1']){
            $sciNameArr = (new TaxonomyService)->parseScientificName($dataArr['sciname'], $dataArr['rankid']);
            $dataArr['unitind1'] = array_key_exists('unitind1', $sciNameArr) ? $sciNameArr['unitind1'] : '';
            $dataArr['unitname1'] = array_key_exists('unitname1', $sciNameArr) ? $sciNameArr['unitname1'] : '';
            $dataArr['unitind2'] = array_key_exists('unitind2', $sciNameArr) ? $sciNameArr['unitind2'] : '';
            $dataArr['unitname2'] = array_key_exists('unitname2', $sciNameArr) ? $sciNameArr['unitname2'] : '';
            $dataArr['unitind3'] = array_key_exists('unitind3', $sciNameArr) ? $sciNameArr['unitind3'] : '';
            $dataArr['unitname3'] = array_key_exists('unitname3', $sciNameArr) ? $sciNameArr['unitname3'] : '';
        }
        if(!array_key_exists('source',$dataArr)){
            $dataArr['source'] = '';
        }
        if(!array_key_exists('notes',$dataArr)){
            $dataArr['notes'] = '';
        }
        if(!array_key_exists('securitystatus',$dataArr)){
            $dataArr['securitystatus'] = 0;
        }
        return $dataArr;
    }
}
