<?php
include_once(__DIR__ . '/../models/Glossary.php');
include_once(__DIR__ . '/../models/GlossaryImages.php');
include_once(__DIR__ . '/DataDownloadService.php');
include_once(__DIR__ . '/FileSystemService.php');
include_once(__DIR__ . '/PhpWordService.php');

class GlossaryPackagingService {

    private array $colRowStyle = array('cantSplit' => true, 'exactHeight' => 180);
    private array $imageCellStyle = array('valign' => 'top', 'width' => 2520, 'borderSize' => 0, 'borderColor' => 'ffffff');
    private array $nodefCellStyle = array('valign' => 'center', 'width' => 2520, 'borderSize' => 0, 'borderColor' => 'ffffff');
    private array $tableStyle = array('width' => 100, 'cellMargin' => 60);

    public function getGlossarySummaryData($glossaryArr, $translationArr, $sourceData): array
    {
        $retArr = array();
        $retArr['references'] = array();
        $retArr['contributors'] = array();
        $retArr['imgcontributors'] = array();
        $retArr['translations'] = array();
        foreach($glossaryArr as $glossary){
            if($glossary['source'] && !in_array($glossary['source'], $retArr['references'], true)) {
                $retArr['references'][] = $glossary['source'];
            }
            if($glossary['translator'] && !in_array($glossary['translator'], $retArr['contributors'], true)) {
                $retArr['contributors'][] = $glossary['translator'];
            }
            if($glossary['author'] && !in_array($glossary['author'], $retArr['contributors'], true)) {
                $retArr['contributors'][] = $glossary['author'];
            }
            if(count($glossary['groupIdArr']) > 0 && count($translationArr) > 0){
                foreach($glossary['groupIdArr'] as $glossGrp){
                    if($glossGrp['relationshiptype'] === 'translation' && array_key_exists($glossGrp['glossgrpid'], $translationArr)){
                        $languages = array_keys($translationArr[$glossGrp['glossgrpid']]);
                        foreach($languages as $language){
                            foreach($translationArr[$glossGrp['glossgrpid']][$language] as $translation){
                                if($translation['source'] && !in_array($translation['source'], $retArr['references'], true)) {
                                    $retArr['references'][] = $translation['source'];
                                }
                                if($translation['translator'] && !in_array($translation['translator'], $retArr['contributors'], true)) {
                                    $retArr['contributors'][] = $translation['translator'];
                                }
                                if($translation['author'] && !in_array($translation['author'], $retArr['contributors'], true)) {
                                    $retArr['contributors'][] = $translation['author'];
                                }
                                $retArr['translations'][$glossary['glossid']][$language][] = $translation;
                                if(count($retArr['translations'][$glossary['glossid']][$language]) > 1){
                                    usort($retArr['translations'][$glossary['glossid']][$language], static fn($a, $b) => strcasecmp($a->term, $b->term));
                                }
                            }
                        }
                    }
                }
            }
        }
        if(array_key_exists('contributorterm', $sourceData) && $sourceData['contributorterm'] && !in_array($sourceData['contributorterm'], $retArr['contributors'], true)){
            $retArr['contributors'][] = $sourceData['contributorterm'];
        }
        if(array_key_exists('contributorimage', $sourceData) && $sourceData['contributorimage'] && !in_array($sourceData['contributorimage'], $retArr['imgcontributors'], true)){
            $retArr['imgcontributors'][] = $sourceData['contributorimage'];
        }
        if(array_key_exists('translator', $sourceData) && $sourceData['translator'] && !in_array($sourceData['translator'], $retArr['contributors'], true)){
            $retArr['contributors'][] = $sourceData['translator'];
        }
        if(array_key_exists('additionalsources', $sourceData) && $sourceData['additionalsources'] && !in_array($sourceData['additionalsources'], $retArr['references'], true)){
            $retArr['references'][] = $sourceData['additionalsources'];
        }
        if(count($retArr['contributors']) > 0){
            ksort($retArr['contributors']);
        }
        if(count($retArr['imgcontributors']) > 0){
            ksort($retArr['imgcontributors']);
        }
        if(count($retArr['references']) > 0){
            ksort($retArr['references']);
        }
        return $retArr;
    }

    public function processDocxDownload($glossidArr, $options, $filename): void
    {
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($glossidArr && $options && $filename && $targetPath){
            $glossaryArr = (new Glossary)->getGlossaryArr(0, 0, false, true, $glossidArr);
            $translationArr = $options['downloadFormat'] === 'translation' ? (new Glossary)->getGlossaryRelatedTermsArrFromGlossidArr($glossidArr, 'translation', $options['translationLanguageArr']) : array();
            $imageData = ($options['downloadFormat'] === 'singlelanguage' && (int)$options['includeImages'] === 1) ? (new GlossaryImages)->getGlossaryImageDataFromGlossidArr($glossidArr) : array();
            $summaryData = $this->getGlossarySummaryData($glossaryArr, $translationArr, $options['sourceData']);
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
            if(count($glossaryArr) > 0){
                $section = PhpWordService::getSection($phpWord, array('paperSize' => 'Letter', 'marginLeft' => 1080, 'marginRight' => 1080, 'marginTop' => 1080, 'marginBottom' => 1080, 'headerHeight' => 100, 'footerHeight' => 0));
                $this->writeDocxHeaderSection($section, $options['downloadTitle']);
                $this->writeDocxTitleSection($section, $options['downloadFormat'], $options['downloadTitle']);
                if($options['downloadFormat'] === 'translation'){
                    if($options['definitionHandling'] === 'nodef'){
                        $this->writeDocxNoDefinitionSection($section, $options['primaryLanguage'], $glossaryArr, $summaryData['translations'], $options['translationLanguageArr']);
                    }
                    else{
                        PhpWordService::addTextBreak($section);
                        foreach($glossaryArr as $glossArr){
                            $textrun = PhpWordService::getTextRun($section, 'transTermPara');
                            PhpWordService::addText($textrun, htmlspecialchars($glossArr['term'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transMainTermDefFont');
                            foreach($options['translationLanguageArr'] as $translationLanguage){
                                $termStr = '[No Translation]';
                                if(array_key_exists($glossArr['glossid'], $summaryData['translations']) && array_key_exists($translationLanguage, $summaryData['translations'][$glossArr['glossid']])) {
                                    $transStrArr = array();
                                    foreach($summaryData['translations'][$glossArr['glossid']][$translationLanguage] as $translation){
                                        $transStrArr[] = $translation['term'];
                                    }
                                    $termStr = implode('; ', $transStrArr);
                                }
                                PhpWordService::addText($textrun, htmlspecialchars((' (' . $translationLanguage . ': ' . $termStr . ')'), ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transMainTermNodefFont');
                            }
                            if($options['definitionHandling'] === 'onedef'){
                                $this->writeDocxOneDefinitionSection($section, $glossArr['definition']);
                            }
                            elseif($options['definitionHandling'] === 'alldef'){
                                $this->writeDocxAllDefinitionsSection($section, $glossArr['glossid'], $glossArr['definition'], $summaryData['translations'], $options['translationLanguageArr']);
                            }
                        }
                    }
                }
                else{
                    foreach($glossaryArr as $glossArr){
                        $textrun = PhpWordService::getTextRun($section, 'transTermPara');
                        PhpWordService::addText($textrun, htmlspecialchars($glossArr['term'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transMainTermDefFont');
                        if($glossArr['definition']){
                            $textrun = PhpWordService::getTextRun($section, 'transDefPara');
                            PhpWordService::addText($textrun, htmlspecialchars($glossArr['definition'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
                        }
                        if((int)$options['includeImages'] === 1 && array_key_exists($glossArr['glossid'], $imageData) && count($imageData[$glossArr['glossid']]) > 0) {
                            $this->writeDocxImageSection($section, $imageData[$glossArr['glossid']]);
                        }
                        else{
                            PhpWordService::addTextBreak($section);
                        }
                    }
                }
                if(count($summaryData['references']) > 0){
                    $this->writeDocxReferencesSection($section, $summaryData['references']);
                }
                if(count($summaryData['contributors']) > 0){
                    $this->writeDocxContributorsSection($section, $summaryData['contributors']);
                }
                if(count($summaryData['imgcontributors']) > 0){
                    $this->writeDocxImageContributorsSection($section, $summaryData['imgcontributors']);
                }
                $this->writeDocxCitationSection($section);
            }
            PhpWordService::saveDocument($phpWord, $fullPath);
            (new DataDownloadService)->streamDownload($contentType, $fullPath);
        }
    }

    public function writeDocxAllDefinitionsSection($section, $glossid, $definition, $translationData, $languageArr): void
    {
        $listItemRun = PhpWordService::getListItemRun($section, 'transDefList');
        PhpWordService::addText($listItemRun, htmlspecialchars(($definition ?: '[No Definition]'), ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
        foreach($languageArr as $translationLanguage){
            $listItemRun = PhpWordService::getListItemRun($section, 'transDefList');
            $defStr = '[No Definition]';
            if(array_key_exists($glossid, $translationData) && array_key_exists($translationLanguage, $translationData[$glossid])) {
                $transDefStrArr = array();
                foreach($translationData[$glossid][$translationLanguage] as $translation){
                    if($translation['definition']){
                        $transDefStrArr[] = $translation['definition'];
                    }
                }
                if(count($transDefStrArr) > 0){
                    $defStr = implode('; ', $transDefStrArr);
                }
            }
            PhpWordService::addText($listItemRun, htmlspecialchars($defStr, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transMainTermNodefFont');
        }
        PhpWordService::addTextBreak($section);
    }

    public function writeDocxCitationSection($section): void
    {
        $citationText = $GLOBALS['DEFAULT_TITLE'] . '. ' . date('Y') . '. ' . SanitizerService::getFullUrlPathPrefix() . '/index.php. ';
        $citationText .= 'Accessed on ' . date('F d') . '. ';
        PhpWordService::addTextBreak($section);
        $textrun = PhpWordService::getTextRun($section, 'titlePara');
        PhpWordService::addText($textrun, htmlspecialchars('How to Cite Us', ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermDefFont');
        $textrun = PhpWordService::getTextRun($section, 'transTermPara');
        PhpWordService::addText($textrun, htmlspecialchars($citationText, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermNodefFont');
    }

    public function writeDocxContributorsSection($section, $contribArr): void
    {
        PhpWordService::addTextBreak($section);
        $textrun = PhpWordService::getTextRun($section, 'titlePara');
        PhpWordService::addText($textrun, htmlspecialchars('Contributors', ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermDefFont');
        foreach($contribArr as $cont){
            $listItemRun = PhpWordService::getListItemRun($section, 'transDefList');
            PhpWordService::addText($listItemRun, htmlspecialchars($cont, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
        }
    }

    public function writeDocxHeaderSection($section, $title): void
    {
        $header = PhpWordService::getHeader($section);
        PhpWordService::addPreserveText($header, ($title . ' - p.{PAGE} ' . date('Y-m-d')), array('align' => 'right'));
    }

    public function writeDocxImageContributorsSection($section, $imgContribArr): void
    {
        PhpWordService::addTextBreak($section);
        $textrun = PhpWordService::getTextRun($section, 'titlePara');
        PhpWordService::addText($textrun, htmlspecialchars('Image Contributors', ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermDefFont');
        foreach($imgContribArr as $cont){
            $listItemRun = PhpWordService::getListItemRun($section, 'transDefList');
            PhpWordService::addText($listItemRun, htmlspecialchars($cont, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
        }
    }

    public function writeDocxImageSection($section, $imageArr): void
    {
        $table = PhpWordService::getTable($section, 'exportTable');
        foreach($imageArr as $imgArr){
            if($imgArr['url'] && FileSystemService::fileExists(($GLOBALS['SERVER_ROOT'] . $imgArr['url']))){
                PhpWordService::addTableRow($table);
                $cell = PhpWordService::getTableCell($table, 4125, $this->imageCellStyle);
                $textrun = PhpWordService::getTextRun($cell, 'transDefPara');
                $imgSize = FileSystemService::getImageSize($imgArr['url']);
                if($imgSize){
                    if($imgSize[0] > $imgSize[1]){
                        $targetWidth = $imgSize[0];
                        if($imgSize[0] > 230) {
                            $targetWidth = 230;
                        }
                        PhpWordService::addImage($textrun, ($GLOBALS['SERVER_ROOT'] . $imgArr['url']), array('width' => $targetWidth));
                    }
                    else{
                        $targetHeight = $imgSize[1];
                        if($imgSize[1] > 170) {
                            $targetHeight = 170;
                        }
                        PhpWordService::addImage($textrun, ($GLOBALS['SERVER_ROOT'] . $imgArr['url']), array('height' => $targetHeight));
                    }
                    $cell = PhpWordService::getTableCell($table, 5625, $this->imageCellStyle);
                    $textrun = PhpWordService::getTextRun($cell, 'transTermPara');
                    if($imgArr['createdby']){
                        PhpWordService::addText($textrun, htmlspecialchars('Image courtesy of: ', ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermDefFont');
                        PhpWordService::addText($textrun, htmlspecialchars($imgArr['createdby'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
                        PhpWordService::addTextBreak($textrun);
                        PhpWordService::addTextBreak($textrun);
                    }
                    if($imgArr['structures']){
                        PhpWordService::addText($textrun, htmlspecialchars('Structures: ', ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermDefFont');
                        PhpWordService::addText($textrun, htmlspecialchars($imgArr['structures'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
                        PhpWordService::addTextBreak($textrun);
                        PhpWordService::addTextBreak($textrun);
                    }
                    if($imgArr['notes']){
                        PhpWordService::addText($textrun, htmlspecialchars('Notes: ', ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermDefFont');
                        PhpWordService::addText($textrun, htmlspecialchars($imgArr['notes'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
                    }
                }
            }
        }
    }

    public function writeDocxNoDefinitionSection($section, $primaryLanguage, $glossaryArr, $translationData, $languageArr): void
    {
        $table = PhpWordService::getTable($section, 'exportTable');
        PhpWordService::addTableRow($table);
        $cell = PhpWordService::getTableCell($table, 2520, $this->nodefCellStyle);
        $textrun = PhpWordService::getTextRun($cell, 'transTermPara');
        PhpWordService::addText($textrun, htmlspecialchars($primaryLanguage, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTableHeaderNodeFont');
        foreach($languageArr as $translationLanguage){
            $cell = PhpWordService::getTableCell($table, 2520, $this->nodefCellStyle);
            $textrun = PhpWordService::getTextRun($cell, 'transTermPara');
            PhpWordService::addText($textrun, htmlspecialchars($translationLanguage, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTableHeaderNodeFont');
        }
        foreach($glossaryArr as $glossArr){
            PhpWordService::addTableRow($table);
            $cell = PhpWordService::getTableCell($table, 2520, $this->nodefCellStyle);
            $textrun = PhpWordService::getTextRun($cell, 'transTermPara');
            PhpWordService::addText($textrun, htmlspecialchars($glossArr['term'], ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transMainTermNodefFont');
            foreach($languageArr as $translationLanguage){
                $cell = PhpWordService::getTableCell($table, 2520, $this->nodefCellStyle);
                $textrun = PhpWordService::getTextRun($cell, 'transTermPara');
                $termStr = '[No Translation]';
                if(array_key_exists($glossArr['glossid'], $translationData) && array_key_exists($translationLanguage, $translationData[$glossArr['glossid']])) {
                    $transStrArr = array();
                    foreach($translationData[$glossArr['glossid']][$translationLanguage] as $translation){
                        $transStrArr[] = $translation['term'];
                    }
                    $termStr = implode('; ', $transStrArr);
                }
                PhpWordService::addText($textrun, htmlspecialchars($termStr, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transMainTermNodefFont');
            }
        }
    }

    public function writeDocxOneDefinitionSection($section, $definition): void
    {
        $textrun = PhpWordService::getTextRun($section, 'transDefPara');
        PhpWordService::addText($textrun, htmlspecialchars(($definition ?: '[No Definition]'), ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
        PhpWordService::addTextBreak($section);
    }

    public function writeDocxReferencesSection($section, $refArr): void
    {
        PhpWordService::addTextBreak($section);
        $textrun = PhpWordService::getTextRun($section, 'titlePara');
        PhpWordService::addText($textrun, htmlspecialchars('References', ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transTransTermDefFont');
        foreach($refArr as $ref){
            $listItemRun = PhpWordService::getListItemRun($section, 'transDefList');
            PhpWordService::addText($listItemRun, htmlspecialchars($ref, ENT_QUOTES | ENT_XML1, 'UTF-8'), 'transDefTextFont');
        }
    }

    public function writeDocxTitleSection($section, $format, $title): void
    {
        $textrun = PhpWordService::getTextRun($section, 'titlePara');
        PhpWordService::addText($textrun, htmlspecialchars((($format === 'translation' ? 'Translation Table for ' : 'Single Language Glossary for ') . $title), ENT_QUOTES | ENT_XML1, 'UTF-8'), 'titleFont');
        PhpWordService::addTextBreak($textrun);
    }
}
