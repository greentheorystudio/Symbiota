<?php
class Utilities {

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
