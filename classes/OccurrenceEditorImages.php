<?php
include_once('OccurrenceEditorManager.php');
include_once('SpecProcessorOcr.php');
include_once('ImageShared.php');

class OccurrenceEditorImages extends OccurrenceEditorManager {

	private $photographerArr = array();
	private $activeImgId = 0;
    private $errorStr;
    private $imageRootUrl;
    private $imageRootPath;

    public function __construct(){
 		parent::__construct();
	}

	public function addImageOccurrence($postArr): bool
    {
		$status = true;
		if($this->addOccurrence($postArr)){
			if($this->activeImgId && $this->addImage($postArr)) {
                $rawStr = '';
                $ocrSource = '';
                if($postArr['ocrblock']){
                    $rawStr = trim($postArr['ocrblock']);
                    if($postArr['ocrsource']) {
                        $ocrSource = $postArr['ocrsource'];
                    }
                    else {
                        $ocrSource = 'User submitted';
                    }
                }
                elseif(isset($postArr['tessocr']) && $postArr['tessocr']){
                    $ocrManager = new SpecProcessorOcr();
                    $rawStr = $ocrManager->ocrImageById($this->activeImgId);
                    $ocrSource = 'Tesseract';
                }
                if($rawStr){
                    if($ocrSource) {
                        $ocrSource .= ': ' . date('Y-m-d');
                    }
                    $sql = 'INSERT INTO specprocessorrawlabels(imgid, rawstr, source) '.
                        'VALUES('.$this->activeImgId.',"'.$this->cleanInStr($rawStr).'","'.$this->cleanInStr($ocrSource).'")';
                    if(!$this->conn->query($sql)){
                        $this->errorStr = 'ERROR loading OCR text block: '.$this->conn->error;
                    }
                }
            }
		}
		else{
			$status = false;
		}
		return $status;
	}

	public function editImage(): string
    {
		global $IMAGE_DOMAIN;
		$this->setRootpaths();
		$status = 'Image editted successfully!';
		$imgId = $_REQUEST['imgid'];
	 	$url = $_REQUEST['url'];
	 	$tnUrl = $_REQUEST['tnurl'];
	 	$origUrl = $_REQUEST['origurl'];
        $oldTnUrl = '';
	 	if(array_key_exists('renameweburl',$_REQUEST)){
	 		$oldUrl = $_REQUEST['oldurl'];
	 		$oldName = str_replace($this->imageRootUrl,$this->imageRootPath,$oldUrl);
	 		$newWebName = str_replace($this->imageRootUrl,$this->imageRootPath,$url);
	 		if($url !== $oldUrl){
	 			if(file_exists($newWebName)){
 					$status = 'ERROR: unable to modify image URL because a file already exists with that name; ';
		 			$url = $oldUrl;
	 			}
	 			else if(!rename($oldName,$newWebName)){
                     $url = $oldUrl;
                     $status .= 'Web URL rename FAILED (possible write permissions issue); ';
                 }
	 		}
		}
		if(array_key_exists('renametnurl',$_REQUEST)){
	 		$oldTnUrl = $_REQUEST['oldtnurl'];
	 		$oldName = str_replace($this->imageRootUrl,$this->imageRootPath,$oldTnUrl);
	 		$newName = str_replace($this->imageRootUrl,$this->imageRootPath,$tnUrl);
	 		if($tnUrl !== $oldTnUrl){
	 			if(file_exists($newName)){
 					$status = 'ERROR: unable to modify image URL because a file already exists with that name; ';
		 			$tnUrl = $oldTnUrl;
	 			}
	 			else if(!rename($oldName,$newName)){
                     $tnUrl = $oldTnUrl;
                     $status = 'Thumbnail URL rename FAILED (possible write permissions issue); ';
                 }
	 		}
		}
		if(array_key_exists('renameorigurl',$_REQUEST)){
	 		$oldOrigUrl = $_REQUEST['oldorigurl'];
	 		$oldName = str_replace($this->imageRootUrl,$this->imageRootPath,$oldOrigUrl);
	 		$newName = str_replace($this->imageRootUrl,$this->imageRootPath,$origUrl);
	 		if($origUrl !== $oldOrigUrl){
	 			if(file_exists($newName)){
 					$status = 'ERROR: unable to modify image URL because a file already exists with that name; ';
		 			$tnUrl = $oldTnUrl;
	 			}
	 			else if(!rename($oldName,$newName)){
                     $origUrl = $oldOrigUrl;
                     $status .= 'ERROR: Thumbnail URL rename FAILED (possible write permissions issue); ';
                 }
	 		}
		}
		$occId = $_REQUEST['occid'];
		$caption = $this->cleanInStr($_REQUEST['caption']);
		$photographer = $this->cleanInStr($_REQUEST['photographer']);
		$photographerUid = (array_key_exists('photographeruid',$_REQUEST)?$_REQUEST['photographeruid']:'');
		$notes = $this->cleanInStr($_REQUEST['notes']);
		$copyRight = $this->cleanInStr($_REQUEST['copyright']);
		$sortSeq = (is_numeric($_REQUEST['sortsequence'])?$_REQUEST['sortsequence']:'');
		$sourceUrl = $this->cleanInStr($_REQUEST['sourceurl']);

		if($IMAGE_DOMAIN){
    		if(strpos($url, '/') === 0){
	    		$url = 'http://'.$_SERVER['HTTP_HOST'].$url;
    		}
    		if($tnUrl && strpos($tnUrl, '/') === 0){
	    		$tnUrl = 'http://'.$_SERVER['HTTP_HOST'].$tnUrl;
    		}
    		if($origUrl && strpos($origUrl, '/') === 0){
	    		$origUrl = 'http://'.$_SERVER['HTTP_HOST'].$origUrl;
    		}
    	}

	    $sql = 'UPDATE images '.
			'SET url = "'.$url.'", thumbnailurl = '.($tnUrl?'"'.$tnUrl.'"':'NULL').
			',originalurl = '.($origUrl?'"'.$origUrl.'"':'NULL').',occid = '.$occId.',caption = '.
			($caption?'"'.$caption.'"':'NULL').
			',photographer = '.($photographer?'"'.$photographer.'"': 'NULL').
			',photographeruid = '.($photographerUid?: 'NULL').
			',notes = '.($notes?'"'.$notes.'"':'NULL').
			($sortSeq?',sortsequence = '.$sortSeq:'').
			',copyright = '.($copyRight?'"'.$copyRight.'"':'NULL').',imagetype = "specimen",sourceurl = '.
			($sourceUrl?'"'.$sourceUrl.'"':'NULL').
			' WHERE (imgid = '.$imgId.')';
		//echo $sql;
		if($this->conn->query($sql)){
            $kArr = $this->getImageTagValues();
            foreach($kArr as $key => $description) {
                   $sql = null;
                   if (array_key_exists("ch_$key",$_REQUEST)) {
                      $sql = 'INSERT IGNORE into imagetag (imgid,keyvalue) values (?,?) ';
                   }
                   else if (array_key_exists('hidden_' .$key,$_REQUEST) && $_REQUEST['hidden_' .$key] === 1) {
                      $sql = 'DELETE from imagetag where imgid = ? and keyvalue = ? ';
                   }
                   if ($sql !== null) {
                      $stmt = $this->conn->stmt_init();
                      $stmt->prepare($sql);
                      if ($stmt) {
                         $stmt->bind_param('is',$imgId,$key);
                         if (!$stmt->execute()) {
                            $status .= " (Warning: Failed to update image tag [$key] for $imgId.  " . $stmt->error ;
                         }
                         $stmt->close();
                      }
                   }
            }
        } else { 
			$status .= 'ERROR: image not changed, ' .$this->conn->error. 'SQL: ' .$sql;
		}
		return $status;
	}

	public function deleteImage($imgIdDel, $removeImg): bool
    {
		$status = true; 
		$imgManager = new ImageShared();
		if(!$imgManager->deleteImage($imgIdDel, $removeImg)){
			$this->errorStr = implode('',$imgManager->getErrArr());
			$status = false;
		}
		return $status;
	}

	public function remapImage($imgId, $targetOccid = 0): bool
    {
		$status = true;
		if(!is_numeric($imgId) || !is_numeric($targetOccid)){
			return false;
		}
		if($targetOccid){
			$sql = 'UPDATE images SET occid = '.$targetOccid.' WHERE (imgid = '.$imgId.')';
			if($this->conn->query($sql)){
				$imgSql = 'UPDATE images i INNER JOIN omoccurrences o ON i.occid = o.occid '.
					'SET i.tid = o.tidinterpreted WHERE (i.imgid = '.$imgId.')';
				//echo $imgSql;
				$this->conn->query($imgSql);
			}
			else{
				$this->errorArr[] = 'ERROR: Unalbe to remap image to another occurrence record. Error msg: '.$this->conn->error;
				$status = false;
			}
		}
		else{
			$sql = 'UPDATE images SET occid = NULL WHERE (imgid = '.$imgId.')';
			if(!$this->conn->query($sql)){
				$this->errorArr[] = 'ERROR: Unalbe to disassociate from occurrence record. Error msg: '.$this->conn->error;
				$status = false;
			}
		}
		return $status;
	}
	
	public function addImage($postArr): bool
    {
		$imgManager = new ImageShared();
		
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
		$imgManager->setTargetPath($subTargetPath);

		if(array_key_exists('nolgimage',$postArr) && $postArr['nolgimage'] === 1){
			$imgManager->setMapLargeImg(false);
		}
		else{
			$imgManager->setMapLargeImg(true);
		}
		
		if(array_key_exists('caption',$postArr)) {
            $imgManager->setCaption($postArr['caption']);
        }
		if(array_key_exists('photographeruid',$postArr)) {
            $imgManager->setPhotographerUid($postArr['photographeruid']);
        }
		if(array_key_exists('photographer',$postArr)) {
            $imgManager->setPhotographer($postArr['photographer']);
        }
		if(array_key_exists('sourceurl',$postArr)) {
            $imgManager->setSourceUrl($postArr['sourceurl']);
        }
		if(array_key_exists('copyright',$postArr)) {
            $imgManager->setCopyright($postArr['copyright']);
        }
		if(array_key_exists('notes',$postArr)) {
            $imgManager->setNotes($postArr['notes']);
        }
		if(array_key_exists('sortsequence',$postArr)) {
            $imgManager->setSortSeq($postArr['sortsequence']);
        }

		$sourceImgUri = $postArr['imgurl'];
		if($sourceImgUri){
			if(array_key_exists('copytoserver',$postArr) && $postArr['copytoserver']){
                $imgManager->copyImageFromUrl($sourceImgUri);
			}
			else{
				$imgManager->parseUrl($sourceImgUri);
			}
		}
		else{
            $imgManager->uploadImage();
		}
		$imgManager->setOccid($this->occid);
		if(isset($this->occurrenceMap[$this->occid]['tidinterpreted'])) {
            $imgManager->setTid($this->occurrenceMap[$this->occid]['tidinterpreted']);
        }
		if($imgManager->processImage()){
			$this->activeImgId = $imgManager->getActiveImgId();
		}
		
		$status = $imgManager->insertImageTags($postArr);
		
		$this->errorStr = $imgManager->getErrStr();
		return $status;
	}
	
	private function setRootPaths(): void
    {
		global $IMAGE_ROOT_URL, $IMAGE_ROOT_PATH;
		$this->imageRootPath = $IMAGE_ROOT_PATH;
		if(substr($this->imageRootPath,-1) !== '/') {
            $this->imageRootPath .= '/';
        }
		$this->imageRootUrl = $IMAGE_ROOT_URL;
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
				$this->photographerArr[$row->uid] = $this->cleanOutStr($row->fullname);
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
            $this->photographerArr[$row->uid] = $this->cleanOutStr($row->fullname);
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
