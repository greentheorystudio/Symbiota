<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class TaxonProfileManager {

    private $conn;
    private $taxon = array();
    private $displayLocality = false;
    private $teReader = false;

    public function __construct(){
        $connection = new DbConnection();
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
            $sql .= 'WHERE (t.TID = '.$this->conn->real_escape_string($t).') ';
        }
        else{
            $sql .= 'WHERE (t.SciName = "'.$this->conn->real_escape_string($t).'") ';
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
            $this->taxon['vernaculars'] = array();
            $this->taxon['synonyms'] = array();
            $this->taxon['images'] = array();
            $this->taxon['imageCnt'] = array();
            $this->taxon['media'] = array();
            $this->taxon['map'] = $this->getMapImgUrl($this->taxon['tid'],$this->taxon['securityStatus']);
            if($clId){
                $this->setClName($clId);
            }
            if($this->taxon['submittedTid'] === $this->taxon['tid']){
                $this->setVernaculars();
                $this->setSynonyms();
                $this->setTaxaMedia();
                $this->taxon['sppArr'] = array();
                $this->setSppData($clId);
            }
        }

        return $this->taxon;
    }

    public function setSppData($clId): void
    {
        $tids = array();
        if($clId){
            $sql = 'SELECT t.tid, t.RankId, t.sciname, t.securitystatus '.
                'FROM taxa AS t INNER JOIN fmchklsttaxalink AS ctl ON ctl.TID = t.tid '.
                'WHERE ctl.clid = '.$clId.' AND t.parenttid = '.$this->taxon['tid'].' ';
        }
        else{
            $sql = 'SELECT DISTINCT t.sciname, t.RankId, t.tid, t.securitystatus '.
                'FROM taxa AS t WHERE t.parenttid = '.$this->taxon['tid'].' ';
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
            $sql = 'SELECT t.sciname, i.url, i.thumbnailurl, i.caption '.
                'FROM images AS i INNER JOIN '.
                '(SELECT t.tidaccepted AS tid, SUBSTR(MIN(CONCAT(LPAD(i.sortsequence,6,"0"),i.imgid)),7) AS imgid '.
                'FROM taxa AS t INNER JOIN images AS i ON t.tid = i.tid '.
                'WHERE (t.tidaccepted IN('.implode(',',$tids).')) '.
                'GROUP BY t.tidaccepted) AS i2 ON i.imgid = i2.imgid '.
                'INNER JOIN taxa AS t ON i2.tid = t.tid ';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $sciName = $row->sciname;
                if(array_key_exists($sciName,$this->taxon['sppArr'])){
                    $imgUrl = $row->thumbnailurl ?: $row->url;
                    if(strncmp($imgUrl, '/', 1) === 0) {
                        if(isset($GLOBALS['IMAGE_DOMAIN'])){
                            $imgUrl = $GLOBALS['IMAGE_DOMAIN'] . $imgUrl;
                        }
                        else{
                            $imgUrl = $GLOBALS['CLIENT_ROOT'] . $imgUrl;
                        }
                    }
                    $this->taxon['sppArr'][$sciName]['url'] = $imgUrl;
                    $this->taxon['sppArr'][$sciName]['caption'] = $row->caption;
                }
            }
            $result->close();
        }
        if($this->taxon['rankId'] > 140){
            foreach($this->taxon['sppArr'] as $sn => $snArr){
                $this->taxon['sppArr'][$sn]['map'] = $this->getMapImgUrl((int)$snArr['tid'],(int)$snArr['security']);
            }
        }
    }

    public function setVernaculars(): void
    {
        if($this->taxon['tid']){
            $sql = 'SELECT v.VernacularName, v.`language`, l.iso639_1 '.
                'FROM taxavernaculars AS v INNER JOIN taxa AS t ON v.tid = t.tidaccepted '.
                'LEFT JOIN adminlanguages AS l ON v.`language` = l.langname '.
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

    private function setTaxaMedia(): void
    {
        if($this->taxon['tid']){
            $tidArr = array($this->taxon['tid']);
            $sql1 = 'SELECT DISTINCT t.tid '.
                'FROM taxa AS t LEFT JOIN taxaenumtree AS tn ON t.tid = tn.tid '.
                'WHERE t.tid = t.tidaccepted '.
                'AND tn.parenttid = '.$this->taxon['tid'];
            $rs1 = $this->conn->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $tidArr[] = $r1->tid;
            }
            $rs1->free();

            $tidStr = implode(',',$tidArr);
            $sql = 'SELECT t.sciname, ti.imgid, ti.url, ti.thumbnailurl, ti.originalurl, ti.caption, ti.occid, '.
                'IFNULL(ti.photographer,CONCAT_WS(" ",u.firstname,u.lastname)) AS photographer, ti.owner '.
                'FROM images AS ti LEFT JOIN users AS u ON ti.photographeruid = u.uid '.
                'LEFT JOIN taxa AS t ON ti.tid = t.tid '.
                'WHERE t.tidaccepted IN('.$tidStr.') ';
            if(!$this->displayLocality) {
                $sql .= 'AND ISNULL(ti.occid) ';
            }
            $sql .= 'ORDER BY ti.sortsequence ';
            //echo $sql;
            $result = $this->conn->query($sql);
            $this->taxon['imageCnt'] = $result->num_rows;
            $imgCnt = 0;
            while(($row = $result->fetch_object()) && $imgCnt <= 100){
                $imageArr = array();
                $imgUrl = $row->url;
                $imgThumbnail = $row->thumbnailurl;
                if(strncmp($imgUrl, '/', 1) === 0) {
                    if(isset($GLOBALS['IMAGE_DOMAIN'])){
                        $imgUrl = $GLOBALS['IMAGE_DOMAIN'] . $imgUrl;
                    }
                    else{
                        $imgUrl = $GLOBALS['CLIENT_ROOT'] . $imgUrl;
                    }
                }
                if(strncmp($imgThumbnail, '/', 1) === 0) {
                    if(isset($GLOBALS['IMAGE_DOMAIN'])){
                        $imgThumbnail = $GLOBALS['IMAGE_DOMAIN'] . $imgThumbnail;
                    }
                    else{
                        $imgThumbnail = $GLOBALS['CLIENT_ROOT'] . $imgThumbnail;
                    }
                }
                $imageArr['id'] = $row->imgid;
                $imageArr['url'] = $imgUrl;
                $imageArr['thumbnailurl'] = $imgThumbnail ?: $imgUrl;
                $imageArr['photographer'] = Sanitizer::cleanOutStr($row->photographer);
                $imageArr['caption'] = Sanitizer::cleanOutStr($row->caption);
                $imageArr['occid'] = $row->occid;
                $imageArr['owner'] = Sanitizer::cleanOutStr($row->owner);
                $imageArr['sciname'] = $row->sciname;
                $this->taxon['images'][] = $imageArr;
                $imgCnt++;
            }
            $result->free();

            $sql = 'SELECT t.sciname, m.mediaid, m.accessuri, m.title, m.creator, m.`type`, m.occid, m.format, m.owner, m.description '.
                'FROM media AS m LEFT JOIN taxa AS t ON m.tid = t.tid '.
                'WHERE t.tidaccepted IN('.$tidStr.') '.
                'ORDER BY m.sortsequence ';
            //echo $sql;
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $mediaArr = array();
                $mediaArr['id'] = $row->mediaid;
                $mediaArr['accessuri'] = $row->accessuri;
                $mediaArr['title'] = $row->title;
                $mediaArr['creator'] = $row->creator;
                $mediaArr['type'] = $row->type;
                $mediaArr['occid'] = $row->occid;
                $mediaArr['format'] = $row->format;
                $mediaArr['owner'] = $row->owner;
                $mediaArr['description'] = $row->description;
                $mediaArr['sciname'] = $row->sciname;
                $this->taxon['media'][] = $mediaArr;
            }
            $result->free();
        }
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
                $map = $row->url;
                if(strncmp($map, '/', 1) === 0){
                    if(isset($GLOBALS['IMAGE_DOMAIN'])){
                        $map = $GLOBALS['IMAGE_DOMAIN'] . $map;
                    }
                    else{
                        $map = $GLOBALS['CLIENT_ROOT'] . $map;
                    }
                }
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
