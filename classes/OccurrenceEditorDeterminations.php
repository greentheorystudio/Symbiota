<?php
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceEditorDeterminations extends OccurrenceEditorManager{

    public function __construct(){
        parent::__construct();
    }

    public function getDetMap($identBy, $dateIdent, $sciName): array
    {
        $retArr = array();
        $hasCurrent = 0;
        $sql = 'SELECT detid, identifiedBy, dateIdentified, sciname, verbatimscientificname, scientificNameAuthorship, ' .
            'identificationQualifier, iscurrent, identificationReferences, identificationRemarks, sortsequence ' .
            'FROM omoccurdeterminations ' .
            'WHERE (occid = ' .$this->occid. ') ORDER BY iscurrent DESC, sortsequence';
        //echo "<div>".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $detId = $row->detid;
            $retArr[$detId]['identifiedby'] = SanitizerService::cleanOutStr($row->identifiedBy);
            $retArr[$detId]['dateidentified'] = SanitizerService::cleanOutStr($row->dateIdentified);
            $retArr[$detId]['sciname'] = SanitizerService::cleanOutStr($row->sciname);
            $retArr[$detId]['verbatimscientificname'] = SanitizerService::cleanOutStr($row->verbatimscientificname);
            $retArr[$detId]['scientificnameauthorship'] = SanitizerService::cleanOutStr($row->scientificNameAuthorship);
            $retArr[$detId]['identificationqualifier'] = SanitizerService::cleanOutStr($row->identificationQualifier);
            if((int)$row->iscurrent === 1) {
                $hasCurrent = 1;
            }
            $retArr[$detId]['iscurrent'] = $row->iscurrent;
            $retArr[$detId]['identificationreferences'] = SanitizerService::cleanOutStr($row->identificationReferences);
            $retArr[$detId]['identificationremarks'] = SanitizerService::cleanOutStr($row->identificationRemarks);
            $retArr[$detId]['sortsequence'] = $row->sortsequence;
        }
        $result->free();
        if(!$hasCurrent){
            foreach($retArr as $detId => $detArr){
                if($detArr['identifiedby'] === $identBy && $detArr['dateidentified'] === $dateIdent && $detArr['sciname'] === $sciName){
                    $retArr[$detId]['iscurrent'] = 1;
                    break;
                }
            }
        }
        return $retArr;
    }

    public function addDetermination($detArr,$isEditor): string
    {
        $status = 'Determination submitted successfully!';
        if(!$this->occid) {
            return 'ERROR: occid is null';
        }
        $isCurrent = 0;
        if(!array_key_exists('makecurrent',$detArr)) {
            $detArr['makecurrent'] = 0;
        }
        if(!array_key_exists('printqueue',$detArr)) {
            $detArr['printqueue'] = 0;
        }
        if((int)$detArr['makecurrent'] === 1 && (int)$isEditor < 3){
            $isCurrent = 1;
        }
        if($isEditor === 3){
            $status = 'Determination has been added successfully, but is pending approval before being activated';
        }
        $sortSeq = 1;
        if(preg_match('/([1,2]\d{3})/',$detArr['dateidentified'],$matches)){
            $sortSeq = 2100-$matches[1];
        }
        if($isCurrent){
            $sqlSetCur1 = 'UPDATE omoccurdeterminations SET iscurrent = 0 WHERE occid = '.$this->occid;
            if(!$this->conn->query($sqlSetCur1)){
                $status = 'ERROR resetting dets to not current.';
            }
        }
        $sciname = SanitizerService::cleanInStr($this->conn,$detArr['sciname']);
        $notes = SanitizerService::cleanInStr($this->conn,$detArr['identificationremarks']);
        $sql = 'INSERT INTO omoccurdeterminations(occid, tid, identifiedBy, dateIdentified, sciname, scientificNameAuthorship, '.
            'identificationQualifier, iscurrent, printqueue, appliedStatus, identificationReferences, identificationRemarks, sortsequence) '.
            'VALUES ('.$this->occid.','.($detArr['tidtoadd']?(int)$detArr['tidtoadd']:'NULL').',"'.SanitizerService::cleanInStr($this->conn,$detArr['identifiedby']).'","'.SanitizerService::cleanInStr($this->conn,$detArr['dateidentified']).'","'.
            $sciname.'",'.($detArr['scientificnameauthorship']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['scientificnameauthorship']).'"':'NULL').','.
            ($detArr['identificationqualifier']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationqualifier']).'"':'NULL').','.
            $detArr['makecurrent'].','.$detArr['printqueue'].','.($isEditor === 3?0:1).','.
            ($detArr['identificationreferences']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationreferences']).'"':'NULL').','.
            ($notes?'"'.$notes.'"':'NULL').','.
            $sortSeq.')';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $guid = UuidService::getUuidV4();
            $detId = $this->conn->insert_id;
            if(!$this->conn->query('INSERT INTO guidoccurdeterminations(guid,detid) VALUES("'.$guid.'",'.$detId.')')){
                $status .= ' (Warning: GUID mapping #1 failed)';
            }
            if($isCurrent){
                $sqlInsert = 'INSERT IGNORE INTO omoccurdeterminations(occid, tid, identifiedBy, dateIdentified, sciname, verbatimScientificName, '.
                    'scientificNameAuthorship, identificationQualifier, identificationReferences, identificationRemarks, sortsequence) '.
                    'SELECT occid, tid, IFNULL(identifiedby,"unknown"), IFNULL(dateidentified,"unknown"), sciname, verbatimScientificName, '.
                    'scientificnameauthorship, identificationqualifier, identificationreferences, identificationremarks, 10 '.
                    'FROM omoccurrences WHERE (occid = '.$this->occid.')';
                //echo "<div>".$sqlInsert."</div>";
                if($this->conn->query($sqlInsert)){
                    $guid = UuidService::getUuidV4();
                    $detId = $this->conn->insert_id;
                    if(!$this->conn->query('INSERT IGNORE INTO guidoccurdeterminations(guid,detid) VALUES("'.$guid.'",'.$detId.')')){
                        $status .= ' (Warning: GUID mapping #2 failed)';
                    }
                }

                $tidToAdd = $detArr['tidtoadd'];
                if($tidToAdd && !is_numeric($tidToAdd)) {
                    $tidToAdd = 0;
                }

                $sStatus = 0;
                if($tidToAdd){
                    $sqlSs = 'SELECT securitystatus FROM taxa WHERE (tid = '.$tidToAdd.')';
                    $rsSs = $this->conn->query($sqlSs);
                    if(($rSs = $rsSs->fetch_object()) && (int)$rSs->securitystatus === 1) {
                        $sStatus = 1;
                    }
                    $rsSs->free();
                    if(!$sStatus){
                        $sql2 = 'SELECT c.clid '.
                            'FROM fmchecklists AS c INNER JOIN fmchklsttaxalink AS cl ON c.clid = cl.clid '.
                            'INNER JOIN taxa AS t ON cl.tid = t.tid '.
                            'INNER JOIN omoccurrences AS o ON c.locality = o.stateprovince '.
                            'WHERE c.type = "rarespp" '.
                            'AND t.tidaccepted = '.$tidToAdd.' AND o.occid = '.$this->occid.' ';
                        //echo $sql; exit;
                        $rsSs2 = $this->conn->query($sql2);
                        if($rsSs2->num_rows){
                            $sStatus = 1;
                        }
                        $rsSs2->free();
                    }
                }

                $sqlNewDet = 'UPDATE omoccurrences '.
                    'SET identifiedBy = "'.SanitizerService::cleanInStr($this->conn,$detArr['identifiedby']).'", dateIdentified = "'.SanitizerService::cleanInStr($this->conn,$detArr['dateidentified']).'",'.
                    'family = '.($detArr['family']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['family']).'"':'NULL').','.
                    'sciname = "'.$sciname.'",genus = NULL, specificEpithet = NULL, taxonRank = NULL, infraspecificepithet = NULL,'.
                    'scientificNameAuthorship = '.($detArr['scientificnameauthorship']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['scientificnameauthorship']).'"':'NULL').','.
                    'identificationQualifier = '.($detArr['identificationqualifier']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationqualifier']).'"':'NULL').','.
                    'identificationReferences = '.($detArr['identificationreferences']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationreferences']).'"':'NULL').','.
                    'identificationRemarks = '.($detArr['identificationremarks']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationremarks']).'"':'NULL').', '.
                    'tid = '.($tidToAdd?:'NULL').', localitysecurity = '.$sStatus.
                    ' WHERE (occid = '.$this->occid.')';
                //echo "<div>".$sqlNewDet."</div>";
                $this->conn->query($sqlNewDet);
                $sql = 'UPDATE images SET tid = '.($tidToAdd?:'NULL').' WHERE (occid = '.$this->occid.')';
                //echo $sql;
                if(!$this->conn->query($sql)){
                    $status = 'ERROR: Annotation added but failed to remap images to new name.';
                }
            }
        }
        else{
            $status = 'ERROR - failed to add determination.';
        }
        return $status;
    }

    public function editDetermination($detArr): string
    {
        if(!array_key_exists('printqueue',$detArr)) {
            $detArr['printqueue'] = 0;
        }
        $status = 'Determination editted successfully!';
        $sql = 'UPDATE omoccurdeterminations '.
            'SET identifiedBy = "'.SanitizerService::cleanInStr($this->conn,$detArr['identifiedby']).'", '.
            'dateIdentified = "'.SanitizerService::cleanInStr($this->conn,$detArr['dateidentified']).'", '.
            'sciname = "'.SanitizerService::cleanInStr($this->conn,$detArr['sciname']).'", '.
            'scientificNameAuthorship = '.($detArr['scientificnameauthorship']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['scientificnameauthorship']).'"':'NULL').','.
            'identificationQualifier = '.($detArr['identificationqualifier']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationqualifier']).'"':'NULL').','.
            'identificationReferences = '.($detArr['identificationreferences']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationreferences']).'"':'NULL').','.
            'identificationRemarks = '.($detArr['identificationremarks']?'"'.SanitizerService::cleanInStr($this->conn,$detArr['identificationremarks']).'"':'NULL').','.
            'sortsequence = '.($detArr['sortsequence']?:'10').','.
            'printqueue = '.($detArr['printqueue']?:'NULL').' '.
            'WHERE (detid = '.$detArr['detid'].')';
        if(!$this->conn->query($sql)){
            $status = 'ERROR - failed to edit determination.';
        }
        return $status;
    }

    public function deleteDetermination($detId): string
    {
        $status = 'Determination deleted successfully!';
        $isCurrent = 0;
        $occid = 0;

        $sql = 'SELECT * FROM omoccurdeterminations WHERE detid = '.$detId;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_assoc()){
            $detArr = array();
            $isCurrent = $r['iscurrent'];
            $occid = $r['occid'];
            foreach($r as $k => $v){
                if($v) {
                    $detArr[$k] = $this->encodeStr($v);
                }
            }
            $detObj = json_encode($detArr);
            $sqlArchive = 'UPDATE guidoccurdeterminations '.
                'SET archivestatus = 1, archiveobj = "'.SanitizerService::cleanInStr($this->conn,$detObj).'" '.
                'WHERE (detid = '.$detId.')';
            $this->conn->query($sqlArchive);
        }

        if($isCurrent){
            $prevDetId = 0;
            $sql2 = 'SELECT detid FROM omoccurdeterminations WHERE occid = '.$occid.' AND detid <> '.$detId.' '.
                'ORDER BY detid DESC LIMIT 1 ';
            $rs = $this->conn->query($sql2);
            if($r = $rs->fetch_object()){
                $prevDetId = $r->detid;
            }
            if($prevDetId){
                $this->applyDetermination($prevDetId, 1);
            }
        }

        $sql = 'DELETE FROM omoccurdeterminations WHERE (detid = '.$detId.')';
        if(!$this->conn->query($sql)){
            $status = 'ERROR - failed to delete determination.';
        }

        return $status;
    }

    public function applyDetermination($detId, $makeCurrent): string
    {
        $statusStr = 'Determiantion has been applied';
        $iqStr = '';
        $sqlcr = 'SELECT identificationremarks FROM omoccurdeterminations WHERE detid = '.$detId;
        $rscr = $this->conn->query($sqlcr);
        if($rcr = $rscr->fetch_object()){
            $iqStr = $rcr->identificationremarks;
            if(preg_match('/ConfidenceRanking: (\d{1,2})/',$iqStr,$m)){
                $iqStr = trim(str_replace('ConfidenceRanking: '.$m[1],'',$iqStr),' ;');
            }
        }
        $rscr->free();

        $sql = 'UPDATE omoccurdeterminations '.
            'SET iscurrent = '.$makeCurrent.', '.
            'identificationremarks = '.($iqStr?'"'.SanitizerService::cleanInStr($this->conn,$iqStr).'"':'NULL').' WHERE detid = '.$detId;
        if(!$this->conn->query($sql)){
            $statusStr = 'ERROR attempting to apply dertermiantion.';
        }
        if($makeCurrent){
            $this->makeDeterminationCurrent($detId);
        }
        return $statusStr;
    }

    public function makeDeterminationCurrent($detId): void
    {
        $sqlInsert = 'INSERT IGNORE INTO omoccurdeterminations(occid, identifiedBy, dateIdentified, sciname, verbatimScientificName, scientificNameAuthorship, '.
            'identificationQualifier, identificationReferences, identificationRemarks, sortsequence) '.
            'SELECT occid, IFNULL(identifiedby,"unknown"), IFNULL(dateidentified,"unknown"), sciname, verbatimScientificName, scientificnameauthorship, '.
            'identificationqualifier, identificationreferences, identificationremarks, 10 '.
            'FROM omoccurrences WHERE (occid = '.$this->occid.')';
        if($this->conn->query($sqlInsert)){
            $guid = UuidService::getUuidV4();
            $this->conn->query('INSERT IGNORE INTO guidoccurdeterminations(guid,detid) VALUES("'.$guid.'",'.$this->conn->insert_id.')');
        }
        $tid = 0;
        $sStatus = 0;
        $family = '';
        $sqlTid = 'SELECT t.tid, t.securitystatus, t.family '.
            'FROM omoccurdeterminations AS d INNER JOIN taxa AS t ON d.sciname = t.sciname '.
            'WHERE d.detid = '.$detId.' ';
        $rs = $this->conn->query($sqlTid);
        if($r = $rs->fetch_object()){
            $tid = $r->tid;
            $family = $r->family;
            if((int)$r->securitystatus === 1) {
                $sStatus = 1;
            }
        }
        $rs->free();
        if(!$sStatus && $tid){
            $sql2 = 'SELECT c.clid '.
                'FROM fmchecklists AS c INNER JOIN fmchklsttaxalink AS cl ON c.clid = cl.clid '.
                'INNER JOIN taxa AS t ON cl.tid = t.tid '.
                'INNER JOIN omoccurrences AS o ON c.locality = o.stateprovince '.
                'WHERE c.type = "rarespp" '.
                'AND t.tidaccepted = '.$tid.' AND o.occid = '.$this->occid.' ';
            //echo $sql; exit;
            $rsSs2 = $this->conn->query($sql2);
            if($rsSs2->num_rows){
                $sStatus = 1;
            }
            $rsSs2->free();
        }

        $sqlNewDet = 'UPDATE omoccurrences AS o INNER JOIN omoccurdeterminations AS d ON o.occid = d.occid '.
            'SET o.identifiedBy = d.identifiedBy, o.dateIdentified = d.dateIdentified,o.family = '.($family?'"'.$family.'"':'NULL').','.
            'o.sciname = d.sciname,o.verbatimscientificname = d.verbatimscientificname,o.genus = NULL,o.specificEpithet = NULL,o.taxonRank = NULL,o.infraspecificepithet = NULL,'.
            'o.scientificNameAuthorship = d.scientificnameauthorship,o.identificationQualifier = d.identificationqualifier,'.
            'o.identificationReferences = d.identificationreferences,o.identificationRemarks = d.identificationremarks,'.
            'o.tid = '.($tid?:'NULL').', o.localitysecurity = '.$sStatus.
            ' WHERE (detid = '.$detId.')';
        //echo "<div>".$sqlNewDet."</div>";
        $this->conn->query($sqlNewDet);
        $sqlSetCur1 = 'UPDATE omoccurdeterminations SET iscurrent = 0 WHERE occid = '.$this->occid;
        $this->conn->query($sqlSetCur1);
        $sqlSetCur2 = 'UPDATE omoccurdeterminations SET iscurrent = 1 WHERE detid = '.$detId;
        $this->conn->query($sqlSetCur2);

        if($tid){
            $sql = 'UPDATE images SET tid = '.$tid.' WHERE (occid = '.$this->occid.')';
            $this->conn->query($sql);
        }
    }

    public function addNomAdjustment($detArr,$isEditor): void
    {
        $sql = 'SELECT identificationQualifier '.
            'FROM omoccurrences '.
            'WHERE occid = '.$this->occid;
        //echo "<div>".$sql."</div>";
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $detArr['identificationqualifier'] = $r->identificationQualifier;
        }
        $rs->free();
        $detArr['identifiedby'] = 'Nomenclatural Adjustment';
        $detArr['dateidentified'] = date('F').' '.date('j').', '.date('Y');
        $this->addDetermination($detArr,$isEditor);
    }

    public function getBulkDetRows($collid,$catNum,$sciName,$occStr): string
    {
        $retHtml = '';
        $sql = 'SELECT occid, catalogNumber, sciname, CONCAT_WS(" ",recordedby,IFNULL(recordnumber,eventdate)) AS collector, '.
            'CONCAT_WS(", ",country,stateprovince,county,locality) AS locality '.
            'FROM omoccurrences '.
            'WHERE collid = '.$collid.' ';
        if($catNum){
            $sql .= 'AND catalogNumber = "'.$catNum.'" ';
        }
        elseif($sciName){
            $sql .= 'AND sciname = "'.$sciName.'" ';
        }
        elseif($occStr){
            $sql .= 'AND occid IN('.$occStr.') ';
        }
        $sql .= 'LIMIT 400 ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $loc = $r->locality;
            if(strlen($loc) > 500) {
                $loc = substr($loc, 400);
            }
            $retHtml .= '<tr>';
            $retHtml .= '<td><input type="checkbox" name="occid[]" value="'.$r->occid.'" checked /></td>';
            $retHtml .= '<td>';
            $retHtml .= '<a href="#" onclick="openIndPopup('.$r->occid.'); return false;">'.($r->catalogNumber?:'[no catalog number]').'</a>';
            $retHtml .= '<a href="#" onclick="openEditorPopup('.$r->occid.'); return false;"><i style="height:15px;width:15px;" class="far fa-edit"></i></a>';
            $retHtml .= '</td>';
            $retHtml .= '<td>'.$r->sciname.'</td>';
            $retHtml .= '<td>'.$r->collector.'; '.$loc.'</td>';
            $retHtml .= '</tr>';
        }
        $rs->free();
        return $retHtml;
    }

    public function getCatNumArr($occStr): array
    {
        $retArr = array();
        $sql = 'SELECT catalogNumber '.
            'FROM omoccurrences '.
            'WHERE occid IN('.$occStr.') ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[] = $r->catalogNumber;
        }
        $rs->free();
        return $retArr;
    }

    public function getCollName(): string
    {
        $code = '';
        if($this->collMap['institutioncode']){
            $code .= $this->collMap['institutioncode'];
        }
        if($this->collMap['collectioncode']){
            $code .= ($code?':':'') . $this->collMap['collectioncode'];
        }
        return $this->collMap['collectionname'].($code?' ('.$code.')':'');
    }

}
