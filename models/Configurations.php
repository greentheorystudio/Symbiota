<?php
include_once(__DIR__ . '/Users.php');
include_once(__DIR__ . '/../services/DbService.php');
include_once(__DIR__ . '/../services/EncryptionService.php');
include_once(__DIR__ . '/../services/FileSystemService.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
include_once(__DIR__ . '/../services/UuidService.php');

class Configurations{

    private $conn;

    public $coreConfigurations = array(
        'ACTIVATE_EXSICCATI',
        'ADMIN_EMAIL',
        'APP_ENABLED',
        'CLIENT_ROOT',
        'CSS_VERSION',
        'CSS_VERSION_LOCAL',
        'DEFAULT_LANG',
        'DEFAULT_TITLE',
        'DEFAULTCATID',
        'EMAIL_CONFIGURED',
        'GBIF_ORG_KEY',
        'GBIF_PASSWORD',
        'GBIF_USERNAME',
        'GLOSSARY_MOD_IS_ACTIVE',
        'IMAGE_ROOT_PATH',
        'IMAGE_ROOT_URL',
        'IMAGE_TAG_OPTIONS',
        'IMG_TN_WIDTH',
        'IMG_WEB_WIDTH',
        'IS_ADMIN',
        'KEY_MOD_IS_ACTIVE',
        'LOG_PATH',
        'MAX_UPLOAD_FILESIZE',
        'MOF_SEARCH_FIELD_JSON',
        'OOTD_CONFIG_JSON',
        'PARAMS_ARR',
        'PORTAL_EMAIL_ADDRESS',
        'PORTAL_GUID',
        'PROCESSING_STATUS_OPTIONS',
        'RIGHTS_TERMS',
        'RIGHTS_TERMS_DEFS',
        'SECURITY_KEY',
        'SERVER_ROOT',
        'SMTP_ENCRYPTION',
        'SMTP_ENCRYPTION_MECHANISM',
        'SMTP_HOST',
        'SMTP_PASSWORD',
        'SMTP_PORT',
        'SMTP_USERNAME',
        'SOLR_FULL_IMPORT_INTERVAL',
        'SOLR_URL',
        'SPATIAL_LAYER_CONFIG_JSON',
        'SPATIAL_DRAGDROP_BORDER_COLOR',
        'SPATIAL_DRAGDROP_BORDER_WIDTH',
        'SPATIAL_DRAGDROP_FILL_COLOR',
        'SPATIAL_DRAGDROP_OPACITY',
        'SPATIAL_DRAGDROP_POINT_RADIUS',
        'SPATIAL_DRAGDROP_RASTER_COLOR_SCALE',
        'SPATIAL_INITIAL_BASE_LAYER',
        'SPATIAL_INITIAL_CENTER',
        'SPATIAL_INITIAL_ZOOM',
        'SPATIAL_POINT_BORDER_COLOR',
        'SPATIAL_POINT_BORDER_WIDTH',
        'SPATIAL_POINT_CLUSTER',
        'SPATIAL_POINT_CLUSTER_DISTANCE',
        'SPATIAL_POINT_DISPLAY_HEAT_MAP',
        'SPATIAL_POINT_FILL_COLOR',
        'SPATIAL_POINT_HEAT_MAP_BLUR',
        'SPATIAL_POINT_HEAT_MAP_RADIUS',
        'SPATIAL_POINT_POINT_RADIUS',
        'SPATIAL_POINT_SELECTIONS_BORDER_COLOR',
        'SPATIAL_POINT_SELECTIONS_BORDER_WIDTH',
        'SPATIAL_SHAPES_FILL_COLOR',
        'SPATIAL_SHAPES_BORDER_COLOR',
        'SPATIAL_SHAPES_BORDER_WIDTH',
        'SPATIAL_SHAPES_OPACITY',
        'SPATIAL_SHAPES_POINT_RADIUS',
        'SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR',
        'SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH',
        'SPATIAL_SHAPES_SELECTIONS_FILL_COLOR',
        'SPATIAL_SHAPES_SELECTIONS_OPACITY',
        'SYMB_UID',
        'TAXON_UNITS',
        'TAXONOMIC_RANKS',
        'TEMP_DIR_ROOT',
        'USAGE_POLICY_URL',
        'USER_DISPLAY_NAME',
        'USER_RIGHTS',
        'USERNAME'
    );

    public $rightsTerms = array(
        'http://creativecommons.org/publicdomain/zero/1.0/' => array(
            'title' => 'CC0 1.0 (Public-domain)',
            'url' => 'https://creativecommons.org/publicdomain/zero/1.0/legalcode',
            'def' => 'Users can copy, modify, distribute and perform the work, even for commercial purposes, all without asking permission.'
        ),
        'http://creativecommons.org/licenses/by/3.0/' => array(
            'title' => 'CC BY (Attribution)',
            'url' => 'http://creativecommons.org/licenses/by/3.0/legalcode',
            'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material for any purpose, even commercially. The licensor cannot revoke these freedoms as long as you follow the license terms.'
        ),
        'http://creativecommons.org/licenses/by-nc/3.0/' => array(
            'title' => 'CC BY-NC (Attribution-Non-Commercial)',
            'url' => 'http://creativecommons.org/licenses/by-nc/3.0/legalcode',
            'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material. The licensor cannot revoke these freedoms as long as you follow the license terms.'
        ),
        'http://creativecommons.org/licenses/by/4.0/' => array(
            'title' => 'CC BY (Attribution)',
            'url' => 'http://creativecommons.org/licenses/by/4.0/legalcode',
            'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material for any purpose, even commercially. The licensor cannot revoke these freedoms as long as you follow the license terms.'
        ),
        'http://creativecommons.org/licenses/by-nc/4.0/' => array(
            'title' => 'CC BY-NC (Attribution-Non-Commercial)',
            'url' => 'http://creativecommons.org/licenses/by-nc/4.0/legalcode',
            'def' => 'Users can copy, redistribute the material in any medium or format, remix, transform, and build upon the material. The licensor cannot revoke these freedoms as long as you follow the license terms.'
        ),
        'http://creativecommons.org/licenses/by-nc-nd/4.0/' => array(
            'title' => 'CC BY-NC-ND 4.0 (Attribution-NonCommercial-NoDerivatives 4.0 International)',
            'url' => 'http://creativecommons.org/licenses/by-nc-nd/4.0/legalcode',
            'def' => 'Users can copy and redistribute the material in any medium or format. The licensor cannot revoke these freedoms as long as you follow the license terms.'
        )
    );

    public function __construct(){
        $connection = new DbService();
        $this->conn = $connection->getConnection();
        if(!$this->conn || $this->conn->connect_errno) {
            echo '<h2 style="color:red;">Cannot connect to the database</h2>';
            $this->conn = null;
            exit();
        }
    }

    public function __destruct(){
        $this->conn->close();
    }

    public function addConfiguration($name, $value): int
    {
        $returnVal = 0;
        if(strpos($name, 'PASSWORD') !== false || strpos($name, 'USERNAME') !== false){
            $value = EncryptionService::encrypt($value);
        }
        $sql = 'INSERT INTO configurations(configurationname, configurationvalue) '.
            "VALUES('" . SanitizerService::cleanInStr($this->conn, $name) . "', '" . SanitizerService::cleanInStr($this->conn, $value) . "') ";
        if($this->conn->query($sql)){
            $returnVal = 1;
        }
        return $returnVal;
    }

    public function addConfigurationArr($configArr): int
    {
        $returnVal = 1;
        if(count($configArr) > 0){
            foreach($configArr as $key => $value){
                if($key && $returnVal === 1){
                    $returnVal = $this->addConfiguration($key, $value);
                }
            }
        }
        return $returnVal;
    }

    public function deleteConfiguration($name): int
    {
        $returnVal = 0;
        $sql = 'DELETE FROM configurations '.
            'WHERE configurationname = "' . SanitizerService::cleanInStr($this->conn, $name) . '" ';
        if($this->conn->query($sql)){
            $returnVal = 1;
        }
        return $returnVal;
    }

    public function deleteConfigurationArr($configArr): int
    {
        $returnVal = 1;
        if(count($configArr) > 0){
            foreach($configArr as $key => $value){
                if($key && $returnVal === 1){
                    $returnVal = $this->deleteConfiguration($key);
                }
            }
        }
        return $returnVal;
    }

    public function deleteMapDataFile($fileName): bool
    {
        FileSystemService::deleteFile($GLOBALS['SERVER_ROOT'] . '/content/spatial/' . $fileName);
        return true;
    }

    public function getCssVersion(): int
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        return $year . $month . $day;
    }

    public function getConfigurationData(): array
    {
        $returnArr = array();
        $sql = 'SELECT configurationname, configurationvalue FROM configurations ';
        if(($result = $this->conn->query($sql)) && $result->num_rows) {
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            foreach($rows as $index => $row){
                $value = $row['configurationvalue'];
                if(strpos($row['configurationname'], 'PASSWORD') !== false || strpos($row['configurationname'], 'USERNAME') !== false){
                    $value = EncryptionService::decrypt($value);
                }
                elseif(substr_compare($row['configurationname'], '_JSON', -5) === 0){
                    $value = json_decode($value, true);
                }
                if(in_array($row['configurationname'], $this->coreConfigurations, true)){
                    $returnArr['core'][$row['configurationname']] = $value;
                }
                else{
                    $returnArr['additional'][$row['configurationname']] = $value;
                }
                unset($rows[$index]);
            }
        }
        $returnArr['server']['SERVER_MAX_POST_SIZE'] = FileSystemService::getServerMaxPostSize();
        $returnArr['server']['SERVER_MAX_UPLOAD_FILESIZE'] = FileSystemService::getServerMaxUploadFilesize();
        $returnArr['server']['SERVER_DB_PROPS'] = $this->getDatabasePropArr();
        $returnArr['server']['SERVER_PHP_VERSION'] = $this->getPhpVersion();
        return $returnArr;
    }

    public function getDatabasePropArr(): array
    {
        $versionArr = array();
        $versionStr = '';
        $sql = 'SELECT VERSION() AS ver ';
        if($result = $this->conn->query($sql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                $versionStr = $row['ver'];
            }
        }
        if($versionStr){
            if(strpos($versionStr,'MariaDB') !== false){
                $versionArr['db'] = 'MariaDB';
            }
            else{
                $versionArr['db'] = 'MySQL';
            }
            $versionPieces = explode('-', $versionStr);
            if(is_array($versionPieces)){
                $versionArr['ver'] = $versionPieces[0];
            }
        }
        return $versionArr;
    }

    public function setGlobalCssVersion(): void
    {
        if(strpos($GLOBALS['CSS_VERSION_LOCAL'], '-') !== false){
            $versionParts = explode('-', $GLOBALS['CSS_VERSION_LOCAL']);
            if($versionParts && (int)$versionParts[0] > (int)$GLOBALS['CSS_VERSION']){
                $GLOBALS['CSS_VERSION'] = $GLOBALS['CSS_VERSION_LOCAL'];
            }
        }
        elseif((int)$GLOBALS['CSS_VERSION_LOCAL'] > (int)$GLOBALS['CSS_VERSION']){
            $GLOBALS['CSS_VERSION'] = $GLOBALS['CSS_VERSION_LOCAL'];
        }
    }

    public function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    public static function getRightsTermData($termId): array
    {
        $returnArr = array();
        foreach($GLOBALS['RIGHTS_TERMS'] as $k => $v){
            if($k === $termId){
                $returnArr = $v;
            }
        }
        return $returnArr;
    }

    public function initializeImportConfigurations(): void
    {
        if(file_exists(__DIR__ . '/../config/symbini.php')){
            include(__DIR__ . '/../config/symbini.php');
            $this->validateGlobalArr();
        }
        else{
            $this->setGlobalArrFromDefaults();
        }
        $this->saveGlobalArrToDatabase();
    }

    public function readClientCookies(): void
    {
        $users = new Users();
        if((isset($_COOKIE['BioSurvCrumb']) && (!isset($_REQUEST['action']) || $_REQUEST['action'] !== 'logout'))){
            $tokenArr = json_decode(EncryptionService::decrypt($_COOKIE['BioSurvCrumb']), true);
            if($tokenArr && !$users->authenticateUserFromToken($tokenArr[0], $tokenArr[1], $tokenArr[2])) {
                $users->clearCookieSession();
            }
        }
        if((isset($_COOKIE['BioSurvCrumb'], $_REQUEST['action']) && ($_REQUEST['action'] === 'logout' || $_REQUEST['action'] === 'loginas'))){
            $tokenArr = json_decode(EncryptionService::decrypt($_COOKIE['BioSurvCrumb']), true);
            if($tokenArr){
                $user = $users->getUserByUsername($tokenArr[0]);
                $users->deleteToken($user['uid'], $tokenArr[1]);
            }
        }
    }

    public function saveGlobalArrToDatabase(): void
    {
        $globalKeys = array_keys($GLOBALS);
        foreach($globalKeys as $key){
            if($GLOBALS[$key] && $key !== 'confManager' && $key !== 'DB_SERVER' && $key !== 'RIGHTS_TERMS' && $key !== 'GLOBALS' && $key[0] !== '_'){
                $sql = 'INSERT INTO configurations(configurationname, configurationvalue) ';
                if(is_array($GLOBALS[$key])){
                    $sql .= "VALUES('" . SanitizerService::cleanInStr($this->conn, $key) . "', '" . json_encode($GLOBALS[$key]) . "') ";
                }
                else{
                    $sql .= "VALUES('" . SanitizerService::cleanInStr($this->conn, $key) . "', '" . SanitizerService::cleanInStr($this->conn, $GLOBALS[$key]) . "') ";
                }
                $this->conn->query($sql);
            }
        }
    }

    public function setGlobalArr(): void
    {
        $sql = 'SELECT configurationname, configurationvalue FROM configurations ';
        if($result = $this->conn->query($sql)){
            if($result->num_rows){
                $rows = $result->fetch_all(MYSQLI_ASSOC);
                $result->free();
                foreach($rows as $index => $row){
                    $value = $row['configurationvalue'];
                    if(strpos($row['configurationname'], 'PASSWORD') !== false || strpos($row['configurationname'], 'USERNAME') !== false){
                        $value = EncryptionService::decrypt($value);
                    }
                    $GLOBALS[$row['configurationname']] = $value;
                    unset($rows[$index]);
                }
            }
            else{
                $this->initializeImportConfigurations();
            }
        }
        $GLOBALS['CSS_VERSION'] = '20250203';
        $GLOBALS['JS_VERSION'] = '20250520';
        $GLOBALS['PARAMS_ARR'] = array();
        $GLOBALS['USER_RIGHTS'] = array();
        $this->validateGlobalArr();
    }

    public function setGlobalArrFromDefaults(): void
    {
        $GLOBALS['CLIENT_ROOT'] = FileSystemService::getClientRootPath();
        $GLOBALS['CSS_VERSION_LOCAL'] = $this->getCssVersion();
        $GLOBALS['DEFAULT_LANG'] = 'en';
        $GLOBALS['IMAGE_ROOT_PATH'] = FileSystemService::getServerMediaBaseUploadPath();
        $GLOBALS['IMAGE_ROOT_URL'] = FileSystemService::getClientMediaRootPath();
        $GLOBALS['IMAGE_TAG_OPTIONS'] = '["Diagnostic","Handwriting","HasIDLabel","HasLabel","HasOrganism","HasProblem","ImageOfAdult","ImageOfImmature","ShowsHabitat","TypedText"]';
        $GLOBALS['IMG_TN_WIDTH'] = 200;
        $GLOBALS['IMG_WEB_WIDTH'] = 1400;
        $GLOBALS['LOG_PATH'] = FileSystemService::getServerLogFilePath();
        $GLOBALS['MAX_UPLOAD_FILESIZE'] = FileSystemService::getServerMaxFilesize();
        $GLOBALS['PORTAL_GUID'] = UuidService::getUuidV4();
        $GLOBALS['PROCESSING_STATUS_OPTIONS'] = array('Unprocessed','Stage 1','Stage 2','Stage 3','Pending Review','Expert Required','Reviewed','Closed');
        $GLOBALS['SECURITY_KEY'] = UuidService::getUuidV4();
        $GLOBALS['SERVER_ROOT'] = FileSystemService::getServerRootPath();
        $GLOBALS['SPATIAL_LAYER_CONFIG_JSON'] = null;
        $GLOBALS['SPATIAL_DRAGDROP_BORDER_COLOR'] = '#000000';
        $GLOBALS['SPATIAL_DRAGDROP_BORDER_WIDTH'] = '2';
        $GLOBALS['SPATIAL_DRAGDROP_FILL_COLOR'] = '#AAAAAA';
        $GLOBALS['SPATIAL_DRAGDROP_OPACITY'] = '0.3';
        $GLOBALS['SPATIAL_DRAGDROP_POINT_RADIUS'] = '5';
        $GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] = 'earth';
        $GLOBALS['SPATIAL_INITIAL_BASE_LAYER'] = 'googleterrain';
        $GLOBALS['SPATIAL_INITIAL_CENTER'] = '[-110.90713, 32.21976]';
        $GLOBALS['SPATIAL_INITIAL_ZOOM'] = '7';
        $GLOBALS['SPATIAL_POINT_BORDER_COLOR'] = '#000000';
        $GLOBALS['SPATIAL_POINT_BORDER_WIDTH'] = '1';
        $GLOBALS['SPATIAL_POINT_CLUSTER'] = true;
        $GLOBALS['SPATIAL_POINT_CLUSTER_DISTANCE'] = '50';
        $GLOBALS['SPATIAL_POINT_DISPLAY_HEAT_MAP'] = false;
        $GLOBALS['SPATIAL_POINT_FILL_COLOR'] = '#E69E67';
        $GLOBALS['SPATIAL_POINT_HEAT_MAP_BLUR'] = '15';
        $GLOBALS['SPATIAL_POINT_HEAT_MAP_RADIUS'] = '5';
        $GLOBALS['SPATIAL_POINT_POINT_RADIUS'] = '7';
        $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_COLOR'] = '#10D8E6';
        $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH'] = '2';
        $GLOBALS['SPATIAL_SHAPES_BORDER_COLOR'] = '#3399CC';
        $GLOBALS['SPATIAL_SHAPES_BORDER_WIDTH'] = '2';
        $GLOBALS['SPATIAL_SHAPES_FILL_COLOR'] = '#FFFFFF';
        $GLOBALS['SPATIAL_SHAPES_OPACITY'] = '0.4';
        $GLOBALS['SPATIAL_SHAPES_POINT_RADIUS'] = '5';
        $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR'] = '#0099FF';
        $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH'] = '5';
        $GLOBALS['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR'] = '#FFFFFF';
        $GLOBALS['SPATIAL_SHAPES_SELECTIONS_OPACITY'] = '0.5';
        $GLOBALS['TAXONOMIC_RANKS'] = '[10,30,60,100,140,180,220,230,240]';
        $GLOBALS['TEMP_DIR_ROOT'] = FileSystemService::getServerTempDirPath();
        $GLOBALS['RIGHTS_TERMS'] = $this->rightsTerms;
    }

    public function updateConfigurationValue($name, $value): int
    {
        $returnVal = 0;
        if(strpos($name, 'PASSWORD') !== false || strpos($name, 'USERNAME') !== false){
            $value = EncryptionService::encrypt($value);
        }
        $sql = 'UPDATE configurations '.
            "SET configurationvalue = '" . SanitizerService::cleanInStr($this->conn, $value) . "' ".
            'WHERE configurationname = "' . SanitizerService::cleanInStr($this->conn, $name) . '" ';
        if($this->conn->query($sql)){
            $returnVal = 1;
        }
        return $returnVal;
    }

    public function updateConfigurationValueArr($configArr): int
    {
        $returnVal = 1;
        if(count($configArr) > 0){
            foreach($configArr as $key => $value){
                if($key && $returnVal === 1){
                    $returnVal = $this->updateConfigurationValue($key, $value);
                }
            }
        }
        return $returnVal;
    }

    public function updateCssVersion(): int
    {
        $returnVal = 0;
        $currentCssVersion = '';
        $subVersion = 0;
        $sql = 'SELECT configurationvalue FROM configurations WHERE configurationname = "CSS_VERSION_LOCAL" ';
        if($result = $this->conn->query($sql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $result->free();
            if($row){
                $currentCssVersion = $row['configurationvalue'];
            }
        }
        $newCssVersion = $this->getCssVersion();
        if($currentCssVersion){
            if(strpos($currentCssVersion, '-') !== false){
                $versionParts = explode('-', $currentCssVersion);
                if($versionParts){
                    $subVersion = (int)$versionParts[1];
                }
            }
            if($currentCssVersion === (string)$newCssVersion || $subVersion){
                if(!$subVersion){
                    $subVersion = 1;
                }
                do {
                    $versionParts = explode('-', $newCssVersion);
                    if($versionParts){
                        $newCssVersion = $versionParts[0] . '-' . $subVersion;
                    }
                    else{
                        $newCssVersion .= '-' . $subVersion;
                    }
                    $subVersion++;
                } while($currentCssVersion === $newCssVersion);
            }
            $sql = 'UPDATE configurations '.
                'SET configurationvalue = "' . $newCssVersion . '" WHERE configurationname = "CSS_VERSION_LOCAL" ';
        }
        else{
            $sql = 'INSERT INTO configurations(configurationname, configurationvalue) '.
                'VALUES("CSS_VERSION_LOCAL", "' . $newCssVersion . '") ';
        }
        if($this->conn->query($sql)){
            $returnVal = 1;
        }
        return $returnVal;
    }

    public function uploadMapDataFile(): string
    {
        $returnStr = '';
        if(strtolower(substr($_FILES['addLayerFile']['name'], -4)) === '.zip' || strtolower(substr($_FILES['addLayerFile']['name'], -8)) === '.geojson' || strtolower(substr($_FILES['addLayerFile']['name'], -4)) === '.kml' || strtolower(substr($_FILES['addLayerFile']['name'], -4)) === '.tif' || strtolower(substr($_FILES['addLayerFile']['name'], -5)) === '.tiff' || strtolower(substr($_FILES['addLayerFile']['name'], -5)) === '.json'){
            $targetPath = $GLOBALS['SERVER_ROOT'].'/content/spatial';
            if(file_exists($targetPath) || (mkdir($targetPath, 0775) && is_dir($targetPath))) {
                $uploadFileName = basename($_FILES['addLayerFile']['name']);
                $uploadFileName = str_replace(array(',','&',' '), array('','',''), urldecode($uploadFileName));
                $fileExtension =  substr(strrchr($uploadFileName, '.'), 1);
                $fileNameOnly =  substr($uploadFileName, 0, ((strlen($fileExtension) + 1) * -1));
                $tempFileName = $fileNameOnly;
                $cnt = 0;
                while(file_exists($targetPath.'/'.$tempFileName.'.'.$fileExtension)){
                    $tempFileName = $fileNameOnly.'_'.$cnt;
                    $cnt++;
                }
                if($cnt) {
                    $fileNameOnly = $tempFileName;
                }
                if(move_uploaded_file($_FILES['addLayerFile']['tmp_name'], $targetPath.'/'.$fileNameOnly.'.'.$fileExtension)){
                    $returnStr = $fileNameOnly.'.'.$fileExtension;
                }
            }
        }
        return $returnStr;
    }

    public function validateClientPath($path): int
    {
        $testURL = $_SERVER['SERVER_PORT'] === 443 ? 'https://' : 'http://';
        $testURL .= $_SERVER['HTTP_HOST'];
        $testURL .= ($path ?: '') . '/sitemap.php';
        $headers = @get_headers($testURL);
        $firstHeader = ($headers ? $headers[0] : '');
        return stripos($firstHeader, '200 OK') ? 1 : 0;
    }

    public function validateGlobalArr(): void
    {
        if(!isset($GLOBALS['ADMIN_EMAIL'])){
            $GLOBALS['ADMIN_EMAIL'] = '';
        }
        if(isset($GLOBALS['CLIENT_ROOT']) && substr($GLOBALS['CLIENT_ROOT'],-1) === '/'){
            $GLOBALS['CLIENT_ROOT'] = substr($GLOBALS['CLIENT_ROOT'],0, -1);
        }
        if(!isset($GLOBALS['CLIENT_ROOT'])){
            $GLOBALS['CLIENT_ROOT'] = '';
        }
        if(!isset($GLOBALS['DEFAULT_TITLE'])){
            $GLOBALS['DEFAULT_TITLE'] = '';
        }
        if(!isset($GLOBALS['USAGE_POLICY_URL'])){
            $GLOBALS['USAGE_POLICY_URL'] = '';
        }
        if(!isset($GLOBALS['CSS_VERSION_LOCAL']) || $GLOBALS['CSS_VERSION_LOCAL'] === ''){
            $GLOBALS['CSS_VERSION_LOCAL'] = $this->getCssVersion();
        }
        if(!isset($GLOBALS['DEFAULTCATID'])){
            $GLOBALS['DEFAULTCATID'] = null;
        }
        if(!isset($GLOBALS['DEFAULT_LANG']) || $GLOBALS['DEFAULT_LANG'] !== 'en'){
            $GLOBALS['DEFAULT_LANG'] = 'en';
        }
        if(!isset($GLOBALS['IMAGE_ROOT_PATH']) || $GLOBALS['IMAGE_ROOT_PATH'] === ''){
            $GLOBALS['IMAGE_ROOT_PATH'] = FileSystemService::getServerMediaBaseUploadPath();
            $GLOBALS['IMAGE_ROOT_URL'] = FileSystemService::getClientMediaRootPath();
        }
        if(!isset($GLOBALS['IMAGE_ROOT_URL'])){
            $GLOBALS['IMAGE_ROOT_URL'] = '';
        }
        if(!isset($GLOBALS['IMAGE_TAG_OPTIONS']) || $GLOBALS['IMAGE_TAG_OPTIONS'] === ''){
            $GLOBALS['IMAGE_TAG_OPTIONS'] = '["Diagnostic","Handwriting","HasIDLabel","HasLabel","HasOrganism","HasProblem","ImageOfAdult","ImageOfImmature","ShowsHabitat","TypedText"]';
        }
        if(!isset($GLOBALS['IMG_TN_WIDTH']) || $GLOBALS['IMG_TN_WIDTH'] === ''){
            $GLOBALS['IMG_TN_WIDTH'] = 200;
        }
        if(!isset($GLOBALS['IMG_WEB_WIDTH']) || $GLOBALS['IMG_WEB_WIDTH'] === ''){
            $GLOBALS['IMG_WEB_WIDTH'] = 1400;
        }
        if(!isset($GLOBALS['ACTIVATE_EXSICCATI'])){
            $GLOBALS['ACTIVATE_EXSICCATI'] = false;
        }
        if(!isset($GLOBALS['APP_ENABLED'])){
            $GLOBALS['APP_ENABLED'] = false;
        }
        if(!isset($GLOBALS['KEY_MOD_IS_ACTIVE'])){
            $GLOBALS['KEY_MOD_IS_ACTIVE'] = false;
        }
        if(!isset($GLOBALS['OOTD_CONFIG_JSON']) || $GLOBALS['OOTD_CONFIG_JSON'] === ''){
            $GLOBALS['OOTD_CONFIG_JSON'] = null;
        }
        if(!isset($GLOBALS['GLOSSARY_MOD_IS_ACTIVE'])){
            $GLOBALS['GLOSSARY_MOD_IS_ACTIVE'] = false;
        }
        if(!isset($GLOBALS['LOG_PATH']) || $GLOBALS['LOG_PATH'] === ''){
            $GLOBALS['LOG_PATH'] = FileSystemService::getServerLogFilePath();
        }
        if(!isset($GLOBALS['MAX_UPLOAD_FILESIZE']) || !(int)$GLOBALS['MAX_UPLOAD_FILESIZE'] || (int)$GLOBALS['MAX_UPLOAD_FILESIZE'] > FileSystemService::getServerMaxFilesize()){
            $GLOBALS['MAX_UPLOAD_FILESIZE'] = FileSystemService::getServerMaxFilesize();
        }
        if((!isset($GLOBALS['PORTAL_EMAIL_ADDRESS']) || !$GLOBALS['PORTAL_EMAIL_ADDRESS']) && isset($GLOBALS['ADMIN_EMAIL'])){
            $GLOBALS['PORTAL_EMAIL_ADDRESS'] = $GLOBALS['ADMIN_EMAIL'];
        }
        if(!isset($GLOBALS['PORTAL_GUID']) || $GLOBALS['PORTAL_GUID'] === ''){
            $GLOBALS['PORTAL_GUID'] = UuidService::getUuidV4();
        }
        if(!isset($GLOBALS['PROCESSING_STATUS_OPTIONS'])){
            $GLOBALS['PROCESSING_STATUS_OPTIONS'] = array('Unprocessed','Stage 1','Stage 2','Stage 3','Pending Review','Expert Required','Reviewed','Closed');
        }
        if(!isset($GLOBALS['SECURITY_KEY']) || $GLOBALS['SECURITY_KEY'] === ''){
            $GLOBALS['SECURITY_KEY'] = UuidService::getUuidV4();
        }
        if(!isset($GLOBALS['SERVER_ROOT']) || $GLOBALS['SERVER_ROOT'] === ''){
            $GLOBALS['SERVER_ROOT'] = FileSystemService::getServerRootPath();
            $GLOBALS['LOG_PATH'] = FileSystemService::getServerLogFilePath();
            $GLOBALS['IMAGE_ROOT_PATH'] = FileSystemService::getServerMediaBaseUploadPath();
        }
        if(substr($GLOBALS['SERVER_ROOT'],-1) === '/'){
            $GLOBALS['SERVER_ROOT'] = substr($GLOBALS['SERVER_ROOT'],0, -1);
        }
        if((!isset($GLOBALS['SMTP_USERNAME']) || $GLOBALS['SMTP_USERNAME'] === '') && (!isset($GLOBALS['SMTP_PASSWORD']) || $GLOBALS['SMTP_PASSWORD'] === '')){
            $GLOBALS['SMTP_USERNAME'] = '';
            $GLOBALS['SMTP_PASSWORD'] = '';
            $GLOBALS['SMTP_HOST'] = '';
            $GLOBALS['SMTP_PORT'] = '';
            $GLOBALS['SMTP_ENCRYPTION'] = '';
            $GLOBALS['SMTP_ENCRYPTION_MECHANISM'] = '';
        }
        if(!isset($GLOBALS['SOLR_URL']) || $GLOBALS['SOLR_URL'] === ''){
            $GLOBALS['SOLR_FULL_IMPORT_INTERVAL'] = 0;
        }
        if(!isset($GLOBALS['SPATIAL_LAYER_CONFIG_JSON']) || $GLOBALS['SPATIAL_LAYER_CONFIG_JSON'] === ''){
            $GLOBALS['SPATIAL_LAYER_CONFIG_JSON'] = null;
        }
        if(!isset($GLOBALS['SPATIAL_DRAGDROP_BORDER_COLOR']) || $GLOBALS['SPATIAL_DRAGDROP_BORDER_COLOR'] === ''){
            $GLOBALS['SPATIAL_DRAGDROP_BORDER_COLOR'] = '#000000';
        }
        if(!isset($GLOBALS['SPATIAL_DRAGDROP_BORDER_WIDTH']) || $GLOBALS['SPATIAL_DRAGDROP_BORDER_WIDTH'] === ''){
            $GLOBALS['SPATIAL_DRAGDROP_BORDER_WIDTH'] = '2';
        }
        if(!isset($GLOBALS['SPATIAL_DRAGDROP_FILL_COLOR']) || $GLOBALS['SPATIAL_DRAGDROP_FILL_COLOR'] === ''){
            $GLOBALS['SPATIAL_DRAGDROP_FILL_COLOR'] = '#AAAAAA';
        }
        if(!isset($GLOBALS['SPATIAL_DRAGDROP_OPACITY']) || $GLOBALS['SPATIAL_DRAGDROP_OPACITY'] === ''){
            $GLOBALS['SPATIAL_DRAGDROP_OPACITY'] = '0.3';
        }
        if(!isset($GLOBALS['SPATIAL_DRAGDROP_POINT_RADIUS']) || $GLOBALS['SPATIAL_DRAGDROP_POINT_RADIUS'] === ''){
            $GLOBALS['SPATIAL_DRAGDROP_POINT_RADIUS'] = '5';
        }
        if(!isset($GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE']) || $GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] === ''){
            $GLOBALS['SPATIAL_DRAGDROP_RASTER_COLOR_SCALE'] = 'earth';
        }
        if(!isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER']) || $GLOBALS['SPATIAL_INITIAL_BASE_LAYER'] === ''){
            $GLOBALS['SPATIAL_INITIAL_BASE_LAYER'] = 'googleterrain';
        }
        if(!isset($GLOBALS['SPATIAL_INITIAL_CENTER']) || $GLOBALS['SPATIAL_INITIAL_CENTER'] === ''){
            $GLOBALS['SPATIAL_INITIAL_CENTER'] = '[-110.90713, 32.21976]';
        }
        if(!isset($GLOBALS['SPATIAL_INITIAL_ZOOM']) || $GLOBALS['SPATIAL_INITIAL_ZOOM'] === ''){
            $GLOBALS['SPATIAL_INITIAL_ZOOM'] = '7';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_BORDER_COLOR']) || $GLOBALS['SPATIAL_POINT_BORDER_COLOR'] === ''){
            $GLOBALS['SPATIAL_POINT_BORDER_COLOR'] = '#000000';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_BORDER_WIDTH']) || $GLOBALS['SPATIAL_POINT_BORDER_WIDTH'] === ''){
            $GLOBALS['SPATIAL_POINT_BORDER_WIDTH'] = '1';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_CLUSTER'])){
            $GLOBALS['SPATIAL_POINT_CLUSTER'] = true;
        }
        if(!isset($GLOBALS['MOF_SEARCH_FIELD_JSON'])){
            $GLOBALS['MOF_SEARCH_FIELD_JSON'] = '';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_CLUSTER_DISTANCE']) || $GLOBALS['SPATIAL_POINT_CLUSTER_DISTANCE'] === ''){
            $GLOBALS['SPATIAL_POINT_CLUSTER_DISTANCE'] = '50';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_DISPLAY_HEAT_MAP'])){
            $GLOBALS['SPATIAL_POINT_DISPLAY_HEAT_MAP'] = false;
        }
        if(!isset($GLOBALS['SPATIAL_POINT_FILL_COLOR']) || $GLOBALS['SPATIAL_POINT_FILL_COLOR'] === ''){
            $GLOBALS['SPATIAL_POINT_FILL_COLOR'] = '#E69E67';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_HEAT_MAP_BLUR']) || $GLOBALS['SPATIAL_POINT_HEAT_MAP_BLUR'] === ''){
            $GLOBALS['SPATIAL_POINT_HEAT_MAP_BLUR'] = '15';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_HEAT_MAP_RADIUS']) || $GLOBALS['SPATIAL_POINT_HEAT_MAP_RADIUS'] === ''){
            $GLOBALS['SPATIAL_POINT_HEAT_MAP_RADIUS'] = '5';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_POINT_RADIUS']) || $GLOBALS['SPATIAL_POINT_POINT_RADIUS'] === ''){
            $GLOBALS['SPATIAL_POINT_POINT_RADIUS'] = '7';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_COLOR']) || $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_COLOR'] === ''){
            $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_COLOR'] = '#10D8E6';
        }
        if(!isset($GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH']) || $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH'] === ''){
            $GLOBALS['SPATIAL_POINT_SELECTIONS_BORDER_WIDTH'] = '2';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_BORDER_COLOR']) || $GLOBALS['SPATIAL_SHAPES_BORDER_COLOR'] === ''){
            $GLOBALS['SPATIAL_SHAPES_BORDER_COLOR'] = '#3399CC';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_BORDER_WIDTH']) || $GLOBALS['SPATIAL_SHAPES_BORDER_WIDTH'] === ''){
            $GLOBALS['SPATIAL_SHAPES_BORDER_WIDTH'] = '2';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_FILL_COLOR']) || $GLOBALS['SPATIAL_SHAPES_FILL_COLOR'] === ''){
            $GLOBALS['SPATIAL_SHAPES_FILL_COLOR'] = '#FFFFFF';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_OPACITY']) || $GLOBALS['SPATIAL_SHAPES_OPACITY'] === ''){
            $GLOBALS['SPATIAL_SHAPES_OPACITY'] = '0.4';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_POINT_RADIUS']) || $GLOBALS['SPATIAL_SHAPES_POINT_RADIUS'] === ''){
            $GLOBALS['SPATIAL_SHAPES_POINT_RADIUS'] = '5';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR']) || $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR'] === ''){
            $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_COLOR'] = '#0099FF';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH']) || $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH'] === ''){
            $GLOBALS['SPATIAL_SHAPES_SELECTIONS_BORDER_WIDTH'] = '5';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR']) || $GLOBALS['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR'] === ''){
            $GLOBALS['SPATIAL_SHAPES_SELECTIONS_FILL_COLOR'] = '#FFFFFF';
        }
        if(!isset($GLOBALS['SPATIAL_SHAPES_SELECTIONS_OPACITY']) || $GLOBALS['SPATIAL_SHAPES_SELECTIONS_OPACITY'] === ''){
            $GLOBALS['SPATIAL_SHAPES_SELECTIONS_OPACITY'] = '0.5';
        }
        if(!isset($GLOBALS['TAXONOMIC_RANKS']) || $GLOBALS['TAXONOMIC_RANKS'] === ''){
            $GLOBALS['TAXONOMIC_RANKS'] = '[10,30,60,100,140,180,220,230,240]';
        }
        if(!isset($GLOBALS['TEMP_DIR_ROOT']) || $GLOBALS['TEMP_DIR_ROOT'] === ''){
            $GLOBALS['TEMP_DIR_ROOT'] = FileSystemService::getServerTempDirPath();
        }
        $GLOBALS['EMAIL_CONFIGURED'] = (
            isset($GLOBALS['PORTAL_EMAIL_ADDRESS'], $GLOBALS['SMTP_USERNAME'], $GLOBALS['SMTP_PASSWORD'], $GLOBALS['SMTP_HOST'], $GLOBALS['SMTP_PORT']) &&
            $GLOBALS['PORTAL_EMAIL_ADDRESS'] &&
            $GLOBALS['SMTP_USERNAME'] &&
            $GLOBALS['SMTP_PASSWORD'] &&
            $GLOBALS['SMTP_HOST'] &&
            $GLOBALS['SMTP_PORT']
        );
        if(!isset($GLOBALS['PERMITTED_CHECKLISTS'])){
            $GLOBALS['PERMITTED_CHECKLISTS'] = array();
        }
        if(!isset($GLOBALS['PERMITTED_COLLECTIONS'])){
            $GLOBALS['PERMITTED_COLLECTIONS'] = array();
        }
        if(!isset($GLOBALS['PERMITTED_PROJECTS'])){
            $GLOBALS['PERMITTED_PROJECTS'] = array();
        }
        if(!isset($GLOBALS['RIGHTS_TERMS']) || count($GLOBALS['RIGHTS_TERMS']) === 0){
            $GLOBALS['RIGHTS_TERMS'] = $this->rightsTerms;
        }
        $GLOBALS['SHOW_PASSWORD_RESET'] = isset($GLOBALS['PW_RESET']) && (int)$GLOBALS['PW_RESET'] === 1;
        $GLOBALS['RSS_ACTIVE'] = file_exists(__DIR__ . '/../rss.xml');
        $GLOBALS['TAXON_UNITS'] = array(
            array('rankid' => 10, 'rankname' => 'Kingdom', 'dirparentrankid' => 10, 'reqparentrankid' => 10),
            array('rankid' => 20, 'rankname' => 'Subkingdom', 'dirparentrankid' => 10, 'reqparentrankid' => 10),
            array('rankid' => 30, 'rankname' => 'Phylum', 'dirparentrankid' => 20, 'reqparentrankid' => 10),
            array('rankid' => 40, 'rankname' => 'Subphylum', 'dirparentrankid' => 30, 'reqparentrankid' => 30),
            array('rankid' => 50, 'rankname' => 'Superclass', 'dirparentrankid' => 40, 'reqparentrankid' => 30),
            array('rankid' => 60, 'rankname' => 'Class', 'dirparentrankid' => 50, 'reqparentrankid' => 30),
            array('rankid' => 70, 'rankname' => 'Subclass', 'dirparentrankid' => 60, 'reqparentrankid' => 60),
            array('rankid' => 80, 'rankname' => 'Infraclass', 'dirparentrankid' => 70, 'reqparentrankid' => 60),
            array('rankid' => 90, 'rankname' => 'Superorder', 'dirparentrankid' => 80, 'reqparentrankid' => 60),
            array('rankid' => 100, 'rankname' => 'Order', 'dirparentrankid' => 90, 'reqparentrankid' => 60),
            array('rankid' => 110, 'rankname' => 'Suborder', 'dirparentrankid' => 100, 'reqparentrankid' => 100),
            array('rankid' => 120, 'rankname' => 'Infraorder', 'dirparentrankid' => 110, 'reqparentrankid' => 100),
            array('rankid' => 130, 'rankname' => 'Superfamily', 'dirparentrankid' => 120, 'reqparentrankid' => 100),
            array('rankid' => 140, 'rankname' => 'Family', 'dirparentrankid' => 130, 'reqparentrankid' => 100),
            array('rankid' => 150, 'rankname' => 'Subfamily', 'dirparentrankid' => 140, 'reqparentrankid' => 140),
            array('rankid' => 160, 'rankname' => 'Tribe', 'dirparentrankid' => 150, 'reqparentrankid' => 140),
            array('rankid' => 170, 'rankname' => 'Subtribe', 'dirparentrankid' => 160, 'reqparentrankid' => 140),
            array('rankid' => 180, 'rankname' => 'Genus', 'dirparentrankid' => 170, 'reqparentrankid' => 140),
            array('rankid' => 190, 'rankname' => 'Subgenus', 'dirparentrankid' => 180, 'reqparentrankid' => 180),
            array('rankid' => 220, 'rankname' => 'Species', 'dirparentrankid' => 190, 'reqparentrankid' => 180),
            array('rankid' => 230, 'rankname' => 'Subspecies', 'dirparentrankid' => 220, 'reqparentrankid' => 180)
        );
    }

    public function validateNewConfNameCore($name): int
    {
        return in_array($name, $this->coreConfigurations, true) ? 1 : 0;
    }

    public function validateNewConfNameExisting($name): int
    {
        $sql = 'SELECT id FROM configurations WHERE configurationname = "' . SanitizerService::cleanInStr($this->conn, $name) . '" ';
        return $this->conn->query($sql)->num_rows;
    }

    public function validateOotdConfigJson($jsonStr): bool
    {
        $returnVal = false;
        $configArr = json_decode($jsonStr, true);
        if(array_key_exists('date', $configArr) && $configArr['date'] && is_string($configArr['date'])) {
            $dateArr = explode(' ', $configArr['date']);
            if(array_key_exists('tid', $configArr) && (int)$configArr['tid'] > 0 && count($dateArr) === 4) {
                $returnVal = true;
            }
        }
        return $returnVal;
    }

    public function validatePathIsWritable($path): int
    {
        return FileSystemService::validatePathIsWritable($path) ? 1 : 0;
    }

    public function validateServerPath($path): int
    {
        return FileSystemService::validateServerPath($path) ? 1 : 0;
    }
}
