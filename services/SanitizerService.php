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
        if(strpos($fullRequestPath, '?') !== false){
            $fullRequestPathParts = explode('?', $fullRequestPath);
            if($fullRequestPathParts){
                $returnPath = htmlspecialchars($fullRequestPathParts[0]);
            }
        }
        else{
            $returnPath = htmlspecialchars($fullRequestPath);
        }
        if(substr($returnPath,-4) !== '.php'){
            $fixedPath = '/index.php';
            if(strpos($returnPath, '.php') !== false){
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
            if(array_key_exists('queryId',$_REQUEST) && (!$fullRequestPathParts || strpos($fullRequestPathParts[1], 'queryId=') === false)){
                $argArr[] = 'queryId=' . (int)$_REQUEST['queryId'];
            }
            if($argArr){
                $returnPath .= '?' . implode('&', $argArr);
            }
        }
        return $returnPath;
    }

    public static function getSqlValueString($conn, $value, $dataType): string
    {
        $returnStr = 'NULL';
        if($value){
            if($dataType !== 'number'){
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
            elseif((string)$value !== '' && is_numeric($value)){
                $returnStr = (string)$value;
            }
        }
        return $returnStr;
    }

    public static function validateInternalRequest(): bool
    {
        $valid = false;
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if($referer){
            if(strpos($referer, "http") !== 0){
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

    public static function validateRequestPath(): void
    {
        $requestPath = '';
        $fullRequestPath = $_SERVER['REQUEST_URI'];
        if(strpos($fullRequestPath, '?') !== false){
            $fullRequestPathParts = explode('?', $fullRequestPath);
            if($fullRequestPathParts){
                $requestPath = htmlspecialchars($fullRequestPathParts[0]);
            }
        }
        else{
            $requestPath = htmlspecialchars($fullRequestPath);
        }
        if(strpos($requestPath, $GLOBALS['IMAGE_ROOT_URL']) === false && substr($requestPath,-4) !== '.php' && substr($requestPath,-5) !== '.html'){
            $clientRoot = $GLOBALS['CLIENT_ROOT'] ?? '';
            $fixedPath = $clientRoot . '/index.php';
            if(strpos($requestPath, '.php') !== false){
                $requestPathParts = explode('.php', $requestPath);
                if($requestPathParts){
                    $fixedPath = $clientRoot . $requestPathParts[0] . '.php';
                }

            }
            header('Location: ' . $fixedPath);
        }
    }
}
