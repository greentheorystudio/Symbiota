<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class TaxonProfileManager {

    private $submittedTid;
    private $submittedSciName;
    private $submittedAuthor;
    private $tid;
    private $sciName;
    private $author;
    private $parentTid;
    private $taxonNotes;
    private $taxonSources;
    private $ambSyn = false;
    private $acceptedName = false;
    private $rankId;
    private $language;
    private $langArr = array();
    private $synTidArr = array();
    private $securityStatus;
    private $displayLocality = 1;
    private $numChildren;

    private $clName;
    private $clid;
    private $clInfo;
    private $parentClid;
    private $parentName;
    private $pid;
    private $projName;

    private $vernaculars;
    private $synonyms;
    private $acceptedTaxa;
    private $imageArr;

    private $sppArray;

    private $con;

    public function __construct(){
        $connection = new DbConnection();
        $this->con = $connection->getConnection();
    }

    public function __destruct(){
        if($this->con) {
            $this->con->close();
        }
    }

    public function setTaxon($t,$isFinal=null): void
    {
        $t = trim($t);
        $sql = 'SELECT DISTINCT t.TID, t.family, t.SciName, t.Author, t.RankId, t.Source, t.Notes, t.parenttid, t.SecurityStatus, t.tidaccepted, t2.SciName AS synName '.
            'FROM taxa AS t LEFT JOIN taxa AS t2 ON t.tidaccepted = t2.TID ';
        if(is_numeric($t)){
            $sql .= 'WHERE (t.TID = '.$this->con->real_escape_string($t).') ';
        }
        else{
            $sql .= 'WHERE (t.SciName = "'.$this->con->real_escape_string($t).'") ';
        }
        $sql .= 'ORDER BY synName ';
        //echo $sql;
        $result = $this->con->query($sql);
        if($result->num_rows > 1){
            $this->ambSyn = true;
            while($row = $result->fetch_object()){
                if($row->TID === $row->tidaccepted){
                    $this->acceptedName = true;
                }
                $this->submittedTid = $row->TID;
                $this->submittedSciName = $row->SciName;
                $this->submittedAuthor = $row->Author;
                $this->author = $row->Author;
                $this->rankId = (int)$row->RankId;
                $this->taxonNotes = $row->Notes;
                $this->taxonSources = $row->Source;
                $this->parentTid = $row->parenttid;
                $this->securityStatus = $row->SecurityStatus;
                if($row->synName !== $row->SciName) {
                    $this->synTidArr[$row->tidaccepted] = $row->synName;
                }
            }
            $this->tid = $this->submittedTid;
            $this->sciName = $this->submittedSciName;

            if($this->rankId >= 140 && $this->rankId <= 220){
                $this->setSppData();
            }
        }
        else if ($row = $result->fetch_object()) {
            $this->submittedTid = $row->TID;
            $this->submittedSciName = $row->SciName;
            $this->submittedAuthor = $row->Author;
            $this->author = $row->Author;
            $this->rankId = (int)$row->RankId;
            $this->taxonNotes = $row->Notes;
            $this->taxonSources = $row->Source;
            $this->parentTid = $row->parenttid;
            $this->securityStatus = $row->SecurityStatus;

            if ($this->submittedTid === $row->tidaccepted) {
                $this->acceptedName = true;
                $this->tid = $this->submittedTid;
                $this->sciName = $this->submittedSciName;
            }
            else {
                $this->tid = $row->tidaccepted;
                $this->setAccepted();
            }

            if ($this->rankId >= 140 && $this->rankId <= 220) {
                $this->setSppData();
            }
        }
        else if (!$isFinal && preg_match('/^([A-Z]+[a-z]*\s+x?\s?[a-z]+)/', $t, $m)) {
            $sn = $m[1];
            if (preg_match('/\svar\.\s+([a-z]+)/', $t, $m)) {
                $sn .= ' var. ' . $m[1];
            } elseif (preg_match('/\s+(s[ub]*sp\.)\s+([a-z]+)/', $t, $m)) {
                $sn .= ' ' . $m[1] . ' ' . $m[2];
            }
            $this->setTaxon($sn, 1);
        } else {
            $this->sciName = 'unknown';
        }
        $result->close();
        $this->setSynonyms();
    }

    public function setAttributes(): void
    {
        if($this->acceptedName || ($this->acceptedTaxa && (count($this->acceptedTaxa) < 2))){
            if($this->clid) {
                $this->setChecklistInfo();
            }
            $this->setVernaculars();
            $this->setSynonyms();
        }

    }

    public function setAccepted(): void
    {
        $this->acceptedTaxa = array();
        $sql = 'SELECT t2.tid, t2.family, t2.SciName, t2.Author, t2.RankId, t2.parenttid, t2.SecurityStatus ' .
            'FROM taxa AS t INNER JOIN taxa AS t2 ON t.tidaccepted = t2.TID ' .
            'WHERE t.tid = ' .$this->submittedTid. ' ORDER BY t2.SciName';
        $result = $this->con->query($sql);
        while($row = $result->fetch_object()){
            $this->sciName = $row->SciName;
            $a = $row->Author;
            //$this->acceptedTaxa[$row->tid] = '<i>$this->sciName</i> ' . $a;
            $this->rankId = $row->RankId;
            $this->author = $a;
            $this->parentTid = $row->parenttid;
            $this->securityStatus = $row->SecurityStatus;
        }
        $result->close();
    }

    private function setChecklistInfo(): void
    {
        if($this->tid && $this->clid){
            $sql = 'SELECT Habitat, Abundance, Notes ' .
                'FROM fmchklsttaxalink  ' .
                'WHERE (tid = ' .$this->tid. ') AND (clid = ' .$this->clid. ') ';
            //echo $sql;
            $result = $this->con->query($sql);
            if($row = $result->fetch_object()){
                $info = '';
                if($row->Habitat) {
                    $info .= '; ' . $row->Habitat;
                }
                if($row->Abundance) {
                    $info .= '; ' . $row->Abundance;
                }
                if($row->Notes) {
                    $info .= '; ' . $row->Notes;
                }
                $this->clInfo = substr($info,2);
            }
            $result->free();
        }
    }

    public function getTid(){
        return $this->tid;
    }

    public function getSciName(){
        return $this->sciName;
    }

    public function getDisplayName(){
        if(!$this->sciName){
            return $this->submittedSciName;
        }

        return $this->sciName;
    }

    public function getAuthor(){
        if(!$this->author){
            return $this->submittedAuthor;
        }

        return $this->author;
    }

    public function getSubmittedTid(){
        return $this->submittedTid;
    }

    public function getSubmittedSciName(){
        return $this->submittedSciName;
    }

    public function setSppData(): void
    {
        $this->sppArray = array();
        if($this->clid){
            $sql = 'SELECT t.tid, t.sciname, t.securitystatus '.
                'FROM taxa AS t INNER JOIN taxaenumtree AS te ON t.tid = te.tid '.
                'INNER JOIN fmchklsttaxalink AS ctl ON ctl.TID = t.tid '.
                'WHERE (ctl.clid = '.$this->clid.') AND (te.parenttid = '.$this->tid.')';
        }
        elseif($this->pid){
            $sql = 'SELECT DISTINCT t.tidaccepted AS tid, t.sciname, t.securitystatus '.
                'FROM taxa AS t INNER JOIN taxaenumtree AS te ON t.tid = te.tid '.
                'INNER JOIN fmchklsttaxalink AS ctl ON t.tid = ctl.TID '.
                'INNER JOIN fmchklstprojlink AS cpl ON ctl.clid = cpl.clid '.
                'WHERE cpl.pid = '.$this->pid.' AND te.parenttid = '.$this->tid.' ';
        }
        else{
            $sql = 'SELECT DISTINCT t.sciname, t.tid, t.securitystatus '.
                'FROM taxa AS t INNER JOIN taxaenumtree AS te ON t.tid = te.tid '.
                'WHERE te.parenttid = '.$this->tid.' ';
        }
        //echo $sql; exit;

        $tids = array();
        $result = $this->con->query($sql);
        while($row = $result->fetch_object()){
            $sn = ucfirst(strtolower($row->sciname));
            $this->sppArray[$sn]['tid'] = $row->tid;
            $this->sppArray[$sn]['security'] = $row->securitystatus;
            $tids[] = $row->tid;
        }
        $result->close();
        $this->numChildren = count($tids);

        if(!$tids){
            $sql = 'SELECT DISTINCT t.sciname, t.tid, t.securitystatus '.
                'FROM taxa AS t INNER JOIN taxaenumtree AS te ON t.tidaccepted = te.tid '.
                'WHERE te.parenttid = '.$this->tid.' ';
            //echo $sql;

            $result = $this->con->query($sql);
            while($row = $result->fetch_object()){
                $sn = ucfirst(strtolower($row->sciname));
                $this->sppArray[$sn]['tid'] = $row->tid;
                $this->sppArray[$sn]['security'] = $row->securitystatus;
                $tids[] = $row->tid;
            }
            $result->free();
        }

        if($tids){
            $sql = 'SELECT t.sciname, t.tid, i.imgid, i.url, i.thumbnailurl, i.caption, '.
                'IFNULL(i.photographer,CONCAT_WS(" ",u.firstname,u.lastname)) AS photographer '.
                'FROM images AS i INNER JOIN '.
                '(SELECT t.tidaccepted AS tid, SUBSTR(MIN(CONCAT(LPAD(i.sortsequence,6,"0"),i.imgid)),7) AS imgid '.
                'FROM taxa AS t INNER JOIN images AS i ON t.tid = i.tid '.
                'WHERE (t.tidaccepted IN('.implode(',',$tids).')) '.
                'GROUP BY t.tidaccepted) AS i2 ON i.imgid = i2.imgid '.
                'INNER JOIN taxa AS t ON i2.tid = t.tid '.
                'LEFT JOIN users AS u ON i.photographeruid = u.uid ';
            //echo $sql;
            $result = $this->con->query($sql);
            while($row = $result->fetch_object()){
                $sciName = ucfirst(strtolower($row->sciname));
                if(!array_key_exists($sciName,$this->sppArray)){
                    $firstPos = strpos($sciName, ' ',2)+2;
                    $sciName = substr($sciName,0,strpos($sciName, ' ',$firstPos));
                }
                if(is_string($sciName) || is_int($sciName)){
                    $this->sppArray[$sciName]['imgid'] = $row->imgid;
                    $this->sppArray[$sciName]['url'] = $row->url;
                    $this->sppArray[$sciName]['thumbnailurl'] = $row->thumbnailurl;
                    $this->sppArray[$sciName]['photographer'] = $row->photographer;
                    $this->sppArray[$sciName]['caption'] = $row->caption;
                }
            }
            $result->close();
        }

        if($this->rankId > 140){
            foreach($this->sppArray as $sn => $snArr){
                $tid = $snArr['tid'];
                if($mapArr = $this->getMapArr($tid)){
                    $this->sppArray[$sn]['map'] = array_shift($mapArr);
                }
            }
        }
    }

    public function getSppArray(){
        return $this->sppArray;
    }

    public function setVernaculars(): void
    {
        if($this->tid){
            $this->vernaculars = array();
            $sql = 'SELECT v.vid, v.VernacularName, v.language '.
                'FROM taxavernaculars AS v INNER JOIN taxa AS t ON v.tid = t.tidaccepted '.
                'WHERE t.TID = '.$this->tid.' AND v.SortSequence < 90 '.
                'ORDER BY v.SortSequence, v.VernacularName';
            //echo $sql;
            $result = $this->con->query($sql);
            $tempVernArr = array();
            $vid = 0;
            while($row = $result->fetch_object()){
                if($vid !== $row->vid){
                    $vid = $row->vid;
                    $langStr = strtolower($row->language);
                    if(in_array($langStr, $this->langArr, true)) {
                        $this->vernaculars[] = $row->VernacularName;
                    }
                    else {
                        $tempVernArr[$langStr][] = $row->VernacularName;
                    }
                }
            }
            ksort($tempVernArr);
            foreach($tempVernArr as $lang => $vArr){
                $this->vernaculars[] = '('.$lang.': '.implode(', ',$vArr).')';
            }
            $result->free();
        }
    }

    public function getVernacularStr(){
        $str = '';
        $strArr = $this->vernaculars;
        if($strArr){
            $str = array_shift($strArr);
        }
        if($strArr){
            $str .= "<span class='verns' onclick=\"toggle('verns');\" style='cursor:pointer;display:inline;font-size:70%;' title='Click here to show more common names'>,&nbsp;&nbsp;more...</span>";
            $str .= "<span class='verns' onclick=\"toggle('verns');\" style='display:none;'>, ";
            $str .= implode(', ',$strArr);
            $str .= '</span>';
        }
        return $str;
    }

    public function getVernacularArr(){
        return $this->vernaculars;
    }

    public function setSynonyms(): void
    {
        if($this->tid){
            $this->synonyms = array();
            $sql = 'SELECT tid, SciName, Author FROM taxa '.
                'WHERE tidaccepted = '.$this->tid.' '.
                'ORDER BY SciName';
            //echo $sql;
            $result = $this->con->query($sql);
            while($row = $result->fetch_object()){
                $this->synonyms[$row->tid] = '<i>'.$row->SciName.'</i> '.$row->Author;
            }
            $result->close();
            unset($this->synonyms[$this->tid]);
        }
    }

    public function getSynonymStr(): string
    {
        $str = '';
        $cnt = 0;
        if($this->synonyms){
            foreach ($this->synonyms as $value){
                switch($cnt){
                    case 0:
                        $str = $value;
                        break;
                    case 1:
                        $str .= "<span class='syns' onclick=\"toggle('syns');\" style=\"cursor:pointer;display:inline;font-size:70%;\" title='Click here to show more synonyms'>,&nbsp;&nbsp;more...</span>";
                        $str .= "<span class='syns' onclick=\"toggle('syns');\" style=\"display:none;\">, ".$value;
                        break;
                    default:
                        $str .= ', ' .$value;
                }
                $cnt++;
            }
        }
        if($str && $cnt > 1) {
            $str .= '</span>';
        }
        return $str;
    }

    private function setTaxaImages(): void
    {
        $this->imageArr = array();
        if($this->tid){
            $tidArr = Array($this->tid);
            $sql1 = 'SELECT DISTINCT t.tid '.
                'FROM taxa AS t LEFT JOIN taxaenumtree AS tn ON t.tid = tn.tid '.
                'WHERE t.tid = t.tidaccepted '.
                'AND tn.parenttid = '.$this->tid;
            $rs1 = $this->con->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $tidArr[] = $r1->tid;
            }
            $rs1->free();

            $tidStr = implode(',',$tidArr);
            $sql = 'SELECT t.sciname, ti.imgid, ti.url, ti.thumbnailurl, ti.originalurl, ti.caption, ti.occid, '.
                'IFNULL(ti.photographer,CONCAT_WS(" ",u.firstname,u.lastname)) AS photographer, ti.owner '.
                'FROM images AS ti LEFT JOIN users AS u ON ti.photographeruid = u.uid '.
                'LEFT JOIN taxa AS t ON ti.tid = t.tid '.
                'WHERE t.tidaccepted IN('.$tidStr.') AND ti.SortSequence < 500 AND ti.thumbnailurl IS NOT NULL ';
            if(!$this->displayLocality) {
                $sql .= 'AND ISNULL(ti.occid) ';
            }
            $sql .= 'ORDER BY ti.sortsequence ';
            //echo $sql;
            $result = $this->con->query($sql);
            while($row = $result->fetch_object()){
                $imgUrl = $row->url;
                if($imgUrl === 'empty' && $row->originalurl) {
                    $imgUrl = $row->originalurl;
                }
                $this->imageArr[$row->imgid]['url'] = $imgUrl;
                $this->imageArr[$row->imgid]['thumbnailurl'] = $row->thumbnailurl;
                $this->imageArr[$row->imgid]['photographer'] = Sanitizer::cleanOutStr($row->photographer);
                $this->imageArr[$row->imgid]['caption'] = Sanitizer::cleanOutStr($row->caption);
                $this->imageArr[$row->imgid]['occid'] = $row->occid;
                $this->imageArr[$row->imgid]['owner'] = Sanitizer::cleanOutStr($row->owner);
                $this->imageArr[$row->imgid]['sciname'] = $row->sciname;
            }
            $result->free();
        }
    }

    public function echoImages($start, $length = 0, $useThumbnail = 1, $caption = 'photographer'): bool
    {
        $status = false;
        if(!isset($this->imageArr)){
            $this->setTaxaImages();
        }
        if(!$this->imageArr || count($this->imageArr) < $start) {
            return false;
        }
        $trueLength = ($length&&count($this->imageArr)>$length+$start?$length:count($this->imageArr)-$start);
        $spDisplay = $this->getDisplayName();
        $iArr = array_slice($this->imageArr,$start,$trueLength,true);
        echo '<div>';
        foreach($iArr as $imgId => $imgObj){
            if($start === 0 && $trueLength === 1){
                echo "<div id='centralimage'>";
            }
            else{
                echo "<div class='imgthumb'>";
            }
            $imgUrl = $imgObj['url'];
            $imgAnchor = '../imagelib/imgdetails.php?imgid='.$imgId;
            $imgThumbnail = $imgObj['thumbnailurl'];
            if(isset($GLOBALS['IMAGE_DOMAIN'])){
                if(strncmp($imgUrl, '/', 1) === 0) {
                    $imgUrl = $GLOBALS['IMAGE_DOMAIN'] . $imgUrl;
                }
                if(strncmp($imgThumbnail, '/', 1) === 0) {
                    $imgThumbnail = $GLOBALS['IMAGE_DOMAIN'] . $imgThumbnail;
                }
            }
            if($imgObj['occid']){
                $imgAnchor = '../collections/individual/index.php?occid='.$imgObj['occid'];
            }
            if($useThumbnail && $imgObj['thumbnailurl']) {
                $imgUrl = $imgThumbnail;
            }
            echo '<a href="'.$imgAnchor.'">';
            $titleStr = $imgObj['caption'];
            if($imgObj['sciname'] !== $this->sciName) {
                $titleStr .= ' (linked from ' . $imgObj['sciname'] . ')';
            }
            echo '<img src="'.$imgUrl.'" title="'.$titleStr.'" alt="'.$spDisplay.' image" />';
            echo '</a>';
            echo '<div class="photographer">';
            if($caption === 'photographer' && $imgObj['photographer']){
                echo $imgObj['photographer'].'&nbsp;&nbsp;';
            }
            elseif($caption === 'sciname' && $imgObj['sciname']){
                echo '<i>'.$imgObj['sciname'].'</i>&nbsp;&nbsp;';
            }
            echo '</div>';
            echo '</div>';
            $status = true;
        }
        echo '</div>';
        return $status;
    }

    public function getImageCount(): int
    {
        if(!isset($this->imageArr)) {
            return 0;
        }
        return count($this->imageArr);
    }

    public function getFilteredImageArr($type, $limit = null): array
    {
        $returnArr = array();
        if(!$limit){
            $limit = $this->getImageCount();
        }
        $count = 0;
        foreach($this->imageArr as $imgId => $imgObj){
            if($count >= $limit){
                break;
            }
            if($type === 'field' && !$imgObj['occid']){
                $returnArr[$imgId] = $imgObj;
                $count++;
            }
            if($type === 'specimen' && $imgObj['occid']){
                $returnArr[$imgId] = $imgObj;
                $count++;
            }
        }
        return $returnArr;
    }

    public function getMapArr($tidStr = null): array
    {
        $maps = array();
        if(!$tidStr){
            $tidArr = Array($this->tid,$this->submittedTid);
            if($this->synonyms) {
                $tidArr = array_merge($tidArr, array_keys($this->synonyms));
            }
            $tidStr = trim(implode(',',$tidArr),' ,');
        }
        if($tidStr){
            $sql = 'SELECT tm.url, t.sciname '.
                'FROM taxamaps tm INNER JOIN taxa t ON tm.tid = t.tid '.
                'WHERE (t.tid IN('.$tidStr.'))';
            //echo $sql;
            $result = $this->con->query($sql);
            if($row = $result->fetch_object()){
                $imgUrl = $row->url;
                if(isset($GLOBALS['IMAGE_DOMAIN']) && strncmp($imgUrl, '/', 1) === 0){
                    $imgUrl = $GLOBALS['IMAGE_DOMAIN'].$imgUrl;
                }
                $maps[] = $imgUrl;
            }
            $result->close();
        }
        return $maps;
    }

    public function getDescriptions($inlineStatements = null): array
    {
        $retArr = array();
        if($this->tid){
            $rsArr = array();
            $sql = 'SELECT t.tid, tdb.tdbid, tdb.caption, tdb.source, tdb.sourceurl, '.
                'tds.tdsid, tds.heading, tds.statement, tds.displayheader, tdb.language '.
                'FROM taxa AS t INNER JOIN taxadescrblock AS tdb ON t.tid = tdb.tid '.
                'INNER JOIN taxadescrstmts AS tds ON tdb.tdbid = tds.tdbid '.
                'WHERE t.tidaccepted = '.$this->tid.' '.
                'ORDER BY tdb.displaylevel, tds.sortsequence';
            //echo $sql; exit;
            $rs = $this->con->query($sql);
            while($r = $rs->fetch_assoc()){
                $rsArr[] = $r;
            }
            $rs->free();

            $usedCaptionArr = array();
            foreach($rsArr as $n => $rowArr){
                if($rowArr['tid'] === $this->tid){
                    $retArr = $this->loadDescriptionArr($rowArr, $retArr,$inlineStatements);
                    $usedCaptionArr[] = $rowArr['caption'];
                }
            }
            reset($rsArr);
            foreach($rsArr as $n => $rowArr){
                if($rowArr['tid'] !== $this->tid && !in_array($rowArr['caption'], $usedCaptionArr, true)){
                    $retArr = $this->loadDescriptionArr($rowArr, $retArr,$inlineStatements);
                }
            }

            ksort($retArr);
        }
        return $retArr;
    }

    private function loadDescriptionArr($rowArr,$retArr,$inlineStatements): array
    {
        $indexKey = 0;
        if(!in_array(strtolower($rowArr['language']), $this->langArr, true)){
            $indexKey = 1;
        }
        if(!isset($retArr[$indexKey]) || !array_key_exists($rowArr['tdbid'],$retArr[$indexKey])){
            $retArr[$indexKey][$rowArr['tdbid']]['caption'] = $rowArr['caption'];
            $retArr[$indexKey][$rowArr['tdbid']]['source'] = $rowArr['source'];
            $retArr[$indexKey][$rowArr['tdbid']]['url'] = $rowArr['sourceurl'];
        }
        if(strncmp($rowArr['statement'], '<p>', 3) === 0){
            $rowArr['statement'] = substr($rowArr['statement'], 3);
        }
        if($inlineStatements) {
            if(substr($rowArr['statement'], -4) === '</p>'){
                $rowArr['statement'] = substr($rowArr['statement'], 0, -4);
            }
            if($rowArr['displayheader'] && $rowArr['heading']){
                $statementStr = '<span><b>' .$rowArr['heading']. '</b>: '.$rowArr['statement'].(substr($rowArr['statement'], -7) === '</span>' ?'':'</span>');
            }
            else{
                $statementStr = '<span>'.$rowArr['statement'].(substr($rowArr['statement'], -7) === '</span>' ?'':'</span>');
            }
        }
        else if($rowArr['displayheader'] && $rowArr['heading']){
            $statementStr = '<p><b>' .$rowArr['heading']. '</b>: '.$rowArr['statement'].(substr($rowArr['statement'], -4) === '</p>' ?'':'</p>');
        }
        else{
            $statementStr = '<p>'.$rowArr['statement'].(substr($rowArr['statement'], -4) === '</p>' ?'':'</p>');
        }
        $retArr[$indexKey][$rowArr['tdbid']]['desc'][$rowArr['tdsid']] = $statementStr;
        return $retArr;
    }

    public function getGlossary(): array
    {
        $retArr = array();
        if($this->tid){
            $tidArr = $this->getParentTids();
            $tidArr[] = $this->tid;
            $sql = 'SELECT g.glossid, g.term, g.definition '.
                'FROM glossary AS g LEFT JOIN glossarytaxalink AS gt ON g.glossid = gt.glossid '.
                'WHERE gt.tid IN('.implode(',', $tidArr).') '.
                'ORDER BY g.term ';
            //echo $sql; exit;
            $rs = $this->con->query($sql);
            while($r = $rs->fetch_object()){
                $retArr[$r->glossid]['term'] = $r->term;
                $retArr[$r->glossid]['definition'] = $r->definition;
            }
            $rs->free();
        }
        return $retArr;
    }

    public function getParentTids(): array
    {
        $returnArr = array();
        $sql = 'SELECT parenttid FROM taxaenumtree ' .
            'WHERE tid = ' .$this->tid. ' ';
        $result = $this->con->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[] = $row->parenttid;
        }
        $result->close();
        return $returnArr;
    }

    public function getFamily(): string
    {
        $family = '';
        $sql = 'SELECT t.SciName ' .
            'FROM taxaenumtree AS te LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
            'WHERE te.tid = ' .$this->tid. ' AND t.RankId = 140 ';
        //echo $sql;
        $result = $this->con->query($sql);
        while($row = $result->fetch_object()){
            $family = $row->SciName;
        }
        $result->close();
        return $family;
    }

    public function getRankId(){
        return $this->rankId;
    }

    public function getTaxonNotes(){
        return $this->taxonNotes;
    }

    public function getTaxonSources(){
        return $this->taxonSources;
    }

    public function getParentTid(){
        return $this->parentTid;
    }

    public function getAmbSyn(): bool
    {
        return $this->ambSyn;
    }

    public function getAcceptance(): bool
    {
        return $this->acceptedName;
    }

    public function getSynonymArr(): array
    {
        return $this->synTidArr;
    }

    public function getNumChildren(){
        return $this->numChildren;
    }

    public function getSecurityStatus(){
        return $this->securityStatus;
    }

    public function setDisplayLocality($dl): void
    {
        $this->displayLocality = $dl;
    }

    public function setClName($clv): void
    {
        $sql = 'SELECT c.CLID, c.Name, c.parentclid, cp.name AS parentname ' .
            'FROM fmchecklists c LEFT JOIN fmchecklists cp ON cp.clid = c.parentclid ';
        $inValue = $this->con->real_escape_string($clv);
        if($intVal = (int)$inValue){
            $sql .= 'WHERE (c.CLID = '.$intVal.')';
        }
        else{
            $sql .= "WHERE (c.Name = '".$inValue."')";
        }
        //echo $sql;
        $result = $this->con->query($sql);
        if($row = $result->fetch_object()){
            $this->clid = $row->CLID;
            $this->clName = $row->Name;
            $this->parentClid = $row->parentclid;
            $this->parentName = $row->parentname;
        }
        $result->close();
    }

    public function getClid(){
        return $this->clid;
    }

    public function getClName(){
        return $this->clName;
    }

    public function getParentClid(){
        return $this->parentClid;
    }

    public function getParentName(){
        return $this->parentName;
    }

    public function setProj($p): void
    {
        if(is_numeric($p)){
            $this->pid = $this->con->real_escape_string($p);
            $sql = 'SELECT p.projname FROM fmprojects p WHERE (p.pid = '.$this->con->real_escape_string($p).')';
            $rs = $this->con->query($sql);
            if($row = $rs->fetch_object()){
                $this->projName = $row->projname;
            }
            $rs->close();
        }
        else{
            $this->projName = $p;
            $sql = 'SELECT p.pid FROM fmprojects p WHERE (p.projname = "'.$this->con->real_escape_string($p).'")';
            $rs = $this->con->query($sql);
            if($row = $rs->fetch_object()){
                $this->pid = $row->pid;
            }
            $rs->close();
        }
    }

    public function getProjName(){
        return $this->projName;
    }

    public function setLanguage($lang): void
    {
        $lang = strtolower($lang);
        if($lang === 'en' || $lang === 'english') {
            $this->langArr = array('en', 'english');
        }
        elseif($lang === 'es' || $lang === 'spanish') {
            $this->langArr = array('es', 'spanish', 'espanol');
        }
        elseif($lang === 'fr' || $lang === 'french') {
            $this->langArr = array('fr', 'french');
        }
    }

    public function getCloseTaxaMatches($testValue): array
    {
        $retArr = array();
        $value = $this->con->real_escape_string($testValue);
        $sql = 'SELECT tid, sciname FROM taxa WHERE soundex(sciname) = soundex("'.$value.'")';
        if($rs = $this->con->query($sql)){
            while($r = $rs->fetch_object()){
                if($testValue !== $r->sciname) {
                    $retArr[$r->tid] = $r->sciname;
                }
            }
        }
        return $retArr;
    }
}
