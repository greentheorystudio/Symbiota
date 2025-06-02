<?php
include_once(__DIR__ . '/../models/Checklists.php');
include_once(__DIR__ . '/../models/ChecklistTaxa.php');
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/DataDownloadService.php');
include_once(__DIR__ . '/FileSystemService.php');

class ChecklistPackagingService {

    private $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
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

    public function getChecklistTaxaData($clidArr, $options): array
    {
        $includeSynonymyData = (int)$options['synonyms'] === 1;
        $includeVernacularData = (int)$options['vernaculars'] === 1;
        $taxaArr = (new ChecklistTaxa)->getChecklistTaxa($clidArr, false, $includeSynonymyData, $includeVernacularData, $options['taxaSort']);
        if($options['taxonFilter'] && array_key_exists('rankid', $options['taxonFilter']) && (int)$options['taxonFilter']['rankid'] > 0 && count($taxaArr) > 0){
            $taxaArr = $this->filterTaxaArr($options['taxonFilter'], $taxaArr);
        }
        return $taxaArr;
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
            (new DataDownloadService)->setDownloadHeaders('csv', $contentType, basename($fullPath), $fullPath);
            flush();
            readfile($fullPath);
            //FileSystemService::deleteFile($fullPath, true);
        }
    }
}
