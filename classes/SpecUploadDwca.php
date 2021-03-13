<?php
include_once(__DIR__ . '/SpecUploadBase.php');

class SpecUploadDwca extends SpecUploadBase{

    private $baseFolderName;
    private $extensionFolderName = '';
    private $metaArr;
    private $delimiter = ',';
    private $enclosure = '"';
    private $encoding = 'utf-8';
    private $loopCnt = 0;

    public function __construct() {
        parent::__construct();
        $this->setUploadTargetPath();
    }

    public function uploadFile(): string
    {
        $localFolder = $this->collMetadataArr['institutioncode'].($this->collMetadataArr['collectioncode']?$this->collMetadataArr['collectioncode'].'_':'').time();
        if (!mkdir($concurrentDirectory = $this->uploadTargetPath . $localFolder) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
        $fullPath = $this->uploadTargetPath.$localFolder.'/dwca.zip';

        if(array_key_exists('ulfnoverride',$_POST) && $_POST['ulfnoverride'] && !$this->path){
            $this->path = $_POST['ulfnoverride'];
        }

        if($this->path){
            if($this->uploadType == $this->IPTUPLOAD){
                if(strpos($this->path,'/resource.do')){
                    $this->path = str_replace('/resource.do','/archive.do',$this->path);
                }
                elseif(strpos($this->path,'/resource?')){
                    $this->path = str_replace('/resource','/archive.do',$this->path);
                }
            }
            if(copy($this->path,$fullPath)){
                $this->baseFolderName = $localFolder;
            }
            else{
                $fp = fopen ($fullPath, 'wb+');
                $ch = curl_init(str_replace(" ","%20",$this->path));
                curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                if(!curl_exec($ch)){
                    $this->outputMsg('<li>ERROR: unable to upload file (path: '.$fullPath.') </li>');
                    $this->errorStr = 'ERROR: unable to upload file (path: '.$fullPath.')';
                }
                else{
                    $this->baseFolderName = $localFolder;
                }
                curl_close($ch);
                fclose($fp);
            }
        }
        elseif(array_key_exists('uploadfile',$_FILES)){
            if(is_writable($this->uploadTargetPath.$localFolder)){
                if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $fullPath)){
                    $this->baseFolderName = $localFolder;
                }
                else{
                    $msg = 'unknown';
                    $err = $_FILES['uploadfile']['error'];
                    if($err == 1) {
                        $msg = 'uploaded file exceeds the upload_max_filesize directive in php.ini';
                    }
                    elseif($err == 2) {
                        $msg = 'uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    }
                    elseif($err == 3) {
                        $msg = 'uploaded file was only partially uploaded';
                    }
                    elseif($err == 4) {
                        $msg = 'no file was uploaded';
                    }
                    elseif($err == 5) {
                        $msg = 'unknown error 5';
                    }
                    elseif($err == 6) {
                        $msg = 'missing a temporary folder';
                    }
                    elseif($err == 7) {
                        $msg = 'failed to write file to disk';
                    }
                    elseif($err == 8) {
                        $msg = 'a PHP extension stopped the file upload';
                    }
                    $this->outputMsg('<li>ERROR uploading file (target: '.$fullPath.'): '.$msg.' </li>');
                    $this->errorStr = 'ERROR uploading file: '.$msg;
                }
            }
            else{
                $this->errorStr = 'ERROR uploading file: Target path is not writable to server';
            }
        }

        if($this->baseFolderName && substr($this->baseFolderName,-1) !== '/') {
            $this->baseFolderName .= '/';
        }
        if($this->baseFolderName && !$this->unpackArchive()) {
            $this->baseFolderName = '';
        }
        return $this->baseFolderName;
    }

    public function analyzeUpload(): bool
    {
        $status = false;
        if($this->readMetaFile()){
            if(isset($this->metaArr['occur']['fields'])){
                $this->sourceArr = $this->metaArr['occur']['fields'];
                if(isset($this->metaArr['ident']['fields'])){
                    $this->identSourceArr = $this->metaArr['ident']['fields'];
                }
                $this->setImageSourceArr();
            }
            $status = true;
        }
        return $status;
    }

    private function unpackArchive(): bool
    {
        $status = true;
        $zip = new ZipArchive;
        $targetPath = $this->uploadTargetPath.$this->baseFolderName;
        $zip->open($targetPath.'dwca.zip');
        if($zip->extractTo($targetPath)){
            $this->locateBaseFolder($targetPath);
        }
        else{
            $err = $zip->getStatusString();
            if(!$err){
                if($this->uploadType == $this->IPTUPLOAD){
                    $err = 'target path does not appear to be a valid IPT instance';
                }
                else{
                    $err = 'Upload file or target path does not lead to a valid zip file';
                }
            }
            $this->outputMsg('<li>ERROR unpacking archive file: '.$err.'</li>');
            $this->errorStr = 'ERROR unpacking archive file: '.$err;
            $status = false;
        }
        $zip->close();
        return $status;
    }

    private function locateBaseFolder($baseDir, $pathFrag = ''): void
    {
        if($handle = opendir($baseDir.$pathFrag)) {
            while(false !== ($item = readdir($handle))){
                if($item){
                    $newPath = $baseDir.$pathFrag;
                    if(is_file($newPath.$item) || strtolower(substr($item,-4)) === '.zip'){
                        if(strtolower($item) === 'meta.xml'){
                            $this->extensionFolderName = $pathFrag;
                            break;
                        }
                    }
                    elseif($item !== '.' && $item !== '..' && is_dir($newPath)){
                        $pathFrag .= $item.'/';
                        $this->locateBaseFolder($baseDir, $pathFrag);
                    }
                }
            }
            closedir($handle);
        }
    }

    private function readMetaFile(): bool
    {
        if(!$this->metaArr){
            $this->locateBaseFolder($this->uploadTargetPath.$this->baseFolderName);
            $metaPath = $this->uploadTargetPath.$this->baseFolderName.$this->extensionFolderName.'meta.xml';
            if(file_exists($metaPath)){
                $metaDoc = new DOMDocument();
                $metaDoc->load($metaPath);
                $coreId = '';
                if($coreElements = $metaDoc->getElementsByTagName('core')){
                    foreach($coreElements as $coreElement){
                        $rowType = $coreElement->getAttribute('rowType');
                        if(stripos($rowType,'occurrence')){
                            if($idElement = $coreElement->getElementsByTagName('id')){
                                $coreId = $idElement->item(0)->getAttribute('index');
                                $this->metaArr['occur']['id'] = $coreId;
                            }
                            else{
                                $this->outputMsg('WARNING: Core ID absent');
                            }
                            if($locElements = $coreElement->getElementsByTagName('location')){
                                $this->metaArr['occur']['name'] = $locElements->item(0)->nodeValue;
                            }
                            else{
                                $this->outputMsg('ERROR: Unable to obtain the occurrence file name from meta.xml');
                                $this->errorStr = 'ERROR: Unable to obtain the occurrence file name from meta.xml';
                            }
                            $this->metaArr['occur']['encoding'] = $coreElement->getAttribute('encoding');
                            $this->metaArr['occur']['fieldsTerminatedBy'] = $coreElement->getAttribute('fieldsTerminatedBy');
                            $this->metaArr['occur']['linesTerminatedBy'] = $coreElement->getAttribute('linesTerminatedBy');
                            $this->metaArr['occur']['fieldsEnclosedBy'] = $coreElement->getAttribute('fieldsEnclosedBy');
                            $this->metaArr['occur']['ignoreHeaderLines'] = $coreElement->getAttribute('ignoreHeaderLines');
                            $this->metaArr['occur']['rowType'] = $rowType;
                            if($fieldElements = $coreElement->getElementsByTagName('field')){
                                foreach($fieldElements as $fieldElement){
                                    $term = $fieldElement->getAttribute('term');
                                    if(strpos($term,'/')) {
                                        $term = substr($term, strrpos($term, '/') + 1);
                                    }
                                    $this->metaArr['occur']['fields'][$fieldElement->getAttribute('index')] = $term;
                                }
                            }
                            if($coreId === '0' && !isset($this->metaArr['occur']['fields'][0])){
                                $this->metaArr['occur']['fields'][0] = 'id';
                            }
                            if(($this->metaArr['occur']['ignoreHeaderLines'] === 1) && $this->metaArr['occur']['fieldsTerminatedBy']) {
                                if($this->metaArr['occur']['fieldsTerminatedBy'] === '\t'){
                                    $this->delimiter = "\t";
                                }
                                else{
                                    $this->delimiter = $this->metaArr['occur']['fieldsTerminatedBy'];
                                }
                                $fullPath = $this->uploadTargetPath.$this->baseFolderName.$this->extensionFolderName.$this->metaArr['occur']['name'];
                                $fh = fopen($fullPath, 'rb') or die("Can't open occurrence file");
                                $headerArr = $this->getRecordArr($fh);
                                foreach($headerArr as $k => $v){
                                    if(strtolower($v) !== strtolower($this->metaArr['occur']['fields'][$k])){
                                        $msg = '<div style="margin-left:25px;">';
                                        $msg .= 'WARNING: meta.xml field order out of sync w/ '.$this->metaArr['occur']['name'].'; remapping: field #'.($k+1).' => '.$v;
                                        $msg .= '</div>';
                                        $this->outputMsg($msg);
                                        $this->errorStr = $msg;
                                        $this->metaArr['occur']['fields'][$k] = $v;
                                    }
                                }
                                fclose($fh);
                            }
                        }
                    }
                }
                else{
                    $this->outputMsg('ERROR: Unable to access core element in meta.xml');
                    $this->errorStr = 'ERROR: Unable to access core element in meta.xml';
                    return false;
                }
                if($this->metaArr){
                    $extensionElements = $metaDoc->getElementsByTagName('extension');
                    foreach($extensionElements as $extensionElement){
                        $rowType = $extensionElement->getAttribute('rowType');
                        $tagName = '';
                        if(stripos($rowType,'identification')){
                            $tagName = 'ident';
                        }
                        elseif(stripos($rowType,'image') || stripos($rowType,'audubon_core') || stripos($rowType,'Multimedia')){
                            $tagName = 'image';
                        }
                        $extCoreId = '';
                        if($coreidElement = $extensionElement->getElementsByTagName('coreid')){
                            $extCoreId = $coreidElement->item(0)->getAttribute('index');
                            $this->metaArr[$tagName]['coreid'] = $extCoreId;
                        }
                        if($coreId === '' || $coreId === $extCoreId){
                            if($tagName){
                                if($locElements = $extensionElement->getElementsByTagName('location')){
                                    $this->metaArr[$tagName]['name'] = $locElements->item(0)->nodeValue;
                                }
                                else{
                                    $this->outputMsg('WARNING: Unable to obtain the '.$tagName.' file name from meta.xml');
                                }
                                $this->metaArr[$tagName]['encoding'] = $extensionElement->getAttribute('encoding');
                                $this->metaArr[$tagName]['fieldsTerminatedBy'] = $extensionElement->getAttribute('fieldsTerminatedBy');
                                $this->metaArr[$tagName]['linesTerminatedBy'] = $extensionElement->getAttribute('linesTerminatedBy');
                                $this->metaArr[$tagName]['fieldsEnclosedBy'] = $extensionElement->getAttribute('fieldsEnclosedBy');
                                $this->metaArr[$tagName]['ignoreHeaderLines'] = $extensionElement->getAttribute('ignoreHeaderLines');
                                $this->metaArr[$tagName]['rowType'] = $rowType;
                                if($fieldElements = $extensionElement->getElementsByTagName('field')){
                                    foreach($fieldElements as $fieldElement){
                                        $term = $fieldElement->getAttribute('term');
                                        if(strpos($term,'/')) {
                                            $term = substr($term, strrpos($term, '/') + 1);
                                        }
                                        $index = $fieldElement->getAttribute('index');
                                        if(is_numeric($index)){
                                            $this->metaArr[$tagName]['fields'][$index] = $term;
                                        }
                                    }
                                }
                                $this->metaArr[$tagName]['fields'][$extCoreId] = 'coreid';

                                if(($this->metaArr[$tagName]['ignoreHeaderLines'] == 1) && $this->metaArr[$tagName]['fieldsTerminatedBy']) {
                                    if($this->metaArr[$tagName]['fieldsTerminatedBy'] === '\t'){
                                        $this->delimiter = "\t";
                                    }
                                    else{
                                        $this->delimiter = $this->metaArr[$tagName]['fieldsTerminatedBy'];
                                    }
                                    $fullPath = $this->uploadTargetPath.$this->baseFolderName.$this->extensionFolderName.$this->metaArr[$tagName]['name'];
                                    $fh = fopen($fullPath, 'rb') or die("Can't open $tagName extension file");
                                    $headerArr = $this->getRecordArr($fh);
                                    foreach($headerArr as $k => $v){
                                        $metaField = strtolower($this->metaArr[$tagName]['fields'][$k]);
                                        if($metaField !== 'coreid' && strtolower($v) !== $metaField){
                                            $msg = '<div style="margin-left:25px;">';
                                            $msg .= 'WARNING: meta.xml field order out of sync w/ '.$this->metaArr[$tagName]['name'].'; remapping: field #'.($k+1).' => '.$v;
                                            $msg .= '</div>';
                                            $this->outputMsg($msg);
                                            $this->errorStr = $msg;
                                            $this->metaArr[$tagName]['fields'][$k] = $v;
                                        }
                                    }
                                    fclose($fh);
                                }
                            }
                        }
                    }
                }
                else{
                    $this->errorStr = 'ERROR: Unable to obtain core element from meta.xml';
                    $this->outputMsg($this->errorStr);
                    return false;
                }
            }
            else{
                $this->errorStr = 'ERROR: Malformed DWCA, unable to locate ('.$metaPath.')';
                $this->outputMsg($this->errorStr);
                return false;
            }
        }
        return true;
    }

    public function uploadData($finalTransfer): void
    {
        $fullPath = $this->uploadTargetPath.$this->baseFolderName;
        if(file_exists($fullPath)){
            $this->prepUploadData();
            $this->readMetaFile();
            if(isset($this->metaArr['occur']['fields'])){
                $fullPath .= $this->extensionFolderName;
                if(isset($this->metaArr['occur']['fieldsTerminatedBy']) && $this->metaArr['occur']['fieldsTerminatedBy']){
                    if($this->metaArr['occur']['fieldsTerminatedBy'] === '\t'){
                        $this->delimiter = "\t";
                    }
                    else{
                        $this->delimiter = $this->metaArr['occur']['fieldsTerminatedBy'];
                    }
                }
                else{
                    $this->delimiter = '';
                }
                if(isset($this->metaArr['occur']['fieldsEnclosedBy']) && $this->metaArr['occur']['fieldsEnclosedBy']){
                    $this->enclosure = $this->metaArr['occur']['fieldsEnclosedBy'];
                }
                if(isset($this->metaArr['occur']['encoding']) && $this->metaArr['occur']['encoding']){
                    $this->encoding = strtolower(str_replace('-','',$this->metaArr['occur']['encoding']));
                }
                $id = $this->metaArr['occur']['id'];

                $fullPath .= $this->metaArr['occur']['name'];
                if(file_exists($fullPath)){
                    $fh = fopen($fullPath, 'rb') or die("Can't open occurrence file");

                    if($this->metaArr['occur']['ignoreHeaderLines'] == '1'){
                        $this->getRecordArr($fh);
                    }

                    $cset = strtolower(str_replace('-','',$GLOBALS['CHARSET']));
                    $this->sourceArr = array();
                    foreach($this->metaArr['occur']['fields'] as $k => $v){
                        $this->sourceArr[$k] = strtolower($v);
                    }
                    $this->transferCount = 0;
                    if(!isset($this->fieldMap['dbpk']['field']) || !in_array($this->fieldMap['dbpk']['field'], $this->sourceArr, true)){
                        $this->fieldMap['dbpk']['field'] = strtolower($this->metaArr['occur']['fields'][$id]);
                    }
                    $collName = $this->collMetadataArr['name'].' ('.$this->collMetadataArr['institutioncode'];
                    if($this->collMetadataArr['collectioncode']) {
                        $collName .= '-' . $this->collMetadataArr['collectioncode'];
                    }
                    $collName .= ')';
                    $this->outputMsg('<li>Uploading data for: '.$collName.'</li>');
                    $this->conn->query('SET autocommit=0');
                    $this->conn->query('SET unique_checks=0');
                    $this->conn->query('SET foreign_key_checks=0');
                    while($recordArr = $this->getRecordArr($fh)){
                        $recMap = array();
                        foreach($this->fieldMap as $symbField => $sMap){
                            if(strpos($symbField, 'unmapped') !== 0){
                                $indexArr = array_keys($this->sourceArr,$sMap['field']);
                                $index = array_shift($indexArr);
                                if(array_key_exists($index,$recordArr)){
                                    $valueStr = trim($recordArr[$index]);
                                    if($valueStr){
                                        if($cset != $this->encoding) {
                                            $valueStr = $this->encodeString($valueStr);
                                        }
                                        $recMap[$symbField] = $valueStr;
                                    }
                                }
                            }
                        }
                        $this->loadRecord($recMap);
                        unset($recMap);
                    }
                    fclose($fh);
                    $this->conn->query('COMMIT');
                    $this->conn->query('SET autocommit=1');
                    $this->conn->query('SET unique_checks=1');
                    $this->conn->query('SET foreign_key_checks=1');
                    $this->outputMsg('<li style="margin-left:10px;">Complete: '.$this->getTransferCount().' records loaded</li>');
                    flush();

                    if($this->includeIdentificationHistory){
                        $this->outputMsg('<li>Loading identification history extension... </li>');
                        foreach($this->metaArr['ident']['fields'] as $k => $v){
                            $this->identSourceArr[$k] = strtolower($v);
                        }
                        $this->uploadExtension('ident',$this->identFieldMap,$this->identSourceArr);
                        $this->outputMsg('<li style="margin-left:10px;">Complete: '.$this->identTransferCount.' records loaded</li>');
                    }

                    if($this->includeImages){
                        $this->outputMsg('<li>Loading image extension... </li>');
                        $this->setImageSourceArr();
                        $this->uploadExtension('image',$this->imageFieldMap,$this->imageSourceArr);
                        $this->outputMsg('<li style="margin-left:10px;">Complete: '.$this->imageTransferCount.' records loaded</li>');
                    }

                    $this->cleanUpload();

                    if($finalTransfer){
                        $this->finalTransfer();
                    }

                    $this->removeFiles($this->uploadTargetPath.$this->baseFolderName);
                }
                else{
                    $this->errorStr = 'ERROR: unable to locate occurrence upload file ('.$fullPath.')';
                    $this->outputMsg('<li>'.$this->errorStr.'</li>');
                }
            }
        }
        else{
            $this->errorStr = 'ERROR: unable to locate base path ('.$fullPath.')';
            $this->outputMsg('<li>'.$this->errorStr.'</li>');
        }
    }

    private function setImageSourceArr(): void
    {
        if(isset($this->metaArr['image']['fields'])){
            foreach($this->metaArr['image']['fields'] as $k => $v){
                $v = strtolower($v);
                $prefixStr = '';
                if(in_array($v, $this->imageSourceArr, true)) {
                    $prefixStr = 'dcterms:';
                }
                $this->imageSourceArr[$k] = $prefixStr.$v;
            }
        }
    }

    private function removeFiles($baseDir,$pathFrag = ''): void
    {
        $dirPath = $baseDir.$pathFrag;
        if($handle = opendir($dirPath)) {
            while(false !== ($item = readdir($handle))) {
                if($item){
                    if(is_file($dirPath.$item) || strtolower(substr($item,-4)) === '.zip'){
                        if(stripos($dirPath,$this->uploadTargetPath) === 0){
                            unlink($dirPath.$item);
                        }
                    }
                    elseif($item !== '.' && $item !== '..' && is_dir($dirPath)){
                        $pathFrag .= $item.'/';
                        $this->removeFiles($baseDir, $pathFrag);
                    }
                    if($this->loopCnt > 15) {
                        break;
                    }
                }
                $this->loopCnt++;
            }
            closedir($handle);
        }
        if(stripos($dirPath,$this->uploadTargetPath) === 0){
            rmdir($dirPath);
        }
    }

    private function uploadExtension($targetStr,$fieldMap,$sourceArr): void
    {
        $fullPathExt = '';
        if($this->metaArr[$targetStr]['name']){
            $fullPathExt = $this->uploadTargetPath.$this->baseFolderName.$this->extensionFolderName.$this->metaArr[$targetStr]['name'];
        }
        if($fullPathExt && file_exists($fullPathExt)){
            if(isset($this->metaArr[$targetStr]['fields'])){
                if(isset($this->metaArr[$targetStr]['fieldsTerminatedBy']) && $this->metaArr[$targetStr]['fieldsTerminatedBy']){
                    if($this->metaArr[$targetStr]['fieldsTerminatedBy'] === '\t'){
                        $this->delimiter = "\t";
                    }
                    else{
                        $this->delimiter = $this->metaArr[$targetStr]['fieldsTerminatedBy'];
                    }
                }
                else{
                    $this->delimiter = '';
                }
                if(isset($this->metaArr[$targetStr]['fieldsEnclosedBy']) && $this->metaArr[$targetStr]['fieldsEnclosedBy']){
                    $this->enclosure = $this->metaArr[$targetStr]['fieldsEnclosedBy'];
                }
                if(isset($this->metaArr[$targetStr]['encoding']) && $this->metaArr[$targetStr]['encoding']){
                    $this->encoding = strtolower(str_replace('-','',$this->metaArr[$targetStr]['encoding']));
                }
                $fh = fopen($fullPathExt, 'rb') or die("Can't open extension file");

                if($this->metaArr[$targetStr]['ignoreHeaderLines'] === '1'){
                    $this->getRecordArr($fh);
                }
                $cset = strtolower(str_replace('-','',$GLOBALS['CHARSET']));

                $fieldMap['dbpk']['field'] = 'coreid';
                while($recordArr = $this->getRecordArr($fh)){
                    $recMap = array();
                    foreach($fieldMap as $symbField => $iMap){
                        if(strpos($symbField, 'unmapped') !== 0){
                            $indexArr = array_keys($sourceArr,$iMap['field']);
                            $index = array_shift($indexArr);
                            if(array_key_exists($index,$recordArr)){
                                $valueStr = trim($recordArr[$index]);
                                if($valueStr){
                                    if($cset != $this->encoding) {
                                        $valueStr = $this->encodeString($valueStr);
                                    }
                                    $recMap[$symbField] = $valueStr;
                                }
                            }
                        }
                    }
                    if($targetStr === 'ident'){
                        $this->loadIdentificationRecord($recMap);
                    }
                    elseif($targetStr === 'image'){
                        $this->loadImageRecord($recMap);
                    }
                    unset($recMap);
                }
                fclose($fh);
            }
            else{
                $errMsg = 'ERROR: fields not defined within extension file ('.$fullPathExt.')';
                $this->outputMsg($errMsg);
                $this->errorStr = $errMsg;
            }
        }
        else{
            $errMsg = 'ERROR locating extension file within archive: '.$fullPathExt;
            $this->outputMsg($errMsg);
            $this->errorStr = $errMsg;
        }
    }

    private function getRecordArr($fHandler){
        if($this->delimiter){
            $recordArr = fgetcsv($fHandler,0,$this->delimiter,$this->enclosure);
        }
        else{
            $record = fgets($fHandler);
            if(substr($this->metaArr['occur']['name'],-4) === '.csv'){
                $this->delimiter = ',';
            }
            elseif(strpos($record,"\t") !== false){
                $this->delimiter = "\t";
            }
            elseif(strpos($record, '|') !== false){
                $this->delimiter = '|';
            }
            else{
                $this->delimiter = ',';
            }
            $recordArr = explode($this->delimiter,$record);
            if($this->enclosure){
                foreach($recordArr as $k => $v){
                    if(strpos($v, $this->enclosure) === 0 && substr($v,-1) === $this->enclosure){
                        $recordArr[$k] = substr($v,1, -1);
                    }
                }
            }
        }
        return $recordArr;
    }

    public function setBaseFolderName($name): void
    {
        $this->baseFolderName = $name;
        if($this->baseFolderName && substr($this->baseFolderName,-1) !== '/') {
            $this->baseFolderName .= '/';
        }
    }

    public function getDbpk(){
        $dbpk = parent::getDbpk();
        if(!$dbpk) {
            $dbpk = 'id';
        }
        return $dbpk;
    }

    public function getMetaArr(){
        return $this->metaArr;
    }
}
