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

    public static function deleteFile($filePath, $cleanParentFolder = false): void
    {
        if(file_exists($filePath)) {
            unlink($filePath);
        }
        if($cleanParentFolder){
            $parentPath = self::getParentFolderPath($filePath);
            if(is_dir($parentPath) && !scandir($parentPath)){
                unlink($parentPath);
            }
        }
    }

    public static function getDirectoryFilenameArr($dirPath): array
    {
        $returnArr = array();
        if(is_dir($dirPath) && $handle = opendir($dirPath)) {
            while(($item = readdir($handle)) !== false){
                if($item && $item !== '.' && $item !== '..'){
                    $returnArr[] = $item;
                }
            }
            closedir($handle);
        }
        return $returnArr;
    }

    public static function getImageSize($imageUrl): array
    {
        if(strpos($imageUrl, '/') === 0){
            $imageUrl = self::getServerPathFromUrlPath($imageUrl);
        }
        return getimagesize($imageUrl);
    }

    public static function getParentFolderPath($filePath): string
    {
        $pathParts = explode('/', $filePath);
        array_pop($pathParts);
        return implode('/', $pathParts);
    }

    public static function getServerPathFromUrlPath($path): string
    {
        return str_replace($GLOBALS['IMAGE_ROOT_URL'], $GLOBALS['IMAGE_ROOT_PATH'], $path);
    }

    public static function getServerUploadFilename($targetPath, $origFilename, $suffix = null): string
    {
        $dotPos = strrpos($origFilename,'.');
        $fileExt = strtolower(substr($origFilename, ($dotPos + 1)));
        $shortOrigFilename = substr($origFilename,0, $dotPos);
        $tempFileName = $shortOrigFilename . ($suffix ?: '');
        $cnt = 0;
        while(file_exists($targetPath . '/' . $tempFileName . '.' . $fileExt)){
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

    public static function getServerMediaUploadPath($fragment): string
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

    public static function getTempDwcaUploadPath($collid): string
    {
        $fullUploadPath = $GLOBALS['TEMP_DIR_ROOT'] . '/downloads';
        if(!file_exists($fullUploadPath) && !mkdir($fullUploadPath, 0777, true) && !is_dir($fullUploadPath)) {
            $fullUploadPath = '';
        }
        $fullUploadPath .= '/' . $collid . '_' . time();
        if(!file_exists($fullUploadPath) && !mkdir($fullUploadPath, 0777, true) && !is_dir($fullUploadPath)) {
            $fullUploadPath = '';
        }
        return $fullUploadPath;
    }

    public static function getUrlPathFromServerPath($path): string
    {
        return str_replace($GLOBALS['SERVER_ROOT'], '', $path);
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
                    $imageData['url'] = self::getUrlPathFromServerPath($targetPath . '/' . $webFilename);
                }
            }
            else{
                $imageData['url'] = $imageData['originalurl'];
            }
            if((int)$imageData['width'] > (int)$GLOBALS['IMG_TN_WIDTH']){
                $tnFilename = self::getServerUploadFilename($targetPath, $origFilename, '_tn');
                if($tnFilename && self::createNewImageFromFile(($targetPath . '/' . $targetFilename), $targetPath, $tnFilename, $GLOBALS['IMG_TN_WIDTH'], round($GLOBALS['IMG_TN_WIDTH'] * ($imageData['height'] / $imageData['width'])), $imageData['width'], $imageData['height'])){
                    $imageData['thumbnailurl'] = self::getUrlPathFromServerPath($targetPath . '/' . $tnFilename);
                }
            }
        }
        return $imageData;
    }

    public static function processUploadImageFromFile($imageData, $uploadPath): array
    {
        $origFilename = $_FILES['imgfile']['name'];
        $targetPath = self::getServerMediaUploadPath($uploadPath);
        if($targetPath && $origFilename) {
            $targetFilename = self::getServerUploadFilename($targetPath, $origFilename, '_lg');
            if($targetFilename && self::moveUploadedFileToServer($_FILES['imgfile'], $targetPath, $targetFilename)){
                $imageData['originalurl'] = self::getUrlPathFromServerPath($targetPath . '/' . $targetFilename);
                $imageData = self::processImageDerivatives($imageData, $targetPath, $targetFilename, $origFilename);
            }
        }
        return $imageData;
    }

    public static function processUploadImageFromExternalUrl($imageData, $uploadPath): array
    {
        $origFilename = $imageData['filename'];
        $targetPath = self::getServerMediaUploadPath($uploadPath);
        if($targetPath && $origFilename) {
            $targetFilename = self::getServerUploadFilename($targetPath, $origFilename, '_lg');
            if($targetFilename && self::copyFileToTarget($imageData['sourceurl'], $targetPath, $targetFilename)){
                $imageData['originalurl'] = self::getUrlPathFromServerPath($targetPath . '/' . $targetFilename);
                $imageData = self::processImageDerivatives($imageData, $targetPath, $targetFilename, $origFilename);
            }
        }
        return $imageData;
    }

    public static function transferDwcaToLocalTarget($targetPath, $dwcaPath): bool
    {
        $returnVal = false;
        if(file_exists($dwcaPath)){
            $fp = fopen($targetPath, 'wb+');
            $ch = curl_init(str_replace(' ','%20', $dwcaPath));
            curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            if(curl_exec($ch)) {
                $returnVal = true;
            }
            curl_close($ch);
            fclose($fp);
        }
        return $returnVal;
    }

    public static function transferSymbiotaDwcaToLocalTarget($targetPath, $dwcaPath): bool
    {
        $returnVal = false;
        $searchLabel = '';
        $pathParts = array();
        if(strpos($dwcaPath, 'searchvar=') !== false){
            $searchLabel = 'searchvar';
            $pathParts = explode('?searchvar=', $dwcaPath);
        }
        elseif(strpos($dwcaPath, 'starr=') !== false){
            $searchLabel = 'starr';
            $pathParts = explode('?starr=', $dwcaPath);
        }
        if($pathParts){
            $data = array(
                'schema' => 'dwc',
                'identifications' => '1',
                'images' => '1',
                'attributes' => '1',
                'format' => 'csv',
                'cset' => 'utf-8',
                'zip' => '1',
                'publicsearch' => '1',
                'sourcepage' => 'specimen',
                $searchLabel => $pathParts[1]
            );
            $fp = fopen($targetPath, 'wb+');
            $ch = curl_init(str_replace(' ','%20', $dwcaPath));
            curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            if(curl_exec($ch)) {
                $returnVal = true;
            }
            curl_close($ch);
            fclose($fp);
        }
        return $returnVal;
    }

    public static function unpackZipArchive($targetPath, $zipPath): void
    {
        $zip = new ZipArchive;
        $zip->open($zipPath);
        if(@$zip->extractTo($targetPath . '/')){
            $zip->close();
        }
    }
}
