<?php
include_once(__DIR__ . '/SpecUploadBase.php');

class SpecUploadFile extends SpecUploadBase{

    private $ulFileName;
    private $delimiter = ',';
    private $isCsv = false;

    public function __construct() {
        parent::__construct();
        $this->setUploadTargetPath();
        ini_set('auto_detect_line_endings', true);
    }

    public function uploadFile(){
        if(!$this->ulFileName){
            $finalPath = '';
            if(array_key_exists('ulfnoverride',$_POST) && $_POST['ulfnoverride']){
                $this->ulFileName = substr($_POST['ulfnoverride'],strrpos($_POST['ulfnoverride'],'/')+1);
                if(copy($_POST['ulfnoverride'],$this->uploadTargetPath.$this->ulFileName)){
                    $finalPath = $this->uploadTargetPath.$this->ulFileName;
                }
            }
            elseif(array_key_exists('uploadfile',$_FILES)){
                $this->ulFileName = $_FILES['uploadfile']['name'];
                if(is_writable($this->uploadTargetPath)){
                    if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->uploadTargetPath.$this->ulFileName)){
                        $finalPath = $this->uploadTargetPath.$this->ulFileName;
                    }
                    else{
                        echo '<div style="margin:15px;font-weight:bold;font-size:120%;">';
                        echo 'ERROR uploading file (code '.$_FILES['uploadfile']['error'].'): ';
                        echo 'Zip file may be too large for the upload limits set within the PHP configurations (upload_max_filesize = '.ini_get('upload_max_filesize').'; post_max_size = '.ini_get('post_max_size').')';
                        echo '</div>';
                        return false;
                    }
                }
                else{
                    echo 'Target path ('.$this->uploadTargetPath.') is not writable ';
                }
            }
            if($finalPath && substr($this->ulFileName,-4) === '.zip'){
                $this->ulFileName = '';
                $zipFilePath = $finalPath;
                $zip = new ZipArchive;
                $res = $zip->open($finalPath);
                if($res === TRUE) {
                    for($i = 0; $i < $zip->numFiles; $i++) {
                        $fileName = $zip->getNameIndex($i);
                        if(strpos($fileName, '._') !== 0){
                            $ext = strtolower(substr(strrchr($fileName, '.'), 1));
                            if($ext === 'csv' || $ext === 'txt'){
                                if($this->uploadType != $this->NFNUPLOAD || stripos($fileName,'.reconcile.')){
                                    $this->ulFileName = $fileName;
                                    $zip->extractTo($this->uploadTargetPath,$fileName);
                                    $zip->close();
                                    unlink($zipFilePath);
                                    break;
                                }
                            }
                        }
                    }
                }
                else{
                    echo 'failed, code:' . $res;
                    return false;
                }
            }
        }
        return $this->ulFileName;
    }

    public function analyzeUpload(): bool
    {
        if(strpos($this->ulFileName, 'http') === 0){
            $fullPath = $this->ulFileName;
        }
        else{
            $fullPath = $this->uploadTargetPath.$this->ulFileName;
        }
        if($fullPath){
            $fh = fopen($fullPath,'rb') or die("Can't open file");
            $this->sourceArr = $this->getHeaderArr($fh);
            fclose($fh);
        }
        return true;
    }

    public function uploadData($finalTransfer): void
    {
        if($this->ulFileName){
            set_time_limit(7200);
            ini_set('max_input_time',240);

            $this->outputMsg('<li>Initiating import from: '.$this->ulFileName.'</li>');
            $this->prepUploadData();

            $fullPath = $this->uploadTargetPath.$this->ulFileName;
            $fh = fopen($fullPath,'rb') or die("Can't open file");

            $headerArr = $this->getHeaderArr($fh);

            $this->transferCount = 0;
            $indexArr = array();
            foreach($this->fieldMap as $symbField => $sMap){
                $indexArr[$symbField] = array_search($sMap['field'], $headerArr, false);
            }

            $this->outputMsg('<li>Beginning to load records...</li>',1);
            while($recordArr = $this->getRecordArr($fh)){
                $recMap = array();
                foreach($indexArr as $symbField => $index){
                    if(array_key_exists((int)$index, $recordArr) && $recordArr && (is_string($index) || is_int($index))) {
                        $valueStr = $recordArr[$index];
                        if(strpos($valueStr, '"') === 0 && substr($valueStr,-1) === '"'){
                            $valueStr = substr($valueStr,1, -1);
                        }
                        $recMap[$symbField] = $valueStr;
                    }
                }
                if((int)$this->uploadType === $this->SKELETAL && !$recMap['catalognumber']){
                    unset($recMap);
                    continue;
                }
                if((int)$this->uploadType === $this->SKELETAL && (!array_key_exists('recordenteredby', $recMap) || !$recMap['recordenteredby'])){
                    $recMap['recordenteredby'] = 'preprocessed';
                }
                $this->loadRecord($recMap);
                unset($recMap);
            }
            fclose($fh);

            if(file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->cleanUpload();

            if((int)$this->uploadType === $this->NFNUPLOAD){
                $this->nfnIdentifier = 'url';
                $testSql = 'SELECT tempfield01, tempfield02 FROM uploadspectemp WHERE tempfield02 IS NOT NULL AND collid IN('.$this->collId.') LIMIT 1';
                $testRS = $this->conn->query($testSql);
                if($testRow = $testRS->fetch_object()){
                    if(strlen($testRow->tempfield02) === 45 || strlen($testRow->tempfield02) === 36) {
                        $this->nfnIdentifier = 'uuid';
                    }
                    if(!$this->nfnIdentifier === 'uuid' && !$testRow->tempfield02){
                        $this->outputMsg('<li>ERROR: identifier fields appear to NULL (recordID GUID and subject_references fields)</li>');
                    }
                }
                $testRS->free();
                if($this->nfnIdentifier === 'uuid'){
                    $sqlA = 'UPDATE uploadspectemp SET tempfield02 = substring(tempfield02,10) WHERE tempfield02 LIKE "urn:uuid:%"';
                    if(!$this->conn->query($sqlA)){
                        $this->outputMsg('<li>ERROR cleaning recordID GUID</li>');
                    }
                    $sqlB = 'UPDATE uploadspectemp u INNER JOIN guidoccurrences g ON u.tempfield02 = g.guid '.
                        'SET u.occid = g.occid '.
                        'WHERE (u.collid IN('.$this->collId.')) AND (u.occid IS NULL)';
                    if(!$this->conn->query($sqlB)){
                        $this->outputMsg('<li>ERROR populating occid from recordID GUID (stage1): '.$this->conn->error.'</li>');
                    }
                    $sqlC = 'UPDATE uploadspectemp u INNER JOIN omoccurrences o ON u.tempfield02 = o.occurrenceid '.
                        'SET u.occid = o.occid '.
                        'WHERE (u.collid IN('.$this->collId.')) AND (o.collid IN('.$this->collId.')) AND (u.occid IS NULL)';
                    if(!$this->conn->query($sqlC)){
                        $this->outputMsg('<li>ERROR populating occid from recordID GUID (stage2): '.$this->conn->error.'</li>');
                    }
                }
                else{
                    $convSql = 'UPDATE uploadspectemp '.
                        'SET occid = substring_index(tempfield01,"=",-1) '.
                        'WHERE (collid IN('.$this->collId.')) AND (occid IS NULL)';
                    if(!$this->conn->query($convSql)){
                        $this->outputMsg('<li>ERROR update to extract occid from subject_references field</li>');
                    }
                }
                $sql = 'UPDATE uploadspectemp u LEFT JOIN omoccurrences o ON u.occid = o.occid '.
                    'SET u.occid = NULL '.
                    'WHERE (u.collid IN('.$this->collId.')) AND (o.collid NOT IN('.$this->collId.') OR o.collid IS NULL)';
                if(!$this->conn->query($sql)){
                    $this->outputMsg('<li>ERROR unlinking bad records</li>');
                }
            }

            if($finalTransfer){
                $this->transferOccurrences();
                $this->finalCleanup();
            }
            else{
                $this->outputMsg('<li>Record upload complete, ready for final transfer and activation</li>');
            }
        }
        else{
            $this->outputMsg('<li>File Upload FAILED: unable to locate file</li>');
        }
    }

    private function getHeaderArr($fHandler): array
    {
        $headerData = fgets($fHandler);
        if((strpos($headerData, ',') === false) && strpos($headerData, "\t") !== false) {
            $this->delimiter = "\t";
        }
        if(strpos($headerData,$this->delimiter.'"') !== false || strtolower(substr($this->ulFileName, -4)) === '.csv'){
            $this->isCsv = true;
        }
        if($this->isCsv){
            rewind($fHandler);
            $headerArr = fgetcsv($fHandler,0,$this->delimiter);
        }
        else{
            $headerArr = explode($this->delimiter,$headerData);
        }
        $retArr = array();
        foreach($headerArr as $field){
            $fieldStr = strtolower(trim($field));
            if($fieldStr){
                $retArr[] = $fieldStr;
            }
            else{
                break;
            }
        }
        return $retArr;
    }

    private function getRecordArr($fHandler){
        $recordArr = array();
        if($this->isCsv){
            $recordArr = fgetcsv($fHandler,0,$this->delimiter);
        }
        else{
            $record = fgets($fHandler);
            if($record) {
                $recordArr = explode($this->delimiter, $record);
            }
        }
        return $recordArr;
    }

    public function setUploadFileName($ulFile): void
    {
        $this->ulFileName = $ulFile;
    }

    public function getDbpkOptions(): array
    {
        $sFields = $this->sourceArr;
        sort($sFields);
        return $sFields;
    }
}
