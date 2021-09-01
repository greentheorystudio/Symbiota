<?php

class Utilities {

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
}
