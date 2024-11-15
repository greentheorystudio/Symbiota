<?php
include_once(__DIR__ . '/TPEditorManager.php');
include_once(__DIR__ . '/ImageShared.php');
include_once(__DIR__ . '/MediaShared.php');

class TPImageEditorManager extends TPEditorManager{

    private $imageArr = array();

    public function __construct(){
        parent::__construct();
        set_time_limit(120);
        ini_set('max_input_time',120);
    }

    public function getImages(): array
    {
        $imageArr = array();
        $tidArr = Array($this->tid);
        if($this->rankId === 220){
            $sql1 = 'SELECT DISTINCT tid FROM taxa '.
                'WHERE tid = tidaccepted AND parenttid = '.$this->tid.' ';
            $rs1 = $this->taxonCon->query($sql1);
            while($r1 = $rs1->fetch_object()){
                $tidArr[] = $r1->tid;
            }
            $rs1->free();
        }

        $tidStr = implode(',',$tidArr);
        $this->imageArr = array();
        $sql = 'SELECT ti.imgid, ti.url, ti.thumbnailurl, ti.originalurl, ti.caption, ti.photographer, ti.photographeruid, '.
            'IFNULL(ti.photographer,CONCAT_WS(" ",u.firstname,u.lastname)) AS photographerdisplay, ti.owner, '.
            'ti.locality, ti.occid, ti.notes, ti.sortsequence, ti.sourceurl, ti.copyright, t.tid, t.sciname '.
            'FROM (images AS ti LEFT JOIN users AS u ON ti.photographeruid = u.uid) '.
            'INNER JOIN taxa AS t ON ti.tid = t.tid '.
            'WHERE t.tidaccepted IN('.$tidStr.') AND ti.SortSequence < 500 '.
            'ORDER BY ti.sortsequence';
        //echo $sql; exit;
        $result = $this->taxonCon->query($sql);
        $imgCnt = 0;
        while($row = $result->fetch_object()){
            $imageArr[$imgCnt]['imgid'] = $row->imgid;
            $imageArr[$imgCnt]['url'] = ($row->url && $GLOBALS['CLIENT_ROOT'] && strncmp($row->url, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->url) : $row->url;
            $imageArr[$imgCnt]['thumbnailurl'] = ($row->thumbnailurl && $GLOBALS['CLIENT_ROOT'] && strncmp($row->thumbnailurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->thumbnailurl) : $row->thumbnailurl;
            $imageArr[$imgCnt]['originalurl'] = ($row->originalurl && $GLOBALS['CLIENT_ROOT'] && strncmp($row->originalurl, '/', 1) === 0) ? ($GLOBALS['CLIENT_ROOT'] . $row->originalurl) : $row->originalurl;
            $imageArr[$imgCnt]['photographer'] = $row->photographer;
            $imageArr[$imgCnt]['photographeruid'] = $row->photographeruid;
            $imageArr[$imgCnt]['photographerdisplay'] = $row->photographerdisplay;
            $imageArr[$imgCnt]['caption'] = $row->caption;
            $imageArr[$imgCnt]['owner'] = $row->owner;
            $imageArr[$imgCnt]['locality'] = $row->locality;
            $imageArr[$imgCnt]['sourceurl'] = $row->sourceurl;
            $imageArr[$imgCnt]['copyright'] = $row->copyright;
            $imageArr[$imgCnt]['occid'] = $row->occid;
            $imageArr[$imgCnt]['notes'] = $row->notes;
            $imageArr[$imgCnt]['tid'] = $row->tid;
            $imageArr[$imgCnt]['sciname'] = $row->sciname;
            $imageArr[$imgCnt]['sortsequence'] = $row->sortsequence;
            $imgCnt++;
        }
        $result->close();
        return $imageArr;
    }

    public function echoPhotographerSelect($userId = null): void
    {
        $sql = "SELECT u.uid, CONCAT_WS(', ',u.lastname,u.firstname) AS fullname ".
            'FROM users AS u ORDER BY u.lastname, u.firstname ';
        $result = $this->taxonCon->query($sql);
        while($row = $result->fetch_object()){
            echo "<option value='".$row->uid."' ".($row->uid === $userId? 'SELECTED' : ''). '>' .$row->fullname."</option>\n";
        }
        $result->close();
    }

    public function editImageSort($imgSortEdits): string
    {
        $status = '';
        foreach($imgSortEdits as $editKey => $editValue){
            if(is_numeric($editKey) && is_numeric($editValue)){
                $sql = 'UPDATE images SET sortsequence = '.$editValue.' WHERE imgid = '.$editKey;
                //echo $sql;
                if(!$this->taxonCon->query($sql)){
                    $status .= 'Error editing image.<br />';
                }
            }
        }
        if($status) {
            $status = 'with editImageSort method: ' . $status;
        }
        return $status;
    }

    public function loadImage($postArr): bool
    {
        $tid = ($this->tid?:$postArr['tid']);
        $status = true;
        $imgManager = new ImageShared();

        $imgPath = $postArr['filepath'] ?? null;

        $imgManager->setTid($tid);
        $imgManager->setCaption($postArr['caption'] ?? null);
        $imgManager->setPhotographer($postArr['photographer'] ?? null);
        $imgManager->setPhotographerUid($postArr['photographeruid'] ?? null);
        $imgManager->setSourceUrl($postArr['sourceurl'] ?? null);
        $imgManager->setCopyright($postArr['copyright'] ?? null);
        $imgManager->setOwner($postArr['owner'] ?? null);
        $imgManager->setLocality($postArr['locality'] ?? null);
        $imgManager->setOccid($postArr['occid'] ?? null);
        $imgManager->setNotes($postArr['notes'] ?? null);
        $sort = $postArr['sortsequence'] ?? null;
        if(!$sort) {
            $sort = 40;
        }
        $imgManager->setSortSeq($sort);

        $imgManager->setTargetPath(($this->family?$this->family.'/':'').date('Ym').'/');
        if($imgPath){
            $imgManager->setMapLargeImg(true);
            $importUrl = ((array_key_exists('importurl', $postArr) && $postArr['importurl'] === 1));
            if($importUrl){
                $imgManager->copyImageFromUrl($imgPath);
            }
            else{
                $imgManager->parseUrl($imgPath);
            }
        }
        else{
            $imgManager->uploadImage();
        }
        if(array_key_exists('createlargeimg',$postArr) && (int)$postArr['createlargeimg'] === 1){
            $imgManager->setMapLargeImg(true);
        }
        else{
            $imgManager->setMapLargeImg(false);
        }
        $imgManager->processImage();
        if($imgManager->getErrArr()){
            $this->errorStr = implode('<br/>',$imgManager->getErrArr());
        }
        return $status;
    }

    public function loadMedia($postArr): bool
    {
        $medManager = new MediaShared();
        $medManager->setTargetPath(($this->family?$this->family.'/':'').date('Ym').'/');

        if(array_key_exists('title',$postArr)) {
            $medManager->setTitle($postArr['title']);
        }
        if(array_key_exists('creatoruid',$postArr)) {
            $medManager->setCreatorUid($postArr['creatoruid']);
        }
        if(array_key_exists('creator',$postArr)) {
            $medManager->setCreator($postArr['creator']);
        }
        if(array_key_exists('description',$postArr)) {
            $medManager->setDescription($postArr['description']);
        }
        if(array_key_exists('locationcreated',$postArr)) {
            $medManager->setLocationCreated($postArr['locationcreated']);
        }
        if(array_key_exists('language',$postArr)) {
            $medManager->setLanguage($postArr['language']);
        }
        if(array_key_exists('type',$postArr)) {
            $medManager->setType($postArr['type']);
        }
        if(array_key_exists('format',$postArr)) {
            $medManager->setFormat($postArr['format']);
        }
        if(array_key_exists('usageterms',$postArr)) {
            $medManager->setUsageTerms($postArr['usageterms']);
        }
        if(array_key_exists('rights',$postArr)) {
            $medManager->setRights($postArr['rights']);
        }
        if(array_key_exists('owner',$postArr)) {
            $medManager->setOwner($postArr['owner']);
        }
        if(array_key_exists('publisher',$postArr)) {
            $medManager->setPublisher($postArr['publisher']);
        }
        if(array_key_exists('contributor',$postArr)) {
            $medManager->setContributor($postArr['contributor']);
        }
        if(array_key_exists('bibliographiccitation',$postArr)) {
            $medManager->setBibliographicCitation($postArr['bibliographiccitation']);
        }
        if(array_key_exists('furtherinformationurl',$postArr)) {
            $medManager->setFurtherInformationURL($postArr['furtherinformationurl']);
        }
        if(array_key_exists('sortsequence',$postArr)) {
            $medManager->setSortSequence($postArr['sortsequence']);
        }

        $sourceMedUri = array_key_exists('accessuri',$postArr) ? $postArr['accessuri'] : null;
        if($sourceMedUri){
            $medManager->parseUrl($sourceMedUri);
        }
        else{
            $medManager->uploadMedia();
        }
        $medManager->setTid($postArr['tid']);
        $medManager->processMedia();

        $this->errorStr = $medManager->getErrStr();
        return true;
    }
}
