<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceManager.php');
include_once(__DIR__ . '/Sanitizer.php');

class ImageLibraryManager{

    private $searchTermsArr = array();
    private $recordCount = 0;
    protected $conn;
    private $sqlWhere = '';

    public function __construct() {
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function getFamilyList(): array
    {
        $returnArray = array();
        $sql = 'SELECT DISTINCT ts.Family ';
        $sql .= $this->getImageSql();
        $sql .= 'AND (ts.Family Is Not Null) ';
        //echo $sql;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArray[] = $row->Family;
        }
        $result->free();
        sort($returnArray);
        return $returnArray;
    }

    public function getGenusList($inTaxon = null): array
    {
        $sql = 'SELECT DISTINCT t.UnitName1 ';
        $sql .= $this->getImageSql();
        if($inTaxon){
            $taxon = Sanitizer::cleanInStr($inTaxon);
            $sql .= "AND (ts.Family = '".$taxon."') ";
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArray[] = $row->UnitName1;
        }
        $result->free();
        sort($returnArray);
        return $returnArray;
    }

    public function getSpeciesList($inTaxon = null): array
    {
        $retArr = array();
        $tidArr = array();
        $taxon = '';
        if($inTaxon){
            $taxon = Sanitizer::cleanInStr($inTaxon);
            if(strpos($taxon, ' ')) {
                $tidArr = array_keys(OccurrenceManager::getSynonyms($taxon));
            }
        }
        $sql = 'SELECT DISTINCT t.tid, t.SciName ';
        $sql .= $this->getImageSql();
        if($tidArr){
            $sql .= 'AND ((t.SciName LIKE "'.$taxon.'%") OR (t.tid IN('.implode(',', $tidArr).'))) ';
        }
        elseif($taxon){
            $sql .= "AND ((t.SciName LIKE '".$taxon."%') OR (ts.family = '".$taxon."')) ";
        }
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $retArr[$row->tid] = $row->SciName;
        }
        $result->free();
        asort($retArr);
        return $retArr;
    }

    private function getImageSql(): string
    {
        $sql = 'FROM images AS i LEFT JOIN taxa AS t ON i.tid = t.tid '.
            'INNER JOIN taxstatus AS ts ON t.tid = ts.tid ';
        if(array_key_exists('tags',$this->searchTermsArr) && $this->searchTermsArr['tags']){
            $sql .= 'INNER JOIN imagetag AS it ON i.imgid = it.imgid ';
        }
        if(array_key_exists('keywords',$this->searchTermsArr) && $this->searchTermsArr['keywords']){
            $sql .= 'INNER JOIN imagekeywords AS ik ON i.imgid = ik.imgid ';
        }
        if($this->sqlWhere){
            $sql .= $this->sqlWhere.' AND ';
        }
        else{
            $sql .= 'WHERE ';
        }
        $sql .= '(i.sortsequence < 500) AND (t.RankId > 219) ';
        return $sql;
    }

    public function getCollectionImageList(): array
    {
        $stagingArr = array();
        $sql = 'SELECT collid, CONCAT(collectionname, " (", CONCAT_WS("-",institutioncode,collectioncode),")") as collname, colltype FROM omcollections ORDER BY collectionname';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $stagingArr[$r->collid]['name'] = $r->collname;
            $stagingArr[$r->collid]['type'] = (strpos($r->colltype,'Observations') !== false?'obs':'coll');
        }
        $rs->free();
        $sql = 'SELECT o.collid, COUNT(i.imgid) AS imgcnt '.
            'FROM images i INNER JOIN omoccurrences o ON i.occid = o.occid ';
        $sql .= 'GROUP BY o.collid ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $stagingArr[$row->collid]['imgcnt'] = $row->imgcnt;
        }
        $result->free();
        $retArr = array();
        foreach($stagingArr as $id => $collArr){
            if(array_key_exists('imgcnt', $collArr)){
                $retArr[$collArr['type']][$id]['imgcnt'] = $collArr['imgcnt'];
                $retArr[$collArr['type']][$id]['name'] = $collArr['name'];
            }
        }
        return $retArr;
    }

    public function getPhotographerList(): array
    {
        $retArr = array();
        $sql = 'SELECT u.uid, CONCAT_WS(", ", u.lastname, u.firstname) as pname, CONCAT_WS(", ", u.firstname, u.lastname) as fullname, u.email, Count(ti.imgid) AS imgcnt '.
            'FROM users u INNER JOIN images ti ON u.uid = ti.photographeruid ';
        $sql .= 'GROUP BY u.uid '.
            'ORDER BY u.lastname, u.firstname';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $retArr[$row->uid]['name'] = $row->pname;
            $retArr[$row->uid]['fullname'] = $row->fullname;
            $retArr[$row->uid]['imgcnt'] = $row->imgcnt;
        }
        $result->free();
        return $retArr;
    }

    public function getImageArr($pageRequest,$cntPerPage): array
    {
        $retArr = array();
        if(!$this->recordCount){
            $this->setRecordCnt();
        }
        $sql = 'SELECT DISTINCT i.imgid, o.tidinterpreted, t.tid, t.sciname, i.url, i.thumbnailurl, i.originalurl, '.
            'u.uid, u.lastname, u.firstname, i.caption, '.
            'o.occid, o.stateprovince, o.catalognumber, CONCAT_WS("-",c.institutioncode, c.collectioncode) as instcode ';
        $sql .= $this->getSqlBase();
        $sql .= $this->sqlWhere;
        if(array_key_exists('imagecount',$this->searchTermsArr)&&$this->searchTermsArr['imagecount']){
            if($this->searchTermsArr['imagecount'] === 'taxon'){
                $sql .= 'GROUP BY ts.tidaccepted ';
            }
            elseif($this->searchTermsArr['imagecount'] === 'specimen'){
                $sql .= 'GROUP BY o.occid ';
            }
        }
        $bottomLimit = ($pageRequest - 1)*$cntPerPage;
        if(array_key_exists('uploaddate1',$this->searchTermsArr) && $this->searchTermsArr['uploaddate1']){
            $sql .= 'ORDER BY i.InitialTimeStamp DESC ';
        }
        else{
            $sql .= 'ORDER BY t.sciname ';
        }
        $sql .= 'LIMIT ' .$bottomLimit. ',' .$cntPerPage;
        //echo '<div>Image sql: ' .$sql. '</div>';
        $result = $this->conn->query($sql);
        while($r = $result->fetch_object()){
            $imgId = $r->imgid;
            $retArr[$imgId]['imgid'] = $r->imgid;
            $retArr[$imgId]['tidaccepted'] = $r->tidinterpreted;
            $retArr[$imgId]['tid'] = $r->tid;
            $retArr[$imgId]['sciname'] = $r->sciname;
            $retArr[$imgId]['url'] = $r->url;
            $retArr[$imgId]['thumbnailurl'] = $r->thumbnailurl;
            $retArr[$imgId]['originalurl'] = $r->originalurl;
            $retArr[$imgId]['uid'] = $r->uid;
            $retArr[$imgId]['lastname'] = $r->lastname;
            $retArr[$imgId]['firstname'] = $r->firstname;
            $retArr[$imgId]['caption'] = $r->caption;
            $retArr[$imgId]['occid'] = $r->occid;
            $retArr[$imgId]['stateprovince'] = $r->stateprovince;
            $retArr[$imgId]['catalognumber'] = $r->catalognumber;
            $retArr[$imgId]['instcode'] = $r->instcode;
        }
        $result->free();
        return $retArr;
    }

    private function setRecordCnt(): void
    {
        if($this->sqlWhere){
            if(array_key_exists('imagecount',$this->searchTermsArr)&&$this->searchTermsArr['imagecount']){
                if($this->searchTermsArr['imagecount'] === 'taxon'){
                    $sql = 'SELECT COUNT(DISTINCT o.tidinterpreted) AS cnt ';
                }
                elseif($this->searchTermsArr['imagecount'] === 'specimen'){
                    $sql = 'SELECT COUNT(DISTINCT o.occid) AS cnt ';
                }
                else{
                    $sql = 'SELECT COUNT(DISTINCT i.imgid) AS cnt ';
                }
            }
            else{
                $sql = 'SELECT COUNT(DISTINCT i.imgid) AS cnt ';
            }
            $sql .= $this->getSqlBase();
            $sql .= $this->sqlWhere;
            //echo "<div>Count sql: ".$sql."</div>";
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $this->recordCount = $row->cnt;
            }
            $result->free();
        }
    }

    private function getSqlBase(): string
    {
        $sql = 'FROM images AS i ';
        $sql .= 'LEFT JOIN omoccurrences AS o ON i.occid = o.occid ';
        $sql .= 'LEFT JOIN omcollections AS c ON o.collid = c.collid ';
        $sql .= 'LEFT JOIN users AS u ON i.photographeruid = u.uid ';
        $sql .= 'LEFT JOIN taxa AS t ON i.tid = t.tid ';
        $sql .= 'LEFT JOIN taxstatus AS ts ON i.tid = ts.tid ';
        if(array_key_exists('imagetag',$this->searchTermsArr) && $this->searchTermsArr['imagetag']){
            $sql .= 'LEFT JOIN imagetag AS it ON i.imgid = it.imgid ';
        }
        if(array_key_exists('imagekeyword',$this->searchTermsArr) && $this->searchTermsArr['imagekeyword']){
            $sql .= 'LEFT JOIN imagekeywords AS ik ON i.imgid = ik.imgid ';
        }
        if(array_key_exists('clid',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN fmvouchers AS v ON o.occid = v.occid ';
        }
        if(array_key_exists('assochost',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN omoccurassociations AS oas ON o.occid = oas.occid ';
        }
        if(array_key_exists('polyArr',$this->searchTermsArr)) {
            $sql .= 'LEFT JOIN omoccurpoints AS p ON o.occid = p.occid ';
        }
        if(strpos($this->sqlWhere,'MATCH(f.recordedby)') || strpos($this->sqlWhere,'MATCH(f.locality)')){
            $sql .= 'LEFT JOIN omoccurrencesfulltext AS f ON o.occid = f.occid ';
        }
        return $sql;
    }

    public function getTagArr(): array
    {
        $retArr = array();
        $sql = 'SELECT DISTINCT keyvalue '.
            'FROM imagetag '.
            'ORDER BY keyvalue ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $retArr[] = $r->keyvalue;
            }
        }
        return $retArr;
    }

    public function setSearchTermsArr($stArr): void
    {
        $this->searchTermsArr = $stArr;
    }

    public function setSqlWhere($str): void
    {
        $this->sqlWhere = $str;
    }

    public function getSearchTermsArr(): array
    {
        return $this->searchTermsArr;
    }

    public function getRecordCnt(): int
    {
        return $this->recordCount;
    }
}
