<?php
include_once(__DIR__ . '/OccurrenceEditorManager.php');
include_once(__DIR__ . '/SpecProcessorOcr.php');
include_once(__DIR__ . '/MediaShared.php');
include_once(__DIR__ . '/Sanitizer.php');

class OccurrenceEditorMedia extends OccurrenceEditorManager {

	private $photographerArr = array();
	private $activeImgId = 0;
    private $errorStr;
    private $imageRootUrl;
    private $imageRootPath;

    public function __construct(){
 		parent::__construct();
	}

	public function editMedia(): string
    {
		$this->setRootpaths();
		$status = 'Media editted successfully!';
		$medId = $_REQUEST['medid'];
	 	$url = $_REQUEST['accessuri'];
	 	$occId = $_REQUEST['occid'];
        $tId = $this->occurrenceMap[$this->occid]['tidinterpreted'];
		$title = Sanitizer::cleanInStr($_REQUEST['title']);
		$creator = Sanitizer::cleanInStr($_REQUEST['creator']);
		$creatoruid = (array_key_exists('creatoruid',$_REQUEST)?(int)$_REQUEST['creatoruid']:'');
		$description = Sanitizer::cleanInStr($_REQUEST['description']);
		$locationcreated = Sanitizer::cleanInStr($_REQUEST['locationcreated']);
        $language = Sanitizer::cleanInStr($_REQUEST['language']);
        $type = Sanitizer::cleanInStr($_REQUEST['type']);
        $format = Sanitizer::cleanInStr($_REQUEST['format']);
        $usageterms = Sanitizer::cleanInStr($_REQUEST['usageterms']);
        $rights = Sanitizer::cleanInStr($_REQUEST['rights']);
        $owner = Sanitizer::cleanInStr($_REQUEST['owner']);
        $publisher = Sanitizer::cleanInStr($_REQUEST['publisher']);
        $contributor = Sanitizer::cleanInStr($_REQUEST['contributor']);
        $bibliographiccitation = Sanitizer::cleanInStr($_REQUEST['bibliographiccitation']);
        $furtherinformationurl = Sanitizer::cleanInStr($_REQUEST['furtherinformationurl']);
		$sortsequence = (is_numeric($_REQUEST['sortsequence'])?(int)$_REQUEST['sortsequence']:'');

		if($GLOBALS['IMAGE_DOMAIN'] && strncmp($url, '/', 1) === 0) {
            $url = 'http://'.$_SERVER['HTTP_HOST'].$url;
        }

	    $sql = 'UPDATE media '.
			'SET accessuri = "'.$url.'", occid = '.$occId.', tid = '.($tId ?: 'NULL').','.
            'title = '.($title?'"'.$title.'"':'NULL').','.
			'creator = '.($creator?'"'.$creator.'"': 'NULL').','.
			'creatoruid = '.($creatoruid?: 'NULL').','.
			'description = '.($description?'"'.$description.'"':'NULL').','.
            'locationcreated = '.($locationcreated?'"'.$locationcreated.'"':'NULL').','.
            'language = '.($language?'"'.$language.'"':'NULL').','.
            'type = '.($type?'"'.$type.'"':'NULL').','.
            'format = '.($format?'"'.$format.'"':'NULL').','.
            'usageterms = '.($usageterms?'"'.$usageterms.'"':'NULL').','.
            'rights = '.($rights?'"'.$rights.'"':'NULL').','.
            'owner = '.($owner?'"'.$owner.'"':'NULL').','.
            'publisher = '.($publisher?'"'.$publisher.'"':'NULL').','.
            'contributor = '.($contributor?'"'.$contributor.'"':'NULL').','.
            'bibliographiccitation = '.($bibliographiccitation?'"'.$bibliographiccitation.'"':'NULL').','.
            'furtherinformationurl = '.($furtherinformationurl?'"'.$furtherinformationurl.'"':'NULL').','.
            'sortsequence = '.($sortsequence ?: 'NULL').' '.
			'WHERE (mediaid = '.$medId.')';
		//echo $sql;
		if(!$this->conn->query($sql)){
            $status = 'ERROR: media data not changed.';
        }
        return $status;
	}

	public function deleteMedia($medIdDel, $removeImg): bool
    {
		$status = true; 
		$medManager = new MediaShared();
		if(!$medManager->deleteMedia($medIdDel, $removeImg)){
			$this->errorStr = implode('',$medManager->getErrArr());
			$status = false;
		}
		return $status;
	}

	public function remapMedia($medId, $targetOccid = null): bool
    {
		$status = true;
		if(!is_numeric($medId) || !is_numeric($targetOccid)){
			return false;
		}
		if($targetOccid){
			$sql = 'UPDATE media SET occid = '.$targetOccid.' WHERE (mediaid = '.$medId.')';
			if($this->conn->query($sql)){
				$imgSql = 'UPDATE media AS m INNER JOIN omoccurrences AS o ON m.occid = o.occid '.
					'SET m.tid = o.tidinterpreted WHERE (m.mediaid = '.$medId.')';
				//echo $imgSql;
				$this->conn->query($imgSql);
			}
			else{
				$this->errorArr[] = 'ERROR: Unalbe to remap media to another occurrence record.';
				$status = false;
			}
		}
		else{
			$sql = 'UPDATE media SET occid = NULL WHERE (mediaid = '.$medId.')';
			if(!$this->conn->query($sql)){
				$this->errorArr[] = 'ERROR: Unalbe to disassociate from occurrence record.';
				$status = false;
			}
		}
		return $status;
	}
	
	public function addMedia($postArr): bool
    {
		$medManager = new MediaShared();
		
		$subTargetPath = $this->collMap['institutioncode'];
		if($this->collMap['collectioncode']) {
            $subTargetPath .= '_' . $this->collMap['collectioncode'];
        }
		$subTargetPath .= '/';
		if(!$this->occurrenceMap) {
            $this->setOccurArr();
        }
		$catNum = $this->occurrenceMap[$this->occid]['catalognumber'];
		if($catNum){
			$catNum = str_replace(array('/','\\',' '), '', $catNum);
			if(preg_match('/^(\D{0,8}\d{4,})/', $catNum, $m)){
				$catPath = substr($m[1], 0, -3);
				if(is_numeric($catPath) && strlen($catPath)<5) {
                    $catPath = str_pad($catPath, 5, '0', STR_PAD_LEFT);
                }
				$subTargetPath .= $catPath.'/';
			}
			else{
				$subTargetPath .= '00000/';
			}
		}
		else{
			$subTargetPath .= date('Ym').'/';
		}
		$medManager->setTargetPath($subTargetPath);

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

		$sourceMedUri = $postArr['accessuri'];
		if($sourceMedUri){
            $medManager->parseUrl($sourceMedUri);
		}
		else{
            $medManager->uploadMedia();
		}
		$medManager->setOccid($this->occid);
		if(isset($this->occurrenceMap[$this->occid]['tidinterpreted'])) {
            $medManager->setTid($this->occurrenceMap[$this->occid]['tidinterpreted']);
        }
		if($medManager->processMedia()){
			$this->activeImgId = $medManager->getActiveMedId();
		}
		
		$this->errorStr = $medManager->getErrStr();
		return true;
	}
	
	private function setRootPaths(): void
    {
		$this->imageRootPath = $GLOBALS['IMAGE_ROOT_PATH'];
		if(substr($this->imageRootPath,-1) !== '/') {
            $this->imageRootPath .= '/';
        }
		$this->imageRootUrl = $GLOBALS['IMAGE_ROOT_URL'];
		if(substr($this->imageRootUrl,-1) !== '/') {
            $this->imageRootUrl .= '/';
        }
	}

	public function getPhotographerArr(): array
    {
		if(!$this->photographerArr){
			$sql = "SELECT u.uid, CONCAT_WS(', ',u.lastname,u.firstname) AS fullname ".
                'FROM users u ORDER BY u.lastname, u.firstname ';
			$result = $this->conn->query($sql);
			while($row = $result->fetch_object()){
				$this->photographerArr[$row->uid] = Sanitizer::cleanOutStr($row->fullname);
			}
			$result->close();
		}
		return $this->photographerArr;
	}

    public function getImageTagValues(): array
    {
        $returnArr = array();
        $sql = 'SELECT tagkey, description_en FROM imagetagkey ORDER BY sortorder ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[$row->tagkey] = $row->description_en;
        }
        $result->close();
       return $returnArr;
    } 

    public function getImageTagUsage($imgid): array
    {
        $resultArr = array();
        $imageTagArr = array();
        $sql = 'SELECT k.tagkey '.
            'FROM imagetagkey AS k LEFT JOIN imagetag AS i ON k.tagkey = i.keyvalue '.
            'WHERE i.imgid = '.$imgid.' ';
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $imageTagArr[] = $row->tagkey;
        }

        $sql = 'SELECT tagkey, description_en, shortlabel, sortorder '.
            'FROM imagetagkey ORDER BY sortorder ';
        $result = $this->conn->query($sql);
        $i = 0;
        while($row = $result->fetch_object()){
            $resultArr[$i]['tagkey'] = $row->tagkey;
            $resultArr[$i]['shortlabel'] = $row->shortlabel;
            $resultArr[$i]['description'] = $row->description_en;
            $resultArr[$i]['sortorder'] = $row->sortorder;
            $resultArr[$i]['value'] = (in_array($row->tagkey, $imageTagArr, true)?1:0);
            $i++;
        }
        $result->close();
        return $resultArr;
    }
}
