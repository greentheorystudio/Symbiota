<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/OccurrenceAPIManager.php');
include_once(__DIR__ . '/OccurrenceUtilities.php');
include_once(__DIR__ . '/OccurrenceEditorManager.php');
include_once(__DIR__ . '/SOLRManager.php');

class FieldGuideManager {

    private $conn;
    private $collId = 0;
    private $jobId = '';
    private $token = '';
    private $taxon = '';
    private $viewMode = '';
    private $recStart = 0;
    private $recLimit = 0;
    private $fgResultTot = 0;
    private $fgResultArr = array();
    private $fgImageCntArr = array();
    private $fgResOccArr = array();
    private $fgResTidArr = array();
    private $resultArr = array();
    protected $serverDomain;

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function checkFGLog($collid){
        $retArr = array();
        $jsonFileName = $collid.'-FGLog.json';
        $jsonFile = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) === '/'?'':'/').'temp/data/fieldguide/'.$jsonFileName;
        if(file_exists($jsonFile)){
            $jsonStr = file_get_contents($jsonFile);
            $retArr = json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);
        }
        return $retArr;
    }

    public function processCurrentJobs($jobs){
        foreach($jobs as $job => $jArr){
            $pArr['job_id'] = $job;
            $headers = array(
                'authorization: Token '.$GLOBALS['FIELDGUIDE_API_KEY'],
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Content-Length: '.strlen(http_build_query($pArr))
            );
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => 'https://fieldguide.net/api2/symbiota/cv_job_status',
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 90,
                CURLOPT_POSTFIELDS => http_build_query($pArr),
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            curl_close($ch);
            if($result){
                $statArr = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
                $jobs[$job]['status'] = ($statArr['status']?:'');
                $jobs[$job]['progress'] = ($statArr['progress']?:'');
            }
        }
        return $jobs;
    }

    public function initiateFGBatchProcess(): string
    {
        $status = '';
        $this->setServerDomain();
        $imgArr = $this->getFGBatchImgArr();
        if($imgArr){
            $processDataArr = array();
            $pArr = array();
            $token = '';
            try {
                $token = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    random_int(0, 0xffff), random_int(0, 0xffff),
                    random_int(0, 0xffff),
                    random_int(0, 0x0fff) | 0x4000,
                    random_int(0, 0x3fff) | 0x8000,
                    random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff)
                );
            } catch (Exception $e) {}
            if($token){
                $jsonFileName = $this->collId.'-i-'.$token.'.json';
                $jobID = $this->collId.'_'.$token;
                $processDataArr['job_id'] = $jobID;
                $processDataArr['parenttaxon'] = $this->taxon;
                $processDataArr['dateinitiated'] = date('Y-m-d');
                $processDataArr['images'] = $imgArr;
                $fp = fopen($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$jsonFileName, 'wb');
                fwrite($fp, json_encode($processDataArr, JSON_THROW_ON_ERROR));
                fclose($fp);
                $dataFileUrl = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/').'temp/data/fieldguide/'.$jsonFileName;
                $responseUrl = $this->serverDomain.$GLOBALS['CLIENT_ROOT'].(substr($GLOBALS['CLIENT_ROOT'],-1) === '/'?'':'/').'webservices/fieldguidebatch.php';
                $pArr['job_id'] = $processDataArr['job_id'];
                $pArr['response_url'] = $responseUrl;
                $pArr['url'] = $dataFileUrl;
                $headers = array(
                    'authorization: Token '.$GLOBALS['FIELDGUIDE_API_KEY'],
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                    'Cache-Control: no-cache',
                    'Pragma: no-cache',
                    'Content-Length: '.strlen(http_build_query($pArr))
                );
                $ch = curl_init();
                $options = array(
                    CURLOPT_URL => 'https://fieldguide.net/api2/symbiota/submit_cv_job',
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_TIMEOUT => 90,
                    CURLOPT_POSTFIELDS => http_build_query($pArr),
                    CURLOPT_RETURNTRANSFER => true
                );
                curl_setopt_array($ch, $options);
                curl_exec($ch);
                curl_close($ch);
                unset($processDataArr['images']);
                $this->logFGBatchFile($jsonFileName,$processDataArr);
                $status = 'Batch process initiated';
            }
        }
        else{
            $status = 'No images found for that parent taxon';
        }
        return $status;
    }

    public function logFGBatchFile($jsonFileName,$infoArr): void
    {
        $jobID = $infoArr['job_id'];
        $fileArr = array();
        if(file_exists($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$this->collId.'-FGLog.json')){
            $fileArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'] . '/temp/data/fieldguide/' . $this->collId . '-FGLog.json'), true, 512, JSON_THROW_ON_ERROR);
            unlink($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$this->collId.'-FGLog.json');
        }
        $fileArr['jobs'][$jobID]['file'] = $jsonFileName;
        $fileArr['jobs'][$jobID]['taxon'] = $infoArr['parenttaxon'];
        $fileArr['jobs'][$jobID]['date'] = $infoArr['dateinitiated'];
        $fp = fopen($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$this->collId.'-FGLog.json', 'wb');
        fwrite($fp, json_encode($fileArr, JSON_THROW_ON_ERROR));
        fclose($fp);
    }

    public function cancelFGBatchProcess($collid,$jobId): string
    {
        $resultsCnt = 0;
        $fileArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'] . '/temp/data/fieldguide/' . $collid . '-FGLog.json'), true, 512, JSON_THROW_ON_ERROR);
        unlink($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$collid.'-FGLog.json');
        $fileName = $fileArr['jobs'][$jobId]['file'];
        unlink($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$fileName);
        unset($fileArr['jobs'][$jobId]);
        $jobsCnt = count($fileArr['jobs']);
        if(isset($fileArr['results'])) {
            $resultsCnt = count($fileArr['results']);
        }
        if(($jobsCnt + $resultsCnt) > 0){
            $fp = fopen($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$collid.'-FGLog.json', 'wb');
            fwrite($fp, json_encode($fileArr, JSON_THROW_ON_ERROR));
            fclose($fp);
        }
        $pArr['job_id'] = $jobId;
        $headers = array(
            'authorization: Token '.$GLOBALS['FIELDGUIDE_API_KEY'],
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Content-Length: '.strlen(http_build_query($pArr))
        );
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => 'https://fieldguide.net/api2/symbiota/remove_cv_job',
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_POSTFIELDS => http_build_query($pArr),
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);
        return 'Batch process cancelled';
    }

    public function getFGBatchImgArr(): array
    {
        $returnArr = array();
        $tId = '';
        if($this->taxon) {
            $tId = $this->getFGBatchTaxonTid($this->taxon);
        }
        $sql = 'SELECT i.imgid, o.occid, o.sciname, t.SciName AS taxonorder, i.url '.
            'FROM images AS i LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
            'LEFT JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid '.
            'LEFT JOIN taxaenumtree AS te ON ts.tidaccepted = te.tid '.
            'LEFT JOIN taxa AS t ON te.parenttid = t.TID '.
            'LEFT JOIN taxa AS t2 ON o.tidinterpreted = t2.TID '.
            'WHERE o.collid = '.$this->collId.' AND ((t2.SciName = "'.$this->taxon.'") OR '.
            '((ts.taxauthid = 1 AND te.taxauthid = 1 AND t.RankId = 100)';
        if($tId) {
            $sql .= ' AND o.tidinterpreted IN(SELECT tid FROM taxaenumtree WHERE parenttid = ' . $tId . ')';
        }
        $sql .= ')) ';
        //echo "<div>Sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $imgId = $row->imgid;
            $imgUrl = $row->url;
            if(isset($GLOBALS['IMAGE_DOMAIN']) && $GLOBALS['IMAGE_DOMAIN']){
                $localDomain = $GLOBALS['IMAGE_DOMAIN'];
            }
            else{
                $localDomain = $this->serverDomain;
            }
            if(strncmp($imgUrl, '/', 1) === 0) {
                $imgUrl = $localDomain . $imgUrl;
            }
            $returnArr[$imgId]['occid'] = $row->occid;
            $returnArr[$imgId]['sciname'] = $row->sciname;
            $returnArr[$imgId]['order'] = $row->taxonorder;
            $returnArr[$imgId]['url'] = $imgUrl;
        }
        $result->free();

        return $returnArr;
    }

    public function getFGBatchTaxonTid($taxon): int
    {
        $tId = 0;
        $sql = 'SELECT TID '.
            'FROM taxa '.
            'WHERE SciName = "'.$taxon.'" ';
        //echo "<div>Sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $tId = $row->TID;
        }
        $result->free();

        return $tId;
    }

    public function checkImages($collid): bool
    {
        $images = false;
        $sql = 'SELECT i.imgid '.
            'FROM images AS i LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
            'WHERE o.collid = '.$collid.' LIMIT 1';
        //echo "<div>Sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            if($row->imgid) {
                $images = true;
            }
        }
        $result->free();

        return $images;
    }

    public function validateFGResults($collid,$jobId): bool
    {
        $valid = false;
        if(file_exists($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$collid.'-FGLog.json')){
            $dataArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'] . '/temp/data/fieldguide/' . $collid . '-FGLog.json'), true, 512, JSON_THROW_ON_ERROR);
            if(isset($dataArr['jobs'][$jobId])) {
                $valid = true;
            }
        }
        return $valid;
    }

    public function logFGResults($collid,$token,$resultUrl): void
    {
        $fileName = '';
        $taxon = '';
        $startDate = '';
        $jobArr = array();
        $processDataArr = array();
        $jobID = $collid.'_'.$token;
        $fileArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'] . '/temp/data/fieldguide/' . $collid . '-FGLog.json'), true, 512, JSON_THROW_ON_ERROR);
        unlink($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$collid.'-FGLog.json');
        foreach($fileArr['jobs'] as $job => $jArr){
            if($job === $jobID){
                $fileName = $jArr['file'];
                $taxon = $jArr['taxon'];
                $startDate = $jArr['date'];
            }
            else{
                $jobArr[$job] = $jArr;
            }
        }
        $dateReceived = date('Y-m-d');
        if($fileName && $taxon && $startDate){
            unlink($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$fileName);
            $fileArr['jobs'] = $jobArr;
            $resArr = json_decode(file_get_contents($resultUrl), true, 512, JSON_THROW_ON_ERROR);
            $processDataArr['job_id'] = $jobID;
            $processDataArr['parenttaxon'] = $taxon;
            $processDataArr['dateinitiated'] = $startDate;
            $processDataArr['datereceived'] = $dateReceived;
            $processDataArr['images'] = $resArr['images'];
            if(isset($resArr['image_counts'])) {
                $processDataArr['imagecnts'] = $resArr['image_counts'];
            }
        }
        if($processDataArr){
            $jsonFileName = $collid.'-r-'.$token.'.json';
            $fp = fopen($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$jsonFileName, 'wb');
            fwrite($fp, json_encode($processDataArr, JSON_THROW_ON_ERROR));
            fclose($fp);
            $fileArr['results'][$jobID]['file'] = $jsonFileName;
            $fileArr['results'][$jobID]['taxon'] = $taxon;
            $fileArr['results'][$jobID]['inidate'] = $startDate;
            $fileArr['results'][$jobID]['recdate'] = $dateReceived;
            $fp = fopen($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$collid.'-FGLog.json', 'wb');
            fwrite($fp, json_encode($fileArr, JSON_THROW_ON_ERROR));
            fclose($fp);
        }
    }

    public function deleteFGBatchResults($collid,$jobId): string
    {
        $jobsCnt = 0;
        $fileArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'] . '/temp/data/fieldguide/' . $collid . '-FGLog.json'), true, 512, JSON_THROW_ON_ERROR);
        unlink($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$collid.'-FGLog.json');
        $fileName = $fileArr['results'][$jobId]['file'];
        unlink($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$fileName);
        unset($fileArr['results'][$jobId]);
        $resultsCnt = count($fileArr['results']);
        if(isset($fileArr['jobs'])) {
            $jobsCnt = count($fileArr['jobs']);
        }
        if(($jobsCnt + $resultsCnt) > 0){
            $fp = fopen($GLOBALS['SERVER_ROOT'].'/temp/data/fieldguide/'.$collid.'-FGLog.json', 'wb');
            fwrite($fp, json_encode($fileArr, JSON_THROW_ON_ERROR));
            fclose($fp);
        }
        return 'Batch results deleted';
    }

    public function primeFGResults(): void
    {
        $resultFilename = $this->collId.'-r-'.$this->token.'.json';
        $fileArr = json_decode(file_get_contents($GLOBALS['SERVER_ROOT'] . '/temp/data/fieldguide/' . $resultFilename), true, 512, JSON_THROW_ON_ERROR);
        $this->taxon = $fileArr['parenttaxon'];
        $this->fgResultArr = $fileArr['images'];
        if(isset($fileArr['imagecnts'])) {
            $this->fgImageCntArr = $fileArr['imagecnts'];
        }
        foreach($this->fgResultArr as $imgId => $ifArr){
            if(($ifArr['status'] === 'OK') && $ifArr['result']) {
                foreach($ifArr['result'] as $name){
                    if(!array_key_exists($name,$this->fgResTidArr)){
                        $this->fgResTidArr[$name] = array();
                    }
                }
            }
        }
        $imgIdArr = array_keys($this->fgResultArr);
        $this->primeFGResultsOccArr($imgIdArr);
        $this->getFGResultTids();
    }

    public function primeFGResultsOccArr($imgArr): void
    {
        $tempArr = $this->fgResultArr;
        $imgIDStr = implode(',',$imgArr);
        $sql = 'SELECT DISTINCT imgid, occid '.
            'FROM images '.
            'WHERE imgid IN(' .$imgIDStr. ') ';
        //echo "<div>Sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $add = false;
            $imgId = $row->imgid;
            $occId = $row->occid;
            $fgStatus = $this->fgResultArr[$imgId]['status'];
            $fgResults = $this->fgResultArr[$imgId]['result'];
            if($this->viewMode === 'full'){
                $add = true;
            }
            elseif($fgStatus === 'OK' && $fgResults && count($fgResults) > 0){
                $add = true;
            }
            if($add){
                if(!in_array($occId, $this->fgResOccArr, true)) {
                    $this->fgResOccArr[] = $occId;
                }
                $this->fgResultArr[$occId][$imgId] = $tempArr[$imgId];
            }
        }
        $result->free();
    }

    public function getFGResultTids(): void
    {
        $fgIDNamesArr = array_keys($this->fgResTidArr);
        $fgIDNamesStr = "'".implode("','",$fgIDNamesArr)."'";
        $sql = 'SELECT t.SciName, t.TID '.
            'FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tid '.
            'WHERE t.SciName IN(' .$fgIDNamesStr. ') AND ts.taxauthid = 1 AND t.TID = ts.tidaccepted ';
        //echo "<div>Sql: ".$sql."</div>";
        if($result = $this->conn->query($sql)){
            while($row = $result->fetch_object()){
                $sciname = $row->SciName;
                $tid = $row->TID;
                $this->fgResTidArr[$sciname][] = $tid;
            }
            $result->free();
        }
    }

    public function getFGResultImgArr(): array
    {
        $returnArr = array();
        $fgOccIdStr = implode(',',$this->fgResOccArr);
        $sql = 'SELECT i.imgid, o.occid, o.sciname, IFNULL(ts.family,o.family) AS family, i.url, c.InstitutionCode, c.CollectionCode '.
            'FROM images AS i LEFT JOIN omoccurrences AS o ON i.occid = o.occid '.
            'LEFT JOIN taxstatus AS ts ON o.tidinterpreted = ts.tid '.
            'LEFT JOIN omcollections AS c ON o.collid = c.CollID '.
            'WHERE o.occid IN('.$fgOccIdStr.') AND ts.taxauthid = 1 ';
        //echo "<div>Sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $imgId = $row->imgid;
            $imgUrl = $row->url;
            if($GLOBALS['IMAGE_DOMAIN']){
                $localDomain = $GLOBALS['IMAGE_DOMAIN'];
            }
            else{
                $localDomain = $this->serverDomain;
            }
            if(strncmp($imgUrl, '/', 1) === 0) {
                $imgUrl = $localDomain . $imgUrl;
            }
            $returnArr[$imgId]['occid'] = $row->occid;
            $returnArr[$imgId]['InstitutionCode'] = $row->InstitutionCode;
            $returnArr[$imgId]['CollectionCode'] = $row->CollectionCode;
            $returnArr[$imgId]['sciname'] = $row->sciname;
            $returnArr[$imgId]['family'] = $row->family;
            $returnArr[$imgId]['url'] = $imgUrl;
        }
        $result->free();

        return $returnArr;
    }

    public function getReturnOccArr(): array
    {
        $returnArr = array();
        $occIDStr = implode(',',$this->fgResOccArr);
        $sql = 'SELECT DISTINCT occid '.
            'FROM omoccurrences '.
            'WHERE occid IN(' .$occIDStr. ') ' .
            'ORDER BY occid ';
        if($this->recLimit) {
            $sql .= 'LIMIT ' . $this->recStart . ',' . $this->recLimit;
        }
        //echo "<div>Sql: ".$sql."</div>";
        $result = $this->conn->query($sql);
        while($row = $result->fetch_object()){
            $returnArr[] = $row->occid;
        }
        $result->free();
        return $returnArr;
    }

    public function processFGResults(): void
    {
        $this->fgResultTot = count($this->fgResOccArr);
        $imgArr = $this->getFGResultImgArr();
        $limitArr = $this->getReturnOccArr();
        $i = 1;
        $prevOccid = 0;
        //echo json_encode($imgArr, JSON_THROW_ON_ERROR);
        foreach($this->fgResultArr as $occId => $oArr){
            if(($this->recLimit && $i > $this->recLimit)){
                break;
            }
            if(in_array($occId, $limitArr, true)){
                foreach($oArr as $imgId => $iArr){
                    if($imgArr[$imgId]){
                        $ifArr = $imgArr[$imgId];
                        $currID = $ifArr['sciname'];
                        $family = $ifArr['family'];
                        $imgUrl = $ifArr['url'];
                        $fgStatus = $iArr['status'];
                        $fgResults = $iArr['result'];
                        if($prevOccid !== $occId){
                            $prevOccid = $occId;
                            $i++;
                        }
                        $this->resultArr[$occId]['InstitutionCode'] = $ifArr['InstitutionCode'];
                        $this->resultArr[$occId]['CollectionCode'] = $ifArr['CollectionCode'];
                        $this->resultArr[$occId]['sciname'] = $currID;
                        $this->resultArr[$occId]['family'] = $family;
                        $this->resultArr[$occId][$imgId]['url'] = $imgUrl;
                        $this->resultArr[$occId][$imgId]['status'] = $fgStatus;
                        $this->resultArr[$occId][$imgId]['results'] = $fgResults;
                    }
                }
            }
        }
    }

    public function processDeterminations($pArr): void
    {
        $occArr = $pArr['occid'];
        foreach($occArr as $occId){
            $idIndex = 'id'.$occId;
            $detTidAccepted = $pArr[$idIndex];
            $detFamily = '';
            $detSciNameAuthor = '';
            $sciname = '';
            $determiner = 'FieldGuide CV Determination';
            $sql = 'SELECT ts.family, t.SciName, t.Author '.
                'FROM taxa AS t LEFT JOIN taxstatus AS ts ON t.TID = ts.tid '.
                'LEFT JOIN taxauthority AS ta ON ts.taxauthid = ta.taxauthid '.
                'WHERE t.TID = '.$detTidAccepted.' AND ta.isprimary = 1 ';
            //echo "<div>Sql: ".$sql."</div>";
            $result = $this->conn->query($sql);
            while($row = $result->fetch_object()){
                $sciname = $row->SciName;
                $detFamily = $row->family;
                $detSciNameAuthor = $row->Author;
            }
            $result->free();
            $occManager = new OccurrenceEditorDeterminations();
            $occManager->setOccId($occId);
            $occManager->setCollId($pArr['collid']);
            $iArr = array(
                'identificationqualifier' => '',
                'sciname' => $sciname,
                'tidtoadd' => $detTidAccepted,
                'family' => $detFamily,
                'scientificnameauthorship' => $detSciNameAuthor,
                'confidenceranking' => 5,
                'identifiedby' => $determiner,
                'dateidentified' => date('m-d-Y'),
                'identificationreferences' => '',
                'identificationremarks' => '',
                'makecurrent' => 1,
                'occid' => $occId
            );
            $occManager->addDetermination($iArr,1);
        }
        if($GLOBALS['SOLR_MODE']){
            $solrManager = new SOLRManager();
            $solrManager->updateSOLR();
        }
    }

    public function setCollID($val): void
    {
        $this->collId = $val;
    }

    public function setRecLimit($val): void
    {
        $this->recLimit = $val;
    }

    public function setRecStart($val): void
    {
        $this->recStart = $val;
    }

    public function setJobID($val): void
    {
        $this->jobId = $val;
        $jobArr = explode('_',$val,2);
        if($jobArr){
            $this->token = $jobArr[1];
        }
    }

    public function setViewMode($val): void
    {
        $this->viewMode = $val;
    }

    public function setTaxon($val): void
    {
        $this->taxon = $val;
    }

    public function getResults(): array
    {
        return $this->resultArr;
    }

    public function getImageCnts(): array
    {
        return $this->fgImageCntArr;
    }

    public function getResultTot(): int
    {
        return $this->fgResultTot;
    }

    public function getTids(): array
    {
        return $this->fgResTidArr;
    }

    public function setServerDomain(): void
    {
        $this->serverDomain = 'http://';
        if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
            $this->serverDomain = 'https://';
        }
        $this->serverDomain .= $_SERVER['HTTP_HOST'];
        if($_SERVER['SERVER_PORT'] && $_SERVER['SERVER_PORT'] !== 80) {
            $this->serverDomain .= ':' . $_SERVER['SERVER_PORT'];
        }
    }
}
