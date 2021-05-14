<?php
include_once(__DIR__ . '/DbConnection.php');

class Utilities {

	private $conn;

	public function __construct() {
		$connection = new DbConnection();
		$this->conn = $connection->getConnection();
	}

    public function getContentString($url): array
    {
        $retArr = array();
        if($url && $fh = fopen($url, 'rb')) {
            stream_set_timeout($fh, 10);
            $contentStr = '';
            while($line = fread($fh, 1024)){
                $contentStr .= trim($line);
            }
            fclose($fh);
            $retArr['str'] = $contentStr;
            $statusStr = $http_response_header[0];
            if(preg_match( '#HTTP/[0-9.]+\s+(\d+)#',$statusStr, $out)){
                $retArr['code'] = (int)$out[1];
            }
        }
        return $retArr;
    }

    public function cleanInStr($str){
        $newStr = trim($str);
        if($newStr){
            $newStr = preg_replace('/\s\s+/', ' ',$newStr);
            $newStr = $this->conn->real_escape_string($newStr);
        }
        return $newStr;
    }
}
