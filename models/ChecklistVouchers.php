<?php
include_once(__DIR__ . '/../services/DbService.php');

class ChecklistVouchers{

	private $conn;

    private $fields = array(
        'vid' => array('dataType' => 'number', 'length' => 10),
        'tid' => array('dataType' => 'number', 'length' => 10),
        'clid' => array('dataType' => 'number', 'length' => 10),
        'occid' => array('dataType' => 'number', 'length' => 10),
        'editornotes' => array('dataType' => 'string', 'length' => 50),
        'preferredimage' => array('dataType' => 'number', 'length' => 11),
        'notes' => array('dataType' => 'string', 'length' => 250),
        'initialtimestamp' => array('dataType' => 'timestamp', 'length' => 0)
    );

    public function __construct(){
        $connection = new DbService();
	    $this->conn = $connection->getConnection();
	}

 	public function __destruct(){
        $this->conn->close();
	}

    public function createChecklistVoucherRecord($clid, $occid, $tid): int
    {
        $newID = 0;
        $sql = 'INSERT INTO fmvouchers(clid, occid, tid) '.
            'VALUES (' . (int)$clid . ', ' . (int)$occid . ', ' . ($tid ? (int)$tid : 'NULL') . ') ';
        //echo "<div>".$sql."</div>";
        if($this->conn->query($sql)){
            $newID = $this->conn->insert_id;
        }
        return $newID;
    }

    public function deleteChecklistVoucherRecord($clid, $occid): int
    {
        $retVal = 1;
        if($clid && $occid){
            $sql = 'DELETE FROM fmvouchers WHERE clid = ' . (int)$clid . ' AND occid = ' . (int)$occid . ' ';
            //echo $sql;
            if(!$this->conn->query($sql)){
                $retVal = 0;
            }
        }
        return $retVal;
    }

    public function deleteOccurrenceChecklistVoucherRecords($idType, $id): int
    {
        $retVal = 0;
        $whereStr = '';
        if($idType === 'occid'){
            $whereStr = 'occid = ' . (int)$id;
        }
        elseif($idType === 'occidArr'){
            $whereStr = 'occid IN(' . implode(',', $id) . ')';
        }
        elseif($idType === 'collid'){
            $whereStr = 'occid IN(SELECT occid FROM omoccurrences WHERE collid = ' . (int)$id . ')';
        }
        if($whereStr){
            $sql = 'DELETE FROM fmvouchers WHERE ' . $whereStr . ' ';
            if($this->conn->query($sql)){
                $retVal = 1;
            }
        }
        return $retVal;
    }

    public function getChecklistListByOccurrenceVoucher($occid): array
    {
        $retArr = array();
        if($occid){
            $sql = 'SELECT c.clid, c.name '.
                'FROM fmchecklists AS c LEFT JOIN fmvouchers AS v ON c.clid = v.clid '.
                'WHERE v.occid = ' . (int)$occid . ' ORDER BY c.name ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    $nodeArr['clid'] = $row['clid'];
                    $nodeArr['name'] = $row['name'];
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getChecklistTaxaVouchers($clid, $tid): array
    {
        $retArr = array();
        if($clid && $tid){
            $sql = 'SELECT o.occid, c.collectionname, c.institutioncode, c.collectioncode, o.sciname, o.catalognumber, '.
                'o.othercatalognumbers, o.recordedby, o.recordnumber, o.eventdate, o.country, o.stateprovince, o.county, '.
                'o.locality, o.decimallatitude, o.decimallongitude '.
                'FROM fmvouchers AS v LEFT JOIN omoccurrences AS o ON v.occid = o.occid '.
                'LEFT JOIN omcollections AS c ON o.collid = c.collid '.
                'WHERE v.clid = ' . (int)$clid . ' AND v.tid = ' . (int)$tid . ' ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $nodeArr = array();
                    $nodeArr['occid'] = $row['occid'];
                    $nodeArr['collectionname'] = $row['collectionname'];
                    $nodeArr['institutioncode'] = $row['institutioncode'];
                    $nodeArr['collectioncode'] = $row['collectioncode'];
                    $nodeArr['sciname'] = $row['sciname'];
                    $nodeArr['catalognumber'] = $row['catalognumber'];
                    $nodeArr['othercatalognumbers'] = $row['othercatalognumbers'];
                    $nodeArr['recordedby'] = $row['recordedby'];
                    $nodeArr['recordnumber'] = $row['recordnumber'];
                    $nodeArr['eventdate'] = $row['eventdate'];
                    $nodeArr['country'] = $row['country'];
                    $nodeArr['stateprovince'] = $row['stateprovince'];
                    $nodeArr['county'] = $row['county'];
                    $nodeArr['locality'] = $row['locality'];
                    $nodeArr['decimallatitude'] = $row['decimallatitude'];
                    $nodeArr['decimallongitude'] = $row['decimallongitude'];
                    $retArr[] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function getChecklistVouchers($clidArr): array
    {
        $retArr = array();
        if($clidArr && count($clidArr) > 0){
            $sql = 'SELECT v.tid, o.occid, c.institutioncode, o.catalognumber, o.othercatalognumbers, o.recordedby, o.recordnumber, o.eventdate '.
                'FROM fmvouchers AS v LEFT JOIN omoccurrences AS o ON v.occid = o.occid '.
                'LEFT JOIN omcollections AS c ON o.collid = c.collid '.
                'WHERE v.clid IN(' . implode(',', $clidArr) . ') ORDER BY o.collid ';
            if($result = $this->conn->query($sql)){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    if(!array_key_exists($row['tid'], $retArr)){
                        $retArr[$row['tid']] = array();
                    }
                    $nodeArr = array();
                    $voucherLabel = '';
                    if($row['recordedby']){
                        $voucherLabel .= $row['recordedby'];
                    }
                    elseif($row['catalognumber']){
                        $voucherLabel .= $row['catalognumber'];
                    }
                    elseif($row['othercatalognumbers']){
                        $voucherLabel .= $row['othercatalognumbers'];
                    }
                    if(strlen($voucherLabel) > 25){
                        $breakPoint = strpos($voucherLabel,';');
                        if(!$breakPoint){
                            $breakPoint = strpos($voucherLabel,',');
                        }
                        if(!$breakPoint){
                            $breakPoint = strpos($voucherLabel,' ', 10);
                        }
                        if($breakPoint){
                            $voucherLabel = substr($voucherLabel,0, $breakPoint) . '...';
                        }
                    }
                    if($row['recordnumber']){
                        $voucherLabel .= ' ' . $row['recordnumber'];
                    }
                    elseif($row['eventdate']){
                        $voucherLabel .= $row['eventdate'];
                    }
                    $voucherLabel .= ' [' . $row['institutioncode'] . ']';
                    $nodeArr['occid'] = $row['occid'];
                    $nodeArr['label'] = trim($voucherLabel);
                    $retArr[$row['tid']][] = $nodeArr;
                    unset($rows[$index]);
                }
            }
        }
        return $retArr;
    }

    public function updateTidFromOccurrenceRecord($occid, $tid): void
    {
        if((int)$occid > 0){
            $sql = 'UPDATE fmvouchers SET tid = ' . (((int)$tid > 0) ? (int)$tid : 'NULL') . ' WHERE occid = ' . (int)$occid . ' ';
            $this->conn->query($sql);
        }
    }
}
