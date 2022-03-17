<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/UuidFactory.php');
include_once(__DIR__ . '/Encryption.php');
include_once(__DIR__ . '/ProfileManager.php');

class ConfigurationManager{

    private $conn;

    public $coreConfigurations = array(
        'DEFAULT_LANG',
        'DEFAULTCATID',
        'DEFAULT_TITLE',
        'ADMIN_EMAIL',
        'CHARSET',
        'MAX_UPLOAD_FILESIZE',
        'PORTAL_GUID',
        'SECURITY_KEY',
        'CLIENT_ROOT',
        'SERVER_ROOT',
        'TEMP_DIR_ROOT',
        'LOG_PATH',
        'SMTP_HOST',
        'SMTP_PORT',
        'SMTP_ENCRYPTION',
        'SMTP_ENCRYPTION_MECHANISM',
        'SMTP_USERNAME',
        'SMTP_PASSWORD',
        'IMAGE_DOMAIN',
        'IMAGE_ROOT_URL',
        'IMAGE_ROOT_PATH',
        'IMG_WEB_WIDTH',
        'IMG_TN_WIDTH',
        'IMG_LG_WIDTH',
        'IMG_FILE_SIZE_LIMIT',
        'SOLR_URL',
        'SOLR_FULL_IMPORT_INTERVAL',
        'GBIF_USERNAME',
        'GBIF_PASSWORD',
        'GBIF_ORG_KEY',
        'SPATIAL_INITIAL_CENTER',
        'SPATIAL_INITIAL_ZOOM',
        'SPATIAL_INITIAL_BASE_LAYER',
        'GOOGLE_ANALYTICS_KEY',
        'RIGHTS_TERMS',
        'CSS_VERSION_LOCAL',
        'KEY_MOD_IS_ACTIVE',
        'DYN_CHECKLIST_RADIUS',
        'DISPLAY_COMMON_NAMES',
        'ACTIVATE_EXSICCATI',
        'ACTIVATE_CHECKLIST_FG_EXPORT',
        'ACTIVATE_GEOLOCATE_TOOLKIT',
        'PARAMS_ARR',
        'USER_RIGHTS',
        'CSS_VERSION',
        'USER_DISPLAY_NAME',
        'USERNAME',
        'SYMB_UID',
        'IS_ADMIN',
        'RIGHTS_TERMS_DEFS'
    );

    public $baseDirectories = array(
        'admin',
        'checklists',
        'classes',
        'collections',
        'config',
        'games',
        'glossary',
        'ident',
        'imagelib',
        'misc',
        'profile',
        'projects',
        'references',
        'spatial',
        'taxa',
        'webservices'
    );

    public function __construct(){
        $connection = new DbConnection();
        $this->conn = $connection->getConnection();
        if(!$this->conn || $this->conn->connect_errno) {
            echo '<h1 style="color:red;">Cannot connect to the database</h1>';
            $this->conn = null;
            exit();
        }
    }

    public function __destruct(){
        if($this->conn) {
            $this->conn->close();
        }
    }

    public function setGlobalArr(): void
    {
        $sql = 'SELECT configurationname, configurationvalue FROM configurations ';
        $rs = $this->conn->query($sql);
        if($rs->num_rows){
            while($r = $rs->fetch_object()){
                $GLOBALS[$r->configurationname] = $r->configurationvalue;
            }
        }
        else{
            $this->initializeImportConfigurations();
        }
        $rs->free();
        if(!isset($GLOBALS['CLIENT_ROOT'])){
            $GLOBALS['CLIENT_ROOT'] = '';
        }
        if(!isset($GLOBALS['DEFAULT_TITLE'])){
            $GLOBALS['DEFAULT_TITLE'] = '';
        }
        $GLOBALS['CSS_VERSION'] = '20220309';
        $GLOBALS['PARAMS_ARR'] = array();
        $GLOBALS['USER_RIGHTS'] = array();
    }

    public function getCollectionCategoryArr(): array
    {
        $retArr = array();
        $sql = 'SELECT ccpk, category FROM omcollcategories ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[$r->ccpk] = $r->category;
        }
        $rs->free();
        return $retArr;
    }

    public function getConfigurationsArr(): array
    {
        $retArr = array();
        $retArr['core'] = array();
        $retArr['additional'] = array();
        $sql = 'SELECT configurationname, configurationvalue FROM configurations ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $retArr[$r->configurationname] = $r->configurationvalue;
            if(in_array($r->configurationname, $this->coreConfigurations, true)){
                $retArr['core'][$r->configurationname] = $r->configurationvalue;
            }
            else{
                $retArr['additional'][$r->configurationname] = $r->configurationvalue;
            }
        }
        $rs->free();
        return $retArr;
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

    public function saveGlobalArrToDatabase(): void
    {
        $globalKeys = array_keys($GLOBALS);
        foreach($globalKeys as $key){
            if($GLOBALS[$key] && $key !== 'confManager' && $key !== 'DB_SERVER' && $key !== 'RIGHTS_TERMS' && $key !== 'GLOBALS' && $key[0] !== '_'){
                $sql = 'INSERT INTO configurations(configurationname, configurationvalue) ';
                if(is_array($GLOBALS[$key])){
                    $sql .= "VALUES('".$key."','".json_encode($GLOBALS[$key])."')";
                }
                else{
                    $sql .= 'VALUES("'.$key.'","'.$GLOBALS[$key].'")';
                }
                $this->conn->query($sql);
            }
        }
    }

    public function validateGlobalArr(): void
    {
        if(!isset($GLOBALS['CHARSET']) || $GLOBALS['CHARSET'] === '' || !in_array($GLOBALS['CHARSET'], array('UTF-8','ISO-8859-1'))){
            $GLOBALS['CHARSET'] = 'UTF-8';
        }
        if(!isset($GLOBALS['DEFAULT_LANG']) || $GLOBALS['DEFAULT_LANG'] !== 'en'){
            $GLOBALS['DEFAULT_LANG'] = 'en';
        }
        if(!isset($GLOBALS['MAX_UPLOAD_FILESIZE']) || !(int)$GLOBALS['MAX_UPLOAD_FILESIZE'] || (int)$GLOBALS['MAX_UPLOAD_FILESIZE'] > $this->getServerMaxFilesize()){
            $GLOBALS['MAX_UPLOAD_FILESIZE'] = $this->getServerMaxFilesize();
        }
        if(!isset($GLOBALS['SERVER_ROOT']) || $GLOBALS['SERVER_ROOT'] === ''){
            $GLOBALS['SERVER_ROOT'] = $this->getServerRootPath();
            $GLOBALS['LOG_PATH'] = $this->getServerLogFilePath();
            $GLOBALS['IMAGE_ROOT_PATH'] = $this->getServerMediaUploadPath();
        }
        if(isset($GLOBALS['SERVER_ROOT']) && substr($GLOBALS['SERVER_ROOT'],-1) === '/'){
            $GLOBALS['SERVER_ROOT'] = substr($GLOBALS['SERVER_ROOT'],0, -1);
        }
        if(isset($GLOBALS['CLIENT_ROOT']) && substr($GLOBALS['CLIENT_ROOT'],-1) === '/'){
            $GLOBALS['CLIENT_ROOT'] = substr($GLOBALS['CLIENT_ROOT'],0, -1);
        }
        if(!isset($GLOBALS['TEMP_DIR_ROOT']) || $GLOBALS['TEMP_DIR_ROOT'] === ''){
            $GLOBALS['TEMP_DIR_ROOT'] = $this->getServerTempDirPath();
        }
        if(!isset($GLOBALS['LOG_PATH']) || $GLOBALS['LOG_PATH'] === ''){
            $GLOBALS['LOG_PATH'] = $this->getServerLogFilePath();
        }
        if(!isset($GLOBALS['IMAGE_ROOT_PATH']) || $GLOBALS['IMAGE_ROOT_PATH'] === ''){
            $GLOBALS['IMAGE_ROOT_PATH'] = $this->getServerMediaUploadPath();
            $GLOBALS['IMAGE_ROOT_URL'] = $this->getClientMediaRootPath();
        }
        if(!isset($GLOBALS['IMG_FILE_SIZE_LIMIT']) || !(int)$GLOBALS['IMG_FILE_SIZE_LIMIT'] || (int)$GLOBALS['IMG_FILE_SIZE_LIMIT'] > $this->getServerMaxFilesize()){
            $GLOBALS['IMG_FILE_SIZE_LIMIT'] = $this->getServerMaxFilesize();
        }
        if(!isset($GLOBALS['PORTAL_GUID']) || $GLOBALS['PORTAL_GUID'] === ''){
            $GLOBALS['PORTAL_GUID'] = $this->getGUID();
        }
        if(!isset($GLOBALS['SECURITY_KEY']) || $GLOBALS['SECURITY_KEY'] === ''){
            $GLOBALS['SECURITY_KEY'] = $this->getGUID();
        }
        if(!isset($GLOBALS['SOLR_URL']) || $GLOBALS['SOLR_URL'] === ''){
            $GLOBALS['SOLR_FULL_IMPORT_INTERVAL'] = 0;
        }
        if((!isset($GLOBALS['SMTP_USERNAME']) || $GLOBALS['SMTP_USERNAME'] === '') && (!isset($GLOBALS['SMTP_PASSWORD']) || $GLOBALS['SMTP_PASSWORD'] === '')){
            $GLOBALS['SMTP_HOST'] = '';
            $GLOBALS['SMTP_PORT'] = '';
            $GLOBALS['SMTP_ENCRYPTION'] = '';
            $GLOBALS['SMTP_ENCRYPTION_MECHANISM'] = '';
        }
        if(!isset($GLOBALS['SPATIAL_INITIAL_CENTER']) || $GLOBALS['SPATIAL_INITIAL_CENTER'] === ''){
            $GLOBALS['SPATIAL_INITIAL_CENTER'] = '[-110.90713, 32.21976]';
        }
        if(!isset($GLOBALS['SPATIAL_INITIAL_ZOOM']) || $GLOBALS['SPATIAL_INITIAL_ZOOM'] === ''){
            $GLOBALS['SPATIAL_INITIAL_ZOOM'] = '7';
        }
        if(!isset($GLOBALS['SPATIAL_INITIAL_BASE_LAYER']) || $GLOBALS['SPATIAL_INITIAL_BASE_LAYER'] === ''){
            $GLOBALS['SPATIAL_INITIAL_BASE_LAYER'] = 'googleterrain';
        }
        $GLOBALS['CSS_VERSION_LOCAL'] = $this->getCssVersion();
    }

    public function setGlobalArrFromDefaults(): void
    {
        $GLOBALS['CHARSET'] = 'UTF-8';
        $GLOBALS['DEFAULT_LANG'] = 'en';
        $GLOBALS['MAX_UPLOAD_FILESIZE'] = $this->getServerMaxFilesize();
        $GLOBALS['SERVER_ROOT'] = $this->getServerRootPath();
        $GLOBALS['CLIENT_ROOT'] = $this->getClientRootPath();
        $GLOBALS['TEMP_DIR_ROOT'] = $this->getServerTempDirPath();
        $GLOBALS['LOG_PATH'] = $this->getServerLogFilePath();
        $GLOBALS['IMAGE_ROOT_PATH'] = $this->getServerMediaUploadPath();
        $GLOBALS['IMAGE_ROOT_URL'] = $this->getClientMediaRootPath();
        $GLOBALS['IMG_FILE_SIZE_LIMIT'] = $this->getServerMaxFilesize();
        $GLOBALS['PORTAL_GUID'] = $this->getGUID();
        $GLOBALS['SECURITY_KEY'] = $this->getGUID();
        $GLOBALS['CSS_VERSION_LOCAL'] = $this->getCssVersion();
        $GLOBALS['SPATIAL_INITIAL_CENTER'] = '[-110.90713, 32.21976]';
        $GLOBALS['SPATIAL_INITIAL_ZOOM'] = '7';
        $GLOBALS['SPATIAL_INITIAL_BASE_LAYER'] = 'googleterrain';
    }

    public function getCoreConfigurationsArr(): array
    {
        return $this->coreConfigurations;
    }

    public function getServerMaxUploadFilesize(): int
    {
        return (int)ini_get('upload_max_filesize');
    }

    public function getServerMaxPostSize(): int
    {
        return (int)ini_get('post_max_size');
    }

    public function getServerRootPath(): string
    {
        $returnPath = '';
        $serverPath = substr(getcwd(), 1);
        $serverPathArr = explode('/', $serverPath);
        if($serverPathArr){
            $lastIndex = (count($serverPathArr)) - 1;
            if($lastIndex > 0){
                if(array_intersect($serverPathArr, $this->baseDirectories)){
                    if(in_array($serverPathArr[$lastIndex], $this->baseDirectories, true)){
                        --$lastIndex;
                    }
                    else{
                        do {
                            --$lastIndex;
                        } while(!in_array($serverPathArr[$lastIndex], $this->baseDirectories, true) && $lastIndex > 0);
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

    public function getClientRootPath(): string
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
                if(!in_array($urlPathArr[$lastIndex], $this->baseDirectories, true)){
                    do {
                        --$lastIndex;
                    } while(!in_array($urlPathArr[$lastIndex], $this->baseDirectories, true) && $lastIndex > 0);
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

    public function getServerTempDirPath(): string
    {
        $serverPath = $this->getServerRootPath();
        return $serverPath . '/temp';
    }

    public function getServerLogFilePath(): string
    {
        $serverPath = $this->getServerRootPath();
        return $serverPath . '/content/logs';
    }

    public function getServerMediaUploadPath(): string
    {
        $serverPath = $this->getServerRootPath();
        return $serverPath . '/content/imglib';
    }

    public function getClientMediaRootPath(): string
    {
        $clientPath = $this->getClientRootPath();
        return $clientPath . '/content/imglib';
    }

    public function getGUID(): string
    {
        return UuidFactory::getUuidV4();
    }

    public function getServerMaxFilesize(): int
    {
        $upload = $this->getServerMaxUploadFilesize();
        $post = $this->getServerMaxPostSize();
        return max($upload, $post);
    }

    public function updateConfigurationValue($name, $value): bool
    {
        $sql = 'UPDATE configurations '.
            'SET configurationvalue = "'.$value.'" '.
            'WHERE configurationname = "'.$name.'" ';
        return $this->conn->query($sql);
    }

    public function deleteConfiguration($name): bool
    {
        $sql = 'DELETE FROM configurations '.
            'WHERE configurationname = "'.$name.'" ';
        return $this->conn->query($sql);
    }

    public function addConfiguration($name, $value): bool
    {
        $sql = 'INSERT INTO configurations(configurationname, configurationvalue) '.
            'VALUES("'.$name.'","'.$value.'")';
        return $this->conn->query($sql);
    }

    public function validateNewConfNameCore($name): bool
    {
        return in_array($name, $this->coreConfigurations, true);
    }

    public function validateNewConfNameExisting($name): bool
    {
        $sql = 'SELECT id FROM configurations WHERE configurationname = "'.$name.'" ';
        return $this->conn->query($sql)->num_rows;
    }

    public function validatePathIsWritable($path): bool
    {
        return is_writable($path);
    }

    public function validateServerPath($path): bool
    {
        $testPath = $path . '/sitemap.php';
        return file_exists($testPath);
    }

    public function validateClientPath($path): bool
    {
        $testURL = 'http://';
        if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) {
            $testURL = 'https://';
        }
        $testURL .= $_SERVER['HTTP_HOST'];
        if($_SERVER['SERVER_PORT'] && (int)$_SERVER['SERVER_PORT'] !== 80) {
            $testURL .= ':' . $_SERVER['SERVER_PORT'];
        }
        $testURL .= $path . '/sitemap.php';
        $headers = @get_headers($testURL);
        $firstHeader = ($headers ? $headers[0] : '');
        return stripos($firstHeader, '200 OK');
    }

    public function updateCssVersion(): bool
    {
        $currentCssVersion = '';
        $subVersion = 0;
        $sql = 'SELECT configurationvalue FROM configurations WHERE configurationname = "CSS_VERSION_LOCAL" ';
        $rs = $this->conn->query($sql);
        while($r = $rs->fetch_object()){
            $currentCssVersion = $r->configurationvalue;
        }
        $rs->free();
        $newCssVersion = $this->getCssVersion();
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
            'SET configurationvalue = "'.$newCssVersion.'" WHERE configurationname = "CSS_VERSION_LOCAL" ';
        return $this->conn->query($sql);
    }

    public function getCssVersion(): int
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        return $year . $month . $day;
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

    public function readClientCookies(): void
    {
        if((isset($_COOKIE['SymbiotaCrumb']) && (!isset($_REQUEST['submit']) || $_REQUEST['submit'] !== 'logout'))){
            $tokenArr = json_decode(Encryption::decrypt($_COOKIE['SymbiotaCrumb']), true);
            if($tokenArr){
                $pHandler = new ProfileManager();
                if($pHandler->setUserName($tokenArr[0])){
                    $pHandler->setRememberMe(true);
                    $pHandler->setToken($tokenArr[1]);
                    $pHandler->setTokenAuthSql();
                    if(!$pHandler->authenticate()){
                        $pHandler->reset();
                    }
                }
                $pHandler->__destruct();
            }
        }

        if((isset($_COOKIE['SymbiotaCrumb']) && ((isset($_REQUEST['submit']) && $_REQUEST['submit'] === 'logout') || isset($_REQUEST['loginas'])))){
            $tokenArr = json_decode(Encryption::decrypt($_COOKIE['SymbiotaCrumb']), true);
            if($tokenArr){
                $pHandler = new ProfileManager();
                $uid = $pHandler->getUid($tokenArr[0]);
                $pHandler->deleteToken($uid,$tokenArr[1]);
                $pHandler->__destruct();
            }
        }
    }
}
