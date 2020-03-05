<?php
include_once('DbConnection.php');
include_once('OccurrenceMaintenance.php');
include_once('UuidFactory.php');

class ImageLocalProcessor {

	protected $conn;

	private $collArr = array();
	private $activeCollid;
	private $collProcessedArr = array();

	protected $sourcePathBase;
	private $targetPathBase;
	private $targetPathFrag;
	private $origPathFrag;
	private $imgUrlBase;
	private $serverRoot;
	
	private $matchCatalogNumber = true;
	private $matchOtherCatalogNumbers = false;
	private $webPixWidth = '';
	private $tnPixWidth = '';
	private $lgPixWidth = '';
	private $webFileSizeLimit = 300000;
	private $lgFileSizeLimit = 3000000;
	private $jpgQuality= 80;
	private $webImg = 1;
	private $tnImg = 1;
	private $lgImg = 1;
	private $webSourceSuffix = '';
	private $tnSourceSuffix = '_tn';
	private $lgSourceSuffix = '_lg';
	private $keepOrig = 0;

	private $skeletalFileProcessing = true;
	private $createNewRec = true;
	private $imgExists = 0;
	protected $dbMetadata = 1;
	private $processUsingImageMagick = 0;

	private $logMode = 0;
	private $logPath = '';
	private $logFH;
	private $mdOutputFH;
	private $errorMessage;
	
	private $sourceGdImg;
	private $sourceImagickImg;
	
	private $dataLoaded = 0;

	private $monthNames = array('jan'=>'01','ene'=>'01','feb'=>'02','mar'=>'03','abr'=>'04','apr'=>'04',
		'may'=>'05','jun'=>'06','jul'=>'07','ago'=>'08','aug'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dec'=>'12','dic'=>'12');

    private $processedFiles = array();
 

	public function __construct(){
		global $IMG_WEB_WIDTH, $IMG_TN_WIDTH, $IMG_LG_WIDTH, $IMG_FILE_SIZE_LIMIT, $LOG_PATH, $SERVER_ROOT;
		ini_set('memory_limit','1024M');
		ini_set('auto_detect_line_endings', true);
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
		if($IMG_WEB_WIDTH) {
			$this->webPixWidth = $IMG_WEB_WIDTH;
		}
		if($IMG_TN_WIDTH) {
			$this->tnPixWidth = $IMG_TN_WIDTH;
		}
		if($IMG_LG_WIDTH) {
			$this->lgPixWidth = $IMG_LG_WIDTH;
		}
		if($IMG_FILE_SIZE_LIMIT) {
			$this->webFileSizeLimit = $IMG_FILE_SIZE_LIMIT;
		}
		if($LOG_PATH) {
			$this->logPath = $LOG_PATH;
		}
		if($SERVER_ROOT) {
			$this->serverRoot = $SERVER_ROOT;
		}
	}

	public function __destruct(){
		if($this->dbMetadata && !($this->conn === false)) {
			$this->conn->close();
		}

		if($this->logFH) {
			fclose($this->logFH);
		}
	}

	public function initProcessor($logTitle = ''): void
	{
		if($this->logPath && $this->logMode > 1){
			if(!file_exists($this->logPath) && !mkdir($concurrentDirectory = $this->logPath, 0, true) && !is_dir($concurrentDirectory)) {
				echo('Warning: unable to create log file: ' .$this->logPath);
			}
			if(file_exists($this->logPath)){
				$titleStr = str_replace(' ','_',$logTitle);
				if(strlen($titleStr) > 50) {
					$titleStr = substr($titleStr, 0, 50);
				}
				$logFile = $this->logPath.$titleStr. '_' .date('Y-m-d'). '.log';
				$this->logFH = fopen($logFile, 'ab');
				$this->logOrEcho("\nDateTime: ".date('Y-m-d h:i:s A'));
			}
			else{
				echo 'ERROR creating Log file; path not found: '.$this->logPath."\n";
			}
		}
	}

	public function batchLoadImages(): void
	{
		global $IMAGE_ROOT_PATH, $IMAGE_ROOT_URL, $IMAGE_DOMAIN;
		if(strpos($this->sourcePathBase, 'http') === 0){
			$headerArr = get_headers($this->sourcePathBase);
			if(!$headerArr){
				$this->logOrEcho('ABORT: sourcePathBase returned bad headers ('.$this->sourcePathBase.')');
				exit();
			} 
			preg_match('/http.+\s(\d{3})\s/i',$headerArr[0],$codeArr);
			if($codeArr[1] === 403){
				$this->logOrEcho('ABORT: sourcePathBase returned Forbidden ('.$this->sourcePathBase.')');
				exit();
			}
			if($codeArr[1] === 404){
				$this->logOrEcho('ABORT: sourcePathBase returned a page Not Found error ('.$this->sourcePathBase.')');
				exit();
			}
			if($codeArr[1] !== 200){
				$this->logOrEcho('ABORT: sourcePathBase returned error code '.$codeArr[1].' ('.$this->sourcePathBase.')');
				exit();
			}
		}
		elseif(!file_exists($this->sourcePathBase)){
			$this->logOrEcho('ABORT: sourcePathBase does not exist ('.$this->sourcePathBase.')');
			exit();
		}
		if(!$this->targetPathBase){
			$this->targetPathBase = $IMAGE_ROOT_PATH;
		}
		if(!$this->targetPathBase){
			$this->targetPathBase = $IMAGE_ROOT_PATH;
		}
		if($this->targetPathBase && substr($this->targetPathBase,-1) !== '/' && substr($this->targetPathBase,-1) !== "\\"){
			$this->targetPathBase .= '/';
		}
		
		if(!$this->imgUrlBase){
			$this->imgUrlBase = $IMAGE_ROOT_URL;
		}
		if($IMAGE_DOMAIN && strpos($this->imgUrlBase, 'http://') !== 0 && strpos($this->imgUrlBase, 'https://') !== 0) {
			$urlPrefix = 'http://';
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
				$urlPrefix = 'https://';
			}
			$urlPrefix .= $_SERVER['HTTP_HOST'];
			if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
				$urlPrefix .= ':' . $_SERVER['SERVER_PORT'];
			}
			$this->imgUrlBase = $urlPrefix.$this->imgUrlBase;
		}
		if($this->imgUrlBase && substr($this->imgUrlBase,-1) !== '/' && substr($this->imgUrlBase,-1) !== "\\"){
			$this->imgUrlBase .= '/';
		}

		if($this->logMode === 1){
			echo '<ul>';
		}
		foreach($this->collArr as $collid => $cArr){
			$this->activeCollid = $collid;
			$collStr = '';
			if(isset($cArr['instcode'])) {
				$collStr = str_replace(' ', '', $cArr['instcode'] . ($cArr['collcode'] ? '_' . $cArr['collcode'] : ''));
			}
			if(!$collStr) {
				$collStr = str_replace('/', '_', $cArr['sourcePathFrag']);
			}

			$mdFileName = '';
			if(!$this->dbMetadata){
				$mdFileName = $this->logPath.$collStr.'_imagedata_'.date('Y-m-d').'_'.time().'.csv';
				$this->mdOutputFH = fopen($mdFileName, 'wb');
				fwrite($this->mdOutputFH, '"collid","catalogNumber","url","thumbnailurl","originalurl"'."\n");
				if($this->mdOutputFH){
					$this->logOrEcho("Image Metadata written out to CSV file: '".$mdFileName."' (same folder as script)");
				}
				else{
					$this->logOrEcho('Image upload aborted: Unable to establish connection to output file to where image metadata is to be written');
					exit('ABORT: Image upload aborted: Unable to establish connection to output file to where image metadata is to be written');
				}
			}
			
			$sourcePathFrag = '';
			$this->targetPathFrag = '';
			if(isset($cArr['sourcePathFrag'])){
				$sourcePathFrag = $cArr['sourcePathFrag'];
				$this->targetPathFrag = $cArr['sourcePathFrag'];
			}
			else{
				$this->targetPathFrag .= $collStr;
			}
			if(substr($this->targetPathFrag,-1) !== '/' && substr($this->targetPathFrag,-1) !== "\\"){
				$this->targetPathFrag .= '/';
			}
			if($sourcePathFrag && substr($sourcePathFrag,-1) !== '/' && substr($sourcePathFrag,-1) !== "\\"){
				$sourcePathFrag .= '/';
			}
			if(!file_exists($this->targetPathBase . $this->targetPathFrag) && !mkdir($concurrentDirectory = $this->targetPathBase . $this->targetPathFrag, 0777, true) && !is_dir($concurrentDirectory)) {
                $this->logOrEcho('ERROR: unable to create new folder (' .$this->targetPathBase.$this->targetPathFrag. ') ');
                exit('ABORT: unable to create new folder (' .$this->targetPathBase.$this->targetPathFrag. ')');
            }

			if($this->keepOrig){
				$this->origPathFrag = 'orig/'.date('Ym').'/';
				if(!file_exists($this->targetPathBase . $this->targetPathFrag . 'orig/') && !mkdir($concurrentDirectory = $this->targetPathBase . $this->targetPathFrag . 'orig/') && !is_dir($concurrentDirectory)) {
					$this->logOrEcho('NOTICE: unable to create base folder to store original files (' .$this->targetPathBase.$this->targetPathFrag. ') ');
				}
				if(file_exists($this->targetPathBase . $this->targetPathFrag . 'orig/') && !file_exists($this->targetPathBase . $this->targetPathFrag . $this->origPathFrag) && !mkdir($concurrentDirectory = $this->targetPathBase . $this->targetPathFrag . $this->origPathFrag) && !is_dir($concurrentDirectory)) {
					$this->logOrEcho('NOTICE: unable to create folder to store original files (' .$this->targetPathBase.$this->targetPathFrag.$this->origPathFrag. ') ');
				}
			}

			$this->logOrEcho('Starting image processing: '.$sourcePathFrag);
			if(strpos($this->sourcePathBase, 'http') === 0){
				$this->processHtml($sourcePathFrag);
			}
			else if($this->errorMessage === 'abort' && !$this->processFolder($sourcePathFrag)) {
				$this->errorMessage = '';
				continue;
			}
			if(!$this->dbMetadata){
				if($this->mdOutputFH) {
					fclose($this->mdOutputFH);
				}
				if(array_key_exists('email', $cArr) && $cArr['email']) {
					$this->sendMetadata($cArr['email'], $mdFileName);
				}
			}
			$this->logOrEcho('Done uploading '.$sourcePathFrag.' ('.date('Y-m-d h:i:s A').')');
		}
		if($this->collProcessedArr){
			$this->updateCollectionStats();
		}
		
		$this->logOrEcho('Image upload process finished! (' .date('Y-m-d h:i:s A').") \n");
		if($this->logMode === 1){
			echo '</ul>';
		}
	}

	private function processFolder($pathFrag = ''): ?bool
	{
		set_time_limit(3600);
		if(file_exists($this->sourcePathBase.$pathFrag)){
			if($dirFH = opendir($this->sourcePathBase.$pathFrag)){
				while($fileName = readdir($dirFH)){
					if($fileName !== '.' && $fileName !== '..' && $fileName !== '.svn'){
						if(is_file($this->sourcePathBase.$pathFrag.$fileName)){
							if(!stripos($fileName,$this->tnSourceSuffix.'.jpg') && !stripos($fileName,$this->lgSourceSuffix.'.jpg')){
								$this->logOrEcho('Processing File (' .date('Y-m-d h:i:s A'). '): ' .$fileName);
								$fileExt = strtolower(substr($fileName,strrpos($fileName,'.')));
								if($fileExt === '.jpg'){
									if($this->processImageFile($fileName,$pathFrag)){
										if(!in_array($this->activeCollid, $this->collProcessedArr, true)) {
											$this->collProcessedArr[] = $this->activeCollid;
										}
									}
									else if($this->errorMessage === 'abort') {
										return false;
									}
								}
								elseif($fileExt === '.tif'){
									$this->logOrEcho('ERROR: File skipped, TIFFs image files are not a supported: ' .$fileName,1);
								}
								elseif(($fileExt === '.csv' || $fileExt === '.txt' || $fileExt === '.tab' || $fileExt === '.dat')){
									if($this->skeletalFileProcessing){
										$this->processSkeletalFile($this->sourcePathBase.$pathFrag.$fileName);
										if(!in_array($this->activeCollid, $this->collProcessedArr, true)) {
											$this->collProcessedArr[] = $this->activeCollid;
										}
									}
								}
								elseif($fileExt === '.xml') {
                                    if (!in_array("$pathFrag$fileName", $this->processedFiles, true)) {
										$this->processedFiles[] = "$pathFrag$fileName";
									}
									if(!in_array($this->activeCollid, $this->collProcessedArr, true)) {
										$this->collProcessedArr[] = $this->activeCollid;
									}
								}
								elseif($fileExt === '.ds_store' || strtolower($fileName) === 'thumbs.db'){
									unlink($this->sourcePathBase.$pathFrag.$fileName);
								}
								else{
									$this->logOrEcho('ERROR: File skipped, not a supported image file: ' .$fileName,1);
								}
							}
						}
						elseif(is_dir($this->sourcePathBase.$pathFrag.$fileName)){
							$this->processFolder($pathFrag.$fileName. '/');
						}
					}
				}
				if($dirFH) {
					closedir($dirFH);
				}
			}
			else{
				$this->logOrEcho('ERROR: unable to access source directory: ' .$this->sourcePathBase.$pathFrag,1);
			}
		}
		else{
			$this->logOrEcho('Source path does not exist: ' .$this->sourcePathBase.$pathFrag,1);
		}
		return true;
	}

	private function processHtml($pathFrag = ''): ?bool
	{
		set_time_limit(3600);
		$headerArr = get_headers($this->sourcePathBase.$pathFrag);
		preg_match('/http.+\s(\d{3})\s/i',$headerArr[0],$codeArr);
		if($codeArr[1] === 200){
			$dom = new DOMDocument();
			$dom->loadHTMLFile($this->sourcePathBase.$pathFrag);
			$aNodes= $dom->getElementsByTagName('a');
			$skipAnchors = array('Name','Last modified','Size','Description','Parent Directory');
			foreach( $aNodes as $aNode ) {
				$fileName = trim($aNode->nodeValue);
				if(!in_array($fileName, $skipAnchors, true)){
					$fileExt = '';
					if(strrpos($fileName,'.')) {
						$fileExt = strtolower(substr($fileName, strrpos($fileName, '.') + 1));
					}
					if($fileExt){
						if(!stripos($fileName,$this->tnSourceSuffix.'.jpg') && !stripos($fileName,$this->lgSourceSuffix.'.jpg')){
							$this->logOrEcho('Processing File (' .date('Y-m-d h:i:s A'). '): ' .$fileName);
							if($fileExt === 'jpg'){
								if($this->processImageFile($fileName,$pathFrag)){
									if(!in_array($this->activeCollid, $this->collProcessedArr, true)) {
										$this->collProcessedArr[] = $this->activeCollid;
									}
								}
								else if($this->errorMessage === 'abort') {
									return false;
								}
							}
							elseif($fileExt === 'tif'){
								$this->logOrEcho('ERROR: File skipped, TIFFs image files are not a supported: ' .$fileName,1);
							}
							elseif(($fileExt === 'csv' || $fileExt === 'txt' || $fileExt === 'tab' || $fileExt === 'dat')){
								if($this->skeletalFileProcessing){
									$this->processSkeletalFile($this->sourcePathBase.$pathFrag.$fileName);
									if(!in_array($this->activeCollid, $this->collProcessedArr, true)) {
										$this->collProcessedArr[] = $this->activeCollid;
									}
								}
							}
							elseif($fileExt === 'xml') {
								if(!in_array($this->activeCollid, $this->collProcessedArr, true)) {
									$this->collProcessedArr[] = $this->activeCollid;
								}
							}
							else{
								$this->logOrEcho('ERROR: File skipped, not a supported image file: ' .$fileName,1);
							}
						}
					}
					elseif(stripos($fileName,'Parent Dir') === false){
						$this->logOrEcho('New dir path: '.$this->sourcePathBase.$pathFrag.$fileName.'<br/>');
						if(substr($fileName,-1) !== '/') {
							$fileName .= '/';
						}
						$this->processHtml($pathFrag.$fileName);
					}
				}
			}
		}
		else{
			$this->logOrEcho('Source directory skipped (code ' .$codeArr[0]. ') : ' .$this->sourcePathBase.$pathFrag,1);
		}
		return true;
	}

	private function processImageFile($fileName,$sourcePathFrag = ''): bool
	{
		flush();
		if($specPk = $this->getPrimaryKey($fileName)){
			$occId = 0;
			if($this->dbMetadata){
				$occId = $this->getOccId($specPk);
			}
			$targetFileName = str_replace(' ','_',$fileName);
			$fileName = rawurlencode($fileName);
			$fileNameExt = '.jpg';
			$fileNameBase = $fileName;
			if($p = strrpos($fileName,'.')){
				$fileNameExt = substr($fileName,$p);
				$fileNameBase = substr($fileName,0,$p);
				if($this->webSourceSuffix){
					$fileNameBase = substr($fileNameBase,0,-1*strlen($this->webSourceSuffix));
				}
			}
			if($occId || !$this->dbMetadata){
				$sourcePath = $this->sourcePathBase.$sourcePathFrag;
				$targetFolder = '';
				if(strlen($specPk) > 3){
					$folderName = $specPk;
					if(preg_match('/^(\D*\d+)\D+/',$folderName,$m)){
						$folderName = $m[1];
					}
					$targetFolder = substr($folderName,0, -3);
					$targetFolder = str_replace(array('.','\\','/','#',' '),'',$targetFolder).'/';
					if($targetFolder && strlen($targetFolder) < 6 && is_numeric($targetFolder[0])){
						$targetFolder = str_repeat('0',6-strlen($targetFolder)).$targetFolder;
					}
				}
				if(!$targetFolder) {
					$targetFolder = date('Ym') . '/';
				}
				$targetFrag = $this->targetPathFrag.$targetFolder;
				$targetPath = $this->targetPathBase.$targetFrag;
				if(!file_exists($targetPath) && !mkdir($targetPath) && !is_dir($targetPath)) {
					$this->logOrEcho('ERROR: unable to create new folder (' .$targetPath. ') ');
				}
				if($this->webImg === 1 || $this->webImg === 2){
					if(file_exists($targetPath.$targetFileName)){
						if($this->imgExists === 2){
							unlink($targetPath.$targetFileName);
							if(file_exists($targetPath.substr($targetFileName,0, -4). 'tn.jpg')){
								unlink($targetPath.substr($targetFileName,0, -4). 'tn.jpg');
							}
							if(file_exists($targetPath.substr($targetFileName,0, -4). '_tn.jpg')){
								unlink($targetPath.substr($targetFileName,0, -4). '_tn.jpg');
							}
							if(file_exists($targetPath.substr($targetFileName,0, -4). 'lg.jpg')){
								unlink($targetPath.substr($targetFileName,0, -4). 'lg.jpg');
							}
							if(file_exists($targetPath.substr($targetFileName,0, -4). '_lg.jpg')){
								unlink($targetPath.substr($targetFileName,0, -4). '_lg.jpg');
							}
						}
						elseif($this->imgExists === 1){
							$cnt = 1;
							$tempFileName = $targetFileName;
							while(file_exists($targetPath.$targetFileName)){
								$targetFileName = str_ireplace('.jpg', '_' .$cnt. '.jpg',$tempFileName);
								$cnt++;
							}
						}
						else{
							$this->logOrEcho('NOTICE: image import skipped because image file already exists ',1);
							return false;
						}
					}
				}
				elseif($this->webImg === 3){
					if(!$this->imgExists){
						$recExists = 0;
						$sql = 'SELECT url '.
							'FROM images WHERE (occid = '.$occId.') ';
						$rs = $this->conn->query($sql);
						while($r = $rs->fetch_object()){
							if(stripos($r->url,$fileName) || stripos($r->url,str_replace('%20', '_', $fileName)) || stripos($r->url,str_replace('%20', ' ', $fileName))){
								$recExists = 1;
							}
						}
						$rs->free();
						if($recExists){
							$this->logOrEcho('NOTICE: image import skipped because specimen record already exists ',1);
							return false;
						}
					}
				}
				[$width, $height] = getimagesize($sourcePath . $fileName);
				if($width && $height){
					if(strpos($sourcePath, 'http://') === 0 || strpos($sourcePath, 'https://') === 0) {
						$x = array_change_key_case(get_headers($sourcePath.$fileName, 1),CASE_LOWER); 
						if ( strcasecmp($x[0], 'HTTP/1.1 200 OK') !== 0 ) {
							$fileSize = $x['content-length'][1]; 
						}
 						else { 
 							$fileSize = $x['content-length']; 
 						}
					} 
					else { 
						$fileSize = @filesize($sourcePath.$fileName);
					}
					
					$webUrlFrag = '';
					if($this->webImg){
						if($this->webImg === 1){
							if($fileSize < $this->webFileSizeLimit && $width < ($this->webPixWidth*2)){
								if(copy($sourcePath.$fileName,$targetPath.$targetFileName)){
									$webUrlFrag = $this->imgUrlBase.$targetFrag.$targetFileName;
									$this->logOrEcho('Source image imported as web image (' .date('Y-m-d h:i:s A'). ') ',1);
								}
							}
							else if($this->createNewImage($sourcePath.$fileName,$targetPath.$targetFileName,$this->webPixWidth,round($this->webPixWidth*$height/$width),$width,$height)){
								$webUrlFrag = $this->imgUrlBase.$targetFrag.$targetFileName;
								$this->logOrEcho('Web image created from source image (' .date('Y-m-d h:i:s A'). ') ',1);
							}
						}
						elseif($this->webImg === 2){
							$webFileName = $fileNameBase.$this->webSourceSuffix.$fileNameExt;
							if(copy($sourcePath.$webFileName,$targetPath.$targetFileName)){
								$webUrlFrag = $this->imgUrlBase.$targetFrag.$targetFileName;
								$this->logOrEcho('Source image imported as web image (' .date('Y-m-d h:i:s A'). ') ',1);
							}
						}
						elseif($this->webImg === 3){
							$webFileName = $fileNameBase.$this->webSourceSuffix.$fileNameExt;
							$webUrlFrag = $sourcePath.$webFileName;
							$this->logOrEcho('Source used as web image (' .date('Y-m-d h:i:s A'). ') ',1);
						}
					}
					if(!$webUrlFrag){
						$this->logOrEcho('Failed to create web image ',1);
					}
					$lgUrlFrag = '';
					if($this->lgImg){
						$lgTargetFileName = substr($targetFileName,0,-4). '_lg.jpg';
						if($this->lgImg === 1){
							if($width > ($this->webPixWidth*1.3)){
								if($width > $this->lgPixWidth || ($fileSize && $fileSize > $this->lgFileSizeLimit)){
									if($this->createNewImage($sourcePath.$fileName,$targetPath.$lgTargetFileName,$this->lgPixWidth,round($this->lgPixWidth*$height/$width),$width,$height)){
										$lgUrlFrag = $this->imgUrlBase.$targetFrag.$lgTargetFileName;
										$this->logOrEcho('Resized source as large derivative (' .date('Y-m-d h:i:s A'). ') ',1);
									}
								}
								else if(copy($sourcePath.$fileName,$targetPath.$lgTargetFileName)){
									$lgUrlFrag = $this->imgUrlBase.$targetFrag.$lgTargetFileName;
									$this->logOrEcho('Imported source as large derivative (' .date('Y-m-d h:i:s A'). ') ',1);
								}
								else{
									$this->logOrEcho('WARNING: unable to import large derivative (' .$sourcePath.$fileName. ') ',1);
								}
							}
						}
						elseif($this->lgImg === 2){
							$lgUrlFrag = $sourcePath.$fileName;
							$this->logOrEcho('Used source as large derivative (' .date('Y-m-d h:i:s A'). ') ',1);
						}
						elseif($this->lgImg === 3){
							$lgSourceFileName = $fileNameBase.$this->lgSourceSuffix.$fileNameExt;
							if($this->uriExists($sourcePath.$lgSourceFileName)){
								if(copy($sourcePath.$lgSourceFileName,$targetPath.$lgTargetFileName)){
									if(strpos($sourcePath, 'http') !== 0) {
										unlink($sourcePath . $lgSourceFileName);
									}
									$lgUrlFrag = $this->imgUrlBase.$targetFrag.$lgTargetFileName;
									$this->logOrEcho('Imported large derivative of source for large version(' .date('Y-m-d h:i:s A'). ') ',1);
								}
							}
							else{
								$this->logOrEcho('WARNING: unable to import large derivative (' .$sourcePath.$lgSourceFileName. ') ',1);
							}
						}
						elseif($this->lgImg === 4){
							$lgSourceFileName = $fileNameBase.$this->lgSourceSuffix.$fileNameExt;
							if($this->uriExists($sourcePath.$lgSourceFileName)){
								$lgUrlFrag = $sourcePath.$lgSourceFileName;
								$this->logOrEcho('Large version mapped to large derivative of source (' .date('Y-m-d h:i:s A'). ') ',1);
							}
							else{
								$this->logOrEcho('WARNING: unable to map to large derivative (' .$sourcePath.$lgSourceFileName. ') ',1);
							}
						}
					}
					$tnUrlFrag = '';
					if($this->tnImg){
						$tnTargetFileName = substr($targetFileName,0,-4). '_tn.jpg';
						if($this->tnImg === 1){
							if($this->createNewImage($sourcePath.$fileName,$targetPath.$tnTargetFileName,$this->tnPixWidth,round($this->tnPixWidth*$height/$width),$width,$height)){
								$tnUrlFrag = $this->imgUrlBase.$targetFrag.$tnTargetFileName;
								$this->logOrEcho('Created thumbnail from source (' .date('Y-m-d h:i:s A'). ') ',1);
							}
						}
						elseif($this->tnImg === 2){
							$tnFileName = $fileNameBase.$this->tnSourceSuffix.$fileNameExt;
							if($this->uriExists($sourcePath.$tnFileName)){
								rename($sourcePath.$tnFileName,$targetPath.$tnTargetFileName);
							}
							$tnUrlFrag = $this->imgUrlBase.$targetFrag.$tnTargetFileName;
							$this->logOrEcho('Imported source as thumbnail (' .date('Y-m-d h:i:s A'). ') ',1);
						}
						elseif($this->tnImg === 3){
							$tnFileName = $fileNameBase.$this->tnSourceSuffix.$fileNameExt;
							if($this->uriExists($sourcePath.$tnFileName)){
								$tnUrlFrag = $sourcePath.$tnFileName;
								$this->logOrEcho('Thumbnail is map of source thumbnail (' .date('Y-m-d h:i:s A'). ') ',1);
							}
						}
					}
					
					if($this->sourceGdImg){
						imagedestroy($this->sourceGdImg);
						$this->sourceGdImg = null;
					}
					if($this->sourceImagickImg){
						$this->sourceImagickImg->clear();
						$this->sourceImagickImg = null;
					}
					$this->recordImageMetadata(($this->dbMetadata?$occId:$specPk),$webUrlFrag,$tnUrlFrag,$lgUrlFrag);
					if(file_exists($sourcePath.$fileName)){
						if($this->keepOrig){
							if(file_exists($this->targetPathBase.$this->targetPathFrag.$this->origPathFrag)){
								rename($sourcePath.$fileName,$this->targetPathBase.$this->targetPathFrag.$this->origPathFrag.$fileName. '.orig');
							}
						} else {
							unlink($sourcePath.$fileName);
						}
					}
					$this->logOrEcho('Image processed successfully (' .date('Y-m-d h:i:s A'). ')!',1);
				}
				else{
					$this->logOrEcho('File skipped (' .$sourcePath.$fileName. '), unable to obtain dimensions of original image',1);
					return false;
				}
			}
		}
		else{
			$this->logOrEcho('File skipped (' .$sourcePathFrag.$fileName. '), unable to extract specimen identifier',1);
			return false;
		}
		flush();
		return true;
	}

	private function createNewImage($sourcePathBase, $targetPath, $newWidth, $newHeight, $sourceWidth, $sourceHeight): bool
	{
		if($this->processUsingImageMagick) {
			$status = $this->createNewImageImagick($sourcePathBase,$targetPath,$newWidth);
		} 
		elseif(function_exists('gd_info') && extension_loaded('gd')) {
			$status = $this->createNewImageGD($sourcePathBase,$targetPath,$newWidth,$newHeight,$sourceWidth,$sourceHeight);
		}
		else{
			$this->logOrEcho('FATAL ERROR: No appropriate image handler for image conversions',1);
			exit('ABORT: No appropriate image handler for image conversions');
		}
		return $status;
	}
	
	private function createNewImageImagick($sourceImg,$targetPath,$newWidth): bool
	{
		$status = false;
		if($newWidth < 300){
			system('convert '.$sourceImg.' -thumbnail '.$newWidth.'x'.($newWidth*1.5).' '.$targetPath, $retval);
		}
		else{
			system('convert '.$sourceImg.' -resize '.$newWidth.'x'.($newWidth*1.5).($this->jpgQuality?' -quality '.$this->jpgQuality:'').' '.$targetPath, $retval);
		}
		if(file_exists($targetPath)){
			$status = true;
		}
		return $status;
	}
	
	private function createNewImageGD($sourcePathBase, $targetPath, $newWidth, $newHeight, $sourceWidth, $sourceHeight): bool
	{
		$status = false;
		if(!$this->sourceGdImg){
			$this->sourceGdImg = imagecreatefromjpeg($sourcePathBase);
		}
		if(!$newWidth || !$newHeight){
			$this->logOrEcho('ERROR: Unable to create image because new width or height is not set (w:' .$newWidth.' h:'.$newHeight.')');
			return $status;
		}
		$tmpImg = imagecreatetruecolor($newWidth,$newHeight);
		imagecopyresized($tmpImg,$this->sourceGdImg,0,0,0,0,$newWidth,$newHeight,$sourceWidth,$sourceHeight);

		if($this->jpgQuality){
			$status = imagejpeg($tmpImg, $targetPath, $this->jpgQuality);
		}
		else{
			$status = imagejpeg($tmpImg, $targetPath);
		}
		
		if(!$status){
			$this->logOrEcho('ERROR: Unable to resize and write file: ' .$targetPath,1);
		}
		
		imagedestroy($tmpImg);
		return $status;
	}
	
	private function getPrimaryKey($str){
		$specPk = '';
		if(isset($this->collArr[$this->activeCollid]['pmterm'])){
			$pmTerm = $this->collArr[$this->activeCollid]['pmterm'];
			if(strpos($pmTerm, '/') !== 0 || strpos(substr($pmTerm,-3),'/') === false){
				$this->logOrEcho('PROCESS ABORTED: Regular Expression term illegal due to missing forward slashes delimiting the term: ' .$pmTerm,1);
				$this->errorMessage = 'abort';
				return false;
			}
			if(!strpos($pmTerm,'(') || !strpos($pmTerm,')')){
				$this->logOrEcho('PROCESS ABORTED: Regular Expression term illegal due to missing capture term: ' .$pmTerm,1);
				$this->errorMessage = 'abort';
				return false;
			}
			if(preg_match($pmTerm,$str,$matchArr)){
				if(array_key_exists(1,$matchArr) && $matchArr[1]){
					$specPk = $matchArr[1];
				}
				if (isset($this->collArr[$this->activeCollid]['prpatt'])) { 				
					$specPk = preg_replace($this->collArr[$this->activeCollid]['prpatt'],$this->collArr[$this->activeCollid]['prrepl'],$specPk);
				}
				if(isset($matchArr[2])){
					$this->webSourceSuffix = $matchArr[2];
				}
			}
		}
		return $specPk;
	}

	private function getOccId($specPk){
		$occId = 0;
		if($this->matchCatalogNumber){
			$sql = 'SELECT occid FROM omoccurrences '.
				'WHERE (catalognumber IN("'.$specPk.'"'.(strpos($specPk, '0') === 0 ?',"'.ltrim($specPk,'0 ').'"':'').')) '.
				'AND (collid = '.$this->activeCollid.')';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$occId = $row->occid;
			}
			$rs->free();
		}
		if($this->matchOtherCatalogNumbers){
			$sql = 'SELECT occid FROM omoccurrences '.
				'WHERE (othercatalognumbers IN("'.$specPk.'"'.(strpos($specPk, 0) === '0' ?',"'.ltrim($specPk,'0 ').'"':'').')) '.
				'AND (collid = '.$this->activeCollid.')';
			$rs = $this->conn->query($sql);
			if($row = $rs->fetch_object()){
				$occId = $row->occid;
			}
			$rs->free();
		}
		if(!$occId && $this->createNewRec){
			$sql2 = 'INSERT INTO omoccurrences(collid,'.($this->matchCatalogNumber?'catalognumber':'othercatalognumbers').',processingstatus,dateentered) '.
				'VALUES('.$this->activeCollid.',"'.$specPk.'","unprocessed","'.date('Y-m-d H:i:s').'")';
			if($this->conn->query($sql2)){
				$occId = $this->conn->insert_id;
				$this->logOrEcho("Specimen record does not exist; new empty specimen record created and assigned an 'unprocessed' status (occid = ".$occId. ') ',1);
			}
			else{
				$this->logOrEcho('ERROR creating new occurrence record: ' .$this->conn->error,1);
			}
		}
		if(!$occId){
			$this->logOrEcho('ERROR: File skipped, unable to locate specimen record ' .$specPk. ' (' .date('Y-m-d h:i:s A'). ') ',1);
		}
		return $occId;
	}

	private function recordImageMetadata($specID,$webUrl,$tnUrl,$oUrl){
		if($this->dbMetadata){
			$status = $this->databaseImage($specID,$webUrl,$tnUrl,$oUrl);
		}
		else{
			$status = $this->writeMetadataToFile($specID,$webUrl,$tnUrl,$oUrl);
		}
		return $status;
	}
	
	private function databaseImage($occId,$webUrl,$tnUrl,$oUrl): bool
	{
		$status = true;
		if($occId && is_numeric($occId)){
			$this->logOrEcho('Preparing to load record into database',1);
			$imgId = 0;
			$sql = 'SELECT imgid, url, thumbnailurl, originalurl '.
				'FROM images WHERE (occid = '.$occId.') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if(strcasecmp($r->url,$webUrl) === 0){
					if(!$this->conn->query('DELETE FROM specprocessorrawlabels WHERE imgid = '.$r->imgid)){
						$this->logOrEcho('ERROR deleting OCR for image record #'.$r->imgid.' (equal URLs): '.$this->conn->error,1);
					}
					if(!$this->conn->query('DELETE FROM images WHERE imgid = '.$r->imgid)){
						$this->logOrEcho('ERROR deleting image record #'.$r->imgid.' (equal URLs): '.$this->conn->error,1);
					}
				}
				elseif($this->imgExists === 2 && strcasecmp(basename($r->url),basename($webUrl)) === 0){
					if(!$this->conn->query('DELETE FROM specprocessorrawlabels WHERE imgid = '.$r->imgid)){
						$this->logOrEcho('ERROR deleting OCR for image record #'.$r->imgid.' (equal basename): '.$this->conn->error,1);
					}
					if($this->conn->query('DELETE FROM images WHERE imgid = '.$r->imgid)){
						$urlPath = parse_url($r->url, PHP_URL_PATH);
						if($urlPath && strpos($urlPath, $this->imgUrlBase) === 0){
							$wFile = str_replace($this->imgUrlBase,$this->targetPathBase,$urlPath);
							if(file_exists($wFile) && is_writable($wFile)) {
								unlink($wFile);
							}
						}
						$urlTnPath = parse_url($r->thumbnailurl, PHP_URL_PATH);
						if($urlTnPath && strpos($urlTnPath, $this->imgUrlBase) === 0){
							$wFile = str_replace($this->imgUrlBase,$this->targetPathBase,$urlTnPath);
							if(file_exists($wFile) && is_writable($wFile)) {
								unlink($wFile);
							}
						}
						$urlLgPath = parse_url($r->url, PHP_URL_PATH);
						if($urlLgPath && strpos($urlLgPath, $this->imgUrlBase) === 0){
							$wFile = str_replace($this->imgUrlBase,$this->targetPathBase,$urlLgPath);
							if(file_exists($wFile) && is_writable($wFile)) {
								unlink($wFile);
							}
						}
					}
					else{
						$this->logOrEcho('ERROR: Unable to delete image record #'.$r->imgid.' (equal basename): '.$this->conn->error,1);
					}
				}
			}
			$rs->free();

			$sql1 = 'INSERT INTO images(occid,url';
			$sql2 = 'VALUES ('.$occId.',"'.$webUrl.'"';
			if($tnUrl){
				$sql1 .= ',thumbnailurl';
				$sql2 .= ',"'.$tnUrl.'"';
			}
			if($oUrl){
				$sql1 .= ',originalurl';
				$sql2 .= ',"'.$oUrl.'"';
			}
			$sql1 .= ',imagetype,owner,format) ';
			$sql2 .= ',"specimen","'.$this->collArr[$this->activeCollid]['collname'].'","image/jpeg")';
			$sql = $sql1.$sql2;
			if($sql){
				if($this->conn->query($sql)){
					$this->dataLoaded = 1;
				}
				else{
					$status = false;
					$this->logOrEcho('ERROR: Unable to load image record into database: ' .$this->conn->error. '; SQL: ' .$sql,1);
				}
				if($imgId){
					$this->logOrEcho("WARNING: Existing image record replaced; occid: $occId ",1);
				}
				else{
					$this->logOrEcho('SUCCESS: Image record loaded into database',1);
				}
			}
		}
		else{
			$status = false;
			$this->logOrEcho('ERROR: Missing occid (omoccurrences PK), unable to load record ');
		}
		flush();
		return $status;
	}
	
	private function writeMetadataToFile($specPk,$webUrl,$tnUrl,$oUrl){
		$status = true;
		if($this->mdOutputFH){
			$status = fwrite($this->mdOutputFH, $this->activeCollid.',"'.$specPk.'","'.$webUrl.'","'.$tnUrl.'","'.$oUrl.'"'."\n");
		}
		return $status;
	}
	
	private function processSkeletalFile($filePath): ?bool
	{
		$this->logOrEcho('Preparing to load Skeletal file into database',1);
		$fh = fopen($filePath, 'rb');
		if($fh){
			$fileExt = substr($filePath,-4);
			if($fileExt === '.csv'){
				$hArr = fgetcsv($fh);
				$delimiter = 'csv';
			}
			elseif($fileExt === '.tab'){
				$headerStr = fgets($fh);
				$hArr = explode("\t",$headerStr);
				$delimiter = "\t";
			}
			elseif($fileExt === '.dat' || $fileExt === '.txt'){
				$headerStr = fgets($fh);
				if(strpos($headerStr,"\t") !== false){
					$hArr = explode("\t",$headerStr);
					$delimiter = "\t";
				}
				elseif(strpos($headerStr, '|') !== false){
					$hArr = explode('|',$headerStr);
					$delimiter = '|';
				}
				elseif(strpos($headerStr, ',') !== false){
					rewind($fh);
					$hArr = fgetcsv($fh);
					$delimiter = 'csv';
				}
				else{
					$this->logOrEcho('ERROR: Unable to identify delimiter for metadata file ',1);
					return false;
				}
			}
			else{
				$this->logOrEcho('ERROR: Skeletal file skipped: unable to determine file type ',1);
				return false;
			}
			if($hArr){
				$headerArr = array();
				foreach($hArr as $field){
					$fieldStr = strtolower(trim($field));
					if($fieldStr === 'exsnumber') {
						$fieldStr = 'exsiccatinumber';
					}
					if($fieldStr){
						$headerArr[] = $fieldStr;
					}
					else{
						break;
					}
				}

				$symbMap = array();
				if(in_array('catalognumber', $headerArr, true)){
					$sqlMap = 'SHOW COLUMNS FROM omoccurrences';
					$rsMap = $this->conn->query($sqlMap);
					while($rMap = $rsMap->fetch_object()){
						$field = strtolower($rMap->Field);
						if(in_array($field, $headerArr, true)){
							$type = $rMap->Type;
							if(strpos($type, 'double') !== false || strpos($type, 'int') !== false || strpos($type, 'decimal') !== false){
								$symbMap[$field]['type'] = 'numeric';
							}
							elseif(strpos($type, 'date') !== false){
								$symbMap[$field]['type'] = 'date';
							}
							else{
								$symbMap[$field]['type'] = 'string';
								if(preg_match('/\(\d+\)$/', $type, $matches)){
									$symbMap[$field]['size'] = substr($matches[0],1, -1);
								}
							}
						}
					}
					unset($symbMap['datelastmodified'], $symbMap['occid'], $symbMap['collid'], $symbMap['catalognumber'], $symbMap['institutioncode'], $symbMap['collectioncode'], $symbMap['dbpk'], $symbMap['processingstatus'], $symbMap['observeruid'], $symbMap['tidinterpreted']);

					$symbMap['ometid']['type'] = 'numeric';
					$symbMap['exsiccatititle']['type'] = 'string';
					$symbMap['exsiccatititle']['size'] = 150;
					$symbMap['exsiccatinumber']['type'] = 'string';
					$symbMap['exsiccatinumber']['size'] = 45;
					$exsiccatiTitleMap = array();

					while($recordArr = $this->getRecordArr($fh,$delimiter)){
						$catNum = 0;
						$recMap = array();
						foreach($headerArr as $k => $hStr){
							if($hStr === 'catalognumber') {
								$catNum = $recordArr[$k];
							}
							if(array_key_exists($hStr,$symbMap)){
								$valueStr = '';
								if(array_key_exists($k,$recordArr)) {
									$valueStr = $recordArr[$k];
								}
								if($valueStr){
									if(strpos($valueStr, '"') === 0 && substr($valueStr,-1) === '"'){
										$valueStr = substr($valueStr,1, -1);
									}
									$valueStr = trim($valueStr);
									if($valueStr) {
										$recMap[$hStr] = $valueStr;
									}
								}
							}
						}

						if((!array_key_exists('sciname',$recMap) || !$recMap['sciname'])){
							if(array_key_exists('genus',$recMap) && $recMap['genus']){
								$sn = $recMap['genus'];
								if(array_key_exists('specificepithet',$recMap) && $recMap['specificepithet']) {
									$sn .= ' ' . $recMap['specificepithet'];
								}
								if(array_key_exists('taxonrank',$recMap) && $recMap['taxonrank']) {
									$sn .= ' ' . $recMap['taxonrank'];
								}
								if(array_key_exists('infraspecificepithet',$recMap) && $recMap['infraspecificepithet']) {
									$sn .= ' ' . $recMap['infraspecificepithet'];
								}
								$recMap['sciname'] = $sn;
							}
							elseif(array_key_exists('scientificname',$recMap) && $recMap['scientificname']){
								$recMap['sciname'] = $this->formatScientificName($recMap['scientificname']);
							}
							if(array_key_exists('sciname',$recMap)){
								$symbMap['sciname']['type'] = 'string';
								$symbMap['sciname']['size'] = 255;
							}
						}
						
						if(!array_key_exists('eventdate',$recMap) || !$recMap['eventdate']){
							if(array_key_exists('verbatimeventdate',$recMap) && $recMap['verbatimeventdate']){
								$dateStr = $this->formatDate($recMap['verbatimeventdate']); 
								if($dateStr){
									$recMap['eventdate'] = $dateStr;
									if($dateStr === $recMap['verbatimeventdate']) {
										unset($recMap['verbatimeventdate']);
									}
									if(!array_key_exists('eventdate',$symbMap)){
										$symbMap['eventdate']['type'] = 'date';
									}
								}
							}
						}
						
						if(array_key_exists('exsiccatinumber',$recMap) && $recMap['exsiccatinumber']){
							if(array_key_exists('exsiccatititle',$recMap) && $recMap['exsiccatititle'] && (!array_key_exists('ometid',$recMap) || !$recMap['ometid'])){
								if(array_key_exists($recMap['exsiccatititle'],$exsiccatiTitleMap)){
									$recMap['ometid'] = $exsiccatiTitleMap[$recMap['exsiccatititle']];
								}
								else{
									$titleStr = trim($this->conn->real_escape_string($recMap['exsiccatititle']));
									$sql = 'SELECT ometid FROM omexsiccatititles '.
										'WHERE (title = "'.$titleStr.'") OR (abbreviation = "'.$titleStr.'")';
									$rs = $this->conn->query($sql);
									if($r = $rs->fetch_object()){
										$recMap['ometid'] = $r->ometid;
										$exsiccatiTitleMap[$recMap['exsiccatititle']] = $r->ometid;
									}
									$rs->free();
								}
							}
							if(array_key_exists('ometid',$recMap) && $recMap['ometid']){
								$numStr = trim($this->conn->real_escape_string($recMap['exsiccatinumber']), ' #num');
								$sql = 'SELECT omenid FROM omexsiccatinumbers '.
									'WHERE ometid = ('.$recMap['ometid'].') AND (exsnumber = "'.$numStr.'")';
								$rs = $this->conn->query($sql);
								if($r = $rs->fetch_object()){
									$recMap['omenid'] = $r->omenid;
								}
								$rs->free();
								if(!array_key_exists('omenid',$recMap)){
									$sql = 'INSERT INTO omexsiccatinumbers(ometid,exsnumber) '.
										'VALUES('.$recMap['ometid'].',"'.$numStr.'")';
									if($this->conn->query($sql)) {
										$recMap['omenid'] = $this->conn->insert_id;
									}
								}
							}
						}
						if(array_key_exists('exsiccatititle',$recMap) && $recMap['exsiccatititle'] && (!array_key_exists('ometid',$recMap) || !$recMap['ometid'])){
							$exsStr = $recMap['exsiccatititle'];
							if(array_key_exists('exsiccatinumber',$recMap) && $recMap['exsiccatinumber']){
								$exsStr .= ', '.$recMap['exsiccatinumber'].'; ';
							}
							$occRemarks = $recMap['occurrenceremarks'];
							if($occRemarks) {
								$occRemarks .= '; ';
							}
							$recMap['occurrenceremarks'] = $occRemarks.$exsStr;
						}
						
						if($catNum){
							$occid = 0;
							$deltaCatNum = $this->getPrimaryKey($catNum);
							if($deltaCatNum !== '') {
								$catNum = $deltaCatNum;
							}
		
							$activeFields = array_keys($recMap);
							if(in_array('ometid', $activeFields, true)) {
								unset($activeFields[array_search('ometid', $activeFields, true)]);
							}
							if(in_array('omenid', $activeFields, true)) {
								unset($activeFields[array_search('omenid', $activeFields, true)]);
							}
							if(in_array('exsiccatititle', $activeFields, true)) {
								unset($activeFields[array_search('exsiccatititle', $activeFields, true)]);
							}
							if(in_array('exsiccatinumber', $activeFields, true)) {
								unset($activeFields[array_search('exsiccatinumber', $activeFields, true)]);
							}
							
							$termArr = array();
							if($this->matchCatalogNumber) {
								$termArr[] = '(catalognumber IN("' . $catNum . '"' . (strpos($catNum, '0') === 0 ? ',"' . ltrim($catNum, '0 ') . '"' : '') . '))';
							}
							if($this->matchOtherCatalogNumbers) {
								$termArr[] = '(othercatalognumbers IN("' . $catNum . '"' . (strpos($catNum, '0') === 0 ? ',"' . ltrim($catNum, '0 ') . '"' : '') . '))';
							}
							if($termArr){
								$sql = 'SELECT occid'.(!array_key_exists('occurrenceremarks',$recMap)?',occurrenceremarks':'').
									($activeFields?','.implode(',',$activeFields):'').' '.
									'FROM omoccurrences '.
									'WHERE (collid = '.$this->activeCollid.') AND ('.implode(' OR ', $termArr).')';
								//echo $sql;
								$rs = $this->conn->query($sql);
								if($r = $rs->fetch_assoc()){
									$occid = $r['occid'];
									if($activeFields){
										$updateValueArr = array();
										$occRemarkArr = array();
										foreach($activeFields as $activeField){
											$activeValue = $this->cleanString($recMap[$activeField]);
											if(!trim($r[$activeField])){
												$type = (array_key_exists('type',$symbMap[$activeField])?$symbMap[$activeField]['type']:'string');
												$size = (array_key_exists('size',$symbMap[$activeField])?$symbMap[$activeField]['size']:0);
												if($type === 'numeric'){
													if(is_numeric($activeValue)){
														$updateValueArr[$activeField] = $activeValue;
													}
												}
												elseif($type === 'date'){
													$dateStr = $this->formatDate($activeValue); 
													if($dateStr){
														$updateValueArr[$activeField] = $activeValue;
													} 
													else if($activeField === 'eventdate'){
														if(!array_key_exists('verbatimeventdate',$updateValueArr) || $updateValueArr['verbatimeventdate']){
															$updateValueArr['verbatimeventdate'] = $activeValue;
														}
													}
												}
												else{
													if($size && strlen($activeValue) > $size){
														$activeValue = substr($activeValue,0,$size);
													}
													$updateValueArr[$activeField] = $activeValue;
												}
											}
										}
										$updateFrag = '';
										foreach($updateValueArr as $k => $uv){
											$updateFrag .= ','.$k.'="'.$this->encodeString($uv).'"';
										}
										if($occRemarkArr){
											$occStr = '';
											foreach($occRemarkArr as $k => $orv){
												$occStr .= ','.$k.': '.$this->encodeString($orv);
											} 
											$updateFrag .= ',occurrenceremarks="'.($r['occurrenceremarks']?$r['occurrenceremarks'].'; ':'').substr($occStr,1).'"';
										}
										if($updateFrag){
											$sqlUpdate = 'UPDATE omoccurrences SET '.substr($updateFrag,1).' WHERE occid = '.$occid;
											if($this->conn->query($sqlUpdate)){
												$this->dataLoaded = 1;
											}
											else{
												$this->logOrEcho('ERROR: Unable to update existing record with new skeletal record ');
												$this->logOrEcho("SQL : $sqlUpdate ",1);
											}
										}
									}
								}
								$rs->free();
							}
							if(!$occid && $activeFields) {
								$sqlIns1 = 'INSERT INTO omoccurrences(collid,'.($this->matchCatalogNumber?'catalogNumber':'othercatalogNumbers').',processingstatus,dateentered';
								$sqlIns2 = 'VALUES ('.$this->activeCollid.',"'.$catNum.'","unprocessed","'.date('Y-m-d H:i:s').'"';
								foreach($activeFields as $aField){
									$sqlIns1 .= ','.$aField;
									$value = $this->cleanString($recMap[$aField]);
									$type = (array_key_exists('type',$symbMap[$aField])?$symbMap[$aField]['type']:'string');
									$size = (array_key_exists('size',$symbMap[$aField])?$symbMap[$aField]['size']:0);
									if($type === 'numeric'){
										if(is_numeric($value)){
											$sqlIns2 .= ',' .$value;
										}
										else{
											$sqlIns2 .= ',NULL';
										}
									}
									elseif($type === 'date'){
										$dateStr = $this->formatDate($value);
										if($dateStr){
											$sqlIns2 .= ',"'.$dateStr.'"';
										}
										else{
											$sqlIns2 .= ',NULL';
											if($aField === 'eventdate' && !array_key_exists('verbatimeventdate',$symbMap)){
												$sqlIns1 .= ',verbatimeventdate';
												$sqlIns2 .= ',"'.$value.'"';
											}
										}
									}
									else{
										if($size && strlen($value) > $size){
											$value = substr($value,0,$size);
										}
										if($value){
											$sqlIns2 .= ',"'.$this->encodeString($value).'"';
										}
										else{
											$sqlIns2 .= ',NULL';
										}
									}
								}
								$sqlIns = $sqlIns1.') '.$sqlIns2.')';
								if($this->conn->query($sqlIns)){
									$this->dataLoaded = 1;
									$occid = $this->conn->insert_id;
								}
								else{
									$this->logOrEcho('ERROR trying to load new skeletal record: '.$this->conn->error);
								}
							}
							if(isset($recMap['omenid']) && $occid){
								$sqlExs ='INSERT INTO omexsiccatiocclink(omenid,occid) VALUES('.$recMap['omenid'].','.$occid.')';
								if(!$this->conn->query($sqlExs)){
									$this->logOrEcho('ERROR linking record to exsiccati ('.$recMap['omenid'].'-'.$occid.'): '.$this->conn->error);
								}
							}
						}
						unset($recMap);
					}
				}
				else{
					$this->logOrEcho('ERROR: Failed to locate catalognumber MD within file (' .$filePath. '),  ',1);
					return false;
				}
			}
			$this->logOrEcho('Skeletal file loaded ',1);
			fclose($fh);
			if(true){
				$fileName = substr($filePath,strrpos($filePath,'/')).'.orig_'.time();
				if(!file_exists($this->targetPathBase . $this->targetPathFrag . 'orig_skeletal') && !mkdir($concurrentDirectory = $this->targetPathBase . $this->targetPathFrag . 'orig_skeletal') && !is_dir($concurrentDirectory)) {
					throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
				}
				if(!rename($filePath,$this->targetPathBase.$this->targetPathFrag.'orig_skeletal'.$fileName)){
					$this->logOrEcho('ERROR: unable to move (' .$filePath. ') ',1);
				}
			} 
			else if(!unlink($filePath)){
				$this->logOrEcho('ERROR: unable to delete file (' .$filePath. ') ',1);
			}
		}
		else{
			$this->logOrEcho("ERROR: Can't open skeletal file ".$filePath. ' ');
		}
		return true;
	}

	private function getRecordArr($fh, $delimiter){
		if(!$delimiter) {
			return false;
		}
		$recordArr = array();
		if($delimiter === 'csv'){
			$recordArr = fgetcsv($fh);
		}
		else{
			$recordStr = fgets($fh);
			if($recordStr) {
				$recordArr = explode($delimiter, $recordStr);
			}
		}
		return $recordArr;
	}
	
	private function updateCollectionStats(): void
	{
		if($this->dbMetadata){
			$occurMain = new OccurrenceMaintenance($this->conn);
	
			$this->logOrEcho('Cleaning house...');
			$collString = implode(',',$this->collProcessedArr);
			if(!$occurMain->generalOccurrenceCleaning($collString)){
				$errorArr = $occurMain->getErrorArr();
				foreach($errorArr as $errorStr){
					$this->logOrEcho($errorStr,1);
				}
			}
			
			$this->logOrEcho('Protecting sensitive species...');
			$occurMain->protectRareSpecies();
			
			$this->logOrEcho('Updating statistics...');
			foreach($this->collProcessedArr as $collid){
				$occurMain->updateCollectionStats($collid);
			}
			$occurMain->__destruct();
			
			$this->logOrEcho('Populating global unique identifiers (GUIDs) for all records...');
			$uuidManager = new UuidFactory($this->conn);
			$uuidManager->setSilent(1);
			$uuidManager->populateGuids();
			$uuidManager->__destruct();
		}
		$this->logOrEcho('Stats update completed');
	}

	private function sendMetadata($email,$mdFileName): void
	{
		if($email && $mdFileName){
			$subject = 'Images processed on '.date('Y-m-d');

			$separator = md5(time());
			$eol = "\r\n";

			$headers = 'MIME-Version: 1.0 '.$eol.
				'Content-Type: multipart/mixed; boundary="'.$separator.'"'.$eol.
				'To: '.$email.$eol.
				'From: Admin <seinetAdmin@asu.edu> '.$eol.
				'Content-Transfer-Encoding: 8bit'.$eol.
				'This is a MIME encoded message.'.$eol;

			$url = 'http://swbiodiversity.org/seinet/collections/misc/specprocessor/index.php?tabindex=1&collid='.$this->activeCollid;
			$body = '--' .$separator.$eol.
				'Content-Type: text/html; charset=iso-8859-1'.$eol.
				'Content-Transfer-Encoding: 8bit'.$eol.
				'Images in the attached file have been processed and are ready to be uploaded into your collection. '.
				'This can be done using the image loading tools located in the Processing Tools (see link below).'.
				'<a href="'.$url.'">'.$url.'</a>'.
				'<br/>If you have problems with the new password, contact the System Administrator ';

			$fname = substr(strrchr($mdFileName, '/'), 1);
			$data = file_get_contents($mdFileName);
			$body .= '--' . $separator . $eol.
				'Content-Type: application/octet-stream; name="'.$fname.'"'.$eol.
				'Content-Transfer-Encoding: base64'.$eol.
				'Content-Disposition: attachment'.$eol.
				chunk_split( base64_encode($data)).$eol.
				'--'.$separator.'--';

			if(!mail($email,$subject,$body,$headers)){
				echo 'Mail send ... ERROR!';
			}
		}
	}

	public function setCollArr($cArr): void
	{
		if($cArr){
			if(is_array($cArr)){
				$this->collArr = $cArr;
				if($this->dbMetadata){
					$sql = 'SELECT collid, institutioncode, collectioncode, collectionname, managementtype FROM omcollections '.
						'WHERE (collid IN('.implode(',',array_keys($cArr)).'))';
					if($rs = $this->conn->query($sql)){
						if($rs->num_rows){
							while($r = $rs->fetch_object()){
								$this->collArr[$r->collid]['instcode'] = $r->institutioncode;
								$this->collArr[$r->collid]['collcode'] = $r->collectioncode;
								$this->collArr[$r->collid]['collname'] = $r->collectionname;
								$this->collArr[$r->collid]['managementtype'] = $r->managementtype;
							}
						}
						else{
							$this->logOrEcho('ABORT: unable to get collection metadata from database (collids might be wrong) ');
							exit('ABORT: unable to get collection metadata from database');
						}
						$rs->free();
					}
					else{
						$this->logOrEcho('ABORT: unable run SQL to obtain additional collection metadata: '.$this->conn->error);
						exit('ABORT: unable run SQL to obtain additional collection metadata'.$this->conn->error);
					}
				}
			}
		}
		else{
			$this->logOrEcho('Error: collection array does not exist');
			exit('ABORT: collection array does not exist');
		}
	}
	
	public function setSourcePathBase($p): void
	{
		$sub = substr($p,-1);
		if($p && $sub !== '/' && $sub !== "\\") {
			$p .= '/';
		}
		$this->sourcePathBase = $p;
	}

	public function setTargetPathBase($p): void
	{
		$sub = substr($p,-1);
		if($p && $sub !== '/' && $sub !== "\\") {
			$p .= '/';
		}
		$this->targetPathBase = $p;
	}

	public function setImgUrlBase($u): void
	{
		if($u && substr($u,-1) !== '/') {
			$u .= '/';
		}
		$this->imgUrlBase = $u;
	}

	public function setServerRoot($path): void
	{
		$this->serverRoot = $path;
	}

	public function setMatchCatalogNumber($b): void
	{
		if($b) {
			$this->matchCatalogNumber = true;
		}
		else {
			$this->matchCatalogNumber = false;
		}
	}

	public function setMatchOtherCatalogNumbers($b): void
	{
		if($b) {
			$this->matchOtherCatalogNumbers = true;
		}
		else {
			$this->matchOtherCatalogNumbers = false;
		}
	}

	public function setWebPixWidth($w): void
	{
		$this->webPixWidth = $w;
	}

	public function setTnPixWidth($tn): void
	{
		$this->tnPixWidth = $tn;
	}

	public function setLgPixWidth($lg): void
	{
		$this->lgPixWidth = $lg;
	}

	public function setWebFileSizeLimit($size): void
	{
		$this->webFileSizeLimit = $size;
	}

	public function setLgFileSizeLimit($size): void
	{
		$this->lgFileSizeLimit = $size;
	}

	public function setJpgQuality($q): void
	{
		$this->jpgQuality = $q;
	}

	public function setWebImg($c): void
	{
		$this->webImg = $c;
	}

	public function setTnImg($c): void
	{
		$this->tnImg = $c;
	}

	public function setLgImg($c): void
	{
		$this->lgImg = $c;
	}

	public function setCreateWebImg($c): void
	{
		$this->webImg = $c;
	}

	public function setCreateTnImg($c): void
	{
		$this->tnImg = $c;
	}

	public function setCreateLgImg($c): void
	{
		$this->lgImg = $c;
	}

	public function setKeepOrig($c): void
	{
		$this->keepOrig = $c;
	}

	public function setSkeletalFileProcessing($c): void
	{
		$this->skeletalFileProcessing = $c;
	}

	public function setCreateNewRec($c): void
	{
		$this->createNewRec = $c;
	}

	public function setCopyOverImg($c): void
	{
		if($c === 1){
			$this->imgExists = 2;
		}
		else{
			$this->imgExists = 1;
		}
	}

	public function setImgExists($c): void
	{
		$this->imgExists = $c;
	}

	public function setDbMetadata($v): void
	{
		$this->dbMetadata = $v;
	}

	public function setUseImageMagick($useIM): void
	{
		$this->processUsingImageMagick = $useIM;
	}

	public function setLogMode($c): void
	{
		$this->logMode = $c;
	}

	public function setLogPath($path): void
	{
		$subPath = substr($path,-1);
		if($path && $subPath !== '/' && $subPath !== "\\") {
			$path .= '/';
		}
		$this->logPath = $path;
	}

	private function formatDate($inStr){
		$dateStr = trim($inStr);
		if(!$dateStr) {
			return false;
		}
		$t = '';
		$y = '';
		$m = '00';
		$d = '00';
		if(preg_match('/\d{2}:\d{2}:\d{2}/',$dateStr,$match)){
			$t = $match[0];
		}
		if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})\D*/',$dateStr,$match)){
			$y = $match[1];
			$m = $match[2];
			$d = $match[3];
		}
		elseif(preg_match('/^(\d{1,2})\s(\D{3,})\.*\s(\d{2,4})/',$dateStr,$match)){
			$d = $match[1];
			$mStr = $match[2];
			$y = $match[3];
			$mStr = strtolower(substr($mStr,0,3));
			$m = $this->monthNames[$mStr];
		}
		elseif(preg_match('/^(\d{1,2})-(\D{3,})-(\d{2,4})/',$dateStr,$match)){
			$d = $match[1];
			$mStr = $match[2];
			$y = $match[3];
			$mStr = strtolower(substr($mStr,0,3));
			$m = $this->monthNames[$mStr];
		}
		elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})/',$dateStr,$match)){
			$m = $match[1];
			$d = $match[2];
			$y = $match[3];
		}
		elseif(preg_match('/^(\D{3,})\.*\s(\d{1,2}),?\s(\d{2,4})/',$dateStr,$match)){
			$mStr = $match[1];
			$d = $match[2];
			$y = $match[3];
			$mStr = strtolower(substr($mStr,0,3));
			$m = $this->monthNames[$mStr];
		}
		elseif(preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2,4})/',$dateStr,$match)){
			$m = $match[1];
			$d = $match[2];
			$y = $match[3];
		}
		elseif(preg_match('/^(\D{3,})\.*\s([1,2][0,5-9]\d{2})/',$dateStr,$match)){
			$mStr = strtolower(substr($match[1],0,3));
			$m = $this->monthNames[$mStr];
			$y = $match[2];
		}
		elseif(preg_match('/([1,2][0,5-9]\d{2})/',$dateStr,$match)){
			$y = $match[1];
		}
		if($y){
			if(strlen($y) === 2){
				if($y < 20) {
					$y = '20' . $y;
				}
				else {
					$y = '19' . $y;
				}
			}
			if(strlen($m) === 1) {
				$m = '0' . $m;
			}
			if(strlen($d) === 1) {
				$d = '0' . $d;
			}
			$dateStr = $y.'-'.$m.'-'.$d;
		}
		else{
			$timeStr = strtotime($dateStr);
			if($timeStr) {
				$dateStr = date('Y-m-d H:i:s', $timeStr);
			}
		}
		if($t){
			$dateStr .= ' '.$t;
		}
		return $dateStr;
	}
	
	private function formatScientificName($inStr){
		$sciNameStr = trim($inStr);
		$sciNameStr = preg_replace('/\s\s+/', ' ',$sciNameStr);
		$tokens = explode(' ',$sciNameStr);
		if($tokens){
			$sciNameStr = array_shift($tokens);
			if(strlen($sciNameStr) < 2) {
				$sciNameStr = ' ' . array_shift($tokens);
			}
			if($tokens){
				$term = array_shift($tokens);
				$sciNameStr .= ' '.$term;
				if($term === 'x') {
					$sciNameStr .= ' ' . array_shift($tokens);
				}
			}
			$tRank = '';
			$infraSp = '';
			foreach($tokens as $c => $v){
				switch($v) {
					case 'subsp.':
					case 'subsp':
					case 'ssp.':
					case 'ssp':
					case 'subspecies':
					case 'var.':
					case 'var':
					case 'variety':
					case 'forma':
					case 'form':
					case 'f.':
					case 'fo.':
						if(array_key_exists($c+1,$tokens) && ctype_lower($tokens[$c+1])){
							$tRank = $v;
							if(($tRank === 'ssp' || $tRank === 'subsp' || $tRank === 'var') && substr($tRank,-1) !== '.') {
								$tRank .= '.';
							}
							$infraSp = $tokens[$c+1];
						}
				}
			}
			if($infraSp){
				$sciNameStr .= ' '.$tRank.' '.$infraSp;
			}
		}
		return $sciNameStr;
	}
	
	private function uriExists($url) {
		global $IMAGE_DOMAIN, $IMAGE_ROOT_URL, $IMAGE_ROOT_PATH;
		$exists = false;
		$localUrl = '';
		if(strpos($url, '/') === 0){
			if($IMAGE_DOMAIN){
				$url = $IMAGE_DOMAIN.$url;
			}
			elseif($IMAGE_ROOT_URL && strpos($url,$IMAGE_ROOT_URL) === 0){
				$localUrl = str_replace($IMAGE_ROOT_URL,$IMAGE_ROOT_PATH,$url);
			}
			else{
				$urlPrefix = 'http://';
				if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
					$urlPrefix = 'https://';
				}
				$urlPrefix .= $_SERVER['HTTP_HOST'];
				if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
					$urlPrefix .= ':' . $_SERVER['SERVER_PORT'];
				}
				$url = $urlPrefix.$url;
			}
		}
		
		if(file_exists($url) || ($localUrl && file_exists($localUrl))){
			return true;
	    }

	    if(!$exists){
		    $handle   = curl_init($url);
		    curl_setopt($handle, CURLOPT_HEADER, false);
		    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
		    curl_setopt($handle, CURLOPT_HTTPHEADER, Array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15') );
		    curl_setopt($handle, CURLOPT_NOBODY, true);
		    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
		    $exists = curl_exec($handle);
		    curl_close($handle);
	    }
	     
	    if(!$exists){
	    	$exists = (@fclose(@fopen($url, 'rb')));
	    }
	    
	    if(!@exif_imagetype($url)) {
			$exists = false;
		}

	    return $exists;
	}	
	
	private function encodeString($inStr): string
	{
		global $CHARSET;
		$retStr = trim($inStr);
		$search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
		$replace = array("'","'",'"','"','*','-','-');
		$inStr= str_replace($search, $replace, $inStr);
		
		if($inStr){
			$charLower = strtolower($CHARSET);
			if($charLower === 'utf-8' || $charLower === 'utf8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) === 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
				}
			}
			elseif($charLower === 'iso-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') === 'UTF-8'){
					$retStr = utf8_decode($inStr);
				}
			}
		}
		return $retStr;
	}

	private function cleanString($inStr){
		$retStr = trim($inStr);
		$retStr = str_replace(array(chr(10), chr(11), chr(13), chr(20), chr(30)), ' ', $retStr);
		$retStr = $this->conn->real_escape_string($retStr);
		return $retStr;
	}

	protected function logOrEcho($str,$indent = 0): void
	{
		if(($this->logMode > 1) && $this->logFH) {
			if($indent) {
				$str = "\t" . $str;
			}
			fwrite($this->logFH,$str."\n");
		}
		if($this->logMode === 1 || $this->logMode === 3){
			echo '<li '.($indent?'style="margin-left:'.($indent*15).'px"':'').'>'.$str."</li>\n";
			@ob_flush();
			@flush();
		}
	}
}
