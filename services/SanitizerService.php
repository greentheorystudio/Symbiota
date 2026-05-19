<?php
class SanitizerService {

    public static function cleanInStr($conn, $str): string
    {
        $newStr = '';
        if($str){
            $newStr = trim($str);
            $newStr = preg_replace('/\s\s+/', ' ',$newStr);
            $newStr = $conn->real_escape_string($newStr);
        }
        elseif(is_numeric($str)){
            $newStr = (string)$str;
        }
        return $newStr;
    }

    public static function cleanInArray($conn, $arr): array
    {
        $newArray = array();
        foreach($arr as $key => $value){
            if(is_array($value)){
                $newArray[self::cleanInStr($conn,$key)] = self::cleanInArray($conn,$value);
            }
            else{
                $newArray[self::cleanInStr($conn,$key)] = self::cleanInStr($conn,$value);
            }
        }
        return $newArray;
    }

    public static function cleanOutStr($str): string
    {
        return $str ? str_replace(array('"', "'"), array('&quot;', '&apos;'), $str) : '';
    }

    public static function cleanOutArray($arr): array
    {
        $newArray = array();
        foreach($arr as $key => $value){
            if(is_array($value)){
                $newArray[$key] = self::cleanOutArray($value);
            }
            else{
                $newArray[$key] = self::cleanOutStr($value);
            }
        }
        return $newArray;
    }

    public static function getCleanedRequestPath($includeArgs = null): string
    {
        $returnPath = '';
        $fullRequestPathParts = array();
        $fullRequestPath = $_SERVER['REQUEST_URI'];
        if(str_contains($fullRequestPath, '?')){
            $fullRequestPathParts = explode('?', $fullRequestPath);
            if($fullRequestPathParts){
                $returnPath = htmlspecialchars($fullRequestPathParts[0]);
            }
        }
        else{
            $returnPath = htmlspecialchars($fullRequestPath);
        }
        if(!str_ends_with($returnPath, '.php')){
            $fixedPath = '/index.php';
            if(str_contains($returnPath, '.php')){
                $returnPathParts = explode('.php', $returnPath);
                if($returnPathParts){
                    $fixedPath = $returnPathParts[0] . '.php';
                }

            }
            $returnPath = $fixedPath;
        }
        if($includeArgs){
            $argArr = array();
            if($fullRequestPathParts){
                $argArr[] = str_replace('&amp;', '&',htmlspecialchars($fullRequestPathParts[1], ENT_NOQUOTES));
            }
            if(array_key_exists('queryId',$_REQUEST) && (!$fullRequestPathParts || !str_contains($fullRequestPathParts[1], 'queryId='))){
                $argArr[] = 'queryId=' . (int)$_REQUEST['queryId'];
            }
            if($argArr){
                $returnPath .= '?' . implode('&', $argArr);
            }
        }
        return $returnPath;
    }

    public static function getConnectionProtocol(): string
    {
        $returnStr = 'http://';
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' && $_SERVER['HTTPS'] !== ''){
            $returnStr = 'https://';
        }
        if(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443){
            $returnStr = 'https://';
        }
        if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            $returnStr = 'https://';
        }
        return $returnStr;
    }

    public static function getFullUrlPathPrefix(): string
    {
        return self::getConnectionProtocol() . $_SERVER['HTTP_HOST'] . $GLOBALS['CLIENT_ROOT'];
    }

    public static function getSqlValueString($conn, $value, $dataType): string
    {
        $returnStr = 'NULL';
        if($value){
            if(($dataType === 'number' && (string)$value !== '') || is_numeric($value)){
                $returnStr = (string)$value;
            }
            elseif($dataType !== 'number'){
                if($dataType === 'json' || $dataType === 'sql'){
                    $returnStr = "'" . $value . "'";
                }
                else{
                    $cleanedVal = self::cleanInStr($conn, $value);
                    if($cleanedVal && $cleanedVal !== ''){
                        $cleanedVal = str_replace('\"', '"', $cleanedVal);
                        $returnStr = '"' . str_replace('"', '""', $cleanedVal) . '"';
                    }
                }
            }
        }
        return $returnStr;
    }

    public static function validateInternalRequest(): bool
    {
        $valid = false;
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if($referer){
            if(strncmp($referer, 'http', 4) !== 0){
                $referer = 'http:' . $referer;
            }
            $arr1 = explode('//', $referer);
            $arr2 = explode('/', $arr1[1]);
            if($arr2[0] === $_SERVER['HTTP_HOST']){
                $valid = true;
            }
        }
        return $valid;
    }

    public static function validateJsonStr($jsonStr): bool
    {
        try {
            $data = json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);
            return (bool)$data;
        } catch (JsonException) {
            return false;
        }
    }

    public static function validateRequestPath(): void
    {
        $requestPath = '';
        $fullRequestPath = $_SERVER['REQUEST_URI'];
        if(str_contains($fullRequestPath, '?')){
            $fullRequestPathParts = explode('?', $fullRequestPath);
            if($fullRequestPathParts){
                $requestPath = htmlspecialchars($fullRequestPathParts[0]);
            }
        }
        else{
            $requestPath = htmlspecialchars($fullRequestPath);
        }
        if(!str_contains($requestPath, $GLOBALS['IMAGE_ROOT_URL']) && !str_ends_with($requestPath, '.php') && !str_ends_with($requestPath, '.html')){
            $clientRoot = $GLOBALS['CLIENT_ROOT'] ?? '';
            $fixedPath = $clientRoot . '/index.php';
            if(str_contains($requestPath, '.php')){
                $requestPathParts = explode('.php', $requestPath);
                if($requestPathParts){
                    $fixedPath = $clientRoot . $requestPathParts[0] . '.php';
                }

            }
            header('Location: ' . $fixedPath);
        }
    }
}
