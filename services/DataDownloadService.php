<?php
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/DataUtilitiesService.php');
include_once(__DIR__ . '/SanitizerService.php');

class DataDownloadService {

    private $conn;

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
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
        return $returnVal;
    }

    public function writeGPXFromOccurrenceArr($occArr): string
    {
        $returnStr = '<gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '.
            'xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd" version="1.1" creator="springsdata.org">';
        foreach($occArr as $data){
            $returnStr .= '<wpt lat="' . $data['decimallatitude'] . '" lon="' . $data['decimallongitude'] . '">';
            $returnStr .= '<name>' . ($data['recordedby'] ? htmlspecialchars($data['recordedby']) : '') . ' ' . ($data['recordnumber'] ? htmlspecialchars($data['recordnumber']) : '') . '</name>';
            $returnStr .= '<desc>' . ($data['sciname'] ? htmlspecialchars($data['sciname']) : '') . '</desc>';
            $returnStr .= '</wpt>';
        }
        $returnStr .= '</gpx>';
        return $returnStr;
    }

    public function writeKMLFromOccurrenceArr($occArr): string
    {
        $returnStr = '<kml xmlns="http://www.opengis.net/kml/2.2">';
        $returnStr .= '<Document>';
        foreach($occArr as $data){
            $returnStr .= '<Placemark>';
            $returnStr .= '<name>' . ($data['recordedby'] ? htmlspecialchars($data['recordedby']) : '') . ' ' . ($data['recordnumber'] ? htmlspecialchars($data['recordnumber']) : '') . '</name>';
            $returnStr .= '<ExtendedData>';
            foreach($data as $field => $value) {
                $returnStr .= '<Data name="' . $field . '">';
                $returnStr .= '<value>' . ($value ? htmlspecialchars($value) : '') . '</value>';
                $returnStr .= '</Data>';
            }
            $returnStr .= '</ExtendedData>';
            $returnStr .= '<Point><coordinates>' . $data['decimallongitude'] . ',' . $data['decimallatitude'] . '</coordinates></Point>';
            $returnStr .= '</Placemark>';
        }
        $returnStr .= '</Document></kml>';
        return $returnStr;
    }
}
