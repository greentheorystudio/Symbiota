<?php
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/FileSystemService.php');

class DataDownloadService {

    private $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function getContentTypeFromFileType($fileType): string
    {
        $returnVal = '';
        if($fileType === 'csv'){
            $returnVal = 'text/csv; charset=UTF-8';
        }
        elseif($fileType === 'zip'){
            $returnVal = 'application/zip';
        }
        elseif($fileType === 'geojson'){
            $returnVal = 'application/vnd.geo+json';
        }
        elseif($fileType === 'kml'){
            $returnVal = 'application/vnd.google-earth.kml+xml';
        }
        elseif($fileType === 'gpx'){
            $returnVal = 'application/gpx+xml';
        }
        elseif($fileType === 'docx'){
            $returnVal = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }
        return $returnVal;
    }

    public function processCsvDownloadFromSql($sql, $filename): void
    {
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($sql && $filename && $targetPath){
            $fullPath = $targetPath . '/' . $filename;
            $fileHandler = FileSystemService::openFileHandler($fullPath);
            if($result = $this->conn->query($sql,MYSQLI_USE_RESULT)){
                $headerRow = array();
                $fields = mysqli_fetch_fields($result);
                foreach($fields as $val){
                    $headerRow[] = $val->name;
                }
                FileSystemService::writeRowToCsv($fileHandler, $headerRow);
                while($row = $result->fetch_assoc()){
                    FileSystemService::writeRowToCsv($fileHandler, $row);
                }
                $result->free();
                FileSystemService::closeFileHandler($fileHandler);
                $outputType = $this->getContentTypeFromFileType('csv');
                $this->setDownloadHeaders($outputType, basename($fullPath), $fullPath);
                flush();
                readfile($fullPath);
                FileSystemService::deleteFile($fullPath, true);
            }
        }
    }

    public function setDownloadHeaders($outputType, $filename, $content): void
    {
        header('Content-Type: ' . $outputType);
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($content));
    }

    public function streamDownload($contentType, $outputFilePath): void
    {
        if(ob_get_level()){
            ob_end_clean();
        }
        $this->setDownloadHeaders($contentType, basename($outputFilePath), $outputFilePath);
        readfile($outputFilePath);
        flush();
        FileSystemService::deleteFile($outputFilePath, true);
    }

    public function writeGeoJSONFromGeoJSONArr($fileName, $dataArr): string
    {
        $fullPath = '';
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($fileName && $targetPath){
            $fullPath = $targetPath . '/' . $fileName;
            $fileHandler = FileSystemService::openFileHandler($fullPath);
            FileSystemService::writeTextToFile($fileHandler, ('{"type":"FeatureCollection","numFound":' . ((int)$dataArr['numFound'] > 0 ? $dataArr['numFound'] : '0') . ',"start":0,"features":['));
            $index = 0;
            foreach($dataArr['features'] as $feature){
                FileSystemService::writeTextToFile($fileHandler, json_encode($feature));
                $index++;
                if($index < (int)$dataArr['numFound']){
                    FileSystemService::writeTextToFile($fileHandler, ',');
                }
            }
            FileSystemService::writeTextToFile($fileHandler, ']}');
            FileSystemService::closeFileHandler($fileHandler);
        }
        return $fullPath;
    }

    public function writeGPXFromOccurrenceArr($fileName, $occArr): string
    {
        $fullPath = '';
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($fileName && $targetPath){
            $fullPath = $targetPath . '/' . $fileName;
            $fileHandler = FileSystemService::openFileHandler($fullPath);
            FileSystemService::writeTextToFile($fileHandler, '<gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ');
            FileSystemService::writeTextToFile($fileHandler, 'xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" version="1.1" creator="springsdata.org">');
            foreach($occArr as $data){
                FileSystemService::writeTextToFile($fileHandler, ('<wpt lat="' . $data['decimallatitude'] . '" lon="' . $data['decimallongitude'] . '">'));
                FileSystemService::writeTextToFile($fileHandler, ('<name>' . ($data['recordedby'] ? htmlspecialchars($data['recordedby']) : '') . ' ' . ($data['recordnumber'] ? htmlspecialchars($data['recordnumber']) : '') . '</name>'));
                FileSystemService::writeTextToFile($fileHandler, ('<desc>' . ($data['sciname'] ? htmlspecialchars($data['sciname']) : '') . '</desc>'));
                FileSystemService::writeTextToFile($fileHandler, '</wpt>');
            }
            FileSystemService::writeTextToFile($fileHandler, '</gpx>');
            FileSystemService::closeFileHandler($fileHandler);
        }
        return $fullPath;
    }

    public function writeKMLFromOccurrenceArr($fileName, $occArr): string
    {
        $fullPath = '';
        $targetPath = FileSystemService::getTempDownloadUploadPath();
        if($fileName && $targetPath){
            $fullPath = $targetPath . '/' . $fileName;
            $fileHandler = FileSystemService::openFileHandler($fullPath);
            FileSystemService::writeTextToFile($fileHandler, '<kml xmlns="http://www.opengis.net/kml/2.2">');
            FileSystemService::writeTextToFile($fileHandler, '<Document>');
            foreach($occArr as $data){
                FileSystemService::writeTextToFile($fileHandler, '<Placemark>');
                FileSystemService::writeTextToFile($fileHandler, ('<name>' . ($data['recordedby'] ? htmlspecialchars($data['recordedby']) : '') . ' ' . ($data['recordnumber'] ? htmlspecialchars($data['recordnumber']) : '') . '</name>'));
                FileSystemService::writeTextToFile($fileHandler, '<ExtendedData>');
                foreach($data as $field => $value) {
                    FileSystemService::writeTextToFile($fileHandler, ('<Data name="' . $field . '">'));
                    FileSystemService::writeTextToFile($fileHandler, ('<value>' . ($value ? htmlspecialchars($value) : '') . '</value>'));
                    FileSystemService::writeTextToFile($fileHandler, '</Data>');
                }
                FileSystemService::writeTextToFile($fileHandler, '</ExtendedData>');
                FileSystemService::writeTextToFile($fileHandler, ('<Point><coordinates>' . (float)$data['decimallongitude'] . ',' . (float)$data['decimallatitude'] . '</coordinates></Point>'));
                FileSystemService::writeTextToFile($fileHandler, '</Placemark>');
            }
            FileSystemService::writeTextToFile($fileHandler, '</Document></kml>');
            FileSystemService::closeFileHandler($fileHandler);
        }
        return $fullPath;
    }
}
