<?php
include_once(__DIR__ . '/DataUtilitiesService.php');
include_once(__DIR__ . '/DbService.php');
include_once(__DIR__ . '/SanitizerService.php');

class DarwinCoreArchiverService {

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

    public function setDownloadHeaders($downloadType, $outputType, $filename, $content): void
    {
        header('Content-Type: ' . $outputType);
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        if($downloadType === 'geojson' || $downloadType === 'gpx' || $downloadType === 'kml'){
            header('Content-Length: ' . strlen($content));
        }
        else{
            header('Content-Length: ' . filesize($content));
        }
    }


}
