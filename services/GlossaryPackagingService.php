<?php
include_once(__DIR__ . '/../models/Glossary.php');
include_once(__DIR__ . '/../models/GlossaryImages.php');
include_once(__DIR__ . '/../models/GlossarySources.php');
include_once(__DIR__ . '/DataDownloadService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/PhpWordService.php');

class GlossaryPackagingService {

    private array $colRowStyle = array('cantSplit' => true, 'exactHeight' => 180);
    private array $imageCellStyle = array('valign' => 'top', 'width' => 2520, 'borderSize' => 0, 'borderColor' => 'ffffff');
    private array $nodefCellStyle = array('valign' => 'center', 'width' => 2520, 'borderSize' => 0, 'borderColor' => 'ffffff');
    private array $tableStyle = array('width' => 100, 'cellMargin' => 60);

    public function processDocxDownload($glossidArr, $options, $filename): void
    {
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($glossidArr && $options && $filename && $targetPath){
            $contentType = (new DataDownloadService)->getContentTypeFromFileType('docx');
            $fullPath = $targetPath . '/' . $filename;
            $phpWord = PhpWordService::getPhpWord();
            PhpWordService::addParagraphStyle($phpWord,'titlePara', array('align' => 'center', 'lineHeight' => 1.0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'keepNext' => true));
            PhpWordService::addFontStyle($phpWord, 'titleFont', array('bold' => true, 'size' => 16, 'name' => 'Microsoft Sans Serif'));
            PhpWordService::addParagraphStyle($phpWord,'transTermPara', array('align' => 'left', 'lineHeight' => 1.0, 'spaceBefore' => 0, 'spaceAfter' => 0, 'keepNext' => true));
            PhpWordService::addFontStyle($phpWord, 'transTermTopicNodefFont', array('bold' => true, 'size' => 15, 'name' => 'Microsoft Sans Serif'));
            PhpWordService::addFontStyle($phpWord, 'transTermTopicDefFont', array('bold' => true, 'size' => 14, 'name' => 'Microsoft Sans Serif'));
            PhpWordService::addParagraphStyle($phpWord,'transDefPara', array('align' => 'left', 'lineHeight' => 1.0, 'indent' => 0.78125, 'spaceBefore' => 0, 'spaceAfter' => 0, 'keepNext' => true));
            PhpWordService::addParagraphStyle($phpWord,'transDefList', array('align' => 'left', 'lineHeight' => 1.0, 'indent' => 0.78125, 'spaceBefore' => 0, 'spaceAfter' => 0, 'keepNext' => true));
            PhpWordService::addFontStyle($phpWord, 'transTableHeaderNodeFont', array('bold' => false, 'size' => 12, 'underline' => 'single', 'name' => 'Microsoft Sans Serif', 'color' => '000000'));
            PhpWordService::addFontStyle($phpWord, 'transMainTermNodefFont', array('bold' => false, 'size' => 12, 'name' => 'Microsoft Sans Serif', 'color' => '21304B'));
            PhpWordService::addFontStyle($phpWord, 'transTransTermNodefFont', array('bold' => false, 'size' => 12, 'name' => 'Microsoft Sans Serif', 'color' => '000000'));
            PhpWordService::addFontStyle($phpWord, 'transMainTermDefFont', array('bold' => true, 'size' => 12, 'name' => 'Microsoft Sans Serif', 'color' => '21304B'));
            PhpWordService::addFontStyle($phpWord, 'transTransTermDefFont', array('bold' => true, 'size' => 12, 'name' => 'Microsoft Sans Serif', 'color' => '000000'));
            PhpWordService::addFontStyle($phpWord, 'transDefTextFont', array('bold' => false, 'size' => 12, 'name' => 'Microsoft Sans Serif', 'color' => '000000'));
            PhpWordService::addTableStyle($phpWord, 'exportTable', $this->tableStyle, $this->colRowStyle);
            $section = PhpWordService::getSection($phpWord, array('paperSize' => 'Letter', 'marginLeft' => 1080, 'marginRight' => 1080, 'marginTop' => 1080, 'marginBottom' => 1080, 'headerHeight' => 100, 'footerHeight' => 0));


            /*$textrun = PhpWordService::getTextRun($section, 'defaultPara');
            PhpWordService::addText($textrun, htmlspecialchars($dataArr['data']['name'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'titleFont');
            PhpWordService::addTextBreak($textrun);
            PhpWordService::addText($textrun, 'Authors: ', 'topicFont');
            PhpWordService::addText($textrun, ($dataArr['data']['authors'] ? htmlspecialchars($dataArr['data']['authors'], ENT_QUOTES | ENT_XML1, 'UTF-8') : ''), 'textFont');
            PhpWordService::addTextBreak($textrun);
            if($dataArr['data']['publication']){
                PhpWordService::addText($textrun, 'Publication: ', 'topicFont');
                PhpWordService::addText($textrun, htmlspecialchars($dataArr['data']['publication'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'textFont');
                PhpWordService::addTextBreak($textrun);
            }
            if($dataArr['data']['locality'] || ($dataArr['data']['latcentroid'] && $dataArr['data']['longcentroid'])){
                $this->writeDocxLocalitySection($textrun, $dataArr);
            }
            if($dataArr['data']['abstract']){
                PhpWordService::addText($textrun, 'Abstract: ', 'topicFont');
                PhpWordService::addText($textrun, htmlspecialchars($dataArr['data']['abstract'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'textFont');
                PhpWordService::addTextBreak($textrun);
            }
            if($dataArr['data']['notes']){
                PhpWordService::addText($textrun, 'Notes: ', 'topicFont');
                PhpWordService::addText($textrun, htmlspecialchars($dataArr['data']['notes'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'textFont');
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
            }*/


            $glossaryArr = (new Glossary)->getGlossaryArr(0, 0, false, true, $glossidArr);
            if(count($glossaryArr) > 0){
                $this->writeDocxHeaderSection($section, $options['downloadTitle']);
                if($options['downloadFormat'] === 'translation'){

                }
                else{

                }
            }
            PhpWordService::saveDocument($phpWord, $fullPath);
            (new DataDownloadService)->streamDownload($contentType, $fullPath);
        }
    }

    public function writeDocxHeaderSection($section, $title): void
    {
        $header = PhpWordService::getHeader($section);
        PhpWordService::addPreserveText($header, ($title . ' - p.{PAGE} ' . date('Y-m-d')), array('align' => 'right'));
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
            if($imageSrc && $imageSrc[0] === '/' && FileSystemService::fileExists(($GLOBALS['SERVER_ROOT'] . $imageSrc))){
                $cell = PhpWordService::getTableCell($table, null, $this->imageCellStyle);
                $textrun = PhpWordService::getTextRun($cell, 'imagePara');
                PhpWordService::addImage($textrun, ($GLOBALS['SERVER_ROOT'] . $imageSrc), array('width' => 160, 'height' => 160));
                PhpWordService::addTextBreak($textrun);
                PhpWordService::addText($textrun, htmlspecialchars($taxon['sciname'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'topicFont');
                PhpWordService::addTextBreak($textrun);
                if((int)$options['vernaculars'] === 1 && $taxon['vernacularData'] && count($taxon['vernacularData']) > 0){
                    $vernacularArr = array();
                    foreach($taxon['vernacularData'] as $vernacular){
                        $vernacularArr[] = htmlspecialchars($vernacular['vernacularname'], ENT_QUOTES | ENT_XML1, 'UTF-8');
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
            PhpWordService::addText($textrun, htmlspecialchars($localityStr, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'textFont');
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
                PhpWordService::addText($textrun, (' ' . htmlspecialchars($taxon['author'], ENT_QUOTES | ENT_XML1, 'UTF-8')), 'textFont');
                PhpWordService::addTextBreak($textrun);
            }
            if((int)$options['vernaculars'] === 1 && $taxon['vernacularData'] && count($taxon['vernacularData']) > 0){
                $vernacularArr = array();
                foreach($taxon['vernacularData'] as $vernacular){
                    $vernacularArr[] = htmlspecialchars($vernacular['vernacularname'], ENT_QUOTES | ENT_XML1, 'UTF-8');
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
                    PhpWordService::addText($textrun, htmlspecialchars($notesStr, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'textFont');
                }
                if(array_key_exists($taxon['tid'], $dataArr['vouchers']) && count($dataArr['vouchers'][$taxon['tid']]) > 0){
                    $voucherStrArr = array();
                    foreach($dataArr['vouchers'][$taxon['tid']] as $voucher){
                        $voucherStrArr[] = htmlspecialchars($voucher['label'], ENT_QUOTES | ENT_XML1, 'UTF-8');
                    }
                    if(count($voucherStrArr) > 0){
                        PhpWordService::addText($textrun, implode(', ', $voucherStrArr), 'textFont');
                    }
                }
            }
        }
    }
}
