<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/UuidFactory.php');
include_once(__DIR__ . '/Sanitizer.php');

class MediaShared{

	private $conn;
	private $sourceGdImg;

	private $sourcePath = '';
	private $targetPath = '';
	private $urlBase = '';
	private $medName = '';
	private $medExt = '';
	private $imageRootPath;
	private $imageRootUrl;

	private $sourceFileSize = 0;

	private $webPixWidth = 1600;
	private $lgPixWidth = 3168;
	private $webFileSizeLimit = 300000;
	private $jpgCompression= 70;

	private $mapLargeImg = true;

	private $title;
	private $creator;
	private $creatoruid;
	private $type;
	private $format;
	private $owner;
	private $furtherinformationurl;
	private $occid;
	private $tid;
	private $language;
	private $usageterms;
	private $rights;
	private $bibliographiccitation;
	private $publisher;
	private $contributor;
	private $locationcreated;
    private $description;
    private $sortsequence;

	private $activeMedId = 0;

	private $errArr = array();

	public function __construct(){
		$connection = new DbConnection();
 		$this->conn = $connection->getConnection();
 		$this->imageRootPath = $GLOBALS['IMAGE_ROOT_PATH'];
		if(substr($this->imageRootPath,-1) !== '/') {
			$this->imageRootPath .= '/';
		}
		$this->imageRootUrl = $GLOBALS['IMAGE_ROOT_URL'];
		if(substr($this->imageRootUrl,-1) !== '/') {
			$this->imageRootUrl .= '/';
		}
		if($GLOBALS['IMG_FILE_SIZE_LIMIT']){
			$this->webFileSizeLimit = $GLOBALS['IMG_FILE_SIZE_LIMIT'];
		}
		ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0)');
		ini_set('memory_limit','512M');
 	}

	public function __destruct(){
		if($this->conn){
			$this->conn->close();
			$this->conn = null;
		}
	}

	public function reset(): void
	{
 		$this->sourcePath = '';
		$this->medName = '';
		$this->medExt = '';

		$this->title = '';
		$this->creator = '';
		$this->creatoruid = '';
		$this->type = '';
		$this->format = '';
		$this->owner = '';
		$this->furtherinformationurl = '';
		$this->occid = '';
		$this->tid = '';
		$this->language = '';
		$this->usageterms = '';
		$this->rights = '';
		$this->bibliographiccitation = '';
		$this->publisher = '';
		$this->contributor = '';
        $this->locationcreated = '';
        $this->description = '';
        $this->sortsequence = '';

		$this->activeMedId = 0;

		unset($this->errArr);
		$this->errArr = array();

 	}

	public function uploadMedia($medFile = null): bool
	{
        if(!$medFile){
            $medFile = 'medfile';
        }
        if($this->targetPath){
			if(file_exists($this->targetPath)){
				$medFileName = basename($_FILES[$medFile]['name']);
				$fileName = $this->cleanFileName($medFileName);
				if(is_writable($this->targetPath) && move_uploaded_file($_FILES[$medFile]['tmp_name'], $this->targetPath . $fileName . $this->medExt)) {
                    $this->sourcePath = $this->targetPath.$fileName.$this->medExt;
                    $this->medName = $fileName;
                    return true;
                }

				$this->errArr[] = 'FATAL ERROR: unable to move media file to target ('.$this->targetPath.$fileName.$this->medExt.')';
			}
			else{
				$this->errArr[] = 'FATAL ERROR: Target path does not exist in uploadMedia method ('.$this->targetPath.')';
			}
		}
		else{
            $this->errArr[] = 'FATAL ERROR: Path NULL in uploadMedia method';
		}
		return false;
	}

	public function parseUrl($url): bool
	{
		$status = false;
		$url = str_replace(' ','%20',$url);
		if(strncmp($url, '/', 1) === 0){
			if($GLOBALS['IMAGE_DOMAIN']){
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

		if($this->uriExists($url)){
			$this->sourcePath = $url;
			$this->medName = $this->cleanFileName($url);
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
			if($pos = strrpos($fName,'/')){
				$fName = substr($fName,$pos+1);
			}
		}

		if(strpos($fName,'?')) {
			$fName = substr($fName, 0, strpos($fName, '?'));
		}
		if($p = strrpos($fName,'.')){
			if(!$this->medExt) {
				$this->medExt = strtolower(substr($fName, $p));
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
			while(file_exists($this->targetPath.$tempFileName.$this->medExt)){
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

	public function processMedia(): bool
	{
		if(!$this->medName){
			$this->errArr[] = 'FATAL ERROR: Media file name null in processMedia function';
			return false;
		}

		$medUrl = '';
        if(strncmp($this->sourcePath, 'http://', 7) === 0 || strncmp($this->sourcePath, 'https://', 8) === 0){
			$medUrl = $this->sourcePath;
		}
        if(!$medUrl){
            $medUrl = $this->medName.$this->medExt;
		}
        $status = true;
		if($medUrl){
			$status = $this->databaseMedia($medUrl);
		}
		return $status;
	}

	private function databaseMedia($medUrl): bool
	{
		$status = true;
		if($medUrl){
			$urlBase = $this->getUrlBase();
			if(strncasecmp($medUrl, 'http://', 7) !== 0 && strncasecmp($medUrl, 'https://', 8) !== 0){
				$medUrl = $urlBase.$medUrl;
			}

			if(!$this->tid && $this->occid){
				$sql1 = 'SELECT tidinterpreted FROM omoccurrences WHERE tidinterpreted IS NOT NULL AND occid = '.$this->occid;
				$rs1 = $this->conn->query($sql1);
				if($r1 = $rs1->fetch_object()){
					$this->tid = $r1->tidinterpreted;
				}
				$rs1->free();
			}

			$sql = 'INSERT INTO media(tid, occid, accessuri, title, creatoruid, creator, type, format, owner, furtherinformationurl,'.
				'language, usageterms, rights, bibliographiccitation, publisher, contributor, locationcreated, description, ' .
				'sortsequence) '.
				'VALUES ('.($this->tid?:'NULL').','.($this->occid?:'NULL').',"'.$medUrl.'",'.
				($this->title?'"'.$this->title.'"':'NULL').','.
				($this->creatoruid?:'NULL').','.
                ($this->creator?'"'.$this->creator.'"':'NULL').','.
                ($this->type?'"'.$this->type.'"':'NULL').','.
				($this->format?'"'.$this->format.'"':'NULL').','.
				($this->owner?'"'.$this->owner.'"':'NULL').','.
				($this->furtherinformationurl?'"'.$this->furtherinformationurl.'"':'NULL').','.
				($this->language?'"'.$this->language.'"':'NULL').','.
				($this->usageterms?'"'.$this->usageterms.'"':'NULL').','.
				($this->rights?'"'.$this->rights.'"':'NULL').','.
				($this->bibliographiccitation?'"'.$this->bibliographiccitation.'"':'NULL').','.
                ($this->publisher?'"'.$this->publisher.'"':'NULL').','.
                ($this->contributor?'"'.$this->contributor.'"':'NULL').','.
                ($this->locationcreated?'"'.$this->locationcreated.'"':'NULL').','.
                ($this->description?'"'.$this->description.'"':'NULL').','.
				($this->sortsequence?:'50').')';
			//echo $sql; exit;
			if(!$this->conn->query($sql)){
                $this->errArr[] = 'ERROR loading data.';
                $status = false;
			}
		}
		return $status;
	}

	public function deleteMedia($medIdDel, $removeMed): bool
	{
		$medUrl = '';
		$occid = 0;
		$sqlQuery = 'SELECT * FROM media WHERE (mediaid = '.$medIdDel.')';
		$rs = $this->conn->query($sqlQuery);
		if($r = $rs->fetch_object()){
			$medUrl = $r->accessuri;
			$this->tid = $r->tid;
			$occid = $r->occid;
		}
		$rs->close();

		$sql = 'DELETE FROM media WHERE (mediaid = '.$medIdDel.')';
		//echo $sql;
		if($this->conn->query($sql)){
			if($removeMed){
				$medUrl2 = '';
				$domain = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
					$domain = 'https://';
				}
				$domain .= $_SERVER['HTTP_HOST'];
				if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80 && $_SERVER['SERVER_PORT'] !== 443) {
					$domain .= ':' . $_SERVER['SERVER_PORT'];
				}
				if(stripos($medUrl,$domain) === 0){
					$medUrl2 = $medUrl;
					$medUrl = substr($medUrl,strlen($domain));
				}
				elseif(stripos($medUrl,$this->imageRootUrl) === 0){
					$medUrl2 = $domain.$medUrl;
				}

				$sql = 'SELECT mediaid FROM media WHERE (accessuri = "'.$medUrl.'") ';
				if($medUrl2) {
					$sql .= 'OR (accessuri = "' . $medUrl2 . '")';
				}
				$rs = $this->conn->query($sql);
				if($rs->num_rows){
					$this->errArr[] = 'WARNING: Deleted records from database successfully but FAILED to delete media file from server because it is being referenced by another record.';
				}
				else{
					$imgDelPath = str_replace($this->imageRootUrl,$this->imageRootPath,$medUrl);
					if((strncmp($imgDelPath, 'http', 4) !== 0) && !unlink($imgDelPath)) {
						$this->errArr[] = 'WARNING: Deleted records from database successfully but FAILED to delete media file from server (path: '.$imgDelPath.')';
					}
                }
			}
		}
		else{
			$this->errArr[] = 'ERROR: Unable to delete media record.';
			return false;
		}
		return true;
	}

	public function getActiveMedId(): int
	{
		return $this->activeMedId;
	}

	public function getSourcePath(): string
	{
		return $this->sourcePath;
	}

	public function getUrlBase(): string
	{
		$urlBase = $this->urlBase;
		if($GLOBALS['IMAGE_DOMAIN']){
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
		return $this->medName;
	}

	public function getImgExt(): string
	{
		return $this->medExt;
	}

	public function getWebFileSizeLimit(): int
	{
		return $this->webFileSizeLimit;
	}

	public function setTitle($v): void
	{
		$this->title = Sanitizer::cleanInStr($v);
	}

	public function setCreator($v): void
	{
		$this->creator = Sanitizer::cleanInStr($v);
	}

	public function setCreatorUid($v): void
	{
		if(is_numeric($v)){
			$this->creatoruid = $v;
		}
	}

	public function setDescription($v): void
	{
		$this->description = Sanitizer::cleanInStr($v);
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
		$this->owner = Sanitizer::cleanInStr($v);
	}

	public function setType($v): void
	{
		$this->type = Sanitizer::cleanInStr($v);
	}

    public function setFormat($v): void
    {
        $this->format = Sanitizer::cleanInStr($v);
    }

    public function setUsageTerms($v): void
    {
        $this->usageterms = Sanitizer::cleanInStr($v);
    }

    public function setRights($v): void
    {
        $this->rights = Sanitizer::cleanInStr($v);
    }

    public function setPublisher($v): void
    {
        $this->publisher = Sanitizer::cleanInStr($v);
    }

    public function setContributor($v): void
    {
        $this->contributor = Sanitizer::cleanInStr($v);
    }

    public function setBibliographicCitation($v): void
    {
        $this->bibliographiccitation = Sanitizer::cleanInStr($v);
    }

    public function setFurtherInformationURL($v): void
    {
        $this->furtherinformationurl = Sanitizer::cleanInStr($v);
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

	public function setLanguage($v): void
	{
		$this->language = Sanitizer::cleanInStr($v);
	}

	public function setSortSequence($v): void
	{
		if(is_numeric($v)){
			$this->sortsequence = $v;
		}
	}

	public function setLocationCreated($v): void
	{
		$this->locationcreated = Sanitizer::cleanInStr($v);
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
			if($GLOBALS['IMAGE_DOMAIN']){
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
			curl_close($handle);
		}

		if(!$exists && file_exists($uri)) {
            return true;
        }

		if(!$exists){
			$exists = (@fclose(@fopen($uri, 'rb')));
		}
		return $exists;
	}
}
