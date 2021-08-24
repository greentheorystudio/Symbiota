<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/Sanitizer.php');

class SpecProcessorOcr{

	private $conn;
	private $tempPath;
	private $imgUrlLocal;
	private $deleteAllOcrFiles = 0;

	private $cropX = 0;
	private $cropY = 0;
	private $cropW = 1;
	private $cropH = 1;

	private $collid;
	private $specKeyPattern;
	private $ocrSource;
	
	private $verbose = 0;
	private $logFH;
	private $errorStr;
	
	public function __construct() {
		$this->setTempPath();
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

	public function __destruct(){
		if($this->logFH) {
			fclose($this->logFH);
		}
 		if(!($this->conn === false)) {
			$this->conn->close();
		}
	}

	public function ocrImageById($imgid,$getBest = null,$sciName=null){
		$rawStr = '';
		$sql = 'SELECT url, originalurl FROM images WHERE imgid = '.$imgid;
		if($rs = $this->conn->query($sql)){
			if($r = $rs->fetch_object()){
				$imgUrl = ($r->originalurl?:$r->url);
				$rawStr = $this->ocrImageByUrl($imgUrl, $getBest, $sciName);
			}
			$rs->free();
		}
		return $rawStr;
	}
	
	private function ocrImageByUrl($imgUrl,$getBest = null,$sciName=null){
		if($imgUrl){
			if($this->loadImage($imgUrl)){
				$this->cropImage();
				if($getBest){
					$rawStr = $this->getBestOCR($sciName);
				}
				else{
					$rawStr = $this->ocrImage();
				}
				if(!$rawStr) {
					if($this->imageTrimBorder()){
						if($getBest){
							$rawStr = $this->getBestOCR($sciName);
						}
						else{
							$rawStr = $this->ocrImage();
						}
					}
					if(!$rawStr) {
						$rawStr = 'Failed OCR return';
					}
				}
				$rawStr = $this->cleanRawStr($rawStr);
				unlink($this->imgUrlLocal);
			}
			else{
				$err = 'ERROR: Unable to load image, URL: '.$imgUrl;
				$this->logMsg($err,1);
				$rawStr = 'ERROR';
			}
		}
		else{
			$err = 'ERROR: Empty URL';
			$this->logMsg($err,1);
			$rawStr = 'ERROR';
		}
		return $rawStr;
	}

	private function ocrImage($url = null): string
	{
		$retStr = '';
		if(!$url) {
			$url = $this->imgUrlLocal;
		}
		if($url){
			$output = array();
			$outputFile = substr($url,0, -4);
			if(isset($GLOBALS['TESSERACT_PATH']) && $GLOBALS['TESSERACT_PATH']){
				if(strncmp($GLOBALS['TESSERACT_PATH'], 'C:', 2) === 0){
					exec('"'.$GLOBALS['TESSERACT_PATH'].'" '.$url.' '.$outputFile,$output);
				}
				else{
					exec($GLOBALS['TESSERACT_PATH'].' '.$url.' '.$outputFile,$output);
				}
			}
			else{
				exec('/usr/local/bin/tesseract '.$url.' '.$outputFile,$output);
			}

			if(file_exists($outputFile.'.txt')){
				if($fh = fopen($outputFile.'.txt', 'rb')){
					while (!feof($fh)) {
						$retStr .= $this->encodeString(fread($fh, 8192));
					}
					fclose($fh);
				}
				unlink($outputFile.'.txt');
			}
			else{
				$this->logMsg('ERROR: Unable to locate output file',1);
			}
		}
		return $retStr;
	}

	private function databaseRawStr($imgId,$rawStr,$notes,$source): ?bool
	{
		$retVal = false;
	    if(is_numeric($imgId) && $rawStr){
			$score = '';
			if($rawStr === 'Failed OCR return') {
				$score = 0;
			}
			$sql = 'INSERT INTO specprocessorrawlabels(imgid,rawstr,notes,source,score) '.
				'VALUE ('.$imgId.',"'.Sanitizer::cleanInStr($rawStr).'",'.
				($notes?'"'.Sanitizer::cleanInStr($notes).'"':'NULL').','.
				($source?'"'.Sanitizer::cleanInStr($source).'"':'NULL').','.
				($score?'"'.Sanitizer::cleanInStr($score).'"':'NULL').')';
			//echo 'SQL: '.$sql."\n";
			if($this->conn->query($sql)){
                $retVal = true;
			}
			else{
                $this->logMsg('ERROR: Unable to load fragment into database.',1);
            }
        }
		return $retVal;
	}

	private function loadImage($imgUrl): bool
	{
		$status = false;
		if($imgUrl){
			if(strncmp($imgUrl, '/', 1) === 0){
				if($GLOBALS['IMAGE_DOMAIN']){
					$imgUrl = $GLOBALS['IMAGE_DOMAIN'].$imgUrl;
				}
				else{
					$urlDomain = 'http://';
					if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
						$urlDomain = 'https://';
					}
					$urlDomain .= $_SERVER['HTTP_HOST'];
					if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
						$urlDomain .= ':' . $_SERVER['SERVER_PORT'];
					}
					$imgUrl = $urlDomain.$imgUrl;
				}
			}
			$ts = time();
			$this->imgUrlLocal = $this->tempPath.$ts.'_img.jpg';

			$status = copy($imgUrl,$this->imgUrlLocal);
		}
		return $status;
	}

	public function batchOcrUnprocessed($inCollStr,$procStatus = null,$limit = null,$getBest = null): void
	{
		if(!$procStatus){
            $procStatus = 'unprocessed';
        }
	    if($inCollStr) {
			$collArr = array();
			set_time_limit(600);
			ini_set('memory_limit','512M');
			
			$sql = 'SELECT DISTINCT collid, CONCAT_WS("-",institutioncode,collectioncode) AS instcode '.
				'FROM omcollections '.
				'WHERE collid IN('.$inCollStr.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$collArr[$r->collid] = $r->instcode;
			}
			$rs->free();
			
			foreach($collArr as $collid => $instCode){
				$this->logMsg('Starting batch processing for '.$instCode);
				$sql = 'SELECT i.imgid, IFNULL(i.originalurl, i.url) AS url, o.sciName, i.occid '.
					'FROM omoccurrences o INNER JOIN images i ON o.occid = i.occid '.
					'LEFT JOIN specprocessorrawlabels r ON i.imgid = r.imgid '.
					'WHERE (o.collid = '.$collid.') AND r.prlid IS NULL ';
				if($procStatus) {
					$sql .= 'AND o.processingstatus = "unprocessed" ';
				}
				if($limit) {
					$sql .= 'LIMIT ' . $limit;
				}
				if($rs = $this->conn->query($sql)){
					$recCnt = 1;
					while($r = $rs->fetch_object()){
						$rawStr = $this->ocrImageByUrl($r->url,$getBest,$r->sciName);
						if($rawStr !== 'ERROR'){
							$this->logMsg('#'.$recCnt.': image <a href="../editor/occurrenceeditor.php?occid='.$r->occid.'" target="_blank">'.$r->imgid.'</a> processed ('.date('Y-m-d H:i:s').')');
							$notes = '';
							$source = 'Tesseract: '.date('Y-m-d');
							$this->databaseRawStr($r->imgid,$rawStr,$notes,$source);
						}
						flush();
						$recCnt++;
					}
		 			$rs->free();
				}
			}
		}
	}

	public function harvestOcrText($postArr): bool
	{
		$status = true;
		set_time_limit(3600);
		$this->collid = $postArr['collid'];
		$this->ocrSource = $postArr['ocrsource'];
		$this->specKeyPattern = $postArr['speckeypattern'];
		if($this->specKeyPattern) {
            if(array_key_exists('sourcepath',$postArr) && $postArr['sourcepath']){
                $sourcePath = $postArr['sourcepath'];
            }
            else{
                $this->deleteAllOcrFiles = 1;
                $sourcePath = $this->uploadOcrFile();
            }
            if($sourcePath) {
                if(strncmp($sourcePath, 'http', 4) === 0){
                    $headerArr = get_headers($sourcePath);
                    if($headerArr) {
                        preg_match('/http.+\s(\d{3})\s/i',$headerArr[0],$codeArr);
                        if($codeArr[1] === 403){
                            $this->errorStr = 'ERROR loading OCR files: sourcePath returned Forbidden ('.$sourcePath.')';
                            $this->logMsg($this->errorStr);
                            $status = false;
                        }
                        else if($codeArr[1] === 404){
                            $this->errorStr = 'ERROR loading OCR files: sourcePath returned a page Not Found error ('.$sourcePath.')';
                            $this->logMsg($this->errorStr);
                            $status = false;
                        }
                        else if($codeArr[1] !== 200){
                            $this->errorStr = 'ERROR loading OCR files: sourcePath returned error code '.$codeArr[1].' ('.$sourcePath.')';
                            $this->logMsg($this->errorStr);
                            $status = false;
                        }
                    }
                    else {
                        $this->errorStr = 'ERROR loading OCR files: sourcePath returned bad headers ('.$sourcePath.')';
                        $this->logMsg($this->errorStr);
                        $status = false;
                    }
                }
                elseif(file_exists($sourcePath)) {
                    if(substr($sourcePath,-1) !== '/') {
                        $sourcePath .= '/';
                    }
                    if(strncmp($sourcePath, 'http', 4) === 0){
                        $this->processOcrHtml($sourcePath);
                    }
                    else{
                        $this->processOcrFolder($sourcePath);
                    }
                }
                else {
                    $this->errorStr = 'ERROR loading OCR files: sourcePath does not exist ('.$sourcePath.')';
                    $this->logMsg($this->errorStr);
                    $status = false;
                }
            }
            else {
                $this->errorStr = 'ERROR loading OCR files: OCR source path is missing';
                $this->logMsg($this->errorStr);
                $status = false;
            }
        }
		else {
			$this->errorStr = 'ERROR loading OCR files: Specimen catalog number pattern missing';
			$this->logMsg($this->errorStr);
            $status = false;
		}
		$this->logMsg('Done loading OCR files ');
		return $status;
	}
	
	private function uploadOcrFile(): string
    {
		$retPath = '';
		if(array_key_exists('ocrfile', $_FILES)) {
            if($this->tempPath) {
                $zipPath = $this->tempPath.'ocrupload.zip';
                if(file_exists($zipPath)) {
                    unlink($zipPath);
                }
                if(is_writable($this->tempPath)){
                    if(move_uploaded_file($_FILES['ocrfile']['tmp_name'], $zipPath)){
                        $zip = new ZipArchive;
                        $res = $zip->open($zipPath);
                        if($res === true) {
                            $extractPath = $this->tempPath.'ocrtext_'.time().'/';
                            if (!mkdir($extractPath) && !is_dir($extractPath)) {
                                throw new RuntimeException(sprintf('Directory "%s" was not created', $extractPath));
                            }
                            if($zip->extractTo($extractPath)){
                                $retPath = $extractPath;
                            }
                            $zip->close();
                            unlink($zipPath);
                        }
                        else{
                            $this->errorStr = 'ERROR unpacking OCR file: '.$res;
                            $this->logMsg($this->errorStr);
                        }
                    }
                    else{
                        $this->errorStr = 'ERROR loading OCR file: input file lacks zip extension';
                        $this->logMsg($this->errorStr);
                    }
                }
                else{
                    $this->errorStr = 'ERROR loading OCR file: Destination is not writable to server';
                    $this->logMsg($this->errorStr);
                }
            }
            else {
                $this->errorStr = 'ERROR loading OCR file: temp target path empty';
                $this->logMsg($this->errorStr);
            }
        }
		else {
			$this->errorStr = 'ERROR loading OCR file: OCR file missing';
			$this->logMsg($this->errorStr);
		}
		return $retPath;
	}
	
	private function processOcrHtml($sourcePath): void
	{
		$dom = new DOMDocument();
		$dom->loadHTMLFile($sourcePath);
		$aNodes= $dom->getElementsByTagName('a');
		$skipAnchors = array('Name','Last modified','Size','Description');
		foreach( $aNodes as $aNode ) {
			$fileName = $aNode->nodeValue;
			if(!in_array($fileName, $skipAnchors, true)){
				$fileExt = strtolower(substr($fileName,strrpos($fileName,'.')+1));
				if($fileExt){
					$this->logMsg('Processing OCR File: ' .$fileName);
					if($fileExt === 'txt'){
						$this->processOcrFile($fileName,$sourcePath);
					}
					else{
						$this->logMsg('ERROR: File skipped, not a supported OCR file with .txt extension: ' .$sourcePath.$fileName);
					}
				}
				elseif(stripos($fileName,'Parent Dir') === false){
					$this->logMsg('New dir path: '.$sourcePath.$fileName);
					$this->processOcrHtml($sourcePath.$fileName.'/');
				}
			}
		}
	}

	private function processOcrFolder($sourcePath): void
	{
		if($dirFH = opendir($sourcePath)){
			while($fileName = readdir($dirFH)){
				if($fileName !== '.' && $fileName !== '..' && $fileName !== '.svn'){
					if(is_file($sourcePath.$fileName)){
						$this->logMsg('Processing OCR File: ' .$fileName);
						$fileExt = strtolower(substr($fileName,strrpos($fileName,'.')));
						if($fileExt === '.txt'){
							$this->processOcrFile($fileName,$sourcePath);
						}
						else{
							$this->logMsg('ERROR: File skipped, not a supported OCR text file (.txt): ' .$fileName);
						}
					}
					elseif(is_dir($sourcePath.$fileName)){
						$this->processOcrFolder($sourcePath.$fileName. '/');
					}
				}
			}
			if($dirFH) {
				closedir($dirFH);
			}
		}
		else{
			$this->logMsg('ERROR: unable to access source directory: ' .$sourcePath,1);
		}
		if($this->deleteAllOcrFiles) {
			unlink($sourcePath);
		}
	}

	private function processOcrFile($fileName,$sourcePath): void
	{
		if($rawTextFH = fopen($sourcePath.$fileName, 'rb')){
			$rawStr = fread($rawTextFH, filesize($sourcePath.$fileName));
			fclose($rawTextFH);
			if($this->deleteAllOcrFiles) {
				unlink($sourcePath . $fileName);
			}
			$catNumber = '';
			if(preg_match($this->specKeyPattern, $fileName, $matchArr) && array_key_exists(1, $matchArr) && $matchArr[1]) {
				$catNumber = $matchArr[1];
			}
			if($catNumber){
				$imgArr = array();
				$sql = 'SELECT i.imgid, IFNULL(i.originalurl,i.url) AS url '.
					'FROM images i INNER JOIN omoccurrences o ON i.occid = o.occid '.
					'WHERE (o.collid = '.$this->collid.') AND (o.catalognumber = "'.Sanitizer::cleanInStr($catNumber).'")';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$imgArr[$r->imgid] = $r->url;
				}
				$rs->free();
				if(!$imgArr){
					$fileBaseName = basename($sourcePath.$fileName, '.txt');
					if(strlen($fileBaseName)>4){
						$sql = 'SELECT i.imgid, IFNULL(i.originalurl,i.url) AS url '.
							'FROM images i INNER JOIN omoccurrences o ON i.occid = o.occid '.
							'WHERE (o.collid = '.$this->collid.') AND ((i.originalurl LIKE "%/'.Sanitizer::cleanInStr($fileBaseName).'.jpg") OR (i.url LIKE "%/'.Sanitizer::cleanInStr($fileBaseName).'.jpg"))';
						$rs = $this->conn->query($sql);
						while($r = $rs->fetch_object()){
							$imgArr[$r->imgid] = $r->url;
						}
						$rs->free();
					}
				}
				if($imgArr){
					$imgId = key($imgArr);
					if(count($imgArr) > 1){
						$fileBaseName = basename($sourcePath.$fileName, '.txt');
						$imgIdOverride = '';
						foreach($imgArr as $k => $v){
							if (stripos($v,'/'.$fileBaseName.'.') || stripos($v,'/'.$fileBaseName.'_lg.')) {
								$imgIdOverride= $k;
								break;
							}

							if(stripos($v,'/'.$fileBaseName.'_')) {
								$imgIdOverride= $k;
							}
						}
						if($imgIdOverride) {
							$imgId = $imgIdOverride;
						}
					}
					if(file_exists($sourcePath . $fileName) && $this->databaseRawStr($imgId, $rawStr, '', $this->ocrSource . ': ' . date('Y-m-d'))) {
						unlink($sourcePath . $fileName);
					}
				}
				else{
					$this->logMsg('ERROR: unable locate specimen image (catalog #: '.$catNumber.')',1);
				}
			}
			else{
				$this->logMsg('ERROR: unable to extract catalog number ('.$fileName.' using '.$this->specKeyPattern.')',1);
			}
		}
		else{
			$this->logMsg('ERROR: unable to read rawOcr file: '.$fileName,1);
		}
	}

	private function cropImage(): bool
	{
		$status = false;
		if($this->cropX || $this->cropY || $this->cropW < 1 || $this->cropH < 1){
			try{
				if($img = imagecreatefromjpeg($this->imgUrlLocal)){
					$imgW = imagesx($img);
					$imgH = imagesy($img);
					if(($this->cropX + $this->cropW) > 1) {
						$this->cropW = 1 - $this->cropX;
					}
					if(($this->cropY + $this->cropH) > 1) {
						$this->cropH = 1 - $this->cropY;
					}
					$pX = $imgW*$this->cropX;
					$pY = $imgH*$this->cropY;
					$pW = $imgW*$this->cropW;
					$pH = $imgH*$this->cropH;
					$dest = imagecreatetruecolor($pW,$pH);
	
					if(imagecopy($dest,$img,0,0,$pX,$pY,$pW,$pH)){
						$status = imagejpeg($dest,$this->imgUrlLocal);
					}
					imagedestroy($dest);
					imagedestroy($img);
				}
			}
			catch(Exception $e){}
		}
		return $status;
	}

	private function imageTrimBorder(): bool
	{
		$retVal = false;
	    $img = imagecreatefromjpeg($this->imgUrlLocal);
		$width = (int)imagesx($img);
		$height = (int)imagesy($img);
		$bTop = 0;
		$bLeft = 0;
		$bBottom = $height - 1;
		$bRight = $width - 1;

		for(; $bTop < $height; $bTop += 2) {
			for($x = 0; $x < $width; $x += 2) {
				$rgb = imagecolorat($img, $x, $bTop);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				if(($r < -100 || $r > 100) && ($g < -100 || $g > 100) && ($b < -100 || $b > 100)){
					break 2;
				}
			}
		}

		if($bTop !== $height) {
            for(; $bBottom >= 0; $bBottom -= 2) {
                for($x = 0; $x < $width; $x += 2) {
                    $rgb = imagecolorat($img, $x, $bBottom);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    if(($r < -100 || $r > 100) && ($g < -100 || $g > 100) && ($b < -100 || $b > 100)){
                        break 2;
                    }
                }
            }

            for(; $bLeft < $width; $bLeft += 2) {
                for($y = $bTop; $y <= $bBottom; $y += 2) {
                    $rgb = imagecolorat($img, $bLeft, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    if(($r < -100 || $r > 100) && ($g < -100 || $g > 100) && ($b < -100 || $b > 100)){
                        break 2;
                    }
                }
            }

            for(; $bRight >= 0; $bRight -= 2) {
                for($y = $bTop; $y <= $bBottom; $y += 2) {
                    $rgb = imagecolorat($img, $bRight, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    if(($r < -100 || $r > 100) && ($g < -100 || $g > 100) && ($b < -100 || $b > 100)){
                        break 2;
                    }
                }
            }

            $bBottom++;
            $bRight++;

            $w = $bRight - $bLeft;
            $h = $bBottom - $bTop;
            if($w < $width || $h < $height){
                $dest = imagecreatetruecolor($w,$h);
                if(imagecopy($dest, $img, 0, 0, $bLeft, $bTop, $w, $h)){
                    imagejpeg($dest,$this->imgUrlLocal);
                }
                imagedestroy($dest);
                imagedestroy($img);
                $retVal = true;
            }
		}
		return $retVal;
	}

	private function getBestOCR($sciName = null): ?string
	{
		$rawStr_base = $this->ocrImage();
		$score_base = $this->scoreOCR($rawStr_base, $sciName);
		$urlTemp = str_replace('.jpg','_f1.jpg',$this->imgUrlLocal);
		copy($this->imgUrlLocal,$urlTemp);
		$this->filterImage($urlTemp);
		$rawStr_treated = $this->ocrImage($urlTemp);
		$score_treated = $this->scoreOCR($rawStr_treated, $sciName);
		unlink($urlTemp);
		if($score_treated > $score_base) {
			$this->logMsg('Best Score applied',1);
			return $rawStr_treated;
		}

		return $rawStr_base;
	}

	private function filterImage($url = null): bool
	{
		$status = false;
		if(!$url) {
			$url = $this->imgUrlLocal;
		}
		if($img = imagecreatefromjpeg($url)){
			imagefilter($img,IMG_FILTER_GRAYSCALE);
			imagefilter($img,IMG_FILTER_BRIGHTNESS,10);
			imagefilter($img,IMG_FILTER_CONTRAST,1);
			$sharpenMatrix = array
			(
				array(-1.2, -1, -1.2),
				array(-1, 20, -1),
				array(-1.2, -1, -1.2)
			);
			$divisor = array_sum(array_map('array_sum', $sharpenMatrix));
			$offset = 0;
			imageconvolution($img, $sharpenMatrix, $divisor, $offset);
			imagegammacorrect($img, 6, 1.0);
			$status = imagejpeg($img,$url);
			imagedestroy($img);
		}
		return $status;
	}

	private function scoreOCR($rawStr, $sciName = null) {
		$sLength = strlen($rawStr);
		if($sLength > 12) {
			$numWords = 0;
			$numBadLinesIncremented = false;
			$numBadLines = 1;
			$lines = explode("\n", $rawStr);
			foreach($lines as $line) {
				$line = trim($line);
				if(strlen($line) > 2) {
					$words = explode(' ', $line);
					foreach($words as $word) {
						if(strlen($word) > 2)
						{
							$goodChars = 0;
							$badChars = 0;
							foreach (count_chars($word, 1) as $i => $let) {
								if(($i > 47 && $i < 60) || ($i > 64 && $i < 91) || ($i > 96 && $i < 123) || $i === 176) {
									$goodChars++;
								}
								else if(($i < 44 || $i > 59) && !($i === 32 || $i === 35 || $i === 34 || $i === 39 || $i === 38 || $i === 40 || $i === 41 || $i === 61)) {
									$badChars++;
								}
							}
							if($goodChars > 3*$badChars) {
								$numWords++;
							}
						}
					}
				}
				else if($numBadLines === 1) {
					if($numBadLinesIncremented) {
						$numBadLines++;
					}
					else {
						$numBadLinesIncremented = true;
					}
				}
				else {
					$numBadLines++;
				}
			}
			$numGoodChars = 0;
			$numBadChars = 1;
			$numBadIncremented = false;
			foreach (count_chars($rawStr, 1) as $i => $val) {
				if(($i > 47 && $i < 60) || ($i > 64 && $i < 91) || ($i > 96 && $i < 123) || $i === 176) {
					$numGoodChars += $val;
				}
				else if(($i < 44 || $i > 59) && !($i === 32 || $i === 35 || $i === 34 || $i === 39 || $i === 38 || $i === 40 || $i === 41 || $i === 61)) {
					if($numBadChars === 1) {
						if($numBadIncremented) {
							$numBadChars += $val;
						}
						else {
							$numBadIncremented = true;
							$numBadChars += ($val-1);
						}
					}
					else {
						$numBadChars += $val;
					}
				}
			}
			return (($numWords*$numGoodChars)/($sLength*$numBadChars*$numBadLines)) + $this->findSciName($rawStr,$sciName);
		}

		return 0;
	}

	private function findSciName($rawStr,$sciName) {
		$result = 0;
		if($sciName !== '') {
			$words = explode(' ', $sciName);
			foreach($words as $word) {
				$wrdLen = strlen($word);
				if($wrdLen > 4) {
					if(stripos($rawStr,$word) !== false) {
						$result += 0.3;
					}
					elseif(stripos($rawStr,str_replace('g', 'p', $word)) !== false) {
						$result += 0.2;
					}
					elseif(stripos($rawStr,str_replace('q', 'p', $word)) !== false) {
						$result += 0.2;
					}
					elseif(stripos($rawStr,str_replace('1', 'l', $word)) !== false) {
						$result += 0.2;
					}
					elseif(stripos($rawStr,str_replace('1', 'i', $word)) !== false) {
						$result += 0.2;
					}
					elseif(stripos($rawStr,str_replace('b', 'h', $word)) !== false) {
						$result += 0.2;
					}
					elseif(stripos($rawStr,str_replace('v', 'y', $word)) !== false) {
						$result += 0.2;
					}
					else {
						$shrtWrd = substr($word, 1);
						if(stripos($rawStr,$shrtWrd) !== false) {
							$result += 0.1;
						}
						elseif(stripos($rawStr,str_replace('I', 'l', $shrtWrd)) !== false) {
							$result += 0.1;
						}
						elseif(stripos($rawStr,str_replace('H', 'll', $shrtWrd)) !== false) {
							$result += 0.1;
						}
						else {
							$shrtWrd = substr($word, 0, $wrdLen-1);
							if(stripos($rawStr,$shrtWrd) !== false) {
								$result += 0.1;
							}
						}
					}
				}
			}
		}
		$goodWords =
			array (
					'collect', 'fungi', 'location', 'locality', 'along', 'rock', 'outcrop', 'thallus', 'pseudotsuga',
					'habitat', 'det.', 'determine',	'date', 'long.', 'latitude', 'lat.', 'shale', 'laevis',
					'longitude', 'elevation', 'elev.', 'quercus', 'acer', 'highway', 'preserve', 'hardwood',
					'road', 'sandstone', ' granit', 'slope', 'county', 'near', 'north', 'forest', 'Bungartz',
					'south', 'east', 'west', 'stream', 'Wetmore', 'Nash', 'Imsaug', 'mile', 'wood', 'Esslinger',
					'Thomson', 'Lendemer', 'Johnson', 'Harris', 'Rosentretter', 'Hodges', 'Malachowski',
					'Tucker', 'Egan', 'Fink', 'Shushan', 'Sullivan', 'Crane', 'Schoknecht', 'Marsh', 'Lumbsch',
					'Trana', 'Phillipe', 'Landron', 'Eyerdam', 'Sharnoff', 'Schuster', 'Perlmutter', 'Fryday',
					'Ohlsson', 'Howard', 'Taylor', 'Arnot', 'Gowan', 'Dey', 'Scotter', 'Llano', 'Keith', 'Moberg',
					'Brako', 'Ricklefs', 'Darrow', 'Macoun', 'Barclay', 'Culberson', 'Alvarez', 'ground', 'ridge',
					'Wong', 'Gould', 'Shchepanek', 'Wheeler', 'Hasse', 'Kashiwadani', 'Havaas', 'Weise', 'Sheard',
					'Malme', 'Hansen', 'Erbisch', 'Degelius', 'Hafellner', 'Reed', 'Sweat', 'Streimann', 'McCune',
					'Ryan', 'Brodo', 'Bratt', 'Burnett', 'Knudsen', 'Weber', 'Vezda', 'Langlois', 'Follmann',
					'Buck', 'Arnold', 'Thaxter', 'Armstrong', 'Ahti', 'Wheeler', 'Britton', 'Marble', 'national',
					'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
					'November', 'December', 'Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
					'Calkins', 'McHenry', 'Schofield', 'SHIMEK', 'Hepp', 'Talbot', 'Riefner', 'WAGHORNE', 'Becking',
					'Nebecker', 'Lebo', 'Advaita', 'DeBolt', 'Austin', 'Brouard', 'Amtoft', 'KIENER', 'Kalb', 'Hertel',
					'Clair', 'Nee', 'Boykin', 'Sundberg', 'Elix', 'Santesson', 'plant', 'glade', 'parish', 'swamp',
					'Ilex', 'Diospyros', '(Ach.)', 'Leight', 'river', 'trail', 'mount', 'wall', 'index', 'pine',
					'vicinity', 'durango', 'madre', 'stalk', 'moss', 'down', 'some', 'base', 'alga', 'brown', 'punta',
					'dirt', 'stand', 'meter', 'dead', 'steep', 'isla', 'town', 'station', 'picea', 'shore', 'over',
					'attached', 'apothecia', 'spruce', 'upper', 'rosa', 'rocky', 'litter', 'about', 'shade', 'coast',
					'tree', 'live', 'fork', 'cliff', 'amabilis', 'facing', 'junction', 'white', 'partial', 'bare',
					'scrub', 'then', 'boulder', 'conifer', 'branch', 'adjacent', 'peak', 'sonoran', 'maple', 'sample',
					'expose', 'parashant', 'pinyon', 'growing', 'fragment', 'shrub', 'below', 'limestone', 'scatter',
					'snag', 'douglas', 'secondary', 'state', 'point', 'pass', 'basalt', 'edge', 'year', 'hemlock',
					'vigor', 'association', 'cedar', 'community', 'head', 'cowlitz', 'tsuga', 'juniper', 'monument',
					'between', 'baker-snoqualmie', 'menziesii', 'heterophylla', 'just', 'wenatchee', 'ranger', 'grand',
					'mixed', 'rhyolite', 'plot', 'growth', 'desert', 'spore', 'sierra', 'abies', 'small', 'gifford',
					'pinchot', 'district', 'pinus', 'valley', 'aspect', 'santa', 'open', 'service', 'degree', 'above',
					'island', 'side', 'bark', 'lake', 'creek', 'canyon', 'from', 'substrate', 'slope', 'with', 'area'
			);
		foreach($goodWords as $goodWord) {
			if(stripos($rawStr,$goodWord) !== false) {
				$result += 0.2;
			}
		}
		return $result;
	}

	public function setCropX($x): void
	{
		$this->cropX = $x;
	}

	public function setCropY($y): void
	{
		$this->cropY = $y;
	}

	public function setCropW($w): void
	{
		$this->cropW = $w;
	}

	public function setCropH($h): void
	{
		$this->cropH = $h;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	public function setVerbose($s): void
	{
		$this->verbose = (int)$s;
		if($this->verbose === 1 || $this->verbose === 3){
			if($this->tempPath){
				$GLOBALS['LOG_PATH'] = $this->tempPath.'log_'.date('Ymd').'.log';
				$this->logFH = fopen($GLOBALS['LOG_PATH'], 'ab');
			}
		}
	}

	private function setTempPath(): void
	{
		if($GLOBALS['TEMP_DIR_ROOT']){
			$tempPath = $GLOBALS['TEMP_DIR_ROOT'];
		}
		else{
			$tempPath = ini_get('upload_tmp_dir');
		}
		if(!$tempPath){
			$tempPath = $GLOBALS['SERVER_ROOT'];
			if(substr($tempPath,-1) !== '/') {
				$tempPath .= '/';
			}
			$tempPath .= 'temp/';
		}
		if(substr($tempPath,-1) !== '/') {
			$tempPath .= '/';
		}
		if(file_exists($tempPath . 'symbocr/') || mkdir($concurrentDirectory = $tempPath . 'symbocr/') || is_dir($concurrentDirectory)){
			$tempPath .= 'symbocr/';
		}

		$this->tempPath = $tempPath;
	}

	private function logMsg($msg, $indent = null): void
	{
		if($this->verbose === 1 || $this->verbose === 3){
			if($this->logFH){
				$msg .= "\n";
				if($indent) {
					$msg = "\t" . $msg;
				}
				fwrite($this->logFH, $msg);
			}
		}
		elseif($this->verbose > 1 ){
			echo '<li style="margin-left:'.($indent?$indent*15:'0').'px">'.$msg.'</li>';
		}
	}

	private function cleanRawStr($inStr){
		$retStr = $this->encodeString($inStr);

		$replacements = array("/\." => 'A.', "/-\\" => 'A', "\X/" => 'W', "\Y/" => 'W', "`\‘i/" => 'W', chr(96) => "'", chr(145) => "'", chr(146) => "'",
			'�' => "'", chr(147) => '"', chr(148) => '"', chr(152) => '"', chr(239) => '�');
		$retStr = str_replace(array_keys($replacements), $replacements, $retStr);

		$false_num_class = "[OSZl|I!\d]";
		$preg_replace_callback_pattern =
			array(
				'/' .$false_num_class."{1,3}(\.".$false_num_class."{1,7})\s?".chr(176)."\s?[NSEW(\\\V)(\\\W)]/",
				'/' .$false_num_class. '{1,3}' .chr(176)."\s?".$false_num_class."{1,3}(\.".$false_num_class."{1,3})?\s?'\s?[NSEW(\\\V)(\\\W)]/",
				'/' .$false_num_class. '{1,3}' .chr(176)."\s?".$false_num_class."{1,3}\s?'\s?(".$false_num_class."{1,3}(\.".$false_num_class."{1,3})?\"\s?)?[NSEW(\\\V)(\\\W)]/"
			);
		$retStr = preg_replace_callback($preg_replace_callback_pattern, create_function('$matches','return str_replace(array("l","|","!","I","O","S","Z"), array("1","1","1","1","0","5","2"), $matches[0]);'), $retStr);
		$retStr = preg_replace("/(\d\s?[".chr(176)."'\"])\s?\\\[VW]/", '\${1}W', $retStr, -1);
		$retStr = preg_replace_callback(
			"/(((?i)January|Jan\.?|February|Feb\.?|March|Mar\.?|April|Apr\.?|May|June|Jun\.?|July|Jul\.?|August|Aug\.?|September|Sept?\.?|October|Oct\.?|November|Nov\.?|December|Dec\.?)\s)(([\dOIl|!ozZS]{1,2}),?\s)([\dOI|!lozZS]{4})/",
			create_function('$matches','return $matches[1].str_replace(array("l","|","!","I","O","o","Z","z","S"), array("1","1","1","1","0","0","2","2","5"), $matches[3]).str_replace(array("l","|","!","I","O","o","Z","z","S"), array("1","1","1","1","0","0","2","2","5"), $matches[5]);'),
			$retStr
		);
		//replace Zs with 2s, Is with 1s and Os with 0s in dates of type DD-Mon(th)-YYYY or DDMon(th)YYYY or DD Mon(th) YYYY
		$retStr = preg_replace_callback(
			"/([\dOIl!|ozZS]{1,2}[-\s]?)(((?i)January|Jan\.?|February|Feb\.?|March|Mar\.?|April|Apr\.?|May|June|Jun\.?|July|Jul\.?|August|Aug\.?|September|Sept?\.?|October|Oct\.?|November|Nov\.?|December|Dec\.?)[-\s]?)([\dOIl|!ozZS]{4})/i",
			create_function('$matches','return str_replace(array("l","|","!","I","O","o","Z","z","S"), array("1","1","1","1","0","0","2","2","5"), $matches[1]).$matches[2].str_replace(array("l","|","!","I","O","o","Z","z","S"), array("1","1","1","1","0","0","2","2","5"), $matches[4]);'),
			$retStr
		);
		return $retStr;
	}

	private function encodeString($inStr): string
	{
		$retStr = $inStr;
		$search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
		$replace = array("'","'",'"','"','*','-','-');
		$inStr = str_replace($search, $replace, $inStr);
		$badwordchars=array("\xe2\x80\x98",
							"\xe2\x80\x99",
							"\xe2\x80\x9c",
							"\xe2\x80\x9d",
							"\xe2\x80\x94",
							"\xe2\x80\xa6"
		);
		$fixedwordchars=array("'", "'", '"', '"', '-', '...');
		$inStr = str_replace($badwordchars, $fixedwordchars, $inStr);
		
		if($inStr){
			$lowerStr = strtolower($GLOBALS['CHARSET']);
			if($lowerStr === 'utf-8' || $lowerStr === 'utf8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
				}
			}
			elseif($lowerStr === 'iso-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
				}
			}
		}
		return $retStr;
	}
}
