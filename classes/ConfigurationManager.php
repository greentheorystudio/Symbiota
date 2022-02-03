<?php
include_once(__DIR__ . '/DbConnection.php');
include_once(__DIR__ . '/UuidFactory.php');

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
        'GOOGLE_ANALYTICS_KEY',
        'RIGHTS_TERMS',
        'CSS_VERSION_LOCAL',
        'KEY_MOD_IS_ACTIVE',
        'DYN_CHECKLIST_RADIUS',
        'DISPLAY_COMMON_NAMES',
        'ACTIVATE_EXSICCATI',
        'ACTIVATE_CHECKLIST_FG_EXPORT',
        'ACTIVATE_GEOLOCATE_TOOLKIT'
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
                $sql = 'INSERT INTO configurations(configurationname, configurationvalue) '.
                    'VALUES("'.$key.'","'.$GLOBALS[$key].'")';
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
        $GLOBALS['CSS_VERSION_LOCAL'] = $this->getCssVersion();
    }

    public function setGlobalArrFromDefaults(): void
    {
        $GLOBALS['CHARSET'] = 'UTF-8';
        $GLOBALS['DEFAULT_LANG'] = 'en';
        $GLOBALS['MAX_UPLOAD_FILESIZE'] = $this->getServerMaxFilesize();
        $GLOBALS['SERVER_ROOT'] = $this->getServerRootPath();
        $GLOBALS['CLIENT_ROOT'] = $this->getClientRootPath();
        $GLOBALS['LOG_PATH'] = $this->getServerLogFilePath();
        $GLOBALS['IMAGE_ROOT_PATH'] = $this->getServerMediaUploadPath();
        $GLOBALS['IMAGE_ROOT_URL'] = $this->getClientMediaRootPath();
        $GLOBALS['IMG_FILE_SIZE_LIMIT'] = $this->getServerMaxFilesize();
        $GLOBALS['PORTAL_GUID'] = $this->getGUID();
        $GLOBALS['SECURITY_KEY'] = $this->getGUID();
        $GLOBALS['CSS_VERSION_LOCAL'] = $this->getCssVersion();
        $GLOBALS['SPATIAL_INITIAL_CENTER'] = '[-110.90713, 32.21976]';
        $GLOBALS['SPATIAL_INITIAL_ZOOM'] = '7';
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
        $currentRoot = getcwd();
        return str_replace('/admin', '', $currentRoot);
    }

    public function getClientRootPath(): string
    {
        $urlPathArr = explode('/admin', $_SERVER['REQUEST_URI']);
        return ($urlPathArr?$urlPathArr[0]:'');
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

    public function getCssVersion(): int
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        return $year . $month . $day;
    }
}
