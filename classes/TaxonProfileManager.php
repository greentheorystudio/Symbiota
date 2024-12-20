<?php
include_once(__DIR__ . '/../models/Taxa.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class TaxonProfileManager {

    private $conn;
    private $taxon = array();
    private $displayLocality = false;
    private $teReader = false;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
        if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin',$GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppAdmin',$GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll',$GLOBALS['USER_RIGHTS'])){
            $this->teReader = true;
        }
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function setTaxon($t, $clId = null): array
    {
        $t = trim($t);
        $sql = 'SELECT DISTINCT t.TID, t.family, t.SciName, t.Author, t.RankId, t.Source, t.Notes, t.parenttid, t.SecurityStatus, '.
            't.tidaccepted, t2.SciName AS accSciName, t2.family AS accFamily, t2.Author AS accAuthor, t2.RankId AS accRankId, '.
            't2.parenttid AS accParentTid, t2.SecurityStatus AS accSecurityStatus, t2.Notes AS accNotes, t2.Source AS accSource '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID ';
        if(is_numeric($t)){
            $sql .= 'WHERE t.TID = '.$this->conn->real_escape_string($t).' ';
        }
        else{
            $sql .= 'WHERE t.SciName = "'.$this->conn->real_escape_string($t).'" ';
        }
        $sql .= 'ORDER BY accSciName ';
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $accepted = ($row->TID === $row->tidaccepted);
            $this->taxon['submittedTid'] = (int)$row->TID;
            $this->taxon['submittedSciName'] = $row->SciName;
            $this->taxon['tid'] = $accepted ? (int)$row->TID : (int)$row->tidaccepted;
            $this->taxon['sciName'] = $accepted ? $row->SciName : $row->accSciName;
            $this->taxon['author'] = $accepted ? $row->Author : $row->accAuthor;
            $this->taxon['rankId'] = $accepted ? (int)$row->RankId : (int)$row->accRankId;
            $this->taxon['family'] = $accepted ? $row->family : $row->accFamily;
            $this->taxon['taxonNotes'] = $accepted ? $row->Notes : $row->accNotes;
            $this->taxon['taxonSources'] = $accepted ? $row->Source : $row->accSource;
            $this->taxon['parentTid'] = $accepted ? (int)$row->parenttid : (int)$row->accParentTid;
            $this->taxon['securityStatus'] = $accepted ? (int)$row->SecurityStatus : (int)$row->accSecurityStatus;
        }
        $result->close();
        if($this->taxon){
            if($this->taxon['securityStatus'] === 0 || $this->teReader){
                $this->displayLocality = true;
            }
            $this->taxon['identifiers'] = (new Taxa)->getTaxonIdentifiersFromTid($this->taxon['tid']);
            $this->taxon['vernaculars'] = array();
            $this->taxon['synonyms'] = array();
            $this->taxon['imageCnt'] = array();
            $this->taxon['map'] = $this->getMapImgUrl($this->taxon['tid'],$this->taxon['securityStatus']);
            if($clId){
                $this->setClName($clId);
            }
            $this->setVernaculars();
            $this->setSynonyms();
            $this->setTaxaImageCount();
            $this->taxon['sppArr'] = array();
            $this->setSppData($clId);
        }

        return $this->taxon;
    }

    public function setSppData($clId): void
    {
        $tids = array();
        if($clId){
            $sql = 'SELECT t.tid, t.RankId, t.sciname, t.securitystatus '.
                'FROM taxa AS t INNER JOIN fmchklsttaxalink AS ctl ON ctl.TID = t.tid '.
                'WHERE t.tid = t.tidaccepted AND ctl.clid = '.$clId.' AND t.parenttid = '.$this->taxon['tid'].' '.
                'ORDER BY t.sciname ';
        }
        else{
            $sql = 'SELECT DISTINCT t.sciname, t.RankId, t.tid, t.securitystatus '.
                'FROM taxa AS t WHERE t.tid = t.tidaccepted AND t.parenttid = '.$this->taxon['tid'].' '.
                'ORDER BY t.sciname ';
        }
        //echo $sql; exit;
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $sn = $row->sciname;
            $this->taxon['sppArr'][$sn]['sciName'] = $sn;
            $this->taxon['sppArr'][$sn]['tid'] = $row->tid;
            $this->taxon['sppArr'][$sn]['rankid'] = $row->RankId;
            $this->taxon['sppArr'][$sn]['security'] = $row->securitystatus;
            $tids[] = $row->tid;
        }
        $result->close();
        if($tids){
            $sql = 'SELECT t.sciname, t.tid, i.imgid, i.url, i.thumbnailurl, i.caption, '.
                'IFNULL(i.photographer,CONCAT_WS(" ",u.firstname,u.lastname)) AS photographer '.
                'FROM taxa AS t LEFT JOIN images AS i ON t.tid = i.tid '.
                'LEFT JOIN users AS u ON i.photographeruid = u.uid '.
                'WHERE t.tid IN('.implode(',',$tids).') AND i.sortsequence < 50 '.
                'GROUP BY t.TID ';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $sciName = $row->sciname;
                if($row->url && array_key_exists($sciName,$this->taxon['sppArr'])){
                    $imgUrl = $row->thumbnailurl ?: $row->url;
                    if($imgUrl && $GLOBALS['CLIENT_ROOT'] && strncmp($imgUrl, '/', 1) === 0) {
                        $imgUrl = $GLOBALS['CLIENT_ROOT'] . $imgUrl;
                    }
                    $this->taxon['sppArr'][$sciName]['url'] = $imgUrl;
                    $this->taxon['sppArr'][$sciName]['caption'] = $row->caption;
                }
            }
            $result->close();

            $sql = 'SELECT t.sciname, t.tid, i.imgid, i.url, i.thumbnailurl, i.caption, '.
                'IFNULL(i.photographer,CONCAT_WS(" ",u.firstname,u.lastname)) AS photographer '.
                'FROM images AS i LEFT JOIN taxaenumtree AS te ON i.tid = te.tid '.
                'LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
                'LEFT JOIN users AS u ON i.photographeruid = u.uid '.
                'WHERE te.parenttid IN('.implode(',',$tids).') AND t.TID = t.tidaccepted AND i.sortsequence < 50 '.
                'GROUP BY t.TID ';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $sciName = $row->sciname;
                if($row->url && array_key_exists($sciName,$this->taxon['sppArr']) && !array_key_exists('url',$this->taxon['sppArr'][$sciName])){
                    $imgUrl = $row->thumbnailurl ?: $row->url;
                    if($imgUrl && strncmp($imgUrl, '/', 1) === 0) {
                        $imgUrl = $GLOBALS['CLIENT_ROOT'] . $imgUrl;
                    }
                    $this->taxon['sppArr'][$sciName]['url'] = $imgUrl;
                    $this->taxon['sppArr'][$sciName]['caption'] = $row->caption;
                }
            }
            $result->close();
        }
        foreach($this->taxon['sppArr'] as $sn => $snArr){
            if((int)$snArr['rankid'] > 140){
                $this->taxon['sppArr'][$sn]['map'] = $this->getMapImgUrl((int)$snArr['tid'],(int)$snArr['security']);
            }
        }
    }

    public function setVernaculars(): void
    {
        if($this->taxon['tid']){
            $sql = 'SELECT v.VernacularName, v.`language`, l.iso639_1 '.
                'FROM taxavernaculars AS v INNER JOIN taxa AS t ON v.tid = t.tidaccepted '.
                'LEFT JOIN adminlanguages AS l ON v.langid = l.langid '.
                'WHERE t.TID = '.$this->taxon['tid'].' '.
                'ORDER BY v.VernacularName';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $vernName = $row->VernacularName;
                if($row->iso639_1 !== $GLOBALS['DEFAULT_LANG']){
                    $vernName .= ' (' . $row->language . ')';
                }
                $this->taxon['vernaculars'][] = $vernName;
            }
            ksort($this->taxon['vernaculars']);
            $result->free();
        }
    }

    public function setSynonyms(): void
    {
        if($this->taxon['tid']){
            $sql = 'SELECT tid, SciName, Author FROM taxa '.
                'WHERE tidaccepted = '.$this->taxon['tid'].' AND tid <> '.$this->taxon['tid'].' '.
                'ORDER BY SciName';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $synArr = array();
                $synArr['sciname'] = $row->SciName;
                $synArr['author'] = $row->Author;
                $this->taxon['synonyms'][] = $synArr;
            }
            $result->close();
        }
    }

    private function setTaxaImageCount(): void
    {
        if($this->taxon['tid']){
            $sql = 'SELECT t.tid '.
                'FROM images AS ti LEFT JOIN taxa AS t ON ti.tid = t.tid '.
                'WHERE t.tidaccepted IN(SELECT DISTINCT t.tid FROM taxa AS t LEFT JOIN taxaenumtree AS tn ON t.tid = tn.tid '.
                'WHERE t.tid = t.tidaccepted AND (tn.parenttid = '.$this->taxon['tid'].' OR t.tid = '.$this->taxon['tid'].')) ';
            if(!$this->displayLocality) {
                $sql .= 'AND ISNULL(ti.occid) ';
            }
            //echo $sql;
            $result = $this->conn->query($sql);
            $this->taxon['imageCnt'] = $result->num_rows;
            $result->free();
        }
    }

    public function getTaxaMedia($tid, $mediaType, $limit, $includeAV): array
    {
        $returnArr = array();
        $returnArr['images'] = array();
        $returnArr['media'] = array();
        if($tid){
            $sql = 'SELECT t.TID, t.tidaccepted, t.SecurityStatus, t2.SecurityStatus AS accSecurityStatus '.
                'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID '.
                'WHERE t.TID = '.$this->conn->real_escape_string($tid).' ';
            //echo $sql;
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $accepted = ($row->TID === $row->tidaccepted);
                $securityStatus = $accepted ? (int)$row->SecurityStatus : (int)$row->accSecurityStatus;
                if($securityStatus === 0 || $this->teReader){
                    $this->displayLocality = true;
                }
            }
            $result->free();

            $sql = 'SELECT t.tid, t.sciname, ti.imgid, ti.url, ti.thumbnailurl, ti.originalurl, ti.caption, ti.occid, '.
                'IFNULL(ti.photographer,CONCAT_WS(" ",u.firstname,u.lastname)) AS photographer, ti.owner, o.basisOfRecord, '.
                'o.catalogNumber, o.otherCatalogNumbers '.
                'FROM images AS ti LEFT JOIN users AS u ON ti.photographeruid = u.uid '.
                'LEFT JOIN taxa AS t ON ti.tid = t.tid '.
                'LEFT JOIN omoccurrences AS o ON ti.occid = o.occid '.
                'WHERE t.tidaccepted IN(SELECT DISTINCT t.tid FROM taxa AS t LEFT JOIN taxaenumtree AS tn ON t.tid = tn.tid '.
                'WHERE t.tid = t.tidaccepted AND (tn.parenttid = '.$tid.' OR t.tid = '.$tid.')) ';
            if(!$this->displayLocality || $mediaType === 'taxon') {
                $sql .= 'AND ISNULL(ti.occid) ';
            }
            if($mediaType === 'occurrence') {
                $sql .= 'AND ti.occid IS NOT NULL ';
            }
            $sql .= 'ORDER BY ti.sortsequence ';
            if($limit){
                $sql .= 'LIMIT '.(int)$limit.' ';
            }
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $imageArr = array();
                $imgUrl = ($row->url && $GLOBALS['CLIENT_ROOT'] && strncmp($row->url, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->url) : $row->url;
                $imgThumbnail = ($row->thumbnailurl && $GLOBALS['CLIENT_ROOT'] && strncmp($row->thumbnailurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->thumbnailurl) : $row->thumbnailurl;
                $imageArr['id'] = $row->imgid;
                $imageArr['url'] = $imgUrl;
                $imageArr['thumbnailurl'] = $imgThumbnail ?: $imgUrl;
                $imageArr['photographer'] = SanitizerService::cleanOutStr($row->photographer);
                $imageArr['caption'] = SanitizerService::cleanOutStr($row->caption);
                $imageArr['occid'] = $row->occid;
                $imageArr['catalognumber'] = $row->catalogNumber;
                $imageArr['othercatalognumbers'] = $row->otherCatalogNumbers;
                $imageArr['basisofrecord'] = $row->basisOfRecord;
                $imageArr['owner'] = SanitizerService::cleanOutStr($row->owner);
                $imageArr['sciname'] = $row->sciname;
                $imageArr['tid'] = $row->tid;
                $returnArr['images'][] = $imageArr;
            }
            $result->free();

            if($includeAV){
                $sql = 'SELECT t.tid, t.sciname, m.mediaid, m.accessuri, m.title, m.creator, m.`type`, m.occid, m.format, m.owner, m.description '.
                    'FROM media AS m LEFT JOIN taxa AS t ON m.tid = t.tid '.
                    'WHERE t.tidaccepted IN(SELECT DISTINCT t.tid FROM taxa AS t LEFT JOIN taxaenumtree AS tn ON t.tid = tn.tid '.
                    'WHERE t.tid = t.tidaccepted AND (tn.parenttid = '.$tid.' OR t.tid = '.$tid.')) ';
                if($mediaType === 'taxon') {
                    $sql .= 'AND ISNULL(m.occid) ';
                }
                if($mediaType === 'occurrence') {
                    $sql .= 'AND m.occid IS NOT NULL ';
                }
                $sql .= 'ORDER BY m.sortsequence ';
                if($limit){
                    $sql .= 'LIMIT '.(int)$limit.' ';
                }
                //echo $sql;
                $result = $this->conn->query($sql);
                while($row = $result->fetch_object()){
                    $mediaArr = array();
                    $mediaArr['id'] = $row->mediaid;
                    $mediaArr['accessuri'] = ($row->accessuri && $GLOBALS['CLIENT_ROOT'] && strncmp($row->accessuri, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->accessuri) : $row->accessuri;
                    $mediaArr['title'] = $row->title;
                    $mediaArr['creator'] = $row->creator;
                    $mediaArr['type'] = $row->type;
                    $mediaArr['occid'] = $row->occid;
                    $mediaArr['format'] = $row->format;
                    $mediaArr['owner'] = $row->owner;
                    $mediaArr['description'] = $row->description;
                    $mediaArr['sciname'] = $row->sciname;
                    $mediaArr['tid'] = $row->tid;
                    $returnArr['media'][] = $mediaArr;
                }
                $result->free();
            }
        }
        return $returnArr;
    }

    public function getMapImgUrl($tid,$security): string
    {
        $map = '';
        if($tid && ($this->teReader || !$security)){
            $sql = 'SELECT tm.url, t.sciname '.
                'FROM taxamaps AS tm INNER JOIN taxa AS t ON tm.tid = t.tid '.
                'WHERE t.tid = '.$tid.' ';
            //echo $sql;
            $result = $this->conn->query($sql);
            if($row = $result->fetch_object()){
                $map = ($row->url && $GLOBALS['CLIENT_ROOT'] && strncmp($row->url, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->url) : $row->url;
            }
            $result->close();
        }
        return $map;
    }

    public function setClName($clv): void
    {
        $sql = 'SELECT c.CLID, c.Name, c.parentclid, cp.name AS parentname ' .
            'FROM fmchecklists c LEFT JOIN fmchecklists cp ON cp.clid = c.parentclid ';
        $inValue = $this->conn->real_escape_string($clv);
        if($intVal = (int)$inValue){
            $sql .= 'WHERE (c.CLID = '.$intVal.')';
        }
        else{
            $sql .= "WHERE (c.Name = '".$inValue."')";
        }
        //echo $sql;
        $result = $this->conn->query($sql);
        if($row = $result->fetch_object()){
            $this->taxon['clid'] = (int)$row->CLID;
            $this->taxon['clName'] = $row->Name;
            $this->taxon['parentClid'] = (int)$row->parentclid;
            $this->taxon['parentName'] = $row->parentname;
        }
        $result->close();
    }
}
