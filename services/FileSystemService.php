<?php
class FileSystemService {

    public static $baseDirectories = array(
        'admin',
        'api',
        'checklists',
        'classes',
        'collections',
        'components',
        'config',
        'games',
        'glossary',
        'hooks',
        'ident',
        'imagelib',
        'misc',
        'models',
        'profile',
        'projects',
        'services',
        'spatial',
        'stores',
        'taxa',
        'tutorial'
    );

    public static function addFileToZipArchive($zipArchive, $filePath): void
    {
        if(file_exists($filePath)) {
            $zipArchive->addFile($filePath);
            $zipArchive->renameName($filePath, basename($filePath));
        }
    }

    public static function closeFileHandler($fileHandler): void
    {
        fclose($fileHandler);
    }

    public static function closeZipArchive($zipArchive): void
    {
        $zipArchive->close();
    }

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

    public static function createNewZipArchive($zipArchive, $targetPath)
    {
        return $zipArchive->open($targetPath, ZipArchive::CREATE);
    }

    public static function deleteDirectory($directoryPath): bool
	{
        if(is_dir($directoryPath)) {
            $files = scandir($directoryPath);
            foreach($files as $file) {
                if($file !== '.' && $file !== '..') {
                    $filePath = $directoryPath . '/' . $file;
                    if(is_dir($filePath)) {
                        self::deleteDirectory($filePath);
                    }
                    else {
                        unlink($filePath);
                    }
                }
            }
            rmdir($directoryPath);
        }
        return (is_dir($directoryPath) === false);
	}

    public static function deleteFile($filePath, $cleanParentFolder = false): void
    {
        if(file_exists($filePath)) {
            unlink($filePath);
            if($cleanParentFolder){
                $parentPath = dirname($filePath);
                if(is_dir($parentPath) && !glob(($parentPath . '/*'))){
                    self::deleteDirectory($parentPath);
                }
            }
        }
    }

    public static function fileExists($filePath): bool
    {
        return file_exists($filePath);
    }

    public static function getClientMediaRootPath(): string
    {
        $clientPath = self::getClientRootPath();
        return $clientPath . '/content/imglib';
    }

    public static function getClientRootPath(): string
    {
        $returnPath = '';
        $urlPath = substr($_SERVER['REQUEST_URI'], 1);
        $urlPathArr = explode('/', $urlPath);
        if($urlPathArr){
            $lastIndex = (count($urlPathArr)) - 1;
            if($lastIndex > 0){
                if(strpos($urlPathArr[$lastIndex], '.php') !== false){
                    --$lastIndex;
                }
                if(!in_array($urlPathArr[$lastIndex], self::$baseDirectories, true)){
                    do {
                        --$lastIndex;
                    } while(!in_array($urlPathArr[$lastIndex], self::$baseDirectories, true) && $lastIndex > 0);
                }
                if($lastIndex > 0){
                    $index = 0;
                    do {
                        $returnPath .= '/' . $urlPathArr[$index];
                        $index++;
                    } while($index <= $lastIndex);
                }
            }
        }
        return $returnPath;
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
        if(strncmp($imageUrl, '/', 1) === 0){
            $imageUrl = self::getServerPathFromUrlPath($imageUrl);
        }
        return getimagesize($imageUrl);
    }

    public static function getServerLogFilePath(): string
    {
        $serverPath = self::getServerRootPath();
        return $serverPath . '/content/logs';
    }

    public static function getServerMaxFilesize(): int
    {
        $upload = self::getServerMaxUploadFilesize();
        $post = self::getServerMaxPostSize();
        return max($upload, $post);
    }

    public static function getServerMaxPostSize(): int
    {
        return (int)ini_get('post_max_size');
    }

    public static function getServerMaxUploadFilesize(): int
    {
        return (int)ini_get('upload_max_filesize');
    }

    public static function getServerMediaBaseUploadPath(): string
    {
        $serverPath = self::getServerRootPath();
        return $serverPath . '/content/imglib';
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

    public static function getServerPathFromUrlPath($path): string
    {
        return str_replace($GLOBALS['IMAGE_ROOT_URL'], $GLOBALS['IMAGE_ROOT_PATH'], $path);
    }

    public static function getServerRootPath(): string
    {
        $returnPath = '';
        $serverPath = substr(getcwd(), 1);
        $serverPathArr = explode('/', $serverPath);
        if($serverPathArr){
            $lastIndex = (count($serverPathArr)) - 1;
            if($lastIndex > 0){
                if(array_intersect($serverPathArr, self::$baseDirectories)){
                    if(in_array($serverPathArr[$lastIndex], self::$baseDirectories, true)){
                        --$lastIndex;
                    }
                    else{
                        do {
                            --$lastIndex;
                        } while(!in_array($serverPathArr[$lastIndex], self::$baseDirectories, true) && $lastIndex > 0);
                    }
                }
                if($lastIndex > 0){
                    $index = 0;
                    do {
                        $returnPath .= '/' . $serverPathArr[$index];
                        $index++;
                    } while($index <= $lastIndex);
                }
            }
        }
        return $returnPath;
    }

    public static function getServerTempDirPath(): string
    {
        $serverPath = self::getServerRootPath();
        return $serverPath . '/temp';
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

    public static function getTempDownloadUploadPath(): string
    {
        $fullUploadPath = $GLOBALS['TEMP_DIR_ROOT'] . '/downloads';
        if(!file_exists($fullUploadPath) && !mkdir($fullUploadPath, 0777, true) && !is_dir($fullUploadPath)) {
            $fullUploadPath = '';
        }
        $fullUploadPath .= '/' . time();
        if(!file_exists($fullUploadPath) && !mkdir($fullUploadPath, 0777, true) && !is_dir($fullUploadPath)) {
            $fullUploadPath = '';
        }
        return $fullUploadPath;
    }

    public static function getUrlPathFromServerPath($path): string
    {
        return str_replace($GLOBALS['SERVER_ROOT'], '', $path);
    }

    public static function initializeNewDomDocument(): DOMDocument
    {
        return new DOMDocument('1.0', 'UTF-8');
    }

    public static function initializeNewZipArchive(): ZipArchive
    {
        return new ZipArchive;
    }

    public static function isDirectory($targetPath): bool
    {
        return is_dir($targetPath);
    }

    public static function moveUploadedFileToServer($file, $targetPath, $targetFilename): bool
    {
        if(move_uploaded_file($file['tmp_name'], $targetPath . '/' . $targetFilename)){
            return true;
        }
        return false;
    }

    public static function openFileHandler($filePath)
    {
        return fopen($filePath, 'wb');
    }

    public static function processImageDerivatives($imageData, $targetPath, $targetFilename, $origFilename): array
    {
        if((int)$imageData['width'] > 0 && (int)$imageData['height'] > 0){
            if((int)$imageData['width'] > (int)$GLOBALS['IMG_WEB_WIDTH']){
                $webFilename = self::getServerUploadFilename($targetPath, $origFilename);
                if($webFilename && self::createNewImageFromFile(($targetPath . '/' . $targetFilename), $targetPath, $webFilename, $GLOBALS['IMG_WEB_WIDTH'], round(($GLOBALS['IMG_WEB_WIDTH'] * ($imageData['height'] / $imageData['width'])), 0, PHP_ROUND_HALF_UP), $imageData['width'], $imageData['height'])){
                    $imageData['url'] = self::getUrlPathFromServerPath($targetPath . '/' . $webFilename);
                }
            }
            else{
                $imageData['url'] = $imageData['originalurl'];
            }
            if((int)$imageData['width'] > (int)$GLOBALS['IMG_TN_WIDTH']){
                $tnFilename = self::getServerUploadFilename($targetPath, $origFilename, '_tn');
                if($tnFilename && self::createNewImageFromFile(($targetPath . '/' . $targetFilename), $targetPath, $tnFilename, $GLOBALS['IMG_TN_WIDTH'], round(($GLOBALS['IMG_TN_WIDTH'] * ($imageData['height'] / $imageData['width'])), 0, PHP_ROUND_HALF_UP), $imageData['width'], $imageData['height'])){
                    $imageData['thumbnailurl'] = self::getUrlPathFromServerPath($targetPath . '/' . $tnFilename);
                }
            }
        }
        return $imageData;
    }

    public static function processUploadImageFromFile($imageData, $uploadPath): array
    {
        $origFilename = $_FILES['imgfile']['name'];
        if(strtolower(substr($origFilename, -4)) === '.jpg' || strtolower(substr($origFilename, -5)) === '.jpeg' || strtolower(substr($origFilename, -4)) === '.png'){
            $targetPath = self::getServerMediaUploadPath($uploadPath);
            if($targetPath && $origFilename) {
                $targetFilename = self::getServerUploadFilename($targetPath, $origFilename, '_lg');
                if($targetFilename && self::moveUploadedFileToServer($_FILES['imgfile'], $targetPath, $targetFilename)){
                    $imageData['originalurl'] = self::getUrlPathFromServerPath($targetPath . '/' . $targetFilename);
                    $imageData = self::processImageDerivatives($imageData, $targetPath, $targetFilename, $origFilename);
                }
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

    public static function saveDomDocument($domDocument, $targetPath): void
    {
        $domDocument->save($targetPath);
    }

    public static function transferDwcaToLocalTarget($targetPath, $dwcaPath): bool
    {
        $returnVal = false;
        $fp = fopen($targetPath, 'wb+');
        $ch = curl_init(str_replace(' ','%20', $dwcaPath));
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3600);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        if(filesize($targetPath) > 0){
            $returnVal = true;
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
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3600);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            if(filesize($targetPath) > 0){
                $returnVal = true;
            }
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

    public static function validatePathIsWritable($path): bool
    {
        return is_writable($path);
    }

    public static function validateServerPath($path): bool
    {
        $testPath = $path . '/sitemap.php';
        return file_exists($testPath);
    }

    public static function writeRowToCsv($fileHandler, $row): void
    {
        fputcsv($fileHandler, $row);
    }

    public static function writeTextToFile($fileHandler, $text): void
    {
        fwrite($fileHandler, $text);
    }
}
