<?php
class FileSystemService {

    public static function copyFileToTarget($source, $targetPath, $targetFilename): bool
    {
        if(copy($source, $targetPath . '/' . $targetFilename)){
            return true;
        }
        return false;
    }

    public static function createNewImageFromFile($source, $targetPath, $targetFilename, $newWidth, $newHeight, $sourceWidth, $sourceHeight): bool
    {
        $sourceImage = imagecreatefromjpeg($source);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresized($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
        if(imagejpeg($newImage, ($targetPath . '/' . $targetFilename), 100)){
            return true;
        }
        return false;
    }

    public static function deleteDirectory($dir): bool
	{
        if(!file_exists($dir)){
            $returnVal = true;
        }
        elseif(is_dir($dir)) {
            foreach(scandir($dir) as $item){
                if($item === '.' || $item === '..'){
                    continue;
                }
                if(!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)){
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

    public static function getServerUploadFilename($targetPath, $origFilename, $suffix = null): string
    {
        $dotPos = strrpos($origFilename,'.');
        $fileExt = strtolower(substr($origFilename, ($dotPos + 1)));
        $shortOrigFilename = substr($origFilename,0, $dotPos);
        $tempFileName = $shortOrigFilename . ($suffix ?: '');
        $cnt = 0;
        while(file_exists($targetPath . $tempFileName . '.' . $fileExt)){
            $tempFileName = $shortOrigFilename . ($suffix ?: '') . '_' . $cnt;
            $cnt++;
        }
        if($cnt){
            $returnStr = $tempFileName . '.' . $fileExt;
        }
        elseif($suffix){
            $returnStr = $shortOrigFilename . $suffix . '.' . $fileExt;
        }
        else{
            $returnStr = $origFilename;
        }
        return $returnStr;
    }

    public static function getServerUploadPath($fragment): string
    {
        $fullUploadPath = $GLOBALS['IMAGE_ROOT_PATH'] . '/' . $fragment;
        if(!file_exists($fullUploadPath) && !mkdir($fullUploadPath, 0777, true) && !is_dir($fullUploadPath)) {
            $fullUploadPath = '';
        }
        $fullUploadPath .= '/' . date('Ym');
        if(!file_exists($fullUploadPath) && !mkdir($fullUploadPath, 0777, true) && !is_dir($fullUploadPath)) {
            $fullUploadPath = '';
        }
        return $fullUploadPath;
    }

    public static function moveUploadedFileToServer($file, $targetPath, $targetFilename): bool
    {
        if(move_uploaded_file($file['tmp_name'], $targetPath . '/' . $targetFilename)){
            return true;
        }
        return false;
    }

    public static function processImageDerivatives($imageData, $targetPath, $targetFilename, $origFilename): array
    {
        if((int)$imageData['width'] > 0 && (int)$imageData['height'] > 0){
            if((int)$imageData['width'] > (int)$GLOBALS['IMG_WEB_WIDTH']){
                $webFilename = self::getServerUploadFilename($targetPath, $origFilename);
                if($webFilename && self::createNewImageFromFile(($targetPath . '/' . $targetFilename), $targetPath, $webFilename, $GLOBALS['IMG_WEB_WIDTH'], round($GLOBALS['IMG_WEB_WIDTH'] * ($imageData['height'] / $imageData['width'])), $imageData['width'], $imageData['height'])){
                    $imageData['url'] = $GLOBALS['IMAGE_ROOT_URL'] . '/' . $targetPath . '/' . $webFilename;
                }
            }
            else{
                $imageData['url'] = $imageData['originalurl'];
            }
            if((int)$imageData['width'] > (int)$GLOBALS['IMG_TN_WIDTH']){
                $tnFilename = self::getServerUploadFilename($targetPath, $origFilename, '_tn');
                if($tnFilename && self::createNewImageFromFile(($targetPath . '/' . $targetFilename), $targetPath, $tnFilename, $GLOBALS['IMG_TN_WIDTH'], round($GLOBALS['IMG_TN_WIDTH'] * ($imageData['height'] / $imageData['width'])), $imageData['width'], $imageData['height'])){
                    $imageData['thumbnailurl'] = $GLOBALS['IMAGE_ROOT_URL'] . '/' . $targetPath . '/' . $tnFilename;
                }
            }
        }
        return $imageData;
    }

    public static function processUploadImageFromFile($imageData, $uploadPath): array
    {
        $origFilename = $_FILES['imgfile']['name'];
        $targetPath = self::getServerUploadPath($uploadPath);
        if($targetPath && $origFilename) {
            $targetFilename = self::getServerUploadFilename($targetPath, $origFilename, '_lg');
            if($targetFilename && self::moveUploadedFileToServer($_FILES['imgfile'], $targetPath, $targetFilename)){
                $imageData['originalurl'] = $GLOBALS['IMAGE_ROOT_URL'] . '/' . $targetPath . '/' . $targetFilename;
                $imageData = self::processImageDerivatives($imageData, $targetPath, $targetFilename, $origFilename);
            }
        }
        return $imageData;
    }

    public static function processUploadImageFromExternalUrl($imageData, $uploadPath): array
    {
        $origFilename = $imageData['filename'];
        $targetPath = self::getServerUploadPath($uploadPath);
        if($targetPath && $origFilename) {
            $targetFilename = self::getServerUploadFilename($targetPath, $origFilename, '_lg');
            if($targetFilename && self::copyFileToTarget($imageData['sourceurl'], $targetPath, $targetFilename)){
                $imageData['originalurl'] = $GLOBALS['IMAGE_ROOT_URL'] . '/' . $targetPath . '/' . $targetFilename;
                $imageData = self::processImageDerivatives($imageData, $targetPath, $targetFilename, $origFilename);
            }
        }
        return $imageData;
    }
}
