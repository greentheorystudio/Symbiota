<?php
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/OccurrenceMaintenance.php');
include_once(__DIR__ . '/../services/SanitizerService.php');

class ImageProcessor {

    private $conn;

    private $collid = 0;
    private $spprid = 0;
    private $collArr;
    private $matchCatalogNumber = true;
    private $matchOtherCatalogNumbers = false;

    private $logMode = 0;
    private $logFH;
    private $destructConn = true;

    public function __construct($con = null){
        if($con){
            $this->conn = $con;
            $this->destructConn = false;
        }
        else{
            $connection = new DbService();
            $this->conn = $connection->getConnection();
            if(!$this->conn) {
                exit('ABORT: Image upload aborted: Unable to establish connection to database');
            }
        }
    }

    public function __destruct(){
        if($this->destructConn && !($this->conn === false)) {
            $this->conn->close();
        }

        if($this->logFH) {
            fclose($this->logFH);
        }
    }

    public function initProcessor($processorType = null): void
    {
        if($this->logFH) {
            fclose($this->logFH);
        }
        if($this->logMode > 1){
            $GLOBALS['LOG_PATH'] = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) === '/'?'':'/').'content/logs/';
            if($processorType) {
                $GLOBALS['LOG_PATH'] .= $processorType . '/';
            }
            if(!file_exists($GLOBALS['LOG_PATH']) && !mkdir($GLOBALS['LOG_PATH']) && !is_dir($GLOBALS['LOG_PATH'])) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $GLOBALS['LOG_PATH']));
            }
            if(file_exists($GLOBALS['LOG_PATH'])){
                $logFile = $GLOBALS['LOG_PATH'].$this->collid;
                $logFile .= '_'.date('Y-m-d').'.log';
                $this->logFH = fopen($logFile, 'ab');
            }
            else{
                echo 'ERROR creating Log file; path not found: '.$GLOBALS['LOG_PATH']."\n";
            }
        }
    }

    public function loadImageFile(): string
    {
        $retStr = '';
        $inFileName = basename($_FILES['uploadfile']['name']);
        $ext = substr(strrchr($inFileName, '.'), 1);
        $fileName = 'imageMappingFile_'.time();
        $fullPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) !== '/'?'/':'').'temp/data/';
        if(is_writable($fullPath) && move_uploaded_file($_FILES['uploadfile']['tmp_name'], $fullPath . $fileName . '.' . $ext)) {
            if($ext === 'zip'){
                $zipFilePath = $fullPath.$fileName.'.zip';
                $ext = '';
                $zip = new ZipArchive;
                $res = $zip->open($zipFilePath);
                if($res === TRUE) {
                    for($i = 0; $i < $zip->numFiles; $i++){
                        $fileExt = substr(strrchr($zip->getNameIndex($i), '.'), 1);
                        if($fileExt === 'csv' || $fileExt === 'txt'){
                            $ext = $fileExt;
                            $zip->renameIndex($i, $fileName.'.'.$ext);
                            $zip->extractTo($fullPath,$fileName.'.'.$ext);
                            $zip->close();
                            unlink($zipFilePath);
                            break;
                        }
                    }
                }
                else{
                    echo 'failed, code:' . $res;
                }
            }
            $retStr = $fileName.'.'.$ext;
        }
        return $retStr;
    }

    public function echoFileMapping($fileName): void
    {
        $fullPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) !== '/'?'/':'').'temp/data/'.$fileName;
        if($fh = fopen($fullPath,'rb')){
            $translationMap = array('catalognumber' => 'catalognumber', 'url' => 'url', 'thumbnailurl' => 'thumbnailurl',
                'originalurl' => 'originalurl', 'thumbnail' => 'thumbnailurl', 'large' => 'originalurl', 'web' => 'url');
            $headerArr = fgetcsv($fh);
            foreach($headerArr as $i => $sourceField){
                if($sourceField !== 'collid'){
                    echo '<tr><td style="padding:2px;">';
                    echo $sourceField;
                    $sourceField = strtolower($sourceField);
                    echo '<input type="hidden" name="sf['.$i.']" value="'.$sourceField.'" />';
                    echo '</td><td>';
                    echo '<select name="tf['.$i.']" style="background:'.(!array_key_exists($sourceField,$translationMap)?'yellow':'').'">';
                    echo '<option value="">Select Target Field</option>';
                    echo '<option value="">-------------------------</option>';
                    echo '<option value="catalognumber" '.(isset($translationMap[$sourceField]) && $translationMap[$sourceField] === 'catalognumber'?'SELECTED':'').'>Catalog Number (required)</option>';
                    echo '<option value="originalurl" '.(isset($translationMap[$sourceField]) && $translationMap[$sourceField] === 'originalurl'?'SELECTED':'').'>Large Image URL (required)</option>';
                    echo '<option value="url" '.(isset($translationMap[$sourceField]) && $translationMap[$sourceField] === 'url'?'SELECTED':'').'>Web Image URL</option>';
                    echo '<option value="thumbnailurl" '.(isset($translationMap[$sourceField]) && $translationMap[$sourceField] === 'thumbnailurl'?'SELECTED':'').'>Thumbnail URL</option>';
                    echo '</select>';
                    echo '</td></tr>';
                }
            }
        }
    }

    public function loadFileData($postArr): void
    {
        if(isset($postArr['filename'], $postArr['tf'])){
            $fieldMap = array_flip($postArr['tf']);
            $fullPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) !== '/'?'/':'').'temp/data/'.$postArr['filename'];
            if($fh = fopen($fullPath,'rb')){
                fgetcsv($fh);
                while($recordArr = fgetcsv($fh)){
                    if($recordArr){
                        $catalogNumber = (isset($fieldMap['catalognumber'])?SanitizerService::cleanInStr($this->conn,$recordArr[$fieldMap['catalognumber']]):'');
                        $originalUrl = (isset($fieldMap['originalurl'])?SanitizerService::cleanInStr($this->conn,$recordArr[$fieldMap['originalurl']]):'');
                        $url = (isset($fieldMap['url'])?SanitizerService::cleanInStr($this->conn,$recordArr[$fieldMap['url']]):'');
                        $thumbnailUrl = (isset($fieldMap['thumbnailurl'])?SanitizerService::cleanInStr($this->conn,$recordArr[$fieldMap['thumbnailurl']]):'');
                        if($catalogNumber && $originalUrl){
                            echo '<li>Processing catalogNumber: '.$catalogNumber.'</li>';
                            $occArr = array();
                            $sql = 'SELECT occid FROM omoccurrences WHERE collid = '.$this->collid.' AND catalognumber = "'.$catalogNumber.'"';
                            $rs = $this->conn->query($sql);
                            while($r = $rs->fetch_object()){
                                $occArr[] = $r->occid;
                            }
                            $rs->free();
                            if($occArr){
                                $origFileName = substr(strrchr($originalUrl, '/'), 1);
                                $urlFileName = substr(strrchr($url, '/'), 1);
                                foreach($occArr as $k => $occid){
                                    $sql1 = 'SELECT imgid, url, originalurl, thumbnailurl FROM images WHERE (occid = '.$occid.')';
                                    $rs1 = $this->conn->query($sql1);
                                    while($r1 = $rs1->fetch_object()){
                                        $uFileName = substr(strrchr($r1->url, '/'), 1);
                                        $oFileName = substr(strrchr($r1->originalurl, '/'), 1);
                                        if(($oFileName && ($oFileName === $origFileName || $oFileName === $urlFileName)) || ($uFileName && ($uFileName === $origFileName || $uFileName === $urlFileName))){
                                            $sql2 = 'UPDATE images '.
                                                'SET url = "'.$url.'", originalurl = "'.$originalUrl.'", thumbnailurl = '.($thumbnailUrl?'"'.$thumbnailUrl.'"':'NULL').' '.
                                                'WHERE imgid = '.$r1->imgid;
                                            if($this->conn->query($sql2)){
                                                echo '<li style="margin-left:10px">Existing image replaced with new image mapping: <a href="../editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">'.$catalogNumber.'</a></li>';
                                                $this->deleteImage($r1->url);
                                                $this->deleteImage($r1->originalurl);
                                                $this->deleteImage($r1->thumbnailurl);
                                                unset($occArr[$k]);
                                                break;
                                            }

                                            echo '<li style="margin-left:10px">ERROR updating existing image record.</li>';
                                        }
                                    }
                                    $rs1->free();
                                }
                            }
                            else{
                                $sqlIns = 'INSERT INTO omoccurrences(collid,catalognumber,processingstatus,dateentered) '.
                                    'VALUES('.$this->collid.',"'.$catalogNumber.'","unprocessed",now())';
                                if($this->conn->query($sqlIns)){
                                    $occArr[] = $this->conn->insert_id;
                                    echo '<li style="margin-left:10px">Unable to find record with matching catalogNumber; new occurrence record created</li>';
                                }
                                else{
                                    echo '<li style="margin-left:10px">ERROR creating new occurrence record.</li>';
                                }
                            }
                            foreach($occArr as $occid){
                                $sqlInsert = 'INSERT INTO images(occid,url,originalurl,thumbnailurl) '.
                                    'VALUES('.$occid.',"'.$url.'","'.$originalUrl.'",'.($thumbnailUrl?'"'.$thumbnailUrl.'"':'NULL').')';
                                if($this->conn->query($sqlInsert)){
                                    echo '<li style="margin-left:10px">Image URLs linked to: <a href="../editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">'.$catalogNumber.'</a></li>';
                                }
                                else{
                                    echo '<li style="margin-left:10px">ERROR loading image.</li>';
                                }
                            }
                        }
                    }
                }
            }
            fclose($fh);
            unlink($fullPath);
        }
    }

    private function deleteImage($imgUrl): void
    {
        if(strncasecmp($imgUrl, 'http', 4) === 0 || strncasecmp($imgUrl, 'https', 5) === 0){
            $imgUrl = parse_url($imgUrl, PHP_URL_PATH);
        }
        if($GLOBALS['IMAGE_ROOT_URL'] && strpos($imgUrl,$GLOBALS['IMAGE_ROOT_URL']) === 0){
            $imgPath = $GLOBALS['IMAGE_ROOT_PATH'].substr($imgUrl,strlen($GLOBALS['IMAGE_ROOT_URL']));
            unlink($imgPath);
        }
    }

    private function getOccid($specPk,$sourceIdentifier,$fileName = null){
        $occid = 0;
        if($this->collid){
            if($this->matchCatalogNumber){
                $sql = 'SELECT occid FROM omoccurrences WHERE (collid = '.$this->collid.') '.
                    'AND (catalognumber IN("'.$specPk.'"'.(strncmp($specPk, '0', 1) === 0 ?',"'.ltrim($specPk,'0 ').'"':'').')) ';
                $rs = $this->conn->query($sql);
                if($row = $rs->fetch_object()){
                    $occid = $row->occid;
                }
                $rs->free();
            }
            if(!$occid && $this->matchOtherCatalogNumbers){
                $sql = 'SELECT occid FROM omoccurrences WHERE (collid = '.$this->collid.') '.
                    'AND (othercatalognumbers IN("'.$specPk.'"'.(strncmp($specPk, '0', 1) === 0 ?',"'.ltrim($specPk,'0 ').'"':'').')) ';
                $rs = $this->conn->query($sql);
                if($row = $rs->fetch_object()){
                    $occid = $row->occid;
                }
                $rs->free();
            }
            if($occid){
                $occLink = '<a href="../individual/index.php?occid='.$occid.'" target="_blank">'.$occid.'</a>';
                if($fileName){
                    $fileBaseName = $fileName;
                    $fileExt = '';
                    $dotPos = strrpos($fileName,'.');
                    if($dotPos){
                        $fileBaseName = substr($fileName,0,$dotPos);
                        $fileExt = strtolower(substr($fileName,$dotPos+1));
                    }
                    $imgArr = array();
                    $sqlTest = 'SELECT imgid, sourceidentifier FROM images WHERE (occid = '.$occid.') ';
                    $rsTest = $this->conn->query($sqlTest);
                    while($rTest = $rsTest->fetch_object()){
                        $imgArr[$rTest->imgid] = $rTest->sourceidentifier;
                    }
                    $rsTest->free();
                    $highResList = array('cr2','dng','tiff','tif','nef');
                    foreach($imgArr as $imgId => $sourceId){
                        if($sourceId && preg_match('/^([A-Za-z0-9\-]+);\sfilename:\s(.+)$/', $sourceId, $m)) {
                            $guid = $m[1];
                            $fn = $m[2];
                            $fnArr = explode('.',$fn);
                            $fnExt = strtolower(array_pop($fnArr));
                            $fnBase = implode($fnArr);
                            if ($guid === $sourceIdentifier) {
                                $occid = false;
                                $this->logOrEcho('NOTICE: Image mapping skipped; image identifier ('.$sourceIdentifier.') already in system (#'.$occLink.')',2);
                                break;
                            }

                            if ($fn === $fileName) {
                                $occid = false;
                                $this->logOrEcho('NOTICE: Image mapping skipped; file ('.$fileName.') already in system (#'.$occLink.')',2);
                                break;
                            }

                            if ($fileBaseName  === $fnBase && $fnExt === 'jpg') {
                                $occid = false;
                                break;
                            }

                            if($fileExt === 'jpg' && in_array($fnExt, $highResList, true)) {
                                $this->conn->query('DELETE FROM images WHERE imgid = '.$imgId);
                            }
                        }
                    }
                }
                else if($sourceIdentifier){
                    $sql = 'DELETE i.* FROM images i INNER JOIN omoccurrences o ON i.occid = o.occid '.
                        'WHERE (o.occid = '.$occid.') AND (i.originalurl LIKE "http%://api.idigbio.org%") AND (i.sourceIdentifier = "'.$sourceIdentifier.'")';
                    $this->conn->query($sql);
                    $this->logOrEcho('Replacing previously mapped image with new input',2);
                }
                if($occid) {
                    $this->logOrEcho('Linked image to existing record (' . ($fileName ? $fileName . '; ' : '') . '#' . $occLink . ') ', 2);
                }
            }
            else{
                $sql2 = 'INSERT INTO omoccurrences(collid,'.($this->matchCatalogNumber?'catalognumber':'othercatalognumbers').',processingstatus,dateentered) '.
                    'VALUES('.$this->collid.',"'.$specPk.'","unprocessed","'.date('Y-m-d H:i:s').'")';
                if($this->conn->query($sql2)){
                    $occid = $this->conn->insert_id;
                    $this->logOrEcho('Linked image to new "unprocessed" occurrence record (#<a href="../individual/index.php?occid='.$occid.'" target="_blank">'.$occid.'</a>) ',2);
                }
                else{
                    $this->logOrEcho('ERROR creating new occurrence record.',2);
                }
            }
        }
        return $occid;
    }

    private function databaseImage($occid,$webUrl,$tnUrl,$lgUrl,$archiveUrl,$ownerStr,$sourceIdentifier): bool
    {
        if($occid){
            $format = 'image/jpeg';
            $sql = 'INSERT INTO images(occid,url,thumbnailurl,originalurl,archiveurl,owner,sourceIdentifier,format) '.
                'VALUES ('.$occid.',"'.$webUrl.'",'.($tnUrl?'"'.$tnUrl.'"':'NULL').','.($lgUrl?'"'.$lgUrl.'"':'NULL').','.
                ($archiveUrl?'"'.$archiveUrl.'"':'NULL').','.($ownerStr?'"'.SanitizerService::cleanInStr($this->conn,$ownerStr).'"':'NULL').','.
                ($sourceIdentifier?'"'.SanitizerService::cleanInStr($this->conn,$sourceIdentifier).'"':'NULL').',"'.$format.'")';
            if($this->conn->query($sql)){
                $status = true;
            }
            else{
                $status = false;
                $this->logOrEcho('ERROR: Unable to load image record into database.',3);
            }
        }
        else{
            $status = false;
            $this->logOrEcho('ERROR: Missing occid (omoccurrences PK), unable to load record ',2);
        }
        return $status;
    }

    private function cleanHouse($collList): void
    {
        $this->logOrEcho('Updating collection statistics...',1);
        $occurMain = new OccurrenceMaintenance($this->conn);

        if($collList){
            $this->logOrEcho('Updating collection statistics...',2);
            foreach($collList as $collid){
                if(!$occurMain->updateCollectionStats($collid)){
                    $errorArr = $occurMain->getErrorArr();
                    foreach($errorArr as $errorStr){
                        $this->logOrEcho($errorStr,1);
                    }
                }
            }
        }
        $occurMain->__destruct();

        $this->logOrEcho('Populating global unique identifiers (GUIDs) for all records...',2);
        $uuidManager = new GUIDManager($this->conn);
        $uuidManager->setSilent(1);
        $uuidManager->populateGuids();
        $uuidManager->__destruct();
    }

    private function updateLastRunDate($date): void
    {
        if($this->spprid){
            $sql = 'UPDATE specprocessorprojects SET source = "'.$date.'" WHERE spprid = '.$this->spprid;
            if(!$this->conn->query($sql)){
                $this->logOrEcho('ERROR updating last run date.');
            }
        }
    }

    private function setCollArr(): void
    {
        if($this->collid){
            $sql = 'SELECT collid, institutioncode, collectioncode, collectionname, managementtype '.
                'FROM omcollections '.
                'WHERE (collid = '.$this->collid.')';
            $rs = $this->conn->query($sql);
            while($r = $rs->fetch_object()){
                $this->collArr['instcode'] = $r->institutioncode;
                $this->collArr['collcode'] = $r->collectioncode;
                $this->collArr['collname'] = $r->collectionname;
                $this->collArr['managementtype'] = $r->managementtype;
            }
            $rs->free();
        }
    }

    public function setCollid($id): void
    {
        $this->collid = $id;
        $this->setCollArr();
    }

    public function setSpprid($spprid): void
    {
        if(is_numeric($spprid)){
            $this->spprid = $spprid;
        }
    }

    public function setLogMode($c): void
    {
        $this->logMode = $c;
    }

    private function logOrEcho($str, $indent = null): void
    {
        if(($this->logMode > 1) && $this->logFH) {
            if($indent) {
                $str = "\t" . $str;
            }
            fwrite($this->logFH,strip_tags($str)."\n");
        }
        if($this->logMode === 1 || $this->logMode === 3){
            echo '<li '.($indent?'style="margin-left:'.($indent?$indent*15:'0').'px"':'').'>'.$str."</li>\n";
            flush();
        }
    }
}
