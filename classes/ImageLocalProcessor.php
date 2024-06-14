<?php
include_once(__DIR__ . '/../services/DbConnectionService.php');
include_once(__DIR__ . '/OccurrenceMaintenance.php');
include_once(__DIR__ . '/OccurrenceUtilities.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/TaxonomyService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class ImageLocalProcessor {

    protected $conn;
    private $collArr = array();
    private $activeCollid;
    private $collProcessedArr = array();
    protected $sourcePathBase;
    private $targetPathFrag;
    private $origPathFrag;
    private $matchCatalogNumber = true;
    private $matchOtherCatalogNumbers = false;
    private $tnImg = 0;
    private $lgImg = 0;
    private $tnSourceSuffix = '_tn';
    private $lgSourceSuffix = '_lg';
    private $keepOrig = 0;
    private $skeletalFileProcessing = true;
    private $createNewRec = true;
    private $imgExists = 0;
    private $logMode = 0;
    private $logPath = '';
    private $logFH;
    private $errorMessage;
    private $sourceGdImg;
    private $sourceImagickImg;
    private $processedFiles = array();

    public function __construct(){
        ini_set('memory_limit','1024M');
        ini_set('auto_detect_line_endings', true);
        $connection = new DbConnectionService();
        $this->conn = $connection->getConnection();
        if($GLOBALS['LOG_PATH']) {
            $this->logPath = $GLOBALS['LOG_PATH'];
        }
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }

        if($this->logFH) {
            fclose($this->logFH);
        }
    }

    public function initProcessor($logTitle = null): void
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
        if(strncmp($this->sourcePathBase, 'http', 4) === 0){
            $headerArr = get_headers($this->sourcePathBase);
            if(!$headerArr){
                $this->logOrEcho('ABORT: sourcePathBase returned bad headers ('.$this->sourcePathBase.')');
                exit();
            }
            preg_match('/http.+\s(\d{3})\s/i',$headerArr[0],$codeArr);
            if ((int)$codeArr[1] === 403) {
                $this->logOrEcho('ABORT: sourcePathBase returned Forbidden ('.$this->sourcePathBase.')');
                exit();
            }
            if ((int)$codeArr[1] === 404) {
                $this->logOrEcho('ABORT: sourcePathBase returned a page Not Found error ('.$this->sourcePathBase.')');
                exit();
            }
            if((int)$codeArr[1] !== 200) {
                $this->logOrEcho('ABORT: sourcePathBase returned error code '.$codeArr[1].' ('.$this->sourcePathBase.')');
                exit();
            }
        }
        elseif(!file_exists($this->sourcePathBase)){
            $this->logOrEcho('ABORT: sourcePathBase does not exist ('.$this->sourcePathBase.')');
            exit();
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
            if(!file_exists($GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag) && !mkdir($concurrentDirectory = $GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag, 0777, true) && !is_dir($concurrentDirectory)) {
                $this->logOrEcho('ERROR: unable to create new folder (' .$GLOBALS['IMAGE_ROOT_PATH'].'/'.$this->targetPathFrag. ') ');
                exit('ABORT: unable to create new folder (' .$GLOBALS['IMAGE_ROOT_PATH'].'/'.$this->targetPathFrag. ')');
            }

            if($this->keepOrig){
                $this->origPathFrag = 'orig/'.date('Ym').'/';
                if(!file_exists($GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag . 'orig/') && !mkdir($concurrentDirectory = $GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag . 'orig/') && !is_dir($concurrentDirectory)) {
                    $this->logOrEcho('NOTICE: unable to create base folder to store original files (' .$GLOBALS['IMAGE_ROOT_PATH'].'/'.$this->targetPathFrag. ') ');
                }
                if(file_exists($GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag . 'orig/') && !file_exists($GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag . $this->origPathFrag) && !mkdir($concurrentDirectory = $GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag . $this->origPathFrag) && !is_dir($concurrentDirectory)) {
                    $this->logOrEcho('NOTICE: unable to create folder to store original files (' .$GLOBALS['IMAGE_ROOT_PATH'].'/'.$this->targetPathFrag.$this->origPathFrag. ') ');
                }
            }

            $this->logOrEcho('Starting image processing: '.$sourcePathFrag);
            if(strncmp($this->sourcePathBase, 'http', 4) === 0){
                $this->processHtml($sourcePathFrag);
            }
            else if($this->errorMessage === 'abort' || !$this->processFolder($sourcePathFrag)) {
                $this->errorMessage = '';
                continue;
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

    private function processFolder($pathFrag = null): ?bool
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

    private function processHtml($pathFrag = null): void
    {
        $codeArr = array();
        set_time_limit(3600);
        $headerArr = get_headers($this->sourcePathBase.$pathFrag);
        if($headerArr){
            preg_match('/http.+\s(\d{3})\s/i',$headerArr[0],$codeArr);
        }
        if($codeArr && $codeArr[1] === 200){
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
                                    return;
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
    }

    private function processImageFile($fileName,$sourcePathFrag = null): bool
    {
        flush();
        $fileSize = 0;
        $retVal = true;
        $fileExists = false;
        if($specPk = $this->getPrimaryKey($fileName)){
            $occId = $this->getOccId($specPk);
            $targetFileName = str_replace(' ','_',$fileName);
            $fileName = rawurlencode($fileName);
            if($occId){
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
                $targetPath = $GLOBALS['IMAGE_ROOT_PATH'].'/'.$targetFrag;
                if(!file_exists($targetPath) && !mkdir($targetPath) && !is_dir($targetPath)) {
                    $this->logOrEcho('ERROR: unable to create new folder (' .$targetPath. ') ');
                }
                if(file_exists($targetPath.$targetFileName)){
                    $fileExists = true;
                }
                if($fileExists){
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
                        $retVal = false;
                    }
                }
                if(!$fileExists || $this->imgExists !== 0){
                    [$width, $height] = getimagesize($sourcePath . $fileName);
                    if($width && $height){
                        if(strncmp($sourcePath, 'http://', 7) === 0 || strncmp($sourcePath, 'https://', 8) === 0) {
                            $x = array_change_key_case(get_headers($sourcePath.$fileName, 1),CASE_LOWER);
                            if($x){
                                if( strcasecmp($x[0], 'HTTP/1.1 200 OK') !== 0 ) {
                                    $fileSize = $x['content-length'][1];
                                }
                                else {
                                    $fileSize = $x['content-length'];
                                }
                            }
                        }
                        else {
                            $fileSize = @filesize($sourcePath.$fileName);
                        }
                        if($fileSize){
                            $fileSize /= 1000000;
                        }

                        $webUrlFrag = '';
                        if($this->createNewImage($sourcePath.$fileName,$targetPath.$targetFileName,$GLOBALS['IMG_WEB_WIDTH'],round($GLOBALS['IMG_WEB_WIDTH'] * ($height / $width)),$width,$height)){
                            $webUrlFrag = $GLOBALS['IMAGE_ROOT_URL'].'/'.$targetFrag.$targetFileName;
                            $this->logOrEcho('Web image created from source image (' .date('Y-m-d h:i:s A'). ') ',1);
                        }
                        if(!$webUrlFrag){
                            $this->logOrEcho('Failed to create web image ',1);
                        }
                        $lgUrlFrag = '';
                        if($this->lgImg){
                            $lgTargetFileName = substr($targetFileName,0,-4). '_lg.jpg';
                            if($fileSize && $fileSize > $GLOBALS['MAX_UPLOAD_FILESIZE']){
                                if($this->createNewImage($sourcePath.$fileName,$targetPath.$lgTargetFileName,$GLOBALS['IMG_LG_WIDTH'],round($GLOBALS['IMG_LG_WIDTH'] * ($height / $width)),$width,$height)){
                                    $lgUrlFrag = $GLOBALS['IMAGE_ROOT_URL'].'/'.$targetFrag.$lgTargetFileName;
                                    $this->logOrEcho('Resized source as large derivative (' .date('Y-m-d h:i:s A'). ') ',1);
                                }
                            }
                            else if(copy($sourcePath.$fileName,$targetPath.$lgTargetFileName)){
                                $lgUrlFrag = $GLOBALS['IMAGE_ROOT_URL'].'/'.$targetFrag.$lgTargetFileName;
                                $this->logOrEcho('Imported source as large derivative (' .date('Y-m-d h:i:s A'). ') ',1);
                            }
                            else{
                                $this->logOrEcho('WARNING: unable to import large derivative (' .$sourcePath.$fileName. ') ',1);
                            }
                        }
                        $tnUrlFrag = '';
                        if($this->tnImg){
                            $tnTargetFileName = substr($targetFileName,0,-4). '_tn.jpg';
                            if($this->createNewImage($sourcePath.$fileName,$targetPath.$tnTargetFileName,$GLOBALS['IMG_TN_WIDTH'],round($GLOBALS['IMG_TN_WIDTH'] * ( $height / $width)),$width,$height)){
                                $tnUrlFrag = $GLOBALS['IMAGE_ROOT_URL'].'/'.$targetFrag.$tnTargetFileName;
                                $this->logOrEcho('Created thumbnail from source (' .date('Y-m-d h:i:s A'). ') ',1);
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
                        $this->recordImageMetadata($occId,$webUrlFrag,$tnUrlFrag,$lgUrlFrag);
                        if(file_exists($sourcePath.$fileName)){
                            if($this->keepOrig){
                                if(file_exists($GLOBALS['IMAGE_ROOT_PATH'].'/'.$this->targetPathFrag.$this->origPathFrag)){
                                    rename($sourcePath.$fileName,$GLOBALS['IMAGE_ROOT_PATH'].'/'.$this->targetPathFrag.$this->origPathFrag.$fileName. '.orig');
                                }
                            } else {
                                unlink($sourcePath.$fileName);
                            }
                        }
                        $this->logOrEcho('Image processed successfully (' .date('Y-m-d h:i:s A'). ')!',1);
                    }
                    else{
                        $this->logOrEcho('File skipped (' .$sourcePath.$fileName. '), unable to obtain dimensions of original image',1);
                        $retVal = false;
                    }
                }
            }
        }
        else{
            $this->logOrEcho('File skipped (' .$sourcePathFrag.$fileName. '), unable to extract occurrence identifier',1);
            $retVal = false;
        }
        flush();
        return $retVal;
    }

    private function createNewImage($sourcePathBase, $targetPath, $newWidth, $newHeight, $sourceWidth, $sourceHeight): bool
    {
        if(function_exists('gd_info') && extension_loaded('gd')) {
            $status = $this->createNewImageGD($sourcePathBase,$targetPath,$newWidth,$newHeight,$sourceWidth,$sourceHeight);
        }
        else{
            $this->logOrEcho('FATAL ERROR: No appropriate image handler for image conversions',1);
            exit('ABORT: No appropriate image handler for image conversions');
        }
        return $status;
    }

    private function createNewImageGD($sourcePathBase, $targetPath, $newWidth, $newHeight, $sourceWidth, $sourceHeight): bool
    {
        $status = false;
        if(!$this->sourceGdImg){
            $this->sourceGdImg = imagecreatefromjpeg($sourcePathBase);
        }
        if($newWidth && $newHeight){
            $tmpImg = imagecreatetruecolor($newWidth,$newHeight);
            imagecopyresized($tmpImg,$this->sourceGdImg,0,0,0,0,$newWidth,$newHeight,$sourceWidth,$sourceHeight);
            $status = imagejpeg($tmpImg, $targetPath, 90);
            if(!$status){
                $this->logOrEcho('ERROR: Unable to resize and write file: ' .$targetPath,1);
            }
            imagedestroy($tmpImg);
        }
        else{
            $this->logOrEcho('ERROR: Unable to create image because new width or height is not set (w:' .$newWidth.' h:'.$newHeight.')');
        }
        return $status;
    }

    private function getPrimaryKey($str){
        $specPk = '';
        if(isset($this->collArr[$this->activeCollid]['pmterm'])){
            $pmTerm = $this->collArr[$this->activeCollid]['pmterm'];
            if(strncmp($pmTerm, '/', 1) !== 0 || strpos(substr($pmTerm,-3),'/') === false){
                $this->logOrEcho('PROCESS ABORTED: Regular Expression term illegal due to missing forward slashes delimiting the term: ' .$pmTerm,1);
                $this->errorMessage = 'abort';
            }
            else if(!strpos($pmTerm,'(') || !strpos($pmTerm,')')){
                $this->logOrEcho('PROCESS ABORTED: Regular Expression term illegal due to missing capture term: ' .$pmTerm,1);
                $this->errorMessage = 'abort';
            }
            else if(preg_match($pmTerm,$str,$matchArr)){
                if(array_key_exists(1,$matchArr) && $matchArr[1]){
                    $specPk = $matchArr[1];
                }
                if (isset($this->collArr[$this->activeCollid]['prpatt'])) {
                    $specPk = preg_replace($this->collArr[$this->activeCollid]['prpatt'],$this->collArr[$this->activeCollid]['prrepl'],$specPk);
                }
            }
        }
        return $specPk;
    }

    private function getOccId($specPk): int
    {
        $occId = 0;
        if($this->matchCatalogNumber){
            $sql = 'SELECT occid FROM omoccurrences '.
                'WHERE (catalognumber IN("'.$specPk.'"'.(strncmp($specPk, '0', 1) === 0 ?',"'.ltrim($specPk,'0 ').'"':'').')) '.
                'AND (collid = '.$this->activeCollid.')';
            $rs = $this->conn->query($sql);
            if($row = $rs->fetch_object()){
                $occId = $row->occid;
            }
            $rs->free();
        }
        if($this->matchOtherCatalogNumbers){
            $sql = 'SELECT occid FROM omoccurrences '.
                'WHERE (othercatalognumbers IN("'.$specPk.'"'.(strpos($specPk, 0) === 0 ?',"'.ltrim($specPk,'0 ').'"':'').')) '.
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
                $this->logOrEcho("Specimen record does not exist; new empty occurrence record created and assigned an 'unprocessed' status (occid = ".$occId. ') ',1);
            }
            else{
                $this->logOrEcho('ERROR creating new occurrence record.',1);
            }
        }
        if(!$occId){
            $this->logOrEcho('ERROR: File skipped, unable to locate occurrence record ' .$specPk. ' (' .date('Y-m-d h:i:s A'). ') ',1);
        }
        return $occId;
    }

    private function recordImageMetadata($specID,$webUrl,$tnUrl,$oUrl): void
    {
        $this->databaseImage($specID,$webUrl,$tnUrl,$oUrl);
    }

    private function databaseImage($occId,$webUrl,$tnUrl,$oUrl): void
    {
        if($occId && is_numeric($occId)){
            $this->logOrEcho('Preparing to load record into database',1);
            $sql = 'SELECT imgid, url, thumbnailurl, originalurl '.
                'FROM images WHERE (occid = '.$occId.') ';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                if(strcasecmp($r->url,$webUrl) === 0){
                    if(!$this->conn->query('DELETE FROM images WHERE imgid = '.$r->imgid)){
                        $this->logOrEcho('ERROR deleting image record #'.$r->imgid.' (equal URLs).',1);
                    }
                }
                elseif($this->imgExists === 2 && strcasecmp(basename($r->url),basename($webUrl)) === 0){
                    if($this->conn->query('DELETE FROM images WHERE imgid = '.$r->imgid)){
                        $urlPath = parse_url($r->url, PHP_URL_PATH);
                        if($urlPath && strpos($urlPath, $GLOBALS['IMAGE_ROOT_URL']) === 0){
                            $wFile = str_replace($GLOBALS['IMAGE_ROOT_URL'],$GLOBALS['IMAGE_ROOT_PATH'],$urlPath);
                            if(file_exists($wFile) && is_writable($wFile)) {
                                unlink($wFile);
                            }
                        }
                        $urlTnPath = parse_url($r->thumbnailurl, PHP_URL_PATH);
                        if($urlTnPath && strpos($urlTnPath, $GLOBALS['IMAGE_ROOT_URL']) === 0){
                            $wFile = str_replace($GLOBALS['IMAGE_ROOT_URL'],$GLOBALS['IMAGE_ROOT_PATH'],$urlTnPath);
                            if(file_exists($wFile) && is_writable($wFile)) {
                                unlink($wFile);
                            }
                        }
                        $urlLgPath = parse_url($r->url, PHP_URL_PATH);
                        if($urlLgPath && strpos($urlLgPath, $GLOBALS['IMAGE_ROOT_URL']) === 0){
                            $wFile = str_replace($GLOBALS['IMAGE_ROOT_URL'],$GLOBALS['IMAGE_ROOT_PATH'],$urlLgPath);
                            if(file_exists($wFile) && is_writable($wFile)) {
                                unlink($wFile);
                            }
                        }
                    }
                    else{
                        $this->logOrEcho('ERROR: Unable to delete image record #'.$r->imgid.' (equal basename).',1);
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
                    $this->logOrEcho('SUCCESS: Image record loaded into database',1);
                }
                else{
                    $this->logOrEcho('ERROR: Unable to load image record into database.',1);
                }
            }
        }
        else{
            $this->logOrEcho('ERROR: Missing occid (omoccurrences PK), unable to load record ');
        }
        flush();
    }

    private function processSkeletalFile($filePath): ?bool
    {
        $this->logOrEcho('Preparing to load Skeletal file into database',1);
        $fh = fopen($filePath, 'rb');
        $hArr = array();
        $delimiter = '';
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
                }
            }
            else{
                $this->logOrEcho('ERROR: Skeletal file skipped: unable to determine file type ',1);
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
                    unset($symbMap['datelastmodified'], $symbMap['occid'], $symbMap['collid'], $symbMap['catalognumber'], $symbMap['institutioncode'], $symbMap['collectioncode'], $symbMap['dbpk'], $symbMap['processingstatus'], $symbMap['observeruid'], $symbMap['tid']);

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
                                    if(strncmp($valueStr, '"', 1) === 0 && substr($valueStr,-1) === '"'){
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
                                $recMap['sciname'] = TaxonomyService::formatScientificName($recMap['scientificname']);
                            }
                            if(array_key_exists('sciname',$recMap)){
                                $symbMap['sciname']['type'] = 'string';
                                $symbMap['sciname']['size'] = 255;
                            }
                        }

                        if(!array_key_exists('eventdate',$recMap) || !$recMap['eventdate']){
                            if(array_key_exists('verbatimeventdate',$recMap) && $recMap['verbatimeventdate']){
                                $dateStr = OccurrenceUtilities::formatDate($recMap['verbatimeventdate']);
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
                                $index = array_search('ometid', $activeFields, true);
                                if(is_string($index) || is_int($index)){
                                    unset($activeFields[$index]);
                                }
                            }
                            if(in_array('omenid', $activeFields, true)) {
                                $index = array_search('omenid', $activeFields, true);
                                if(is_string($index) || is_int($index)){
                                    unset($activeFields[$index]);
                                }
                            }
                            if(in_array('exsiccatititle', $activeFields, true)) {
                                $index = array_search('exsiccatititle', $activeFields, true);
                                if(is_string($index) || is_int($index)){
                                    unset($activeFields[$index]);
                                }
                            }
                            if(in_array('exsiccatinumber', $activeFields, true)) {
                                $index = array_search('exsiccatinumber', $activeFields, true);
                                if(is_string($index) || is_int($index)){
                                    unset($activeFields[$index]);
                                }
                            }

                            $termArr = array();
                            if($this->matchCatalogNumber) {
                                $termArr[] = '(catalognumber IN("' . $catNum . '"' . (strncmp($catNum, '0', 1) === 0 ? ',"' . ltrim($catNum, '0 ') . '"' : '') . '))';
                            }
                            if($this->matchOtherCatalogNumbers) {
                                $termArr[] = '(othercatalognumbers IN("' . $catNum . '"' . (strncmp($catNum, '0', 1) === 0 ? ',"' . ltrim($catNum, '0 ') . '"' : '') . '))';
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
                                            $activeValue = SanitizerService::cleanInStr($this->conn,$recMap[$activeField]);
                                            if(!trim($r[$activeField])){
                                                $type = (array_key_exists('type',$symbMap[$activeField])?$symbMap[$activeField]['type']:'string');
                                                $size = (array_key_exists('size',$symbMap[$activeField])?$symbMap[$activeField]['size']:0);
                                                if($type === 'numeric'){
                                                    if(is_numeric($activeValue)){
                                                        $updateValueArr[$activeField] = $activeValue;
                                                    }
                                                }
                                                elseif($type === 'date'){
                                                    $dateStr = OccurrenceUtilities::formatDate($activeValue);
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
                                            if(!$this->conn->query($sqlUpdate)){
                                                $this->logOrEcho('ERROR: Unable to update existing record with new skeletal record ');
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
                                    $value = SanitizerService::cleanInStr($this->conn,$recMap[$aField]);
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
                                        $dateStr = OccurrenceUtilities::formatDate($value);
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
                                    $occid = $this->conn->insert_id;
                                }
                                else{
                                    $this->logOrEcho('ERROR trying to load new skeletal record.');
                                }
                            }
                            if(isset($recMap['omenid']) && $occid){
                                $sqlExs ='INSERT INTO omexsiccatiocclink(omenid,occid) VALUES('.$recMap['omenid'].','.$occid.')';
                                if(!$this->conn->query($sqlExs)){
                                    $this->logOrEcho('ERROR linking record to exsiccati ('.$recMap['omenid'].'-'.$occid.').');
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
            $fileName = substr($filePath,strrpos($filePath,'/')).'.orig_'.time();
            if(!file_exists($GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag . 'orig_skeletal') && !mkdir($concurrentDirectory = $GLOBALS['IMAGE_ROOT_PATH'] . '/' . $this->targetPathFrag . 'orig_skeletal') && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
            if(!rename($filePath,$GLOBALS['IMAGE_ROOT_PATH'].'/'.$this->targetPathFrag.'orig_skeletal'.$fileName)){
                $this->logOrEcho('ERROR: unable to move (' .$filePath. ') ',1);
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
        $uuidManager = new GUIDManager($this->conn);
        $uuidManager->setSilent(1);
        $uuidManager->populateGuids();
        $uuidManager->__destruct();
        $this->logOrEcho('Stats update completed');
    }

    public function setCollArr($cArr): void
    {
        if($cArr){
            if(is_array($cArr)){
                $this->collArr = $cArr;
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
                    $this->logOrEcho('ABORT: unable run SQL to obtain additional collection metadata.');
                    exit('ABORT: unable run SQL to obtain additional collection metadata');
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

    public function setTnImg($c): void
    {
        $this->tnImg = (int)$c;
    }

    public function setLgImg($c): void
    {
        $this->lgImg = (int)$c;
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

    public function setImgExists($c): void
    {
        $this->imgExists = (int)$c;
    }

    public function setLogMode($c): void
    {
        $this->logMode = (int)$c;
    }

    public function setLogPath($path): void
    {
        $subPath = substr($path,-1);
        if($path && $subPath !== '/' && $subPath !== "\\") {
            $path .= '/';
        }
        $this->logPath = $path;
    }

    private function encodeString($inStr): string
    {
        $search = array(chr(145),chr(146),chr(147),chr(148),chr(149),chr(150),chr(151));
        $replace = array("'","'",'"','"','*','-','-');
        return str_replace($search, $replace, $inStr);
    }

    protected function logOrEcho($str,$indent = null): void
    {
        if(($this->logMode > 1) && $this->logFH) {
            if($indent) {
                $str = "\t" . $str;
            }
            fwrite($this->logFH,$str."\n");
        }
        if($this->logMode === 1 || $this->logMode === 3){
            echo '<li '.($indent ? 'style="margin-left:'.($indent * 15).'px"':'').'>'.$str."</li>\n";
            @flush();
        }
    }
}
