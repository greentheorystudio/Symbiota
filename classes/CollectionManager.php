<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');
include_once(__DIR__ . '/SOLRManager.php');

class CollectionManager {

    private $conn;

	public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function cleanSOLRIndex($collidStr): int
    {
        $SOLROccArr = array();
        $mysqlOccArr = array();
        $collidStr = Sanitizer::cleanInStr($this->conn, $collidStr);
        $solrWhere = 'q=(collid:(' . $collidStr . '))';
        $solrURL = $GLOBALS['SOLR_URL'].'/select?'.$solrWhere;
        $solrURL .= '&rows=1&start=1&wt=json';
        //echo str_replace(' ','%20',$solrURL);
        $solrArrJson = file_get_contents(str_replace(' ','%20',$solrURL));
        $solrArr = json_decode($solrArrJson, true);
        $cnt = $solrArr['response']['numFound'];
        $occURL = $GLOBALS['SOLR_URL'].'/select?'.$solrWhere.'&rows='.$cnt.'&start=1&fl=occid&wt=json';
        //echo str_replace(' ','%20',$occURL);
        $solrOccArrJson = file_get_contents(str_replace(' ','%20',$occURL));
        $solrOccArr = json_decode($solrOccArrJson, true);
        $recArr = $solrOccArr['response']['docs'];
        foreach($recArr as $k){
            $SOLROccArr[] = $k['occid'];
        }
        $sql = 'SELECT occid FROM omoccurrences WHERE collid IN(' . $collidStr . ') ';
        if($rs = $this->conn->query($sql)){
            while($r = $rs->fetch_object()){
                $mysqlOccArr[] = $r->occid;
            }
        }
        $delOccArr = array_diff($SOLROccArr, $mysqlOccArr);
        if($delOccArr){
            (new SOLRManager)->deleteSOLRDocument($delOccArr);
        }
        return 1;
    }

    public function getCollectionArr(): array
    {
        $retArr = array();
        $sql = 'SELECT c.collid, c.institutioncode, c.CollectionCode, c.CollectionName, c.collectionid, '.
            'c.FullDescription, c.Homepage, c.individualurl, c.Contact, c.email, c.datarecordingmethod, c.defaultRepCount, '.
            'c.latitudedecimal, c.longitudedecimal, c.icon, c.colltype, c.managementtype, c.publicedits, c.isPublic, '.
            'c.guidtarget, c.rights, c.rightsholder, c.accessrights, c.dwcaurl, c.sortseq, c.securitykey, c.collectionguid, s.uploaddate '.
            'FROM omcollections AS c LEFT JOIN omcollectionstats AS s ON c.collid = s.collid ';
        if(!$GLOBALS['IS_ADMIN']){
            $sql .= 'WHERE c.isPublic = 1 ';
            if($GLOBALS['PERMITTED_COLLECTIONS']){
                $sql .= 'OR c.collid IN('.implode(',', $GLOBALS['PERMITTED_COLLECTIONS']).') ';
            }
        }
        $sql .= 'ORDER BY c.SortSeq, c.CollectionName';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($row = $rs->fetch_object()){
            $nodeArr = array();
            $uDate = null;
            $nodeArr['collid'] = $row->collid;
            $nodeArr['institutioncode'] = $row->institutioncode;
            $nodeArr['collectioncode'] = $row->CollectionCode;
            $nodeArr['collectionname'] = $row->CollectionName;
            $nodeArr['collectionid'] = $row->collectionid;
            $nodeArr['fulldescription'] = $row->FullDescription;
            $nodeArr['homepage'] = $row->Homepage;
            $nodeArr['individualurl'] = $row->individualurl;
            $nodeArr['contact'] = $row->Contact;
            $nodeArr['email'] = $row->email;
            $nodeArr['latitudedecimal'] = $row->latitudedecimal;
            $nodeArr['longitudedecimal'] = $row->longitudedecimal;
            $nodeArr['icon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($row->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->icon) : $row->icon;
            $nodeArr['colltype'] = $row->colltype;
            $nodeArr['managementtype'] = $row->managementtype;
            $nodeArr['datarecordingmethod'] = $row->datarecordingmethod;
            $nodeArr['defaultRepCount'] = $row->defaultRepCount;
            $nodeArr['publicedits'] = $row->publicedits;
            $nodeArr['guidtarget'] = $row->guidtarget;
            $nodeArr['rights'] = $row->rights;
            $nodeArr['rightsholder'] = $row->rightsholder;
            $nodeArr['accessrights'] = $row->accessrights;
            $nodeArr['dwcaurl'] = $row->dwcaurl;
            $nodeArr['sortseq'] = $row->sortseq;
            $nodeArr['skey'] = $row->securitykey;
            $nodeArr['guid'] = $row->collectionguid;
            if($row->uploaddate){
                $uDate = $row->uploaddate;
                $month = substr($uDate,5,2);
                $day = substr($uDate,8,2);
                $year = substr($uDate,0,4);
                $uDate = date('j F Y', mktime(0,0,0, $month, $day, $year));
            }
            $retArr['uploaddate'] = $uDate;
            $retArr[] = $nodeArr;
        }
        $rs->free();
        return $retArr;
    }

    public function getCollectionInfoArr($collId): array
    {
        $retArr = array();
        $uDate = null;
        $sql = 'SELECT c.collid, c.institutioncode, c.collectioncode, c.collectionname, c.collectionid, c.fulldescription, c.homepage, '.
            'c.individualurl, c.contact, c.email, c.datarecordingmethod, c.defaultrepcount, c.latitudedecimal, c.longitudedecimal, '.
            'c.icon, c.colltype, c.managementtype, c.publicedits, c.ispublic, c.guidtarget, c.rights, c.rightsholder, '.
            'c.accessrights, c.dwcaurl, c.sortseq, c.securitykey, c.collectionguid, c.publishtogbif, c.publishtoidigbio, '.
            'c.aggkeysstr, s.uploaddate, s.recordcnt, s.georefcnt, s.familycnt, s.genuscnt, s.speciescnt, s.dynamicproperties, '.
            'i.iid, i.institutionname, i.institutionname2, i.address1, i.address2, i.city, i.stateprovince, i.postalcode, i.country '.
            'FROM omcollections AS c LEFT JOIN omcollectionstats AS s ON c.collid = s.collid '.
            'LEFT JOIN institutions AS i ON c.iid = i.iid '.
            'WHERE c.collid = '.(int)$collId.' ';
        //echo $sql;
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr['collid'] = $r->collid;
            $retArr['institutioncode'] = $r->institutioncode;
            $retArr['collectioncode'] = $r->collectioncode;
            $retArr['collectionname'] = $r->collectionname;
            $retArr['collectionid'] = $r->collectionid;
            $retArr['fulldescription'] = $r->fulldescription;
            $retArr['homepage'] = $r->homepage;
            $retArr['individualurl'] = $r->individualurl;
            $retArr['contact'] = $r->contact;
            $retArr['email'] = $r->email;
            $retArr['datarecordingmethod'] = $r->datarecordingmethod;
            $retArr['defaultrepcount'] = $r->defaultrepcount;
            $retArr['latitudedecimal'] = $r->latitudedecimal;
            $retArr['longitudedecimal'] = $r->longitudedecimal;
            $retArr['icon'] = ($GLOBALS['CLIENT_ROOT'] && strncmp($r->icon, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $r->icon) : $r->icon;
            $retArr['colltype'] = $r->colltype;
            $retArr['managementtype'] = $r->managementtype;
            $retArr['publicedits'] = $r->publicedits;
            $retArr['ispublic'] = (int)$r->ispublic;
            $retArr['guidtarget'] = $r->guidtarget;
            $retArr['rights'] = $r->rights;
            $retArr['rightsholder'] = $r->rightsholder;
            $retArr['accessrights'] = $r->accessrights;
            $retArr['dwcaurl'] = $r->dwcaurl;
            $retArr['sortseq'] = $r->sortseq;
            $retArr['securitykey'] = $r->securitykey;
            $retArr['collectionguid'] = $r->collectionguid;
            $retArr['publishtogbif'] = $r->publishtogbif;
            $retArr['publishtoidigbio'] = $r->publishtoidigbio;
            $retArr['aggkeysstr'] = $r->aggkeysstr;
            if($r->uploaddate){
                $uDate = $r->uploaddate;
                $month = substr($uDate,5,2);
                $day = substr($uDate,8,2);
                $year = substr($uDate,0,4);
                $uDate = date('j F Y', mktime(0,0,0, $month, $day, $year));
            }
            $retArr['uploaddate'] = $uDate;
            $retArr['recordcnt'] = $r->recordcnt;
            $retArr['georefcnt'] = $r->georefcnt;
            $retArr['familycnt'] = $r->familycnt;
            $retArr['genuscnt'] = $r->genuscnt;
            $retArr['speciescnt'] = $r->speciescnt;
            $retArr['dynamicProperties'] = $r->dynamicproperties;
            $retArr['iid'] = $r->iid;
            $retArr['institutionname'] = $r->institutionname;
            $retArr['institutionname2'] = $r->institutionname2;
            $retArr['address1'] = $r->address1;
            $retArr['address2'] = $r->address2;
            $retArr['city'] = $r->city;
            $retArr['stateprovince'] = $r->stateprovince;
            $retArr['postalcode'] = $r->postalcode;
            $retArr['country'] = $r->country;
            $retArr['configuredData'] = null;
        }
        $rs->free();
        if(file_exists($GLOBALS['SERVER_ROOT'] . '/content/json/collection' . $collId . 'occurrencedatafields.json')) {
            $retArr['configuredData'] = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'].'/content/json/collection'.$collId.'occurrencedatafields.json'), true);
        }
        return $retArr;
    }

    public function getSpeciesListDownloadData($collid): array
    {
        $returnArr = array();
        $targetTidArr = array();
        $parentTaxonArr = array();
        $tidSql = 'SELECT DISTINCT tid FROM omoccurrences WHERE collid = ' . (int)$collid . ' ';
        $rs = $this->conn->query($tidSql);
        while($r = $rs->fetch_object()){
            if($r->tid){
                $targetTidArr[] = $r->tid;
            }
        }
        $rs->free();
        $parentTaxonSql = 'SELECT DISTINCT te.tid, t.TID AS parentTid, t.RankId, t.SciName '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
            'WHERE te.tid IN(' . implode(',', $targetTidArr) . ') AND t.tid = t.tidaccepted AND t.RankId IN(10,30,60,100,140) ';
        //echo '<div>Parent sql: ' .$parentTaxonSql. '</div>';
        $rs = $this->conn->query($parentTaxonSql);
        while($r = $rs->fetch_object()){
            $parentTaxonArr[$r->tid][(int)$r->RankId]['id'] = $r->parentTid;
            $parentTaxonArr[$r->tid][(int)$r->RankId]['sciname'] = $r->SciName;
        }
        $rs->free();

        $sql = 'SELECT DISTINCT t.TID, t.SciName '.
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.tid = t.TID '.
            'WHERE (te.tid IN(' . implode(',', $targetTidArr) . ') AND t.RankId >= 180 AND t.tid = t.tidaccepted) '.
            'AND (t.SciName LIKE "% %" OR t.TID NOT IN(SELECT DISTINCT parenttid FROM taxa)) ';
        //echo '<div>Table sql: ' .$sql. '</div>';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $tid = $r->TID;
            if($tid){
                $recordArr = array();
                $parentArr = (array_key_exists($tid,$parentTaxonArr)?$parentTaxonArr[$tid]:array());
                $recordArr['kingdomName'] = (array_key_exists(10, $parentArr) ? $parentArr[10]['sciname'] : '');
                $recordArr['phylumName'] = (array_key_exists(30, $parentArr) ? $parentArr[30]['sciname'] : '');
                $recordArr['className'] = (array_key_exists(60, $parentArr) ? $parentArr[60]['sciname'] : '');
                $recordArr['orderName'] = (array_key_exists(100, $parentArr) ? $parentArr[100]['sciname'] : '');
                $recordArr['familyName'] = (array_key_exists(140, $parentArr) ? $parentArr[140]['sciname'] : '');
                $recordArr['SciName'] = $r->SciName;
                $returnArr[] = $recordArr;
            }
        }
        $rs->free();
        $kingdomName  = array_column($returnArr, 'kingdomName');
        $phylumName = array_column($returnArr, 'phylumName');
        $className = array_column($returnArr, 'className');
        $orderName = array_column($returnArr, 'orderName');
        $familyName = array_column($returnArr, 'familyName');
        $SciName = array_column($returnArr, 'SciName');
        array_multisort($kingdomName, SORT_ASC, $phylumName, SORT_ASC, $className, SORT_ASC, $orderName, SORT_ASC, $familyName, SORT_ASC, $SciName, SORT_ASC, $returnArr);
        array_unshift($returnArr, array('Kingdom', 'Phylum', 'Class', 'Order', 'Family', 'Scientific Name'));
        return $returnArr;
    }

    public function performOccurrenceCleaning($collidStr): void
    {
        $sql = 'UPDATE omoccurrences SET sciname = family WHERE collid IN(' . $collidStr . ') AND family IS NOT NULL AND ISNULL(sciname) ';
        $this->conn->query($sql);

        $sql = 'UPDATE omoccurrences AS o LEFT JOIN taxa AS t ON o.sciname = t.sciname '.
            'SET o.tid = t.tid '.
            'WHERE o.collid IN(' . $collidStr . ') AND ISNULL(o.tid) AND t.tid IS NOT NULL ';
        $this->conn->query($sql);

        $sql = 'UPDATE omoccurrences AS o INNER JOIN images AS i ON o.occid = i.occid '.
            'SET i.tid = o.tid '.
            'WHERE o.collid IN(' . $collidStr . ') AND i.tid <> o.tid ';
        $this->conn->query($sql);
    }

    public function updateCollectionStatistics($collidStr): int
    {
        $collidStr = Sanitizer::cleanInStr($this->conn, $collidStr);
        $this->performOccurrenceCleaning($collidStr);
        return $this->updateCollectionStats($collidStr);
    }

    public function updateCollectionStats($collidStr): int
    {
        $returnVal = 1;
        $recordCnt = 0;
        $georefCnt = 0;
        $familyCnt = 0;
        $genusCnt = 0;
        $speciesCnt = 0;
        $statsArr = array();
        
        $sql = 'SELECT COUNT(o.occid) AS SpecimenCount, COUNT(o.decimalLatitude) AS GeorefCount, '.
            'COUNT(DISTINCT o.family) AS FamilyCount, COUNT(o.typeStatus) AS TypeCount, '.
            'COUNT(DISTINCT CASE WHEN t.RankId >= 180 THEN t.UnitName1 ELSE NULL END) AS GeneraCount, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS SpecimensCountID, '.
            'COUNT(DISTINCT CASE WHEN t.RankId = 220 THEN t.SciName ELSE NULL END) AS SpeciesCount, '.
            'COUNT(DISTINCT CASE WHEN t.RankId >= 220 THEN t.SciName ELSE NULL END) AS TotalTaxaCount '.
            'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $recordCnt = $r->SpecimenCount;
            $georefCnt = $r->GeorefCount;
            $familyCnt = $r->FamilyCount;
            $genusCnt = $r->GeneraCount;
            $speciesCnt = $r->SpeciesCount;
            $statsArr['SpecimensCountID'] = $r->SpecimensCountID;
            $statsArr['TotalTaxaCount'] = $r->TotalTaxaCount;
            $statsArr['TypeCount'] = $r->TypeCount;
        }
        $rs->free();

        $sql = 'SELECT count(DISTINCT o.occid) as imgcnt '.
            'FROM omoccurrences AS o LEFT JOIN images AS i ON o.occid = i.occid '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $statsArr['imgcnt'] = $r->imgcnt;
        }
        $rs->free();

        $sql = 'SELECT COUNT(CASE WHEN g.resourceurl LIKE "http://www.boldsystems%" THEN o.occid ELSE NULL END) AS boldcnt, '.
            'COUNT(CASE WHEN g.resourceurl LIKE "http://www.ncbi%" THEN o.occid ELSE NULL END) AS gencnt '.
            'FROM omoccurrences AS o LEFT JOIN omoccurgenetic AS g ON o.occid = g.occid '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $statsArr['boldcnt'] = $r->boldcnt;
            $statsArr['gencnt'] = $r->gencnt;
        }
        $rs->free();

        $sql = 'SELECT count(r.occid) AS refcnt '.
            'FROM omoccurrences AS o LEFT JOIN referenceoccurlink AS r ON o.occid = r.occid '.
            'WHERE o.collid IN(' . $collidStr . ') ';
        $rs = $this->conn->query($sql);
        if($r = $rs->fetch_object()){
            $statsArr['refcnt'] = $r->refcnt;
        }
        $rs->free();

        $sql = 'SELECT o.family, COUNT(o.occid) AS SpecimensPerFamily, COUNT(o.decimalLatitude) AS GeorefSpecimensPerFamily, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerFamily, '.
            'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerFamily '.
            'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'WHERE o.collid IN(' . $collidStr . ') '.
            'GROUP BY o.family ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $family = $r->family ? str_replace(array('"',"'"), '',$r->family) : '';
            if($family){
                $statsArr['families'][$family]['SpecimensPerFamily'] = $r->SpecimensPerFamily;
                $statsArr['families'][$family]['GeorefSpecimensPerFamily'] = $r->GeorefSpecimensPerFamily;
                $statsArr['families'][$family]['IDSpecimensPerFamily'] = $r->IDSpecimensPerFamily;
                $statsArr['families'][$family]['IDGeorefSpecimensPerFamily'] = $r->IDGeorefSpecimensPerFamily;
            }
        }
        $rs->free();

        $sql = 'SELECT o.country, COUNT(o.occid) AS CountryCount, COUNT(o.decimalLatitude) AS GeorefSpecimensPerCountry, '.
            'COUNT(CASE WHEN t.RankId >= 220 THEN o.occid ELSE NULL END) AS IDSpecimensPerCountry, '.
            'COUNT(CASE WHEN t.RankId >= 220 AND o.decimalLatitude IS NOT NULL THEN o.occid ELSE NULL END) AS IDGeorefSpecimensPerCountry '.
            'FROM omoccurrences AS o LEFT JOIN taxa AS t ON o.tid = t.TID '.
            'WHERE o.collid IN(' . $collidStr . ') '.
            'GROUP BY o.country ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $country = $r->country ? str_replace(array('"',"'"), '',$r->country) : '';
            if($country){
                $statsArr['countries'][$country]['CountryCount'] = $r->CountryCount;
                $statsArr['countries'][$country]['GeorefSpecimensPerCountry'] = $r->GeorefSpecimensPerCountry;
                $statsArr['countries'][$country]['IDSpecimensPerCountry'] = $r->IDSpecimensPerCountry;
                $statsArr['countries'][$country]['IDGeorefSpecimensPerCountry'] = $r->IDGeorefSpecimensPerCountry;
            }
        }
        $rs->free();

        $returnArrJson = json_encode($statsArr);
        $sql = 'UPDATE omcollectionstats '.
            "SET dynamicProperties = '".Sanitizer::cleanInStr($this->conn, $returnArrJson)."' ".
            'WHERE collid IN(' . $collidStr . ') ';
        if(!$this->conn->query($sql)){
            $returnVal = 0;
        }
        
        $sql = 'UPDATE omcollectionstats AS cs '.
            'SET cs.recordcnt = '.$recordCnt.',cs.georefcnt = '.$georefCnt.',cs.familycnt = '.$familyCnt.',cs.genuscnt = '.$genusCnt.
            ',cs.speciescnt = '.$speciesCnt.', cs.datelastmodified = CURDATE() '.
            'WHERE cs.collid IN(' . $collidStr . ') ';
        if(!$this->conn->query($sql)){
            $returnVal = 0;
        }
        
        if($GLOBALS['SOLR_MODE']){
            (new SOLRManager)->updateSOLR();
        }
        return $returnVal;
    }
}
