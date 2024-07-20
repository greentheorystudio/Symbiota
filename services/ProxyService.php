<?php
class ProxyService {

	public static function getExternalData($url, $requestType): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, str_replace(' ','%20', $url));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 90);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        if($requestType === 'post'){
            $headers = array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'Content-Length: '.strlen(http_build_query($pArr))
            );
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($pArr));
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result ? mb_convert_encoding($result, 'UTF-8', 'UTF-8,ISO-8859-1') : '{}';
    }

    public static function getFileInfoFromUrl($url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        curl_exec($ch);
        $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [
            'fileExists' => (int)$httpResponseCode === 200,
            'fileSize' => (int)$fileSize
        ];
    }
}
