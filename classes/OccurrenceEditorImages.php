<?php
include_once(__DIR__ . '/OccurrenceEditorManager.php');
include_once(__DIR__ . '/ImageShared.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

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
			if($this->activeImgId) {
                $this->addImage($postArr);
            }
		}
		else{
			$status = false;
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

	public function remapImage($imgId, $targetOccid = null): bool
    {
		$status = true;
		if(!is_numeric($imgId) || !is_numeric($targetOccid)){
			return false;
		}
		if($targetOccid){
			$sql = 'UPDATE images SET occid = '.$targetOccid.' WHERE (imgid = '.$imgId.')';
			if($this->conn->query($sql)){
				$imgSql = 'UPDATE images AS i INNER JOIN omoccurrences AS o ON i.occid = o.occid '.
					'SET i.tid = o.tid WHERE i.imgid = '.$imgId.' ';
				//echo $imgSql;
				$this->conn->query($imgSql);
			}
			else{
				$this->errorArr[] = 'ERROR: Unalbe to remap image to another occurrence record.';
				$status = false;
			}
		}
		else{
			$sql = 'UPDATE images SET occid = NULL WHERE (imgid = '.$imgId.')';
			if(!$this->conn->query($sql)){
				$this->errorArr[] = 'ERROR: Unalbe to disassociate from occurrence record.';
				$status = false;
			}
		}
		return $status;
	}
	
	public function addImage($postArr): bool
    {
		$imgManager = new ImageShared();
		
		$subTargetPath = $this->collId;
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
		if(isset($this->occurrenceMap[$this->occid]['tid'])) {
            $imgManager->setTid($this->occurrenceMap[$this->occid]['tid']);
        }
		if($imgManager->processImage()){
			$this->activeImgId = $imgManager->getActiveImgId();
		}
		
		$this->errorStr = $imgManager->getErrStr();
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
				$this->photographerArr[$row->uid] = SanitizerService::cleanOutStr($row->fullname);
			}
			$result->close();
		}
		return $this->photographerArr;
	}
}
