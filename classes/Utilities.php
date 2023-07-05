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

    public function deleteDirectory($dir): bool
    {
        $returnVal = false;
        if(!file_exists($dir)){
            $returnVal = true;
        }
        elseif(is_dir($dir)) {
            foreach(scandir($dir) as $item){
                if($item === '.' || $item === '..'){
                    continue;
                }
                if(!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)){
                    return false;
                }

            }
            $returnVal = rmdir($dir);
        }
        else {
            $returnVal = unlink($dir);
        }
        return $returnVal;
    }
}
