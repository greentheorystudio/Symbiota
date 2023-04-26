<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/UuidFactory.php');
include_once(__DIR__ . '/Sanitizer.php');

class ImageShared{

	private $conn;
	private $sourceGdImg;

	private $sourcePath = '';
	private $targetPath = '';
	private $urlBase = '';
	private $imgName = '';
	private $imgExt = '';
	private $imageRootPath;
	private $imageRootUrl;

	private $sourceWidth = 0;
	private $sourceHeight = 0;
	private $sourceFileSize = 0;

	private $tnPixWidth = 200;
	private $webPixWidth = 1600;
	private $lgPixWidth = 3168;
	private $webFileSizeLimit = 300000;
	private $jpgCompression= 70;

	private $mapLargeImg = true;

	private $caption;
	private $photographer;
	private $photographerUid;
	private $sourceUrl;
	private $format;
	private $owner;
	private $locality;
	private $occid;
	private $tid;
	private $sourceIdentifier;
	private $rights;
	private $accessRights;
	private $copyright;
	private $notes;
	private $sortSeq;
	private $context;

	private $activeImgId = 0;

	private $errArr = array();

	public function __construct(){
		$connection = new DbConnection();
 		$this->conn = $connection->getConnection();
 		$this->imageRootPath = $GLOBALS['IMAGE_ROOT_PATH'] ?? '';
		if(substr($this->imageRootPath,-1) !== '/') {
			$this->imageRootPath .= '/';
		}
		$this->imageRootUrl = $GLOBALS['IMAGE_ROOT_URL'] ?? '';
		if(substr($this->imageRootUrl,-1) !== '/') {
			$this->imageRootUrl .= '/';
		}
		if(isset($GLOBALS['IMG_TN_WIDTH'])){
			$this->tnPixWidth = $GLOBALS['IMG_TN_WIDTH'];
		}
		if(isset($GLOBALS['IMG_WEB_WIDTH'])){
			$this->webPixWidth = $GLOBALS['IMG_WEB_WIDTH'];
		}
		if(isset($GLOBALS['IMG_LG_WIDTH'])){
			$this->lgPixWidth = $GLOBALS['IMG_LG_WIDTH'];
		}
		if(isset($GLOBALS['MAX_UPLOAD_FILESIZE'])){
			$this->webFileSizeLimit = $GLOBALS['MAX_UPLOAD_FILESIZE'];
		}
		ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
		$opts = array(
			'http'=>array(
				'user_agent' => $GLOBALS['DEFAULT_TITLE'],
				'method'=> 'GET',
				'header'=> implode("\r\n", array('Content-type: text/plain;'))
			)
		);
		$this->context = stream_context_create($opts);
		ini_set('memory_limit','512M');
 	}

	public function __destruct(){
		if($this->sourceGdImg) {
			imagedestroy($this->sourceGdImg);
		}
		if($this->conn){
			$this->conn->close();
			$this->conn = null;
		}
	}

	public function reset(): void
	{
 		if($this->sourceGdImg) {
			imagedestroy($this->sourceGdImg);
		}
 		$this->sourceGdImg = null;

 		$this->sourcePath = '';
		$this->imgName = '';
		$this->imgExt = '';

		$this->sourceWidth = 0;
		$this->sourceHeight = 0;

		$this->caption = '';
		$this->photographer = '';
		$this->photographerUid = '';
		$this->sourceUrl = '';
		$this->format = '';
		$this->owner = '';
		$this->locality = '';
		$this->occid = '';
		$this->tid = '';
		$this->sourceIdentifier = '';
		$this->rights = '';
		$this->accessRights = '';
		$this->copyright = '';
		$this->notes = '';
		$this->sortSeq = '';

		$this->activeImgId = 0;

		unset($this->errArr);
		$this->errArr = array();

 	}

	public function uploadImage($imgFile = null): bool
	{
		if(!$imgFile){
            $imgFile = 'imgfile';
        }
	    if($this->targetPath){
			if(file_exists($this->targetPath)){
				$imgFileName = basename($_FILES[$imgFile]['name']);
				$fileName = $this->cleanFileName($imgFileName);
				if(is_writable($this->targetPath) && move_uploaded_file($_FILES[$imgFile]['tmp_name'], $this->targetPath . $fileName . $this->imgExt)) {
                    $this->sourcePath = $this->targetPath.$fileName.$this->imgExt;
                    $this->imgName = $fileName;
                    return true;
                }

				$this->errArr[] = 'FATAL ERROR: unable to move image to target ('.$this->targetPath.$fileName.$this->imgExt.')';
			}
			else{
				$this->errArr[] = 'FATAL ERROR: Target path does not exist in uploadImage method ('.$this->targetPath.')';
			}
		}
		else{
			$this->errArr[] = 'FATAL ERROR: Path NULL in uploadImage method';
		}
		return false;
	}

	public function copyImageFromUrl($sourceUri): bool
	{
		$returnVal = false;
	    if($sourceUri) {
            if($this->uriExists($sourceUri)) {
                if($this->targetPath) {
                    if(file_exists($this->targetPath)) {
                        $fileName = $this->cleanFileName($sourceUri);
                        $this->context = stream_context_create( array( 'http' => array( 'header' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36') ) );
                        if(copy($sourceUri, $this->targetPath.$fileName.$this->imgExt, $this->context)){
                            $this->sourcePath = $this->targetPath.$fileName.$this->imgExt;
                            $this->imgName = $fileName;
                            $returnVal = true;
                        }
                        else{
                            $this->errArr[] = 'FATAL ERROR: Unable to copy image to target ('.$this->targetPath.$fileName.$this->imgExt.')';
                        }
                    }
                    else {
                        $this->errArr[] = 'FATAL ERROR: Image target file ('.$this->targetPath.') does not exist in copyImageFromUrl method';
                    }
                }
                else {
                    $this->errArr[] = 'FATAL ERROR: Image target url NULL in copyImageFromUrl method';
                }
            }
            else {
                $this->errArr[] = 'FATAL ERROR: Image source file ('.$sourceUri.') does not exist in copyImageFromUrl method';
            }
        }
	    else {
			$this->errArr[] = 'FATAL ERROR: Image source uri NULL in copyImageFromUrl method';
		}
	    return $returnVal;
	}

	public function parseUrl($url): bool
	{
		$status = false;
		$url = str_replace(' ','%20',$url);
		if(strncmp($url, '/', 1) === 0){
			if(isset($GLOBALS['IMAGE_DOMAIN'])){
				$url = $GLOBALS['IMAGE_DOMAIN'].$url;
			}
			else{
				$urlPrefix = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
					$urlPrefix = 'https://';
				}
				$urlPrefix .= $_SERVER['HTTP_HOST'];
				if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80 && $_SERVER['SERVER_PORT'] !== 443) {
					$urlPrefix .= ':' . $_SERVER['SERVER_PORT'];
				}
				$url = $urlPrefix.$url;
			}
		}

		$this->sourceUrl = $url;
		if($this->uriExists($url)){
			$this->sourcePath = $url;
			$this->imgName = $this->cleanFileName($url);
			$status = true;
		}
		else{
			$this->errArr[] = 'FATAL ERROR: image url does not exist ('.$url.')';
		}
		return $status;
	}

	public function cleanFileName($fPath): string
    {
		$fName = $fPath;
		$imgInfo = null;
		if(strncasecmp($fPath, 'http://', 7) === 0 || strncasecmp($fPath, 'https://', 8) === 0){
			if(($dimArr = self::getImgDim($fPath)) && $dimArr) {
                $this->sourceWidth = $dimArr[0];
                $this->sourceHeight = $dimArr[1];
            }

			if($pos = strrpos($fName,'/')){
				$fName = substr($fName,$pos+1);
			}
		}

		if(strpos($fName,'?')) {
			$fName = substr($fName, 0, strpos($fName, '?'));
		}
		if($p = strrpos($fName,'.')){
			$this->sourceIdentifier = 'filename: '.$fName;
			if(!$this->imgExt) {
				$this->imgExt = strtolower(substr($fName, $p));
			}
			$fName = substr($fName,0,$p);
		}

		$fName = str_replace(array('.', '%20', '%23', ' ', '__', '__', chr(231), chr(232), chr(233), chr(234), chr(260), chr(230), chr(236), chr(237), chr(238), chr(239), chr(240), chr(241), chr(261), chr(247), chr(248), chr(249), chr(262), chr(250), chr(251), chr(263), chr(264), chr(265)), array('', '_', '_', '_', '_', '_', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'n', 'n'), $fName);
		$fName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $fName);
		$fName = trim($fName,' _-');

		if(strlen($fName) > 30) {
			$fName = substr($fName,0,30);
		}
		$fName .= '_'.time();
		if($this->targetPath){
			$tempFileName = $fName;
			$cnt = 0;
			while(file_exists($this->targetPath.$tempFileName.'_tn.jpg')){
				$tempFileName = $fName.'_'.$cnt;
				$cnt++;
			}
			if($cnt) {
				$fName = $tempFileName;
			}
		}

		return $fName;
	}

	public function setTargetPath($subPath = null): bool
	{
		$returnVal = true;
	    $path = $this->imageRootPath;
		$url = $this->imageRootUrl;
		if($path){
			if($subPath){
                $badChars = array(' ',':','.','"',"'",'>','<','%','*','|','?');
                $subPath = str_replace($badChars, '', $subPath);
            }
            else{
                $subPath = 'misc/'.date('Ym').'/';
            }
            if(substr($subPath,-1) !== '/') {
                $subPath .= '/';
            }
            $path .= $subPath;
            $url .= $subPath;
            if(file_exists($path) || (mkdir($path, 0777, true) && is_dir($path))) {
                $this->targetPath = $path;
                $this->urlBase = $url;
            }
            else{
                $this->errArr[] = 'FATAL ERROR: Unable to create directory: '.$path;
                $returnVal = false;
            }
        }
		else{
            $this->errArr[] = 'FATAL ERROR: Path empty in setTargetPath method';
            $returnVal = false;
        }
		return $returnVal;
	}

	public function processImage(): bool
	{
		if(!$this->imgName){
			$this->errArr[] = 'FATAL ERROR: Image file name null in processImage function';
			return false;
		}
		$imgTnUrl = '';
		if($this->createNewImage('_tn',$this->tnPixWidth,70)){
			$imgTnUrl = $this->imgName.'_tn.jpg';
		}

		if(!$this->sourceWidth || !$this->sourceHeight){
			[$this->sourceWidth, $this->sourceHeight] = self::getImgDim(str_replace(' ', '%20', $this->sourcePath));
		}
		$this->setSourceFileSize();

		$imgLgUrl = '';
		if($this->mapLargeImg){
			if($this->sourceWidth > ($this->webPixWidth*1.2) || $this->sourceFileSize > $this->webFileSizeLimit){
				if(strncmp($this->sourcePath, 'http://', 7) === 0 || strncmp($this->sourcePath, 'https://', 8) === 0) {
					$imgLgUrl = $this->sourcePath;
				}
				else if($this->sourceWidth < ($this->lgPixWidth*1.2)){
					if(copy($this->sourcePath,$this->targetPath.$this->imgName.'_lg'.$this->imgExt, $this->context)){
						$imgLgUrl = $this->imgName.'_lg'.$this->imgExt;
					}
				}
				else if($this->createNewImage('_lg',$this->lgPixWidth)){
					$imgLgUrl = $this->imgName.'_lg.jpg';
				}
			}
		}

		$imgWebUrl = '';
		if(strncmp($this->sourcePath, 'http://', 7) === 0 || strncmp($this->sourcePath, 'https://', 8) === 0){
			$imgWebUrl = $this->sourcePath;
		}
		if(!$imgWebUrl){
			if($this->sourceWidth < ($this->webPixWidth*1.2) && $this->sourceFileSize < $this->webFileSizeLimit){
				if(strncasecmp($this->sourcePath, 'http://', 7) === 0 || strncasecmp($this->sourcePath, 'https://', 8) === 0){
					if(copy($this->sourcePath, $this->targetPath.$this->imgName.$this->imgExt, $this->context)){
						$imgWebUrl = $this->imgName.$this->imgExt;
					}
				}
				else{
					$imgWebUrl = $this->imgName.$this->imgExt;
				}
			}
			else{
				$newWidth = ($this->sourceWidth<($this->webPixWidth*1.2)?$this->sourceWidth:$this->webPixWidth);
				$this->createNewImage('',$newWidth);
				$imgWebUrl = $this->imgName.'.jpg';
			}
		}

		$status = true;
		if($imgWebUrl){
			$status = $this->databaseImage($imgWebUrl,$imgTnUrl,$imgLgUrl);
		}
		return $status;
	}

	public function createNewImage($subExt, $targetWidth, $qualityRating = null, $targetPathOverride = null): bool
	{
		$status = false;
		if($this->sourcePath){
			if(!$qualityRating) {
				$qualityRating = $this->jpgCompression;
			}

			if(function_exists('gd_info') && extension_loaded('gd')) {
				$status = $this->createNewImageGD($subExt,$targetWidth,$qualityRating,$targetPathOverride);
			}
			else{
				$this->errArr[] = 'ERROR: No appropriate image handler for image conversions';
			}
		}
		return $status;
	}

	private function createNewImageGD($subExt, $newWidth, $qualityRating, $targetPathOverride): bool
	{
		$status = false;

		if(!$this->sourceWidth || !$this->sourceHeight){
			[$this->sourceWidth, $this->sourceHeight] = self::getImgDim(str_replace(' ', '%20', $this->sourcePath));
		}
		if($this->sourceWidth){
			$newHeight = round($this->sourceHeight*($newWidth/$this->sourceWidth));
			if($newWidth > $this->sourceWidth){
				$newWidth = $this->sourceWidth;
				$newHeight = $this->sourceHeight;
			}
			if(!$this->sourceGdImg){
				if($this->imgExt === '.gif'){
			   		$this->sourceGdImg = imagecreatefromgif($this->sourcePath);
					if(!$this->format) {
						$this->format = 'image/gif';
					}
				}
				elseif($this->imgExt === '.png'){
			   		$this->sourceGdImg = imagecreatefrompng($this->sourcePath);
					if(!$this->format) {
						$this->format = 'image/png';
					}
				}
				else{
					$this->sourceGdImg = imagecreatefromjpeg($this->sourcePath);
					if(!$this->format) {
						$this->format = 'image/jpeg';
					}
				}
			}

			if($this->sourceGdImg){
				$tmpImg = imagecreatetruecolor($newWidth,$newHeight);
				imagecopyresized($tmpImg,$this->sourceGdImg,0,0,0,0,$newWidth,$newHeight,$this->sourceWidth,$this->sourceHeight);

				$targetPath = $targetPathOverride;
				if(!$targetPath) {
					$targetPath = $this->targetPath . $this->imgName . $subExt . '.jpg';
				}
				if($qualityRating){
					$status = imagejpeg($tmpImg, $targetPath, $qualityRating);
				}
				else{
					$status = imagejpeg($tmpImg, $targetPath);
				}

				if(!$status){
					$this->errArr[] = 'ERROR: failed to create images using target path ('.$targetPath.')';
				}

				imagedestroy($tmpImg);
			}
			else{
				$this->errArr[] = 'ERROR: unable to create image object in createNewImageGD method (sourcePath: '.$this->sourcePath.')';
			}
		}
		else{
			$this->errArr[] = 'ERROR: unable to get source image width ('.$this->sourcePath.')';
		}
		return $status;
	}

	private function databaseImage($imgWebUrl,$imgTnUrl,$imgLgUrl): bool
	{
		$status = true;
		if($imgWebUrl){
			$urlBase = $this->getUrlBase();
			if(strncasecmp($imgWebUrl, 'http://', 7) !== 0 && strncasecmp($imgWebUrl, 'https://', 8) !== 0){
				$imgWebUrl = $urlBase.$imgWebUrl;
			}
			if($imgTnUrl && strncasecmp($imgTnUrl, 'http://', 7) !== 0 && strncasecmp($imgTnUrl, 'https://', 8) !== 0){
				$imgTnUrl = $urlBase.$imgTnUrl;
			}
			if($imgLgUrl && strncasecmp($imgLgUrl, 'http://', 7) !== 0 && strncasecmp($imgLgUrl, 'https://', 8) !== 0){
				$imgLgUrl = $urlBase.$imgLgUrl;
			}

			if(!$this->tid && $this->occid){
				$sql1 = 'SELECT tid FROM omoccurrences WHERE tid IS NOT NULL AND occid = '.$this->occid;
				$rs1 = $this->conn->query($sql1);
				if($r1 = $rs1->fetch_object()){
					$this->tid = $r1->tid;
				}
				$rs1->free();
			}

			$sql = 'INSERT INTO images (tid, url, thumbnailurl, originalurl, photographer, photographeruid, format, caption, '.
				'owner, sourceurl, copyright, locality, occid, notes, username, sortsequence, sourceIdentifier, ' .
				' rights, accessrights) '.
				'VALUES ('.($this->tid?:'NULL').',"'.$imgWebUrl.'",'.
				($imgTnUrl?'"'.$imgTnUrl.'"':'NULL').','.
				($imgLgUrl?'"'.$imgLgUrl.'"':'NULL').','.
				($this->photographer?'"'.$this->photographer.'"':'NULL').','.
				($this->photographerUid?:'NULL').','.
				($this->format?'"'.$this->format.'"':'NULL').','.
				($this->caption?'"'.$this->caption.'"':'NULL').','.
				($this->owner?'"'.$this->owner.'"':'NULL').','.
				($this->sourceUrl?'"'.$this->sourceUrl.'"':'NULL').','.
				($this->copyright?'"'.$this->copyright.'"':'NULL').','.
				($this->locality?'"'.$this->locality.'"':'NULL').','.
				($this->occid?:'NULL').','.
				($this->notes?'"'.$this->notes.'"':'NULL').',"'.
				Sanitizer::cleanInStr($this->conn,$GLOBALS['USERNAME']).'",'.
				($this->sortSeq?:'50').','.
				($this->sourceIdentifier?'"'.$this->sourceIdentifier.'"':'NULL').','.
				($this->rights?'"'.$this->rights.'"':'NULL').','.
				($this->accessRights?'"'.$this->accessRights.'"':'NULL').')';
			//echo $sql; exit;
			if($this->conn->query($sql)){
				$guid = UuidFactory::getUuidV4();
				$this->activeImgId = $this->conn->insert_id;
				if(!$this->conn->query('INSERT INTO guidimages(guid,imgid) VALUES("'.$guid.'",'.$this->activeImgId.')')) {
					$this->errArr[] = ' Warning: GUID mapping failed';
				}
			}
			else{
				$this->errArr[] = 'ERROR loading data.';
				$status = false;
			}
		}
		return $status;
	}

	public function deleteImage($imgIdDel, $removeImg): bool
	{
		$imgUrl = '';
		$imgThumbnailUrl = '';
		$imgOriginalUrl = '';
		$occid = 0;
		$sqlQuery = 'SELECT * FROM images WHERE (imgid = '.$imgIdDel.')';
		$rs = $this->conn->query($sqlQuery);
		if($r = $rs->fetch_object()){
			$imgUrl = $r->url;
			$imgThumbnailUrl = $r->thumbnailurl;
			$imgOriginalUrl = $r->originalurl;
			$this->tid = $r->tid;
			$occid = $r->occid;
			$imgArr = array();
			$imgObj = '';
			foreach($r as $k => $v){
				if($v) {
					$imgArr[$k] = $v;
				}
				$imgObj .= '"'.$k.'":"'.Sanitizer::cleanInStr($this->conn,$v).'",';
			}
			$imgObj = json_encode($imgArr);
			$sqlArchive = 'UPDATE guidimages '.
			"SET archivestatus = 1, archiveobj = '{".trim($imgObj,',')."}' ".
			'WHERE (imgid = '.$imgIdDel.')';
			$this->conn->query($sqlArchive);
		}
		$rs->close();

		$this->conn->query('DELETE FROM imagetag WHERE (imgid = '.$imgIdDel.')');

		$sql = 'DELETE FROM images WHERE (imgid = '.$imgIdDel.')';
		//echo $sql;
		if($this->conn->query($sql)){
			if($removeImg){
				$imgUrl2 = '';
				$domain = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
					$domain = 'https://';
				}
				$domain .= $_SERVER['HTTP_HOST'];
				if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80 && $_SERVER['SERVER_PORT'] !== 443) {
					$domain .= ':' . $_SERVER['SERVER_PORT'];
				}
				if(stripos($imgUrl,$domain) === 0){
					$imgUrl2 = $imgUrl;
					$imgUrl = substr($imgUrl,strlen($domain));
				}
				elseif(stripos($imgUrl,$this->imageRootUrl) === 0){
					$imgUrl2 = $domain.$imgUrl;
				}

				$sql = 'SELECT imgid FROM images WHERE (url = "'.$imgUrl.'") ';
				if($imgUrl2) {
					$sql .= 'OR (url = "' . $imgUrl2 . '")';
				}
				$rs = $this->conn->query($sql);
				if($rs->num_rows){
					$this->errArr[] = 'WARNING: Deleted records from database successfully but FAILED to delete image from server because it is being referenced by another record.';
				}
				else{
					$imgDelPath = str_replace($this->imageRootUrl,$this->imageRootPath,$imgUrl);
					if((strncmp($imgDelPath, 'http', 4) !== 0) && !unlink($imgDelPath)) {
						$this->errArr[] = 'WARNING: Deleted records from database successfully but FAILED to delete image from server (path: '.$imgDelPath.')';
					}

					if($imgThumbnailUrl){
						if(stripos($imgThumbnailUrl,$domain) === 0){
							$imgThumbnailUrl = substr($imgThumbnailUrl,strlen($domain));
						}
						$imgTnDelPath = str_replace($this->imageRootUrl,$this->imageRootPath,$imgThumbnailUrl);
						if(file_exists($imgTnDelPath) && strncmp($imgTnDelPath, 'http', 4) !== 0) {
							unlink($imgTnDelPath);
						}
					}

					if($imgOriginalUrl){
						if(stripos($imgOriginalUrl,$domain) === 0){
							$imgOriginalUrl = substr($imgOriginalUrl,strlen($domain));
						}
						$imgOriginalDelPath = str_replace($this->imageRootUrl,$this->imageRootPath,$imgOriginalUrl);
						if(file_exists($imgOriginalDelPath) && strncmp($imgOriginalDelPath, 'http', 4) !== 0) {
							unlink($imgOriginalDelPath);
						}
					}
				}
			}
		}
		else{
			$this->errArr[] = 'ERROR: Unable to delete image record.';
			return false;
		}
		return true;
	}

	public function insertImageTags($reqArr): bool
	{
		$status = true;
		if($this->activeImgId){
			$kArr = $this->getImageTagValues();
			foreach($kArr as $key => $description) {
				if(array_key_exists("ch_$key",$reqArr)) {
					$sql = 'INSERT INTO imagetag (imgid,keyvalue) VALUES (?,?) ';
					$stmt = $this->conn->stmt_init();
					$stmt->prepare($sql);
					if($stmt){
						$stmt->bind_param('is',$this->activeImgId,$key);
						if(!$stmt->execute()){
							$status = false;
							$this->errArr[] = "Warning: Failed to add image tag [$key] for $this->activeImgId.  " . $stmt->error;
						}
						$stmt->close();
					}
				}
			}
		}
		return $status;
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

	public function getActiveImgId(): int
	{
		return $this->activeImgId;
	}

	public function getSourcePath(): string
	{
		return $this->sourcePath;
	}

	public function getUrlBase(): string
	{
		$urlBase = $this->urlBase;
		if(isset($GLOBALS['IMAGE_DOMAIN'])){
			$urlPrefix = 'http://';
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
				$urlPrefix = 'https://';
			}
			$urlPrefix .= $_SERVER['HTTP_HOST'];
			if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80 && $_SERVER['SERVER_PORT'] !== 443) {
				$urlPrefix .= ':' . $_SERVER['SERVER_PORT'];
			}
			$urlBase = $urlPrefix.$urlBase;
		}
		return $urlBase;
	}

	public function getImgName(): string
	{
		return $this->imgName;
	}

	public function getImgExt(): string
	{
		return $this->imgExt;
	}

	public function getTnPixWidth(): int
	{
		return $this->tnPixWidth;
	}

	public function getWebPixWidth(): int
	{
		return $this->webPixWidth;
	}

	public function getWebFileSizeLimit(): int
	{
		return $this->webFileSizeLimit;
	}

	public function setMapLargeImg($t): void
	{
		$this->mapLargeImg = $t;
	}

	public function setCaption($v): void
	{
		$this->caption = Sanitizer::cleanInStr($this->conn,$v);
	}

	public function setPhotographer($v): void
	{
		$this->photographer = Sanitizer::cleanInStr($this->conn,$v);
	}

	public function setPhotographerUid($v): void
	{
		if(is_numeric($v)){
			$this->photographerUid = $v;
		}
	}

	public function setSourceUrl($v): void
	{
		$this->sourceUrl = Sanitizer::cleanInStr($this->conn,$v);
	}

	public function getTargetPath(): string
	{
		return $this->targetPath;
	}

	public function getFormat(){
		return $this->format;
	}

	public function setOwner($v): void
	{
		$this->owner = Sanitizer::cleanInStr($this->conn,$v);
	}

	public function setLocality($v): void
	{
		$this->locality = Sanitizer::cleanInStr($this->conn,$v);
	}

	public function setOccid($v): void
	{
		if(is_numeric($v)){
			$this->occid = $v;
		}
	}

	public function setTid($v): void
	{
		if(is_numeric($v)){
			$this->tid = $v;
		}
	}

	public function getTid(){
		return $this->tid;
	}

	public function setNotes($v): void
	{
		$this->notes = Sanitizer::cleanInStr($this->conn,$v);
	}

	public function setSortSeq($v): void
	{
		if(is_numeric($v)){
			$this->sortSeq = $v;
		}
	}

	public function setCopyright($v): void
	{
		$this->copyright = Sanitizer::cleanInStr($this->conn,$v);
	}

	public function getErrArr(): array
	{
		$retArr = $this->errArr;
		unset($this->errArr);
		$this->errArr = array();
		return $retArr;
	}

	public function getErrStr(): string
	{
		$retStr = implode('; ',$this->errArr);
		unset($this->errArr);
		$this->errArr = array();
		return $retStr;
	}

	public function getSourceFileSize(): int
	{
		if(!$this->sourceFileSize) {
			$this->setSourceFileSize();
		}
		return $this->sourceFileSize;
	}

	private function setSourceFileSize(){
		if($this->sourcePath && !$this->sourceFileSize){
			if(strncasecmp($this->sourcePath, 'http://', 7) === 0 || strncasecmp($this->sourcePath, 'https://', 8) === 0){
				$x = array_change_key_case(get_headers($this->sourcePath, 1),CASE_LOWER);
				if($x){
                    if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') !== 0 ) {
                        if(isset($x['content-length'][1])) {
                            $this->sourceFileSize = $x['content-length'][1];
                        }
                        elseif(isset($x['content-length'])) {
                            $this->sourceFileSize = $x['content-length'];
                        }
                    }
                    else if(isset($x['content-length'])) {
                        $this->sourceFileSize = $x['content-length'];
                    }
                }
	 		}
			else{
				$this->sourceFileSize = filesize($this->sourcePath);
			}
		}
		return $this->sourceFileSize;
	}

	public function uriExists($uri) {
		$exists = false;
        if(strncmp($uri, '/', 1) === 0){
			if($GLOBALS['IMAGE_ROOT_URL'] && strpos($uri,$GLOBALS['IMAGE_ROOT_URL']) === 0){
				$fileName = str_replace($GLOBALS['IMAGE_ROOT_URL'],$GLOBALS['IMAGE_ROOT_PATH'],$uri);
				if(file_exists($fileName)) {
					$exists = true;
				}
			}
			if(isset($GLOBALS['IMAGE_DOMAIN'])){
				$uri = $GLOBALS['IMAGE_DOMAIN'].$uri;
			}
			else{
				$urlPrefix = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
					$urlPrefix = 'https://';
				}
				$urlPrefix .= $_SERVER['HTTP_HOST'];
				if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80 && $_SERVER['SERVER_PORT'] !== 443) {
					$urlPrefix .= ':' . $_SERVER['SERVER_PORT'];
				}
				$uri = $urlPrefix.$uri;
			}
		}

		if(!$exists && function_exists('curl_init')) {
			$handle = curl_init($uri);
			curl_setopt($handle, CURLOPT_HEADER, true);
			curl_setopt($handle, CURLOPT_NOBODY, true);
			curl_setopt($handle, CURLOPT_FAILONERROR, true);
			curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36');
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			$exists = curl_exec($handle);
			$retCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			if($retCode < 400) {
				$exists = true;
			}
			if($retCode === 403) {
				$this->errArr[] = "403 Forbidden error (resource is not public or portal's IP address has been blocked)";
			}
			if($exists){
				if(!$this->format && !$this->imgExt && ($this->format = curl_getinfo($handle, CURLINFO_CONTENT_TYPE))) {
					if($this->format === 'image/gif') {
						$this->imgExt = '.gif';
					}
					elseif($this->format === 'image/png') {
						$this->imgExt = '.png';
					}
					elseif($this->format === 'image/jpeg') {
						$this->imgExt = '.jpg';
					}
				}
				if(!$this->sourceFileSize && $fileSize = curl_getinfo($handle, CURLINFO_CONTENT_LENGTH_DOWNLOAD)) {
					$this->sourceFileSize = $fileSize;
				}
			}
			curl_close($handle);
		}

		if(!$exists){
			if(file_exists($uri) || is_array(@getimagesize(str_replace(' ', '%20', $uri)))){
				return true;
			}
		}

		if(!$exists){
			$exists = (@fclose(@fopen($uri, 'rb')));
		}
		return $exists;
	}

	public static function getImgDim($imgUrl){
		if(!$imgUrl) {
			return false;
		}
		$imgDim = self::getImgDim1($imgUrl);
		if(!$imgDim) {
			$imgDim = self::getImgDim2($imgUrl);
		}
		if(!$imgDim) {
			$imgDim = @getimagesize($imgUrl);
		}
		return $imgDim;
	}

	private static function getImgDim1($imgUrl): array
    {
		$retArr = array();
	    $opts = array(
            'http'=>array(
                    'user_agent' => $GLOBALS['DEFAULT_TITLE'],
                    'method'=> 'GET',
                    'header'=> implode("\r\n", array('Content-type: text/plain;')
                )
            )
		);
		$context = stream_context_create($opts);
		if($handle = fopen($imgUrl, 'rb', false, $context)){
			$new_block = NULL;
			if(!feof($handle)) {
				$new_block = fread($handle, 32);
				$i = 0;
				if($new_block[$i] === "\xFF" && $new_block[$i+1] === "\xD8" && $new_block[$i+2] === "\xFF" && $new_block[$i+3] === "\xE0") {
					$i += 4;
					if($new_block[$i+2] === "\x4A" && $new_block[$i+3] === "\x46" && $new_block[$i+4] === "\x49" && $new_block[$i+5] === "\x46" && $new_block[$i+6] === "\x00") {
						$block_size = unpack('H*', $new_block[$i] . $new_block[$i+1]);
						if($block_size){
                            $block_size = hexdec($block_size[1]);
                            while(!feof($handle)) {
                                $i += $block_size;
                                $new_block .= $block_size > 0?fread($handle, $block_size):'';
                                if(is_int($i) && isset($new_block[$i]) && $new_block[$i] === "\xFF") {
                                    $sof_marker = array("\xC0", "\xC1", "\xC2", "\xC3", "\xC5", "\xC6", "\xC7", "\xC8", "\xC9", "\xCA", "\xCB", "\xCD", "\xCE", "\xCF");
                                    if(in_array($new_block[$i + 1], $sof_marker, true)) {
                                        $size_data = $new_block[$i+2] . $new_block[$i+3] . $new_block[$i+4] . $new_block[$i+5] . $new_block[$i+6] . $new_block[$i+7] . $new_block[$i+8];
                                        $unpacked = unpack('H*', $size_data);
                                        if($unpacked){
                                            $unpacked = $unpacked[1];
                                            $height = hexdec($unpacked[6] . $unpacked[7] . $unpacked[8] . $unpacked[9]);
                                            $width = hexdec($unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                                            return array($width, $height);
                                        }
                                    }
                                    $i += 2;
                                    $block_size = strlen($new_block) >= ($i+1)?unpack('H*', $new_block[$i] . $new_block[$i+1]):null;
                                    if($block_size){
                                        $block_size = hexdec($block_size[1]);
                                    }
                                }
                            }
                        }
					}
				}
			}
		}
		return $retArr;
	}

	private static function getImgDim2($imgUrl) {
        $width = 0;
        $height = 0;
        $data = file_get_contents($imgUrl);
		$im = @imagecreatefromstring($data);
		if($im) {
            $width = @imagesx($im);
            $height = @imagesy($im);
            imagedestroy($im);
		}
		if($width && $height) {
            return array($width,$height);
		}
		return false;
	}
}
