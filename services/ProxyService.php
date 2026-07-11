<?php
include_once(__DIR__ . '/FileSystemService.php');

class ProxyService {

	public static function getExternalData($url, $requestType, $postData = null): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, str_replace(' ','%20', $url));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        if($requestType === 'post' && $postData){
            $headers = array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Content-Length: ' . strlen(http_build_query($postData))
            );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result ? mb_convert_encoding($result, 'UTF-8', 'UTF-8,ISO-8859-1') : '{}';
    }

    public static function getFileContentsFromUrl($url): string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        if (curl_errno($ch)) {
            $returnVal = '';
        }
        else {
            $returnVal = file_get_contents($url);
        }
        curl_close($ch);
        return $returnVal;
    }

    public static function getFileInfoFromUrl($url): array
    {
        $size = array();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, static function($ch, $headerLine) use (&$filename) {
            if(strncasecmp($headerLine, 'Location:', 9) === 0) {
                $targetUrl = trim(substr($headerLine, 9));
                if($targetUrl){
                    $path = parse_url($targetUrl, PHP_URL_PATH);
                    $filename = basename($path);
                }
            }
            return strlen($headerLine);
        });
        curl_exec($ch);
        $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if((int)$httpResponseCode === 200 && $filename && (str_ends_with(strtolower($filename), '.jpg') || str_ends_with(strtolower($filename), '.jpeg') || str_ends_with(strtolower($filename), '.png'))){
            $size = FileSystemService::getImageSize($url);
        }
        return [
            'fileExists' => (int)$httpResponseCode === 200,
            'fileName' => $filename,
            'fileSize' => (int)$fileSize,
            'fileHeight' => $size ? (int)$size[1] : 0,
            'fileWidth' => $size ? (int)$size[0] : 0
        ];
    }

    public static function getFilenameFromUrl($url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, static function($ch, $headerLine) use (&$returnVal) {
            if(strncasecmp($headerLine, 'Location:', 9) === 0) {
                $targetUrl = trim(substr($headerLine, 9));
                if($targetUrl){
                    $path = parse_url($targetUrl, PHP_URL_PATH);
                    $returnVal = basename($path);
                }
            }
            return strlen($headerLine);
        });
        curl_exec($ch);
        curl_close($ch);
        return $returnVal;
    }
}
