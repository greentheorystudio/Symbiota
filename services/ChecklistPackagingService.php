<?php
include_once(__DIR__ . '/../models/Checklists.php');
include_once(__DIR__ . '/../models/ChecklistTaxa.php');
include_once(__DIR__ . '/../models/ChecklistVouchers.php');
include_once(__DIR__ . '/../models/Images.php');
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/DataDownloadService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/PhpWordService.php');

class ChecklistPackagingService {

    private $conn;

    private $blankCellStyle = array('valign' => 'center', 'width' => 2475, 'borderSize' => 15, 'borderColor' => '000000');
    private $colRowStyle = array('cantSplit' => true, 'exactHeight' => 3750);
    private $imageCellStyle = array('valign' => 'center', 'width' => 2475, 'borderSize' => 15, 'borderColor' => '808080');
    private $tableStyle = array('width' => 100);

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function deleteAppDataArchive($clid): int
    {
        $returnVal = 0;
        if((int)$clid > 0){
            $fullPath = $GLOBALS['SERVER_ROOT'] . '/content/checklist/cl-' . $clid . '.zip';
            FileSystemService::deleteFile($fullPath);
            $returnVal = 1;
        }
        return $returnVal;
    }

    public function filterTaxaArr($filter, $taxaArr): array
    {
        $returnArr = array();
        foreach($taxaArr as $taxon){
            if((int)$filter['rankid'] === 140 && $taxon['family'] === $filter['sciname']){
                $returnArr[] = $taxon;
            }
            elseif((int)$filter['rankid'] > 140 && ($taxon['sciname'] === $filter['sciname'] || strpos($taxon['sciname'], ($filter['sciname'] . ' ')) === 0)){
                $returnArr[] = $taxon;
            }
        }
        return $returnArr;
    }

    public function getChecklistData($clidArr, $clid, $options): array
    {
        $checklistDataArr = array();
        $checklistDataArr['images'] = array();
        $checklistDataArr['vouchers'] = array();
        $checklistDataArr['data'] = (new Checklists)->getChecklistData($clid);
        $checklistDataArr['taxa'] = $this->getChecklistTaxaData($clidArr, $options);
        if($options['taxonFilter'] && array_key_exists('rankid', $options['taxonFilter']) && (int)$options['taxonFilter']['rankid'] > 0 && count($checklistDataArr['taxa']) > 0){
            $checklistDataArr['taxa'] = $this->filterTaxaArr($options['taxonFilter'], $checklistDataArr['taxa']);
        }
        if((int)$options['images'] === 1 && count($checklistDataArr['taxa']) > 0){
            $checklistDataArr['images'] = $this->setChecklistImageDataArr($checklistDataArr['taxa'], $clidArr, $options);
        }
        if((int)$options['notes'] === 1 && count($checklistDataArr['taxa']) > 0){
            $checklistDataArr['vouchers'] = $this->setChecklistVoucherDataArr($checklistDataArr['taxa'], $clidArr);
        }
        return $checklistDataArr;
    }

    public function getChecklistTaxaCountData($taxaArr): array
    {
        $returnArr = array();
        $totalArr = array();
        $speciesArr = array();
        $generaArr = array();
        $familyArr = array();
        foreach($taxaArr as $taxon){
            if(!in_array($taxon['sciname'], $totalArr, true)){
                $totalArr[] = $taxon['sciname'];
            }
            if($taxon['family'] && $taxon['family'] !== '[Incertae Sedis]' && !in_array($taxon['family'], $familyArr, true)){
                $familyArr[] = $taxon['family'];
            }
            if((int)$taxon['rankid'] === 180 && !in_array($taxon['sciname'], $generaArr, true)){
                $generaArr[] = $taxon['sciname'];
            }
            elseif((int)$taxon['rankid'] >= 220){
                $unitNameArr = explode(' ', $taxon['sciname']);
                if($unitNameArr){
                    if(!in_array($unitNameArr[0], $generaArr, true)){
                        $generaArr[] = $unitNameArr[0];
                    }
                    if((int)$taxon['rankid'] === 220 && !in_array($taxon['sciname'], $speciesArr, true)){
                        $speciesArr[] = $taxon['sciname'];
                    }
                    elseif(!in_array(($unitNameArr[0] . ' ' . $unitNameArr[1]), $speciesArr, true)){
                        $speciesArr[] = ($unitNameArr[0] . ' ' . $unitNameArr[1]);
                    }
                }
            }
        }
        $returnArr['total'] = count($totalArr);
        $returnArr['species'] = count($speciesArr);
        $returnArr['genera'] = count($generaArr);
        $returnArr['families'] = count($familyArr);
        return $returnArr;
    }

    public function getChecklistTaxaData($clidArr, $options): array
    {
        $includeSynonymyData = (int)$options['synonyms'] === 1;
        $includeVernacularData = (int)$options['vernaculars'] === 1;
        $useAcceptedNames = (int)$options['acceptedNames'] === 1;
        $taxaArr = (new ChecklistTaxa)->getChecklistTaxa($clidArr, false, $includeSynonymyData, $includeVernacularData, $useAcceptedNames, $options['taxaSort']);
        if($options['taxonFilter'] && array_key_exists('rankid', $options['taxonFilter']) && (int)$options['taxonFilter']['rankid'] > 0 && count($taxaArr) > 0){
            $taxaArr = $this->filterTaxaArr($options['taxonFilter'], $taxaArr);
        }
        return $taxaArr;
    }

    public function getIdArrFromTaxaArr($taxaArr, $idField): array
    {
        $returnArr = array();
        foreach($taxaArr as $taxon){
            if(!in_array($taxon[$idField], $returnArr, true)){
                $returnArr[] = $taxon[$idField];
            }
        }
        return $returnArr;
    }

    public function initializeAppDataArchive($clid): string
    {
        $returnVal = '';
        if((int)$clid > 0){
            $archiveFileName = 'cl-' . $clid . '.zip';
            $archiveFilePath = $GLOBALS['SERVER_ROOT'] . '/content/checklist/' . $archiveFileName;
            $zipArchive = FileSystemService::initializeNewZipArchive();
            if(FileSystemService::createNewZipArchive($zipArchive, $archiveFilePath)){
                FileSystemService::addFileFromStringToZipArchive($zipArchive, '{"images":[', 'data.json');
                FileSystemService::closeZipArchive($zipArchive);
                $returnVal = $archiveFileName;
            }
        }
        return $returnVal;
    }

    public function mergeDataArr($returnArr, $newArr): array
    {
        $keys = array_keys($newArr);
        foreach($keys as $key){
            $returnArr[$key] = $newArr[$key];
        }
        return $returnArr;
    }

    public function packageChecklistTaggedImages($clidArr, $tidArr, $archiveFile): array
    {
        $returnArr = array();
        $fullArchivePath = $GLOBALS['SERVER_ROOT'] . '/content/checklist/' . $archiveFile;
        $zipArchive = FileSystemService::openZipArchive($fullArchivePath);
        $dataFileContent = FileSystemService::openZipArchiveFile($zipArchive, 'data.json');
        if($zipArchive && $dataFileContent){
            $imageArr = (new Images)->getChecklistTaggedImageData($clidArr, 0, $tidArr);
            foreach($imageArr as $tid => $tidImgArr){
                foreach($tidImgArr as $image){
                    $fileType = substr($image['url'], (strrpos($image['url'], '.') ?: -1) + 1);
                    if($fileType === 'jpg' || $fileType === 'jpeg' || $fileType === 'png'){
                        $loaded = false;
                        $fileName = $image['imgid'] . '.' . $fileType;
                        $data = array('filename' => $fileName, 'tid'   => $tid, 'photographer'   => $image['photographer'], 'owner'   => $image['owner']);
                        if(strncmp($image['url'], '/', 1) === 0){
                            FileSystemService::addFileToZipArchive($zipArchive, FileSystemService::getServerPathFromUrlPath($image['url']), $fileName);
                            $loaded = true;
                        }
                        else{
                            $fileData = file_get_contents($image['url']);
                            if($fileData){
                                FileSystemService::addFileFromStringToZipArchive($zipArchive, $fileData, $fileName);
                                $loaded = true;
                            }
                        }
                        if($loaded){
                            $dataFileContent .= json_encode($data) . ',';
                            $returnArr[] = $tid;
                        }
                    }
                }
            }
            FileSystemService::addFileFromStringToZipArchive($zipArchive, $dataFileContent, 'data.json');
            FileSystemService::closeZipArchive($zipArchive);
        }
        return $returnArr;
    }

    public function processCsvDownload($clidArr, $options, $filename): void
    {
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($clidArr && $options && $filename && $targetPath){
            $taxaArr = $this->getChecklistTaxaData($clidArr, $options);
            $contentType = (new DataDownloadService)->getContentTypeFromFileType('csv');
            $fullPath = $targetPath . '/' . $filename;
            $fileHandler = FileSystemService::openFileHandler($fullPath);
            $headerArr = array('Family', 'ScientificName', 'TaxonId');
            if((int)$options['authors'] === 1){
                $headerArr[] = 'ScientificNameAuthorship';
            }
            if((int)$options['notes'] === 1){
                $headerArr[] = 'Notes';
            }
            if((int)$options['vernaculars'] === 1){
                $headerArr[] = 'CommonNames';
            }
            FileSystemService::writeRowToCsv($fileHandler, $headerArr);
            foreach($taxaArr as $taxon){
                $rowArr = array();
                $rowArr[] = $taxon['family'];
                $rowArr[] = $taxon['sciname'];
                $rowArr[] = $taxon['tid'];
                if((int)$options['authors'] === 1){
                    $rowArr[] = $taxon['author'];
                }
                if((int)$options['notes'] === 1){
                    $notesArr = array();
                    if($taxon['habitat']){
                        $notesArr[] = $taxon['habitat'];
                    }
                    if($taxon['abundance']){
                        $notesArr[] = $taxon['abundance'];
                    }
                    if($taxon['notes']){
                        $notesArr[] = $taxon['notes'];
                    }
                    $rowArr[] = count($notesArr) > 0 ? implode(', ', $notesArr) : '';
                }
                if((int)$options['vernaculars'] === 1){
                    $vernacularArr = array();
                    if(count($taxon['vernacularData']) > 0){
                        foreach($taxon['vernacularData'] as $vernacular){
                            $vernacularArr[] = $vernacular['vernacularname'];
                        }
                    }
                    $rowArr[] = count($vernacularArr) > 0 ? implode(', ', $vernacularArr) : '';
                }
                FileSystemService::writeRowToCsv($fileHandler, $rowArr);
            }
            FileSystemService::closeFileHandler($fileHandler);
            (new DataDownloadService)->streamDownload($contentType, $fullPath);
        }
    }

    public function processDocxDownload($clidArr, $clid, $options, $filename): void
    {
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($clidArr && $clid && $options && $filename && $targetPath){
            $contentType = (new DataDownloadService)->getContentTypeFromFileType('docx');
            $fullPath = $targetPath . '/' . $filename;
            $dataArr = $this->getChecklistData($clidArr, $clid, $options);
            $countArr = $this->getChecklistTaxaCountData($dataArr['taxa']);
            $phpWord = PhpWordService::getPhpWord();
            PhpWordService::addParagraphStyle($phpWord, 'defaultPara', array('align' => 'left', 'lineHeight' => 1.0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'keepNext' => true));
            PhpWordService::addFontStyle($phpWord, 'titleFont', array('bold' => true, 'size' => 20, 'name' => 'Arial'));
            PhpWordService::addFontStyle($phpWord, 'topicFont', array('bold' => true, 'size' => 12, 'name' => 'Arial'));
            PhpWordService::addFontStyle($phpWord, 'textFont', array('size' => 12, 'name' => 'Arial'));
            PhpWordService::addParagraphStyle($phpWord, 'linePara', array('align' => 'left', 'lineHeight' => 1.0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'keepNext' => true));
            PhpWordService::addParagraphStyle($phpWord, 'familyPara', array('align' => 'left', 'lineHeight' => 1.0, 'spaceBefore' => 225, 'spaceAfter' => 75, 'keepNext' => true));
            PhpWordService::addFontStyle($phpWord, 'familyFont', array('bold' => true, 'size' => 16, 'name' => 'Arial'));
            PhpWordService::addParagraphStyle($phpWord, 'scinamePara', array('align' => 'left', 'lineHeight' => 1.0, 'indent' => 0.3125, 'spaceBefore' => 0, 'spaceAfter' => 45, 'keepNext' => true));
            PhpWordService::addFontStyle($phpWord, 'scientificnameFont', array('bold' => true, 'italic' => true, 'size' => 12, 'name' => 'Arial'));
            PhpWordService::addParagraphStyle($phpWord, 'synonymPara', array('align' => 'left', 'lineHeight' => 1.0, 'indent' => 0.78125, 'spaceBefore' => 0, 'spaceAfter' => 45));
            PhpWordService::addFontStyle($phpWord, 'synonymFont', array('bold' => false, 'italic' => true, 'size' => 12, 'name' => 'Arial'));
            PhpWordService::addParagraphStyle($phpWord, 'notesvouchersPara', array('align' => 'left', 'lineHeight' => 1.0, 'indent' => 0.78125, 'spaceBefore' => 0, 'spaceAfter' => 45));
            PhpWordService::addParagraphStyle($phpWord, 'imagePara', array('align' => 'center', 'lineHeight' => 1.0, 'spaceBefore' => 0, 'spaceAfter' => 0));
            PhpWordService::addTableStyle($phpWord, 'imageTable', $this->tableStyle, $this->colRowStyle);
            $section = PhpWordService::getSection($phpWord, array('pageSizeW' => 12240, 'pageSizeH' => 15840, 'marginLeft' => 1080, 'marginRight' => 1080, 'marginTop' => 1080, 'marginBottom' => 1080, 'headerHeight' => 0, 'footerHeight' => 0));
            $textrun = PhpWordService::getTextRun($section, 'defaultPara');
            PhpWordService::addText($textrun, $dataArr['data']['name'], 'titleFont');
            PhpWordService::addTextBreak($textrun);
            PhpWordService::addText($textrun, 'Authors: ', 'topicFont');
            PhpWordService::addText($textrun, ($dataArr['data']['authors'] ?: ''), 'textFont');
            PhpWordService::addTextBreak($textrun);
            if($dataArr['data']['publication']){
                PhpWordService::addText($textrun, 'Publication: ', 'topicFont');
                PhpWordService::addText($textrun, $dataArr['data']['publication'], 'textFont');
                PhpWordService::addTextBreak($textrun);
            }
            if($dataArr['data']['locality'] || ($dataArr['data']['latcentroid'] && $dataArr['data']['longcentroid'])){
                $this->writeDocxLocalitySection($textrun, $dataArr);
            }
            if($dataArr['data']['abstract']){
                PhpWordService::addText($textrun, 'Abstract: ', 'topicFont');
                PhpWordService::addText($textrun, $dataArr['data']['abstract'], 'textFont');
                PhpWordService::addTextBreak($textrun);
            }
            if($dataArr['data']['notes']){
                PhpWordService::addText($textrun, 'Notes: ', 'topicFont');
                PhpWordService::addText($textrun, $dataArr['data']['notes'], 'textFont');
                PhpWordService::addTextBreak($textrun);
            }
            $textrun = PhpWordService::getTextRun($section, 'linePara');
            PhpWordService::addLine($textrun, array('weight' => 1, 'width' => 670, 'height' => 0));
            $textrun = PhpWordService::getTextRun($section, 'defaultPara');
            PhpWordService::addText($textrun, 'Families: ', 'topicFont');
            PhpWordService::addText($textrun, (string)$countArr['families'], 'textFont');
            PhpWordService::addTextBreak($textrun);
            PhpWordService::addText($textrun, 'Genera: ', 'topicFont');
            PhpWordService::addText($textrun, (string)$countArr['genera'], 'textFont');
            PhpWordService::addTextBreak($textrun);
            PhpWordService::addText($textrun, 'Species: ', 'topicFont');
            PhpWordService::addText($textrun, (string)$countArr['species'], 'textFont');
            PhpWordService::addTextBreak($textrun);
            PhpWordService::addText($textrun, 'Total Taxa: ', 'topicFont');
            PhpWordService::addText($textrun, (string)$countArr['total'], 'textFont');
            PhpWordService::addTextBreak($textrun);
            if((int)$options['images'] === 1){
                $this->writeDocxImageSection($section, $dataArr, $options);
            }
            else{
                $this->writeDocxTaxaListSection($section, $dataArr, $options);
            }
            PhpWordService::saveDocument($phpWord, $fullPath);
            (new DataDownloadService)->streamDownload($contentType, $fullPath);
        }
    }

    public function setChecklistImageDataArr($taxaArr, $clidArr, $options): array
    {
        $targetTidArr = array();
        $tidArr = $this->getIdArrFromTaxaArr($taxaArr, 'tidaccepted');
        $taxonImageLimit = array_key_exists('taxonLimit', $options) ? (int)$options['taxonLimit'] : 1;
        $returnArr = (new Images)->getChecklistTaggedImageData($clidArr, $taxonImageLimit);
        foreach($tidArr as $tid){
            if(!array_key_exists($tid, $returnArr)){
                $targetTidArr[] = $tid;
            }
        }
        $batches = array_chunk($targetTidArr, 100);
        foreach($batches as $batch){
            if(count($batch) > 0){
                $returnArr = $this->mergeDataArr($returnArr, (new Images)->getChecklistImageData($batch, $taxonImageLimit));
            }
        }
        return $returnArr;
    }

    public function setChecklistVoucherDataArr($taxaArr, $clidArr): array
    {
        $returnArr = array();
        $tidArr = $this->getIdArrFromTaxaArr($taxaArr, 'tid');
        $batches = array_chunk($tidArr, 200);
        foreach($batches as $batch){
            if(count($batch) > 0){
                $returnArr = $this->mergeDataArr($returnArr, (new ChecklistVouchers)->getChecklistVouchers($clidArr, $batch));
            }
        }
        return $returnArr;
    }

    public function writeDocxImageSection($section, $dataArr, $options): void
    {
        $imageCnt = 0;
        $previousFamily = '';
        $table = PhpWordService::getTable($section, 'imageTable');
        foreach($dataArr['taxa'] as $taxon){
            $imageSrc = '';
            $imageCnt++;
            if($imageCnt % 4 === 1) {
                PhpWordService::addTableRow($table);
            }
            if(array_key_exists($taxon['tidaccepted'], $dataArr['images']) && count($dataArr['images'][$taxon['tidaccepted']]) > 0){
                $imageSrc = $dataArr['images'][$taxon['tidaccepted']][0]['thumbnailurl'] ?: $dataArr['images'][$taxon['tidaccepted']][0]['url'];
            }
            if($imageSrc && $imageSrc[0] === '/'){
                $cell = PhpWordService::getTableCell($table, null, $this->imageCellStyle);
                $textrun = PhpWordService::getTextRun($cell, 'imagePara');
                PhpWordService::addImage($textrun, ($GLOBALS['SERVER_ROOT'] . $imageSrc), array('width' => 160, 'height' => 160));
                PhpWordService::addTextBreak($textrun);
                PhpWordService::addText($textrun, $taxon['sciname'], 'topicFont');
                PhpWordService::addTextBreak($textrun);
                if((int)$options['vernaculars'] === 1 && $taxon['vernacularData'] && count($taxon['vernacularData']) > 0){
                    $vernacularArr = array();
                    foreach($taxon['vernacularData'] as $vernacular){
                        $vernacularArr[] = $vernacular['vernacularname'];
                    }
                    PhpWordService::addText($textrun, (count($vernacularArr) > 0 ? implode(', ', $vernacularArr) : ''), 'topicFont');
                    PhpWordService::addTextBreak($textrun);
                }
                if($options['taxaSort'] === 'family' && $taxon['family'] !== $previousFamily){
                    PhpWordService::addText($textrun, ('[' . $taxon['family'] . ']'), 'textFont');
                    $previousFamily = $taxon['family'];
                }
            }
            else{
                $cell = PhpWordService::getTableCell($table, null, $this->blankCellStyle);
                $textrun = PhpWordService::getTextRun($cell, 'imagePara');
                PhpWordService::addText($textrun, 'Image', 'topicFont');
                PhpWordService::addTextBreak($textrun);
                PhpWordService::addText($textrun, 'not yet', 'topicFont');
                PhpWordService::addTextBreak($textrun);
                PhpWordService::addText($textrun, 'available', 'topicFont');
            }
        }
    }

    public function writeDocxLocalitySection($textrun, $dataArr): void
    {
        $localityStr = '';
        if($dataArr['data']['locality']){
            $localityStr .= $dataArr['data']['locality'];
        }
        if($dataArr['data']['latcentroid'] && $dataArr['data']['longcentroid']){
            $localityStr .= ($localityStr ? ' ' : '') . '(' . $dataArr['data']['latcentroid'] . ', ' . $dataArr['data']['longcentroid'] . ')';
        }
        if($localityStr){
            PhpWordService::addText($textrun, 'Locality: ', 'topicFont');
            PhpWordService::addText($textrun, $localityStr, 'textFont');
            PhpWordService::addTextBreak($textrun);
        }
    }

    public function writeDocxTaxaListSection($section, $dataArr, $options): void
    {
        $previousFamily = '';
        foreach($dataArr['taxa'] as $taxon){
            if($options['taxaSort'] === 'family' && $taxon['family'] !== $previousFamily){
                $textrun = PhpWordService::getTextRun($section, 'familyPara');
                PhpWordService::addText($textrun, $taxon['family'], 'familyFont');
                $previousFamily = $taxon['family'];
            }
            $textrun = PhpWordService::getTextRun($section, 'scinamePara');
            PhpWordService::addText($textrun, $taxon['sciname'], 'scientificnameFont');
            if((int)$options['authors'] === 1 && $taxon['author']){
                PhpWordService::addText($textrun, (' ' . $taxon['author']), 'textFont');
                PhpWordService::addTextBreak($textrun);
            }
            if((int)$options['vernaculars'] === 1 && $taxon['vernacularData'] && count($taxon['vernacularData']) > 0){
                $vernacularArr = array();
                foreach($taxon['vernacularData'] as $vernacular){
                    $vernacularArr[] = $vernacular['vernacularname'];
                }
                PhpWordService::addText($textrun, (count($vernacularArr) > 0 ? (' - ' . implode(', ', $vernacularArr)) : ''), 'topicFont');
            }
            if((int)$options['synonyms'] === 1 && $taxon['synonymyData'] && count($taxon['synonymyData']) > 0){
                $synonymArr = array();
                foreach($taxon['synonymyData'] as $synonym){
                    $synonymArr[] = $synonym['sciname'];
                }
                if(count($synonymArr) > 0){
                    $textrun = PhpWordService::getTextRun($section, 'synonymPara');
                    PhpWordService::addText($textrun, '[', 'textFont');
                    PhpWordService::addText($textrun, implode(', ', $synonymArr), 'synonymFont');
                    PhpWordService::addText($textrun, ']', 'textFont');
                }
            }
            if((int)$options['notes'] === 1){
                $textrun = PhpWordService::getTextRun($section, 'notesvouchersPara');
                if($taxon['habitat'] || $taxon['notes'] || $taxon['abundance'] || $taxon['source']){
                    $notesStr = '';
                    if($taxon['habitat']){
                        $notesStr .= $taxon['habitat'] . (($taxon['notes'] || $taxon['abundance'] || $taxon['source']) ? ', ' : '');
                    }
                    if($taxon['abundance']){
                        $notesStr .= $taxon['abundance'] . (($taxon['notes'] || $taxon['source']) ? ', ' : '');
                    }
                    if($taxon['notes']){
                        $notesStr .= $taxon['notes'] . ($taxon['source'] ? ', ' : '');
                    }
                    if($taxon['source']){
                        $notesStr .= 'Source: ' . $taxon['source'];
                    }
                    PhpWordService::addText($textrun, $notesStr, 'textFont');
                }
                if(array_key_exists($taxon['tid'], $dataArr['vouchers']) && count($dataArr['vouchers'][$taxon['tid']]) > 0){
                    $voucherStrArr = array();
                    foreach($dataArr['vouchers'][$taxon['tid']] as $voucher){
                        $voucherStrArr[] = $voucher['label'];
                    }
                    if(count($voucherStrArr) > 0){
                        PhpWordService::addText($textrun, implode(', ', $voucherStrArr), 'textFont');
                    }
                }
            }
        }
    }
}
