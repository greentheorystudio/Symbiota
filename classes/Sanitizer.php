<?php
include_once(__DIR__ . '/DbConnection.php');

class Sanitizer {

    public static function cleanInStr($str): string
    {
        $connection = new DbConnection();
        $conn = $connection->getConnection();
        $newStr = trim($str);
        $newStr = preg_replace('/\s\s+/', ' ',$newStr);
        $newStr = $conn->real_escape_string($newStr);
        return $newStr;
    }

    public static function cleanInArray($arr): array
    {
        $newArray = array();
        foreach($arr as $key => $value){
            if(is_array($value)){
                $newArray[self::cleanInStr($key)] = self::cleanInArray($value);
            }
            else{
                $newArray[self::cleanInStr($key)] = self::cleanInStr($value);
            }
        }
        return $newArray;
    }

    public static function cleanOutStr($str): string
    {
        return str_replace(array('"', "'"), array('&quot;', '&apos;'), $str);
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
        if(substr($requestPath,-4) !== '.php' && substr($requestPath,-5) !== '.html'){
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

    public static function getCleanedRequestPath($includeArgs = null): string
    {
        $returnPath = '';
        $requestArgs = '';
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
}
