<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');
include_once(__DIR__ . '/DwcArchiverCore.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class OccurrenceDataset {

    private $conn;
    private $collArr = array();
    private $datasetId = 0;
    private $errorArr = array();

    public function __construct(){
        $connection = new DbConnectionService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function getDatasetMetadata($dsid): array
    {
        $retArr = array();
        if($GLOBALS['SYMB_UID'] && $dsid){
            $sql = 'SELECT datasetid, name, notes, uid, sortsequence, initialtimestamp '.
                'FROM omoccurdatasets '.
                'WHERE (datasetid = '.$dsid.') ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr['name'] = $r->name;
                $retArr['notes'] = $r->notes;
                $retArr['uid'] = $r->uid;
                $retArr['sort'] = $r->sortsequence;
                $retArr['ts'] = $r->initialtimestamp;
            }
            $rs->free();
            $sql1 = 'SELECT role '.
                'FROM userroles '.
                'WHERE (tablename = "omoccurdatasets") AND (tablepk = '.$dsid.') AND (uid = '.$GLOBALS['SYMB_UID'].') ';
            $rs1 = $this->conn->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $retArr['roles'][] = $r1->role;
            }
            $rs1->free();
        }
        return $retArr;
    }

    public function getDatasetArr(): array
    {
        $retArr = array();
        if($GLOBALS['SYMB_UID']){
            $sql = 'SELECT datasetid, name, notes, sortsequence, initialtimestamp '.
                'FROM omoccurdatasets '.
                'WHERE (uid = '.$GLOBALS['SYMB_UID'].') '.
                'ORDER BY sortsequence,name';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $retArr['owner'][$r->datasetid]['name'] = $r->name;
                $retArr['owner'][$r->datasetid]['notes'] = $r->notes;
                $retArr['owner'][$r->datasetid]['sort'] = $r->sortsequence;
                $retArr['owner'][$r->datasetid]['ts'] = $r->initialtimestamp;
            }
            $rs->free();

            $sql1 = 'SELECT d.datasetid, d.name, d.notes, d.sortsequence, d.initialtimestamp, r.role '.
                'FROM omoccurdatasets d INNER JOIN userroles r ON d.datasetid = r.tablepk '.
                'WHERE (r.uid = '.$GLOBALS['SYMB_UID'].') AND (r.role IN("DatasetAdmin","DatasetEditor","DatasetReader")) '.
                'ORDER BY sortsequence,name';
            //echo $sql1;
            $rs1 = $this->conn->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $retArr['other'][$r1->datasetid]['name'] = $r1->name;
                $retArr['other'][$r1->datasetid]['role'] = $r1->role;
                $retArr['other'][$r1->datasetid]['notes'] = $r1->notes;
                $retArr['other'][$r1->datasetid]['sort'] = $r1->sortsequence;
                $retArr['other'][$r1->datasetid]['ts'] = $r1->initialtimestamp;
            }
            $rs1->free();
        }
        return $retArr;
    }

    public function editDataset($dsid,$name,$notes): bool
    {
        $sql = 'UPDATE omoccurdatasets '.
            'SET name = "'.SanitizerService::cleanInStr($this->conn,$name).'", notes = "'.SanitizerService::cleanInStr($this->conn,$notes).'" '.
            'WHERE datasetid = '.$dsid;
        if(!$this->conn->query($sql)){
            $this->errorArr[] = 'ERROR saving dataset edits';
            return false;
        }
        return true;
    }

    public function createDataset($name,$notes,$uid): bool
    {
        $sql = 'INSERT INTO omoccurdatasets (name,notes,uid) '.
            'VALUES("'.SanitizerService::cleanInStr($this->conn,$name).'",'.($notes?'"'.SanitizerService::cleanInStr($this->conn,$notes).'"':'NULL').','.$uid.') ';
        if($this->conn->query($sql)){
            $this->datasetId = $this->conn->insert_id;
        }
        else{
            $this->errorArr[] = 'ERROR creating new dataset.';
            return false;
        }
        return true;
    }

    public function mergeDatasets($targetArr): bool
    {
        $status = true;
        $targetDsid = array_shift($targetArr);
        $sql1 = 'UPDATE omoccurdatasets SET name = CONCAT(name," (merged)") WHERE datasetid = '.$targetDsid;
        if($this->conn->query($sql1)){
            $sql2 = 'UPDATE IGNORE omoccurdatasetlink SET datasetid = '.$targetDsid.' WHERE datasetid IN('.implode(',',$targetArr).')';
            if($this->conn->query($sql2)){
                $sql3 = 'DELETE FROM omoccurdatasets WHERE datasetid IN('.implode(',',$targetArr).')';
                if(!$this->conn->query($sql3)){
                    $this->errorArr[] = 'WARNING: Unable to remove extra datasets.';
                    $status = false;
                }
            }
            else{
                $this->errorArr[] = 'FATAL ERROR: Unable to transfer occurrence records into target dataset.';
                $status = false;
            }
        }
        else{
            $this->errorArr[] = 'FATAL ERROR: Unable to rename target dataset in prep for merge.';
            $status = false;
        }
        return $status;
    }

    public function cloneDatasets($targetArr): bool
    {
        $status = true;
        $sql = 'SELECT datasetid, name, notes, sortsequence FROM omoccurdatasets '.
            'WHERE datasetid IN('.implode(',',$targetArr).')';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $newName = $r->name.' - Copy';
            $newNameTemp = $newName;
            $cnt = 1;
            do{
                $sql1 = 'SELECT datasetid FROM omoccurdatasets WHERE name = "'.$newNameTemp.'" AND uid = '.$GLOBALS['SYMB_UID'];
                $nameExists = false;
                $rs1 = $this->conn->query($sql1);
                while($rs1->fetch_object()){
                    $newNameTemp = $newName.' '.$cnt;
                    $nameExists = true;
                    $cnt++;
                }
                $rs1->free();
            }while($nameExists);
            $newName = $newNameTemp;
            $sql2 = 'INSERT INTO omoccurdatasets(name, notes, sortsequence, uid) '.
                'VALUES("'.$newName.'","'.$r->notes.'",'.($r->sortsequence?:'""').','.$GLOBALS['SYMB_UID'].')';
            if($this->conn->query($sql2)){
                $this->datasetId = $this->conn->insert_id;
                $sql3 = 'INSERT INTO omoccurdatasetlink(occid, datasetid, notes) '.
                    'SELECT occid, '.$this->datasetId.', notes FROM omoccurdatasetlink WHERE datasetid = '.$r->datasetid;
                if(!$this->conn->query($sql3)){
                    $this->errorArr[] = 'ERROR: Unable to clone dataset links into new datasets.';
                    $status = false;
                }
            }
            else{
                $this->errorArr[] = 'ERROR: Unable to create new dataset within clone method.';
                $status = false;
            }
        }
        $rs->free();
        return $status;
    }

    public function deleteDataset($dsid): bool
    {
        $status = true;
        $sql1 = 'DELETE FROM userroles '.
            'WHERE (role IN("DatasetAdmin","DatasetEditor","DatasetReader")) AND (tablename = "omoccurdatasets") AND (tablepk = '.$dsid.') ';
        //echo $sql;
        if($this->conn->query($sql1)){
            $sql2 = 'DELETE FROM omoccurdatasets WHERE datasetid = '.$dsid;
            if($this->conn->query($sql2)){
                $sql3 = 'DELETE FROM omoccurdatasetlink WHERE datasetid = '.$dsid;
                if(!$this->conn->query($sql3)){
                    $this->errorArr[] = 'ERROR: Unable to delete target datasets.';
                    $status = false;
                }
            }
            else{
                $this->errorArr[] = 'ERROR: Unable to delete target datasets.';
                $status = false;
            }
        }
        else{
            $this->errorArr[] = 'ERROR deleting user.';
            $status = false;
        }
        return $status;
    }

    public function getUsers($datasetId): array
    {
        $retArr = array();
        $sql = 'SELECT u.uid, r.role, CONCAT_WS(", ",u.lastname,u.firstname) as username '.
            'FROM userroles r INNER JOIN users u ON r.uid = u.uid '.
            'WHERE r.role IN("DatasetAdmin","DatasetEditor","DatasetReader") '.
            'AND (r.tablename = "omoccurdatasets") AND (r.tablepk = '.$datasetId.')';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[$r->role][$r->uid] = $r->username;
        }
        $rs->free();
        return $retArr;
    }

    public function addUser($datasetID,$uid,$role): bool
    {
        if(is_numeric($uid)){
            $sql = 'INSERT INTO userroles(uid,role,tablename,tablepk,uidassignedby) VALUES('.$uid.',"'.SanitizerService::cleanInStr($this->conn,$role).'","omoccurdatasets",'.$datasetID.','.$GLOBALS['SYMB_UID'].')';
            if(!$this->conn->query($sql)){
                $this->errorArr[] = 'ERROR adding new user.';
                return false;
            }
        }
        return true;
    }

    public function deleteUser($dsid,$uid,$role): bool
    {
        $status = true;
        $sql = 'DELETE FROM userroles '.
            'WHERE (uid = '.$uid.') AND (role = "'.$role.'") AND (tablename = "omoccurdatasets") AND (tablepk = '.$dsid.') ';
        //echo $sql;
        if(!$this->conn->query($sql)){
            $this->errorArr[] = 'ERROR deleting user.';
            return false;
        }
        return $status;
    }

    public function getOccurrences($datasetId): array
    {
        $retArr = array();
        if($datasetId){
            $sql = 'SELECT o.occid, o.catalognumber, o.occurrenceid ,o.othercatalognumbers, '.
                'o.sciname, o.family, o.recordedby, o.recordnumber, o.eventdate, '.
                'o.country, o.stateprovince, o.county, o.locality, o.decimallatitude, o.decimallongitude, dl.notes '.
                'FROM omoccurrences o INNER JOIN omoccurdatasetlink dl ON o.occid = dl.occid '.
                'WHERE dl.datasetid = '.$datasetId;
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                if($r->catalognumber) {
                    $retArr[$r->occid]['catnum'] = $r->catalognumber;
                }
                elseif($r->occurrenceid) {
                    $retArr[$r->occid]['catnum'] = $r->occurrenceid;
                }
                elseif($r->othercatalognumbers) {
                    $retArr[$r->occid]['catnum'] = $r->othercatalognumbers;
                }
                else {
                    $retArr[$r->occid]['catnum'] = '';
                }
                $sciname = $r->sciname;
                if($r->family) {
                    $sciname .= ' (' . $r->family . ')';
                }
                $retArr[$r->occid]['sciname'] = $sciname;
                $collStr = $r->recordedby.' '.$r->recordnumber;
                if($r->eventdate) {
                    $collStr .= ' [' . $r->eventdate . ']';
                }
                $retArr[$r->occid]['coll'] = $collStr;
                $retArr[$r->occid]['loc'] = trim($r->country.', '.$r->stateprovince.', '.$r->county.', '.$r->locality,', ');
            }
            $rs->free();
        }
        return $retArr;
    }

    public function removeSelectedOccurrences($datasetId, $occArr): bool
    {
        $status = true;
        if($datasetId && $occArr){
            $sql = 'DELETE FROM omoccurdatasetlink '.
                'WHERE (datasetid = '.$datasetId.') AND (occid IN('.implode(',',$occArr).'))';
            if(!$this->conn->query($sql)){
                $this->errorArr[] = 'ERROR deleting selected occurrences.';
                return false;
            }
        }
        return $status;
    }

    public function addSelectedOccurrences($datasetId, $occArr): bool
    {
        $status = false;
        if(is_numeric($datasetId)){
            if(is_numeric($occArr)) {
                $occArr = array($occArr);
            }
            foreach($occArr as $v){
                if(is_numeric($v)){
                    $sql = 'INSERT IGNORE INTO omoccurdatasetlink(occid,datasetid) VALUES("'.$v.'",'.$datasetId.') ';
                    if($this->conn->query($sql)) {
                        $status = true;
                    }
                    else{
                        $this->errorArr[] = 'ERROR adding occurrence ('.$v.').';
                        $status = false;
                    }
                }
            }
        }
        return $status;
    }

    public function getUserList($term): array
    {
        $retArr = array();
        $sql = 'SELECT u.uid, CONCAT(CONCAT_WS(", ",u.lastname, u.firstname)," - ",u.username," [#",u.uid,"]") AS username '.
            'FROM users u '.
            'WHERE u.lastname LIKE "%'.SanitizerService::cleanInStr($this->conn,$term).'%" OR u.username LIKE "%'.SanitizerService::cleanInStr($this->conn,$term).'%" '.
            'ORDER BY u.lastname,u.firstname';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()) {
            $retArr[] = array('id'=>$r->uid,'label'=>$r->username);
        }
        $rs->free();
        return $retArr;
    }

    public function getCollName($collId): string
    {
        $collName = '';
        if($collId){
            if(!$this->collArr) {
                $this->setCollMetadata($collId);
            }
            $collCode = '';
            if($this->collArr['instcode']){
                $collCode .= $this->collArr['instcode'];
            }
            if($this->collArr['collcode']){
                $collCode .= ($collCode?':':'') . $this->collArr['collcode'];
            }
            $collName = $this->collArr['collname'].($collCode?' ('.$collCode.')':'');
        }
        return $collName;
    }

    private function setCollMetadata($collId): void
    {
        $sql = 'SELECT institutioncode, collectioncode, collectionname, colltype '.
            'FROM omcollections WHERE collid = '.$collId;
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $this->collArr['instcode'] = $r->institutioncode;
                $this->collArr['collcode'] = $r->collectioncode;
                $this->collArr['collname'] = $r->collectionname;
                $this->collArr['colltype'] = $r->colltype;
            }
            $rs->free();
        }
    }

    public function getErrorArr(): array
    {
        return $this->errorArr;
    }

    public function getErrorMessage(): string
    {
        return implode('; ',$this->errorArr);
    }

    public function getDatasetId(): int
    {
        return $this->datasetId;
    }
}
