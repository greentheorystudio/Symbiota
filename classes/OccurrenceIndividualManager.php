<?php
include_once(__DIR__ . '/Manager.php');
include_once(__DIR__ . '/OccurrenceDuplicate.php');
include_once(__DIR__ . '/OccurrenceAccessStats.php');
include_once(__DIR__ . '/Mailer.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceIndividualManager extends Manager{

    private $occid;
    private $collid;
    private $dbpk;
    private $occArr = array();
    private $metadataArr = array();
    private $displayFormat = 'html';

    public function __construct() {
        parent::__construct();
    }

    private function loadMetadata(): void
    {
        if($this->collid){
            $sql = 'SELECT institutioncode, collectioncode, collectionname, homepage, individualurl, contact, email, icon, '.
                'publicedits, rights, rightsholder, accessrights, guidtarget '.
                'FROM omcollections WHERE collid = '.$this->collid;
            $rs = $this->conn->query($sql);
            if($rs){
                $this->metadataArr = $rs->fetch_assoc();
                $rs->free();
            }
        }
    }

    public function getMetadata(): array
    {
        return $this->metadataArr;
    }

    public function setGuid($guid){
        $guid = Sanitizer::cleanInStr($guid);
        if(!$this->occid){
            $sql = 'SELECT occid FROM guidoccurrences WHERE guid = "'.$guid.'"';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $this->occid = $r->occid;
            }
            $rs->free();
        }
        if(!$this->occid){
            $sql = 'SELECT occid FROM omoccurrences WHERE occurrenceid = "'.$guid.'"';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $this->occid = $r->occid;
            }
            $rs->free();
        }
        if(!$this->occid){
            $sql = 'SELECT i.occid '.
                'FROM guidimages g INNER JOIN images i ON g.imgid = i.imgid '.
                'WHERE g.guid = "'.$guid.'" AND i.occid IS NOT NULL ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $this->occid = $r->occid;
            }
            $rs->free();
        }
        if(!$this->occid){
            $sql = 'SELECT d.occid '.
                'FROM guidoccurdeterminations g INNER JOIN omoccurdeterminations d ON g.detid = d.detid '.
                'WHERE g.guid = "'.$guid.'" ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $this->occid = $r->occid;
            }
            $rs->free();
        }
        return $this->occid;
    }

    public function getOccData($fieldKey = null): ?array
    {
        if($this->occid){
            if(!$this->occArr) {
                $this->loadOccurData();
            }
            if($fieldKey){
                return $this->occArr[$fieldKey] ?? null;
            }
        }
        return $this->occArr;
    }

    private function loadOccurData(): void
    {
        $sql = 'SELECT o.occid, collid, o.institutioncode AS secondaryinstcode, o.collectioncode AS secondarycollcode, '.
            'o.occurrenceid, o.catalognumber, o.occurrenceremarks, o.tidinterpreted, o.family, o.sciname, '.
            'o.scientificnameauthorship, o.identificationqualifier, o.identificationremarks, o.identificationreferences, o.taxonremarks, '.
            'o.identifiedby, o.dateidentified, o.recordedby, o.associatedcollectors, o.recordnumber, o.eventdate, MAKEDATE(YEAR(o.eventDate),o.enddayofyear) AS eventdateend, '.
            'o.verbatimeventdate, o.country, o.stateprovince, o.county, o.municipality, o.locality, o.fieldnotes, '.
            'o.minimumelevationinmeters, o.maximumelevationinmeters, o.verbatimelevation, o.localitysecurity, o.localitysecurityreason, '.
            'o.decimallatitude, o.decimallongitude, o.geodeticdatum, o.coordinateuncertaintyinmeters, o.verbatimcoordinates, o.footprintwkt, '.
            'o.georeferenceremarks, o.verbatimattributes, o.locationremarks, o.lifestage, o.sex, o.individualcount, o.samplingprotocol, o.preparations, '.
            'o.typestatus, o.dbpk, o.habitat, o.substrate, o.associatedtaxa, o.reproductivecondition, o.cultivationstatus, o.establishmentmeans, '.
            'o.ownerinstitutioncode, o.othercatalognumbers, o.disposition, o.modified, o.observeruid, g.guid, o.recordenteredby, o.dateentered, o.datelastmodified ';
        $sql .= 'FROM omoccurrences AS o LEFT JOIN guidoccurrences AS g ON o.occid = g.occid ';
        if($this->occid){
            $sql .= 'WHERE (o.occid = '.$this->occid.')';
        }
        elseif($this->collid && $this->dbpk){
            $sql .= 'WHERE (o.collid = '.$this->collid.') AND (o.dbpk = "'.$this->dbpk.'")';
        }
        if($result = $this->conn->query($sql)){
            if($this->occArr = $result->fetch_assoc()){
                if(!$this->occid) {
                    $this->occid = $this->occArr['occid'];
                }
                if(!$this->collid) {
                    $this->collid = $this->occArr['collid'];
                }
                $this->loadMetadata();

                if(!$this->occArr['occurrenceid']){
                    if($this->metadataArr['guidtarget'] === 'catalogNumber'){
                        $this->occArr['occurrenceid'] = $this->occArr['catalognumber'];
                    }
                    elseif($this->metadataArr['guidtarget'] === 'symbiotaUUID'){
                        $this->occArr['occurrenceid'] = $this->occArr['guid'];
                    }
                }

                $this->loadImages();
                $this->loadMedia();
                $this->loadDeterminations();
                $this->loadLoan();
                $this->loadExsiccati();
                $result->free();
            }
            $accessType = 'view';
            if(in_array($this->displayFormat,array('json','xml','rdf','turtle'))) {
                $accessType = 'api' . strtoupper($this->displayFormat);
            }
            $statsManager = new OccurrenceAccessStats();
            $statsManager->recordAccessEvent($this->occid, $accessType);
        }
    }

    private function loadImages(): void
    {
        $sql = 'SELECT imgid, url, thumbnailurl, originalurl, photographer, photographeruid, caption, owner, sourceurl, referenceUrl, copyright, '.
            'rights, accessrights, locality, notes, anatomy, dynamicProperties '.
            'FROM images '.
            'WHERE (occid = '.$this->occid.') ORDER BY sortsequence';
        $result = $this->conn->query($sql);
        if($result){
            while($row = $result->fetch_object()){
                $imgId = $row->imgid;
                $url = $row->url;
                $tnUrl = $row->thumbnailurl;
                $lgUrl = $row->originalurl;
                if($GLOBALS['IMAGE_DOMAIN']){
                    if(strncmp($url, '/', 1) === 0) {
                        $url = $GLOBALS['IMAGE_DOMAIN'] . $url;
                    }
                    if($lgUrl && strncmp($lgUrl, '/', 1) === 0) {
                        $lgUrl = $GLOBALS['IMAGE_DOMAIN'] . $lgUrl;
                    }
                    if($tnUrl && strncmp($tnUrl, '/', 1) === 0) {
                        $tnUrl = $GLOBALS['IMAGE_DOMAIN'] . $tnUrl;
                    }
                }
                if((!$url || $url === 'empty') && $lgUrl) {
                    $url = $lgUrl;
                }
                $this->occArr['imgs'][$imgId]['url'] = $url;
                $this->occArr['imgs'][$imgId]['tnurl'] = $tnUrl;
                $this->occArr['imgs'][$imgId]['lgurl'] = $lgUrl;
                $this->occArr['imgs'][$imgId]['photographer'] = $row->photographer;
                $this->occArr['imgs'][$imgId]['photographeruid'] = $row->photographeruid;
                $this->occArr['imgs'][$imgId]['caption'] = $row->caption;
                $this->occArr['imgs'][$imgId]['owner'] = $row->owner;
                $this->occArr['imgs'][$imgId]['sourceurl'] = $row->sourceurl;
                $this->occArr['imgs'][$imgId]['referenceUrl'] = $row->referenceUrl;
                $this->occArr['imgs'][$imgId]['copyright'] = $row->copyright;
                $this->occArr['imgs'][$imgId]['rights'] = $row->rights;
                $this->occArr['imgs'][$imgId]['accessrights'] = $row->accessrights;
                $this->occArr['imgs'][$imgId]['locality'] = $row->locality;
                $this->occArr['imgs'][$imgId]['notes'] = $row->notes;
                $this->occArr['imgs'][$imgId]['anatomy'] = $row->anatomy;
                $this->occArr['imgs'][$imgId]['dynamicProperties'] = $row->dynamicProperties;
            }
            $result->free();
        }
    }

    private function loadMedia(): void
    {
        $sql = 'SELECT mediaid, accessuri, title, creatoruid, creator, format, owner, furtherinformationurl, language, usageterms, '.
            'rights, bibliographiccitation, publisher, contributor, locationcreated, description '.
            'FROM media '.
            'WHERE (occid = '.$this->occid.') ORDER BY sortsequence';
        $result = $this->conn->query($sql);
        if($result){
            while($row = $result->fetch_object()){
                $medId = $row->mediaid;
                $url = $row->accessuri;
                if($GLOBALS['IMAGE_DOMAIN'] && strncmp($url, '/', 1) === 0) {
                    $url = $GLOBALS['IMAGE_DOMAIN'] . $url;
                }
                $this->occArr['media'][$medId]['accessuri'] = $url;
                $this->occArr['media'][$medId]['title'] = $row->title;
                $this->occArr['media'][$medId]['creatoruid'] = $row->creatoruid;
                $this->occArr['media'][$medId]['creator'] = $row->creator;
                $this->occArr['media'][$medId]['format'] = $row->format;
                $this->occArr['media'][$medId]['owner'] = $row->owner;
                $this->occArr['media'][$medId]['furtherinformationurl'] = $row->furtherinformationurl;
                $this->occArr['media'][$medId]['language'] = $row->language;
                $this->occArr['media'][$medId]['usageterms'] = $row->usageterms;
                $this->occArr['media'][$medId]['rights'] = $row->rights;
                $this->occArr['media'][$medId]['bibliographiccitation'] = $row->bibliographiccitation;
                $this->occArr['media'][$medId]['publisher'] = $row->publisher;
                $this->occArr['media'][$medId]['contributor'] = $row->contributor;
                $this->occArr['media'][$medId]['locationcreated'] = $row->locationcreated;
                $this->occArr['media'][$medId]['description'] = $row->description;
            }
            $result->free();
        }
    }

    public function getPhotographerArr(): array
    {
        $retArr = array();
        $sql = "SELECT u.uid, CONCAT_WS(', ',u.lastname,u.firstname) AS fullname ".
            'FROM users u ORDER BY u.lastname, u.firstname ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $retArr[$row->uid] = Sanitizer::cleanOutStr($row->fullname);
        }
        $result->close();
        return $retArr;
    }

    private function loadDeterminations(): void
    {
        $sql = 'SELECT detid, dateidentified, identifiedby, sciname, scientificnameauthorship, identificationqualifier, '.
            'identificationreferences, identificationremarks '.
            'FROM omoccurdeterminations '.
            'WHERE (occid = '.$this->occid.') AND appliedstatus = 1 '.
            'ORDER BY sortsequence';
        $result = $this->conn->query($sql);
        if($result){
            while($row = $result->fetch_object()){
                $detId = $row->detid;
                $this->occArr['dets'][$detId]['date'] = $row->dateidentified;
                $this->occArr['dets'][$detId]['identifiedby'] = $row->identifiedby;
                $this->occArr['dets'][$detId]['sciname'] = $row->sciname;
                $this->occArr['dets'][$detId]['author'] = $row->scientificnameauthorship;
                $this->occArr['dets'][$detId]['qualifier'] = $row->identificationqualifier;
                $this->occArr['dets'][$detId]['ref'] = $row->identificationreferences;
                $this->occArr['dets'][$detId]['notes'] = $row->identificationremarks;
            }
            $result->free();
        }
    }

    private function loadLoan(): void
    {
        $sql = 'SELECT l.loanIdentifierOwn, i.institutioncode '.
            'FROM omoccurloanslink llink INNER JOIN omoccurloans l ON llink.loanid = l.loanid '.
            'INNER JOIN institutions i ON l.iidBorrower = i.iid '.
            'WHERE (llink.occid = '.$this->occid.') AND (l.dateclosed IS NULL) AND (llink.returndate IS NULL)';
        $result = $this->conn->query($sql);
        if($result){
            while($row = $result->fetch_object()){
                $this->occArr['loan']['identifier'] = $row->loanIdentifierOwn;
                $this->occArr['loan']['code'] = $row->institutioncode;
            }
            $result->free();
        }
    }

    private function loadExsiccati(): void
    {
        $sql = 'SELECT t.title, t.editor, n.omenid, n.exsnumber '.
            'FROM omexsiccatititles t INNER JOIN omexsiccatinumbers n ON t.ometid = n.ometid '.
            'INNER JOIN omexsiccatiocclink l ON n.omenid = l.omenid '.
            'WHERE (l.occid = '.$this->occid.')';
        $rs = $this->conn->query($sql);
        if($rs){
            while($r = $rs->fetch_object()){
                $this->occArr['exs']['title'] = $r->title;
                $this->occArr['exs']['omenid'] = $r->omenid;
                $this->occArr['exs']['exsnumber'] = $r->exsnumber;
            }
            $rs->free();
        }
    }

    public function getDuplicateArr(){
        $dupManager = new OccurrenceDuplicate();
        $retArr = $dupManager->getClusterArr($this->occid);
        if($retArr){
            unset($retArr[$this->occid]);
        }
        return $retArr;
    }

    public function getCommentArr($isEditor): array
    {
        $retArr = array();
        $sql = 'SELECT c.comid, c.comment, u.username, c.reviewstatus, c.initialtimestamp '.
            'FROM omoccurcomments c INNER JOIN users u ON c.uid = u.uid '.
            'WHERE (c.occid = '.$this->occid.') ';
        if(!$isEditor) {
            $sql .= 'AND c.reviewstatus IN(1,3) ';
        }
        $sql .= 'ORDER BY c.initialtimestamp';
        //echo $sql.'<br/><br/>';
        $result = $this->conn->query($sql);
        if($result){
            while($row = $result->fetch_object()){
                $comId = $row->comid;
                $retArr[$comId]['comment'] = $row->comment;
                $retArr[$comId]['reviewstatus'] = $row->reviewstatus;
                $retArr[$comId]['username'] = $row->username;
                $retArr[$comId]['initialtimestamp'] = $row->initialtimestamp;
            }
            $result->free();
        }
        return $retArr;
    }

    public function addComment($commentStr): bool
    {
        $status = false;
        if($GLOBALS['SYMB_UID']){
            $sql = 'INSERT INTO omoccurcomments(occid,comment,uid,reviewstatus) '.
                'VALUES('.$this->occid.',"'.Sanitizer::cleanInStr($commentStr).'",'.$GLOBALS['SYMB_UID'].',1)';
            //echo 'sql: '.$sql;
            if($this->conn->query($sql)){
                $status = true;
            }
            else{
                $status = false;
                $this->errorMessage = 'ERROR adding comment.';
            }
            $this->conn->close();
        }
        return $status;
    }

    public function deleteComment($comId): bool
    {
        $status = true;
        if(is_numeric($comId)){
            $sql = 'DELETE FROM omoccurcomments WHERE comid = '.$comId;
            if(!$this->conn->query($sql)){
                $status = false;
                $this->errorMessage = 'ERROR deleting comment.';
            }
        }
        $this->conn->close();
        return $status;
    }

    public function reportComment($repComId): bool
    {
        if($GLOBALS['EMAIL_CONFIGURED'] && isset($GLOBALS['ADMIN_EMAIL']) && $GLOBALS['ADMIN_EMAIL']){
            $status = true;
            if(!is_numeric($repComId)) {
                return false;
            }
            if(!$this->conn->query('UPDATE omoccurcomments SET reviewstatus = 2 WHERE comid = '.$repComId)){
                $this->errorMessage = 'ERROR changing comment status to needing review.';
                $status = false;
            }
            $this->conn->close();

            $emailAddr = $GLOBALS['ADMIN_EMAIL'];
            $comUrl = 'http://'.$_SERVER['HTTP_HOST'].$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$this->occid.'#commenttab';
            $subject = $GLOBALS['DEFAULT_TITLE'].' inappropriate comment reported<br/>';
            $bodyStr = 'The following comment has been reported as inappropriate:<br/> '.
                '<a href="'.$comUrl.'">'.$comUrl.'</a>';
            $mailerResult = (new Mailer)->sendEmail($emailAddr,$subject,$bodyStr);
            if($mailerResult !== 'Sent'){
                $this->errorMessage = 'ERROR sending email to portal manager, error unknown';
                $status = false;
            }
        }
        else{
            $this->errorMessage = 'Email has not been configured on this portal. ';
            if(isset($GLOBALS['ADMIN_EMAIL']) && $GLOBALS['ADMIN_EMAIL']){
                $this->errorMessage .= 'Please contact portal administrator at: ' . $GLOBALS['ADMIN_EMAIL'];
            }
            $status = false;
        }
        return $status;
    }

    public function makeCommentPublic($comId): bool
    {
        $status = true;
        if(!is_numeric($comId)) {
            return false;
        }
        if(!$this->conn->query('UPDATE omoccurcomments SET reviewstatus = 1 WHERE comid = '.$comId)){
            $this->errorMessage = 'ERROR making comment public.';
            $status = false;
        }
        $this->conn->close();
        return $status;
    }

    public function getGeneticArr(): array
    {
        $retArr = array();
        if($this->occid){
            $sql = 'SELECT idoccurgenetic, identifier, resourcename, locus, resourceurl, notes '.
                'FROM omoccurgenetic '.
                'WHERE occid = '.$this->occid;
            $result = $this->conn->query($sql);
            if($result){
                while($r = $result->fetch_object()){
                    $retArr[$r->idoccurgenetic]['id'] = $r->identifier;
                    $retArr[$r->idoccurgenetic]['name'] = $r->resourcename;
                    $retArr[$r->idoccurgenetic]['locus'] = $r->locus;
                    $retArr[$r->idoccurgenetic]['resourceurl'] = $r->resourceurl;
                    $retArr[$r->idoccurgenetic]['notes'] = $r->notes;
                }
                $result->free();
            }
        }
        return $retArr;
    }

    public function getEditArr(): array
    {
        $retArr = array();
        $sql = 'SELECT e.ocedid, e.fieldname, e.fieldvalueold, e.fieldvaluenew, e.reviewstatus, e.appliedstatus, '.
            'CONCAT_WS(", ",u.lastname,u.firstname) as editor, e.initialtimestamp '.
            'FROM omoccuredits e INNER JOIN users u ON e.uid = u.uid '.
            'WHERE e.occid = '.$this->occid.' ORDER BY e.initialtimestamp DESC ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($result){
            while($r = $result->fetch_object()){
                $k = substr($r->initialtimestamp,0,16);
                if($k){
                    if(!isset($retArr[$k]['editor'])){
                        $retArr[$k]['editor'] = $r->editor;
                        $retArr[$k]['ts'] = $r->initialtimestamp;
                        $retArr[$k]['reviewstatus'] = $r->reviewstatus;
                        $retArr[$k]['appliedstatus'] = $r->appliedstatus;
                    }
                    $retArr[$k]['edits'][$r->ocedid]['fieldname'] = $r->fieldname;
                    $retArr[$k]['edits'][$r->ocedid]['old'] = $r->fieldvalueold;
                    $retArr[$k]['edits'][$r->ocedid]['new'] = $r->fieldvaluenew;
                }
            }
            $result->free();
        }
        return $retArr;
    }

    public function getExternalEditArr(): array
    {
        $retArr = array();
        $sql = 'SELECT r.orid, r.oldvalues, r.newvalues, r.externalsource, r.externaleditor, r.reviewstatus, r.appliedstatus, '.
            'CONCAT_WS(", ",u.lastname,u.firstname) AS username, r.externaltimestamp, r.initialtimestamp '.
            'FROM omoccurrevisions r LEFT JOIN users u ON r.uid = u.uid '.
            'WHERE (r.occid = '.$this->occid.') ORDER BY r.initialtimestamp DESC ';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $editor = $r->externaleditor;
            if($r->username) {
                $editor .= ' (' . $r->username . ')';
            }
            $retArr[$r->orid][$r->appliedstatus]['editor'] = $editor;
            $retArr[$r->orid][$r->appliedstatus]['source'] = $r->externalsource;
            $retArr[$r->orid][$r->appliedstatus]['reviewstatus'] = $r->reviewstatus;
            $retArr[$r->orid][$r->appliedstatus]['ts'] = $r->initialtimestamp;

            $oldValues = json_decode($r->oldvalues, true);
            $newValues = json_decode($r->newvalues, true);
            foreach($oldValues as $fieldName => $value){
                $retArr[$r->orid][$r->appliedstatus]['edits'][$fieldName]['old'] = $value;
                $retArr[$r->orid][$r->appliedstatus]['edits'][$fieldName]['new'] = ($newValues[$fieldName] ?? 'ERROR');
            }
        }
        $rs->free();
        return $retArr;
    }

    public function getAccessStats(): array
    {
        $retArr = array();
        $sql = 'SELECT year(accessdate) as accessdate, accesstype, count(*) AS cnt '.
            'FROM omoccuraccessstats '.
            'WHERE (occid = '.$this->occid.') '.
            'GROUP BY accessdate, accesstype';
        //echo '<div>'.$sql.'</div>';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[$r->accessdate][$r->accesstype] = $r->cnt;
        }
        $rs->free();
        return $retArr;
    }

    public function getVoucherChecklists(): array
    {
        $returnArr = array();
        $sql = 'SELECT c.name, c.clid, c.access, v.notes '.
            'FROM fmchecklists c INNER JOIN fmvouchers v ON c.clid = v.clid '.
            'WHERE v.occid = '.$this->occid.' ';
        if(array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])){
            $sql .= 'AND (c.access = "public" OR c.clid IN('.implode(',',$GLOBALS['USER_RIGHTS']['ClAdmin']).')) ';
        }
        else{
            $sql .= 'AND (c.access = "public") ';
        }
        $sql .= 'ORDER BY c.name';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($result){
            while($row = $result->fetch_object()){
                $nameStr = $row->name;
                if($row->access === 'private') {
                    $nameStr .= ' (private status)';
                }
                $returnArr[$row->clid] = $nameStr;
            }
            $result->free();
        }
        return $returnArr;
    }

    public function linkVoucher($postArr): bool
    {
        $status = true;
        if($this->occid && $postArr['vtid'] && is_numeric($postArr['vclid']) && is_numeric($postArr['vtid'])){
            $sql = 'INSERT INTO fmvouchers(occid,clid,tid,notes,editornotes) '.
                'VALUES('.$this->occid.','.(int)$postArr['vclid'].','.($postArr['vtid']?(int)$postArr['vtid']:'NULL').','.
                ($postArr['vnotes']?'"'.Sanitizer::cleanInStr($postArr['vnotes']).'"':'NULL').','.
                ($postArr['veditnotes']?'"'.Sanitizer::cleanInStr($postArr['veditnotes']).'"':'NULL').')';
            if(!$this->conn->query($sql)){
                $this->errorMessage = 'ERROR linking voucher to checklist.';
                $status = false;
            }
            $this->conn->close();
        }
        return $status;
    }

    public function deleteVoucher($occid,$clid): bool
    {
        $status = true;
        if(is_numeric($occid) && is_numeric($clid)){
            $sql = 'DELETE FROM fmvouchers WHERE (occid = '.$occid.') AND (clid = '.$clid.') ';
            if(!$this->conn->query($sql)){
                $this->errorMessage = 'ERROR deleting voucher.';
                $status = false;
            }
            if(!($this->conn === null)) {
                $this->conn->close();
            }
        }
        return $status;
    }

    public function getDatasetArr(): array
    {
        $retArr = array();
        $roleArr = array();
        if($GLOBALS['SYMB_UID']){
            $sql1 = 'SELECT tablepk, role FROM userroles WHERE (tablename = "omoccurdatasets") AND (uid = '.$GLOBALS['SYMB_UID'].') ';
            $rs1 = $this->conn->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $roleArr[$r1->tablepk] = $r1->role;
            }
            $rs1->free();
        }

        $sql2 = 'SELECT datasetid, name, uid FROM omoccurdatasets ';
        $sql2 .= 'WHERE (uid = '.$GLOBALS['SYMB_UID'].') ';
        if($roleArr) {
            $sql2 .= 'OR (datasetid IN(' . implode(',', array_keys($roleArr)) . ')) ';
        }
        $sql2 .= 'ORDER BY name';
        $rs2 = $this->conn->query($sql2);
        if($rs2){
            while($r2 = $rs2->fetch_object()){
                $retArr[$r2->datasetid]['name'] = $r2->name;
                $roleStr = '';
                if(isset($GLOBALS['SYMB_UID']) && (int)$GLOBALS['SYMB_UID'] === (int)$r2->uid) {
                    $roleStr = 'owner';
                }
                elseif(isset($roleArr[$r2->datasetid]) && $roleArr[$r2->datasetid]) {
                    $roleStr = $roleArr[$r2->datasetid];
                }
                if($roleStr) {
                    $retArr[$r2->datasetid]['role'] = $roleStr;
                }
            }
            $rs2->free();
        }
        else {
            $this->errorMessage = 'ERROR: Unable to set datasets for user.';
        }

        $sql3 = 'SELECT datasetid, notes FROM omoccurdatasetlink WHERE occid = '.$this->occid;
        $rs3 = $this->conn->query($sql3);
        if($rs3){
            while($r3 = $rs3->fetch_object()){
                if(isset($retArr[$r3->datasetid])){
                    $retArr[$r3->datasetid]['linked'] = 1;
                    if($r3->notes) {
                        $retArr[$r3->datasetid]['notes'] = $r3->notes;
                    }
                }
            }
            $rs3->free();
        }
        else {
            $this->errorMessage = 'Unable to get related datasets.';
        }
        return $retArr;
    }

    public function linkToDataset($dsid,$dsName,$notes): bool
    {
        $status = true;
        if($this->occid){
            if(!$dsid && $dsName){
                if(strlen($dsName) > 100) {
                    $dsName = substr($dsName, 0, 100);
                }
                $sql1 = 'INSERT INTO omoccurdatasets(name,uid,collid) '.
                    'VALUES("'.Sanitizer::cleanInStr($dsName).'",'.$GLOBALS['SYMB_UID'].','.$this->collid.')';
                if($this->conn->query($sql1)){
                    $dsid = $this->conn->insert_id;
                }
                else{
                    $this->errorMessage = 'ERROR creating new dataset.';
                    $status = false;
                }
            }
            if($dsid && is_numeric($dsid)){
                $sql2 = 'INSERT INTO omoccurdatasetlink(datasetid,occid,notes) '.
                    'VALUES('.$dsid.','.$this->occid.',"'.Sanitizer::cleanInStr($notes).'")';
                if(!$this->conn->query($sql2)){
                    $this->errorMessage = 'ERROR linking to dataset.';
                    $status = false;
                }
            }
            $this->conn->close();
        }
        return $status;
    }

    public function getChecklists($clidExcludeArr): ?array
    {
        if(!array_key_exists('ClAdmin',$GLOBALS['USER_RIGHTS'])) {
            return null;
        }
        $returnArr = array();
        $targetArr = array_diff($GLOBALS['USER_RIGHTS']['ClAdmin'],$clidExcludeArr);
        if($targetArr){
            $sql = 'SELECT name, clid '.
                'FROM fmchecklists WHERE clid IN('.implode(',',$targetArr).') '.
                'ORDER BY Name';
            //echo $sql;
            if($result = $this->conn->query($sql)){
                while($row = $result->fetch_object()){
                    $returnArr[$row->clid] = $row->name;
                }
                $result->free();
            }
        }
        return $returnArr;
    }

    public function checkArchive(): array
    {
        $retArr = array();
        $sql = 'SELECT archiveobj, notes '.
            'FROM guidoccurrences '.
            'WHERE occid = '.$this->occid.' AND archiveobj IS NOT NULL ';
        //echo $sql;
        if($rs = $this->conn->query($sql)){
            if($r = $rs->fetch_object()){
                $retArr['obj'] = json_decode($r->archiveobj, true);
                $retArr['notes'] = $r->notes;
            }
            $rs->free();
        }
        if(!$retArr){
            $sql = 'SELECT archiveobj, notes '.
                'FROM guidoccurrences '.
                'WHERE occid IS NULL AND archiveobj LIKE \'%"occid":"'.$this->occid.'"%\'';
            //echo $sql;
            if($rs = $this->conn->query($sql)){
                if($r = $rs->fetch_object()){
                    $retArr['obj'] = json_decode($r->archiveobj, true);
                    $retArr['notes'] = $r->notes;
                }
                $rs->free();
            }
        }
        return $retArr;
    }

    public function isTaxonomicEditor(): int
    {
        $isEditor = 0;

        $editTidArr = array();
        $sqlut = 'SELECT idusertaxonomy, tid, geographicscope '.
            'FROM usertaxonomy '.
            'WHERE editorstatus = "OccurrenceEditor" AND uid = '.$GLOBALS['SYMB_UID'];
        //echo $sqlut;
        $rsut = $this->conn->query($sqlut);
        while($rut = $rsut->fetch_object()){
            $editTidArr[$rut->tid] = $rut->geographicscope;
        }
        $rsut->free();

        if($editTidArr){
            $occTidArr = array();
            $sql = '';
            if($this->occArr['tidinterpreted']){
                $occTidArr[] = $this->occArr['tidinterpreted'];
                $sql = 'SELECT parenttid '.
                    'FROM taxaenumtree '.
                    'WHERE (tid = '.$this->occArr['tidinterpreted'].')';
            }
            elseif($this->occArr['sciname'] || $this->occArr['family']){
                $sql = 'SELECT e.parenttid '.
                    'FROM taxaenumtree e INNER JOIN taxa t ON e.tid = t.tid ';
                if($this->occArr['sciname']){
                    $taxon = $this->occArr['sciname'];
                    $tok = explode(' ',$this->occArr['sciname']);
                    if($tok && (count($tok) > 1) && strlen($tok[0]) > 2) {
                        $taxon = $tok[0];
                    }
                    $sql .= 'AND (t.sciname = "'.Sanitizer::cleanInStr($taxon).'") ';
                }
                elseif($this->occArr['family']){
                    $sql .= 'AND (t.sciname = "'.Sanitizer::cleanInStr($this->occArr['family']).'") ';
                }
            }
            if($sql){
                $rs2 = $this->conn->query($sql);
                while($r2 = $rs2->fetch_object()){
                    $occTidArr[] = $r2->parenttid;
                }
                $rs2->free();
            }
            if($occTidArr && array_intersect(array_keys($editTidArr), $occTidArr)) {
                $isEditor = 3;
            }
        }
        return $isEditor;
    }

    public function setOccid($occid): void
    {
        $this->occid = $occid;
    }

    public function getOccid(){
        return $this->occid;
    }

    public function setCollid($id): void
    {
        $this->collid = $id;
    }

    public function setDbpk($pk): void
    {
        $this->dbpk = $pk;
    }

    public function setDisplayFormat($f): void
    {
        if(!in_array($f,array('json','xml','rdf','turtle','html'))) {
            $f = 'html';
        }
        $this->displayFormat = $f;
    }
}
